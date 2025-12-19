<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_berhasil_register()
    {
        $data = [
            'nama' => 'Cindy Laili',
            'email' => 'cindy@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(201);
        $response->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('m_user', [
            'email' => 'cindy@example.com'
        ]);
    }

    /** @test */
    public function register_gagal_email_sudah_terdaftar()
    {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $data = [
            'nama' => 'User Baru',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    /** @test */
    public function register_gagal_email_tidak_valid()
    {
        $data = [
            'nama' => 'User Baru',
            'email' => 'salah-format-email',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    /** @test */
    public function register_gagal_password_kurang_dari_minimal()
    {
        $data = [
            'nama' => 'User Baru',
            'email' => 'valid@example.com',
            'password' => '123',
            'password_confirmation' => '123'
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }
}
