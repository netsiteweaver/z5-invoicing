<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Products: already has uom_id column. Ensure FK if table exists and column present
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'uom_id')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'uom_id')) {
                    $table->unsignedBigInteger('uom_id')->default(1)->after('brand_id');
                }
                // Avoid duplicate foreign key creation errors in repeated migrations
                try {
                    $table->foreign('uom_id')->references('id')->on('uoms');
                } catch (\Throwable $e) {}
                $table->index('uom_id');
            });
        }

        // Goods Receipt Items: replace string uom with uom_id and uom_qty
        if (Schema::hasTable('goods_receipt_items')) {
            Schema::table('goods_receipt_items', function (Blueprint $table) {
                if (!Schema::hasColumn('goods_receipt_items', 'uom_id')) {
                    $table->unsignedBigInteger('uom_id')->nullable()->after('unit_cost');
                }
                if (!Schema::hasColumn('goods_receipt_items', 'uom_quantity')) {
                    $table->integer('uom_quantity')->default(1)->after('uom_id');
                }
                // keep legacy 'uom' string for now for backward compatibility
                try {
                    $table->foreign('uom_id')->references('id')->on('uoms');
                } catch (\Throwable $e) {}
                $table->index('uom_id');
            });
        }

        // Order Items: add uom columns
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('order_items', 'uom_id')) {
                    $table->unsignedBigInteger('uom_id')->nullable()->after('product_id');
                }
                if (!Schema::hasColumn('order_items', 'uom_quantity')) {
                    $table->integer('uom_quantity')->default(1)->after('uom_id');
                }
                try {
                    $table->foreign('uom_id')->references('id')->on('uoms');
                } catch (\Throwable $e) {}
                $table->index('uom_id');
            });
        }

        // Sale Items: add uom columns
        if (Schema::hasTable('sale_items')) {
            Schema::table('sale_items', function (Blueprint $table) {
                if (!Schema::hasColumn('sale_items', 'uom_id')) {
                    $table->unsignedBigInteger('uom_id')->nullable()->after('product_id');
                }
                if (!Schema::hasColumn('sale_items', 'uom_quantity')) {
                    $table->integer('uom_quantity')->default(1)->after('uom_id');
                }
                try {
                    $table->foreign('uom_id')->references('id')->on('uoms');
                } catch (\Throwable $e) {}
                $table->index('uom_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                try { $table->dropForeign(['uom_id']); } catch (\Throwable $e) {}
                try { $table->dropIndex(['uom_id']); } catch (\Throwable $e) {}
            });
        }
        if (Schema::hasTable('goods_receipt_items')) {
            Schema::table('goods_receipt_items', function (Blueprint $table) {
                try { $table->dropForeign(['uom_id']); } catch (\Throwable $e) {}
                try { $table->dropIndex(['uom_id']); } catch (\Throwable $e) {}
                try { $table->dropColumn(['uom_id','uom_quantity']); } catch (\Throwable $e) {}
            });
        }
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                try { $table->dropForeign(['uom_id']); } catch (\Throwable $e) {}
                try { $table->dropIndex(['uom_id']); } catch (\Throwable $e) {}
                try { $table->dropColumn(['uom_id','uom_quantity']); } catch (\Throwable $e) {}
            });
        }
        if (Schema::hasTable('sale_items')) {
            Schema::table('sale_items', function (Blueprint $table) {
                try { $table->dropForeign(['uom_id']); } catch (\Throwable $e) {}
                try { $table->dropIndex(['uom_id']); } catch (\Throwable $e) {}
                try { $table->dropColumn(['uom_id','uom_quantity']); } catch (\Throwable $e) {}
            });
        }
    }
};

