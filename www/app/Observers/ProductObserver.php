<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\User;
use App\Services\Notifications\NotificationOrchestrator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductObserver
{
	public function __construct(private NotificationOrchestrator $orchestrator)
	{
	}

	public function created(Product $product): void
	{
		$this->dispatch('product.created', $product);
	}

	public function updated(Product $product): void
	{
		$this->dispatch('product.updated', $product);
	}

	public function deleted(Product $product): void
	{
		$this->dispatch('product.deleted', $product);
	}

	private function dispatch(string $eventType, Product $product): void
	{
		$actorId = $product->updated_by ?? $product->created_by ?? Auth::id();
		$payload = [
			'event_id' => (string) Str::uuid(),
			'actor_user_id' => $actorId,
			'product_id' => $product->id,
			'product_name' => $product->name,
			'actor_name' => optional(User::find($actorId))->name,
		];
		$this->orchestrator->handle($eventType, $payload);
	}
}

