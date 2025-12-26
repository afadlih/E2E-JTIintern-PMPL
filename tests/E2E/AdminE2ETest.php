<?php

namespace Tests\E2E;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;

/**
 * End-to-End Tests untuk Admin Endpoints
 * Testing CRUD operations dan authorization
 * 
 * Dapat dijalankan terhadap:
 * - Local environment
 * - Deployment/Production URL
 */
class AdminE2ETest extends TestCase
{
    protected function getBaseUrl(): string
    {
        return env('E2E_BASE_URL', 'http://localhost');
    }

    /**
     * Test E2E: Admin endpoint memerlukan authentication
     * 
     * @group e2e
     * @group e2e-admin
     * @group e2e-security
     */
    public function test_admin_endpoint_requires_auth(): void
    {
        $response = Http::get($this->getBaseUrl() . '/api/admin/mahasiswa');
        
        // Should return 401 (unauthorized) or 302 (redirect to login)
        $this->assertContains(
            $response->status(),
            [401, 302],
            'Admin endpoints should require authentication'
        );
    }

    /**
     * Test E2E: Admin periode endpoint exists
     * 
     * @group e2e
     * @group e2e-admin
     */
    public function test_admin_periode_endpoint_exists(): void
    {
        $response = Http::get($this->getBaseUrl() . '/api/admin/periode');
        
        // Should not be 404
        $this->assertNotEquals(404, $response->status(), 'Admin periode endpoint should exist');
        
        // Without auth, should be 401 or 302
        $this->assertContains($response->status(), [401, 302]);
    }

    /**
     * Test E2E: Admin create mahasiswa requires validation
     * 
     * @group e2e
     * @group e2e-admin
     * @group e2e-validation
     */
    public function test_create_mahasiswa_validation(): void
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post($this->getBaseUrl() . '/api/admin/mahasiswa', [
            // Empty data - should trigger validation
        ]);
        
        // Without auth: 401, or with auth but invalid data: 422
        $this->assertContains(
            $response->status(),
            [401, 422, 302],
            'Empty data should trigger validation or auth error'
        );
    }

    /**
     * Test E2E: Unauthorized users cannot access admin routes
     * 
     * @group e2e
     * @group e2e-security
     */
    public function test_unauthorized_cannot_access_admin(): void
    {
        // Try with invalid token
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer invalid-token-12345'
        ])->get($this->getBaseUrl() . '/api/admin/mahasiswa');
        
        $this->assertEquals(401, $response->status(), 'Invalid token should return 401');
    }

    /**
     * Test E2E: Admin endpoints use JSON format
     * 
     * @group e2e
     * @group e2e-api
     */
    public function test_admin_endpoints_return_json(): void
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->get($this->getBaseUrl() . '/api/admin/periode');
        
        $contentType = $response->header('Content-Type');
        $this->assertStringContainsString(
            'application/json',
            $contentType ?? '',
            'Admin API should return JSON'
        );
    }

    /**
     * Test E2E: Admin routes have CSRF protection on web routes
     * 
     * @group e2e
     * @group e2e-security
     */
    public function test_csrf_protection_on_web_routes(): void
    {
        // Web form submissions should require CSRF token
        $response = Http::post($this->getBaseUrl() . '/admin/mahasiswa', [
            'nim' => '2141720001',
            'nama' => 'Test'
        ]);
        
        // Should get 419 (CSRF) or 302 (redirect to login) or 405 (method not allowed)
        $this->assertContains(
            $response->status(),
            [419, 302, 405],
            'CSRF protection should be active'
        );
    }
}
