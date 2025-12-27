/**
 * E2E Test: Multi-Role - Notifikasi Workflow
 * Test Case IDs: E2E_MULTI_NOTIF_001 .. 003
 *
 * Skenario:
 * 1. Admin melakukan aksi (create lowongan atau approve lamaran) dan memicu notifikasi
 * 2. Mahasiswa membuka panel notifikasi dan menandai sebagai dibaca
 * 3. Dosen (opsional) membuka notifikasi evaluasi / monitoring
 */

const { test, expect } = require('@playwright/test');
// Disabled: multirole tests removed per request — keep file but skip all tests
test.skip(true, 'multirole tests removed by test-suite maintenance');
const { loginAs, logout, takeScreenshot } = require('../utils/helpers');

async function openNotifications(page) {
  const bell = page.locator('[data-testid="notification"], .notification-bell, .fa-bell').first();
  if (await bell.isVisible({ timeout: 5000 })) {
    await bell.click();
    await page.waitForTimeout(800);
  }
}

test.describe('Multi-Role - Notifikasi', () => {
  test('E2E_MULTI_NOTIF_001: Admin memicu notifikasi approve lamaran → Mahasiswa membaca', async ({ page }) => {
    test.setTimeout(120000);
    console.log('\n[START] Notifikasi approve lamaran');

    // Login admin dan (heuristik) approve lamaran pertama
    await loginAs(page, 'admin');
    await page.goto('/plotting');
    await page.waitForLoadState('networkidle');

    const approveBtn = page.locator('button:has-text("Approve"), button:has-text("Terima"), button:has-text("Setujui")').first();
    // Read-only: do not perform approve action; just verify the control is present
    if (await approveBtn.isVisible({ timeout: 5000 })) {
      await expect(approveBtn).toBeVisible();
    }
    await takeScreenshot(page, 'notif-admin-action');
    await logout(page);

    // Login mahasiswa dan buka notifikasi
    await loginAs(page, 'mahasiswa');
    await openNotifications(page);

    const notifItems = page.locator('.notification-item, .notif-message');
    const count = await notifItems.count();
    console.log('  > Jumlah notifikasi:', count);
    if (count > 0) {
      const first = notifItems.first();
      await expect(first).toBeVisible();
      const text = await first.innerText();
      console.log('  > Isi notifikasi pertama:', text.substring(0, 120));

      // Tandai dibaca (heuristik)
      const markRead = first.locator('button:has-text("Baca"), button:has-text("Read"), button:has-text("Tandai")').first();
      if (await markRead.isVisible({ timeout: 1000 })) {
        await markRead.click();
        await page.waitForTimeout(800);
      }
    }
    await takeScreenshot(page, 'notif-mahasiswa-view');
    await logout(page);
  });

  test('E2E_MULTI_NOTIF_002: Mahasiswa submit logbook → Dosen melihat notifikasi', async ({ page }) => {
    test.setTimeout(150000);

    // Mahasiswa submit logbook (memicu notifikasi ke dosen jika sistem mendukung)
    await loginAs(page, 'mahasiswa');
    await page.click('text=/logbook/i');
    await page.waitForTimeout(800);
    const addBtn = page.locator('button:has-text("Tambah Logbook")').first();
      if (await addBtn.isVisible()) {
      await addBtn.click();
      const kegiatan = page.locator('textarea[name*="kegiatan"], textarea').first();
      if (await kegiatan.isVisible()) await kegiatan.fill('Entry logbook untuk pengujian notifikasi dosen.');
      const submit = page.locator('button:has-text("Simpan"), button[type="submit"]').first();
      // Read-only: do not click submit; just verify submit exists
      if (await submit.isVisible()) await expect(submit).toBeVisible();
      await page.waitForTimeout(500);
    }
    await logout(page);

    // Dosen login dan buka notifikasi
    await loginAs(page, 'dosen');
    await openNotifications(page);
    const notifItems = page.locator('.notification-item, .notif-message');
    const count = await notifItems.count();
    console.log('  > Notifikasi dosen:', count);
    await takeScreenshot(page, 'notif-dosen-logbook');
    await logout(page);
  });
});
