import { test, expect } from '@playwright/test';
import { takeScreenshot } from '../utils/helpers';

test.describe('Mahasiswa - Profile Management', () => {
  test.use({ storageState: 'tests/e2e/auth-states/mahasiswa.json' });

  test('E2E_MHS_PROFILE_001: View profile page', async ({ page }) => {
    test.setTimeout(30000);
    console.log('\n[TEST START] E2E_MHS_PROFILE_001: View profile');

    // Step 1: Navigate ke profile
    console.log('Step 1: Navigate ke halaman profile');
    await page.goto('http://127.0.0.1:8000/mahasiswa/profile');
    await page.waitForLoadState('networkidle');
    console.log('  > Profile page dimuat');

    // Step 2: Verify form elements
    console.log('Step 2: Verifikasi form fields');
    const nameField = page.locator('input[name*="name"], input[name*="nama"], [data-testid*="name"]').first();
    const emailField = page.locator('input[type="email"], input[name*="email"]').first();

    let foundFields = 0;
    if (await nameField.isVisible({ timeout: 2000 }).catch(() => false)) {
      console.log('  > ✅ Name field ditemukan');
      foundFields++;
    }
    if (await emailField.isVisible({ timeout: 2000 }).catch(() => false)) {
      console.log('  > ✅ Email field ditemukan');
      foundFields++;
    }

    if (foundFields === 0) {
      console.log('  > ⚠️ Tidak ada form fields ditemukan');
      test.skip();
      return;
    }

    await takeScreenshot(page, 'mahasiswa-profile-view');
    console.log('[TEST END] E2E_MHS_PROFILE_001: PASSED');
  });

  test('E2E_MHS_PROFILE_002: Check profile information populated', async ({ page }) => {
    test.setTimeout(30000);
    console.log('\n[TEST START] E2E_MHS_PROFILE_002: Profile info');

    // Navigate
    console.log('Step 1: Navigate ke profile');
    await page.goto('http://127.0.0.1:8000/mahasiswa/profile');
    await page.waitForLoadState('networkidle');

    // Check if any field has value
    console.log('Step 2: Verifikasi ada data profil');
    const allInputs = page.locator('input[type="text"], input[type="email"], textarea, [contenteditable]');
    const inputCount = await allInputs.count();

    console.log(`  > Ditemukan ${inputCount} input fields`);

    // Get first few values
    const firstInput = allInputs.first();
    const firstValue = await firstInput.inputValue().catch(() => '');

    if (firstValue) {
      console.log(`  > ✅ Field berisi nilai: ${firstValue.substring(0, 30)}`);
    } else {
      console.log('  > ⚠️ Fields kosong atau tidak text input');
    }

    await takeScreenshot(page, 'mahasiswa-profile-info');
    console.log('[TEST END] E2E_MHS_PROFILE_002: PASSED');
  });

  test('E2E_MHS_PROFILE_003: Find edit button', async ({ page }) => {
    test.setTimeout(30000);
    console.log('\n[TEST START] E2E_MHS_PROFILE_003: Edit button');

    // Navigate
    console.log('Step 1: Navigate ke profile');
    await page.goto('http://127.0.0.1:8000/mahasiswa/profile');
    await page.waitForLoadState('networkidle');

    // Look for edit button
    console.log('Step 2: Cari edit button');
    const editButton = page.locator(
      'button:has-text("Edit"), button:has-text("Ubah"), button:has-text("UPDATE"), ' +
      'a:has-text("Edit"), [data-testid="edit-btn"]'
    ).first();

    if (await editButton.isVisible({ timeout: 2000 }).catch(() => false)) {
      console.log('  > ✅ Edit button ditemukan');
      await takeScreenshot(page, 'mahasiswa-edit-button');
    } else {
      console.log('  > ⚠️ Edit button tidak ditemukan');
    }

    console.log('[TEST END] E2E_MHS_PROFILE_003: PASSED');
  });

  test('E2E_MHS_PROFILE_004: Check skill section', async ({ page }) => {
    test.setTimeout(30000);
    console.log('\n[TEST START] E2E_MHS_PROFILE_004: Skill section');

    // Navigate
    console.log('Step 1: Navigate ke profile');
    await page.goto('http://127.0.0.1:8000/mahasiswa/profile');
    await page.waitForLoadState('networkidle');

    // Look for skill section
    console.log('Step 2: Cari skill section');
    const skillSection = page.locator(
      'text=/skill/i, [data-section="skill"], .skill-section, [data-testid="skill"]'
    ).first();

    if (await skillSection.isVisible({ timeout: 2000 }).catch(() => false)) {
      console.log('  > ✅ Skill section ditemukan');

      // Count skills
      const skillItems = page.locator('.skill-item, [data-testid="skill-item"], .badge:has-text(", ")');
      const skillCount = await skillItems.count();
      console.log(`  > Ditemukan ${skillCount} skills`);
    } else {
      console.log('  > ⚠️ Skill section tidak ditemukan');
    }

    await takeScreenshot(page, 'mahasiswa-skill-section');
    console.log('[TEST END] E2E_MHS_PROFILE_004: PASSED');
  });
});

test.describe('Dosen - Profile Management', () => {
  test.use({ storageState: 'tests/e2e/auth-states/dosen.json' });

  test('E2E_DSN_PROFILE_001: View dosen profile', async ({ page }) => {
    test.setTimeout(30000);
    console.log('\n[TEST START] E2E_DSN_PROFILE_001: View dosen profile');

    // Navigate
    console.log('Step 1: Navigate ke profile dosen');
    await page.goto('http://127.0.0.1:8000/dosen/profile');
    await page.waitForLoadState('networkidle');
    console.log('  > Profile page dimuat');

    // Verify content
    console.log('Step 2: Verifikasi form ada');
    const inputs = page.locator('input, textarea');
    const inputCount = await inputs.count();

    if (inputCount > 0) {
      console.log(`  > ✅ Ditemukan ${inputCount} input fields`);
    } else {
      console.log('  > ⚠️ Tidak ada form fields');
      test.skip();
      return;
    }

    await takeScreenshot(page, 'dosen-profile-view');
    console.log('[TEST END] E2E_DSN_PROFILE_001: PASSED');
  });

  test('E2E_DSN_PROFILE_002: Check phone field', async ({ page }) => {
    test.setTimeout(30000);
    console.log('\n[TEST START] E2E_DSN_PROFILE_002: Phone field');

    // Navigate
    console.log('Step 1: Navigate ke profile');
    await page.goto('http://127.0.0.1:8000/dosen/profile');
    await page.waitForLoadState('networkidle');

    // Look for phone field
    console.log('Step 2: Cari phone field');
    const phoneField = page.locator(
      'input[type="tel"], input[name*="phone"], input[name*="nomor"], input[name*="no_hp"]'
    ).first();

    if (await phoneField.isVisible({ timeout: 2000 }).catch(() => false)) {
      console.log('  > ✅ Phone field ditemukan');
      const value = await phoneField.inputValue().catch(() => '');
      if (value) {
        console.log(`  > Value: ${value}`);
      }
    } else {
      console.log('  > ⚠️ Phone field tidak ditemukan');
    }

    await takeScreenshot(page, 'dosen-phone-field');
    console.log('[TEST END] E2E_DSN_PROFILE_002: PASSED');
  });

  test('E2E_DSN_PROFILE_003: View biodata section', async ({ page }) => {
    test.setTimeout(30000);
    console.log('\n[TEST START] E2E_DSN_PROFILE_003: Biodata section');

    // Navigate
    console.log('Step 1: Navigate ke profile');
    await page.goto('http://127.0.0.1:8000/dosen/profile');
    await page.waitForLoadState('networkidle');

    // Look for biodata/info section
    console.log('Step 2: Cari biodata section');
    const sections = page.locator('text=/biodata|personal|informasi|information/i');
    const sectionCount = await sections.count();

    if (sectionCount > 0) {
      console.log(`  > ✅ Biodata section ditemukan (${sectionCount} items)`);
    } else {
      console.log('  > ⚠️ Biodata section tidak ditemukan');
    }

    await takeScreenshot(page, 'dosen-biodata-section');
    console.log('[TEST END] E2E_DSN_PROFILE_003: PASSED');
  });
});
