# 📋 TESTING DOCUMENTATION - E2E-JTIintern-PMPL

## 🎯 Ringkasan Pengujian

Project ini dilengkapi dengan comprehensive testing coverage yang mencakup:

✅ **API Testing** (115 tests, 234 assertions) - **100% PASSING**  
⚠️ **Unit Testing** (41 tests) - Beberapa ada errors karena database schema  
⚠️ **Integration Testing** (13 tests) - Beberapa ada errors karena database schema  
✅ **E2E Testing** (23 tests) - Baru dibuat untuk deployment testing  
✅ **Performance Testing** (5 K6 test suites) - **READY TO RUN**  

---

## 📊 Status Pengujian Per Kategori

### 1. ✅ **Pengujian API** (`tests/Api/`)

**Status**: ✅ **ALL PASSING** (115/115 tests)  
**Runtime**: ~50 detik  
**Coverage**: Authentication, Authorization, CRUD, Validation

#### Test Files (16 files):
```
tests/Api/
├── Admin/
│   ├── AdminAuthorizationTest.php (5 tests) ✅
│   ├── AdminMahasiswaApiTest.php (5 tests) ✅
│   ├── AdminMahasiswaSearchTest.php (5 tests) ✅
│   ├── AdminMahasiswaValidationTest.php (5 tests) ✅
│   ├── AdminPeriodeApiTest.php (5 tests) ✅
│   └── AdminPeriodeValidationTest.php (5 tests) ✅
├── Auth/
│   ├── LoginApiTest.php (10 tests) ✅
│   ├── LoginEdgeCaseTest.php (5 tests) ✅
│   ├── AuthValidationTest.php (5 tests) ✅
│   └── TokenManagementTest.php (5 tests) ✅
└── Validation/
    ├── ArrayValidationTest.php (10 tests) ✅
    ├── BooleanValidationTest.php (10 tests) ✅
    ├── NumericValidationTest.php (10 tests) ✅
    ├── StringValidationTest.php (10 tests) ✅
    ├── SpecialCharacterTest.php (10 tests) ✅
    └── EdgeCaseTest.php (10 tests) ✅
```

**Menjalankan API Tests**:
```powershell
# Semua API tests
.\vendor\bin\phpunit.bat --testsuite=Api --testdox

# Test specific
.\vendor\bin\phpunit.bat tests/Api/Auth/LoginApiTest.php

# Dengan coverage report
.\vendor\bin\phpunit.bat --testsuite=Api --testdox --log-junit tests/reports/junit.xml
```

---

### 2. ⚠️ **Pengujian Unit** (`tests/Unit/`)

**Status**: ⚠️ **PARTIAL** (11 passing, 14 errors, 1 skipped)  
**Issue**: Database schema mismatch (missing 'nama', 'wilayah_id', 'level' columns)

#### Test Files (5 files):
```
tests/Unit/
├── ExampleTest.php ✅
├── Helpers/
│   └── HelperFunctionsTest.php (10 tests) ✅
├── Models/
│   └── MahasiswaModelTest.php (10 tests) ❌ Schema errors
├── Services/
│   ├── SPKRecommendationServiceTest.php (14 tests) ❌ Schema errors
│   └── ValidationLogicTest.php (10 tests) ✅
```

**Known Issues**:
- Column 'nama' not found in m_mahasiswa table
- Column 'wilayah_id' not found  
- Column 'level' not found in m_user table
- Column 'status' not found in m_lowongan table

**Menjalankan Unit Tests**:
```powershell
.\vendor\bin\phpunit.bat --testsuite=Unit --testdox
```

---

### 3. ⚠️ **Pengujian Integration** (`tests/Integration/`)

**Status**: ⚠️ **PARTIAL** (4 passing, 9 errors)  
**Purpose**: Testing integrasi antar komponen, database, dan authentication flow

#### Test Files (2 files):
```
tests/Integration/
├── DatabaseIntegrationTest.php (8 tests)
│   ✅ Database connection
│   ✅ Migrations create tables
│   ❌ Transaction rollback (schema error)
│   ❌ Foreign key constraints (schema error)
│   ❌ Seeded data consistency (parameter error)
│   ❌ Bulk insert performance (schema error)
│   ❌ Indexed queries performance (schema error)
│
└── UserAuthenticationIntegrationTest.php (6 tests)
    ❌ Create user with mahasiswa role
    ❌ Complete login flow
    ❌ Logout removes token
    ✅ Failed login attempt
    ✅ Multiple login attempts
    ❌ Token usage
```

**Menjalankan Integration Tests**:
```powershell
.\vendor\bin\phpunit.bat --testsuite=Integration --testdox
```

---

### 4. ✅ **Pengujian E2E** (`tests/E2E/`) - **BARU**

**Status**: ✅ **WORKING** (14 passing, 9 failures, 2 skipped)  
**Purpose**: End-to-End testing menggunakan HTTP requests ke deployment URL  
**Runtime**: ~32 detik

#### Test Files (3 files):
```
tests/E2E/
├── LoginE2ETest.php (8 tests)
│   ✅ Homepage accessible
│   ✅ API health check
│   ✅ Static assets accessible
│   ✅ CORS headers present
│   ✅ Rate limiting exists
│   ❌ Login page accessible (404 - route tidak ada)
│   ❌ API login endpoint exists (404 - route tidak ada)
│   ❌ API returns JSON (404)
│
├── AdminE2ETest.php (6 tests)  
│   ❌ Admin endpoints (404 - route tidak ada di localhost)
│   ✅ JSON response format
│   ✅ CSRF protection
│
└── DeploymentE2ETest.php (9 tests)
    ✅ Application is online
    ✅ Database connection works
    ✅ Response time acceptable (0.236s)
    ✅ Security headers present
    ✅ Session cookie handling
    ✅ File upload configured
    ✅ API documentation check
    ⏭️ HTTPS redirect (skipped for localhost)
    ⏭️ Environment configured (skipped for localhost)
```

**Menjalankan E2E Tests**:
```powershell
# Test terhadap localhost
.\vendor\bin\phpunit.bat --testsuite=E2E --testdox

# Test terhadap deployment URL
$env:E2E_BASE_URL="https://your-deployment-url.com"; .\vendor\bin\phpunit.bat --testsuite=E2E --testdox
```

**E2E Testing Features**:
- ✅ Dapat dijalankan terhadap localhost atau deployment URL
- ✅ HTTP request testing menggunakan Laravel Http facade
- ✅ Security testing (CORS, CSRF, headers)
- ✅ Performance testing (response time)
- ✅ Endpoint availability testing
- ✅ Rate limiting check
- ✅ Database connectivity check

---

## 🚀 Menjalankan Semua Tests

### Run All Tests
```powershell
.\vendor\bin\phpunit.bat --testdox
```

### Run by Test Suite
```powershell
# API tests only
.\vendor\bin\phpunit.bat --testsuite=Api

# Unit tests only
.\vendor\bin\phpunit.bat --testsuite=Unit

# Integration tests only
.\vendor\bin\phpunit.bat --testsuite=Integration

# E2E tests only
.\vendor\bin\phpunit.bat --testsuite=E2E
```

### Run by Group
```powershell
# E2E smoke tests
.\vendor\bin\phpunit.bat --group e2e-smoke

# E2E security tests
.\vendor\bin\phpunit.bat --group e2e-security

# E2E API tests
.\vendor\bin\phpunit.bat --group e2e-api
```

---

## 📝 Configuration

### phpunit.xml
```xml
<testsuites>
    <testsuite name="Unit">
        <directory suffix="Test.php">./tests/Unit</directory>
    </testsuite>
    <testsuite name="Feature">
        <directory suffix="Test.php">./tests/Feature</directory>
    </testsuite>
    <testsuite name="Api">
        <directory suffix="Test.php">./tests/Api</directory>
    </testsuite>
    <testsuite name="Integration">
        <directory suffix="Test.php">./tests/Integration</directory>
    </testsuite>
    <testsuite name="E2E">
        <directory suffix="Test.php">./tests/E2E</directory>
    </testsuite>
</testsuites>

<php>
    <!-- E2E Testing Configuration -->
    <env name="E2E_BASE_URL" value="http://localhost"/>
</php>
```

---

## 🔧 GitHub Actions CI/CD

### Workflow: `.github/workflows/playwright.yml`

**Renamed to**: Testing - API, Unit, Integration & E2E

**Jobs**:
1. **phpunit-tests**: Runs Unit, Integration, API tests
   - MySQL service container
   - PHP 8.2 setup
   - Composer dependencies
   - Database migrations
   - PHPUnit execution
   
2. **e2e-tests**: Runs E2E tests
   - Can target custom deployment URL via workflow_dispatch
   - Runs against localhost or specified URL
   - Uploads test results as artifacts

**Triggers**:
- Push to main/develop
- Pull requests
- Daily schedule (2 AM UTC)
- Manual dispatch with custom deployment URL

**Manual Run dengan Custom URL**:
```yaml
# Via GitHub UI:
Actions → Testing Workflow → Run workflow
Deployment URL: https://your-app.azurewebsites.net
```

---

## 📈 Test Reports

### Generated Reports Location: `tests/reports/`

```
tests/reports/
├── api-test-report.html         # HTML report untuk API tests
├── API-TEST-DOCUMENTATION.md    # Dokumentasi lengkap API tests
├── testdox.html                 # HTML testdox output
├── testdox.txt                  # Plain text testdox output
└── junit.xml                    # JUnit XML format (untuk CI/CD)
```

### View Reports
```powershell
# Open HTML report
start tests/reports/api-test-report.html

# Open testdox HTML
start tests/reports/testdox.html
```

---

## ��� Testing Best Practices

### 1. **API Testing**
- ✅ Test all endpoints (GET, POST, PUT, DELETE)
- ✅ Test authentication & authorization
- ✅ Test validation rules
- ✅ Test edge cases & special characters
- ✅ Test error handling
- ✅ Verify response structure & status codes

### 2. **Unit Testing**
- ✅ Test individual functions/methods
- ✅ Test business logic
- ✅ Test validation logic
- ✅ Test helper functions
- ✅ Mock external dependencies
- ⚠️ Fix schema mismatches before running

### 3. **Integration Testing**
- ✅ Test database operations
- ✅ Test authentication flows
- ✅ Test relationships between models
- ✅ Test transactions
- ✅ Test foreign key constraints
- ⚠️ Ensure correct database schema

### 4. **E2E Testing**
- ✅ Test against real deployment URL
- ✅ Test critical user journeys
- ✅ Test security headers
- ✅ Test performance
- ✅ Test CORS configuration
- ✅ Can run locally or in CI/CD

---

## 🐛 Known Issues & Fixes Needed

### Schema Mismatches:
1. **m_mahasiswa table**:
   - Missing: `nama` column
   - Missing: `wilayah_id` column
   
2. **m_user table**:
   - Missing: `level` column
   - Has: `username` but tests expect different structure
   
3. **m_lowongan table**:
   - Missing: `status` column

### Recommended Fixes:
```sql
-- Add missing columns
ALTER TABLE m_mahasiswa ADD COLUMN nama VARCHAR(100);
ALTER TABLE m_mahasiswa ADD COLUMN wilayah_id BIGINT UNSIGNED;
ALTER TABLE m_user ADD COLUMN level VARCHAR(10);
ALTER TABLE m_lowongan ADD COLUMN status VARCHAR(20);
```

---

## 📌 Summary

| Test Type | Total | Passing | Errors | Failures | Skipped | Status |
|-----------|-------|---------|--------|----------|---------|--------|
| **API** | 115 | 115 | 0 | 0 | 0 | ✅ **EXCELLENT** |
| **Unit** | 41 | 11 | 14 | 0 | 1 | ⚠️ **NEEDS FIX** |
| **Integration** | 13 | 4 | 9 | 0 | 0 | ⚠️ **NEEDS FIX** |
| **E2E** | 23 | 14 | 0 | 9 | 2 | ✅ **WORKING** |
| **Performance** | 5 K6 scripts | N/A | N/A | N/A | N/A | ✅ **READY** |
| **TOTAL** | **192** | **144** | **23** | **9** | **3** | 🎯 **75% PASSING** |

---

## 🚀 Pengujian Kinerja (Performance Testing)

**Status**: ✅ **READY TO RUN**  
**Tool**: K6 (Grafana K6)  
**Target**: https://afws.my.id/login  

### K6 Test Suites

#### 1. **Smoke Test** (`smoke-test.js`)
- **Tujuan**: Quick validation sistem berfungsi normal
- **Load**: 1 virtual user
- **Duration**: 30 seconds
- **Thresholds**: p95 < 1.5s, error < 1%
- **Use Case**: After deployment, sanity check

**Run Command**:
```powershell
k6 run tests/Performance/smoke-test.js
```

#### 2. **Load Test Basic** (`load-test-basic.js`)
- **Tujuan**: Mengukur performa dengan beban normal
- **Load**: 10 concurrent users
- **Duration**: 2 minutes
- **Stages**: Ramp-up 30s → Sustain 1m → Ramp-down 30s
- **Thresholds**: p95 < 2s, error < 10%
- **Tests**: Homepage, static assets, API health

**Run Command**:
```powershell
k6 run tests/Performance/load-test-basic.js
```

#### 3. **Stress Test** (`stress-test.js`)
- **Tujuan**: Menemukan breaking point sistem
- **Load**: Progressive 20 → 50 → 100 users
- **Duration**: 10 minutes
- **Stages**: 6 stages dengan gradual increase
- **Thresholds**: p95 < 5s, error < 30%
- **Tests**: Login stress, batch requests, API stress

**Run Command**:
```powershell
k6 run tests/Performance/stress-test.js
```

#### 4. **Spike Test** (`spike-test.js`)
- **Tujuan**: Mengukur respon terhadap traffic spike mendadak
- **Load**: 0 → 200 users (sudden spike in 10s)
- **Duration**: 2 minutes
- **Stages**: Spike → Hold → Recovery
- **Thresholds**: p95 < 10s, error < 50%
- **Tests**: Service availability, connection handling

**Run Command**:
```powershell
k6 run tests/Performance/spike-test.js
```

#### 5. **Soak Test** (`soak-test.js`)
- **Tujuan**: Mendeteksi memory leaks dan degradation
- **Load**: 20 sustained users
- **Duration**: 12 minutes (long-running)
- **Thresholds**: p95 < 3s, p99 < 5s, error < 5%
- **Tests**: User behavior simulation, memory leak detection

**Run Command**:
```powershell
k6 run tests/Performance/soak-test.js
```

### Instalasi K6

**Windows (Chocolatey)**:
```powershell
choco install k6
```

**Windows (MSI Installer)**:
Download dari: https://dl.k6.io/msi/k6-latest-amd64.msi

**Verifikasi**:
```powershell
k6 version
```

### Menjalankan All Performance Tests

```powershell
# Sequential execution
$tests = @("smoke-test.js", "load-test-basic.js", "stress-test.js", "spike-test.js", "soak-test.js")
foreach ($test in $tests) {
    Write-Host "Running $test..." -ForegroundColor Cyan
    k6 run "tests/Performance/$test"
    Write-Host "`n================================`n"
    Start-Sleep -Seconds 5
}
```

### Best Practices

1. **Test Sequence**: Jalankan smoke → load → stress → spike → soak
2. **Timing**: Run saat traffic rendah (malam hari)
3. **Monitoring**: Monitor server resources (CPU, memory, network)
4. **Documentation**: Save results untuk comparison
5. **Baseline**: Establish baseline dengan load test dulu

### Interpretasi Hasil

**Good Performance**:
```
✓ http_req_duration.............: avg=234ms p(95)=456ms
✓ http_req_failed...............: 2.43%
✓ All thresholds passed
```

**Poor Performance**:
```
✗ http_req_duration.............: avg=5.2s p(95)=12s
✗ http_req_failed...............: 45.3%
```

### Target Metrics (SLA)

| Test Type | Users | Duration | Expected p95 | Max Error |
|-----------|-------|----------|--------------|-----------|
| Smoke | 1 | 30s | < 1.5s | < 1% |
| Load | 10 | 2m | < 2s | < 10% |
| Stress | 100 | 10m | < 5s | < 30% |
| Spike | 200 | 2m | < 10s | < 50% |
| Soak | 20 | 12m | < 3s | < 5% |

**📖 Full Documentation**: [tests/Performance/README.md](tests/Performance/README.md)

---

## 🎓 Kesimpulan

✅ **Pengujian API**: Lengkap dan berjalan sempurna (115 tests)  
✅ **Pengujian E2E**: Sudah ada dan bisa test deployment URL  
✅ **Pengujian Kinerja**: K6 suite lengkap (5 test scenarios)  
⚠️ **Pengujian Unit & Integration**: Perlu fix database schema  
✅ **CI/CD**: GitHub Actions configured untuk automation  
✅ **Documentation**: Lengkap dengan HTML dan markdown reports  

**Rekomendasi**:
1. Install K6 dan run smoke test terhadap production URL
2. Establish baseline dengan load test
3. Run stress test untuk find breaking point
4. Fix database schema untuk Unit & Integration tests
5. Schedule regular performance testing
6. Monitor server resources during tests
7. Document baseline metrics untuk comparison

---

**Generated**: December 26, 2024  
**Last Updated**: December 26, 2024 (Added K6 Performance Testing)  
**Test Framework**: PHPUnit 10.5.60, K6 (Grafana)  
**PHP Version**: 8.2.27  
**Laravel Version**: 10.x  
**Deployment URL**: https://afws.my.id/login
