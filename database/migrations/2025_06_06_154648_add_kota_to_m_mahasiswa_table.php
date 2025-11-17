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
        Schema::table('m_mahasiswa', function (Blueprint $table) {
            // Create the foreign key column (wilayah_id is the PK in m_wilayah table)
            $table->unsignedBigInteger('id_wilayah')->nullable()->after('alamat');

            // Add foreign key constraint
            $table->foreign('id_wilayah')
                  ->references('wilayah_id')  // The primary key column in m_wilayah is wilayah_id
                  ->on('m_wilayah')
                  ->onDelete('set null');  // If a region is deleted, set student's region to null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_mahasiswa', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['id_wilayah']);

            // Then drop the column
            $table->dropColumn('id_wilayah');
        });
    }
};
