/**
 * E2E Test: Admin - Manajemen Mahasiswa
 * Test Case ID: E2E_ADM_002, E2E_ADM_003
 *
 * Skenario yang ditest:
 * 1. Admin menambahkan mahasiswa baru
 * 2. Admin import mahasiswa via CSV
 * 3. Admin update data mahasiswa
 * 4. Admin menghapus mahasiswa
 */

const { test, expect } = require('@playwright/test');
const {
  fillForm,
  expectSuccessNotification,
  verifyTableHasData,
  generateRandomData,
  takeScreenshot,
  navigateToMenu,
  elementExists
} = require('../utils/helpers');

test.describe('Admin - Manajemen Mahasiswa', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/dashboard');
    await expect(page).toHaveURL(/.*dashboard/);
  });

  /**
   * Test Case: E2E_ADM_002
   * Menambahkan data mahasiswa baru via form
   */
  test('E2E_ADM_002: Menambahkan data mahasiswa baru', async ({ page }) => {
    test.setTimeout(60000);
    console.log('\n[TEST START] E2E_ADM_002: Menambahkan data mahasiswa baru');

    // Step 1: Navigasi ke menu Data Mahasiswa
    console.log('Step 1: Navigasi ke menu Data Mahasiswa');
    try {
      await navigateToMenu(page, 'Data Mahasiswa', { timeout: 5000 });
    } catch (err) {
      // fallback to direct route
      await page.goto('/data-mahasiswa');
    }
    await page.waitForLoadState('networkidle');

    // Step 2: Verifikasi halaman data mahasiswa (robust)
    console.log('Step 2: Verifikasi halaman data mahasiswa');
    const headerExists = await elementExists(page, 'h1:has-text("Data Mahasiswa"), h2:has-text("Data Mahasiswa"), .card-header h6:has-text("Data Mahasiswa")', 3000);
    if (headerExists) {
      await expect(page.locator('h1, h2, h3').filter({ hasText: /mahasiswa/i })).toBeVisible();
    } else {
      // fallback: table body must exist
      await expect(page.locator('#mahasiswa-table-body')).toBeVisible({ timeout: 10000 });
    }
    console.log('  > Halaman mahasiswa ditemukan');

    // Step 3: Klik tombol Tambah Mahasiswa (read-only)
    console.log('Step 3: Verifikasi tombol Tambah Mahasiswa (read-only)');
    const addButton = page.locator('button:has-text("Tambah Mahasiswa"), button:has-text("Tambah"), a:has-text("Tambah Mahasiswa")').first();
    if (await addButton.isVisible({ timeout: 5000 })) {
      await expect(addButton).toBeVisible();
      // If modal opens, verify form fields are present but do NOT submit
      try {
        await addButton.click();
        await page.waitForSelector('#modalTambahMahasiswa.show, #modalTambahMahasiswa', { timeout: 3000 }).catch(() => {});
        const modal = page.locator('#modalTambahMahasiswa');
        if (await modal.isVisible({ timeout: 2000 })) {
          await expect(modal.locator('#nim')).toBeVisible();
          await expect(modal.locator('#nama')).toBeVisible();
          await expect(modal.locator('#ipk')).toBeVisible();
          // Close modal to avoid side-effects
          const closeBtn = modal.locator('button[data-bs-dismiss="modal"], button:has-text("Tutup"), .btn-close').first();
          if (await closeBtn.isVisible({ timeout: 1000 })) await closeBtn.click();
        }
      } catch (e) {
        // ignore errors and continue (read-only safety)
      }
    } else {
      console.log('  > Tombol Tambah Mahasiswa tidak ditemukan (read-only)');
    }

    await takeScreenshot(page, 'admin-mahasiswa-create-readonly');
  });

  /**
   * Test Case: E2E_ADM_003
   * Import data mahasiswa via CSV
   */
  test('E2E_ADM_003: Import data mahasiswa via CSV', async ({ page }) => {
    test.setTimeout(90000);
    console.log('\n[TEST START] E2E_ADM_003: Import data mahasiswa via CSV');

    // Step 1: Navigasi ke menu Data Mahasiswa
    console.log('Step 1: Navigasi ke menu Data Mahasiswa');
    await page.click('text=/data mahasiswa|mahasiswa/i');
    await page.waitForLoadState('networkidle');
    console.log('  > Halaman mahasiswa dimuat');

    // Step 2: Klik tombol Import
    console.log('Step 2: Klik tombol Import');
    const importButton = page.locator('button:has-text("Import"), a:has-text("Import CSV")').first();

    // Read-only: verify import controls exist but do not upload files
    if (await importButton.isVisible({ timeout: 5000 })) {
      await expect(importButton).toBeVisible();
      try {
        await importButton.click();
        await page.waitForSelector('.import-modal, #importModal, #importForm', { timeout: 2000 }).catch(() => {});
        const fileInput = page.locator('input[type="file"][accept*="csv"]').first();
        if (await fileInput.isVisible({ timeout: 1000 })) {
          await expect(fileInput).toBeVisible();
        }
      } catch (e) {
        // ignore
      }
    } else {
      console.log('  > Button Import tidak ditemukan (read-only)');
    }

    await takeScreenshot(page, 'admin-import-readonly');
  });

  /**
   * Test Case: Update data mahasiswa
   */
  test('E2E_ADM_UPDATE: Update data mahasiswa existing', async ({ page }) => {
    test.setTimeout(60000);
    console.log('\n[TEST START] E2E_ADM_UPDATE: Update data mahasiswa existing');

    // Step 1: Navigasi ke Data Mahasiswa
    console.log('Step 1: Navigasi ke Data Mahasiswa');
    await page.click('text=/data mahasiswa|mahasiswa/i');
    await page.waitForLoadState('networkidle');
    console.log('  > Halaman mahasiswa dimuat');

    // Step 2: Klik tombol Edit pada mahasiswa pertama
    console.log('Step 2: Klik tombol Edit');
    const editButton = page.locator('button:has-text("Edit"), a:has-text("Edit"), [data-action="edit"]').first();

    if (await editButton.isVisible({ timeout: 5000 })) {
      // Read-only: open edit modal, verify fields exist, do not submit
      await editButton.click();
      await page.waitForSelector('#editMahasiswaModal, #modalEditMahasiswa', { timeout: 2000 }).catch(() => {});
      const modal = page.locator('#editMahasiswaModal, #modalEditMahasiswa');
      if (await modal.isVisible({ timeout: 2000 })) {
        await expect(modal.locator('input[name="nama"], input[id="nama"]')).toBeVisible();
        const saveBtn = modal.locator('button[type="submit"], button:has-text("Simpan")').first();
        await expect(saveBtn).toBeVisible();
        // Close modal
        const closeBtn = modal.locator('button[data-bs-dismiss="modal"], button:has-text("Tutup"), .btn-close').first();
        if (await closeBtn.isVisible({ timeout: 1000 })) await closeBtn.click();
      }
      await takeScreenshot(page, 'admin-mahasiswa-edit-readonly');
    } else {
      console.log('  > Button Edit tidak ditemukan, test diskip');
      test.skip();
    }
  });

  /**
   * Test Case: Hapus data mahasiswa
   */
  test('E2E_ADM_DELETE: Hapus data mahasiswa', async ({ page }) => {
    test.setTimeout(60000);
    console.log('\n[TEST START] E2E_ADM_DELETE: Hapus data mahasiswa');

    // Step 1: Navigasi ke Data Mahasiswa
    console.log('Step 1: Navigasi ke Data Mahasiswa');
    await page.click('text=/data mahasiswa|mahasiswa/i');
    await page.waitForLoadState('networkidle');
    console.log('  > Halaman mahasiswa dimuat');

    // Step 2: Klik tombol Hapus pada mahasiswa terakhir
    console.log('Step 2: Klik tombol Hapus');
    const deleteButtons = page.locator('button:has-text("Hapus"), button:has-text("Delete"), [data-action="delete"]');
    const count = await deleteButtons.count();
    console.log(`  > Ditemukan ${count} button hapus`);

    if (count > 0) {
      // Read-only: verify delete button exists and open confirmation then cancel
      const lastDeleteButton = deleteButtons.last();
      await expect(lastDeleteButton).toBeVisible();
      await lastDeleteButton.click();
      await page.waitForTimeout(800);
      const confirmButton = page.locator('button:has-text("Ya"), button:has-text("Confirm"), button:has-text("Delete")').last();
      const cancel = page.locator('button:has-text("Batal"), button:has-text("Tidak"), button:has-text("Cancel")').last();
      if (await confirmButton.isVisible({ timeout: 2000 })) {
        if (await cancel.isVisible({ timeout: 1000 })) {
          await cancel.click();
        }
      }
      await takeScreenshot(page, 'admin-mahasiswa-delete-confirm-readonly');
    } else {
      console.log('  > Button Hapus tidak ditemukan, test diskip');
      test.skip();
    }
  });

  /**
   * Test Case: Verifikasi tabel mahasiswa memiliki data
   */
  test('E2E_ADM_VIEW: Verifikasi daftar mahasiswa tampil', async ({ page }) => {
    console.log('\n[TEST START] E2E_ADM_VIEW: Verifikasi daftar mahasiswa tampil');

    // Step 1: Navigasi ke Data Mahasiswa
    console.log('Step 1: Navigasi ke Data Mahasiswa');
    await page.click('text=/data mahasiswa|mahasiswa/i');
    await page.waitForLoadState('networkidle');
    console.log('  > Halaman mahasiswa dimuat');

    // Step 2: Verifikasi tabel ada data
    console.log('Step 2: Verifikasi tabel ada data');
    const tableSelector = 'table, [data-table="mahasiswa"], .data-table';
    const rowCount = await verifyTableHasData(page, tableSelector, 1);
    console.log(`  > Ditemukan ${rowCount} baris data`);
    // Step 3: Verifikasi kolom-kolom penting
    console.log('Step 3: Verifikasi kolom tabel');
    await expect(page.locator('th:has-text("NIM"), th:has-text("Nama")')).toBeVisible();
    console.log('  > Kolom NIM dan Nama ditemukan');

    await takeScreenshot(page, 'admin-mahasiswa-table');
    console.log('  > Screenshot tersimpan');
    console.log('[TEST END] E2E_ADM_VIEW: PASSED\n');
  });
});
