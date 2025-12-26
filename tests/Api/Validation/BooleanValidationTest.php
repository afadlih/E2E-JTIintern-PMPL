<?php

namespace Tests\Api\Validation;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BooleanValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_username_boolean_true()
    {
        $response = $this->postJson('/api/login', [
            'username' => true,
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_boolean_false()
    {
        $response = $this->postJson('/api/login', [
            'username' => false,
            'password' => 'password'
        ]);

        $response->assertStatus(422);
    }

    public function test_api_login_password_boolean_true()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => true
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_password_boolean_false()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => false
        ]);

        $response->assertStatus(422);
    }

    public function test_api_login_both_fields_boolean_true()
    {
        $response = $this->postJson('/api/login', [
            'username' => true,
            'password' => true
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_both_fields_boolean_false()
    {
        $response = $this->postJson('/api/login', [
            'username' => false,
            'password' => false
        ]);

        $response->assertStatus(422);
    }

    public function test_api_login_username_string_true()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'true',
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_string_false()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'false',
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_password_string_yes()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'yes'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_password_string_no()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'no'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }
}
