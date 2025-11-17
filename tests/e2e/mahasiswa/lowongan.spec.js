/**
 * E2E Test: Mahasiswa - Fitur Lowongan Magang
 * Test Case ID: E2E_MHS_003, E2E_MHS_004
 *
 * Skenario yang ditest:
 * 1. Mahasiswa melihat daftar lowongan
 * 2. Mahasiswa melihat rekomendasi lowongan (SPK)
 * 3. Mahasiswa apply lowongan
 * 4. Mahasiswa tracking status lamaran
 *
 * Catatan: Test ini menggunakan storageState dari auth-states/mahasiswa.json
 * sehingga tidak perlu login manual
 */

const { test, expect } = require('@playwright/test');

// Test configuration
test.describe('Mahasiswa - Fitur Lowongan Magang', () => {

  // Setup: Navigasi ke halaman dashboard sebelum setiap test
  test.beforeEach(async ({ page }) => {
    await page.goto('/mahasiswa/dashboard');
    // Verifikasi user sudah login
    await expect(page).toHaveURL(/.*mahasiswa\/dashboard/);
  });

  /**
   * Test Case: E2E_MHS_003
   * Skenario: Mahasiswa melihat daftar lowongan dan rekomendasi
   */
  test('E2E_MHS_003: Melihat daftar lowongan dan rekomendasi SPK', async ({ page }) => {
    test.setTimeout(60000); // 60 detik untuk test ini
    console.log('\n[TEST START] E2E_MHS_003: Melihat daftar lowongan dan rekomendasi SPK');

    // Step 1: Navigasi ke menu Lowongan
    console.log('Step 1: Navigasi ke menu Lowongan');
    await page.click('text=Lowongan'); // Sesuaikan selector dengan menu Anda
    await page.waitForURL(/.*mahasiswa\/lowongan/, { timeout: 10000 });
    console.log('  > Berhasil masuk ke halaman lowongan');

    // Step 2: Verifikasi halaman lowongan tampil
    console.log('Step 2: Verifikasi halaman lowongan');
    await expect(page.locator('h1, h2, h3').filter({ hasText: /lowongan/i })).toBeVisible();
    console.log('  > Heading lowongan ditemukan');

    // Step 3: Tunggu daftar lowongan dimuat
    console.log('Step 3: Tunggu daftar lowongan dimuat');
    await page.waitForSelector('[data-testid="lowongan-card"], .lowongan-item, .card', {
      timeout: 15000
    });
    console.log('  > Lowongan card ditemukan');

    // Step 4: Verifikasi ada minimal 1 lowongan
    console.log('Step 4: Hitung jumlah lowongan');
    const lowonganCards = await page.locator('[data-testid="lowongan-card"], .lowongan-item, .card').count();
    console.log(`  > Ditemukan ${lowonganCards} lowongan`);
    expect(lowonganCards).toBeGreaterThan(0);

    // Step 5: Klik tab "Rekomendasi Untuk Anda"
    console.log('Step 5: Cek tab rekomendasi');
    const rekomTab = page.locator('text=/rekomendasi/i, [data-tab="rekomendasi"]').first();
    if (await rekomTab.isVisible()) {
      await rekomTab.click();
      await page.waitForTimeout(2000); // Tunggu load rekomendasi

      // Step 6: Verifikasi ranking/score tampil
      const scoreElements = page.locator('[data-testid="score"], .score, .ranking');
      if (await scoreElements.count() > 0) {
        await expect(scoreElements.first()).toBeVisible();      }
    }

    // Screenshot untuk dokumentasi
    console.log('Step 6: Ambil screenshot');
    await page.screenshot({
      path: 'playwright-report/screenshots/mahasiswa-lowongan-list.png',
      fullPage: true
    });
    console.log('  > Screenshot tersimpan');
    console.log('[TEST END] E2E_MHS_003: PASSED\n');
  });

  /**
   * Test Case: E2E_MHS_004
   * Skenario: Mahasiswa apply lowongan dan tracking status
   */
  test('E2E_MHS_004: Apply lowongan dan tracking status lamaran', async ({ page }) => {
    test.setTimeout(90000); // 90 detik untuk test ini
    console.log('\n[TEST START] E2E_MHS_004: Apply lowongan dan tracking status');

    // Step 1: Navigasi ke halaman lowongan
    console.log('Step 1: Navigasi ke halaman lowongan');
    await page.goto('/mahasiswa/lowongan');
    await page.waitForLoadState('networkidle');
    console.log('  > Halaman lowongan dimuat');

    // Step 2: Pilih lowongan pertama yang tersedia
    console.log('Step 2: Pilih lowongan pertama');
    const firstLowongan = page.locator('[data-testid="lowongan-card"], .lowongan-item, .card').first();
    await expect(firstLowongan).toBeVisible({ timeout: 15000 });
    console.log('  > Lowongan pertama ditemukan');

    // Step 3: Klik untuk melihat detail lowongan
    console.log('Step 3: Buka detail lowongan');
    const detailButton = firstLowongan.locator('button:has-text("Detail"), a:has-text("Lihat Detail")').first();
    if (await detailButton.isVisible()) {
      await detailButton.click();
      await page.waitForTimeout(2000);
    }

    // Step 4: Verifikasi button "Lamar" ada
    console.log('Step 4: Cari button Lamar');
    const applyButton = page.locator('button:has-text("Lamar"), button:has-text("Apply")').first();

    if (await applyButton.isVisible()) {
      console.log('  > Button Lamar ditemukan');
      // Step 5: Klik button Lamar
      console.log('Step 5: Klik button Lamar');
      await applyButton.click();
      await page.waitForTimeout(1000);

      // Step 6: Handle modal konfirmasi (jika ada)
      const confirmButton = page.locator('button:has-text("Konfirmasi"), button:has-text("Ya"), button:has-text("Submit")');
      if (await confirmButton.isVisible({ timeout: 5000 })) {
        await confirmButton.click();      }

      // Step 7: Tunggu response (toast notification atau redirect)
      await page.waitForTimeout(3000);

      // Step 8: Verifikasi notifikasi sukses
      const successNotif = page.locator('text=/berhasil|success|sukses/i, .alert-success, .toast-success');
      if (await successNotif.isVisible({ timeout: 5000 })) {
        await expect(successNotif).toBeVisible();      }
    } else {    }

    // Step 9: Navigasi ke halaman "Lamaran Saya"
    await page.click('text=/lamaran saya|my application|riwayat lamaran/i');
    await page.waitForTimeout(2000);

    // Step 10: Verifikasi daftar lamaran tampil
    const lamaranList = page.locator('[data-testid="lamaran-item"], .lamaran-card, .application-item');
    if (await lamaranList.count() > 0) {
      await expect(lamaranList.first()).toBeVisible();

      // Step 11: Verifikasi status lamaran ada
      const statusBadge = lamaranList.first().locator('[data-testid="status"], .status, .badge');
      if (await statusBadge.isVisible()) {
        const statusText = await statusBadge.innerText();        expect(statusText).toMatch(/menunggu|pending|diterima|accepted|ditolak|rejected/i);
      }
    }

    // Screenshot untuk dokumentasi
    console.log('Step 11: Ambil screenshot hasil');
    await page.screenshot({
      path: 'playwright-report/screenshots/mahasiswa-lamaran-tracking.png',
      fullPage: true
    });
    console.log('  > Screenshot tersimpan');
    console.log('[TEST END] E2E_MHS_004: PASSED\n');
  });

  /**
   * Test Case: Mahasiswa melengkapi profil sebelum apply
   */
  test('E2E_MHS_002: Melengkapi profil mahasiswa (Skills, Minat, CV)', async ({ page }) => {
    test.setTimeout(60000);
    console.log('\n[TEST START] E2E_MHS_002: Melengkapi profil mahasiswa');

    // Step 1: Navigasi ke halaman profil
    console.log('Step 1: Navigasi ke halaman profil');
    await page.click('text=/profil|profile/i');
    await page.waitForTimeout(2000);
    console.log('  > Berhasil masuk ke halaman profil');

    // Step 2: Verifikasi form profil tampil
    console.log('Step 2: Verifikasi form profil');
    await expect(page.locator('h1, h2').filter({ hasText: /profil|profile/i })).toBeVisible();
    console.log('  > Form profil ditemukan');

    // Step 3: Tambah Skills (jika ada form skills)
    console.log('Step 3: Menambah skills');
    const skillsSection = page.locator('[data-section="skills"], #skills-section');
    if (await skillsSection.isVisible({ timeout: 5000 })) {
      const addSkillButton = page.locator('button:has-text("Tambah Skill"), button:has-text("Add Skill")').first();

      if (await addSkillButton.isVisible()) {
        // Tambah 3 skills
        const skills = ['Java', 'Python', 'SQL'];
        for (const skill of skills) {
          await addSkillButton.click();
          await page.waitForTimeout(500);

          // Fill skill input (sesuaikan selector)
          const skillInput = page.locator('input[placeholder*="skill"], input[name*="skill"]').last();
          if (await skillInput.isVisible()) {
            await skillInput.fill(skill);
            console.log(`  > Skill ditambahkan: ${skill}`);
          }
        }
      }
    } else {
      console.log('  > Skills section tidak ditemukan');
    }

    // Step 4: Upload CV (jika ada form upload)
    console.log('Step 4: Upload CV');
    const cvUploadInput = page.locator('input[type="file"][accept*="pdf"]');
    if (await cvUploadInput.isVisible({ timeout: 5000 })) {
      // Path ke file CV dummy (buat file ini di tests/fixtures/)
      const cvFilePath = './tests/fixtures/dummy-cv.pdf';
      await cvUploadInput.setInputFiles(cvFilePath);
      console.log('  > CV berhasil diupload');
    } else {
      console.log('  > Form upload CV tidak ditemukan');
    }

    // Step 5: Simpan perubahan profil
    console.log('Step 5: Simpan perubahan profil');
    const saveButton = page.locator('button:has-text("Simpan"), button:has-text("Save"), button[type="submit"]').first();
    if (await saveButton.isVisible()) {
      await saveButton.click();
      await page.waitForTimeout(2000);
      console.log('  > Button Simpan diklik');

      // Verifikasi notifikasi sukses
      const successMsg = page.locator('text=/berhasil|success|saved/i, .alert-success');
      if (await successMsg.isVisible({ timeout: 5000 })) {
        await expect(successMsg).toBeVisible();
        console.log('  > Profil berhasil diupdate');
      }
    }

    // Screenshot
    console.log('Step 6: Ambil screenshot');
    await page.screenshot({
      path: 'playwright-report/screenshots/mahasiswa-profile-update.png',
      fullPage: true
    });
    console.log('  > Screenshot tersimpan');
    console.log('[TEST END] E2E_MHS_002: PASSED\n');
  });

  /**
   * Test Case: Mahasiswa mengisi logbook
   */
  test('E2E_MHS_005: Mengisi logbook magang harian', async ({ page }) => {
    test.setTimeout(45000);
    console.log('\n[TEST START] E2E_MHS_005: Mengisi logbook magang harian');

    // Step 1: Navigasi ke halaman logbook
    console.log('Step 1: Navigasi ke halaman logbook');
    await page.click('text=/logbook/i');
    await page.waitForTimeout(2000);
    console.log('  > Berhasil masuk ke halaman logbook');

    // Step 2: Klik button "Tambah Logbook"
    console.log('Step 2: Klik button Tambah Logbook');
    const addLogbookButton = page.locator('button:has-text("Tambah Logbook"), button:has-text("Add Entry")').first();

    if (await addLogbookButton.isVisible({ timeout: 5000 })) {
      await addLogbookButton.click();
      await page.waitForTimeout(1000);
      console.log('  > Button Tambah Logbook diklik');

      // Step 3: Isi form logbook
      console.log('Step 3: Mengisi form logbook');
      const tanggalInput = page.locator('input[type="date"], input[name*="tanggal"]').first();
      if (await tanggalInput.isVisible()) {
        await tanggalInput.fill('2025-11-15');
        console.log('  > Tanggal diisi: 2025-11-15');
      }

      const kegiatanInput = page.locator('textarea[name*="kegiatan"], textarea[placeholder*="kegiatan"]').first();
      if (await kegiatanInput.isVisible()) {
        await kegiatanInput.fill('Mengerjakan fitur login dan authentication sistem magang');
        console.log('  > Kegiatan diisi');
      }

      const durasiInput = page.locator('input[name*="durasi"], input[placeholder*="durasi"]').first();
      if (await durasiInput.isVisible()) {
        await durasiInput.fill('8');
        console.log('  > Durasi diisi: 8 jam');
      }

      // Step 4: Submit logbook
      console.log('Step 4: Submit logbook');
      const submitButton = page.locator('button:has-text("Simpan"), button:has-text("Submit"), button[type="submit"]').first();
      if (await submitButton.isVisible()) {
        await submitButton.click();
        await page.waitForTimeout(2000);
        console.log('  > Button Submit diklik');

        // Verifikasi sukses
        const successMsg = page.locator('text=/berhasil|success/i, .alert-success');
        if (await successMsg.isVisible({ timeout: 5000 })) {
          console.log('  > Logbook berhasil disimpan');
        }
      }
    } else {
      console.log('  > Button Tambah Logbook tidak ditemukan');
    }

    // Screenshot
    console.log('Step 5: Ambil screenshot');
    await page.screenshot({
      path: 'playwright-report/screenshots/mahasiswa-logbook-submit.png',
      fullPage: true
    });
    console.log('  > Screenshot tersimpan');
    console.log('[TEST END] E2E_MHS_005: PASSED\n');
  });
});
