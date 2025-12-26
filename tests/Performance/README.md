# K6 Performance Testing Suite

Dokumentasi lengkap untuk performance testing menggunakan K6 pada aplikasi E2E-JTIintern-PMPL yang sudah di-deploy di **https://afws.my.id/login**

## 📋 Daftar Isi

- [Instalasi](#instalasi)
- [Struktur Test](#struktur-test)
- [Cara Menjalankan](#cara-menjalankan)
- [Tipe Testing](#tipe-testing)
- [Interpretasi Hasil](#interpretasi-hasil)
- [Best Practices](#best-practices)
- [Troubleshooting](#troubleshooting)

---

## 🚀 Instalasi

### Windows (Chocolatey)
```powershell
choco install k6
```

### Windows (MSI Installer)
Download dari: https://dl.k6.io/msi/k6-latest-amd64.msi

### Windows (Manual)
```powershell
# Download binary
Invoke-WebRequest -Uri "https://github.com/grafana/k6/releases/latest/download/k6-v0.48.0-windows-amd64.zip" -OutFile "k6.zip"

# Extract
Expand-Archive -Path "k6.zip" -DestinationPath "C:\k6"

# Add to PATH
$env:Path += ";C:\k6"
```

### Verifikasi Instalasi
```powershell
k6 version
```

---

## 📁 Struktur Test

```
tests/Performance/
├── README.md              # Dokumentasi ini
├── smoke-test.js          # Smoke test - validasi cepat (1 user, 30s)
├── load-test-basic.js     # Load test dasar (10 users, 2 min)
├── stress-test.js         # Stress test (100 users, 10 min)
├── spike-test.js          # Spike test (200 users sudden, 2 min)
└── soak-test.js          # Soak/Endurance test (20 users, 12 min)
```

---

## 🎯 Cara Menjalankan

### Menjalankan Single Test

```powershell
# Smoke Test (Quick validation)
k6 run tests/Performance/smoke-test.js

# Load Test (Basic load testing)
k6 run tests/Performance/load-test-basic.js

# Stress Test (High load testing)
k6 run tests/Performance/stress-test.js

# Spike Test (Sudden traffic spike)
k6 run tests/Performance/spike-test.js

# Soak Test (Endurance testing)
k6 run tests/Performance/soak-test.js
```

### Menjalankan dengan Output JSON

```powershell
k6 run --out json=result.json tests/Performance/load-test-basic.js
```

### Menjalankan dengan Custom VUs

```powershell
# Override virtual users
k6 run --vus 50 --duration 5m tests/Performance/load-test-basic.js
```

### Menjalankan Semua Test (Sequential)

```powershell
# Menggunakan script otomatis (RECOMMENDED)
.\tests\Performance\run-all-tests.ps1
```

Script ini akan:
- ✅ Run semua 5 tests secara berurutan
- ✅ Capture semua metrics (response time, error rate, dll)
- ✅ Generate laporan lengkap dalam format Markdown
- ✅ Tampilkan ringkasan hasil di console
- ✅ Save results ke `results/` directory

**Output:**
- Console: Ringkasan hasil yang siap di-copy untuk laporan
- File: `results/PERFORMANCE-TEST-REPORT-[DATE].md`

**Manual (alternatif):**
```powershell
# PowerShell script
$tests = @(
    "smoke-test.js",
    "load-test-basic.js",
    "stress-test.js",
    "spike-test.js",
    "soak-test.js"
)

foreach ($test in $tests) {
    Write-Host "Running $test..." -ForegroundColor Cyan
    k6 run "tests/Performance/$test"
    Write-Host "`n================================`n" -ForegroundColor Green
    Start-Sleep -Seconds 5
}
```

---

## 🧪 Tipe Testing

### 1. **Smoke Test** (`smoke-test.js`)
**Tujuan:** Validasi cepat bahwa sistem berfungsi normal

**Konfigurasi:**
- Virtual Users: 1
- Duration: 30 seconds
- Target URL: https://afws.my.id

**Thresholds:**
- Response time p95: < 1.5s
- Error rate: < 1%

**Kapan Digunakan:**
- Setelah deployment baru
- Sebelum menjalankan load test yang lebih berat
- Quick sanity check

**Perintah:**
```powershell
k6 run tests/Performance/smoke-test.js
```

---

### 2. **Load Test Basic** (`load-test-basic.js`)
**Tujuan:** Mengukur performa sistem dengan beban normal

**Konfigurasi:**
- Virtual Users: 10 concurrent
- Duration: 2 minutes
- Stages:
  - Ramp-up: 30s (0 → 10 users)
  - Sustain: 1m (10 users)
  - Ramp-down: 30s (10 → 0 users)

**Thresholds:**
- Response time p95: < 2s
- Error rate: < 10%

**Test Scenarios:**
1. Homepage/Login page load
2. Static assets (CSS, JS)
3. API health check

**Perintah:**
```powershell
k6 run tests/Performance/load-test-basic.js
```

**Expected Output:**
```
✓ status is 200
✓ page loads in reasonable time
✓ static assets load
✓ API responds
```

---

### 3. **Stress Test** (`stress-test.js`)
**Tujuan:** Menemukan breaking point sistem

**Konfigurasi:**
- Virtual Users: Progressive 20 → 50 → 100
- Duration: 10 minutes
- Stages:
  1. Ramp to 20 users (2m)
  2. Hold at 20 users (2m)
  3. Ramp to 50 users (2m)
  4. Hold at 50 users (1m)
  5. Ramp to 100 users (1m)
  6. Ramp down (2m)

**Thresholds:**
- Response time p95: < 5s
- Error rate: < 30% (acceptable under stress)

**Test Scenarios:**
1. Login page under stress
2. Concurrent multiple requests
3. API endpoint stress

**Custom Metrics:**
- Error rate
- Success rate
- Total requests counter
- Page duration trend

**Perintah:**
```powershell
k6 run tests/Performance/stress-test.js
```

**Expected Behavior:**
- System should degrade gracefully
- Error rate should increase gradually
- Response time should increase but stay predictable

---

### 4. **Spike Test** (`spike-test.js`)
**Tujuan:** Mengukur respon sistem terhadap lonjakan traffic mendadak

**Konfigurasi:**
- Virtual Users: 0 → 200 (sudden spike in 10s)
- Duration: 2 minutes
- Stages:
  1. Spike: 0 → 200 users (10s)
  2. Hold: 200 users (1m)
  3. Recovery: 200 → 0 users (50s)

**Thresholds:**
- Response time p95: < 10s
- Error rate: < 50% (tolerance during spike)

**Test Scenarios:**
1. Service availability check
2. Connection handling
3. Recovery time measurement

**Custom Metrics:**
- Error rate during spike
- Recovery time indicator

**Perintah:**
```powershell
k6 run tests/Performance/spike-test.js
```

**Expected Behavior:**
- System may struggle during initial spike
- Should stabilize after spike
- Should recover gracefully

---

### 5. **Soak Test** (`soak-test.js`)
**Tujuan:** Mendeteksi memory leaks dan performance degradation jangka panjang

**Konfigurasi:**
- Virtual Users: 20 sustained
- Duration: 12 minutes (long-running)
- Constant load throughout

**Thresholds:**
- Response time p95: < 3s (strict)
- Response time p99: < 5s
- Error rate: < 5%

**Test Scenarios:**
1. User behavior simulation:
   - Login
   - Load static assets
   - API calls
   - Browse pages
2. Response time degradation monitoring
3. Memory leak indicators

**Custom Metrics:**
- Memory leak indicator (duration trend)
- Total iterations counter

**Perintah:**
```powershell
k6 run tests/Performance/soak-test.js
```

**Expected Behavior:**
- Response time should remain stable
- No gradual degradation
- Memory usage should be stable

---

## 📊 Interpretasi Hasil

### K6 Output Metrics

```
data_received..................: 156 MB 1.3 MB/s
data_sent......................: 13 MB  108 kB/s
http_req_blocked...............: avg=1.4ms   min=0s     med=1ms    max=145ms   p(90)=2ms    p(95)=3ms
http_req_connecting............: avg=0.6ms   min=0s     med=0s     max=115ms   p(90)=1ms    p(95)=1ms
http_req_duration..............: avg=234ms   min=12ms   med=220ms  max=1.2s    p(90)=345ms  p(95)=456ms
http_req_failed................: 2.43% ✓ 243  ✗ 9757
http_req_receiving.............: avg=1.2ms   min=0s     med=1ms    max=89ms    p(90)=2ms    p(95)=3ms
http_req_sending...............: avg=0.05ms  min=0s     med=0s     max=12ms    p(90)=0s     p(95)=0s
http_req_tls_handshaking.......: avg=0s      min=0s     med=0s     max=0s      p(90)=0s     p(95)=0s
http_req_waiting...............: avg=232ms   min=11ms   med=218ms  max=1.2s    p(90)=343ms  p(95)=453ms
http_reqs......................: 10000  83.3/s
iteration_duration.............: avg=1.2s    min=1.01s  med=1.2s   max=2.4s    p(90)=1.4s   p(95)=1.5s
iterations.....................: 1000   8.33/s
vus............................: 10     min=10 max=10
vus_max........................: 10     min=10 max=10
```

### Metrik Penting

| Metrik | Deskripsi | Good Value |
|--------|-----------|------------|
| **http_req_duration** | Waktu total request | < 2s (p95) |
| **http_req_failed** | Percentage request gagal | < 5% |
| **http_reqs** | Total requests | Higher = better throughput |
| **iterations** | Total test iterations | Should complete all |
| **vus** | Virtual users active | Should match config |

### Status Threshold

- ✓ **Green Check**: Threshold passed
- ✗ **Red Cross**: Threshold failed

### Response Time Guidelines

| Type | Good | Acceptable | Poor |
|------|------|------------|------|
| **Homepage** | < 1s | 1-3s | > 3s |
| **API Calls** | < 500ms | 500ms-2s | > 2s |
| **Static Assets** | < 200ms | 200ms-1s | > 1s |

---

## 🎨 Best Practices

### 1. **Test Sequence**
Jalankan test dalam urutan berikut:
```
Smoke → Load → Stress → Spike → Soak
```

### 2. **Timing**
- Jalankan saat traffic rendah (malam hari)
- Beri jeda 5-10 menit antar test
- Monitor server resources

### 3. **Baseline**
Jalankan load test dulu untuk establish baseline:
```powershell
k6 run tests/Performance/load-test-basic.js
```

### 4. **Incremental Testing**
Jangan langsung ke stress test maksimal:
```powershell
# Start low
k6 run --vus 5 --duration 1m tests/Performance/load-test-basic.js

# Increase gradually
k6 run --vus 10 --duration 2m tests/Performance/load-test-basic.js
k6 run --vus 20 --duration 3m tests/Performance/load-test-basic.js
```

### 5. **Monitoring**
Selalu monitor server side metrics:
- CPU usage
- Memory usage
- Network bandwidth
- Database connections
- Application logs

### 6. **Documentation**
Simpan hasil test untuk reference:
```powershell
# Save to file
k6 run tests/Performance/load-test-basic.js | Out-File -FilePath "results\load-test-$(Get-Date -Format 'yyyy-MM-dd-HHmm').txt"

# Save JSON
k6 run --out json=results/load-test.json tests/Performance/load-test-basic.js
```

---

## 🔧 Troubleshooting

### Error: "k6: command not found"

**Solution:**
```powershell
# Verify PATH
$env:Path -split ';' | Select-String k6

# Re-add to PATH if needed
$env:Path += ";C:\Program Files\k6"

# Restart PowerShell
```

### High Error Rates

**Possible Causes:**
1. Server capacity exceeded
2. Network issues
3. Authentication problems
4. Rate limiting

**Debug:**
```powershell
# Run with debug output
k6 run --http-debug tests/Performance/load-test-basic.js

# Run with single user first
k6 run --vus 1 --duration 30s tests/Performance/load-test-basic.js
```

### Timeouts

**Solution:**
```javascript
// Add to test file
export const options = {
  httpDebug: 'full',
  insecureSkipTLSVerify: true,
  noConnectionReuse: false,
  timeout: '60s', // Increase timeout
};
```

### SSL/TLS Errors

**Solution:**
```powershell
# Skip TLS verification (not recommended for production)
k6 run --insecure-skip-tls-verify tests/Performance/load-test-basic.js
```

### Memory Issues (Soak Test)

**Solution:**
```powershell
# Reduce VUs or duration
k6 run --vus 10 --duration 5m tests/Performance/soak-test.js
```

---

## 📈 Analisis Hasil

### Load Test Results

**Good Performance:**
```
✓ http_req_duration.............: avg=234ms p(95)=456ms
✓ http_req_failed...............: 2.43%
✓ All thresholds passed
```

**Poor Performance:**
```
✗ http_req_duration.............: avg=5.2s p(95)=12s
✗ http_req_failed...............: 45.3%
✗ Thresholds failed: http_req_duration, http_req_failed
```

### Action Items Based on Results

| Result | Action |
|--------|--------|
| **High response time** | Optimize database queries, add caching |
| **High error rate** | Check server logs, increase capacity |
| **Memory leak** | Profile application, fix memory leaks |
| **Spike failures** | Add auto-scaling, implement rate limiting |

---

## 🎯 Target Metrics (SLA)

### Production Targets

| Metric | Target | Critical |
|--------|--------|----------|
| **Response Time (p95)** | < 2s | < 5s |
| **Error Rate** | < 1% | < 5% |
| **Availability** | > 99.9% | > 99% |
| **Concurrent Users** | 100+ | 50+ |

### Test-Specific Targets

| Test Type | Users | Duration | Expected p95 | Max Error Rate |
|-----------|-------|----------|--------------|----------------|
| Smoke | 1 | 30s | < 1.5s | < 1% |
| Load | 10 | 2m | < 2s | < 10% |
| Stress | 100 | 10m | < 5s | < 30% |
| Spike | 200 | 2m | < 10s | < 50% |
| Soak | 20 | 12m | < 3s | < 5% |

---

## 📚 Resources

### K6 Documentation
- Official Docs: https://k6.io/docs/
- Examples: https://github.com/grafana/k6-learn
- Community: https://community.k6.io/

### Performance Testing
- Load Testing Guide: https://k6.io/docs/test-types/load-testing/
- Stress Testing: https://k6.io/docs/test-types/stress-testing/
- Spike Testing: https://k6.io/docs/test-types/spike-testing/
- Soak Testing: https://k6.io/docs/test-types/soak-testing/

### Deployment Target
- Production URL: https://afws.my.id/login
- Login Endpoint: https://afws.my.id/api/login

---

## 📄 Generate Report untuk Laporan

### Cara Mendapatkan Report Lengkap

```powershell
# Run all tests dengan auto-generate report
.\tests\Performance\run-all-tests.ps1
```

**Output yang Dihasilkan:**

1. **Console Output** (Terminal)
   ```
   ═══════════════════════════════════════════════════
     HCara Memasukkan Hasil ke Laporan

### Step-by-Step

1. **Run Test dengan Script**
   ```powershell
   .\tests\Performance\run-all-tests.ps1
   ```

2. **Screenshot Console Output**
   - Scroll ke bagian "HASIL RINGKASAN (COPY TO LAPORAN)"
   - Screenshot tabel hasil
   - Paste ke dokumen laporan

3. **Copy Markdown Report**
   - Buka file: `results/PERFORMANCE-TEST-REPORT-[DATE].md`
   - Copy semua atau sebagian yang dibutuhkan
   - Paste ke dokumen laporan (Word/PDF)

4. **Customize Report**
   - Edit bagian "[Nama Tester]" dengan nama Anda
   - Tambahkan interpretasi sesuai kebutuhan
   - Sesuaikan rekomendasi jika perlu

### Format Laporan Singkat (untuk PPT/Summary)

```markdown
## HASIL PENGUJIAN KINERJA

**Target:** https://afws.my.id/login  
**Tanggal:** [DATE]  
**Status:** ✅ PASSED (5/5 tests)

| Test | Status | Duration | Error Rate |
|------|--------|----------|------------|
| Smoke | ✅ | 30s | 0.5% |
| Load | ✅ | 2m | 2.3% |
| Stress | ✅ | 10m | 15.7% |
| Spike | ✅ | 2m | 35.2% |
| Soak | ✅ | 12m | 3.5% |

**Kesimpulan:** Aplikasi production ready dengan performa baik.
```

### Format Laporan Lengkap (untuk Dokumentasi)

Gunakan file auto-generated: `results/PERFORMANCE-TEST-REPORT-[DATE].md`

Includes:
- ✅ Executive summary
- ✅ Detail metrics per test
- ✅ Analisis hasil
- ✅ Rekomendasi
- ✅ Lampiran konfigurasi
### Contoh Report

Lihat contoh report lengkap di: [REPORT-EXAMPLE.md](REPORT-EXAMPLE.md)

### Customize Report

```powershell
# Custom output directory
.\tests\Performance\run-all-tests.ps1 -OutputDir "laporan-hasil"

# Tidak generate report (hanya run test)
.\tests\Performance\run-all-tests.ps1 -GenerateReport $false

# Custom delay antar test
.\tests\Performance\run-all-tests.ps1 -DelayBetweenTests 10
```

---

## ✅ Quick Start Checklist

- [ ] Install K6
- [ ] Verify installation: `k6 version`
- [ ] Run smoke test first: `k6 run tests/Performance/smoke-test.js`
- [ ] Run all tests dengan report: `.\tests\Performance\run-all-tests.ps1`
- [ ] Review hasil di console (screenshot untuk laporan)
- [ ] Buka file report: `results/PERFORMANCE-TEST-REPORT-[DATE].md`
- [ ] Copy hasil ke dokumen laporan
- [ ] Document baseline metrics
- [ ] Monitor server resources
- [ ] Save results for comparison

---

## 📝 Report Template

```markdown
# Performance Test Report
Date: 2024-XX-XX
Tester: [Your Name]
Environment: Production (https://afws.my.id)

## Test Summary
- Test Type: Load Test
- Duration: 2 minutes
- Virtual Users: 10
- Total Requests: 10,000
- Error Rate: 2.43%

## Results
- Response Time (p95): 456ms ✓
- Response Time (p99): 678ms ✓
- Error Rate: 2.43% ✓
- Throughput: 83.3 req/s ✓

## Threshold Status
✓ All thresholds passed

## Recommendations
- Performance is acceptable
- Consider increasing cache TTL
- Monitor during peak hours

## Next Steps
- Run stress test
- Set up continuous monitoring
- Schedule weekly load tests
```

---

**Created:** 2024-12-26  
**Last Updated:** 2024-12-26  
**Version:** 1.0.0  
**Target Application:** E2E-JTIintern-PMPL  
**Deployment URL:** https://afws.my.id/login
