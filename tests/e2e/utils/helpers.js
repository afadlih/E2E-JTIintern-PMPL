/**
 * Test Utilities - Helper Functions
 * Kumpulan fungsi helper untuk mempermudah penulisan test
 */

/**
 * Helper untuk login manual (tanpa storageState)
 * Berguna untuk test multi-role atau test login itu sendiri
 */
async function loginAs(page, role, credentials) {
  const roleConfig = {
    admin: {
      email: credentials?.admin?.email || process.env.ADMIN_EMAIL || 'admin@polinema.ac.id',
      password: credentials?.admin?.password || process.env.ADMIN_PASSWORD || 'password123',
      expectedUrl: '/dashboard'
    },
    mahasiswa: {
      email: credentials?.mahasiswa?.email || process.env.MAHASISWA_EMAIL || 'mahasiswa@polinema.ac.id',
      password: credentials?.mahasiswa?.password || process.env.MAHASISWA_PASSWORD || 'password123',
      expectedUrl: '/mahasiswa/dashboard'
    },
    dosen: {
      email: credentials?.dosen?.email || process.env.DOSEN_EMAIL || 'dosen@polinema.ac.id',
      password: credentials?.dosen?.password || process.env.DOSEN_PASSWORD || 'password123',
      expectedUrl: '/dosen/dashboard'
    }
  };

  const config = roleConfig[role];
  if (!config) {
    throw new Error(`Unknown role: ${role}. Valid roles: admin, mahasiswa, dosen`);
  }

  await page.goto('/login');
  await page.waitForSelector('input[name="email"]');
  await page.fill('input[name="email"]', config.email);
  await page.fill('input[name="password"]', config.password);
  await page.click('button[type="submit"]');
  await page.waitForURL(`**${config.expectedUrl}`, { timeout: 15000 });

  return config.expectedUrl;
}

/**
 * Helper untuk logout
 */
async function logout(page) {
  const logoutButton = page.locator('button:has-text("Logout"), a:has-text("Logout"), form[action*="logout"] button');

  if (await logoutButton.isVisible({ timeout: 5000 })) {
    await logoutButton.click();
    await page.waitForURL('**/login', { timeout: 10000 });
    return true;
  }

  return false;
}

/**
 * Helper untuk menunggu dan memverifikasi notifikasi sukses
 */
async function expectSuccessNotification(page, messagePattern = /berhasil|success|sukses/i) {
  const notification = page.locator(
    `text=${messagePattern}, .alert-success, .toast-success, .notification-success`
  ).first();

  await notification.waitFor({ state: 'visible', timeout: 10000 });
  return notification;
}

/**
 * Helper untuk menunggu dan memverifikasi notifikasi error
 */
async function expectErrorNotification(page, messagePattern = /error|gagal|failed/i) {
  const notification = page.locator(
    `text=${messagePattern}, .alert-error, .alert-danger, .toast-error, .notification-error`
  ).first();

  await notification.waitFor({ state: 'visible', timeout: 10000 });
  return notification;
}

/**
 * Helper untuk fill form dengan multiple fields
 * @param {Page} page - Playwright page object
 * @param {Object} formData - Object dengan key = selector, value = data
 *
 * Contoh:
 * await fillForm(page, {
 *   'input[name="nama"]': 'John Doe',
 *   'input[name="nim"]': '2141720001',
 *   'select[name="kelas_id"]': '1'
 * });
 */
async function fillForm(page, formData) {
  for (const [selector, value] of Object.entries(formData)) {
    const element = page.locator(selector).first();

    // Check element type
    const tagName = await element.evaluate(el => el.tagName.toLowerCase());
    const inputType = await element.evaluate(el => el.type || '');

    if (tagName === 'select') {
      await element.selectOption(value);
    } else if (inputType === 'checkbox' || inputType === 'radio') {
      if (value === true || value === 'true') {
        await element.check();
      } else {
        await element.uncheck();
      }
    } else if (inputType === 'file') {
      await element.setInputFiles(value);
    } else {
      await element.fill(String(value));
    }
  }
}

/**
 * Helper untuk menunggu API response tertentu
 * @param {Page} page
 * @param {string} urlPattern - Pattern URL API yang ditunggu
 * @param {Function} action - Async function yang trigger API call
 */
async function waitForApiResponse(page, urlPattern, action) {
  const responsePromise = page.waitForResponse(
    response => response.url().includes(urlPattern) && response.status() === 200,
    { timeout: 30000 }
  );

  await action();

  const response = await responsePromise;
  return await response.json();
}

/**
 * Helper untuk take screenshot dengan nama custom
 */
async function takeScreenshot(page, name, options = {}) {
  const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
  const filename = `playwright-report/screenshots/${name}-${timestamp}.png`;

  await page.screenshot({
    path: filename,
    fullPage: options.fullPage !== false, // Default true
    ...options
  });

  console.log(`📸 Screenshot saved: ${filename}`);
  return filename;
}

/**
 * Helper untuk verifikasi table data
 * @param {Page} page
 * @param {string} tableSelector - Selector untuk table element
 * @param {number} expectedMinRows - Minimal jumlah baris yang diharapkan
 */
async function verifyTableHasData(page, tableSelector, expectedMinRows = 1) {
  const table = page.locator(tableSelector).first();
  await table.waitFor({ state: 'visible', timeout: 10000 });

  const rows = table.locator('tbody tr, tr[data-row]');
  const rowCount = await rows.count();

  if (rowCount < expectedMinRows) {
    throw new Error(
      `Expected at least ${expectedMinRows} rows in table, but found ${rowCount}`
    );
  }

  return rowCount;
}

/**
 * Helper untuk navigasi ke menu dengan retry
 */
async function navigateToMenu(page, menuText, options = {}) {
  const maxRetries = options.retries || 3;
  const timeout = options.timeout || 5000;

  for (let i = 0; i < maxRetries; i++) {
    try {
      const menuLink = page.locator(`a:has-text("${menuText}"), button:has-text("${menuText}")`).first();
      await menuLink.waitFor({ state: 'visible', timeout });
      await menuLink.click();
      await page.waitForLoadState('networkidle', { timeout: 10000 });
      return true;
    } catch (error) {
      if (i === maxRetries - 1) throw error;
      await page.waitForTimeout(1000);
    }
  }
}

/**
 * Helper untuk check apakah element exists (tanpa throw error)
 */
async function elementExists(page, selector, timeout = 5000) {
  try {
    await page.waitForSelector(selector, { timeout, state: 'attached' });
    return true;
  } catch {
    return false;
  }
}

/**
 * Helper untuk get text dari element dengan fallback
 */
async function getTextContent(page, selector, fallback = '') {
  try {
    const element = page.locator(selector).first();
    await element.waitFor({ state: 'attached', timeout: 5000 });
    return await element.innerText();
  } catch {
    return fallback;
  }
}

/**
 * Helper untuk upload file
 */
async function uploadFile(page, fileInputSelector, filePath) {
  const fileInput = page.locator(fileInputSelector);
  await fileInput.waitFor({ state: 'attached', timeout: 5000 });
  await fileInput.setInputFiles(filePath);
}

/**
 * Helper untuk wait dengan custom condition
 */
async function waitForCondition(page, conditionFn, options = {}) {
  const timeout = options.timeout || 30000;
  const interval = options.interval || 500;
  const startTime = Date.now();

  while (Date.now() - startTime < timeout) {
    if (await conditionFn(page)) {
      return true;
    }
    await page.waitForTimeout(interval);
  }

  throw new Error('Timeout waiting for condition');
}

/**
 * Helper untuk generate random data
 */
function generateRandomData(type) {
  const timestamp = Date.now();

  const generators = {
    email: () => `test.${timestamp}@polinema.ac.id`,
    nim: () => `214172${String(timestamp).slice(-4)}`,
    nama: () => `Test User ${timestamp}`,
    phone: () => `0812${String(timestamp).slice(-8)}`,
    password: () => `Test@${timestamp}`,
  };

  return generators[type] ? generators[type]() : `random_${timestamp}`;
}

/**
 * Helper untuk create context dengan authentication
 */
async function createAuthenticatedContext(browser, role) {
  const path = require('path');
  const fs = require('fs');

  const storageStatePath = path.resolve(__dirname, '..', 'auth-states', `${role}.json`);

  if (!fs.existsSync(storageStatePath)) {
    throw new Error(
      `Storage state file not found for role: ${role}. ` +
      `Please run 'node tests/setup-auth.js' first.`
    );
  }

  return await browser.newContext({
    storageState: storageStatePath
  });
}

module.exports = {
  loginAs,
  logout,
  expectSuccessNotification,
  expectErrorNotification,
  fillForm,
  waitForApiResponse,
  takeScreenshot,
  verifyTableHasData,
  navigateToMenu,
  elementExists,
  getTextContent,
  uploadFile,
  waitForCondition,
  generateRandomData,
  createAuthenticatedContext,
};
