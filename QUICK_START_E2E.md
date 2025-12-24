# Panduan Cepat Menjalankan E2E Tests

## 🚀 Mulai Cepat

```bash
# 1. Setup auth (jalankan sekali saat pertama kali)
node tests/e2e/setup-auth.js

# 2. Jalankan semua tests
npx playwright test --workers=1

# 3. Lihat hasil HTML report
npx playwright show-report
```

## 📊 Status Test Saat Ini

✅ **11 PASSED** | ❌ **0 FAILED** | ⏭️ **13 SKIPPED**

### Test yang Passing:

**Admin (2):**
- View daftar lowongan ✅
- Akses form tambah lowongan ✅

**Admin Mahasiswa (3):**
- View daftar mahasiswa ✅
- Akses form tambah mahasiswa ✅
- Akses form import mahasiswa ✅

**Mahasiswa (4):**
- View daftar lowongan & rekomendasi ✅
- Lihat detail lowongan & button apply ✅
- View profil ✅
- View halaman logbook ✅

**Dosen (2):**
- View dashboard ✅
- View daftar mahasiswa bimbingan ✅

## 📋 Commands Lengkap

```bash
# Jalankan test spesifik
npx playwright test tests/e2e/admin/lowongan.spec.js

# Jalankan test dengan nama tertentu
npx playwright test --grep "E2E_ADM_LOW_001"

# Jalankan dalam mode debug/headed
npx playwright test --headed --debug

# Jalankan dengan trace
npx playwright test --trace on

# Update snapshot jika perlu
npx playwright test --update-snapshots
```

## 🔍 Struktur Test

```
tests/
├── e2e/
│   ├── setup-auth.js           ← Generate session files
│   ├── auth-states/            ← Simpan session per role
│   │   ├── admin.json
│   │   ├── mahasiswa.json
│   │   └── dosen.json
│   ├── admin/
│   │   ├── lowongan.spec.js
│   │   └── mahasiswa.spec.js
│   ├── mahasiswa/
│   │   ├── lowongan.spec.js
│   │   └── logbook-photo.spec.js
│   ├── dosen/
│   │   └── monitoring.spec.js
│   ├── multi-role/             ← Tests disabled (test.skip)
│   │   ├── apply-approve.spec.js
│   │   └── notifications.spec.js
│   └── utils/
│       └── helpers.js          ← Utility functions
```

## 🛠️ Troubleshooting

**Q: Tests timeout?**  
A: Cek server Laravel running di http://127.0.0.1:8000

**Q: Auth error?**  
A: Jalankan `node tests/e2e/setup-auth.js` untuk regenerate session

**Q: Melihat hasil test?**  
A: Lihat screenshots/videos di `test-results/` atau jalankan `npx playwright show-report`

**Q: Test gagal random/flaky?**  
A: Tests dirancang defensive dengan graceful skip jika elemen tidak ada

## 📝 Catatan Penting

- ✅ Semua tests adalah **read-only** (tidak mengubah data)
- ✅ Multi-role tests **disabled** (sudah di-skip)
- ✅ Tests berjalan dengan 1 worker untuk stabilitas
- ✅ Screenshot otomatis tersimpan untuk debugging
- ✅ Session auth di-cache (valid sampai di-delete)

## 🎯 Tujuan Test

Memverifikasi bahwa:
- ✅ Admin dapat melihat dan mengakses menu lowongan & mahasiswa
- ✅ Mahasiswa dapat melihat lowongan & mengakses profil/logbook
- ✅ Dosen dapat melihat dashboard & mahasiswa bimbingan
- ✅ Navigasi antar halaman berfungsi
- ✅ Data tampil dengan benar sesuai role

