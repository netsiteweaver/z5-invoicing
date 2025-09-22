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
        // First, clean up any duplicate values by keeping only the most recent active record
        $this->cleanupDuplicates();
        
        // Then add unique constraints
        Schema::table('customers', function (Blueprint $table) {
            // Check if constraints don't already exist before adding them
            if (!$this->constraintExists('customers', 'customers_company_name_unique')) {
                $table->unique('company_name', 'customers_company_name_unique');
            }
            if (!$this->constraintExists('customers', 'customers_brn_unique')) {
                $table->unique('brn', 'customers_brn_unique');
            }
            if (!$this->constraintExists('customers', 'customers_vat_unique')) {
                $table->unique('vat', 'customers_vat_unique');
            }
        });
    }
    
    /**
     * Clean up duplicate values by keeping only the most recent active record
     */
    private function cleanupDuplicates(): void
    {
        // Handle duplicate company names
        $duplicates = DB::select("
            SELECT company_name, COUNT(*) as count 
            FROM customers 
            WHERE company_name IS NOT NULL AND company_name != '' 
            GROUP BY company_name 
            HAVING count > 1
        ");
        
        foreach ($duplicates as $duplicate) {
            // Keep the most recent active record, delete others
            $keepRecord = DB::select("
                SELECT id FROM customers 
                WHERE company_name = ? AND status = 1 
                ORDER BY created_at DESC 
                LIMIT 1
            ", [$duplicate->company_name]);
            
            if (!empty($keepRecord)) {
                $keepId = $keepRecord[0]->id;
                DB::delete("
                    DELETE FROM customers 
                    WHERE company_name = ? AND id != ? AND status = 1
                ", [$duplicate->company_name, $keepId]);
            }
        }
        
        // Handle duplicate BRNs
        $duplicates = DB::select("
            SELECT brn, COUNT(*) as count 
            FROM customers 
            WHERE brn IS NOT NULL AND brn != '' 
            GROUP BY brn 
            HAVING count > 1
        ");
        
        foreach ($duplicates as $duplicate) {
            // Keep the most recent active record, delete others
            $keepRecord = DB::select("
                SELECT id FROM customers 
                WHERE brn = ? AND status = 1 
                ORDER BY created_at DESC 
                LIMIT 1
            ", [$duplicate->brn]);
            
            if (!empty($keepRecord)) {
                $keepId = $keepRecord[0]->id;
                DB::delete("
                    DELETE FROM customers 
                    WHERE brn = ? AND id != ? AND status = 1
                ", [$duplicate->brn, $keepId]);
            }
        }
        
        // Handle duplicate VATs
        $duplicates = DB::select("
            SELECT vat, COUNT(*) as count 
            FROM customers 
            WHERE vat IS NOT NULL AND vat != '' 
            GROUP BY vat 
            HAVING count > 1
        ");
        
        foreach ($duplicates as $duplicate) {
            // Keep the most recent active record, delete others
            $keepRecord = DB::select("
                SELECT id FROM customers 
                WHERE vat = ? AND status = 1 
                ORDER BY created_at DESC 
                LIMIT 1
            ", [$duplicate->vat]);
            
            if (!empty($keepRecord)) {
                $keepId = $keepRecord[0]->id;
                DB::delete("
                    DELETE FROM customers 
                    WHERE vat = ? AND id != ? AND status = 1
                ", [$duplicate->vat, $keepId]);
            }
        }
    }
    
    /**
     * Check if a constraint exists
     */
    private function constraintExists(string $table, string $constraintName): bool
    {
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND CONSTRAINT_NAME = ?
        ", [$table, $constraintName]);
        
        return !empty($constraints);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique('customers_company_name_unique');
            $table->dropUnique('customers_brn_unique');
            $table->dropUnique('customers_vat_unique');
        });
    }
};