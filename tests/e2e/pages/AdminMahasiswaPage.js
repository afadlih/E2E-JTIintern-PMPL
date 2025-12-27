/**
 * Contoh Page Object: AdminMahasiswaPage
 * Tujuan: Menunjukkan pattern yang lebih maintainable dibanding selector langsung di test.
 * Gunakan data-testid di aplikasi untuk membuat selector stabil (disarankan refactor UI menambah atribut).
 */
class AdminMahasiswaPage {
  /**
   * @param {import('@playwright/test').Page} page
   */
  constructor(page) {
    this.page = page;
    this.menuMahasiswa = page.locator('text=/data mahasiswa|mahasiswa/i');
    this.addButton = page.locator('button:has-text("Tambah"), a:has-text("Tambah Mahasiswa")').first();
    this.searchInput = page.locator('input[type="search"], input[name="search"], input[placeholder*="cari"]').first();
  }

  async gotoList() {
    await this.menuMahasiswa.click();
    await this.page.waitForLoadState('networkidle');
  }

  async startCreate() {
    await this.addButton.click();
    await this.page.waitForTimeout(500);
  }

  async fillCreateForm(data) {
    await this.page.fill('input[name="nim"], input[id="nim"]', data.nim);
    await this.page.fill('input[name="nama"], input[id="nama"]', data.nama);
    await this.page.fill('input[name="email"], input[id="email"]', data.email);
    const phoneInput = this.page.locator('input[name="no_hp"], input[id="no_hp"]').first();
    if (await phoneInput.isVisible()) await phoneInput.fill(data.no_hp);
  }

  async submit() {
    // Read-only: do not perform submit in page object for safety. Tests should assert presence instead.
    const btn = this.page.locator('button[type="submit"], button:has-text("Simpan")').first();
    if (await btn.isVisible({ timeout: 2000 })) {
      await btn.hover();
      await this.page.waitForTimeout(300);
    }
  }

  async search(term) {
    if (await this.searchInput.isVisible()) {
      await this.searchInput.fill(term);
      await this.page.waitForTimeout(700);
    }
  }
}

module.exports = { AdminMahasiswaPage };
