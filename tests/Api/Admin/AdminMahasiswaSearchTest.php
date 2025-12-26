<?php

namespace Tests\Api\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Kelas;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * API Test: Admin Mahasiswa Search Features
 */
class AdminMahasiswaSearchTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->token = $this->admin->createToken('test-token')->plainTextToken;
    }

    public function test_api_search_mahasiswa_empty_query()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/admin/mahasiswa?search=');

        $response->assertStatus(200);
    }

    public function test_api_search_mahasiswa_special_characters()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/admin/mahasiswa?search=%@#$');

        $response->assertStatus(200);
    }

    public function test_api_search_mahasiswa_very_long_query()
    {
        $longQuery = str_repeat('test', 100);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/admin/mahasiswa?search=' . $longQuery);

        $response->assertStatus(200);
    }

    public function test_api_filter_mahasiswa_invalid_kelas_id()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/admin/mahasiswa?kelas=99999');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => []
            ]);
    }

    public function test_api_mahasiswa_list_pagination()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/admin/mahasiswa?page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data'
            ]);
    }
}
