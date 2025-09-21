<?php

namespace App\Observers;

use App\Models\Supplier;
use App\Models\User;
use App\Services\Notifications\NotificationOrchestrator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SupplierObserver
{
    public function __construct(private NotificationOrchestrator $orchestrator)
    {
    }

    public function created(Supplier $supplier): void
    {
        $this->dispatch('supplier.created', $supplier);
    }

    public function updated(Supplier $supplier): void
    {
        $oldStatus = (int) ($supplier->getOriginal('status') ?? 1);
        $newStatus = (int) ($supplier->status ?? $oldStatus);
        if ($oldStatus !== 0 && $newStatus === 0) {
            $this->dispatch('supplier.deleted', $supplier);
            return;
        }
        $this->dispatch('supplier.updated', $supplier);
    }

    public function deleted(Supplier $supplier): void
    {
        $this->dispatch('supplier.deleted', $supplier);
    }

    private function dispatch(string $eventType, Supplier $supplier): void
    {
        $actorId = $supplier->updated_by ?? $supplier->created_by ?? Auth::id();
        $payload = [
            'event_id' => (string) Str::uuid(),
            'actor_user_id' => $actorId,
            'supplier_id' => $supplier->id,
            'supplier_name' => $supplier->name,
            'actor_name' => optional(User::find($actorId))->name,
        ];
        $this->orchestrator->handle($eventType, $payload);
    }
}


