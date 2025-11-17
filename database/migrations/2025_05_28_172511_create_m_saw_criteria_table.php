<?php
// filepath: database/migrations/2025_05_28_create_m_saw_criteria_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMSawCriteriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_saw_kriteria', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('code', 50);
            $table->float('weight')->comment('Bobot kriteria dalam nilai desimal (0-1)');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default criteria
        DB::table('m_saw_kriteria')->insert([
            [
                'name' => 'Kesamaan Wilayah',
                'code' => 'wilayah',
                'weight' => 0.6,
                'description' => 'Kecocokan wilayah dosen dengan perusahaan',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Kecocokan Skill',
                'code' => 'skill',
                'weight' => 0.4,
                'description' => 'Kecocokan skill dosen dengan skill mahasiswa',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_saw_kriteria');
    }
}