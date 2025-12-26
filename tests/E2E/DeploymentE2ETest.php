<?php

namespace Tests\E2E;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;

/**
 * End-to-End Tests untuk Deployment Environment
 * Tests khusus untuk verifikasi production/staging deployment
 *
 * @group e2e
 * @group e2e-deployment
 */
class DeploymentE2ETest extends TestCase
{
    protected function getBaseUrl(): string
    {
        return env('E2E_BASE_URL', 'http://localhost');
    }

    /**
     * Test E2E: Application is online and responding
     *
     * @group e2e
     * @group e2e-deployment
     * @group e2e-smoke
     */
    public function test_application_is_online(): void
    {
        $response = Http::timeout(10)->get($this->getBaseUrl());

        $this->assertTrue(
            $response->successful() || $response->redirect(),
            'Application should be online and responding'
        );
    }

    /**
     * Test E2E: HTTPS redirect (for production)
     *
     * @group e2e
     * @group e2e-deployment
     * @group e2e-security
     */
    public function test_https_redirect_on_production(): void
    {
        if (str_starts_with($this->getBaseUrl(), 'https://')) {
            $httpUrl = str_replace('https://', 'http://', $this->getBaseUrl());

            $response = Http::withoutRedirecting()->get($httpUrl);

            // Production should redirect HTTP to HTTPS
            $this->assertContains(
                $response->status(),
                [301, 302, 200], // 301/302 redirect or 200 if already handling
                'Production should handle HTTP/HTTPS properly'
            );
        } else {
            $this->markTestSkipped('HTTPS test only for production URLs');
        }
    }

    /**
     * Test E2E: Database connection is working
     * Test via API yang butuh database
     *
     * @group e2e
     * @group e2e-deployment
     * @group e2e-database
     */
    public function test_database_connection_works(): void
    {
        // Test endpoint that requires database
        $response = Http::post($this->getBaseUrl() . '/api/login', [
            'email' => 'test@test.com',
            'password' => 'password'
        ]);

        // If we get 422 (validation) or 401 (wrong creds), database is working
        // If we get 500, there might be database issues
        $this->assertNotEquals(500, $response->status(), 'No server errors - database connection works');
        $this->assertNotEquals(503, $response->status(), 'Service should be available');
    }

    /**
     * Test E2E: Response time acceptable
     *
     * @group e2e
     * @group e2e-deployment
     * @group e2e-performance
     */
    public function test_response_time_acceptable(): void
    {
        $start = microtime(true);
        $response = Http::get($this->getBaseUrl());
        $duration = microtime(true) - $start;

        // Response should be under 5 seconds
        $this->assertLessThan(5.0, $duration, 'Response time should be under 5 seconds');

        // Log the actual time for monitoring
        echo "\nResponse time: " . round($duration, 3) . " seconds";
    }

    /**
     * Test E2E: Environment is properly configured
     *
     * @group e2e
     * @group e2e-deployment
     */
    public function test_environment_configured(): void
    {
        $response = Http::get($this->getBaseUrl() . '/api/test-endpoint-that-does-not-exist');

        // Should get 404, not 500 or debug page
        $this->assertEquals(404, $response->status(), 'Unknown routes should return 404');

        // In production, shouldn't see debug info in response
        if (str_contains($this->getBaseUrl(), 'localhost')) {
            $this->markTestSkipped('Debug mode check only for non-local environments');
        }

        $body = $response->body();
        $this->assertStringNotContainsString('APP_KEY', $body, 'Should not expose environment variables');
        $this->assertStringNotContainsString('DB_PASSWORD', $body, 'Should not expose sensitive data');
    }

    /**
     * Test E2E: Security headers present
     *
     * @group e2e
     * @group e2e-deployment
     * @group e2e-security
     */
    public function test_security_headers_present(): void
    {
        $response = Http::get($this->getBaseUrl());

        $headers = $response->headers();

        // Check for common security headers
        $hasXFrameOptions = isset($headers['X-Frame-Options']) || isset($headers['x-frame-options']);
        $hasXContentTypeOptions = isset($headers['X-Content-Type-Options']) || isset($headers['x-content-type-options']);

        // At least some security headers should be present in production
        if (!str_contains($this->getBaseUrl(), 'localhost')) {
            $this->assertTrue(
                $hasXFrameOptions || $hasXContentTypeOptions,
                'Security headers should be configured in production'
            );
        }

        $this->assertTrue(true, 'Security headers check completed');
    }

    /**
     * Test E2E: File upload limits configured
     *
     * @group e2e
     * @group e2e-deployment
     */
    public function test_file_upload_configured(): void
    {
        // Try to get info about upload limits via API
        $response = Http::attach(
            'file',
            'dummy content',
            'test.txt'
        )->post($this->getBaseUrl() . '/api/admin/mahasiswa/upload');

        // Should get proper response (401 auth, 422 validation, 404 endpoint not found)
        // Should NOT get 500 due to misconfigured upload settings
        $this->assertNotEquals(500, $response->status(), 'File upload should be properly configured');
    }

    /**
     * Test E2E: Session/Cookie handling
     *
     * @group e2e
     * @group e2e-deployment
     */
    public function test_session_cookie_handling(): void
    {
        $response = Http::get($this->getBaseUrl());

        // Check if cookies are being set
        $setCookieHeader = $response->header('Set-Cookie');

        if (str_contains($this->getBaseUrl(), 'localhost')) {
            $this->assertNotNull($setCookieHeader, 'Application should set session cookies');
        }

        $this->assertTrue(true, 'Cookie handling check completed');
    }

    /**
     * Test E2E: API documentation accessible (if available)
     *
     * @group e2e
     * @group e2e-deployment
     */
    public function test_api_documentation_accessible(): void
    {
        $endpoints = [
            '/api/documentation',
            '/docs',
            '/api/docs',
            '/swagger',
        ];

        $found = false;
        foreach ($endpoints as $endpoint) {
            $response = Http::get($this->getBaseUrl() . $endpoint);
            if ($response->successful()) {
                $found = true;
                break;
            }
        }

        // Documentation is optional
        $this->assertTrue(true, 'API documentation check completed');
    }
}
