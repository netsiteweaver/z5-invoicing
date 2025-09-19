<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationTemplate;
use App\Models\NotificationRule;
use App\Models\NotificationRuleRecipient;
use App\Models\Role;

class NotificationSeeder extends Seeder
{
	public function run(): void
	{
		// Resolve admin role id by name
		$adminRoleId = optional(Role::where('name', 'admin')->first())->id ?? 1;

		// Templates
		$tplUserCreatedAdmin = NotificationTemplate::firstOrCreate(
			['name' => 'user_created_admin_notice'],
			[
				'channel' => 'email',
				'subject' => 'New user created: {{user_name}}',
				'body' => "A new user has been created.\nName: {{user_name}}\nEmail: {{user_email}}\nBy: {{actor_name}}",
				'is_active' => true,
			]
		);

		$tplProductCreated = NotificationTemplate::firstOrCreate(
			['name' => 'product_created'],
			[
				'channel' => 'email',
				'subject' => 'Product created: {{product_name}}',
				'body' => "Product created: {{product_name}} by {{actor_name}}",
				'is_active' => true,
			]
		);

		$tplProductUpdated = NotificationTemplate::firstOrCreate(
			['name' => 'product_updated'],
			[
				'channel' => 'email',
				'subject' => 'Product updated: {{product_name}}',
				'body' => "Product updated: {{product_name}} by {{actor_name}}",
				'is_active' => true,
			]
		);

		$tplProductDeleted = NotificationTemplate::firstOrCreate(
			['name' => 'product_deleted'],
			[
				'channel' => 'email',
				'subject' => 'Product deleted: {{product_name}}',
				'body' => "Product deleted: {{product_name}} by {{actor_name}}",
				'is_active' => true,
			]
		);

		// Rules
		$ruleUserCreatedAdmins = NotificationRule::firstOrCreate(
			['name' => 'user-created-admins'],
			[
				'event_type' => 'user.created',
				'channel' => 'email',
				'template_id' => $tplUserCreatedAdmin->id,
				'is_active' => true,
			]
		);
		NotificationRuleRecipient::firstOrCreate([
			'rule_id' => $ruleUserCreatedAdmins->id,
			'recipient_type' => 'role',
			'recipient_value' => (string) $adminRoleId,
		]);
		NotificationRuleRecipient::firstOrCreate([
			'rule_id' => $ruleUserCreatedAdmins->id,
			'recipient_type' => 'special',
			'recipient_value' => 'actor',
		]);

		$ruleProductCreated = NotificationRule::firstOrCreate(
			['name' => 'product-created-actor-admins'],
			[
				'event_type' => 'product.created',
				'channel' => 'email',
				'template_id' => $tplProductCreated->id,
				'is_active' => true,
			]
		);
		NotificationRuleRecipient::firstOrCreate([
			'rule_id' => $ruleProductCreated->id,
			'recipient_type' => 'role',
			'recipient_value' => (string) $adminRoleId,
		]);
		NotificationRuleRecipient::firstOrCreate([
			'rule_id' => $ruleProductCreated->id,
			'recipient_type' => 'special',
			'recipient_value' => 'actor',
		]);

		$ruleProductUpdated = NotificationRule::firstOrCreate(
			['name' => 'product-updated-actor-admins'],
			[
				'event_type' => 'product.updated',
				'channel' => 'email',
				'template_id' => $tplProductUpdated->id,
				'is_active' => true,
			]
		);
		NotificationRuleRecipient::firstOrCreate([
			'rule_id' => $ruleProductUpdated->id,
			'recipient_type' => 'role',
			'recipient_value' => (string) $adminRoleId,
		]);
		NotificationRuleRecipient::firstOrCreate([
			'rule_id' => $ruleProductUpdated->id,
			'recipient_type' => 'special',
			'recipient_value' => 'actor',
		]);

		$ruleProductDeleted = NotificationRule::firstOrCreate(
			['name' => 'product-deleted-actor-admins'],
			[
				'event_type' => 'product.deleted',
				'channel' => 'email',
				'template_id' => $tplProductDeleted->id,
				'is_active' => true,
			]
		);
		NotificationRuleRecipient::firstOrCreate([
			'rule_id' => $ruleProductDeleted->id,
			'recipient_type' => 'role',
			'recipient_value' => (string) $adminRoleId,
		]);
		NotificationRuleRecipient::firstOrCreate([
			'rule_id' => $ruleProductDeleted->id,
			'recipient_type' => 'special',
			'recipient_value' => 'actor',
		]);
	}
}

