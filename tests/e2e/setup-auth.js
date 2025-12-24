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
    password: process.env.ADMIN_PASSWORD || 'admin',
    expectedUrl: '/dashboard',
    storageFile: 'tests/e2e/auth-states/admin.json'
  },
  mahasiswa: {
    email: process.env.MAHASISWA_EMAIL || '2341720074@student.com',
    password: process.env.MAHASISWA_PASSWORD || '2341720074',
    // App redirects mahasiswa to role dashboard
    expectedUrl: '/mahasiswa/dashboard',
    storageFile: 'tests/e2e/auth-states/mahasiswa.json'
  },
  dosen: {
    email: process.env.DOSEN_EMAIL || '1980031@gmail.com',
    password: process.env.DOSEN_PASSWORD || '1980031',
    // App redirects dosen to role dashboard
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

  // Allow running in headed mode for debugging by setting PLAYWRIGHT_HEADFUL=1
  const headful = !!process.env.PLAYWRIGHT_HEADFUL;
  const browser = await chromium.launch({
    headless: !headful,
    slowMo: headful ? 100 : 0
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
        // wait for full load so any client-side scripts that modify inputs finish
        waitUntil: 'load',
        timeout: 60000
      });
    console.log('  > Page loaded successfully');

    // 2. Tunggu form login muncul
    console.log('  Step 2: Waiting for login form');

    // Try a set of fallback selectors for the email input to handle different markup
    const emailSelectors = [
      'input[name="email"]',
      'input[type="email"]',
      'input[id="email"]',
      'input[name*="email"]',
      'input[type="text"]'
    ];

    let emailSelector = null;
    for (const s of emailSelectors) {
      try {
        await page.waitForSelector(s, { timeout: 3000 });
        emailSelector = s;
        break;
      } catch (e) {
        // ignore and try next
      }
    }

    if (!emailSelector) {
      throw new Error('Login email input not found using fallback selectors');
    }

    console.log(`  > Found email input using selector: ${emailSelector}`);

    // 3. Clear dan isi form login (form punya default values)
    console.log(`  Step 3: Filling login form (${config.email})`);
    // Helper to robustly set input value (fill -> evaluate fallback -> keyboard fallback)
    async function setInputValue(selector, value) {
      const locator = page.locator(selector);
      try {
        await locator.fill('');
        await locator.fill(value);
      } catch (e) {
        // ignore and try fallback
      }
        // Try multiple strategies with retries
        for (let attempt = 1; attempt <= 4; attempt++) {
          try {
            // 1) Programmatic fill
            await locator.fill('');
            await locator.fill(value);
          } catch (e) {}

          // 2) Remove possible value attribute and set via JS
          await page.evaluate(({ selector, value }) => {
            const el = document.querySelector(selector);
            if (!el) return;
            try { el.removeAttribute('value'); } catch (e) {}
            el.focus();
            el.value = value;
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
          }, { selector, value });

          // 3) Keyboard typing fallback
          try {
            await page.focus(selector).catch(() => {});
            await page.keyboard.press('Control+A').catch(() => {});
            await page.keyboard.type(value, { delay: 40 });
          } catch (e) {}

          // small wait and verify
          await page.waitForTimeout(200);
          const final = await page.evaluate((s) => document.querySelector(s)?.value, selector);
          console.log(`  > [${selector}] attempt ${attempt} -> length ${final ? final.length : 0}`);
          if (final === value) return;
        }
        // after retries, log warning with final value
        const final = await page.evaluate((s) => document.querySelector(s)?.value, selector);
        console.log(`  > Warning: final value for ${selector} does not match. final='${String(final).slice(0,30)}'`);
    }

    await setInputValue(emailSelector, config.email);

    // password selector fallbacks
    const passwordSelectors = ['input[name="password"]', 'input[type="password"]', 'input[name*="pass"]'];
    let passwordSelector = null;
    for (const s of passwordSelectors) {
      try {
        await page.waitForSelector(s, { timeout: 2000 });
        passwordSelector = s;
        break;
      } catch (e) {}
    }
    if (!passwordSelector) {
      throw new Error('Password input not found');
    }

    // Use the same robust setter for password
      // Use the same robust setter for password (reuses setInputValue)
      async function setPassword(selector, value) {
        await setInputValue(selector, value);
        const plen = await page.evaluate((s) => document.querySelector(s)?.value.length || 0, selector);
        console.log(`  > Password field length: ${plen}`);
    }

    await setPassword(passwordSelector, config.password);

    // Tunggu sebentar untuk ensure value ter-fill
    await page.waitForTimeout(500);
    console.log('  > Form filled successfully');

    // Extra: simulate real user typing right before submit to avoid page scripts
    // that may overwrite programmatic value sets. This focuses the field,
    // selects all and types the password with a small delay.
    async function typeLikeHuman(selector, value) {
      try {
        await page.focus(selector).catch(() => {});
        await page.keyboard.press('Control+A').catch(() => {});
        await page.keyboard.type(value, { delay: 50 });
        // dispatch input/change in case
        await page.evaluate(({ selector }) => {
          const el = document.querySelector(selector);
          if (!el) return;
          el.dispatchEvent(new Event('input', { bubbles: true }));
          el.dispatchEvent(new Event('change', { bubbles: true }));
        }, { selector });
      } catch (e) {
        // ignore but log
        console.log('  > Warning: human-typing fallback failed', e.message);
      }
    }

    // Apply human-like typing to both fields before submit
    await typeLikeHuman(emailSelector, config.email);
    await typeLikeHuman(passwordSelector, config.password);

    // Final safeguard: attach a capture-phase submit handler that forces the
    // desired values into the inputs immediately when the form is submitted.
    // This ensures any page scripts that run right before submit cannot
    // overwrite the values we intend to send.
    await page.evaluate(({ emailSelector, passwordSelector, emailValue, passwordValue }) => {
      const form = document.querySelector('form');
      if (!form) return;
      // Remove previous listener if any
      try { form.removeEventListener('submit', window.__pw_force_submit_handler); } catch (e) {}
      const handler = function (ev) {
        try {
          const e = document.querySelector(emailSelector);
          const p = document.querySelector(passwordSelector);
          if (e) {
            e.value = emailValue;
            e.setAttribute('value', emailValue);
            e.dispatchEvent(new Event('input', { bubbles: true }));
            e.dispatchEvent(new Event('change', { bubbles: true }));
          }
          if (p) {
            p.value = passwordValue;
            p.setAttribute('value', passwordValue);
            p.dispatchEvent(new Event('input', { bubbles: true }));
            p.dispatchEvent(new Event('change', { bubbles: true }));
          }
        } catch (err) {
          // swallow
        }
      };
      // store handler so we can remove later
      window.__pw_force_submit_handler = handler;
      form.addEventListener('submit', handler, true);
    }, { emailSelector, passwordSelector, emailValue: config.email, passwordValue: config.password });

    // 4. Click tombol login dan tunggu navigation
    console.log('  Step 4: Submitting login form');
    await page.click('button[type="submit"]');

    // Tunggu navigation dengan timeout yang lebih panjang
    console.log('  Step 5: Waiting for navigation');
    try {
      // Prefer explicit dashboard location but also accept any URL different from /login
      await page.waitForURL(url => url.href.includes(config.expectedUrl) || url.href !== `${BASE_URL}/login`, { timeout: 15000 });
      console.log('  > Navigation detected');
    } catch (e) {
      console.log('  > WARNING: No navigation detected within timeout, capturing debug artifacts...');
      try {
        const failScreenshot = path.resolve(process.cwd(), `tests/e2e/auth-states/fail-${role}.png`);
        const failHtml = path.resolve(process.cwd(), `tests/e2e/auth-states/fail-${role}.html`);
        await page.screenshot({ path: failScreenshot, fullPage: true }).catch(() => {});
        const html = await page.content().catch(() => '');
        const fs = require('fs');
        fs.writeFileSync(failHtml, html);
        console.log(`  > Saved debug artifacts: ${failScreenshot}, ${failHtml}`);
      } catch (inner) {
        console.log('  > Failed to save debug artifacts:', inner.message);
      }
      const errorText = await page.textContent('body').catch(() => '');
      console.log(`  > Page content (snippet): ${errorText.substring(0, 400)}`);
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
    const storagePath = path.resolve(process.cwd(), config.storageFile);
    // ensure directory exists
    const fs = require('fs');
    const dir = require('path').dirname(storagePath);
    if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
    await context.storageState({ path: storagePath });

    console.log(`  > Storage state saved: ${path.basename(storagePath)}`);

  } catch (error) {
    console.error(`   ❌ Error during ${role} authentication:`, error.message);
    try {
      // try to capture page snapshot if possible
      const failScreenshot = path.resolve(process.cwd(), `tests/e2e/auth-states/fail-${role}.png`);
      await page.screenshot({ path: failScreenshot, fullPage: true }).catch(() => {});
      console.log(`   > Screenshot saved: ${failScreenshot}`);
    } catch (e) {}
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
