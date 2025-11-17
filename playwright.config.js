/**
 * Playwright Configuration untuk Multi-Role Testing
 * E2E-JTIintern-PMPL Project
 *
 * Konfigurasi ini mendefinisikan 3 project berbeda untuk setiap role:
 * - Admin Project: Menggunakan auth-states/admin.json
 * - Mahasiswa Project: Menggunakan auth-states/mahasiswa.json
 * - Dosen Project: Menggunakan auth-states/dosen.json
 *
 * Cara menjalankan test:
 * - Semua test: npx playwright test
 * - Test spesifik role: npx playwright test --project=mahasiswa
 * - Test spesifik file: npx playwright test tests/mahasiswa/lowongan.spec.js
 * - Dengan UI: npx playwright test --ui
 */

const { defineConfig, devices } = require('@playwright/test');
const path = require('path');

// Load environment variables dari .env.playwright file
require('dotenv').config({ path: '.env.playwright' });

// Base URL aplikasi
const BASE_URL = process.env.BASE_URL || 'http://localhost:8000';

// Path ke storage state files
const AUTH_STATES = {
  admin: path.resolve(__dirname, 'tests/e2e/auth-states/admin.json'),
  mahasiswa: path.resolve(__dirname, 'tests/e2e/auth-states/mahasiswa.json'),
  dosen: path.resolve(__dirname, 'tests/e2e/auth-states/dosen.json'),
};

module.exports = defineConfig({
  // Direktori tempat test files berada
  testDir: './tests/e2e',

  // Pattern untuk test files - hanya file .spec.js
  testMatch: '**/*.spec.js',

  // Ignore setup-auth.js dan helper files
  testIgnore: ['**/setup-auth.js', '**/utils/**', '**/auth-states/**', '**/fixtures/**'],

  // Timeout untuk setiap test (60 detik)
  timeout: 60 * 1000,

  // Timeout untuk expect assertions (10 detik)
  expect: {
    timeout: 10000,
  },

  // Timeout untuk action seperti click, fill, dll (10 detik)
  actionTimeout: 10000,

  // Konfigurasi untuk menjalankan test
  fullyParallel: true, // Jalankan test secara paralel
  forbidOnly: !!process.env.CI, // Fail build jika ada test.only di CI
  retries: process.env.CI ? 2 : 0, // Retry 2x di CI, 0x di local
  workers: process.env.CI ? 1 : undefined, // 1 worker di CI, otomatis di local

  // Reporter untuk hasil test
  reporter: [
    ['html', { outputFolder: 'playwright-report', open: 'never' }],
    ['list', { printSteps: true }],
    ['json', { outputFile: 'playwright-report/test-results.json' }],
    ['junit', { outputFile: 'playwright-report/junit.xml' }],
  ],

  // Shared settings untuk semua projects
  use: {
    // Base URL untuk navigation
    baseURL: BASE_URL,

    // Collect trace saat test gagal (untuk debugging)
    trace: 'retain-on-failure',

    // Screenshot saat test gagal
    screenshot: 'only-on-failure',

    // Video recording (off untuk performa, bisa diaktifkan jika perlu)
    video: 'retain-on-failure',

    // Browser context options
    ignoreHTTPSErrors: true,
    acceptDownloads: true,

    // Viewport size
    viewport: { width: 1280, height: 720 },

    // Action timeout (15 detik)
    actionTimeout: 15 * 1000,

    // Navigation timeout (30 detik)
    navigationTimeout: 30 * 1000,
  },

  /**
   * PROJECTS: Definisi project untuk setiap role
   * Setiap project menggunakan storageState yang berbeda
   */
  projects: [
    // ============================================================
    // SETUP PROJECT: Jalankan setup-auth sebelum test lainnya
    // ============================================================
    {
      name: 'setup',
      testMatch: /setup-auth\.js/,
    },

    // ============================================================
    // ADMIN PROJECT: Test untuk role Admin/Superadmin
    // ============================================================
    {
      name: 'admin',
      testDir: './tests/e2e/admin',
      use: {
        ...devices['Desktop Chrome'],
        storageState: AUTH_STATES.admin,
      },
      dependencies: ['setup'], // Jalankan setup dulu
    },

    // ============================================================
    // MAHASISWA PROJECT: Test untuk role Mahasiswa
    // ============================================================
    {
      name: 'mahasiswa',
      testDir: './tests/e2e/mahasiswa',
      use: {
        ...devices['Desktop Chrome'],
        storageState: AUTH_STATES.mahasiswa,
      },
      dependencies: ['setup'],
    },

    // ============================================================
    // DOSEN PROJECT: Test untuk role Dosen
    // ============================================================
    {
      name: 'dosen',
      testDir: './tests/e2e/dosen',
      use: {
        ...devices['Desktop Chrome'],
        storageState: AUTH_STATES.dosen,
      },
      dependencies: ['setup'],
    },

    // ============================================================
    // MULTI-ROLE PROJECT: Test yang melibatkan multiple roles
    // ============================================================
    {
      name: 'multi-role',
      testDir: './tests/e2e/multi-role',
      use: {
        ...devices['Desktop Chrome'],
        // Multi-role test akan handle authentication sendiri
      },
      dependencies: ['setup'],
    },

    // ============================================================
    // MOBILE VIEWPORT PROJECTS (Opsional)
    // ============================================================
    // Uncomment jika ingin test di mobile viewport
    // {
    //   name: 'mahasiswa-mobile',
    //   testDir: './tests/mahasiswa',
    //   use: {
    //     ...devices['Pixel 5'],
    //     storageState: AUTH_STATES.mahasiswa,
    //   },
    //   dependencies: ['setup'],
    // },

    // ============================================================
    // FIREFOX & WEBKIT PROJECTS (Opsional)
    // ============================================================
    // Uncomment jika ingin test di browser lain
    // {
    //   name: 'mahasiswa-firefox',
    //   testDir: './tests/mahasiswa',
    //   use: {
    //     ...devices['Desktop Firefox'],
    //     storageState: AUTH_STATES.mahasiswa,
    //   },
    //   dependencies: ['setup'],
    // },
  ],

  /**
   * WEB SERVER CONFIGURATION
   * Uncomment jika ingin Playwright otomatis start web server
   */
  // webServer: {
  //   command: 'php artisan serve',
  //   url: 'http://localhost:8000',
  //   timeout: 120 * 1000,
  //   reuseExistingServer: !process.env.CI,
  // },
});
