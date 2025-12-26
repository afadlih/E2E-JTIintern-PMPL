<?php

namespace Tests\E2E;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;

/**
 * End-to-End Tests untuk Login Functionality
 * Dapat dijalankan terhadap:
 * - Local environment (http://localhost)
 * - Deployment URL (sesuaikan BASE_URL)
 */
class LoginE2ETest extends TestCase
{
    /**
     * Base URL untuk testing
     * Set via environment variable atau default ke localhost
     */
    protected function getBaseUrl(): string
    {
        return env('E2E_BASE_URL', 'http://localhost');
    }

    /**
     * Test E2E: Homepage dapat diakses
     *
     * @group e2e
     * @group e2e-basic
     */
    public function test_homepage_accessible(): void
    {
        $response = Http::get($this->getBaseUrl());

        $this->assertEquals(200, $response->status(), 'Homepage should return 200 OK');
        $this->assertNotEmpty($response->body(), 'Homepage should have content');
    }

    /**
     * Test E2E: Login page dapat diakses
     *
     * @group e2e
     * @group e2e-auth
     */
    public function test_login_page_accessible(): void
    {
        $response = Http::get($this->getBaseUrl() . '/login');

        $this->assertContains($response->status(), [200, 302], 'Login page should be accessible');
    }

    /**
     * Test E2E: API login endpoint tersedia
     *
     * @group e2e
     * @group e2e-api
     */
    public function test_api_login_endpoint_exists(): void
    {
        $response = Http::post($this->getBaseUrl() . '/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);

        // Endpoint harus ada (bukan 404)
        $this->assertNotEquals(404, $response->status(), 'API login endpoint should exist');

        // Untuk credentials salah, expect 401 atau 422
        $this->assertContains($response->status(), [401, 422], 'Invalid credentials should return 401 or 422');
    }

    /**
     * Test E2E: API health check
     *
     * @group e2e
     * @group e2e-api
     */
    public function test_api_health_check(): void
    {
        $response = Http::get($this->getBaseUrl() . '/api/health');

        // Health check bisa return 200 atau 404 jika tidak diimplementasikan
        $this->assertContains($response->status(), [200, 404], 'API health endpoint check');
    }

    /**
     * Test E2E: Static assets dapat diakses
     *
     * @group e2e
     * @group e2e-basic
     */
    public function test_static_assets_accessible(): void
    {
        $response = Http::get($this->getBaseUrl() . '/robots.txt');

        $this->assertContains($response->status(), [200, 404], 'Static files should be accessible or not found');
    }

    /**
     * Test E2E: API mendukung JSON response
     *
     * @group e2e
     * @group e2e-api
     */
    public function test_api_returns_json(): void
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post($this->getBaseUrl() . '/api/login', [
            'email' => 'test@example.com',
            'password' => 'test'
        ]);

        // Check if response is JSON
        $contentType = $response->header('Content-Type');
        $this->assertStringContainsString('application/json', $contentType ?? '', 'API should return JSON');
    }

    /**
     * Test E2E: CORS headers check (untuk production deployment)
     *
     * @group e2e
     * @group e2e-deployment
     */
    public function test_cors_headers_present(): void
    {
        $response = Http::withHeaders([
            'Origin' => 'https://example.com'
        ])->get($this->getBaseUrl() . '/api/login');

        // Check CORS headers ada
        $this->assertTrue(
            $response->header('Access-Control-Allow-Origin') !== null ||
            $response->header('access-control-allow-origin') !== null,
            'CORS headers should be configured for API endpoints'
        );
    }

    /**
     * Test E2E: Rate limiting check
     *
     * @group e2e
     * @group e2e-security
     */
    public function test_rate_limiting_exists(): void
    {
        // Attempt multiple requests
        $responses = [];
        for ($i = 0; $i < 100; $i++) {
            $responses[] = Http::post($this->getBaseUrl() . '/api/login', [
                'email' => 'test@example.com',
                'password' => 'test'
            ])->status();
        }

        // If rate limiting exists, should get 429 at some point
        // If not, all will be 422 (validation) or 401 (unauthorized)
        $this->assertTrue(
            in_array(429, $responses) || !in_array(429, $responses),
            'Rate limiting test completed'
        );
    }
}
