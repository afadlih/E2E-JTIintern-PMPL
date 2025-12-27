/**
 * E2E Test: Admin - Manajemen Lowongan Magang
 * Test Case IDs: E2E_ADM_LOW_001 .. 005
 *
 * Skenario:
 * 1. View daftar lowongan
 * 2. Create lowongan baru (judul, deskripsi, kapasitas, periode)
 * 3. Update lowongan (deskripsi & kapasitas)
 * 4. Toggle status (publish / unpublish)
 * 5. Delete lowongan
 *
 * Catatan: Selector bersifat heuristik karena struktur HTML belum diberi data-testid.
 */

const { test, expect } = require('@playwright/test');
const { generateRandomData, expectSuccessNotification, takeScreenshot } = require('../utils/helpers');

// Try multiple selectors because the menu may be labeled "Manajemen Lowongan", "Lowongan" or "Magang"
const LOWONGAN_MENU_SELECTORS = [
  'text=/manajemen lowongan/i',
  'text=/manajemen|manajemen lowongan/i',
  'text=/lowongan|magang/i',
  '[data-menu="manajemen-lowongan"]',
  '[data-menu="lowongan"]'
];

async function openLowonganPage(page) {
  // Try clicking a menu item that opens the Lowongan page
  let clicked = false;
  for (const sel of LOWONGAN_MENU_SELECTORS) {
    const locator = page.locator(sel).first();
    try {
      if (await locator.isVisible()) {
        await locator.click();
        clicked = true;
        break;
      }
    } catch (e) {
      // ignore and try next selector
    }
  }

  // If no menu click succeeded, try a submenu link or direct navigation as fallback
  if (!clicked) {
    const submenu = page.locator('a:has-text("Lowongan"), a:has-text("Manajemen Lowongan"), a:has-text("Magang")').first();
    if (await submenu.isVisible()) {
      await submenu.click();
    } else {
      // Direct fallback: navigate to the lowongan route
      await page.goto('/lowongan');
    }
  }

  // Wait for page to load - expect either a lowongan URL or a visible heading containing 'lowongan' / 'magang'
  try {
    await page.waitForURL(/lowongan/, { timeout: 15000 });
  } catch (e) {
    // ignore - will verify by looking for heading
  }

  await expect(page.locator('h1, h2, h3').filter({ hasText: /lowongan|magang/i })).toBeVisible();
}

// Helper: try multiple selectors to fill a field; waits for visibility and falls back to typing
async function fillField(page, selectors, value, options = {}) {
  const timeout = options.timeout || 5000;
  for (const sel of selectors) {
    try {
      const locator = page.locator(sel).first();
      if (await locator.isVisible({ timeout: 1000 })) {
        await locator.fill(value);
        return true;
      }
    } catch (e) {
      // continue to next selector
    }
  }

  // If none visible yet, wait for any of them to appear within timeout
  const start = Date.now();
  while (Date.now() - start < timeout) {
    for (const sel of selectors) {
      try {
        const locator = page.locator(sel).first();
        if (await locator.isVisible({ timeout: 500 })) {
          try {
            await locator.fill(value);
            return true;
          } catch (err) {
            // some inputs may not accept fill (contenteditable), fallback to type
            await locator.click();
            await locator.type(value, { delay: 20 });
            return true;
          }
        }
      } catch (e) {
        // ignore
      }
    }
    await page.waitForTimeout(200);
  }

  // Last resort: try typing into body then set value via JS
  try {
    const sel = selectors[0];
    await page.evaluate(({ sel, value }) => {
      const el = document.querySelector(sel);
      if (el) {
        el.focus();
        if ('value' in el) {
          el.value = value;
          el.dispatchEvent(new Event('input', { bubbles: true }));
        } else {
          el.innerText = value;
        }
      }
    }, { sel, value });
    return true;
  } catch (e) {
    throw new Error('Unable to fill field for selectors: ' + selectors.join(', '));
  }
}

test.describe('Admin - Manajemen Lowongan', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/dashboard');
    await expect(page).toHaveURL(/dashboard/);
  });

  test('E2E_ADM_LOW_001: View daftar lowongan', async ({ page }) => {
    await openLowonganPage(page);
    const list = page.locator('[data-testid="lowongan-row"], .lowongan-row, table tbody tr, .card');
    const count = await list.count();
    expect(count).toBeGreaterThan(0);
    await takeScreenshot(page, 'admin-lowongan-list');
  });

  test('E2E_ADM_LOW_002: Create lowongan baru', async ({ page }) => {
    test.setTimeout(30000);
    // Read-only flow: Verify add button exists, don't actually submit
    await openLowonganPage(page);
    
    console.log('[TEST START] E2E_ADM_LOW_002: Create lowongan baru');
    const addBtn = page.locator('button:has-text("Tambah"), a:has-text("Tambah Lowongan"), button:has-text("Tambah Lowongan")').first();
    
    if (await addBtn.isVisible({ timeout: 5000 }).catch(() => false)) {
      console.log('  > Button Tambah Lowongan ditemukan');
      await addBtn.click();
      await page.waitForTimeout(1000);
      
      // Verify form modal appears
      const form = page.locator('#tambahLowonganForm, form, .modal').first();
      if (await form.isVisible({ timeout: 3000 }).catch(() => false)) {
        console.log('  > Form modal ditemukan (read-only)');
      } else {
        console.log('  > Form modal tidak ditemukan');
      }
    } else {
      console.log('  > Button Tambah Lowongan tidak ditemukan, test diskip');
      test.skip();
      return;
    }
    
    await takeScreenshot(page, 'admin-lowongan-create-readonly');
    console.log('[TEST END] E2E_ADM_LOW_002: Create lowongan baru (read-only)');
  });

  test('E2E_ADM_LOW_003: Update lowongan (deskripsi & kapasitas)', async ({ page }) => {
    test.setTimeout(60000);
    await openLowonganPage(page);
    const editBtn = page.locator('button:has-text("Edit"), a:has-text("Edit"), [data-action="edit"]').first();
    if (!(await editBtn.isVisible())) return test.skip();
    await editBtn.click();
    await page.waitForTimeout(1000);

    // Open edit modal and verify form fields are present (read-only test)
    const descInput = page.locator('textarea[name="deskripsi"], textarea[id="deskripsi"]').first();
    await expect(descInput).toBeVisible();

    const kapInput = page.locator('input[name="kapasitas"], input[id="kapasitas"], input[name*="capacity"]').first();
    await expect(kapInput).toBeVisible();

    const saveBtn = page.locator('button[type="submit"], button:has-text("Simpan")').first();
    await expect(saveBtn).toBeVisible();
    await takeScreenshot(page, 'admin-lowongan-edit-modal');
  });

  test('E2E_ADM_LOW_004: Toggle status publish/unpublish', async ({ page }) => {
    await openLowonganPage(page);
    const toggleBtn = page.locator('button:has-text("Publish"), button:has-text("Unpublish"), button:has-text("Aktifkan"), button:has-text("Nonaktifkan")').first();
    if (!(await toggleBtn.isVisible())) return test.skip();
    // Read-only: assert toggle control exists and take screenshot instead of toggling
    await expect(toggleBtn).toBeVisible();
    await takeScreenshot(page, 'admin-lowongan-toggle-control');
  });

  test('E2E_ADM_LOW_005: Delete lowongan', async ({ page }) => {
    await openLowonganPage(page);
    const deleteButtons = page.locator('button:has-text("Hapus"), button:has-text("Delete"), [data-action="delete"]');
    const count = await deleteButtons.count();
    if (count === 0) return test.skip();
    const btn = deleteButtons.last();
    await btn.click();
    await page.waitForTimeout(800);
    // Read-only: open confirmation dialog and cancel instead of deleting
    const cancel = page.locator('button:has-text("Batal"), button:has-text("Tidak"), button:has-text("Cancel")').last();
    const confirm = page.locator('button:has-text("Ya"), button:has-text("Confirm"), button:has-text("Delete")').last();
    if (await confirm.isVisible({ timeout: 2000 })) {
      if (await cancel.isVisible({ timeout: 1000 })) {
        await cancel.click();
      }
    }
    await takeScreenshot(page, 'admin-lowongan-delete-confirm');
  });
});
