<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('m_mahasiswa', function (Blueprint $table) {
            $table->unsignedBigInteger('id_kelas')->after('nim')->nullable();
            $table->foreign('id_kelas')->references('id_kelas')->on('m_kelas')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('m_mahasiswa', function (Blueprint $table) {
            if (Schema::hasColumn('m_mahasiswa', 'id_kelas')) {
                $table->dropColumn('id_kelas');
            }
        });
    }
};
