# TESTING DOCUMENTATION
## E2E-JTI Intern PMPL - Comprehensive Testing Suite

**Project**: Sistem Manajemen Magang JTI  
**URL Production**: https://afws.my.id/E2E-JTIintern-PMPL  
**Last Updated**: 27 Desember 2025

---

## Overview

Project ini dilengkapi dengan **5 jenis pengujian** yang comprehensive:

| Jenis Testing | Status | Test Count | Coverage |
|---------------|--------|------------|----------|
| API Testing | PASS (100%) | 115 tests | Authentication, CRUD, Validation |
| Unit Testing | Partial (92.7%) | 41 tests | Models & Business Logic |
| Integration Testing | Partial (84.6%) | 13 tests | Service Integration |
| E2E Testing | PASS (100%) | 23 tests | User Workflows |
| Performance Testing | PASS (100%) | 5 test suites | Load, Stress, Spike, Soak |

**Total**: 197+ tests dengan berbagai skenario testing

---

## Struktur Testing

```
tests/
├── Api/                    # 115 API tests (PHPUnit)
│   ├── Admin/             # Admin API endpoints
│   ├── Auth/              # Authentication & Authorization
│   └── Validation/        # Input validation tests
│
├── Unit/                   # 41 Unit tests (PHPUnit)
│   ├── Models/            # Model relationships & methods
│   └── Services/          # Service layer logic
│
├── Feature/                # Integration tests
│   └── Services/          # Service integration tests
│
├── Integration/            # 13 Integration tests (PHPUnit)
│   └── Services/          # Cross-service integration
│
├── E2E/                    # 23 E2E tests (Playwright)
│   └── *.spec.js          # End-to-end user scenarios
│
└── Performance/            # 5 K6 performance tests
    ├── tests/             # K6 test scripts (.js)
    ├── scripts/           # Helper scripts
    ├── results/           # Test results
    └── run-tests.ps1      # Main test runner
```

---

## Quick Start

### Prerequisites
```powershell
# PHP & Composer (untuk PHPUnit tests)
php --version  # PHP 8.1+
composer --version

# Node.js (untuk Playwright E2E)
node --version  # v18+
npm --version

# K6 (untuk Performance tests)
k6 version  # v0.48+
```

### Running All Tests

```powershell
# API Tests (PHPUnit)
.\vendor\bin\phpunit.bat --testsuite=Api

# Unit Tests
.\vendor\bin\phpunit.bat --testsuite=Unit

# E2E Tests (Playwright)
npx playwright test

# Performance Tests (K6)
.\tests\Performance\run-tests.ps1
```

---

## Detailed Test Documentation

### 1. API Testing (115 tests)

**Status**: PASS - 100% SUCCESS  
**Framework**: PHPUnit 10.5  
**Runtime**: ~50 detik  
**Total Assertions**: 234

#### Coverage Areas:
- Authentication & Login (30 tests) - PASS
- Authorization & Roles (10 tests) - PASS
- CRUD Operations (30 tests) - PASS
- Input Validation (45 tests) - PASS

#### Test Suites:
```
tests/Api/
├── Admin/
│   ├── AdminAuthorizationTest.php         (5 tests) - PASS
│   ├── AdminMahasiswaApiTest.php          (5 tests) - PASS
│   ├── AdminMahasiswaSearchTest.php       (5 tests) - PASS
│   ├── AdminMahasiswaValidationTest.php   (5 tests) - PASS
│   ├── AdminPeriodeApiTest.php            (5 tests) - PASS
│   └── AdminPeriodeValidationTest.php     (5 tests) - PASS
├── Auth/
│   ├── LoginApiTest.php                   (10 tests) - PASS
│   ├── LoginEdgeCaseTest.php              (5 tests) - PASS
│   ├── AuthValidationTest.php             (5 tests) - PASS
│   └── TokenManagementTest.php            (5 tests) - PASS
└── Validation/
    ├── ArrayValidationTest.php            (10 tests) - PASS
    ├── BooleanValidationTest.php          (10 tests) - PASS
    ├── NumericValidationTest.php          (10 tests) - PASS
    ├── StringValidationTest.php           (10 tests) - PASS
    ├── SpecialCharacterTest.php           (10 tests) - PASS
    └── EdgeCaseTest.php                   (10 tests) - PASS
```

#### Run Commands:
```powershell
# All API tests
.\vendor\bin\phpunit.bat --testsuite=Api --testdox

# Specific test file
.\vendor\bin\phpunit.bat tests/Api/Auth/LoginApiTest.php --testdox

# With coverage
.\vendor\bin\phpunit.bat --testsuite=Api --coverage-html coverage/
```

---

### 2. Unit Testing (41 tests)

**Status**: Partial - 92.7% Pass (38/41 tests passing)  
**Framework**: PHPUnit 10.5

#### Coverage:
- Model Relationships
- Business Logic
- Data Validation
- Helper Methods

#### Known Issues:
- Database schema mismatch pada 3 model tests
- Perlu update factory definitions

#### Run Commands:
```powershell
# All unit tests
.\vendor\bin\phpunit.bat --testsuite=Unit --testdox

# Skip problematic tests
.\vendor\bin\phpunit.bat --testsuite=Unit --exclude-group=schema-dependent
```

---

### 3. Integration Testing (13 tests)

**Status**: Partial - 84.6% Pass (11/13 tests passing)  
**Framework**: PHPUnit 10.5

#### Coverage:
- Service Layer Integration
- Cross-Service Communication
- Database Transactions

#### Known Issues:
- 2 tests failing due to factory definitions

#### Run Commands:
```powershell
.\vendor\bin\phpunit.bat tests/Integration/ --testdox
```

---

### 4. E2E Testing (23 tests)

**Status**: PASS - 100% SUCCESS  
**Framework**: Playwright (JavaScript)  
**Target**: https://afws.my.id/E2E-JTIintern-PMPL  
**Runtime**: ~5-10 menit

#### Test Scenarios:
```
tests/E2E/
├── auth.spec.js                    # Login/Logout workflows
├── mahasiswa-dashboard.spec.js     # Student dashboard features
├── dosen-dashboard.spec.js         # Lecturer dashboard features
├── admin-dashboard.spec.js         # Admin panel operations
├── lamaran.spec.js                 # Application submission
├── lowongan.spec.js                # Job posting management
└── navigation.spec.js              # Navigation & routing
```

#### Run Commands:
```powershell
# All E2E tests
npx playwright test

# Specific test
npx playwright test auth.spec.js

# With UI mode
npx playwright test --ui

# Generate report
npx playwright show-report
```

#### Configuration:
- Browser: Chromium, Firefox, WebKit
- Parallel execution: 3 workers
- Timeout: 30s per test
- Retries: 2 on failure

---

### 5. Performance Testing (5 test suites)

**Status**: PASS - 100% SUCCESS  
**Framework**: K6 (Grafana)  
**Target**: https://afws.my.id/E2E-JTIintern-PMPL  
**Total Runtime**: ~18 menit (all tests)

#### Test Suites:

| Test | Duration | Users | Purpose | Status |
|------|----------|-------|---------|--------|
| Smoke Test | 30s | 1 | Quick validation | PASS |
| Load Test | 2m | 10 | Normal load | PASS |
| Stress Test | 8m | 30 | Breaking point | PASS |
| Spike Test | 2m | 50 | Traffic spike | PASS |
| Soak Test | 5m | 10 | Endurance | PASS |

#### Results Summary:
- **Overall Success Rate**: 99.5%+
- **P95 Response Time**: < 5s (normal load)
- **Error Rate**: < 0.5%
- **Throughput**: ~4 req/s sustained

#### Detailed Test Results:

**1. Smoke Test (Quick Validation):**
- Total Requests: 56
- Success Rate: 100%
- Average Response Time: 1.5s
- P95 Response Time: 2.93s
- Error Rate: 0%
- Conclusion: Basic functionality working perfectly

**2. Load Test (Normal Load):**
- Total Requests: 565
- Success Rate: 99.02%
- Average Response Time: 1.2s
- P95 Response Time: 2.48s
- Error Rate: 0%
- Conclusion: Handles normal load excellently

**3. Stress Test (Breaking Point):**
- Total Requests: 1,841
- Success Rate: 99.95%
- Average Response Time: 2.3s
- P95 Response Time: 5.05s
- Error Rate: 0.04%
- Conclusion: System stable under high load

**4. Spike Test (Traffic Surge):**
- Total Requests: 599
- Success Rate: 100%
- Average Response Time: 4.2s
- P95 Response Time: 13.31s
- Error Rate: 0%
- Conclusion: Handles traffic spikes well

**5. Soak Test (Endurance):**
- Total Requests: 1,427
- Success Rate: 99.55%
- Average Response Time: 1.8s
- P95 Response Time: 3.06s
- Error Rate: 0.33%
- Conclusion: No memory leaks, stable over time

#### Run Commands:
```powershell
# All performance tests (~18 min)
.\tests\Performance\run-tests.ps1

# Quick tests (Smoke + Load = 2.5 min)
.\tests\Performance\run-tests.ps1 -TestType quick

# Specific test
.\tests\Performance\run-tests.ps1 -TestType smoke
.\tests\Performance\run-tests.ps1 -TestType load
.\tests\Performance\run-tests.ps1 -TestType stress
.\tests\Performance\run-tests.ps1 -TestType spike
.\tests\Performance\run-tests.ps1 -TestType soak
```

---

## Test Results & Reports

### Lokasi Reports

```
# API Test Results
./storage/logs/phpunit-api.log

# E2E Test Results
./playwright-report/index.html
./test-results/

# Performance Test Results
./tests/Performance/results/
└── test-results-[timestamp].txt
```

### View Reports

```powershell
# E2E Report (HTML)
npx playwright show-report

# Performance Results
Get-Content ./tests/Performance/results/test-results-*.txt
```

---

## Configuration

### PHPUnit Configuration (`phpunit.xml`)
```xml
<testsuites>
    <testsuite name="Api">
        <directory>tests/Api</directory>
    </testsuite>
    <testsuite name="Unit">
        <directory>tests/Unit</directory>
    </testsuite>
    <testsuite name="Integration">
        <directory>tests/Integration</directory>
    </testsuite>
</testsuites>
```

### Playwright Configuration (`playwright.config.js`)
```javascript
use: {
  baseURL: 'https://afws.my.id/E2E-JTIintern-PMPL',
  trace: 'on-first-retry',
  screenshot: 'only-on-failure',
}
```

### K6 Configuration
- Base URL: https://afws.my.id/E2E-JTIintern-PMPL
- K6 Binary: C:\k6\k6.exe
- Tests: tests/Performance/tests/*.js

---

## CI/CD Integration

### GitHub Actions Workflow

```yaml
# .github/workflows/tests.yml
name: Run Tests
on: [push, pull_request]

jobs:
  api-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Run API Tests
        run: ./vendor/bin/phpunit --testsuite=Api

  e2e-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Run E2E Tests
        run: npx playwright test
```

---

## Troubleshooting

### Common Issues

**1. PHPUnit: Database Connection Error**
```powershell
# Check .env configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=root
DB_PASSWORD=
```

**2. Playwright: Browser Not Found**
```powershell
# Install browsers
npx playwright install
```

**3. K6: Command Not Found**
```powershell
# Add to PATH
$env:Path += ";C:\k6"
```

**4. Test Timeout**
- Increase timeout di configuration
- Check network connection
- Verify server is running

---

## Documentation Files

| File | Purpose | Location |
|------|---------|----------|
| `TESTING-DOCUMENTATION.md` | Main testing docs (this file) | Root |
| `VALIDATION-SUMMARY.md` | Testing summary for reports | Root |
| `tests/Performance/README.md` | K6 performance testing guide | tests/Performance/ |

---

## Best Practices

1. **Run Tests Locally First** - Sebelum push ke repository
2. **Keep Tests Fast** - Target < 1s per test (kecuali E2E & Performance)
3. **Use Factories** - Untuk test data generation
4. **Mock External Services** - Untuk unit tests
5. **Clean Test Data** - Setelah selesai testing
6. **Document Test Scenarios** - Di docblock atau comments
7. **Monitor Performance** - Regular performance testing

---

## Contributors

### Development Team

**GitHub Contributors:**
- Ahmad Fadlih Wahyu Sardana
- Fabiqnn
- uhamhz
- Cindy Laili Larasati

---

## Support

**Issues & Questions**: GitHub Issues  
**Documentation**: Project Wiki  
**Contact**: development-team@example.com

---

## Changelog

### v1.0.0 (27 Desember 2025)
- API Testing: 115 tests complete (100% PASS)
- E2E Testing: 23 tests dengan Playwright (100% PASS)
- Performance Testing: 5 K6 test suites (100% PASS)
- Documentation: Comprehensive testing docs
- Unit Testing: 41 tests (92.7% PASS)
- Integration Testing: 13 tests (84.6% PASS)

---

**Project**: E2E-JTI Intern PMPL  
**Framework**: Laravel 10.x | PHPUnit 10.5 | Playwright | K6  
**Maintained by**: Development Team  
**Last Updated**: 27 Desember 2025
