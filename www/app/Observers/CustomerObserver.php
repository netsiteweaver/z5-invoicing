<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\User;
use App\Services\Notifications\NotificationOrchestrator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CustomerObserver
{
    public function __construct(private NotificationOrchestrator $orchestrator)
    {
    }

    public function created(Customer $customer): void
    {
        $this->dispatch('customer.created', $customer);
    }

    public function updated(Customer $customer): void
    {
        $oldStatus = (int) ($customer->getOriginal('status') ?? 1);
        $newStatus = (int) ($customer->status ?? $oldStatus);
        if ($oldStatus !== 0 && $newStatus === 0) {
            $this->dispatch('customer.deleted', $customer);
            return;
        }
        $this->dispatch('customer.updated', $customer);
    }

    public function deleted(Customer $customer): void
    {
        $this->dispatch('customer.deleted', $customer);
    }

    private function dispatch(string $eventType, Customer $customer): void
    {
        $actorId = $customer->updated_by ?? $customer->created_by ?? Auth::id();
        $payload = [
            'event_id' => (string) Str::uuid(),
            'actor_user_id' => $actorId,
            'customer_id' => $customer->id,
            'customer_name' => $customer->display_name ?? ($customer->full_name ?? $customer->company_name ?? ''),
            'actor_name' => optional(User::find($actorId))->name,
        ];
        $this->orchestrator->handle($eventType, $payload);
    }
}


