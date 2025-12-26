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
        // Migration ini seharusnya untuk update kota di m_mahasiswa
        // Tabel m_notifikasi dipindah ke migration 2025_06_08_082936_create_m_notifikasi_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback untuk update kota
    }
};
