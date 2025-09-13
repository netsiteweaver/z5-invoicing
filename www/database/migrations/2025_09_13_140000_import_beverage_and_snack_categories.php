<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		$existingMaxOrder = DB::table('product_categories')->max('display_order');
		$startOrder = is_null($existingMaxOrder) ? 0 : (int) $existingMaxOrder;

		$userId = DB::table('users')->orderBy('id')->value('id');
		if (is_null($userId)) {
			// No users yet; skip inserts to avoid foreign key violations. Re-run later after a user exists.
			return;
		}

		$names = [
			'Energy drinks',
			'Soft drinks',
			'Juices',
			'Snacks',
			'Cakes',
			'Biscuits sugared',
			'Biscuits salted',
			'Chocolate',
			'Gummy',
			'Chewing gum',
			'Others',
		];

		$now = now();

		$rows = [];
		foreach ($names as $index => $name) {
			$rows[] = [
				'uuid' => (string) Str::uuid(),
				'name' => $name,
				'description' => null,
				'parent_id' => null,
				'display_order' => $startOrder + $index + 1,
				'created_by' => $userId,
				'updated_by' => null,
				'created_at' => $now,
				'updated_at' => $now,
				'status' => 1,
			];
		}

		$existing = DB::table('product_categories')->whereIn('name', $names)->pluck('name')->all();
		$toInsert = array_values(array_filter($rows, function ($row) use ($existing) {
			return !in_array($row['name'], $existing, true);
		}));

		if (!empty($toInsert)) {
			DB::table('product_categories')->insert($toInsert);
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		DB::table('product_categories')->whereIn('name', [
			'Energy drinks',
			'Soft drinks',
			'Juices',
			'Snacks',
			'Cakes',
			'Biscuits sugared',
			'Biscuits salted',
			'Chocolate',
			'Gummy',
			'Chewing gum',
			'Others',
		])->delete();
	}
};



