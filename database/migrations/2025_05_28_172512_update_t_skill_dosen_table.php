<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTSkillDosenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the table exists
        if (Schema::hasTable('t_skill_dosen')) {
            // Check if column user_id exists and id_dosen doesn't
            if (Schema::hasColumn('t_skill_dosen', 'user_id') && !Schema::hasColumn('t_skill_dosen', 'id_dosen')) {
                // First drop any existing foreign keys on user_id if they exist
                Schema::table('t_skill_dosen', function (Blueprint $table) {
                    try {
                        $table->dropForeign(['user_id']);
                    } catch (\Exception $e) {
                        // Foreign key might not exist
                    }
                });

                // Rename the column
                Schema::table('t_skill_dosen', function (Blueprint $table) {
                    $table->renameColumn('user_id', 'id_dosen');
                });
            }

            // Add foreign key constraint
            Schema::table('t_skill_dosen', function (Blueprint $table) {
                if (!Schema::hasColumn('t_skill_dosen', 'id_dosen')) {
                    $table->unsignedBigInteger('id_dosen')->after('id');
                }

                try {
                    $table->foreign('id_dosen')
                          ->references('id_dosen')
                          ->on('m_dosen')
                          ->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            });
        } else {
            // Create the table if it doesn't exist
            Schema::create('t_skill_dosen', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_dosen');
                $table->unsignedBigInteger('id_skill');
                $table->timestamps();

                $table->foreign('id_dosen')
                      ->references('id_dosen')
                      ->on('m_dosen')
                      ->onDelete('cascade');
                      
                $table->foreign('id_skill')
                      ->references('id_skill')
                      ->on('m_skill')
                      ->onDelete('cascade');
                      
                $table->unique(['id_dosen', 'id_skill']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // We cannot reliably undo this migration
        // as we don't know the original state
    }
}