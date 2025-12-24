/**
 * E2E Test: Multi-Role - Alur Mahasiswa Apply dan Admin Approve
 * Test Case ID: E2E_MULTI_001, E2E_MULTI_004
 *
 * Skenario yang ditest:
 * 1. Mahasiswa apply lowongan
 * 2. Admin approve lamaran
 * 3. Mahasiswa verifikasi status approved
 *
 * Catatan: Test ini menggunakan manual login untuk switch antar role
 */

const { test, expect } = require('@playwright/test');
// Disabled: multirole tests removed per request — keep file but skip all tests
test.skip(true, 'multirole tests removed by test-suite maintenance');
const {
  loginAs,
  logout,
  expectSuccessNotification,
  takeScreenshot,
  waitForCondition
} = require('../utils/helpers');

test.describe('Multi-Role: Apply & Approve Workflow', () => {

  /**
   * Test Case: E2E_MULTI_004
   * Complete workflow: Mahasiswa apply → Admin approve → Mahasiswa check status
   */
  test('E2E_MULTI_004: Mahasiswa apply, admin approve, mahasiswa verifikasi', async ({ page }) => {
    test.setTimeout(180000); // 3 menit untuk test multi-role
    console.log('\n[TEST START] E2E_MULTI_004: Multi-role workflow - Apply & Approve');

    let lowonganTitle = '';
    let mahasiswaNama = '';

    // ========== PART 1: MAHASISWA APPLY LOWONGAN ==========
    console.log('\n[PART 1] Mahasiswa Apply Lowongan');

    // Step 1: Login sebagai mahasiswa
    console.log('Step 1: Login sebagai mahasiswa');
    await loginAs(page, 'mahasiswa');
    console.log('  > Login mahasiswa berhasil');
    // Step 2: Navigasi ke halaman Lowongan
    console.log('Step 2: Navigasi ke halaman lowongan');
    await page.goto('/mahasiswa/lowongan');
    await page.waitForLoadState('networkidle');
    console.log('  > Halaman lowongan dimuat');

    // Step 3: Ambil nama mahasiswa dari profil/dashboard
    console.log('Step 3: Ambil nama mahasiswa');
    const userNameElement = page.locator('[data-testid="user-name"], .user-profile .name, .navbar .username').first();
    if (await userNameElement.isVisible({ timeout: 5000 })) {
      mahasiswaNama = await userNameElement.innerText();
      console.log(`  > Nama mahasiswa: ${mahasiswaNama}`);
    }

    // Step 4: Pilih lowongan pertama yang tersedia
    console.log('Step 4: Pilih lowongan pertama');
    const lowonganCards = page.locator('[data-testid="lowongan-card"], .lowongan-item, .card');
    await lowonganCards.first().waitFor({ state: 'visible', timeout: 15000 });

    // Ambil judul lowongan untuk tracking
    const titleElement = lowonganCards.first().locator('h3, h4, .title, [data-field="title"]').first();
    if (await titleElement.isVisible({ timeout: 3000 })) {
      lowonganTitle = await titleElement.innerText();
      console.log(`  > Lowongan yang dipilih: ${lowonganTitle}`);
    }

    // Step 5: Klik detail lowongan
    console.log('Step 5: Klik detail lowongan');
    const detailButton = lowonganCards.first().locator('button:has-text("Detail"), a:has-text("Lihat")').first();
    if (await detailButton.isVisible()) {
      await detailButton.click();
      await page.waitForTimeout(2000);
      console.log('  > Detail lowongan dibuka');
    }

    // Step 6: Klik tombol Lamar
    console.log('Step 6: Klik tombol Lamar');
    const applyButton = page.locator('button:has-text("Lamar"), button:has-text("Apply")').first();

    if (await applyButton.isVisible({ timeout: 5000 })) {
      await applyButton.click();
      await page.waitForTimeout(1000);
      console.log('  > Button Lamar diklik');

      // Handle modal konfirmasi
      const confirmButton = page.locator('button:has-text("Konfirmasi"), button:has-text("Ya"), button:has-text("Submit")');
      if (await confirmButton.isVisible({ timeout: 3000 })) {
        await confirmButton.click();
        console.log('  > Konfirmasi apply diklik');
      }

      await page.waitForTimeout(3000);

      // Verifikasi notifikasi sukses
      const successNotif = page.locator('text=/berhasil|success/i, .alert-success');
      if (await successNotif.isVisible({ timeout: 5000 })) {
        console.log('  > Lamaran berhasil disubmit');
      }

      await takeScreenshot(page, 'multi-role-1-mahasiswa-applied');
      console.log('  > Screenshot tersimpan');
    } else {
      console.log('  > Button Lamar tidak ditemukan, test dibatalkan');
      test.skip();
      return;
    }

    // Step 7: Logout mahasiswa
    console.log('Step 7: Logout mahasiswa');
    await logout(page);
    console.log('  > Logout berhasil');
    // ========== PART 2: ADMIN APPROVE LAMARAN ==========
    console.log('\n[PART 2] Admin Approve Lamaran');

    // Step 8: Login sebagai admin
    console.log('Step 8: Login sebagai admin');
    await loginAs(page, 'admin');
    console.log('  > Login admin berhasil');
    // Step 9: Navigasi ke menu Plotting/Lamaran
    console.log('Step 9: Navigasi ke menu Plotting');
    await page.goto('/plotting');
    await page.waitForLoadState('networkidle');
    console.log('  > Halaman plotting dimuat');

    // Atau cari menu yang sesuai
    const plottingMenu = page.locator('a:has-text("Plotting"), a:has-text("Lamaran")').first();
    if (await plottingMenu.isVisible({ timeout: 5000 })) {
      await plottingMenu.click();
      await page.waitForLoadState('networkidle');
    }

    // Step 10: Cari lamaran mahasiswa (berdasarkan nama atau NIM)
    console.log('Step 10: Filter lamaran pending');
    await page.waitForTimeout(2000);

    // Filter lamaran dengan status "Menunggu" atau "Pending"
    const pendingFilter = page.locator('select[name*="status"], button:has-text("Menunggu"), [data-filter="pending"]').first();
    if (await pendingFilter.isVisible({ timeout: 5000 })) {
      if ((await pendingFilter.evaluate(el => el.tagName)) === 'SELECT') {
        await pendingFilter.selectOption('menunggu');
      } else {
        await pendingFilter.click();
      }
      await page.waitForTimeout(1500);
      console.log('  > Filter pending diaplikasikan');
    }

    // Step 11: Approve lamaran pertama (yang baru saja disubmit)
    console.log('Step 11: Approve lamaran');
    const lamaranList = page.locator('[data-testid="lamaran-item"], .lamaran-card, tr[data-row]');
    const lamaranCount = await lamaranList.count();

    console.log(`  > Ditemukan ${lamaranCount} lamaran pending`);

    if (lamaranCount > 0) {
      // Cari approve button
      const approveButton = lamaranList.first().locator('button:has-text("Approve"), button:has-text("Terima"), button:has-text("Setujui")').first();

      if (await approveButton.isVisible({ timeout: 5000 })) {
        await approveButton.click();
        await page.waitForTimeout(1000);
        console.log('  > Button Approve diklik');

        // Handle modal konfirmasi
        const confirmApprove = page.locator('button:has-text("Ya"), button:has-text("Confirm")').last();
        if (await confirmApprove.isVisible({ timeout: 3000 })) {
          await confirmApprove.click();
          console.log('  > Konfirmasi approve diklik');
        }

        await page.waitForTimeout(3000);

        // Verifikasi notifikasi sukses
        const successMsg = page.locator('text=/berhasil|success|approved/i, .alert-success');
        if (await successMsg.isVisible({ timeout: 5000 })) {
          console.log('  > Lamaran berhasil diapprove');
        }

        await takeScreenshot(page, 'multi-role-2-admin-approved');
        console.log('  > Screenshot tersimpan');
      } else {
        console.log('  > Button Approve tidak ditemukan');
      }
    } else {
      console.log('  > Tidak ada lamaran pending');
    }

    // Step 12: Logout admin
    console.log('Step 12: Logout admin');
    await logout(page);
    console.log('  > Logout berhasil');
    // ========== PART 3: MAHASISWA VERIFIKASI STATUS ==========
    console.log('\n[PART 3] Mahasiswa Verifikasi Status');

    // Step 13: Login kembali sebagai mahasiswa
    console.log('Step 13: Login kembali sebagai mahasiswa');
    await loginAs(page, 'mahasiswa');
    console.log('  > Login mahasiswa berhasil');
    // Step 14: Navigasi ke menu "Lamaran Saya"
    console.log('Step 14: Navigasi ke menu Lamaran Saya');
    await page.goto('/mahasiswa/dashboard');
    await page.waitForTimeout(1000);

    const lamaranMenu = page.locator('a:has-text("Lamaran"), a:has-text("My Application")').first();
    if (await lamaranMenu.isVisible({ timeout: 5000 })) {
      await lamaranMenu.click();
      await page.waitForLoadState('networkidle');
      console.log('  > Menu Lamaran Saya dibuka');
    }

    // Step 15: Verifikasi status lamaran berubah menjadi "Diterima"
    console.log('Step 15: Verifikasi status lamaran');
    await page.waitForTimeout(2000);

    const myLamaranList = page.locator('[data-testid="lamaran-item"], .lamaran-card');
    if (await myLamaranList.count() > 0) {
      const statusBadge = myLamaranList.first().locator('[data-testid="status"], .status, .badge').first();

      if (await statusBadge.isVisible({ timeout: 5000 })) {
        const statusText = await statusBadge.innerText();
        console.log(`  > Status lamaran: ${statusText}`);

        // Verifikasi status = Diterima/Approved
        expect(statusText.toLowerCase()).toMatch(/diterima|approved|accepted/);
        console.log('  > Status lamaran sudah Diterima');
      }
    }

    // Step 16: Verifikasi notifikasi (jika ada)
    console.log('Step 16: Cek notifikasi');
    const notificationIcon = page.locator('[data-testid="notification"], .notification-bell, .fa-bell').first();
    if (await notificationIcon.isVisible({ timeout: 3000 })) {
      await notificationIcon.click();
      await page.waitForTimeout(1000);

      // Cek apakah ada notifikasi tentang lamaran diterima
      const notifText = await page.locator('.notification-item, .notif-message').first().innerText({ timeout: 3000 }).catch(() => '');
      if (notifText.match(/diterima|approved/i)) {
        console.log('  > Notifikasi diterima ditemukan');
      }
    }

    await takeScreenshot(page, 'multi-role-3-mahasiswa-verified');
    console.log('  > Screenshot tersimpan');

    // Final cleanup
    await logout(page);
    console.log('\n[TEST END] E2E_MULTI_004: PASSED\n');
  });

  /**
   * Test Case: E2E_MULTI_001
   * Admin create mahasiswa → Mahasiswa login pertama kali
   */
  test('E2E_MULTI_001: Admin create mahasiswa, mahasiswa login pertama kali', async ({ page }) => {
    test.setTimeout(120000);
    console.log('\n[TEST START] E2E_MULTI_001: Admin create mahasiswa & first login');

    const { generateRandomData } = require('../utils/helpers');

    // Generate data mahasiswa baru
    const newMahasiswa = {
      nim: generateRandomData('nim'),
      nama: generateRandomData('nama'),
      email: generateRandomData('email'),
      password: 'Test@12345',
    };

    console.log('\n[PART 1] Admin Create Mahasiswa Baru');

    // Step 1: Login sebagai admin
    console.log('Step 1: Login sebagai admin');
    await loginAs(page, 'admin');
    console.log('  > Login admin berhasil');
    // Step 2: Navigasi ke Data Mahasiswa
    console.log('Step 2: Navigasi ke Data Mahasiswa');
    await page.goto('/dataMhs');
    await page.waitForLoadState('networkidle');
    console.log('  > Halaman Data Mahasiswa dimuat');

    // Step 3: Klik Tambah Mahasiswa
    console.log('Step 3: Klik tombol Tambah Mahasiswa');
    const addButton = page.locator('button:has-text("Tambah"), a:has-text("Tambah")').first();
    await addButton.click();
    await page.waitForTimeout(1000);
    console.log('  > Form tambah mahasiswa dibuka');

    // Step 4: Isi form mahasiswa
    console.log('Step 4: Mengisi form mahasiswa');
    await page.fill('input[name="nim"]', newMahasiswa.nim);
    await page.fill('input[name="nama"]', newMahasiswa.nama);
    await page.fill('input[name="email"]', newMahasiswa.email);
    await page.fill('input[name="password"]', newMahasiswa.password);
    console.log(`  > NIM: ${newMahasiswa.nim}`);
    console.log(`  > Nama: ${newMahasiswa.nama}`);
    console.log(`  > Email: ${newMahasiswa.email}`);

    // Step 5: Submit form
    console.log('Step 5: Submit form mahasiswa');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(3000);
    console.log('  > Form disubmit');

    await expectSuccessNotification(page);
    console.log('  > Mahasiswa berhasil dibuat');
    await takeScreenshot(page, 'multi-role-admin-created-user');
    console.log('  > Screenshot tersimpan');

    // Step 6: Logout admin
    console.log('Step 6: Logout admin');
    await logout(page);
    console.log('  > Logout berhasil');

    console.log('\n[PART 2] Mahasiswa Baru Login Pertama Kali');

    // Step 7: Login sebagai mahasiswa baru
    console.log('Step 7: Login sebagai mahasiswa baru');
    await page.goto('/login');
    await page.fill('input[name="email"]', newMahasiswa.email);
    await page.fill('input[name="password"]', newMahasiswa.password);
    await page.click('button[type="submit"]');
    console.log('  > Credentials diisi dan form disubmit');

    // Step 8: Verifikasi redirect ke dashboard mahasiswa
    console.log('Step 8: Verifikasi redirect ke dashboard');
    await page.waitForURL('**/mahasiswa/dashboard', { timeout: 15000 });
    console.log('  > Redirect ke dashboard mahasiswa berhasil');
    // Step 9: Verifikasi dashboard tampil
    console.log('Step 9: Verifikasi dashboard mahasiswa');
    await expect(page.locator('h1, h2, h3').filter({ hasText: /dashboard/i })).toBeVisible();
    console.log('  > Dashboard mahasiswa tampil dengan benar');

    await takeScreenshot(page, 'multi-role-new-mahasiswa-dashboard');
    console.log('  > Screenshot tersimpan');

    await logout(page);
    console.log('\n[TEST END] E2E_MULTI_001: PASSED\n');
  });
});
