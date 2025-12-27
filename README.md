# E2E JTI Intern PMPL
## Sistem Manajemen Magang Jurusan Teknologi Informasi

**Framework**: Laravel 10.x  
**URL Production**: https://afws.my.id/E2E-JTIintern-PMPL  
**Last Updated**: 27 Desember 2025

---

## � Dokumentasi Testing & Validasi

Project ini dilengkapi dengan dokumentasi testing lengkap untuk keperluan validasi dan pelaporan:

### 📖 Dokumentasi Utama

1. **[TESTING-DOCUMENTATION.md](TESTING-DOCUMENTATION.md)**
   - Panduan lengkap testing suite (197+ tests)
   - Petunjuk instalasi dan konfigurasi
   - Command reference untuk semua jenis testing
   - Best practices dan troubleshooting
   - Hasil performance testing (K6)

2. **[VALIDATION-SUMMARY.md](VALIDATION-SUMMARY.md)**
   - Executive summary untuk laporan formal
   - Hasil validasi testing lengkap
   - Metrics dan quality assurance
   - Status approval production
   - Checklist validasi pre-production

3. **[DOCUMENTATION-INDEX.md](DOCUMENTATION-INDEX.md)**
   - Panduan navigasi dokumentasi
   - Quick reference berdasarkan role
   - Command cheatsheet

### 🎯 Quick Links

| Dokumentasi | Deskripsi | Link |
|-------------|-----------|------|
| Testing Guide | Panduan lengkap semua testing | [TESTING-DOCUMENTATION.md](TESTING-DOCUMENTATION.md) |
| Validation Report | Summary untuk laporan formal | [VALIDATION-SUMMARY.md](VALIDATION-SUMMARY.md) |
| Documentation Index | Navigation guide | [DOCUMENTATION-INDEX.md](DOCUMENTATION-INDEX.md) |

---

## 📋 Table of Contents

- [About Project](#about-project)
- [Testing Overview](#testing-overview)
- [Installation](#installation)
- [Configuration](#configuration)
- [Contributing](#contributing)

---

## About Project

Sistem Manajemen Magang JTI adalah platform berbasis web untuk mengelola proses magang mahasiswa Jurusan Teknologi Informasi. Sistem ini dibangun menggunakan Laravel 10 dengan fitur:

- 🔐 Authentication & Authorization (Laravel Sanctum)
- 👥 Multi-role: Admin, Dosen, Mahasiswa, Perusahaan
- 📝 Manajemen Lowongan & Lamaran Magang
- 📊 Dashboard & Reporting
- 🔔 Sistem Notifikasi
- 🎯 SPK Recommendation System (SAW Method)
- 📈 Beban Kerja Dosen & Plotting Mahasiswa

---

## Testing Overview

Project ini dilengkapi dengan **comprehensive testing suite** mencakup 197+ tests:

| Test Type | Status | Count | Coverage |
|-----------|--------|-------|----------|
| **API Tests** | ✅ **100% PASS** | 115 tests | Auth, CRUD, Validation |
| **Unit Tests** | ⚠️ Partial | 41 tests | Models & Logic |
| **Integration** | ⚠️ Partial | 13 tests | Services |
| **E2E Tests** | ✅ **PASS** | 23 tests | User Workflows |
| **Performance** | ✅ **100% PASS** | 5 suites | Load, Stress, Spike |

**Overall Success Rate**: 97.5% (192/197 tests passing)

### Quick Start Testing

```powershell
# API Tests (PHPUnit)
.\vendor\bin\phpunit.bat --testsuite=Api --testdox

# E2E Tests (Playwright)
npx playwright test

# Performance Tests (K6)
.\tests\Performance\run-tests.ps1
```

**📖 Untuk detail lengkap**, lihat: [TESTING-DOCUMENTATION.md](TESTING-DOCUMENTATION.md)

---

## Installation

### Prerequisites

- PHP 8.1+
- Composer
- MySQL 5.7+
- Node.js 18+
- K6 (optional, for performance testing)

### Steps

1. Clone repository:
```bash
git clone <repository-url>
cd E2E-JTIintern-PMPL
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Setup database:
```bash
php artisan migrate
php artisan db:seed
```

5. Run development server:
```bash
php artisan serve
npm run dev
```

---

## Configuration

### Database

Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=root
DB_PASSWORD=
```

### Testing Configuration

Lihat [TESTING-DOCUMENTATION.md](TESTING-DOCUMENTATION.md) untuk:
- PHPUnit configuration
- Playwright setup
- K6 performance testing
- CI/CD integration

---

## Contributing

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Run tests to ensure quality
4. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
5. Push to the Branch (`git push origin feature/AmazingFeature`)
6. Open a Pull Request

### Testing Before Commit

```powershell
# Run all API tests
.\vendor\bin\phpunit.bat --testsuite=Api

# Run E2E tests
npx playwright test

# Optional: Performance check
.\tests\Performance\run-tests.ps1 -TestType quick
```

---

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## Support & Contact

- **Documentation**: See [TESTING-DOCUMENTATION.md](TESTING-DOCUMENTATION.md)
- **Issues**: GitHub Issues
- **Production**: https://afws.my.id/E2E-JTIintern-PMPL

---

**Built with**: Laravel 10 | PHPUnit | Playwright | K6 | MySQL  
**Last Updated**: 27 Desember 2025
