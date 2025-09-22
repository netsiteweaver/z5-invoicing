<?php

namespace App\Services;

use App\Models\Inventory;
use App\Services\Notifications\NotificationOrchestrator;
use Illuminate\Support\Str;

class LowStockAlertService
{
    public function __construct(private NotificationOrchestrator $orchestrator)
    {
    }

    /**
     * Dispatch an inventory.low_stock notification if stock crossed the threshold downward.
     */
    public function checkAndNotifyCrossing(
        Inventory $inventory,
        int $stockBefore,
        int $stockAfter,
        ?int $actorUserId = null,
        string $reason = '',
        array $extra = []
    ): void {
        // Only consider downward movements
        if ($stockAfter >= $stockBefore) {
            return;
        }

        $threshold = $this->resolveThreshold($inventory);
        if ($stockBefore > $threshold && $stockAfter <= $threshold) {
            $this->dispatch($inventory, $stockAfter, $threshold, $actorUserId, $reason, $extra);
        }
    }

    private function resolveThreshold(Inventory $inventory): int
    {
        $reorderPoint = (int) ($inventory->reorder_point ?? 0);
        $minStockLevel = (int) ($inventory->min_stock_level ?? 0);
        return $reorderPoint > 0 ? $reorderPoint : $minStockLevel;
    }

    private function dispatch(
        Inventory $inventory,
        int $currentStock,
        int $threshold,
        ?int $actorUserId,
        string $reason,
        array $extra
    ): void {
        $inventory->loadMissing(['product', 'department']);

        $payload = array_merge([
            'event_id' => (string) Str::uuid(),
            'actor_user_id' => $actorUserId,
            'inventory_id' => $inventory->id,
            'product_id' => $inventory->product_id,
            'product_name' => optional($inventory->product)->name,
            'department_id' => $inventory->department_id,
            'department_name' => optional($inventory->department)->name,
            'current_stock' => $currentStock,
            'threshold' => $threshold,
            'reorder_point' => (int) ($inventory->reorder_point ?? 0),
            'min_stock_level' => (int) ($inventory->min_stock_level ?? 0),
            'reason' => $reason,
        ], $extra);

        $this->orchestrator->handle('inventory.low_stock', $payload);
    }
}

