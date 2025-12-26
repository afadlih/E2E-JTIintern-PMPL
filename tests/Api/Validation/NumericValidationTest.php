<?php

namespace Tests\Api\Validation;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NumericValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_username_numeric()
    {
        $response = $this->postJson('/api/login', [
            'username' => 123456,
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_password_numeric()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 123456789
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_zero()
    {
        $response = $this->postJson('/api/login', [
            'username' => 0,
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_password_zero()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 0
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_negative_number()
    {
        $response = $this->postJson('/api/login', [
            'username' => -123,
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_float()
    {
        $response = $this->postJson('/api/login', [
            'username' => 12.34,
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_password_float()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 12.34
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_scientific_notation()
    {
        $response = $this->postJson('/api/login', [
            'username' => 1e5,
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_password_large_number()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 999999999999
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_both_fields_numbers()
    {
        $response = $this->postJson('/api/login', [
            'username' => 12345,
            'password' => 67890
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }
}
