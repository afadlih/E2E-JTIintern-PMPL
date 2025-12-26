<?php

namespace Tests\Api\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginEdgeCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_username_null()
    {
        $response = $this->postJson('/api/login', [
            'username' => null,
            'password' => 'password'
        ]);

        $response->assertStatus(422);
    }

    public function test_api_login_password_null()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => null
        ]);

        $response->assertStatus(422);
    }

    public function test_api_login_both_fields_null()
    {
        $response = $this->postJson('/api/login', [
            'username' => null,
            'password' => null
        ]);

        $response->assertStatus(422);
    }

    public function test_api_login_empty_json_body()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422);
    }

    public function test_api_login_extra_fields_ignored()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'password',
            'extra_field' => 'should be ignored'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }
}
