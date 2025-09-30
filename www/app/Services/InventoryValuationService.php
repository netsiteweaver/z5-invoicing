<?php

namespace App\Services;

use App\Models\GoodsReceiptItem;
use App\Models\StockMovement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class InventoryValuationService
{
    /**
     * Compute FIFO valuation for a collection of inventory items.
     * Returns an associative array keyed by inventory id with:
     *  - fifo_total_value: float
     *  - fifo_avg_unit_cost: float
     *  - layers: array of [remaining_quantity, unit_cost]
     */
    public static function computeFifoForInventory(Collection $inventoryItems, ?string $asOfDate = null): array
    {
        $results = [];

        foreach ($inventoryItems as $item) {
            $currentStock = (int) ($item->current_stock ?? 0);
            if ($currentStock <= 0) {
                $results[$item->id] = [
                    'fifo_total_value' => 0.0,
                    'fifo_avg_unit_cost' => 0.0,
                    'layers' => [],
                ];
                continue;
            }

            // Build inbound layers from goods receipt items (oldest first)
            $inboundQuery = GoodsReceiptItem::query()
                ->where('product_id', $item->product_id)
                ->when(!empty($item->department_id), function ($q) use ($item) {
                    $q->where('department_id', $item->department_id);
                })
                ->orderBy('created_at', 'asc');
            if ($asOfDate) {
                $inboundQuery->whereDate('created_at', '<=', $asOfDate);
            }
            $inboundLayers = $inboundQuery->get(['quantity', 'unit_cost']);

            // Total outbound quantity up to as of date
            $outboundQuery = StockMovement::query()
                ->where('product_id', $item->product_id)
                ->when(!empty($item->department_id), function ($q) use ($item) {
                    $q->where('department_id', $item->department_id);
                })
                ->where('movement_type', 'out');
            if ($asOfDate) {
                $outboundQuery->whereDate('movement_date', '<=', $asOfDate);
            }
            $totalOutbound = (int) ($outboundQuery->sum('quantity'));

            // Consume outbound from oldest inbound layers (FIFO) to find remaining layers
            $remainingLayers = [];
            $remainingOutbound = $totalOutbound;
            foreach ($inboundLayers as $layer) {
                $layerQty = (int) ($layer->quantity ?? 0);
                $layerCost = (float) ($layer->unit_cost ?? 0);
                if ($layerQty <= 0) {
                    continue;
                }

                if ($remainingOutbound > 0) {
                    $consumed = min($remainingOutbound, $layerQty);
                    $layerQty -= $consumed;
                    $remainingOutbound -= $consumed;
                }

                if ($layerQty > 0) {
                    $remainingLayers[] = [
                        'quantity' => $layerQty,
                        'unit_cost' => $layerCost,
                    ];
                }
            }

            // Sum up remaining quantity from layers
            $layersRemainingQty = array_sum(array_map(function ($l) { return (int) $l['quantity']; }, $remainingLayers));

            // In theory, $layersRemainingQty should approximate $currentStock. If not, adjust using last known cost.
            $fifoTotalValue = 0.0;
            foreach ($remainingLayers as $l) {
                $fifoTotalValue += ((int) $l['quantity']) * ((float) $l['unit_cost']);
            }

            if ($layersRemainingQty !== $currentStock) {
                $difference = $currentStock - $layersRemainingQty;
                if ($difference !== 0) {
                    // Use the most recent known unit cost as a pragmatic fallback
                    $fallbackCost = 0.0;
                    if (!empty($remainingLayers)) {
                        $last = end($remainingLayers);
                        $fallbackCost = (float) ($last['unit_cost'] ?? 0);
                    } else {
                        // Try to fallback to latest receipt cost
                        $latestReceipt = GoodsReceiptItem::query()
                            ->where('product_id', $item->product_id)
                            ->when(!empty($item->department_id), function ($q) use ($item) { $q->where('department_id', $item->department_id); })
                            ->orderBy('created_at', 'desc')
                            ->first(['unit_cost']);
                        $fallbackCost = (float) ($latestReceipt->unit_cost ?? 0);
                    }
                    $fifoTotalValue += $difference * $fallbackCost;
                }
            }

            $avgUnitCost = $currentStock > 0 ? $fifoTotalValue / $currentStock : 0.0;

            $results[$item->id] = [
                'fifo_total_value' => round($fifoTotalValue, 2),
                'fifo_avg_unit_cost' => round($avgUnitCost, 4),
                'layers' => $remainingLayers,
            ];
        }

        return $results;
    }
}

