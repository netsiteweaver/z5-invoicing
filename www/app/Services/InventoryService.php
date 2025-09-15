<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function adjustStockIn(int $productId, int $departmentId, int $quantity, array $movementMeta = []): void
    {
        DB::transaction(function () use ($productId, $departmentId, $quantity, $movementMeta) {
            $inventory = Inventory::firstOrCreate(
                ['product_id' => $productId, 'department_id' => $departmentId],
                ['current_stock' => 0, 'min_stock_level' => 0, 'reorder_point' => 0, 'created_by' => auth()->id()]
            );

            $inventory->increment('current_stock', $quantity);

            StockMovement::create([
                'product_id' => $productId,
                'department_id' => $departmentId,
                'movement_type' => $movementMeta['movement_type'] ?? 'in',
                'quantity' => $quantity,
                'reference_type' => $movementMeta['reference_type'] ?? null,
                'reference_id' => $movementMeta['reference_id'] ?? null,
                'reference_number' => $movementMeta['reference_number'] ?? null,
                'notes' => $movementMeta['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);
        });
    }

    public function adjustStockOut(int $productId, int $departmentId, int $quantity, array $movementMeta = []): void
    {
        DB::transaction(function () use ($productId, $departmentId, $quantity, $movementMeta) {
            $inventory = Inventory::where('product_id', $productId)
                ->where('department_id', $departmentId)
                ->lockForUpdate()
                ->first();

            if (!$inventory || $inventory->current_stock < $quantity) {
                throw new \RuntimeException('Insufficient stock');
            }

            $inventory->decrement('current_stock', $quantity);

            StockMovement::create([
                'product_id' => $productId,
                'department_id' => $departmentId,
                'movement_type' => $movementMeta['movement_type'] ?? 'out',
                'quantity' => $quantity,
                'reference_type' => $movementMeta['reference_type'] ?? null,
                'reference_id' => $movementMeta['reference_id'] ?? null,
                'reference_number' => $movementMeta['reference_number'] ?? null,
                'notes' => $movementMeta['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);
        });
    }

    public function transfer(int $productId, int $fromDepartmentId, int $toDepartmentId, int $quantity, string $referenceNumber, int $referenceId): void
    {
        DB::transaction(function () use ($productId, $fromDepartmentId, $toDepartmentId, $quantity, $referenceNumber, $referenceId) {
            $this->adjustStockOut($productId, $fromDepartmentId, $quantity, [
                'movement_type' => 'transfer_out',
                'reference_type' => 'stock_transfer',
                'reference_id' => $referenceId,
                'reference_number' => $referenceNumber,
                'notes' => 'Transfer out',
            ]);

            $this->adjustStockIn($productId, $toDepartmentId, $quantity, [
                'movement_type' => 'transfer_in',
                'reference_type' => 'stock_transfer',
                'reference_id' => $referenceId,
                'reference_number' => $referenceNumber,
                'notes' => 'Transfer in',
            ]);
        });
    }
}


