#  Testing Workflow - JTI Intern PMPL

## Daftar Isi

1. [Struktur Testing](#struktur-testing)
2. [Persiapan Awal](#persiapan-awal)
3. [API Testing](#api-testing)
4. [Feature Testing](#feature-testing)
5. [Unit Testing](#unit-testing)
6. [E2E Testing](#e2e-testing)
7. [Membuat Test Case Baru](#membuat-test-case-baru)
8. [Melihat Hasil Testing](#melihat-hasil-testing)
9. [Troubleshooting](#troubleshooting)
10. [Struktur Test Cases](#struktur-test-cases)

---

## Struktur Testing

Proyek ini menggunakan **4 jenis testing** untuk memastikan kualitas aplikasi:

```
tests/
├── Api/                          # API Testing
│   ├── Auth/
│   │   └── LoginApiTest.php
│   ├── Mahasiswa/
│   │   └── MahasiswaLowonganApiTest.php
│   └── Admin/
│       └── AdminMahasiswaApiTest.php
│
├── Feature/                      # Feature Testing
│   ├── Auth/
│   │   └── LoginTest.php
│   ├── Mahasiswa/
│   │   └── ApplyLowonganTest.php
│   └── Admin/
│       └── MahasiswaCRUDTest.php
│
├── Unit/                         # Unit Testing
│   ├── Helpers/
│   │   └── HelperFunctionsTest.php
│   ├── Models/
│   │   └── MahasiswaModelTest.php
│   └── Services/
│       ├── SPKRecommendationServiceTest.php
│       └── ValidationLogicTest.php
│
└── e2e/                          # E2E Testing
    ├── admin/
    │   ├── admin-login.spec.js
    │   └── admin-mahasiswa-crud.spec.js
    ├── mahasiswa/
    │   ├── mahasiswa-login.spec.js
    │   └── mahasiswa-apply-lowongan.spec.js
    ├── dosen/
    │   ├── dosen-login.spec.js
    │   └── dosen-mahasiswa-bimbingan.spec.js
    ├── multi-role/
    │   └── multi-role-workflow.spec.js
    ├── utils/
    ├── fixtures/
    ├── auth-states/
    └── setup-auth.js
```

---

## Persiapan Awal

### 1. Install Dependencies

#### A. Composer Dependencies (untuk PHPUnit Tests)

```bash
# Install semua PHP dependencies
composer install

# Verifikasi PHPUnit terinstall
./vendor/bin/phpunit --version
# Expected output: PHPUnit 10.x
```

#### B. Node.js Dependencies (untuk E2E Tests)

```bash
# Install semua Node.js dependencies
npm install

# Install Playwright browsers (Chromium, Firefox, WebKit)
npx playwright install

# Verifikasi Playwright terinstall
npx playwright --version
# Expected output: Version 1.x
```

### 2. Setup Database untuk Testing

#### A. Buat File .env.testing

```bash
# Windows (PowerShell)
Copy-Item .env .env.testing

# Linux/Mac
cp .env .env.testing
```

#### B. Edit .env.testing

Buka file `.env.testing` dan update konfigurasi database:

```env
# Gunakan SQLite untuk testing (recommended - lebih cepat)
DB_CONNECTION=sqlite
DB_DATABASE=:memory:

# ATAU gunakan MySQL/MariaDB testing database
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=jti_intern_testing
# DB_USERNAME=root
# DB_PASSWORD=
```

#### C. Jalankan Migration dan Seeder

```bash
# Run migration untuk testing database
php artisan migrate --env=testing

# Seed data untuk testing
php artisan db:seed --env=testing

# Verifikasi database berisi data
php artisan db:show --env=testing
```

### 3. Setup Environment Variables untuk E2E Testing

#### A. Buat File .env.playwright (jika belum ada)

```bash
# Buat file .env.playwright di root project
# Windows (PowerShell)
New-Item -Path .env.playwright -ItemType File

# Linux/Mac
touch .env.playwright
```

#### B. Isi File .env.playwright

```env
# Base URL aplikasi
BASE_URL=http://127.0.0.1:8000

# Credentials untuk Admin
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=secret

# Credentials untuk Mahasiswa
MAHASISWA_EMAIL=mahasiswa1@example.com
MAHASISWA_PASSWORD=secret

# Credentials untuk Dosen
DOSEN_EMAIL=dosen1@example.com
DOSEN_PASSWORD=secret
```

**PENTING:** Pastikan credentials ini sesuai dengan data di database Anda!

### 4. Setup Database Production (untuk E2E Testing)

```bash
# Run migration di database production (development)
php artisan migrate

# Seed users untuk testing
php artisan db:seed --class=DatabaseSeeder

# Verifikasi users sudah ada
php artisan tinker
# >>> User::where('email', 'admin@example.com')->first()
# >>> exit
```

### 5. Start Laravel Development Server

```bash
# Start server di port 8000
php artisan serve --host=127.0.0.1 --port=8000
```

**Output yang diharapkan:**

```
INFO  Server running on [http://127.0.0.1:8000].

Press Ctrl+C to stop the server
```

**PENTING:** 
- Server harus tetap running selama E2E tests berjalan
- Buka terminal/tab baru untuk menjalankan testing commands
- Tunggu minimal 10 detik setelah server start sebelum menjalankan tests

---

## API Testing

API Testing menguji REST API endpoints dengan JSON request/response menggunakan Laravel Sanctum authentication.

### Apa itu API Testing?

API Testing fokus pada testing endpoint REST API dengan:
- JSON request/response
- Bearer Token authentication (Sanctum)
- HTTP status codes (200, 201, 400, 401, 403, 422)
- Response structure validation
- Authorization dan permissions

### Cara Menjalankan API Tests

#### Basic Commands

```bash
# Run SEMUA API tests
php artisan test tests/Api

# Run dengan output yang lebih readable
php artisan test tests/Api --testdox

# Run dengan parallel execution (lebih cepat)
php artisan test tests/Api --parallel
```

#### Run Specific Test File

```bash
# Test login API
php artisan test tests/Api/Auth/LoginApiTest.php

# Test mahasiswa lowongan API
php artisan test tests/Api/Mahasiswa/MahasiswaLowonganApiTest.php

# Test admin mahasiswa CRUD API
php artisan test tests/Api/Admin/AdminMahasiswaApiTest.php
```

#### Run Specific Test Method

```bash
# Run satu test method saja
php artisan test --filter test_api_login_admin_berhasil

# Run test yang mengandung kata "lowongan"
php artisan test --filter lowongan
```

#### Output Example

```
PASS  Tests\Api\Auth\LoginApiTest
✓ api login admin berhasil                                     0.45s
✓ api login credentials salah                                  0.12s
✓ api login mahasiswa berhasil                                 0.23s
...

Tests:    10 passed (30 assertions)
Duration: 2.45s
```

### API Test Coverage

- **LoginApiTest.php**
  - Login dengan berbagai role (admin, mahasiswa, dosen)
  - Test validation errors
  - Test token authentication
  - Test logout

- **MahasiswaLowonganApiTest.php**
  - Get list lowongan
  - Get lowongan detail
  - Apply lowongan
  - Check application status
  - Test authorization

- **AdminMahasiswaApiTest.php**
  - CRUD operations untuk mahasiswa
  - Test validation
  - Test search dan filter
  - Test role permissions

---

## Feature Testing

Feature Testing menguji full application workflow dengan database dan HTTP menggunakan session authentication.

### Apa itu Feature Testing?

Feature Testing fokus pada testing workflow lengkap aplikasi dengan:
- Database interactions (CRUD operations)
- Session-based authentication
- HTTP redirects (302 status codes)
- Form submissions dengan CSRF
- Integration antar komponen (Controller → Model → Database)

### Cara Menjalankan Feature Tests

#### Basic Commands

```bash
# Run SEMUA Feature tests
php artisan test tests/Feature

# Run dengan output yang lebih readable
php artisan test tests/Feature --testdox

# Run dengan stop on failure (berhenti saat ada error)
php artisan test tests/Feature --stop-on-failure
```

#### Run Specific Test File

```bash
# Test login flow dengan session
php artisan test tests/Feature/Auth/LoginTest.php

# Test mahasiswa apply lowongan workflow
php artisan test tests/Feature/Mahasiswa/ApplyLowonganTest.php

# Test admin CRUD mahasiswa
php artisan test tests/Feature/Admin/MahasiswaCRUDTest.php
```

#### Run Specific Test Method

```bash
# Run satu test method saja
php artisan test --filter test_admin_dapat_login_dengan_credentials_benar

# Run test yang mengandung kata "apply"
php artisan test --filter apply
```

#### Output Example

```
PASS  Tests\Feature\Auth\LoginTest
✓ admin dapat login dengan credentials benar                   0.56s
✓ mahasiswa dapat login dengan credentials benar               0.48s
✓ redirect ke dashboard setelah login                          0.32s
...

Tests:    8 passed (45 assertions)
Duration: 3.21s
```

### Feature Test Coverage

- **LoginTest.php**
  - Login flow dengan session authentication
  - Test redirect setelah login
  - Test remember me functionality

- **ApplyLowonganTest.php**
  - Mahasiswa view lowongan list
  - Mahasiswa apply lowongan
  - Prevent duplicate application

- **MahasiswaCRUDTest.php**
  - Admin create, read, update, delete mahasiswa
  - Test validation dan authorization

---

## Unit Testing

Unit Testing menguji pure logic functions tanpa dependency eksternal (no database, no HTTP).

### Apa itu Unit Testing?

Unit Testing fokus pada testing isolated functions dengan:
- Pure functions (input → output)
- Calculations dan validations
- String manipulations
- Business logic tanpa database
- Execution sangat cepat (< 1 detik)

### Cara Menjalankan Unit Tests

#### Basic Commands

```bash
# Run SEMUA Unit tests
php artisan test tests/Unit

# Run dengan output yang lebih readable
php artisan test tests/Unit --testdox

# Run dengan coverage report (requires Xdebug)
php artisan test tests/Unit --coverage
```

#### Run Specific Test File

```bash
# Test validation logic (NIM, IPK, etc)
php artisan test tests/Unit/Services/ValidationLogicTest.php

# Test model business logic
php artisan test tests/Unit/Models/MahasiswaModelTest.php

# Test helper functions
php artisan test tests/Unit/Helpers/HelperFunctionsTest.php
```

#### Run Specific Test Method

```bash
# Run satu test method saja
php artisan test --filter test_validate_nim_format_valid

# Run test yang mengandung kata "ipk"
php artisan test --filter ipk
```

#### Output Example

```
PASS  Tests\Unit\Services\ValidationLogicTest
✓ validate nim format valid                                    0.02s
✓ validate nim format invalid kurang 10 digit                  0.01s
✓ validate ipk range valid                                     0.01s
...

Tests:    10 passed (25 assertions)
Duration: 0.15s  ← Very fast!
```

### Unit Test Coverage

- **ValidationLogicTest.php**
  - Validate NIM format
  - Validate IPK range
  - Calculate average

- **MahasiswaModelTest.php**
  - Test model relationships
  - Test business logic
  - Test soft delete

- **HelperFunctionsTest.php**
  - Test helper functions
  - Format data
  - Calculations

---

## E2E Testing (End-to-End)

E2E Testing menguji aplikasi secara keseluruhan menggunakan real browser automation dengan Playwright.

### Apa itu E2E Testing?

E2E Testing fokus pada testing user interaction di browser dengan:
- Real browser automation (Chromium, Firefox, WebKit)
- Click buttons, fill forms, navigate pages
- Screenshot dan video recording
- Multi-role authentication (admin, mahasiswa, dosen)
- Full user workflow dari awal sampai akhir

### Prerequisites: Generate Authentication States

**PENTING:** Sebelum menjalankan E2E tests, Anda HARUS generate authentication states terlebih dahulu!

#### Step 1: Pastikan Server Running

```bash
# Buka terminal/tab baru
# Start Laravel server
php artisan serve --host=127.0.0.1 --port=8000

# Biarkan server tetap running
```

#### Step 2: Generate Auth States

```bash
# Buka terminal/tab LAIN (jangan tutup server)
# Generate authentication states
node tests\e2e\setup-auth.js
```

#### Output yang Diharapkan

```
Starting Multi-Role Authentication Setup...
Base URL: http://127.0.0.1:8000

Setting up authentication for: ADMIN
   -> Navigating to http://127.0.0.1:8000/login
   -> Login successful! Current URL: http://127.0.0.1:8000/dashboard
   ✓ Storage state saved to: tests\e2e\auth-states\admin.json

Setting up authentication for: MAHASISWA
   -> Navigating to http://127.0.0.1:8000/login
   -> Login successful! Current URL: http://127.0.0.1:8000/mahasiswa/dashboard
   ✓ Storage state saved to: tests\e2e\auth-states\mahasiswa.json

Setting up authentication for: DOSEN
   -> Navigating to http://127.0.0.1:8000/login
   -> Login successful! Current URL: http://127.0.0.1:8000/dosen/dashboard
   ✓ Storage state saved to: tests\e2e\auth-states\dosen.json

AUTHENTICATION SETUP SUMMARY
✓ Successful: admin, mahasiswa, dosen
✗ Failed: None

All authentication setups completed successfully!
```

#### File yang Dihasilkan

Authentication states disimpan di:

```
tests/e2e/auth-states/
├── admin.json       ← Session cookies untuk admin
├── mahasiswa.json   ← Session cookies untuk mahasiswa
└── dosen.json       ← Session cookies untuk dosen
```

**Fungsi:** File-file ini berisi session cookies yang akan digunakan oleh Playwright untuk menjalankan tests tanpa perlu login ulang setiap kali.

**Kapan perlu regenerate?**
- Password user berubah
- Session expired (setelah logout)
- Error "Authentication failed"

### Cara Menjalankan E2E Tests

**PENTING:** Pastikan:
1. Laravel server running di http://127.0.0.1:8000
2. Auth states sudah di-generate (`node tests\e2e\setup-auth.js`)

#### A. Run SEMUA E2E Tests

```bash
# Run all tests di semua projects (admin, mahasiswa, dosen)
npx playwright test
```

**Output:**

```
Running 16 tests using 4 workers

  ✓ [admin] admin-login.spec.js:5:5 › E2E_ADM_001: Admin dapat login (3.2s)
  ✓ [mahasiswa] mahasiswa-apply.spec.js:5:5 › E2E_MHS_003: Mahasiswa melihat lowongan (2.8s)
  ...

  16 passed (45s)

To open last HTML report run:
  npx playwright show-report
```

#### B. Run Tests by Role/Project

```bash
# Run HANYA tests untuk Admin
npx playwright test --project=admin

# Run HANYA tests untuk Mahasiswa
npx playwright test --project=mahasiswa

# Run HANYA tests untuk Dosen
npx playwright test --project=dosen
```

#### C. Run Specific Test File

```bash
# Run test file tertentu
npx playwright test tests/e2e/mahasiswa/mahasiswa-apply-lowongan.spec.js

# Run test file admin
npx playwright test tests/e2e/admin/admin-mahasiswa-crud.spec.js
```

#### D. Run Specific Test Case

```bash
# Run test dengan ID tertentu
npx playwright test -g "E2E_MHS_003"

# Run test yang mengandung kata "lowongan"
npx playwright test -g "lowongan"

# Run test yang mengandung kata "apply"
npx playwright test -g "apply"
```

#### E. Run dengan Browser Visible (Headed Mode)

```bash
# Lihat browser saat test berjalan
npx playwright test --headed

# Run specific test dengan headed mode
npx playwright test --headed -g "E2E_MHS_003"
```

**Kapan gunakan headed mode?**
- Debugging visual issues
- Melihat alur user interaction
- Verifikasi UI behavior

#### F. Run dengan Debug Mode (Step-by-Step)

```bash
# Debug mode dengan Playwright Inspector
npx playwright test --debug

# Debug specific test
npx playwright test --debug -g "E2E_MHS_003"
```

**Fitur Debug Mode:**
- Pause di setiap step
- Inspect elements
- View console logs
- Step through test

#### G. Run dengan Specific Browser

```bash
# Run di Chromium (default)
npx playwright test --project=chromium

# Run di Firefox
npx playwright test --project=firefox

# Run di WebKit (Safari)
npx playwright test --project=webkit

# Run di SEMUA browsers
npx playwright test --project=chromium --project=firefox --project=webkit
```

#### H. Run dengan UI Mode (Interactive)

```bash
# Buka Playwright UI Mode
npx playwright test --ui
```

**Fitur UI Mode:**
- Pick and run specific tests
- Watch mode (auto re-run on changes)
- Inspect DOM snapshots
- View test results
- Time travel debugging

---

## Membuat Test Case Baru

### Panduan Umum Membuat Test Case

Sebelum membuat test case, identifikasi jenis testing yang sesuai:

| Jenis Testing | Kapan Digunakan | Tool |
|---------------|-----------------|------|
| **API Testing** | Testing REST API endpoints dengan JSON response | PHPUnit |
| **Feature Testing** | Testing full workflow aplikasi web (form, redirect, session) | PHPUnit |
| **Unit Testing** | Testing fungsi/method isolated tanpa dependencies | PHPUnit |
| **E2E Testing** | Testing user interaction di browser (klik, isi form, navigate) | Playwright |

---

### Contoh 1: Membuat API Test Baru

**Scenario:** Testing API endpoint untuk get daftar perusahaan

**Langkah 1:** Buat file test baru

```bash
# Buat file di folder tests/Api/Admin/
touch tests/Api/Admin/AdminPerusahaanApiTest.php
```

**Langkah 2:** Tulis struktur test

```php
<?php

namespace Tests\Api\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Perusahaan;

class AdminPerusahaanApiTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'username' => 'admin_test',
            'role' => 'Admin'
        ]);
        
        // Generate token
        $this->token = $this->admin->createToken('test-token')->plainTextToken;
    }

    public function test_api_get_all_perusahaan()
    {
        // Arrange: Buat data test
        Perusahaan::factory()->count(5)->create();
        
        // Act: Panggil API endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/admin/perusahaan');
        
        // Assert: Verifikasi response
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'nama',
                             'alamat',
                             'kontak',
                             'created_at'
                         ]
                     ]
                 ])
                 ->assertJsonCount(5, 'data');
    }

    public function test_api_create_perusahaan_berhasil()
    {
        // Arrange: Siapkan data
        $data = [
            'nama' => 'PT Test Perusahaan',
            'alamat' => 'Jl. Test No. 123',
            'kontak' => '08123456789',
            'email' => 'test@perusahaan.com'
        ];
        
        // Act: Kirim POST request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson('/api/admin/perusahaan', $data);
        
        // Assert: Verifikasi response dan database
        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Perusahaan berhasil ditambahkan',
                     'data' => [
                         'nama' => 'PT Test Perusahaan'
                     ]
                 ]);
        
        $this->assertDatabaseHas('m_perusahaan', [
            'nama' => 'PT Test Perusahaan',
            'email' => 'test@perusahaan.com'
        ]);
    }

    public function test_api_create_perusahaan_validation_error()
    {
        // Arrange: Data tidak lengkap (missing required fields)
        $data = [
            'nama' => '' // Empty name should fail
        ];
        
        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson('/api/admin/perusahaan', $data);
        
        // Assert: Expect validation error
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nama']);
    }
}
```

**Langkah 3:** Jalankan test

```bash
php artisan test tests/Api/Admin/AdminPerusahaanApiTest.php --testdox
```

---

### Contoh 2: Membuat Feature Test Baru

**Scenario:** Testing workflow perusahaan upload dokumen

**Langkah 1:** Buat file test

```bash
touch tests/Feature/Perusahaan/UploadDokumenTest.php
```

**Langkah 2:** Tulis test

```php
<?php

namespace Tests\Feature\Perusahaan;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Perusahaan;
use App\Models\Dokumen;

class UploadDokumenTest extends TestCase
{
    use RefreshDatabase;

    public function test_perusahaan_dapat_upload_dokumen_pdf()
    {
        // Arrange: Setup fake storage
        Storage::fake('public');
        
        // Create perusahaan user
        $perusahaan = Perusahaan::factory()->create();
        $user = User::factory()->create([
            'id_ref' => $perusahaan->id,
            'role' => 'Perusahaan'
        ]);
        
        // Act: Login dan upload file
        $response = $this->actingAs($user)
                         ->post('/perusahaan/dokumen/upload', [
                             'judul' => 'Dokumen Test',
                             'file' => UploadedFile::fake()->create('dokumen.pdf', 500),
                             'jenis' => 'Proposal'
                         ]);
        
        // Assert: Check redirect dan file tersimpan
        $response->assertStatus(302)
                 ->assertSessionHas('success', 'Dokumen berhasil diupload');
        
        // Verify file stored
        Storage::disk('public')->assertExists('dokumen/dokumen.pdf');
        
        // Verify database
        $this->assertDatabaseHas('t_dokumen', [
            'judul' => 'Dokumen Test',
            'jenis' => 'Proposal'
        ]);
    }

    public function test_upload_dokumen_gagal_jika_bukan_pdf()
    {
        // Arrange
        $perusahaan = Perusahaan::factory()->create();
        $user = User::factory()->create([
            'id_ref' => $perusahaan->id,
            'role' => 'Perusahaan'
        ]);
        
        // Act: Upload file dengan format salah
        $response = $this->actingAs($user)
                         ->post('/perusahaan/dokumen/upload', [
                             'judul' => 'Dokumen Test',
                             'file' => UploadedFile::fake()->create('dokumen.txt', 100),
                             'jenis' => 'Proposal'
                         ]);
        
        // Assert: Expect validation error
        $response->assertStatus(302)
                 ->assertSessionHasErrors(['file']);
    }
}
```

**Langkah 3:** Jalankan test

```bash
php artisan test tests/Feature/Perusahaan/UploadDokumenTest.php
```

---

### Contoh 3: Membuat Unit Test Baru

**Scenario:** Testing helper function untuk format tanggal Indonesia

**Langkah 1:** Buat file test

```bash
touch tests/Unit/Helpers/DateHelperTest.php
```

**Langkah 2:** Tulis test

```php
<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;

class DateHelperTest extends TestCase
{
    public function test_format_tanggal_indonesia_dari_string()
    {
        // Arrange
        $tanggal = '2025-01-15';
        
        // Act
        $result = format_tanggal_indonesia($tanggal);
        
        // Assert
        $this->assertEquals('15 Januari 2025', $result);
    }

    public function test_format_tanggal_dengan_hari()
    {
        // Arrange
        $tanggal = '2025-01-15'; // Rabu
        
        // Act
        $result = format_tanggal_indonesia($tanggal, true);
        
        // Assert
        $this->assertEquals('Rabu, 15 Januari 2025', $result);
    }

    public function test_format_tanggal_return_empty_jika_invalid()
    {
        // Arrange
        $tanggal = 'invalid-date';
        
        // Act
        $result = format_tanggal_indonesia($tanggal);
        
        // Assert
        $this->assertEquals('', $result);
    }

    /**
     * @dataProvider bulanIndonesiaProvider
     */
    public function test_semua_bulan_indonesia($bulan, $expected)
    {
        // Arrange
        $tanggal = "2025-{$bulan}-01";
        
        // Act
        $result = format_tanggal_indonesia($tanggal);
        
        // Assert
        $this->assertStringContainsString($expected, $result);
    }

    public function bulanIndonesiaProvider()
    {
        return [
            'Januari' => ['01', 'Januari'],
            'Februari' => ['02', 'Februari'],
            'Maret' => ['03', 'Maret'],
            'April' => ['04', 'April'],
            'Mei' => ['05', 'Mei'],
            'Juni' => ['06', 'Juni'],
            'Juli' => ['07', 'Juli'],
            'Agustus' => ['08', 'Agustus'],
            'September' => ['09', 'September'],
            'Oktober' => ['10', 'Oktober'],
            'November' => ['11', 'November'],
            'Desember' => ['12', 'Desember'],
        ];
    }
}
```

**Langkah 3:** Jalankan test

```bash
php artisan test tests/Unit/Helpers/DateHelperTest.php --testdox
```

---

### Contoh 4: Membuat E2E Test Baru

**Scenario:** Testing perusahaan create lowongan via browser

**Langkah 1:** Buat file test

```bash
touch tests/e2e/perusahaan/perusahaan-create-lowongan.spec.js
```

**Langkah 2:** Tulis test

```javascript
const { test, expect } = require('@playwright/test');
const { loginAsPerusahaan } = require('../utils/auth-helpers');

test.describe('Perusahaan - Create Lowongan', () => {
  
  test.beforeEach(async ({ page }) => {
    // Login sebagai perusahaan
    await loginAsPerusahaan(page);
  });

  test('E2E_PRS_001: Perusahaan dapat membuat lowongan baru', async ({ page }) => {
    // Step 1: Navigate ke halaman create lowongan
    await page.goto('http://127.0.0.1:8000/perusahaan/lowongan/create');
    await expect(page).toHaveTitle(/Buat Lowongan/);

    // Step 2: Isi form lowongan
    await page.fill('input[name="judul"]', 'Software Engineer - Backend');
    await page.fill('textarea[name="deskripsi"]', 'Membutuhkan backend developer dengan pengalaman Node.js');
    await page.fill('input[name="kuota"]', '5');
    await page.selectOption('select[name="jenis_id"]', '1'); // Magang
    await page.fill('input[name="durasi"]', '6'); // 6 bulan
    
    // Step 3: Pilih skills yang dibutuhkan
    await page.check('input[type="checkbox"][value="1"]'); // JavaScript
    await page.check('input[type="checkbox"][value="3"]'); // Node.js
    
    // Step 4: Upload gambar lowongan
    const fileInput = page.locator('input[type="file"][name="gambar"]');
    await fileInput.setInputFiles('tests/e2e/fixtures/lowongan-image.jpg');

    // Step 5: Submit form
    await page.click('button[type="submit"]');

    // Step 6: Verify redirect ke halaman daftar lowongan
    await expect(page).toHaveURL(/\/perusahaan\/lowongan/);
    
    // Step 7: Verify success message
    await expect(page.locator('.alert-success')).toContainText('Lowongan berhasil dibuat');
    
    // Step 8: Verify lowongan muncul di list
    await expect(page.locator('text=Software Engineer - Backend')).toBeVisible();
  });

  test('E2E_PRS_002: Validasi form create lowongan', async ({ page }) => {
    // Navigate ke form
    await page.goto('http://127.0.0.1:8000/perusahaan/lowongan/create');

    // Submit form kosong
    await page.click('button[type="submit"]');

    // Verify validation errors muncul
    await expect(page.locator('.invalid-feedback')).toHaveCount(4); // 4 required fields
    await expect(page.locator('text=Judul harus diisi')).toBeVisible();
    await expect(page.locator('text=Deskripsi harus diisi')).toBeVisible();
  });

  test('E2E_PRS_003: Perusahaan dapat edit lowongan', async ({ page }) => {
    // Asumsi sudah ada lowongan dengan ID 1
    await page.goto('http://127.0.0.1:8000/perusahaan/lowongan/1/edit');

    // Update judul
    await page.fill('input[name="judul"]', 'Software Engineer - Backend (Updated)');
    
    // Update kuota
    await page.fill('input[name="kuota"]', '10');

    // Submit
    await page.click('button[type="submit"]');

    // Verify
    await expect(page.locator('.alert-success')).toContainText('Lowongan berhasil diupdate');
    await expect(page.locator('text=Software Engineer - Backend (Updated)')).toBeVisible();
  });

  test('E2E_PRS_004: Perusahaan dapat delete lowongan', async ({ page }) => {
    await page.goto('http://127.0.0.1:8000/perusahaan/lowongan');

    // Click delete button pada lowongan pertama
    await page.click('button.btn-delete[data-id="1"]');

    // Confirm delete di modal
    page.on('dialog', dialog => dialog.accept());
    await page.click('button.confirm-delete');

    // Verify lowongan hilang dari list
    await expect(page.locator('.alert-success')).toContainText('Lowongan berhasil dihapus');
  });
});
```

**Langkah 3:** Generate auth state untuk perusahaan (jika belum ada)

```javascript
// Update tests/e2e/setup-auth.js untuk tambahkan perusahaan
const roles = [
  // ... existing roles
  {
    name: 'PERUSAHAAN',
    email: process.env.PERUSAHAAN_EMAIL || 'perusahaan1@example.com',
    password: process.env.PERUSAHAAN_PASSWORD || 'secret',
    expectedUrl: '/perusahaan/dashboard',
    outputFile: 'tests/e2e/auth-states/perusahaan.json'
  }
];
```

**Langkah 4:** Jalankan test

```bash
# Regenerate auth states
node tests/e2e/setup-auth.js

# Run test
npx playwright test tests/e2e/perusahaan/perusahaan-create-lowongan.spec.js --headed
```

---

### Best Practices Membuat Test Case

#### 1. Naming Convention

**PHPUnit (API, Feature, Unit):**
```php
// Good
public function test_admin_dapat_create_mahasiswa()
public function test_validation_nim_format_invalid()
public function test_api_login_with_wrong_credentials()

// Avoid
public function testAdminCreateMhs() // Tidak deskriptif
public function test1() // Tidak jelas
```

**Playwright (E2E):**
```javascript
// Good
test('E2E_MHS_001: Mahasiswa dapat melihat daftar lowongan', ...)
test('E2E_ADM_002: Admin dapat import mahasiswa via CSV', ...)

// Avoid
test('test mahasiswa', ...) // Tidak deskriptif
test('check lowongan page', ...) // Tidak ada ID reference
```

#### 2. Test Structure (AAA Pattern)

Gunakan pattern **Arrange-Act-Assert:**

```php
public function test_calculate_ipk_average()
{
    // Arrange: Setup data
    $nilai = [3.5, 3.7, 3.8, 3.6];
    
    // Act: Eksekusi function
    $result = calculate_average($nilai);
    
    // Assert: Verify hasil
    $this->assertEquals(3.65, $result);
}
```

#### 3. Test Independence

Setiap test harus bisa berjalan independent:

```php
// Good: Setiap test create data sendiri
public function test_get_mahasiswa_by_id()
{
    $mahasiswa = Mahasiswa::factory()->create();
    $response = $this->get("/api/mahasiswa/{$mahasiswa->id}");
    $response->assertStatus(200);
}

// Avoid: Bergantung pada test lain
public function test_get_mahasiswa_by_id()
{
    // Asumsi mahasiswa dengan ID 1 sudah ada dari test sebelumnya
    $response = $this->get('/api/mahasiswa/1'); // RISKY!
}
```

#### 4. Test Data Management

Gunakan factories untuk generate test data:

```php
// Good: Gunakan factory
$mahasiswa = Mahasiswa::factory()->create([
    'nim' => '2241760001',
    'nama' => 'Test Mahasiswa'
]);

// Avoid: Hard-coded data
$mahasiswa = Mahasiswa::create([
    'nim' => '2241760001', // Bisa conflict dengan data existing
    'nama' => 'Test Mahasiswa',
    // ... banyak field required lainnya
]);
```

#### 5. Assertions yang Jelas

```php
// Good: Specific assertions
$response->assertStatus(200);
$response->assertJsonStructure(['data' => ['id', 'nama', 'nim']]);
$this->assertDatabaseHas('m_mahasiswa', ['nim' => '2241760001']);

// Avoid: Generic assertions
$this->assertTrue($response->status() == 200); // Kurang jelas
```

---

## Melihat Hasil Testing

### A. PHPUnit Test Results (API, Feature, Unit)

#### Console Output

Setelah menjalankan `php artisan test`, lihat output di terminal:

```bash
PASS  Tests\Api\Auth\LoginApiTest
✓ api login admin berhasil                                     0.45s
✓ api login credentials salah                                  0.12s
✓ api login mahasiswa berhasil                                 0.23s
✓ api login dosen berhasil                                     0.18s
...

PASS  Tests\Feature\Auth\LoginTest
✓ admin dapat login dengan credentials benar                   0.56s
✓ mahasiswa dapat login dengan credentials benar               0.48s
...

PASS  Tests\Unit\Services\ValidationLogicTest
✓ validate nim format valid                                    0.02s
✓ validate ipk range valid                                     0.01s
...

Tests:    86 passed (234 assertions)
Duration: 12.45s
```

#### Interpretasi Hasil

**PASS** = Test berhasil  
**FAIL** = Test gagal  
**SKIPPED** = Test di-skip  

**Assertions** = Jumlah validasi yang di-check  
**Duration** = Waktu eksekusi total

#### Jika Ada Test yang Gagal

```bash
FAIL  Tests\Api\Auth\LoginApiTest
✗ api login admin berhasil

Expected status code 200 but received 500

at tests/Api/Auth/LoginApiTest.php:45
```

**Langkah debugging:**
1. Baca error message
2. Cek line number yang error
3. Review test code di file tersebut
4. Check logs Laravel: `storage/logs/laravel.log`

### B. Playwright HTML Report (E2E)

#### Buka HTML Report

```bash
# Setelah test selesai, buka report
npx playwright show-report
```

Report akan otomatis terbuka di browser (default: http://localhost:9323)

#### Isi HTML Report

Report menampilkan:

**1. Test Summary**
- Total tests passed
- Total tests failed
- Execution duration
- Browser yang digunakan

**2. Test Details (per test case)**
- Test name dan status
- Screenshots (before/after)
- Error messages (jika failed)
- Execution logs

**3. Failed Tests**
- Screenshot saat error terjadi
- Full error stack trace
- DOM snapshot
- Link ke trace viewer

#### Screenshot Example

Setiap test akan menyimpan screenshot di:
```
test-results/
├── admin-login-spec/
│   ├── test-failed-1.png
│   └── trace.zip
└── mahasiswa-apply-spec/
    ├── test-passed-1.png
    └── trace.zip
```

### C. Trace Viewer (untuk Debugging Detail)

#### Buka Trace Viewer

```bash
# Manual: Buka specific trace file
npx playwright show-trace test-results/mahasiswa-apply-spec/trace.zip

# Atau klik link "View Trace" di HTML report
```

#### Fitur Trace Viewer

**1. Timeline**
- Lihat urutan eksekusi test
- Hover untuk melihat snapshot di setiap step

**2. Actions**
- Daftar semua actions (click, fill, navigate)
- Durasi setiap action
- Before/after screenshots

**3. DOM Snapshot**
- Inspect HTML di setiap step
- Find selector yang tepat
- Debug selector issues

**4. Network**
- Semua HTTP requests/responses
- Status codes
- Request/response headers dan body

**5. Console**
- Browser console logs
- JavaScript errors
- Console.log output

**6. Source**
- Test code yang dijalankan
- Highlight line yang sedang dieksekusi

### D. Quick Summary Commands

```bash
# Run test dan auto-open report jika ada failure
npx playwright test --reporter=html

# Run test dengan screenshot untuk ALL tests (passed & failed)
npx playwright test --screenshot=on

# Run test dengan video recording
npx playwright test --video=on

# List all tests tanpa run
npx playwright test --list

# Generate report dari existing test results
npx playwright show-report
```

---

## Troubleshooting

### Problem 1: PHPUnit Tests Failed - Database Error

**Gejala:**
```
SQLSTATE[HY000] [1049] Unknown database: 'jti_intern_testing'
```

**Penyebab:** Database testing belum dibuat

**Solusi:**

```bash
# Cek file .env.testing
cat .env.testing  # Linux/Mac
Get-Content .env.testing  # Windows

# Pastikan DB_CONNECTION=sqlite dan DB_DATABASE=:memory:
# ATAU buat database MySQL jika pakai MySQL
mysql -u root -p
CREATE DATABASE jti_intern_testing;
exit;

# Run migration
php artisan migrate --env=testing
```

---

### Problem 2: E2E Tests Failed - Server Not Running

**Gejala:**
```
Error: page.goto: net::ERR_CONNECTION_REFUSED at http://127.0.0.1:8000
```

**Penyebab:** Laravel server tidak running

**Solusi:**

```bash
# Check apakah server running
curl http://127.0.0.1:8000/login  # Linux/Mac
Invoke-WebRequest http://127.0.0.1:8000/login  # Windows

# Jika tidak ada response, start server
php artisan serve --host=127.0.0.1 --port=8000

# Tunggu 10 detik, lalu coba lagi
```

---

### Problem 3: E2E Tests Failed - Authentication Failed

**Gejala:**
```
Error: Authentication failed for ADMIN
Login button not found
```

**Penyebab:** Auth states expired atau credentials salah

**Solusi:**

```bash
# 1. Verify credentials di database
php artisan tinker
>>> User::where('email', 'admin@example.com')->first()
>>> exit

# 2. Update .env.playwright jika credentials berbeda

# 3. Regenerate auth states
node tests\e2e\setup-auth.js

# 4. Coba run test lagi
npx playwright test --project=admin
```

---

### Problem 4: E2E Tests Timeout

**Gejala:**
```
TimeoutError: page.waitForSelector: Timeout 30000ms exceeded
Selector: 'button[type="submit"]'
```

**Penyebab:** Element tidak ditemukan atau load terlalu lama

**Solusi:**

```bash
# 1. Debug dengan headed mode
npx playwright test --headed -g "test-name"

# 2. Gunakan Playwright Codegen untuk find selector yang benar
npx playwright codegen http://127.0.0.1:8000/mahasiswa/dashboard

# 3. Update selector di test file
# Misalnya ganti:
# await page.click('button[type="submit"]')
# Dengan:
# await page.click('[data-testid="submit-btn"]')

# 4. Atau tambah timeout lebih lama di test
# test.setTimeout(90000); // 90 seconds
```

---

### Problem 5: E2E Tests Failed - Selector Not Found

**Gejala:**
```
Error: Selector 'button.btn-primary' not found
```

**Penyebab:** HTML structure berubah atau selector salah

**Solusi:**

```bash
# 1. Inspect dengan Playwright Inspector
npx playwright test --debug -g "test-name"

# 2. Atau gunakan codegen
npx playwright codegen http://127.0.0.1:8000/target-page

# 3. Copy selector yang benar dari Playwright Inspector

# 4. Update di test file:
# Before: await page.click('button.btn-primary')
# After:  await page.click('button:has-text("Submit")')
```

---

### Problem 6: E2E Tests Failed - Database Empty

**Gejala:**
```
Error: No lowongan found
Expected at least 1 lowongan card
```

**Penyebab:** Database tidak ada data untuk testing

**Solusi:**

```bash
# Seed database production (untuk E2E testing)
php artisan db:seed --class=LowonganSeeder
php artisan db:seed --class=PerusahaanSeeder
php artisan db:seed --class=PeriodeSeeder

# Verify data ada
php artisan tinker
>>> Lowongan::count()
>>> exit

# Run test lagi
npx playwright test
```

---

### Problem 7: Tests Running Very Slow

**Gejala:**
- PHPUnit tests takes > 30 seconds
- E2E tests takes > 5 minutes

**Solusi:**

```bash
# PHPUnit: Gunakan parallel execution
php artisan test --parallel

# PHPUnit: Gunakan SQLite in-memory
# Edit .env.testing:
# DB_CONNECTION=sqlite
# DB_DATABASE=:memory:

# E2E: Run specific test instead of all
npx playwright test --project=mahasiswa

# E2E: Reduce workers
npx playwright test --workers=2
```

---

### Problem 8: Port 8000 Already in Use

**Gejala:**
```
Address already in use: 127.0.0.1:8000
```

**Penyebab:** Ada process lain menggunakan port 8000

**Solusi:**

```bash
# Windows: Kill process di port 8000
netstat -ano | findstr :8000
taskkill /F /PID [PID_NUMBER]

# Linux/Mac: Kill process di port 8000
lsof -i :8000
kill -9 [PID_NUMBER]

# Atau gunakan port lain
php artisan serve --host=127.0.0.1 --port=8001

# Update BASE_URL di .env.playwright
# BASE_URL=http://127.0.0.1:8001
```

---

### Problem 9: Playwright Browsers Not Installed

**Gejala:**
```
Error: Executable doesn't exist at ...
```

**Penyebab:** Playwright browsers belum di-install

**Solusi:**

```bash
# Install semua browsers
npx playwright install

# Atau install specific browser
npx playwright install chromium
npx playwright install firefox
npx playwright install webkit

# Verify installation
npx playwright --version
```

---

### Problem 10: Permission Denied (Linux/Mac)

**Gejala:**
```
EACCES: permission denied
```

**Solusi:**

```bash
# Fix permissions
chmod +x vendor/bin/phpunit
chmod -R 777 storage
chmod -R 777 bootstrap/cache

# Atau run dengan sudo (not recommended)
sudo php artisan test
```

---

## Struktur Test Cases

### Admin Tests

File: `tests/e2e/admin/mahasiswa.spec.js`

1. **E2E_ADM_002**: Menambahkan data mahasiswa baru
2. **E2E_ADM_003**: Import data mahasiswa via CSV
3. **E2E_ADM_UPDATE**: Update data mahasiswa existing
4. **E2E_ADM_DELETE**: Hapus data mahasiswa
5. **E2E_ADM_VIEW**: Verifikasi daftar mahasiswa tampil

### Mahasiswa Tests

File: `tests/e2e/mahasiswa/lowongan.spec.js`

1. **E2E_MHS_002**: Melengkapi profil mahasiswa (Skills, Minat, CV)
2. **E2E_MHS_003**: Melihat daftar lowongan dan rekomendasi SPK
3. **E2E_MHS_004**: Apply lowongan dan tracking status lamaran
4. **E2E_MHS_005**: Mengisi logbook magang harian

### Dosen Tests

File: `tests/e2e/dosen/monitoring.spec.js`

1. **E2E_DSN_001**: Verifikasi dashboard dosen
2. **E2E_DSN_002**: Melihat daftar mahasiswa bimbingan
3. **E2E_DSN_003**: Melakukan evaluasi mahasiswa
4. **E2E_DSN_004**: Update profil dosen
5. **E2E_DSN_005**: Monitoring logbook mahasiswa

### Multi-Role Tests

File: `tests/e2e/multi-role/apply-approve.spec.js`

1. **E2E_MULTI_001**: Admin create mahasiswa, mahasiswa login pertama kali
2. **E2E_MULTI_004**: Mahasiswa apply, admin approve, mahasiswa verifikasi

---

## Alur Lengkap Testing (Step-by-Step)

### Langkah 1: Persiapan Environment

```bash
# 1. Install dependencies
npm install

# 2. Setup database
php artisan migrate
php artisan db:seed

# 3. Verify .env.playwright
cat .env.playwright
```

### Langkah 2: Start Server

```bash
# Terminal 1: Start Laravel server
php artisan serve --host=127.0.0.1 --port=8000
```

### Langkah 3: Generate Auth States

```bash
# Terminal 2: Generate authentication
node tests\setup-auth.js
```

Pastikan output menunjukkan semua role berhasil (admin, mahasiswa, dosen).

### Langkah 4: Jalankan Tests

```bash
# Run semua tests
npx playwright test

# Atau run by role
npx playwright test --project=mahasiswa
```

### Langkah 5: Lihat Hasil

```bash
# Buka HTML report
npx playwright show-report

# Jika ada test gagal, lihat trace
npx playwright show-trace test-results/[folder]/trace.zip
```

### Langkah 6: Fix Failed Tests (jika ada)

1. Lihat error message di console
2. Buka trace viewer untuk detail eksekusi
3. Inspect HTML dengan codegen jika selector issue
4. Update test file
5. Re-run test yang gagal

---

## Configuration Files

### playwright.config.js

File konfigurasi utama Playwright yang mendefinisikan:
- Test directory: `./tests/e2e`
- Test match pattern: `**/*.spec.js`
- 3 projects: admin, mahasiswa, dosen
- Timeout settings
- Reporter configuration

### .env.playwright

Environment variables untuk testing:
- BASE_URL: URL aplikasi
- Credentials untuk setiap role

### tests/e2e/setup-auth.js

Script untuk generate authentication states. Melakukan:
1. Launch browser (headless)
2. Navigate ke /login
3. Fill credentials
4. Submit form
5. Wait for redirect
6. Save storageState ke JSON file

---

## Best Practices

### 1. Selalu Regenerate Auth States

Jika password berubah atau session expired, jalankan:
```bash
node tests\setup-auth.js
```

### 2. Pastikan Server Running

Sebelum run tests, verify server:
```bash
# Test dengan curl
curl http://127.0.0.1:8000/login
```

### 3. Clean Test Results Berkala

```bash
# Hapus old test results
Remove-Item test-results -Recurse -Force
Remove-Item playwright-report -Recurse -Force
```

### 4. Use Specific Selectors

Gunakan selector yang spesifik untuk menghindari false positive:
```javascript
// Good
await page.locator('[data-testid="submit-button"]').click();

// Avoid
await page.locator('button').first().click();
```

### 5. Add Explicit Waits

Tambahkan wait untuk element yang load dynamically:
```javascript
await page.waitForSelector('[data-testid="lowongan-card"]', { 
  timeout: 10000 
});
```

---

## Maintenance

### Update Test Selectors

Jika UI berubah, update selectors:

1. Jalankan codegen:
```bash
npx playwright codegen http://127.0.0.1:8000
```

2. Copy selector yang benar dari Playwright Inspector

3. Update di test file

### Add New Tests

1. Buat file baru di folder yang sesuai (admin/mahasiswa/dosen)
2. Import test utilities:
```javascript
const { test, expect } = require('@playwright/test');
```

3. Gunakan storageState dari project yang sesuai (otomatis)

4. Tulis test cases

5. Jalankan:
```bash
npx playwright test path/to/new-test.spec.js
```

---

## Test Credentials

| Role | Email | Password | Dashboard URL |
|------|-------|----------|---------------|
| Admin | admin@example.com | secret | /dashboard |
| Mahasiswa | mahasiswa1@example.com | secret | /mahasiswa/dashboard |
| Dosen | dosen1@example.com | secret | /dosen/dashboard |

---

## Quick Reference Commands

```bash
# List all tests
npx playwright test --list

# Run specific test
npx playwright test -g "E2E_MHS_003"

# Run with headed browser
npx playwright test --headed

# Debug mode
npx playwright test --debug

# Show report
npx playwright show-report

# Generate auth
node tests\setup-auth.js

# Start server
php artisan serve --host=127.0.0.1 --port=8000
```

---
