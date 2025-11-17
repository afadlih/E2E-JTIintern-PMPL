/**
 * Setup Authentication untuk Multi-Role Testing
 * Script ini akan login sebagai setiap role dan menyimpan session ke file storageState
 *
 * Cara menjalankan:
 * node tests/setup-auth.js
 *
 * File yang dihasilkan:
 * - tests/auth-states/admin.json
 * - tests/auth-states/mahasiswa.json
 * - tests/auth-states/dosen.json
 */

const { chromium } = require('@playwright/test');
const path = require('path');
require('dotenv').config({ path: '.env.playwright' });

// Konfigurasi kredensial untuk setiap role
// PENTING: Ganti dengan kredensial sesuai environment Anda
const credentials = {
  admin: {
    email: process.env.ADMIN_EMAIL || 'admin@example.com',
    password: process.env.ADMIN_PASSWORD || 'secret',
    expectedUrl: '/dashboard',
    storageFile: 'tests/e2e/auth-states/admin.json'
  },
  mahasiswa: {
    email: process.env.MAHASISWA_EMAIL || 'mahasiswa1@example.com',
    password: process.env.MAHASISWA_PASSWORD || 'secret',
    expectedUrl: '/mahasiswa/dashboard',
    storageFile: 'tests/e2e/auth-states/mahasiswa.json'
  },
  dosen: {
    email: process.env.DOSEN_EMAIL || 'dosen@example.com',
    password: process.env.DOSEN_PASSWORD || 'secret',
    expectedUrl: '/dosen/dashboard',
    storageFile: 'tests/e2e/auth-states/dosen.json'
  }
};

// Base URL aplikasi
const BASE_URL = process.env.BASE_URL || 'http://localhost:8000';

/**
 * Function untuk melakukan login dan menyimpan storageState
 */
async function setupAuthForRole(role, config) {
  console.log(`\n[AUTH SETUP] Starting authentication for: ${role.toUpperCase()}`);

  const browser = await chromium.launch({
    headless: true, // Headless mode untuk speed
    slowMo: 100
  });

  const context = await browser.newContext({
    ignoreHTTPSErrors: true,
    acceptDownloads: true,
    viewport: { width: 1280, height: 720 }
  });

  const page = await context.newPage();

  try {
    // 1. Navigate ke halaman login
    console.log(`  Step 1: Navigating to ${BASE_URL}/login`);
    await page.goto(`${BASE_URL}/login`, {
      waitUntil: 'domcontentloaded',
      timeout: 60000
    });
    console.log('  > Page loaded successfully');

    // 2. Tunggu form login muncul
    console.log('  Step 2: Waiting for login form');
    await page.waitForSelector('input[name="email"]', { timeout: 5000 });
    console.log('  > Form elements found');

    // 3. Clear dan isi form login (form punya default values)
    console.log(`  Step 3: Filling login form (${config.email})`);
    await page.locator('input[name="email"]').clear();
    await page.locator('input[name="email"]').fill(config.email);
    await page.locator('input[name="password"]').clear();
    await page.locator('input[name="password"]').fill(config.password);

    // Tunggu sebentar untuk ensure value ter-fill
    await page.waitForTimeout(500);
    console.log('  > Form filled successfully');

    // 4. Click tombol login dan tunggu navigation
    console.log('  Step 4: Submitting login form');
    await page.click('button[type="submit"]');

    // Tunggu navigation dengan timeout yang lebih panjang
    console.log('  Step 5: Waiting for navigation');
    try {
      await page.waitForURL(url => url.href !== `${BASE_URL}/login`, { timeout: 10000 });
      console.log('  > Navigation detected');
    } catch (e) {
      console.log('  > WARNING: No navigation detected, checking for errors...');
      const errorText = await page.textContent('body').catch(() => '');
      console.log(`  > Page content: ${errorText.substring(0, 200)}`);
    }

    // 5. Wait a bit more untuk ensure redirect complete
    await page.waitForTimeout(1000);

    // 6. Verifikasi login berhasil dengan mengecek URL
    console.log('  Step 6: Verifying login success');
    const currentUrl = page.url();
    console.log(`  > Current URL: ${currentUrl}`);

    if (!currentUrl.includes(config.expectedUrl)) {
      throw new Error(`Login failed. Expected URL to contain '${config.expectedUrl}', but got '${currentUrl}'`);
    }

    console.log('  > Login successful!');

    // 7. Simpan session ke storageState file
    console.log('  Step 7: Saving session state');
    const storagePath = path.resolve(__dirname, '..', config.storageFile);
    await context.storageState({ path: storagePath });

    console.log(`  > Storage state saved: ${path.basename(storagePath)}`);

  } catch (error) {
    console.error(`   ❌ Error during ${role} authentication:`, error.message);
    throw error;
  } finally {
    await browser.close();
  }
}

/**
 * Main function untuk setup semua role
 */
async function setupAllAuth() {
  console.log('🚀 Starting Multi-Role Authentication Setup...');
  console.log(`📍 Base URL: ${BASE_URL}\n`);

  const roles = Object.keys(credentials);
  const results = {
    success: [],
    failed: []
  };

  for (const role of roles) {
    try {
      await setupAuthForRole(role, credentials[role]);
      results.success.push(role);
    } catch (error) {
      results.failed.push({ role, error: error.message });
    }
  }

  // Summary
  console.log('\n' + '='.repeat(60));
  console.log('📊 AUTHENTICATION SETUP SUMMARY');
  console.log('='.repeat(60));
  console.log(`✅ Successful: ${results.success.join(', ') || 'None'}`);
  console.log(`❌ Failed: ${results.failed.map(f => f.role).join(', ') || 'None'}`);

  if (results.failed.length > 0) {
    console.log('\n❌ Failed Details:');
    results.failed.forEach(f => {
      console.log(`   - ${f.role}: ${f.error}`);
    });
    process.exit(1);
  } else {
    console.log('\n🎉 All authentication setups completed successfully!');
    console.log('✨ Anda sekarang dapat menjalankan test dengan: npx playwright test');
  }
}

// Jalankan setup
setupAllAuth().catch(error => {
  console.error('💥 Fatal error:', error);
  process.exit(1);
});
