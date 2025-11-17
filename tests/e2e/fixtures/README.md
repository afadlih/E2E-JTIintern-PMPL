# Test Fixtures

Folder ini berisi dummy files untuk testing upload functionality.

## Files yang Diperlukan

Buat file-file berikut untuk testing:

### 1. dummy-cv.pdf
CV dummy untuk testing upload CV mahasiswa.

```bash
# Cara membuat dengan PowerShell
"Dummy CV Content for Testing" | Out-File -FilePath dummy-cv.pdf -Encoding utf8
```

### 2. dummy-photo.jpg
Foto dummy untuk testing upload foto profil atau logbook.

Download sample image atau gunakan:
```bash
# Download sample image
curl -o dummy-photo.jpg https://via.placeholder.com/150
```

### 3. dummy-document.pdf
Dokumen pendukung dummy untuk testing upload dokumen lamaran.

```bash
"Dummy Document Content" | Out-File -FilePath dummy-document.pdf -Encoding utf8
```

### 4. dummy-mahasiswa.csv
File CSV untuk testing import mahasiswa (Admin).

```csv
nim,nama,email,kelas_id,prodi_id,no_hp
2141720001,Test Mahasiswa 1,test1@polinema.ac.id,1,1,081234567890
2141720002,Test Mahasiswa 2,test2@polinema.ac.id,1,1,081234567891
2141720003,Test Mahasiswa 3,test3@polinema.ac.id,2,1,081234567892
2141720004,Test Mahasiswa 4,test4@polinema.ac.id,2,1,081234567893
2141720005,Test Mahasiswa 5,test5@polinema.ac.id,3,1,081234567894
```

## Catatan

- File-file ini tidak di-commit ke repository (lihat .gitignore)
- Setiap developer harus membuat file-file ini di local
- File harus ada sebelum menjalankan test yang memerlukan upload

## Generate All Fixtures (PowerShell)

Jalankan script berikut untuk generate semua fixtures:

```powershell
# Navigate to fixtures folder
cd tests/fixtures

# Create dummy CV
@"
CURRICULUM VITAE
================

Nama: Test Mahasiswa
NIM: 2141720001
Email: test@polinema.ac.id
Telepon: 081234567890

PENDIDIKAN
----------
Politeknik Negeri Malang
D4 Teknik Informatika
IPK: 3.75

KETERAMPILAN
------------
- Java Programming
- PHP & Laravel
- JavaScript & React
- Database (MySQL, PostgreSQL)

PENGALAMAN
----------
1. Magang di PT Example (2024)
   - Mengembangkan sistem internal
   - Maintenance database

SERTIFIKASI
-----------
- Oracle Certified Associate (2023)
- AWS Cloud Practitioner (2024)
"@ | Out-File -FilePath dummy-cv.pdf -Encoding utf8

# Create dummy document
"Dokumen Pendukung Lamaran Magang" | Out-File -FilePath dummy-document.pdf -Encoding utf8

# Create CSV for import
@"
nim,nama,email,kelas_id,prodi_id,no_hp
2141720001,Test Mahasiswa 1,test1@polinema.ac.id,1,1,081234567890
2141720002,Test Mahasiswa 2,test2@polinema.ac.id,1,1,081234567891
2141720003,Test Mahasiswa 3,test3@polinema.ac.id,2,1,081234567892
2141720004,Test Mahasiswa 4,test4@polinema.ac.id,2,1,081234567893
2141720005,Test Mahasiswa 5,test5@polinema.ac.id,3,1,081234567894
"@ | Out-File -FilePath dummy-mahasiswa.csv -Encoding utf8

Write-Host "✅ All test fixtures created successfully!"
```

## Verifikasi

Setelah generate, pastikan file-file berikut ada:

```powershell
Get-ChildItem tests/fixtures
```

Output yang diharapkan:
- dummy-cv.pdf
- dummy-document.pdf
- dummy-mahasiswa.csv
- (dummy-photo.jpg - download manual)
