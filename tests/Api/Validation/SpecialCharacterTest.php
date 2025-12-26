<?php

namespace Tests\Api\Validation;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SpecialCharacterTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_username_with_quotes()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'test"user',
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_with_single_quotes()
    {
        $response = $this->postJson('/api/login', [
            'username' => "test'user",
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_with_backslash()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'test\\user',
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_with_percent()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'test%user',
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_with_ampersand()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'test&user',
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_password_with_equals()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'pass=word'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_password_with_plus()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'pass+word'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_password_with_asterisk()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'pass*word'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_with_dollar()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'test$user',
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_with_exclamation()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'test!user',
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }
}
