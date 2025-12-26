<?php

namespace Tests\Api\Validation;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArrayValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_username_array()
    {
        $response = $this->postJson('/api/login', [
            'username' => ['test', 'user'],
            'password' => 'password'
        ]);

        $response->assertStatus(422);
    }

    public function test_api_login_password_array()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => ['pass', 'word']
        ]);

        $response->assertStatus(422);
    }

    public function test_api_login_both_fields_arrays()
    {
        $response = $this->postJson('/api/login', [
            'username' => ['test'],
            'password' => ['password']
        ]);

        $response->assertStatus(422);
    }

    public function test_api_login_username_empty_array()
    {
        $response = $this->postJson('/api/login', [
            'username' => [],
            'password' => 'password'
        ]);

        $response->assertStatus(422);
    }

    public function test_api_login_password_empty_array()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => []
        ]);

        $response->assertStatus(422);
    }

    public function test_api_login_username_nested_array()
    {
        $response = $this->postJson('/api/login', [
            'username' => [['test']],
            'password' => 'password'
        ]);

        $response->assertStatus(422);
    }

    public function test_api_login_username_associative_array()
    {
        $response = $this->postJson('/api/login', [
            'username' => ['name' => 'test'],
            'password' => 'password'
        ]);

        $response->assertStatus(422);
    }

    public function test_api_login_password_associative_array()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => ['value' => 'password']
        ]);

        $response->assertStatus(422);
    }

    public function test_api_login_username_mixed_array()
    {
        $response = $this->postJson('/api/login', [
            'username' => ['test', 123, true],
            'password' => 'password'
        ]);

        $response->assertStatus(422);
    }

    public function test_api_login_entire_payload_nested()
    {
        $response = $this->postJson('/api/login', [
            'credentials' => [
                'username' => 'testuser',
                'password' => 'password'
            ]
        ]);

        $response->assertStatus(422);
    }
}
