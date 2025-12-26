<?php

namespace Tests\Api\Validation;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StringValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_username_only_spaces()
    {
        $response = $this->postJson('/api/login', [
            'username' => '     ',
            'password' => 'password'
        ]);

        $response->assertStatus(422);
    }

    public function test_api_login_username_very_long()
    {
        $response = $this->postJson('/api/login', [
            'username' => str_repeat('a', 500),
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_password_only_spaces()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => '     '
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_with_newlines()
    {
        $response = $this->postJson('/api/login', [
            'username' => "test\nuser",
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_with_tabs()
    {
        $response = $this->postJson('/api/login', [
            'username' => "test\tuser",
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_password_with_unicode()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'pâsswörd123'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_with_emoji()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'test😀user',
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_both_fields_empty_strings()
    {
        $response = $this->postJson('/api/login', [
            'username' => '',
            'password' => ''
        ]);

        $response->assertStatus(422);
    }

    public function test_api_login_username_with_sql_comment()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'admin-- ',
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }

    public function test_api_login_username_with_html_tags()
    {
        $response = $this->postJson('/api/login', [
            'username' => '<admin>',
            'password' => 'password'
        ]);

        $this->assertContains($response->status(), [401, 422]);
    }
}
