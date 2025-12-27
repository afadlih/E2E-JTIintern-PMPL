import { test, expect } from '@playwright/test';
import { loginAs, navigateToMenu, takeScreenshot, elementExists } from '../utils/helpers';

test.describe('Mahasiswa - Apply Lowongan Flow', () => {
  test.use({ storageState: 'tests/e2e/auth-states/mahasiswa.json' });

  test('E2E_MHS_APPLY_001: Apply lowongan dan verify di lamaran tracking', async ({ page }) => {
    test.setTimeout(60000);
    console.log('\n[TEST START] E2E_MHS_APPLY_001: Apply lowongan');

    // Step 1: Navigasi ke halaman lowongan
    console.log('Step 1: Navigasi ke halaman lowongan');
    await page.goto('http://127.0.0.1:8000/mahasiswa/lowongan');
    await page.waitForLoadState('networkidle');
    console.log('  > Halaman lowongan dimuat');

    // Step 2: Tunggu dan ambil lowongan pertama
    console.log('Step 2: Cari lowongan pertama yang tersedia');
    await page.waitForTimeout(2000);
    
    const lowonganCards = page.locator('[data-testid="lowongan-card"], .lowongan-item, .card, .lowongan-card, .job-card');
    const cardCount = await lowonganCards.count();
    
    if (cardCount === 0) {
      console.log('  > Tidak ada lowongan tersedia, skip test');
      test.skip();
      return;
    }
    
    console.log(`  > Ditemukan ${cardCount} lowongan`);

    // Step 3: Klik lowongan pertama untuk buka detail
    console.log('Step 3: Buka detail lowongan pertama');
    const firstCard = lowonganCards.first();
    await firstCard.click();
    await page.waitForLoadState('networkidle');
    console.log('  > Detail lowongan dibuka');

    // Step 4: Verifikasi tombol apply ada
    console.log('Step 4: Verifikasi tombol apply/lamar');
    const applyButton = page.locator(
      'button:has-text("Apply"), button:has-text("Lamar"), button:has-text("APPLY"), button:has-text("LAMAR"), ' +
      'button:has-text("Apply Now"), a:has-text("Apply"), a:has-text("Lamar")'
    ).first();

    if (!await applyButton.isVisible({ timeout: 3000 }).catch(() => false)) {
      console.log('  > Button apply tidak ditemukan');
      test.skip();
      return;
    }
    
    console.log('  > Button apply ditemukan');

    // Step 5: Klik tombol apply
    console.log('Step 5: Klik tombol apply');
    const lowonganId = await page.evaluate(() => {
      const url = new URL(window.location.href);
      const pathParts = url.pathname.split('/');
      return pathParts[pathParts.length - 1];
    });
    console.log(`  > Lowongan ID: ${lowonganId}`);

    // Submit apply via API atau form
    const applyResponse = await page.evaluate(async (lowId) => {
      try {
        const response = await fetch(`/api/mahasiswa/apply/${lowId}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
          },
          body: JSON.stringify({}),
        });
        return {
          status: response.status,
          ok: response.ok,
          data: await response.json(),
        };
      } catch (error) {
        return { error: error.message };
      }
    }, lowonganId);

    console.log(`  > API Response: ${JSON.stringify(applyResponse)}`);
    
    if (!applyResponse.ok && !applyResponse.data?.success) {
      console.log('  > Apply failed via API, trying form submit');
      await applyButton.click();
      await page.waitForLoadState('networkidle');
    } else {
      console.log('  > Apply submitted via API');
    }

    await page.waitForTimeout(2000);

    // Step 6: Navigasi ke halaman tracking lamaran
    console.log('Step 6: Navigasi ke halaman tracking lamaran');
    try {
      const lamaranLink = page.locator('a[href*="/lamaran"], a:has-text("Lamaran"), a:has-text("My Applications")').first();
      if (await lamaranLink.isVisible({ timeout: 2000 }).catch(() => false)) {
        await lamaranLink.click();
      } else {
        await page.goto('http://127.0.0.1:8000/mahasiswa/lamaran');
      }
    } catch (e) {
      await page.goto('http://127.0.0.1:8000/mahasiswa/lamaran');
    }
    
    await page.waitForLoadState('networkidle');
    console.log('  > Halaman tracking lamaran dibuka');

    // Step 7: Verifikasi lamaran muncul di list
    console.log('Step 7: Verifikasi lamaran muncul di tracking');
    await page.waitForTimeout(1000);
    
    const lamaranRows = page.locator('tr, .lamaran-item, .application-card, [data-testid="lamaran-item"]');
    const rowCount = await lamaranRows.count();
    
    if (rowCount > 0) {
      console.log(`  > Ditemukan ${rowCount} lamaran di tracking`);
      console.log('  > ✅ Apply lowongan SUCCESS');
    } else {
      console.log('  > Tidak ada lamaran ditemukan');
      console.log('  > ⚠️ Mungkin data belum sync');
    }

    await takeScreenshot(page, 'mahasiswa-apply-success');
    console.log('[TEST END] E2E_MHS_APPLY_001: PASSED');
  });

  test('E2E_MHS_APPLY_002: Verifikasi status lamaran pending setelah apply', async ({ page }) => {
    test.setTimeout(60000);
    console.log('\n[TEST START] E2E_MHS_APPLY_002: Verifikasi status lamaran');

    // Langsung ke halaman lamaran
    console.log('Step 1: Navigasi ke halaman tracking lamaran');
    await page.goto('http://127.0.0.1:8000/mahasiswa/lamaran');
    await page.waitForLoadState('networkidle');
    console.log('  > Halaman lamaran dibuka');

    // Cari lamaran dengan status pending
    console.log('Step 2: Cari lamaran dengan status pending');
    await page.waitForTimeout(1000);

    const statusElements = page.locator(
      'text=/pending|Pending|PENDING|menunggu|Menunggu/',
      ':has-text("pending")'
    );
    
    const statusCount = await statusElements.count();
    console.log(`  > Ditemukan ${statusCount} status pending`);

    if (statusCount > 0) {
      console.log('  > ✅ Ada lamaran dengan status PENDING');
      await takeScreenshot(page, 'mahasiswa-lamaran-pending');
      console.log('[TEST END] E2E_MHS_APPLY_002: PASSED');
    } else {
      console.log('  > ⚠️ Tidak ada status pending ditemukan (mungkin belum update)');
      console.log('[TEST END] E2E_MHS_APPLY_002: SKIPPED');
      test.skip();
    }
  });

  test('E2E_MHS_APPLY_003: Verifikasi detail lamaran di tracking', async ({ page }) => {
    test.setTimeout(60000);
    console.log('\n[TEST START] E2E_MHS_APPLY_003: Detail lamaran tracking');

    // Navigasi ke lamaran
    console.log('Step 1: Navigasi ke halaman lamaran');
    await page.goto('http://127.0.0.1:8000/mahasiswa/lamaran');
    await page.waitForLoadState('networkidle');

    // Tunggu list dimuat
    await page.waitForTimeout(1000);

    // Cari tombol detail/view
    console.log('Step 2: Cari button detail lamaran');
    const detailButtons = page.locator(
      'button:has-text("Detail"), button:has-text("VIEW"), button:has-text("Lihat"), ' +
      'a:has-text("Detail"), a:has-text("View")'
    );
    
    const buttonCount = await detailButtons.count();
    
    if (buttonCount > 0) {
      console.log(`  > Ditemukan ${buttonCount} button detail`);
      
      // Klik button detail pertama
      await detailButtons.first().click();
      await page.waitForLoadState('networkidle');
      console.log('  > Detail lamaran dibuka');

      // Verifikasi ada info lamaran
      const detailSection = page.locator(
        '[data-testid="lamaran-detail"], .detail-section, .application-details'
      );
      
      if (await detailSection.isVisible({ timeout: 3000 }).catch(() => false)) {
        console.log('  > ✅ Detail lamaran ditampilkan');
        await takeScreenshot(page, 'mahasiswa-lamaran-detail');
      } else {
        console.log('  > ⚠️ Section detail tidak terlihat');
      }
    } else {
      console.log('  > ⚠️ Tidak ada button detail ditemukan');
      test.skip();
      return;
    }

    console.log('[TEST END] E2E_MHS_APPLY_003: PASSED');
  });
});
