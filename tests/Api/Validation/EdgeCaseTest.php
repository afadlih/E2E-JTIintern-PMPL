<?php

namespace Tests\Api\Validation;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EdgeCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_request_without_content_type()
    {
        $response = $this->post('/api/login', [
            'username' => 'testuser',
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [302, 401, 422]);
    }

    public function test_api_login_username_with_leading_spaces()
    {
        $response = $this->postJson('/api/login', [
            'username' => '   testuser',
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_with_trailing_spaces()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser   ',
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_password_with_leading_spaces()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => '   password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_password_with_trailing_spaces()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'password   '
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_case_sensitive_username()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'TestUser',
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_minimum_one_char()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'a',
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_password_minimum_one_char()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'a'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_with_underscore()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'test_user',
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_with_hyphen()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'test-user',
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }
}
