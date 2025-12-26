<#
.SYNOPSIS
    Run all K6 performance tests sequentially

.DESCRIPTION
    This script runs all K6 performance tests in recommended order:
    1. Smoke Test - Quick validation
    2. Load Test - Normal load
    3. Stress Test - High load
    4. Spike Test - Sudden traffic spike
    5. Soak Test - Long-running endurance

.PARAMETER OutputDir
    Directory to save test results (default: results/)

.PARAMETER SaveResults
    Whether to save results to files (default: $true)

.PARAMETER DelayBetweenTests
    Seconds to wait between tests (default: 5)

.PARAMETER GenerateReport
    Generate detailed report for documentation (default: $true)

.EXAMPLE
    .\run-all-tests.ps1
    Run all tests with default settings

.EXAMPLE
    .\run-all-tests.ps1 -OutputDir "test-results" -DelayBetweenTests 10
    Run all tests with custom output directory and 10s delay
#>

param(
    [string]$OutputDir = "results",
    [bool]$SaveResults = $true,
    [int]$DelayBetweenTests = 5,
    [bool]$GenerateReport = $true
)

# Color functions
function Write-Success { Write-Host $args -ForegroundColor Green }
function Write-Info { Write-Host $args -ForegroundColor Cyan }
function Write-Warning { Write-Host $args -ForegroundColor Yellow }
function Write-Error { Write-Host $args -ForegroundColor Red }

# Check if k6 is installed
function Test-K6Installed {
    try {
        $version = k6 version 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-Success "✓ K6 is installed: $version"
            return $true
        }
    }
    catch {
        Write-Error "✗ K6 is not installed"
        Write-Info "Install K6:"
        Write-Info "  - Chocolatey: choco install k6"
        Write-Info "  - Download: https://dl.k6.io/msi/k6-latest-amd64.msi"
        return $false
    }
}

# Create output directory
if ($SaveResults) {
    if (-not (Test-Path $OutputDir)) {
        New-Item -ItemType Directory -Path $OutputDir | Out-Null
        Write-Info "Created output directory: $OutputDir"
    }
}

# Test definitions
$tests = @(
    @{
        Name = "Smoke Test"
        File = "smoke-test.js"
        Description = "Quick validation (1 user, 30s)"
        Icon = "[SMOKE]"
        Duration = "30s"
    },
    @{
        Name = "Load Test"
        File = "load-test-basic.js"
        Description = "Normal load (10 users, 2m)"
        Icon = "[LOAD]"
        Duration = "2m"
    },
    @{
        Name = "Stress Test"
        File = "stress-test.js"
        Description = "High load (100 users, 10m)"
        Icon = "[STRESS]"
        Duration = "10m"
    },
    @{
        Name = "Spike Test"
        File = "spike-test.js"
        Description = "Traffic spike (200 users, 2m)"
        Icon = "[SPIKE]"
        Duration = "2m"
    },
    @{
        Name = "Soak Test"
        File = "soak-test.js"
        Description = "Endurance (20 users, 12m)"
        Icon = "[SOAK]"
        Duration = "12m"
    }
)

# Main execution
Write-Host ""
Write-Host "═══════════════════════════════════════════════════" -ForegroundColor Magenta
Write-Host "  K6 Performance Testing Suite" -ForegroundColor Magenta
Write-Host "═══════════════════════════════════════════════════" -ForegroundColor Magenta
Write-Host ""
Write-Host "Target: https://afws.my.id/login" -ForegroundColor Yellow
Write-Host "Total Tests: $($tests.Count)" -ForegroundColor Yellow
Write-Host ""

# Check K6 installation
if (-not (Test-K6Installed)) {
    exit 1
}

Write-Host ""
Write-Host "Starting test execution..." -ForegroundColor Cyan
Write-Host ""

$totalTests = $tests.Count
$completedTests = 0
$failedTests = @()
$testResults = @()
$startTime = Get-Date

foreach ($test in $tests) {
    $testNumber = $completedTests + 1
    
    Write-Host "─────────────────────────────────────────────────" -ForegroundColor DarkGray
    Write-Host "$($test.Icon) Test $testNumber/$totalTests : $($test.Name)" -ForegroundColor Cyan
    Write-Host "─────────────────────────────────────────────────" -ForegroundColor DarkGray
    Write-Info "Description: $($test.Description)"
    Write-Info "Duration: $($test.Duration)"
    Write-Info "File: $($test.File)"
    Write-Host ""
    
    $testStartTime = Get-Date
    
    # Build k6 command
    $k6Command = "k6 run tests/Performance/$($test.File)"
    
    # Add JSON output if saving results
    if ($SaveResults) {
        $timestamp = Get-Date -Format "yyyy-MM-dd-HHmmss"
        $outputFile = Join-Path $OutputDir "$($test.File.Replace('.js', ''))-$timestamp.json"
        $k6Command += " --out json=$outputFile"
        Write-Info "Results will be saved to: $outputFile"
    }
    
    Write-Host ""
    Write-Host "Running test..." -ForegroundColor Yellow
    Write-Host ""
    
    # Execute k6 test and capture output
    try {
        $k6Output = Invoke-Expression $k6Command 2>&1 | Out-String
        Write-Host $k6Output
        
        if ($LASTEXITCODE -eq 0) {
            $testEndTime = Get-Date
            $testDuration = ($testEndTime - $testStartTime).ToString("mm\:ss")
            
            # Parse K6 output for metrics
            $metrics = @{
                TestName = $test.Name
                Status = "PASSED"
                Duration = $testDuration
                ResponseTimeAvg = "N/A"
                ResponseTimeP95 = "N/A"
                ResponseTimeP99 = "N/A"
                ErrorRate = "N/A"
                TotalRequests = "N/A"
                RequestsPerSec = "N/A"
            }
            
            # Extract metrics from output
            if ($k6Output -match "http_req_duration.*?avg=([^\s]+)") { $metrics.ResponseTimeAvg = $Matches[1] }
            if ($k6Output -match "http_req_duration.*?p\(95\)=([^\s]+)") { $metrics.ResponseTimeP95 = $Matches[1] }
            if ($k6Output -match "http_req_duration.*?p\(99\)=([^\s]+)") { $metrics.ResponseTimeP99 = $Matches[1] }
            if ($k6Output -match "http_req_failed.*?(\d+\.?\d*)%") { $metrics.ErrorRate = $Matches[1] + "%" }
            if ($k6Output -match "http_reqs.*?(\d+)") { $metrics.TotalRequests = $Matches[1] }
            if ($k6Output -match "http_reqs.*?(\d+\.?\d*)/s") { $metrics.RequestsPerSec = $Matches[1] }
            
            $testResults += $metrics
            
            Write-Host ""
            Write-Success "✓ $($test.Name) completed successfully (Duration: $testDuration)"
            $completedTests++
        }
        else {
            $testEndTime = Get-Date
            $testDuration = ($testEndTime - $testStartTime).ToString("mm\:ss")
            
            $metrics = @{
                TestName = $test.Name
                Status = "FAILED"
                Duration = $testDuration
                ResponseTimeAvg = "N/A"
                ResponseTimeP95 = "N/A"
                ResponseTimeP99 = "N/A"
                ErrorRate = "N/A"
                TotalRequests = "N/A"
                RequestsPerSec = "N/A"
            }
            $testResults += $metrics
            
            Write-Host ""
            Write-Error "✗ $($test.Name) failed (Exit code: $LASTEXITCODE)"
            $failedTests += $test.Name
            $completedTests++
        }
    }
    catch {
        $testEndTime = Get-Date
        $testDuration = ($testEndTime - $testStartTime).ToString("mm\:ss")
        
        $metrics = @{
            TestName = $test.Name
            Status = "ERROR"
            Duration = $testDuration
            ResponseTimeAvg = "N/A"
            ResponseTimeP95 = "N/A"
            ResponseTimeP99 = "N/A"
            ErrorRate = "N/A"
            TotalRequests = "N/A"
            RequestsPerSec = "N/A"
        }
        $testResults += $metrics
        
        Write-Host ""
        Write-Error "✗ $($test.Name) failed with error: $_"
        $failedTests += $test.Name
        $completedTests++
    }
    
    # Wait between tests (except for last test)
    if ($testNumber -lt $totalTests) {
        Write-Host ""
        Write-Info "Waiting $DelayBetweenTests seconds before next test..."
        Start-Sleep -Seconds $DelayBetweenTests
        Write-Host ""
    }
}

$endTime = Get-Date
$totalDuration = ($endTime - $startTime).ToString("hh\:mm\:ss")

# Summary
Write-Host ""
Write-Host "═══════════════════════════════════════════════════" -ForegroundColor Magenta
Write-Host "  Test Execution Summary" -ForegroundColor Magenta
Write-Host "═══════════════════════════════════════════════════" -ForegroundColor Magenta
Write-Host ""
Write-Host "Total Tests: $totalTests" -ForegroundColor Yellow
Write-Success "Completed: $completedTests"
if ($failedTests.Count -gt 0) {
    Write-Error "Failed: $($failedTests.Count)"
    Write-Host ""
    Write-Host "Failed tests:" -ForegroundColor Red
    foreach ($failed in $failedTests) {
        Write-Host "  ✗ $failed" -ForegroundColor Red
    }
}
else {
    Write-Success "Failed: 0"
}
Write-Host ""
Write-Host "Total Duration: $totalDuration" -ForegroundColor Yellow

if ($SaveResults) {
    Write-Host ""
    Write-Info "Results saved to: $OutputDir"
}

Write-Host ""

# Generate detailed report
if ($GenerateReport) {
    Write-Host "═══════════════════════════════════════════════════" -ForegroundColor Magenta
    Write-Host "  Generating Report..." -ForegroundColor Magenta
    Write-Host "═══════════════════════════════════════════════════" -ForegroundColor Magenta
    Write-Host ""
    
    $reportTimestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $reportDate = Get-Date -Format "yyyy-MM-dd"
    $reportFile = Join-Path $OutputDir "PERFORMANCE-TEST-REPORT-$reportDate.md"
    
    # Create report directory if not exists
    if (-not (Test-Path $OutputDir)) {
        New-Item -ItemType Directory -Path $OutputDir | Out-Null
    }
    
    # Build markdown report
    $report = @"
# LAPORAN PENGUJIAN KINERJA (PERFORMANCE TESTING)

**Aplikasi:** E2E-JTIintern-PMPL  
**Target URL:** https://afws.my.id/login  
**Tanggal Pengujian:** $reportTimestamp  
**Tool:** K6 (Grafana K6)  
**Tester:** [Nama Tester]

---

## EXECUTIVE SUMMARY

### Hasil Pengujian

"@
    
    # Add summary table
    $passedCount = ($testResults | Where-Object { $_.Status -eq "PASSED" }).Count
    $failedCount = ($testResults | Where-Object { $_.Status -ne "PASSED" }).Count
    
    $report += @"

| Metrik | Nilai |
|--------|-------|
| **Total Test Suite** | $totalTests |
| **Test Berhasil** | $passedCount ✅ |
| **Test Gagal** | $failedCount ❌ |
| **Success Rate** | $([math]::Round(($passedCount / $totalTests) * 100, 2))% |
| **Total Durasi** | $totalDuration |
| **Status Keseluruhan** | $(if ($failedCount -eq 0) { "**PASSED** ✅" } else { "**FAILED** ❌" }) |

---

## DETAIL HASIL PER TEST

"@
    
    # Add detailed results for each test
    foreach ($result in $testResults) {
        $statusIcon = if ($result.Status -eq "PASSED") { "✅" } else { "❌" }
        
        $report += @"

### $($result.TestName) $statusIcon

**Status:** $($result.Status)  
**Durasi:** $($result.Duration)

| Metrik | Nilai |
|--------|-------|
| Response Time (Avg) | $($result.ResponseTimeAvg) |
| Response Time (P95) | $($result.ResponseTimeP95) |
| Response Time (P99) | $($result.ResponseTimeP99) |
| Error Rate | $($result.ErrorRate) |
| Total Requests | $($result.TotalRequests) |
| Requests/sec | $($result.RequestsPerSec) |

"@
    }
    
    # Add analysis section
    $report += @"

---

## ANALISIS HASIL

### Kesimpulan

"@
    
    if ($failedCount -eq 0) {
        $report += @"
✅ **Semua test berhasil dijalankan**

Aplikasi menunjukkan performa yang baik pada semua skenario pengujian:
- ✓ Sistem berfungsi normal (Smoke Test)
- ✓ Menangani beban normal dengan baik (Load Test)
- ✓ Mampu menangani beban tinggi (Stress Test)
- ✓ Responsif terhadap lonjakan traffic (Spike Test)
- ✓ Stabil dalam penggunaan jangka panjang (Soak Test)

"@
    }
    else {
        $report += @"
⚠️ **Ditemukan $failedCount test yang gagal**

Test yang gagal:
"@
        foreach ($failed in $failedTests) {
            $report += "- ❌ $failed`n"
        }
        
        $report += @"

**Rekomendasi:**
- Review server capacity dan resource allocation
- Check application logs untuk error details
- Optimize database queries dan caching
- Consider horizontal scaling atau load balancing

"@
    }
    
    # Add recommendations
    $report += @"

### Rekomendasi

"@
    
    # Find highest P95 response time
    $highestP95 = ($testResults | Where-Object { $_.ResponseTimeP95 -ne "N/A" } | ForEach-Object { 
        if ($_.ResponseTimeP95 -match "(\d+\.?\d*)") { [double]$Matches[1] } 
    } | Measure-Object -Maximum).Maximum
    
    if ($highestP95 -gt 5000) {
        $report += "1. ⚠️ Response time tinggi terdeteksi (> 5s) - Perlu optimasi performa`n"
    }
    elseif ($highestP95 -gt 2000) {
        $report += "1. ℹ️ Response time dapat ditingkatkan (> 2s) - Pertimbangkan optimasi`n"
    }
    else {
        $report += "1. ✅ Response time dalam batas normal (< 2s)`n"
    }
    
    # Check error rates
    $highErrorRate = $testResults | Where-Object { 
        $_.ErrorRate -ne "N/A" -and $_.ErrorRate -match "(\d+\.?\d*)" -and [double]$Matches[1] -gt 10 
    }
    
    if ($highErrorRate) {
        $report += "2. ⚠️ Error rate tinggi terdeteksi (> 10%) - Review error logs`n"
    }
    else {
        $report += "2. ✅ Error rate dalam batas acceptable`n"
    }
    
    $report += @"
3. 📊 Lakukan monitoring berkelanjutan untuk performa aplikasi
4. 🔄 Jalankan performance test secara regular (mingguan/bulanan)
5. 📈 Simpan hasil test untuk tracking trend performa

---

## LAMPIRAN

### Test Configuration

| Test Type | Virtual Users | Duration | Threshold P95 | Max Error Rate |
|-----------|--------------|----------|---------------|----------------|
| Smoke Test | 1 | 30s | < 1.5s | < 1% |
| Load Test | 10 | 2m | < 2s | < 10% |
| Stress Test | 20→50→100 | 10m | < 5s | < 30% |
| Spike Test | 0→200 | 2m | < 10s | < 50% |
| Soak Test | 20 | 12m | < 3s | < 5% |

### Environment

- **Target URL:** https://afws.my.id/login
- **Testing Tool:** K6 (Grafana K6)
- **Test Location:** Local
- **Network:** [Specify network condition]
- **Date:** $reportDate

---

**Catatan:** Laporan ini di-generate otomatis oleh script run-all-tests.ps1

"@
    
    # Save report
    $report | Out-File -FilePath $reportFile -Encoding UTF8
    
    Write-Success "✓ Report generated: $reportFile"
    Write-Host ""
    
    # Display summary table in console
    Write-Host "═══════════════════════════════════════════════════" -ForegroundColor Magenta
    Write-Host "  HASIL RINGKASAN (COPY TO LAPORAN)" -ForegroundColor Magenta
    Write-Host "═══════════════════════════════════════════════════" -ForegroundColor Magenta
    Write-Host ""
    
    # Console-friendly summary table
    Write-Host "RINGKASAN HASIL PENGUJIAN KINERJA" -ForegroundColor Yellow
    Write-Host "Tanggal: $reportTimestamp" -ForegroundColor Cyan
    Write-Host "Target: https://afws.my.id/login" -ForegroundColor Cyan
    Write-Host ""
    
    # Results table
    $tableWidth = 70
    Write-Host ("─" * $tableWidth) -ForegroundColor DarkGray
    Write-Host ("{0,-30} {1,10} {2,10} {3,15}" -f "Test Name", "Status", "Duration", "Error Rate") -ForegroundColor Yellow
    Write-Host ("─" * $tableWidth) -ForegroundColor DarkGray
    
    foreach ($result in $testResults) {
        $statusDisplay = if ($result.Status -eq "PASSED") { "✅ PASS" } else { "❌ FAIL" }
        $color = if ($result.Status -eq "PASSED") { "Green" } else { "Red" }
        Write-Host ("{0,-30} {1,10} {2,10} {3,15}" -f $result.TestName, $statusDisplay, $result.Duration, $result.ErrorRate) -ForegroundColor $color
    }
    
    Write-Host ("─" * $tableWidth) -ForegroundColor DarkGray
    Write-Host ""
    
    # Summary stats
    Write-Host "STATISTIK:" -ForegroundColor Yellow
    Write-Host "  Total Tests: $totalTests"
    Write-Host "  Passed: $passedCount" -ForegroundColor Green
    Write-Host "  Failed: $failedCount" -ForegroundColor $(if ($failedCount -eq 0) { "Green" } else { "Red" })
    Write-Host "  Success Rate: $([math]::Round(($passedCount / $totalTests) * 100, 2))%"
    Write-Host "  Total Duration: $totalDuration"
    Write-Host ""
    
    if ($failedCount -eq 0) {
        Write-Success "🎉 SEMUA TEST BERHASIL!"
    }
    else {
        Write-Warning "⚠️ Ada $failedCount test yang gagal. Review laporan untuk detail."
    }
    
    Write-Host ""
    Write-Info "📄 Laporan lengkap tersedia di: $reportFile"
    Write-Host ""
}

Write-Host "═══════════════════════════════════════════════════" -ForegroundColor Magenta
Write-Host ""

# Exit with appropriate code
if ($failedTests.Count -gt 0) {
    exit 1
}
else {
    exit 0
}
