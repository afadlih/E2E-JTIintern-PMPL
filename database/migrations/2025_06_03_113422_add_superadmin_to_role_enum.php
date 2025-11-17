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
        // First, get the current enum values
        $currentEnumValues = $this->getCurrentEnumValues('m_user', 'role');
        
        // Check if 'superadmin' is already in the enum
        if (!in_array('superadmin', $currentEnumValues)) {
            // Add 'superadmin' to the enum values
            $enumValues = implode("','", array_merge($currentEnumValues, ['superadmin']));
            DB::statement("ALTER TABLE m_user MODIFY role ENUM('$enumValues') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get the current enum values
        $currentEnumValues = $this->getCurrentEnumValues('m_user', 'role');
        
        // Remove 'superadmin' from the enum values
        $enumValues = array_filter($currentEnumValues, function($value) {
            return $value !== 'superadmin';
        });
        
        // Update the enum
        $enumValuesStr = implode("','", $enumValues);
        DB::statement("ALTER TABLE m_user MODIFY role ENUM('$enumValuesStr') NOT NULL");
    }

    /**
     * Get current enum values for a column
     */
    private function getCurrentEnumValues($table, $column)
    {
        $columnInfo = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = '{$column}'")[0];
        preg_match('/^enum\((.*)\)$/', $columnInfo->Type, $matches);
        
        $enumValues = [];
        if (isset($matches[1])) {
            $enumValuesStr = $matches[1];
            // Remove quotes and split by comma
            $enumValues = array_map(function($value) {
                return trim($value, "'");
            }, explode(',', $enumValuesStr));
        }
        
        return $enumValues;
    }
};