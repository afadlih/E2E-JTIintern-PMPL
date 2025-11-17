<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateColumnsToMPeriodeTable extends Migration
{
    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_periode', function (Blueprint $table) {
            $table->date('tgl_mulai')->nullable()->after('waktu');
            $table->date('tgl_selesai')->nullable()->after('tgl_mulai');
        });
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_periode', function (Blueprint $table) {
            $table->dropColumn('tgl_mulai');
            $table->dropColumn('tgl_selesai');
        });
    }
}