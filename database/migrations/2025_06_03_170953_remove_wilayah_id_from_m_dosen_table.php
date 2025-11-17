<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveWilayahIdFromMDosenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_dosen', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu jika ada
            if (Schema::hasColumn('m_dosen', 'wilayah_id')) {
                // Cari foreign key constraints untuk kolom wilayah_id
                $foreignKeys = $this->getForeignKeysForColumn('m_dosen', 'wilayah_id');
                
                // Hapus foreign key constraints jika ditemukan
                foreach ($foreignKeys as $foreignKey) {
                    Schema::table('m_dosen', function (Blueprint $table) use ($foreignKey) {
                        $table->dropForeign($foreignKey);
                    });
                }
                
                // Hapus kolom wilayah_id
                $table->dropColumn('wilayah_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_dosen', function (Blueprint $table) {
            // Menambahkan kembali kolom wilayah_id jika rollback
            if (!Schema::hasColumn('m_dosen', 'wilayah_id')) {
                $table->unsignedBigInteger('wilayah_id')->nullable();
                
                // Tambahkan kembali foreign key
                $table->foreign('wilayah_id')
                    ->references('id_wilayah')
                    ->on('m_wilayah')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            }
        });
    }
    
    /**
     * Get the foreign key constraints that reference a column
     *
     * @param string $table
     * @param string $column
     * @return array
     */
    private function getForeignKeysForColumn($table, $column)
    {
        $foreignKeys = [];
        
        // Gunakan database-specific query untuk mendapatkan foreign keys
        try {
            // MySQL / MariaDB approach
            $constraints = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = '{$table}'
                AND COLUMN_NAME = '{$column}'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            foreach ($constraints as $constraint) {
                $foreignKeys[] = $constraint->CONSTRAINT_NAME;
            }
        } catch (\Exception $e) {
            // Fallback for other databases: just drop the column without checking FK
            // This is not ideal, but provides a backup approach
        }
        
        return $foreignKeys;
    }
}