<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clean up any duplicate active customers only
        $this->cleanupActiveDuplicates();
        
        // Add indexes for better performance (not unique constraints)
        Schema::table('customers', function (Blueprint $table) {
            // Add indexes for better query performance
            if (!$this->indexExists('customers', 'customers_company_name_index')) {
                $table->index('company_name', 'customers_company_name_index');
            }
            if (!$this->indexExists('customers', 'customers_brn_index')) {
                $table->index('brn', 'customers_brn_index');
            }
            if (!$this->indexExists('customers', 'customers_vat_index')) {
                $table->index('vat', 'customers_vat_index');
            }
        });
    }
    
    /**
     * Clean up duplicate active customers only
     */
    private function cleanupActiveDuplicates(): void
    {
        // Handle duplicate ACTIVE company names only
        $duplicates = DB::select("
            SELECT company_name, COUNT(*) as count 
            FROM customers 
            WHERE company_name IS NOT NULL AND company_name != '' AND status = 1
            GROUP BY company_name 
            HAVING count > 1
        ");
        
        foreach ($duplicates as $duplicate) {
            // Get all ACTIVE records with this company name
            $records = DB::select("
                SELECT id, created_at 
                FROM customers 
                WHERE company_name = ? AND status = 1
                ORDER BY created_at DESC
            ", [$duplicate->company_name]);
            
            if (count($records) > 1) {
                // Keep the most recent active record
                $keepId = $records[0]->id;
                
                // Delete all other active records
                $deleteIds = array_slice(array_column($records, 'id'), 1);
                if (!empty($deleteIds)) {
                    DB::delete("
                        DELETE FROM customers 
                        WHERE id IN (" . implode(',', array_fill(0, count($deleteIds), '?')) . ")
                    ", $deleteIds);
                }
            }
        }
        
        // Handle duplicate ACTIVE BRNs only
        $duplicates = DB::select("
            SELECT brn, COUNT(*) as count 
            FROM customers 
            WHERE brn IS NOT NULL AND brn != '' AND status = 1
            GROUP BY brn 
            HAVING count > 1
        ");
        
        foreach ($duplicates as $duplicate) {
            $records = DB::select("
                SELECT id, created_at 
                FROM customers 
                WHERE brn = ? AND status = 1
                ORDER BY created_at DESC
            ", [$duplicate->brn]);
            
            if (count($records) > 1) {
                $keepId = $records[0]->id;
                $deleteIds = array_slice(array_column($records, 'id'), 1);
                if (!empty($deleteIds)) {
                    DB::delete("
                        DELETE FROM customers 
                        WHERE id IN (" . implode(',', array_fill(0, count($deleteIds), '?')) . ")
                    ", $deleteIds);
                }
            }
        }
        
        // Handle duplicate ACTIVE VATs only
        $duplicates = DB::select("
            SELECT vat, COUNT(*) as count 
            FROM customers 
            WHERE vat IS NOT NULL AND vat != '' AND status = 1
            GROUP BY vat 
            HAVING count > 1
        ");
        
        foreach ($duplicates as $duplicate) {
            $records = DB::select("
                SELECT id, created_at 
                FROM customers 
                WHERE vat = ? AND status = 1
                ORDER BY created_at DESC
            ", [$duplicate->vat]);
            
            if (count($records) > 1) {
                $keepId = $records[0]->id;
                $deleteIds = array_slice(array_column($records, 'id'), 1);
                if (!empty($deleteIds)) {
                    DB::delete("
                        DELETE FROM customers 
                        WHERE id IN (" . implode(',', array_fill(0, count($deleteIds), '?')) . ")
                    ", $deleteIds);
                }
            }
        }
    }
    
    /**
     * Check if an index exists
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select("
            SELECT INDEX_NAME 
            FROM information_schema.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND INDEX_NAME = ?
        ", [$table, $indexName]);
        
        return !empty($indexes);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_company_name_index');
            $table->dropIndex('customers_brn_index');
            $table->dropIndex('customers_vat_index');
        });
    }
};