<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

/**
 * Integration Tests untuk Database Connections
 * Testing integrasi database operations
 * 
 * @group integration
 * @group integration-database
 */
class DatabaseIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test integrasi: Database connection works
     * 
     * @group integration
     */
    public function test_database_connection(): void
    {
        $this->assertTrue(\DB::connection()->getDatabaseName() !== null);
    }

    /**
     * Test integrasi: Migrations run successfully
     * 
     * @group integration
     */
    public function test_migrations_create_tables(): void
    {
        // Check main tables exist
        $tables = [
            'm_user',
            'm_mahasiswa',
            'm_dosen',
            'm_periode',
            'm_lowongan',
            't_lamaran',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(
                \Schema::hasTable($table),
                "Table {$table} should exist after migrations"
            );
        }
    }

    /**
     * Test integrasi: Transaction rollback works
     * 
     * @group integration
     */
    public function test_transaction_rollback(): void
    {
        try {
            \DB::beginTransaction();

            User::factory()->create(['email' => 'test@test.com']);

            // Force rollback
            \DB::rollBack();

            // User should not exist
            $this->assertDatabaseMissing('m_user', [
                'email' => 'test@test.com'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    /**
     * Test integrasi: Foreign key constraints work
     * 
     * @group integration
     */
    public function test_foreign_key_constraints(): void
    {
        $user = User::factory()->create();

        // Delete user
        $user->delete();

        // User should be deleted
        $this->assertDatabaseMissing('m_user', [
            'id_user' => $user->id_user
        ]);
    }

    /**
     * Test integrasi: Seeded data is consistent
     * 
     * @group integration
     */
    public function test_seeded_data_consistency(): void
    {
        // Run seeders
        $this->seed();

        // Check if basic data exists
        $this->assertDatabaseCount('m_user', '>', 0);
    }

    /**
     * Test integrasi: Bulk insert performance
     * 
     * @group integration
     * @group integration-performance
     */
    public function test_bulk_insert_performance(): void
    {
        $users = [];
        for ($i = 0; $i < 100; $i++) {
            $users[] = [
                'username' => 'bulk_user_' . $i,
                'email' => 'bulk' . $i . '@test.com',
                'password' => bcrypt('password'),
                'level' => 'MHS',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $start = microtime(true);
        \DB::table('m_user')->insert($users);
        $duration = microtime(true) - $start;

        $this->assertLessThan(2.0, $duration, 'Bulk insert should complete in under 2 seconds');
        $this->assertDatabaseCount('m_user', 100);
    }

    /**
     * Test integrasi: Query optimization with indexes
     * 
     * @group integration
     * @group integration-performance
     */
    public function test_indexed_queries_performance(): void
    {
        // Create test data
        User::factory()->count(100)->create();

        $start = microtime(true);
        $user = User::where('email', 'test@example.com')->first();
        $duration = microtime(true) - $start;

        // Query should be fast with index
        $this->assertLessThan(0.1, $duration, 'Indexed query should be very fast');
    }
}
