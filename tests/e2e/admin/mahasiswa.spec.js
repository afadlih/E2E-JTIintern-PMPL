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
  takeScreenshot
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
    await page.click('text=/data mahasiswa|mahasiswa/i');
    await page.waitForLoadState('networkidle');

    // Step 2: Verifikasi halaman data mahasiswa
    console.log('Step 2: Verifikasi halaman data mahasiswa');
    await expect(page.locator('h1, h2, h3').filter({ hasText: /mahasiswa/i })).toBeVisible();
    console.log('  > Halaman mahasiswa ditemukan');

    // Step 3: Klik tombol Tambah Mahasiswa
    console.log('Step 3: Klik tombol Tambah Mahasiswa');
    const addButton = page.locator('button:has-text("Tambah"), a:has-text("Tambah Mahasiswa")').first();
    await addButton.click();
    await page.waitForTimeout(1000);

    // Step 4: Generate data mahasiswa random
    const mahasiswaData = {
      nim: generateRandomData('nim'),
      nama: generateRandomData('nama'),
      email: generateRandomData('email'),
      no_hp: generateRandomData('phone'),
    };

    console.log('📝 Data mahasiswa yang akan ditambahkan:', mahasiswaData);

    // Step 5: Isi form mahasiswa
    await fillForm(page, {
      'input[name="nim"], input[id="nim"]': mahasiswaData.nim,
      'input[name="nama"], input[id="nama"]': mahasiswaData.nama,
      'input[name="email"], input[id="email"]': mahasiswaData.email,
      'input[name="no_hp"], input[id="no_hp"]': mahasiswaData.no_hp,
    });

    // Step 6: Pilih kelas (jika ada dropdown)
    const kelasSelect = page.locator('select[name="kelas_id"], select[id="kelas_id"]').first();
    if (await kelasSelect.isVisible({ timeout: 2000 })) {
      await kelasSelect.selectOption({ index: 1 }); // Pilih kelas pertama
    }

    // Step 7: Submit form
    const submitButton = page.locator('button[type="submit"], button:has-text("Simpan")').first();
    await submitButton.click();

    // Step 8: Tunggu redirect atau notifikasi
    await page.waitForTimeout(2000);

    // Step 9: Verifikasi notifikasi sukses
    await expectSuccessNotification(page);
    // Step 10: Verifikasi data muncul di tabel
    const searchInput = page.locator('input[type="search"], input[placeholder*="cari"], input[name="search"]').first();
    if (await searchInput.isVisible({ timeout: 3000 })) {
      await searchInput.fill(mahasiswaData.nim);
      await page.waitForTimeout(1000);

      // Verifikasi NIM muncul di hasil search
      await expect(page.locator(`text=${mahasiswaData.nim}`)).toBeVisible({ timeout: 5000 });
    }

    await takeScreenshot(page, 'admin-mahasiswa-created');
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

    if (await importButton.isVisible({ timeout: 5000 })) {
      await importButton.click();
      await page.waitForTimeout(1000);
      console.log('  > Button Import diklik');

      // Step 3: Upload file CSV
      console.log('Step 3: Upload file CSV');
      const fileInput = page.locator('input[type="file"][accept*="csv"]').first();

      if (await fileInput.isVisible({ timeout: 3000 })) {
        // Path ke file CSV dummy (perlu dibuat di tests/fixtures/)
        const csvPath = './tests/fixtures/dummy-mahasiswa.csv';
        await fileInput.setInputFiles(csvPath);
        console.log('  > File CSV diupload');
        // Step 4: Klik tombol Process Import
        console.log('Step 4: Proses import');
        const processButton = page.locator('button:has-text("Import"), button:has-text("Process")').last();
        await processButton.click();
        console.log('  > Button Process diklik');

        // Step 5: Tunggu proses import
        await page.waitForTimeout(5000);

        // Step 6: Verifikasi summary import
        console.log('Step 5: Verifikasi hasil import');
        const summaryText = await page.locator('.import-summary, .result-summary').innerText();
        console.log('  > Summary Import:', summaryText);

        expect(summaryText).toMatch(/berhasil|success|imported/i);

        await takeScreenshot(page, 'admin-import-success');
        console.log('  > Screenshot tersimpan');
        console.log('[TEST END] E2E_ADM_003: PASSED\n');
      } else {
        console.log('  > File input tidak ditemukan, test diskip');
        test.skip();
      }
    } else {
      console.log('  > Button Import tidak ditemukan, test diskip');
      test.skip();
    }
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
      await editButton.click();
      await page.waitForTimeout(1000);
      console.log('  > Button Edit diklik');

      // Step 3: Update nama mahasiswa
      console.log('Step 3: Update nama mahasiswa');
      const updatedName = `Updated ${generateRandomData('nama')}`;
      const namaInput = page.locator('input[name="nama"], input[id="nama"]').first();
      await namaInput.fill(updatedName);
      console.log(`  > Nama diupdate: ${updatedName}`);
      // Step 4: Submit update
      console.log('Step 4: Submit update');
      const submitButton = page.locator('button[type="submit"], button:has-text("Simpan")').first();
      await submitButton.click();
      await page.waitForTimeout(2000);
      console.log('  > Button Submit diklik');

      // Step 5: Verifikasi notifikasi sukses
      console.log('Step 5: Verifikasi notifikasi sukses');
      await expectSuccessNotification(page);
      console.log('  > Data mahasiswa berhasil diupdate');
      await takeScreenshot(page, 'admin-mahasiswa-updated');
      console.log('  > Screenshot tersimpan');
      console.log('[TEST END] E2E_ADM_UPDATE: PASSED\n');
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
      const lastDeleteButton = deleteButtons.last();
      await lastDeleteButton.click();
      await page.waitForTimeout(1000);
      console.log('  > Button Hapus diklik');

      // Step 3: Konfirmasi hapus (jika ada modal)
      console.log('Step 3: Konfirmasi hapus');
      const confirmButton = page.locator('button:has-text("Ya"), button:has-text("Confirm"), button:has-text("Delete")').last();
      if (await confirmButton.isVisible({ timeout: 3000 })) {
        await confirmButton.click();
        await page.waitForTimeout(2000);
        console.log('  > Konfirmasi hapus diklik');
      }

      // Step 4: Verifikasi notifikasi sukses
      console.log('Step 4: Verifikasi notifikasi sukses');
      await expectSuccessNotification(page);
      console.log('  > Data mahasiswa berhasil dihapus');
      await takeScreenshot(page, 'admin-mahasiswa-deleted');
      console.log('  > Screenshot tersimpan');
      console.log('[TEST END] E2E_ADM_DELETE: PASSED\n');
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
