<?php
// filepath: database/migrations/2025_05_28_create_m_dosen_workload_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMDosenWorkloadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_dosen_beban_kerja', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('id_dosen');
            $table->integer('max_mahasiswa')->default(10);
            $table->integer('current_mahasiswa')->default(0);
            $table->timestamps();

            $table->foreign('id_dosen')
                  ->references('id_dosen')
                  ->on('m_dosen')
                  ->onDelete('cascade');
        });
        
        // Auto-populate workload for existing dosen
        DB::statement("
            INSERT INTO m_dosen_beban_kerja (id_dosen, created_at, updated_at)
            SELECT id_dosen, NOW(), NOW() FROM m_dosen
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_dosen_beban_kerja');
    }
}