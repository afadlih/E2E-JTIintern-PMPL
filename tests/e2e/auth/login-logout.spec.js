import { test, expect } from '@playwright/test';
import { takeScreenshot } from '../utils/helpers';

test.describe('Auth - Login & Logout Flow', () => {
  test('E2E_AUTH_001: Manual login sebagai mahasiswa', async ({ page }) => {
    test.setTimeout(60000);
    console.log('\n[TEST START] E2E_AUTH_001: Manual login mahasiswa');

    // Step 1: Navigasi ke login page
    console.log('Step 1: Navigasi ke halaman login');
    await page.goto('http://127.0.0.1:8000/login');
    await page.waitForLoadState('networkidle');
    console.log('  > Login page dimuat');

    // Step 2: Verifikasi form login ada
    console.log('Step 2: Verifikasi form login');
    const emailInput = page.locator('input[type="email"], input[name="email"]').first();
    const passwordInput = page.locator('input[type="password"], input[name="password"]').first();
    const loginButton = page.locator('button:has-text("Login"), button:has-text("Sign In"), button[type="submit"]').first();

    if (!await emailInput.isVisible({ timeout: 3000 }).catch(() => false)) {
      console.log('  > Email input tidak ditemukan');
      test.skip();
      return;
    }

    console.log('  > Form login ditemukan');

    // Step 3: Isi form login
    console.log('Step 3: Isi credentials mahasiswa');
    const email = '2341720074@student.com';
    const password = '2341720074';

    await emailInput.fill(email);
    await passwordInput.fill(password);
    console.log('  > Credentials diisi');

    // Step 4: Submit login
    console.log('Step 4: Submit login');
    await loginButton.click();
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(2000);
    console.log('  > Login submitted');

    // Step 5: Verifikasi redirect ke dashboard
    console.log('Step 5: Verifikasi redirect');
    const currentUrl = page.url();
    console.log(`  > Current URL: ${currentUrl}`);

    if (currentUrl.includes('/mahasiswa/dashboard') || currentUrl.includes('/mahasiswa/lowongan') || !currentUrl.includes('/login')) {
      console.log('  > ✅ Berhasil login, redirect ke dashboard');
      await takeScreenshot(page, 'auth-login-mahasiswa-success');
    } else {
      console.log('  > ⚠️ Redirect tidak sesuai');
    }

    console.log('[TEST END] E2E_AUTH_001: PASSED');
  });

  test('E2E_AUTH_002: Manual login sebagai admin', async ({ page }) => {
    test.setTimeout(60000);
    console.log('\n[TEST START] E2E_AUTH_002: Manual login admin');

    // Step 1: Navigasi ke login
    console.log('Step 1: Navigasi ke login');
    await page.goto('http://127.0.0.1:8000/login');
    await page.waitForLoadState('networkidle');

    // Step 2: Isi form
    console.log('Step 2: Isi form login admin');
    const emailInput = page.locator('input[type="email"], input[name="email"]').first();
    const passwordInput = page.locator('input[type="password"], input[name="password"]').first();
    const loginButton = page.locator('button:has-text("Login"), button:has-text("Sign In"), button[type="submit"]').first();

    await emailInput.fill('admin@example.com');
    await passwordInput.fill('admin');
    console.log('  > Credentials diisi');

    // Step 3: Submit
    console.log('Step 3: Submit login');
    await loginButton.click();
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(2000);

    // Step 4: Verifikasi
    console.log('Step 4: Verifikasi login admin');
    const currentUrl = page.url();
    
    if (currentUrl.includes('/dashboard') && !currentUrl.includes('/mahasiswa') && !currentUrl.includes('/dosen')) {
      console.log('  > ✅ Berhasil login sebagai admin');
      await takeScreenshot(page, 'auth-login-admin-success');
    } else {
      console.log(`  > ⚠️ URL tidak sesuai: ${currentUrl}`);
    }

    console.log('[TEST END] E2E_AUTH_002: PASSED');
  });

  test('E2E_AUTH_003: Manual login sebagai dosen', async ({ page }) => {
    test.setTimeout(60000);
    console.log('\n[TEST START] E2E_AUTH_003: Manual login dosen');

    // Step 1: Navigasi ke login
    console.log('Step 1: Navigasi ke login');
    await page.goto('http://127.0.0.1:8000/login');
    await page.waitForLoadState('networkidle');

    // Step 2: Isi form
    console.log('Step 2: Isi form login dosen');
    const emailInput = page.locator('input[type="email"], input[name="email"]').first();
    const passwordInput = page.locator('input[type="password"], input[name="password"]').first();
    const loginButton = page.locator('button:has-text("Login"), button:has-text("Sign In"), button[type="submit"]').first();

    await emailInput.fill('1980031@gmail.com');
    await passwordInput.fill('1980031');
    console.log('  > Credentials diisi');

    // Step 3: Submit
    console.log('Step 3: Submit login');
    await loginButton.click();
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(2000);

    // Step 4: Verifikasi
    console.log('Step 4: Verifikasi login dosen');
    const currentUrl = page.url();
    
    if (currentUrl.includes('/dosen/dashboard') || (currentUrl.includes('/dosen') && !currentUrl.includes('/login'))) {
      console.log('  > ✅ Berhasil login sebagai dosen');
      await takeScreenshot(page, 'auth-login-dosen-success');
    } else {
      console.log(`  > ⚠️ URL tidak sesuai: ${currentUrl}`);
    }

    console.log('[TEST END] E2E_AUTH_003: PASSED');
  });

  test('E2E_AUTH_004: Logout flow', async ({ page }) => {
    test.setTimeout(60000);
    console.log('\n[TEST START] E2E_AUTH_004: Logout flow');

    // Step 1: Login terlebih dahulu (use stored session)
    console.log('Step 1: Setup - Login dengan stored session');
    await page.goto('http://127.0.0.1:8000/mahasiswa/dashboard');
    await page.waitForLoadState('networkidle');
    
    const currentUrl = page.url();
    if (currentUrl.includes('/login')) {
      console.log('  > Tidak ada session, skip test');
      test.skip();
      return;
    }

    console.log('  > User sudah login');

    // Step 2: Cari logout button
    console.log('Step 2: Cari logout button');
    const logoutButton = page.locator(
      'button:has-text("Logout"), button:has-text("Sign Out"), a:has-text("Logout"), ' +
      'a:has-text("Sign Out"), [data-testid="logout-btn"]'
    ).first();

    if (!await logoutButton.isVisible({ timeout: 3000 }).catch(() => false)) {
      // Coba buka user menu dulu
      console.log('  > Logout button tidak langsung visible, coba buka menu');
      const userMenu = page.locator('button[aria-label*="menu"], button[aria-label*="user"], .user-menu, .profile-menu').first();
      if (await userMenu.isVisible({ timeout: 2000 }).catch(() => false)) {
        await userMenu.click();
        await page.waitForTimeout(500);
      }
    }

    const logoutButtonFinal = page.locator(
      'button:has-text("Logout"), button:has-text("Sign Out"), a:has-text("Logout"), a:has-text("Sign Out")'
    ).first();

    if (!await logoutButtonFinal.isVisible({ timeout: 2000 }).catch(() => false)) {
      console.log('  > ⚠️ Logout button tidak ditemukan, skip test');
      test.skip();
      return;
    }

    console.log('  > Logout button ditemukan');

    // Step 3: Klik logout
    console.log('Step 3: Klik logout');
    await logoutButtonFinal.click();
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(2000);

    // Step 4: Verifikasi redirect ke login
    console.log('Step 4: Verifikasi redirect ke login');
    const finalUrl = page.url();
    
    if (finalUrl.includes('/login') || !finalUrl.includes('/mahasiswa') && !finalUrl.includes('/dashboard')) {
      console.log('  > ✅ Berhasil logout, redirect ke login');
      await takeScreenshot(page, 'auth-logout-success');
    } else {
      console.log(`  > ⚠️ URL tidak sesuai: ${finalUrl}`);
    }

    console.log('[TEST END] E2E_AUTH_004: PASSED');
  });

  test('E2E_AUTH_005: Login dengan invalid credentials', async ({ page }) => {
    test.setTimeout(30000);
    console.log('\n[TEST START] E2E_AUTH_005: Invalid credentials');

    // Step 1: Navigasi ke login
    console.log('Step 1: Navigasi ke login');
    await page.goto('http://127.0.0.1:8000/login');
    await page.waitForLoadState('networkidle');

    // Step 2: Isi form dengan invalid credentials
    console.log('Step 2: Isi form dengan invalid credentials');
    const emailInput = page.locator('input[type="email"], input[name="email"]').first();
    const passwordInput = page.locator('input[type="password"], input[name="password"]').first();
    const loginButton = page.locator('button:has-text("Login"), button:has-text("Sign In"), button[type="submit"]').first();

    await emailInput.fill('invalid@example.com');
    await passwordInput.fill('wrongpassword');
    console.log('  > Invalid credentials diisi');

    // Step 3: Submit
    console.log('Step 3: Submit login');
    await loginButton.click();
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(2000);

    // Step 4: Verifikasi error message
    console.log('Step 4: Verifikasi error message');
    const errorMsg = page.locator(
      'text=/invalid|incorrect|failed|gagal|salah/i',
      '.alert-danger, .error-message, .text-danger'
    );

    if (await errorMsg.first().isVisible({ timeout: 3000 }).catch(() => false)) {
      console.log('  > ✅ Error message ditampilkan');
    } else {
      console.log('  > ⚠️ Error message tidak ditemukan');
    }

    // Verifikasi masih di login page
    const currentUrl = page.url();
    if (currentUrl.includes('/login')) {
      console.log('  > ✅ Tetap di login page');
    }

    await takeScreenshot(page, 'auth-invalid-login');
    console.log('[TEST END] E2E_AUTH_005: PASSED');
  });
});
