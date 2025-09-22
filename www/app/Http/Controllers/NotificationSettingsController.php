<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificationRule;
use App\Models\NotificationRuleRecipient;
use App\Models\NotificationTemplate;
use App\Models\User;

class NotificationSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:settings.view');
        $this->middleware('permission:settings.edit')->only(['update']);
    }

    public function index()
    {
        // Define supported events
        $events = [
            'user.created' => 'User Created',
            'user.updated' => 'User Updated',
            'user.deleted' => 'User Deleted',
            'inventory.low_stock' => 'Inventory Low Stock',
            'product.created' => 'Product Created',
            'product.updated' => 'Product Updated',
            'product.deleted' => 'Product Deleted',
            'order.created' => 'Order Created',
            'order.updated' => 'Order Updated',
            'order.deleted' => 'Order Deleted',
            'sale.created' => 'Sale Created',
            'sale.updated' => 'Sale Updated',
            'sale.deleted' => 'Sale Deleted',
            'customer.created' => 'Customer Created',
            'customer.updated' => 'Customer Updated',
            'customer.deleted' => 'Customer Deleted',
            'supplier.created' => 'Supplier Created',
            'supplier.updated' => 'Supplier Updated',
            'supplier.deleted' => 'Supplier Deleted',
        ];

        // Only Admin/Root active users are eligible recipients
        $users = User::active()
            ->whereIn('user_level', ['Admin', 'Root'])
            ->orderBy('name')
            ->get(['id','name','email']);

        // Load existing rules and recipients
        $rules = NotificationRule::with('recipients')
            ->whereIn('event_type', array_keys($events))
            ->get()
            ->keyBy('event_type');

        return view('settings.notifications', compact('events', 'users', 'rules'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'recipients' => 'nullable|array',
            'recipients.*' => 'array', // event => [userIds]
        ]);

        $recipientsByEvent = $validated['recipients'] ?? [];

        // Supported events (must mirror index())
        $events = [
            'user.created' => 'User Created',
            'user.updated' => 'User Updated',
            'user.deleted' => 'User Deleted',
            'inventory.low_stock' => 'Inventory Low Stock',
            'product.created' => 'Product Created',
            'product.updated' => 'Product Updated',
            'product.deleted' => 'Product Deleted',
            'order.created' => 'Order Created',
            'order.updated' => 'Order Updated',
            'order.deleted' => 'Order Deleted',
            'sale.created' => 'Sale Created',
            'sale.updated' => 'Sale Updated',
            'sale.deleted' => 'Sale Deleted',
            'customer.created' => 'Customer Created',
            'customer.updated' => 'Customer Updated',
            'customer.deleted' => 'Customer Deleted',
            'supplier.created' => 'Supplier Created',
            'supplier.updated' => 'Supplier Updated',
            'supplier.deleted' => 'Supplier Deleted',
        ];

        // Only Admin/Root active users are allowed to be saved as recipients
        $allowedUserIds = User::active()
            ->whereIn('user_level', ['Admin', 'Root'])
            ->pluck('id')
            ->map(fn($v) => (int) $v)
            ->toArray();

        // Ensure a default template exists for email channel
        $defaultTemplate = NotificationTemplate::where('channel', 'email')->orderBy('id')->first();
        if (!$defaultTemplate) {
            $defaultTemplate = NotificationTemplate::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'channel' => 'email',
                'name' => 'Default Notification',
                'subject' => 'Notification: {{event}}',
                'body' => "Hello,\n\nReference: {{reference}}\n\nThanks.",
                'is_active' => true,
            ]);
        }

        // Iterate over ALL events; if an event is missing in the payload, treat as empty selection
        foreach (array_keys($events) as $eventType) {
            $userIdsPosted = $recipientsByEvent[$eventType] ?? [];
            // Sanitize user IDs to allowed set
            $userIds = array_values(array_intersect($allowedUserIds, array_map('intval', $userIdsPosted)));

            // Find or create a rule for the event
            $rule = NotificationRule::firstOrCreate(
                ['event_type' => $eventType],
                [
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'name' => strtoupper(str_replace('.', ' ', $eventType)),
                    'channel' => 'email',
                    'template_id' => $defaultTemplate->id,
                    'is_active' => true,
                    'is_system' => false,
                ]
            );

            // Reset recipients for this rule (also clears when empty)
            NotificationRuleRecipient::where('rule_id', $rule->id)->delete();
            $order = 0;
            foreach (array_unique($userIds) as $uid) {
                NotificationRuleRecipient::create([
                    'rule_id' => $rule->id,
                    'recipient_type' => 'user',
                    'recipient_value' => (string) $uid,
                    'priority' => $order++,
                    'is_active' => true,
                ]);
            }
        }

        return redirect()->route('settings.notifications')->with('success', 'Notification recipients updated.');
    }
}


