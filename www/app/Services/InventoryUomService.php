<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Uom;
use App\Models\Inventory;
use App\Models\StockMovement;
use App\Models\GoodsReceiptItem;
use App\Models\SaleItem;

class InventoryUomService
{
    /**
     * Add inventory for a product using any UOM
     */
    public static function addInventory(
        int $productId,
        int $departmentId,
        int $quantity,
        int $uomId,
        int $uomQuantity = 1,
        string $referenceType = 'manual',
        int $referenceId = null,
        string $referenceNumber = null,
        string $notes = null
    ): Inventory {
        // Convert to base units
        $baseQuantity = UomService::toBaseUnits($quantity, $uomId, $uomQuantity);
        
        // Get or create inventory record
        $inventory = Inventory::firstOrCreate(
            [
                'product_id' => $productId,
                'department_id' => $departmentId,
            ],
            [
                'current_stock' => 0,
                'min_stock_level' => 0,
                'reorder_point' => 0,
                'created_by' => auth()->id(),
            ]
        );

        // Update stock
        $inventory->increment('current_stock', $baseQuantity);

        // Record stock movement
        StockMovement::create([
            'product_id' => $productId,
            'department_id' => $departmentId,
            'movement_type' => 'in',
            'quantity' => $baseQuantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'reference_number' => $referenceNumber,
            'notes' => $notes,
            'created_by' => auth()->id(),
        ]);

        return $inventory;
    }

    /**
     * Remove inventory for a product using any UOM
     */
    public static function removeInventory(
        int $productId,
        int $departmentId,
        int $quantity,
        int $uomId,
        int $uomQuantity = 1,
        string $referenceType = 'manual',
        int $referenceId = null,
        string $referenceNumber = null,
        string $notes = null
    ): Inventory {
        // Convert to base units
        $baseQuantity = UomService::toBaseUnits($quantity, $uomId, $uomQuantity);
        
        // Get inventory record
        $inventory = Inventory::where('product_id', $productId)
            ->where('department_id', $departmentId)
            ->first();

        if (!$inventory) {
            throw new \Exception('Inventory record not found');
        }

        // Check if sufficient stock
        if ($inventory->current_stock < $baseQuantity) {
            throw new \Exception('Insufficient stock');
        }

        // Update stock
        $inventory->decrement('current_stock', $baseQuantity);

        // Record stock movement
        StockMovement::create([
            'product_id' => $productId,
            'department_id' => $departmentId,
            'movement_type' => 'out',
            'quantity' => $baseQuantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'reference_number' => $referenceNumber,
            'notes' => $notes,
            'created_by' => auth()->id(),
        ]);

        return $inventory;
    }

    /**
     * Get current stock in a specific UOM
     */
    public static function getStockInUom(int $productId, int $departmentId, int $uomId): float
    {
        $inventory = Inventory::where('product_id', $productId)
            ->where('department_id', $departmentId)
            ->first();

        if (!$inventory) {
            return 0;
        }

        return UomService::fromBaseValue($inventory->current_stock, $uomId);
    }

    /**
     * Get stock levels for a product in multiple UOMs
     */
    public static function getStockLevels(int $productId, int $departmentId): array
    {
        $inventory = Inventory::where('product_id', $productId)
            ->where('department_id', $departmentId)
            ->first();

        if (!$inventory) {
            return [];
        }

        $product = Product::find($productId);
        if (!$product || !$product->uom) {
            return [];
        }

        // Get all UOMs for this product's dimension
        $uoms = Uom::byDimension($product->uom->dimension_code)
            ->active()
            ->get();

        $stockLevels = [];
        foreach ($uoms as $uom) {
            $stockLevels[] = [
                'uom_id' => $uom->id,
                'uom_name' => $uom->name,
                'uom_code' => $uom->code,
                'quantity' => UomService::fromBaseValue($inventory->current_stock, $uom->id),
                'is_base_unit' => $uom->id === $product->uom_id,
            ];
        }

        return $stockLevels;
    }

    /**
     * Check if sufficient stock is available
     */
    public static function hasSufficientStock(
        int $productId,
        int $departmentId,
        int $quantity,
        int $uomId,
        int $uomQuantity = 1
    ): bool {
        $baseQuantity = UomService::toBaseUnits($quantity, $uomId, $uomQuantity);
        
        $inventory = Inventory::where('product_id', $productId)
            ->where('department_id', $departmentId)
            ->first();

        if (!$inventory) {
            return false;
        }

        return $inventory->current_stock >= $baseQuantity;
    }

    /**
     * Get stock summary for a product
     */
    public static function getStockSummary(int $productId, int $departmentId): array
    {
        $inventory = Inventory::where('product_id', $productId)
            ->where('department_id', $departmentId)
            ->first();

        if (!$inventory) {
            return [
                'current_stock' => 0,
                'min_stock_level' => 0,
                'max_stock_level' => 0,
                'reorder_point' => 0,
                'is_low_stock' => false,
                'stock_levels' => [],
            ];
        }

        $product = Product::find($productId);
        $stockLevels = $product ? self::getStockLevels($productId, $departmentId) : [];

        return [
            'current_stock' => $inventory->current_stock,
            'min_stock_level' => $inventory->min_stock_level,
            'max_stock_level' => $inventory->max_stock_level,
            'reorder_point' => $inventory->reorder_point,
            'is_low_stock' => $inventory->is_low_stock,
            'stock_levels' => $stockLevels,
        ];
    }

    /**
     * Convert inventory quantity between UOMs
     */
    public static function convertInventoryQuantity(
        int $productId,
        int $departmentId,
        float $quantity,
        int $fromUomId,
        int $toUomId
    ): float {
        // First convert to base units
        $baseQuantity = UomService::toBaseValue($quantity, $fromUomId);
        
        // Then convert to target UOM
        return UomService::fromBaseValue($baseQuantity, $toUomId);
    }

    /**
     * Get available UOMs for a product
     */
    public static function getAvailableUoms(int $productId): \Illuminate\Database\Eloquent\Collection
    {
        $product = Product::find($productId);
        if (!$product || !$product->uom) {
            return collect();
        }

        return Uom::byDimension($product->uom->dimension_code)
            ->active()
            ->ordered()
            ->get();
    }
}
