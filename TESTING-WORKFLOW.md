# Testing Workflow Guide - E2E JTI Intern PMPL

## Table of Contents
- [Overview](#overview)
- [Quick Start](#quick-start)
- [Testing Workflow](#testing-workflow)
- [For New Team Members](#for-new-team-members)
- [Report Output](#report-output)
- [Writing Tests](#writing-tests)
- [Test Statistics](#test-statistics)
- [Troubleshooting](#troubleshooting)

---

## Overview

Project ini menggunakan **PHPUnit** dengan **Laravel Testing Framework** untuk automated testing.

### Test Suite Structure:
```
tests/
├── Unit/                    # Test untuk logika bisnis individual
│   ├── Helpers/            # Helper functions
│   ├── Models/             # Model tests
│   └── Services/           # Service layer tests
├── Feature/                # Test untuk fitur end-to-end
│   ├── Admin/              # Admin features
│   └── Auth/               # Authentication
├── Api/                    # API endpoint tests
└── reports/                # Generated test reports
    ├── testdox.html       # Visual HTML report
    ├── junit.xml          # CI/CD compatible XML
    └── testdox.txt        # Plain text summary
```

**Current Status:** 57/57 tests passing (100%)

---

## Quick Start

### Step 1: Setup Environment

```powershell
# 1. Copy environment file
cp .env.example .env.testing

# 2. Configure database untuk testing
# Edit .env.testing:
DB_CONNECTION=mysql
DB_DATABASE=testing_jti_intern
DB_USERNAME=root
DB_PASSWORD=

# 3. Install dependencies
composer install

# 4. Generate key
php artisan key:generate

# 5. Run migrations
php artisan migrate --env=testing

# 6. Seed test data (optional)
php artisan db:seed --env=testing
```

### Step 2: Run Tests

**Option 1: Script dengan Visualisasi (Recommended)**
```powershell
.\run-tests.ps1
```

**Option 2: Manual Command**
```powershell
php artisan test --testsuite=Unit,Feature --exclude-group=api
```

---

## Testing Workflow

### 1. Development Workflow

```
Write Code → Write Test → Run Test → Pass? 
                              ↓ No    ↓ Yes
                          Fix Code → Commit → Push
```

**Step-by-step:**

1. **Sebelum coding:**
   ```powershell
   # Pull latest changes
   git pull origin main
   
   # Run existing tests
   .\run-tests.ps1
   ```

2. **Saat development:**
   ```powershell
   # Run specific test file
   php artisan test tests/Unit/Models/MahasiswaModelTest.php
   
   # Run dengan filter
   php artisan test --filter="mahasiswa_berhasil"
   ```

3. **Sebelum commit:**
   ```powershell
   # Run semua test
   .\run-tests.ps1
   
   # Pastikan 100% pass
   git add .
   git commit -m "feat: add new feature with tests"
   git push origin feature-branch
   ```

### 2. CI/CD Workflow

```yaml
# .github/workflows/test.yml example
name: Run Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test --testsuite=Unit,Feature --exclude-group=api
```

### 3. Code Review Workflow

**Reviewer Checklist:**
- [ ] Semua tests passing
- [ ] Test coverage adequate (min 80%)
- [ ] No skipped tests without reason
- [ ] Test names descriptive
- [ ] No commented test code

---

## For New Team Members

### Scenario 1: Developer Baru Join Project

**Pertama kali setup:**

```powershell
# 1. Clone repository
git clone https://github.com/afadlih/E2E-JTIintern-PMPL.git
cd E2E-JTIintern-PMPL

# 2. Install dependencies
composer install
npm install

# 3. Setup database testing
# Buat database baru: testing_jti_intern
# Edit .env.testing dengan credentials database

# 4. Run migrations
php artisan migrate --env=testing

# 5. Test apakah setup berhasil
.\run-tests.ps1
```

**Expected Output:**
```
========================================
  RUNNING LARAVEL TESTS
========================================

Started at: 2025-11-25 14:06:06

✓ All tests passed!

Generated Reports:
   HTML Report  : tests/reports/testdox.html
   XML Report   : tests/reports/junit.xml
   Text Summary : tests/reports/testdox.txt

TOTAL: 58 tests, 57 passed, 1 skipped
```

### Scenario 2: QA/Tester Menjalankan Test

**Quick Test Run:**

```powershell
# Jalankan semua test
.\run-tests.ps1

# Akan muncul prompt:
# Open HTML report in browser? (Y/N): Y

# Browser akan otomatis buka report visual
```

**Manual Review:**
1. Buka `tests/reports/testdox.html` di browser
2. Lihat checklist hijau (✓) dan merah (✗)
3. Check detail di `tests/reports/testdox.txt`

### Scenario 3: Team Lain Ingin Testing Fitur Tertentu

#### Example: Testing Fitur Mahasiswa

```powershell
# Test fitur mahasiswa saja
php artisan test tests/Feature/Admin/MahasiswaCRUDTest.php

# Output:
#   PASS  Tests\Feature\Admin\MahasiswaCRUDTest
#   ✓ admin dapat melihat daftar mahasiswa
#   ✓ admin berhasil menambah mahasiswa baru
#   ✓ admin tidak bisa menambah mahasiswa nim duplikat
#   ✓ admin berhasil update data mahasiswa
#   ✓ admin berhasil menghapus mahasiswa
#   ... (10 tests total)
```

#### Example: Testing Authentication

```powershell
# Test fitur login/auth
php artisan test tests/Feature/Auth/LoginTest.php

# Output:
#   PASS  Tests\Feature\Auth\LoginTest
#   ✓ admin dapat login dengan credentials benar
#   ✓ login gagal dengan password salah
#   ✓ mahasiswa redirect ke mahasiswa dashboard
#   ... (8 tests total)
```

#### Example: Testing Specific Method

```powershell
# Test method tertentu
php artisan test --filter="test_mahasiswa_berhasil_apply_lowongan"

# Atau test dengan kata kunci
php artisan test --filter="apply_lowongan"
```

### Scenario 4: External Team Testing (e.g., UAT Team)

**Preparation:**

1. **Generate comprehensive report:**
```powershell
# Run dengan coverage (requires Xdebug)
php artisan test --coverage --coverage-html=tests/reports/coverage

# Generate all reports
.\run-tests.ps1
```

2. **Share reports folder:**
```powershell
# Zip reports folder
Compress-Archive -Path tests/reports -DestinationPath test-reports.zip

# Share via email/drive
# Team eksternal bisa buka testdox.html tanpa install apapun
```

3. **Access report:**
   - Open `testdox.html` di browser
   - Tidak perlu PHP/Laravel installed
   - Lihat visual checklist semua test cases

---

## Report Output

### 1. HTML Report (testdox.html)

**Visual & Interactive:**
- Green checkmarks untuk passed tests
- Red cross untuk failed tests
- Grouping by test suite
- Color-coded by status

**Best for:**
- Presentasi ke stakeholder
- Quick visual overview
- Sharing dengan non-technical team

**How to open:**
```powershell
start tests/reports/testdox.html
```

### 2. JUnit XML (junit.xml)

**Machine-readable format:**
```xml
<testsuites>
  <testsuite name="MahasiswaCRUDTest" tests="10" failures="0">
    <testcase name="admin_dapat_melihat_daftar_mahasiswa" time="0.16"/>
    <testcase name="admin_berhasil_menambah_mahasiswa_baru" time="0.11"/>
    ...
  </testsuite>
</testsuites>
```

**Best for:**
- CI/CD integration (Jenkins, GitLab CI, GitHub Actions)
- Automated reporting
- Metric tracking

### 3. Text Summary (testdox.txt)

**Plain text checklist:**
```
Mahasiswa CRUD (Tests\Feature\Admin\MahasiswaCRUD)
 [x] Admin dapat melihat daftar mahasiswa
 [x] Admin berhasil menambah mahasiswa baru
 [x] Admin tidak bisa menambah mahasiswa nim duplikat
 ...
```

**Best for:**
- Command-line review
- Quick scan di terminal
- Documentation

---

## Writing Tests

### Test Naming Convention

```php
// Good: Descriptive, clear intent
public function test_admin_berhasil_menambah_mahasiswa_baru()
public function test_mahasiswa_tidak_bisa_apply_lowongan_sama_dua_kali()

// Bad: Unclear
public function test1()
public function testMahasiswa()
```

### Unit Test Template

```php
<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Mahasiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MahasiswaModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Case: Mahasiswa dapat menyimpan data
     * 
     * Scenario:
     * - Given: Data mahasiswa valid
     * - When: Menyimpan data ke database
     * - Then: Data tersimpan dengan benar
     */
    public function test_mahasiswa_dapat_menyimpan_data()
    {
        // Arrange (Setup)
        $data = [
            'nim' => '2141720001',
            'nama' => 'Test User',
            'ipk' => 3.5,
        ];
        
        // Act (Execute)
        $mahasiswa = Mahasiswa::create($data);
        
        // Assert (Verify)
        $this->assertNotNull($mahasiswa->id_mahasiswa);
        $this->assertDatabaseHas('m_mahasiswa', [
            'nim' => '2141720001',
            'nama' => 'Test User',
        ]);
    }
}
```

### Feature Test Template

```php
<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Mahasiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MahasiswaCRUDTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Case: Admin dapat menambah mahasiswa baru
     * 
     * Scenario:
     * - Given: User login sebagai admin
     * - When: Submit form tambah mahasiswa dengan data valid
     * - Then: Mahasiswa baru tersimpan di database
     * - And: Response sukses dengan status 201
     */
    public function test_admin_berhasil_menambah_mahasiswa_baru()
    {
        // Arrange
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        
        $mahasiswaData = [
            'nim' => '2141720099',
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'id_kelas' => 1,
            'ipk' => 3.5,
        ];
        
        // Act
        $response = $this->postJson('/api/admin/mahasiswa', $mahasiswaData);
        
        // Assert
        $response->assertStatus(201);
        $response->assertJson(['status' => 'success']);
        $this->assertDatabaseHas('m_mahasiswa', [
            'nim' => '2141720099',
        ]);
    }
}
```

### Test Groups

```php
/**
 * Mark test sebagai API test (akan di-skip pada run standar)
 * @group api
 */
class ApiTest extends TestCase { }

/**
 * Mark test sebagai slow (untuk selective run)
 * @group slow
 */
public function test_heavy_operation() { }

/**
 * Mark test sebagai integration test
 * @group integration
 */
public function test_external_service() { }
```

**Run specific group:**
```powershell
# Run only API tests
php artisan test --group=api

# Exclude slow tests
php artisan test --exclude-group=slow
```

---

## Test Statistics

### Current Coverage (November 25, 2025)

| Metric | Count | Status |
|--------|-------|--------|
| **Total Tests** | 58 | PASS |
| **Passed** | 57 | 98.3% |
| **Failed** | 0 | PASS |
| **Skipped** | 1 | WARNING |
| **Avg Duration** | 22.5s | FAST |

### Breakdown by Suite

**Unit Tests (30 tests - 100% pass):**
```
[PASS] HelperFunctionsTest       : 10/10 (0.08s)
[PASS] MahasiswaModelTest        : 10/10 (15.81s)
[PASS] ValidationLogicTest       : 10/10 (0.01s)
[WARN] SPKRecommendationService  : 9/10 (2.24s) - 1 skipped
```

**Feature Tests (27 tests - 100% pass):**
```
[PASS] MahasiswaCRUDTest         : 10/10 (0.73s)
[PASS] LoginTest                 : 8/8 (0.54s)
[SKIP] ApplyLowonganTest         : 0/8 (skipped - @group api)
```

### Test Performance

| Test Suite | Duration | Status |
|------------|----------|--------|
| Fast (< 1s) | 35 tests | Excellent |
| Medium (1-5s) | 20 tests | Good |
| Slow (> 5s) | 3 tests | Consider optimization |

---

## Advanced Usage

### Test Coverage Report

```powershell
# Generate HTML coverage (requires Xdebug)
php artisan test --coverage-html=tests/reports/coverage

# Generate text coverage
php artisan test --coverage

# Set minimum coverage threshold
php artisan test --min=80
```

**Enable Xdebug in php.ini:**
```ini
[Xdebug]
zend_extension=xdebug
xdebug.mode=coverage
```

### Parallel Testing

```powershell
# Run tests in parallel (faster)
php artisan test --parallel

# Specify number of processes
php artisan test --parallel --processes=4
```

### Database Management

```powershell
# Fresh database before each test
# Already configured with RefreshDatabase trait

# Manual refresh
php artisan migrate:fresh --env=testing

# Seed test data
php artisan db:seed --env=testing --class=TestDataSeeder
```

---

## Troubleshooting

### Issue 1: "Database not found"

**Problem:** Test gagal dengan error database tidak ditemukan

**Solution:**
```powershell
# 1. Pastikan database testing ada
# Buat database: testing_jti_intern

# 2. Check .env.testing
DB_CONNECTION=mysql
DB_DATABASE=testing_jti_intern
DB_HOST=127.0.0.1
DB_PORT=3306

# 3. Run migrations
php artisan migrate --env=testing

# 4. Test connection
php artisan migrate:status --env=testing
```

### Issue 2: "Class not found"

**Problem:** Error "Class 'App\Models\Mahasiswa' not found"

**Solution:**
```powershell
# Rebuild autoload
composer dump-autoload

# Clear cache
php artisan clear-compiled
php artisan cache:clear
```

### Issue 3: Tests running slow

**Problem:** Test suite takes too long (> 60s)

**Solution:**
```powershell
# 1. Use parallel testing
php artisan test --parallel

# 2. Run specific suite
php artisan test --testsuite=Unit  # Faster

# 3. Disable seeders in RefreshDatabase
# Use factories instead of seeders

# 4. Check for N+1 queries in tests
```

### Issue 4: Report tidak ter-generate

**Problem:** File testdox.html tidak ada

**Solution:**
```powershell
# 1. Create reports directory
New-Item -ItemType Directory -Path tests/reports -Force

# 2. Check phpunit.xml configuration
# Pastikan <logging> section ada

# 3. Check permissions (Linux/Mac)
chmod -R 755 tests/reports

# 4. Run dengan explicit output
php artisan test --testdox-html=tests/reports/testdox.html
```

### Issue 5: "Skipped tests"

**Problem:** Test ter-skip tanpa alasan jelas

**Solution:**
```php
// Check for:
// 1. @group api annotation
// 2. $this->markTestSkipped()
// 3. Missing dependencies (mock objects)

// To see skip reasons:
php artisan test --testsuite=Unit,Feature --exclude-group=api --verbose
```

---

## Resources & References

### Documentation
- [Laravel Testing Docs](https://laravel.com/docs/10.x/testing)
- [PHPUnit Manual](https://phpunit.de/documentation.html)
- [Laravel HTTP Tests](https://laravel.com/docs/10.x/http-tests)
- [Database Testing](https://laravel.com/docs/10.x/database-testing)

### Best Practices
- Test naming: Use descriptive method names in Indonesian or English
- Test structure: Follow AAA pattern (Arrange, Act, Assert)
- Test isolation: Use RefreshDatabase trait
- Test data: Use factories instead of manual data creation
- Test coverage: Aim for 80%+ coverage

### Tools
- PHPUnit: Testing framework
- Mockery: Mocking framework
- Laravel Telescope: Debug helper (disabled in testing)
- Xdebug: Code coverage tool

---

## Support & Contribution

### Getting Help

**Internal Team:**
- Slack: #testing-support
- Email: dev-team@jti-intern.ac.id

**Issues:**
- GitHub Issues: [Report Bug](https://github.com/afadlih/E2E-JTIintern-PMPL/issues)
- Documentation: [Wiki](https://github.com/afadlih/E2E-JTIintern-PMPL/wiki)

### Contributing Tests

1. Fork repository
2. Create feature branch: `git checkout -b test/new-feature`
3. Write tests following conventions
4. Ensure all tests pass: `.\run-tests.ps1`
5. Submit Pull Request

**Pull Request Checklist:**
- [ ] All tests passing (100%)
- [ ] New features have test coverage
- [ ] Test names are descriptive
- [ ] No console warnings
- [ ] Documentation updated if needed

---

## Quick Reference

### Common Commands

```powershell
# Run all tests
.\run-tests.ps1

# Run specific suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Run specific file
php artisan test tests/Unit/Models/MahasiswaModelTest.php

# Run with filter
php artisan test --filter="mahasiswa"

# Run excluding group
php artisan test --exclude-group=api

# Generate coverage
php artisan test --coverage

# Parallel execution
php artisan test --parallel

# Stop on failure
php artisan test --stop-on-failure

# Verbose output
php artisan test --verbose
```

### Test Lifecycle

```php
// Run once before all tests in class
public static function setUpBeforeClass(): void

// Run before each test
protected function setUp(): void

// Run after each test
protected function tearDown(): void

// Run once after all tests in class
public static function tearDownAfterClass(): void
```

---

---

## Jenis-Jenis Testing

Project ini menggunakan 4 jenis testing untuk memastikan kualitas aplikasi:

### 1. API Testing
Testing REST API endpoints dengan JSON request/response menggunakan Laravel Sanctum authentication.

**Fokus:**
- JSON request/response
- Bearer Token authentication (Sanctum)
- HTTP status codes (200, 201, 400, 401, 403, 422)
- Response structure validation
- Authorization dan permissions

**Test Coverage:**
- `tests/Api/Auth/LoginApiTest.php` - Login dengan berbagai role
- `tests/Api/Mahasiswa/MahasiswaLowonganApiTest.php` - Get lowongan, apply lowongan
- `tests/Api/Admin/AdminMahasiswaApiTest.php` - CRUD mahasiswa via API

**Cara Menjalankan:**
```powershell
# Run semua API tests
php artisan test tests/Api

# Run dengan output readable
php artisan test tests/Api --testdox

# Run specific file
php artisan test tests/Api/Auth/LoginApiTest.php

# Run specific method
php artisan test --filter test_api_login_admin_berhasil
```

### 2. Feature Testing
Testing full application workflow dengan database dan HTTP menggunakan session authentication.

**Fokus:**
- Database interactions (CRUD operations)
- Session-based authentication
- HTTP redirects (302 status codes)
- Form submissions dengan CSRF
- Integration antar komponen (Controller - Model - Database)

**Test Coverage:**
- `tests/Feature/Auth/LoginTest.php` - Login flow dengan session
- `tests/Feature/Mahasiswa/ApplyLowonganTest.php` - Workflow apply lowongan
- `tests/Feature/Admin/MahasiswaCRUDTest.php` - Admin CRUD operations

**Cara Menjalankan:**
```powershell
# Run semua Feature tests
php artisan test tests/Feature

# Run dengan stop on failure
php artisan test tests/Feature --stop-on-failure

# Run specific file
php artisan test tests/Feature/Auth/LoginTest.php
```

### 3. Unit Testing
Testing pure logic functions tanpa dependency eksternal (no database, no HTTP).

**Fokus:**
- Pure functions (input - output)
- Calculations dan validations
- String manipulations
- Business logic tanpa database
- Execution sangat cepat (< 1 detik)

**Test Coverage:**
- `tests/Unit/Services/ValidationLogicTest.php` - Validate NIM, IPK
- `tests/Unit/Models/MahasiswaModelTest.php` - Model business logic
- `tests/Unit/Helpers/HelperFunctionsTest.php` - Helper functions

**Cara Menjalankan:**
```powershell
# Run semua Unit tests
php artisan test tests/Unit

# Run dengan coverage (requires Xdebug)
php artisan test tests/Unit --coverage

# Run specific file
php artisan test tests/Unit/Services/ValidationLogicTest.php
```

### 4. E2E Testing (End-to-End)
Testing aplikasi secara keseluruhan menggunakan real browser automation dengan Playwright.

**Fokus:**
- Real browser automation (Chromium, Firefox, WebKit)
- Click buttons, fill forms, navigate pages
- Screenshot dan video recording
- Multi-role authentication (admin, mahasiswa, dosen)
- Full user workflow dari awal sampai akhir

**Test Coverage:**
- `tests/e2e/admin/` - Admin workflows
- `tests/e2e/mahasiswa/` - Mahasiswa workflows
- `tests/e2e/dosen/` - Dosen workflows
- `tests/e2e/multi-role/` - Multi-role interactions

**Cara Menjalankan:**
```powershell
# Prerequisites: Generate auth states
node tests\e2e\setup-auth.js

# Run semua E2E tests
npx playwright test

# Run by role
npx playwright test --project=mahasiswa

# Run with visible browser
npx playwright test --headed

# Debug mode
npx playwright test --debug
```

---

## E2E Testing Setup

### Prerequisites

#### 1. Install Dependencies

**Node.js Dependencies:**
```powershell
# Install semua dependencies
npm install

# Install Playwright browsers
npx playwright install

# Verifikasi installation
npx playwright --version
```

**Expected output:** Version 1.x

#### 2. Setup Environment Variables

**Buat file .env.playwright:**
```powershell
# Windows
New-Item -Path .env.playwright -ItemType File
```

**Isi .env.playwright:**
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

**PENTING:** Pastikan credentials ini sesuai dengan data di database!

#### 3. Start Laravel Server

```powershell
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

#### 4. Generate Authentication States

**Wajib dilakukan sebelum run E2E tests:**

```powershell
# Generate auth states untuk semua role
node tests\e2e\setup-auth.js
```

**Expected Output:**
```
Starting Multi-Role Authentication Setup...
Base URL: http://127.0.0.1:8000

Setting up authentication for: ADMIN
   -> Navigating to http://127.0.0.1:8000/login
   -> Login successful! Current URL: http://127.0.0.1:8000/dashboard
   Success: Storage state saved to: tests\e2e\auth-states\admin.json

Setting up authentication for: MAHASISWA
   -> Navigating to http://127.0.0.1:8000/login
   -> Login successful! Current URL: http://127.0.0.1:8000/mahasiswa/dashboard
   Success: Storage state saved to: tests\e2e\auth-states\mahasiswa.json

Setting up authentication for: DOSEN
   -> Navigating to http://127.0.0.1:8000/login
   -> Login successful! Current URL: http://127.0.0.1:8000/dosen/dashboard
   Success: Storage state saved to: tests\e2e\auth-states\dosen.json

AUTHENTICATION SETUP SUMMARY
Success: admin, mahasiswa, dosen
Failed: None

All authentication setups completed successfully!
```

**File yang Dihasilkan:**
```
tests/e2e/auth-states/
├── admin.json       <- Session cookies untuk admin
├── mahasiswa.json   <- Session cookies untuk mahasiswa
└── dosen.json       <- Session cookies untuk dosen
```

**Kapan perlu regenerate?**
- Password user berubah
- Session expired (setelah logout)
- Error "Authentication failed"

### E2E Testing Commands

**Run semua tests:**
```powershell
npx playwright test
```

**Run by role:**
```powershell
# Run hanya tests admin
npx playwright test --project=admin

# Run hanya tests mahasiswa
npx playwright test --project=mahasiswa

# Run hanya tests dosen
npx playwright test --project=dosen
```

**Run specific test:**
```powershell
# Run test file tertentu
npx playwright test tests/e2e/mahasiswa/mahasiswa-apply-lowongan.spec.js

# Run test dengan ID tertentu
npx playwright test -g "E2E_MHS_003"

# Run test yang mengandung kata "lowongan"
npx playwright test -g "lowongan"
```

**Debug mode:**
```powershell
# Run dengan browser visible
npx playwright test --headed

# Run dengan debug inspector
npx playwright test --debug

# Run dengan UI mode (interactive)
npx playwright test --ui
```

**View results:**
```powershell
# Open HTML report
npx playwright show-report

# Open specific trace file
npx playwright show-trace test-results/[folder]/trace.zip
```

### E2E Test Coverage

#### Admin Tests (`tests/e2e/admin/`)
- **E2E_ADM_001**: Admin dapat login
- **E2E_ADM_002**: Menambahkan data mahasiswa baru
- **E2E_ADM_003**: Import data mahasiswa via CSV
- **E2E_ADM_UPDATE**: Update data mahasiswa existing
- **E2E_ADM_DELETE**: Hapus data mahasiswa
- **E2E_ADM_VIEW**: Verifikasi daftar mahasiswa tampil

#### Mahasiswa Tests (`tests/e2e/mahasiswa/`)
- **E2E_MHS_001**: Mahasiswa dapat login
- **E2E_MHS_002**: Melengkapi profil mahasiswa (Skills, Minat, CV)
- **E2E_MHS_003**: Melihat daftar lowongan dan rekomendasi SPK
- **E2E_MHS_004**: Apply lowongan dan tracking status lamaran
- **E2E_MHS_005**: Mengisi logbook magang harian

#### Dosen Tests (`tests/e2e/dosen/`)
- **E2E_DSN_001**: Verifikasi dashboard dosen
- **E2E_DSN_002**: Melihat daftar mahasiswa bimbingan
- **E2E_DSN_003**: Melakukan evaluasi mahasiswa
- **E2E_DSN_004**: Update profil dosen
- **E2E_DSN_005**: Monitoring logbook mahasiswa

#### Multi-Role Tests (`tests/e2e/multi-role/`)
- **E2E_MULTI_001**: Admin create mahasiswa, mahasiswa login pertama kali
- **E2E_MULTI_004**: Mahasiswa apply, admin approve, mahasiswa verifikasi

---

## Membuat Test Case Baru

### Panduan Umum

Sebelum membuat test case, identifikasi jenis testing yang sesuai:

| Jenis Testing | Kapan Digunakan | Tool |
|---------------|-----------------|------|
| **API Testing** | Testing REST API endpoints dengan JSON response | PHPUnit |
| **Feature Testing** | Testing full workflow aplikasi web (form, redirect, session) | PHPUnit |
| **Unit Testing** | Testing fungsi/method isolated tanpa dependencies | PHPUnit |
| **E2E Testing** | Testing user interaction di browser (klik, isi form, navigate) | Playwright |

### Contoh: Membuat API Test Baru

**Scenario:** Testing API endpoint untuk get daftar perusahaan

**Step 1: Buat file test**
```powershell
New-Item -Path tests/Api/Admin/AdminPerusahaanApiTest.php -ItemType File
```

**Step 2: Tulis test**
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
        
        $this->admin = User::factory()->create([
            'username' => 'admin_test',
            'role' => 'Admin'
        ]);
        
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
        // Arrange
        $data = [
            'nama' => 'PT Test Perusahaan',
            'alamat' => 'Jl. Test No. 123',
            'kontak' => '08123456789',
            'email' => 'test@perusahaan.com'
        ];
        
        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson('/api/admin/perusahaan', $data);
        
        // Assert
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
}
```

**Step 3: Run test**
```powershell
php artisan test tests/Api/Admin/AdminPerusahaanApiTest.php --testdox
```

### Contoh: Membuat E2E Test Baru

**Scenario:** Testing perusahaan create lowongan via browser

**Step 1: Buat file test**
```powershell
New-Item -Path tests/e2e/perusahaan/perusahaan-create-lowongan.spec.js -ItemType File
```

**Step 2: Tulis test**
```javascript
const { test, expect } = require('@playwright/test');

test.describe('Perusahaan - Create Lowongan', () => {
  
  test.use({ storageState: 'tests/e2e/auth-states/perusahaan.json' });

  test('E2E_PRS_001: Perusahaan dapat membuat lowongan baru', async ({ page }) => {
    // Step 1: Navigate ke halaman create lowongan
    await page.goto('http://127.0.0.1:8000/perusahaan/lowongan/create');
    await expect(page).toHaveTitle(/Buat Lowongan/);

    // Step 2: Isi form lowongan
    await page.fill('input[name="judul"]', 'Software Engineer - Backend');
    await page.fill('textarea[name="deskripsi"]', 'Membutuhkan backend developer dengan pengalaman Node.js');
    await page.fill('input[name="kuota"]', '5');
    await page.selectOption('select[name="jenis_id"]', '1');
    await page.fill('input[name="durasi"]', '6');
    
    // Step 3: Pilih skills
    await page.check('input[type="checkbox"][value="1"]');
    await page.check('input[type="checkbox"][value="3"]');
    
    // Step 4: Upload gambar
    const fileInput = page.locator('input[type="file"][name="gambar"]');
    await fileInput.setInputFiles('tests/e2e/fixtures/lowongan-image.jpg');

    // Step 5: Submit form
    await page.click('button[type="submit"]');

    // Step 6: Verify redirect
    await expect(page).toHaveURL(/\/perusahaan\/lowongan/);
    
    // Step 7: Verify success message
    await expect(page.locator('.alert-success')).toContainText('Lowongan berhasil dibuat');
    
    // Step 8: Verify lowongan muncul
    await expect(page.locator('text=Software Engineer - Backend')).toBeVisible();
  });
});
```

**Step 3: Generate auth state (jika belum ada)**
```powershell
# Update tests/e2e/setup-auth.js untuk tambah role perusahaan
# Lalu run:
node tests\e2e\setup-auth.js
```

**Step 4: Run test**
```powershell
npx playwright test tests/e2e/perusahaan/perusahaan-create-lowongan.spec.js --headed
```

### Best Practices

#### 1. Naming Convention

**PHPUnit:**
```php
// Good
public function test_admin_dapat_create_mahasiswa()
public function test_validation_nim_format_invalid()
public function test_api_login_with_wrong_credentials()

// Avoid
public function testAdminCreateMhs()
public function test1()
```

**Playwright:**
```javascript
// Good
test('E2E_MHS_001: Mahasiswa dapat melihat daftar lowongan', ...)
test('E2E_ADM_002: Admin dapat import mahasiswa via CSV', ...)

// Avoid
test('test mahasiswa', ...)
test('check lowongan page', ...)
```

#### 2. Test Structure (AAA Pattern)

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
    $response = $this->get('/api/mahasiswa/1'); // RISKY!
}
```

#### 4. Use Factories

```php
// Good
$mahasiswa = Mahasiswa::factory()->create([
    'nim' => '2241760001',
    'nama' => 'Test Mahasiswa'
]);

// Avoid
$mahasiswa = Mahasiswa::create([
    'nim' => '2241760001',
    'nama' => 'Test Mahasiswa',
    // ... banyak field required lainnya
]);
```

---

## E2E Troubleshooting

### Problem 1: Server Not Running

**Gejala:**
```
Error: page.goto: net::ERR_CONNECTION_REFUSED at http://127.0.0.1:8000
```

**Solusi:**
```powershell
# Check server
Invoke-WebRequest http://127.0.0.1:8000/login

# Jika tidak ada response, start server
php artisan serve --host=127.0.0.1 --port=8000

# Tunggu 10 detik, lalu coba lagi
```

### Problem 2: Authentication Failed

**Gejala:**
```
Error: Authentication failed for ADMIN
Login button not found
```

**Solusi:**
```powershell
# 1. Verify credentials di database
php artisan tinker
# >>> User::where('email', 'admin@example.com')->first()
# >>> exit

# 2. Update .env.playwright jika credentials berbeda

# 3. Regenerate auth states
node tests\e2e\setup-auth.js

# 4. Run test lagi
npx playwright test --project=admin
```

### Problem 3: Timeout Error

**Gejala:**
```
TimeoutError: page.waitForSelector: Timeout 30000ms exceeded
Selector: 'button[type="submit"]'
```

**Solusi:**
```powershell
# 1. Debug dengan headed mode
npx playwright test --headed -g "test-name"

# 2. Gunakan codegen untuk find selector
npx playwright codegen http://127.0.0.1:8000/mahasiswa/dashboard

# 3. Update selector di test file
```

### Problem 4: Port Already in Use

**Gejala:**
```
Address already in use: 127.0.0.1:8000
```

**Solusi:**
```powershell
# Kill process di port 8000
netstat -ano | findstr :8000
taskkill /F /PID [PID_NUMBER]

# Atau gunakan port lain
php artisan serve --host=127.0.0.1 --port=8001

# Update BASE_URL di .env.playwright
```

### Problem 5: Browsers Not Installed

**Gejala:**
```
Error: Executable doesn't exist
```

**Solusi:**
```powershell
# Install semua browsers
npx playwright install

# Verify installation
npx playwright --version
```

---

## Configuration Files

### phpunit.xml
Konfigurasi PHPUnit untuk API, Feature, dan Unit tests.

**Key Sections:**
```xml
<testsuites>
    <testsuite name="Unit">
        <directory>tests/Unit</directory>
    </testsuite>
    <testsuite name="Feature">
        <directory>tests/Feature</directory>
    </testsuite>
    <testsuite name="Api">
        <directory>tests/Api</directory>
    </testsuite>
</testsuites>

<logging>
    <testdoxHtml outputFile="tests/reports/testdox.html"/>
    <junit outputFile="tests/reports/junit.xml"/>
    <testdoxText outputFile="tests/reports/testdox.txt"/>
</logging>
```

### playwright.config.js
Konfigurasi Playwright untuk E2E tests.

**Key Sections:**
- Test directory: `./tests/e2e`
- Test match: `**/*.spec.js`
- Projects: admin, mahasiswa, dosen
- Timeout: 30000ms
- Reporter: HTML

### .env.testing
Environment untuk PHPUnit tests.

**Recommended:**
```env
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

### .env.playwright
Environment untuk Playwright tests.

**Required:**
```env
BASE_URL=http://127.0.0.1:8000
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=secret
MAHASISWA_EMAIL=mahasiswa1@example.com
MAHASISWA_PASSWORD=secret
DOSEN_EMAIL=dosen1@example.com
DOSEN_PASSWORD=secret
```

---

## Test Credentials

| Role | Email | Password | Dashboard URL |
|------|-------|----------|---------------|
| Admin | admin@example.com | admin | /dashboard |
| Mahasiswa | 2341720074@student.com | 2341720074 | /mahasiswa/dashboard |
| Dosen | 1980031@gmail.com | 1980031 | /dosen/dashboard |

---

**Last Updated:** November 25, 2025  
**Version:** 2.0  
**Maintained by:** JTI Intern Development Team
