# Simple K6 Test Runner
param(
    [string]$OutputDir = "results"
)

Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  K6 Performance Testing Suite" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Target: https://afws.my.id/login" -ForegroundColor Yellow
Write-Host ""

# Create output directory
if (-not (Test-Path $OutputDir)) {
    New-Item -ItemType Directory -Path $OutputDir | Out-Null
}

# Test list
$tests = @("smoke-test.js", "load-test-basic.js", "stress-test.js", "spike-test.js", "soak-test.js")
$results = @()
$startTime = Get-Date

foreach ($test in $tests) {
    Write-Host "=========================================" -ForegroundColor DarkGray
    Write-Host "Running: $test" -ForegroundColor Cyan
    Write-Host "=========================================" -ForegroundColor DarkGray
    Write-Host ""
    
    $testStart = Get-Date
    
    # Run K6
    $output = & C:\k6\k6.exe run "tests/Performance/$test" 2>&1 | Out-String
    Write-Host $output
    
    $testEnd = Get-Date
    $duration = ($testEnd - $testStart).ToString("mm\:ss")
    
    # Parse results
    $status = if ($LASTEXITCODE -eq 0) { "PASS" } else { "FAIL" }
    $errorRate = if ($output -match "http_req_failed.*?(\d+\.?\d*)%") { $Matches[1] + "%" } else { "N/A" }
    $p95 = if ($output -match "p\(95\)=([^\s]+)") { $Matches[1] } else { "N/A" }
    
    $results += [PSCustomObject]@{
        Test = $test
        Status = $status
        Duration = $duration
        ErrorRate = $errorRate
        P95 = $p95
    }
    
    Write-Host ""
    if ($status -eq "PASS") {
        Write-Host "Result: PASSED" -ForegroundColor Green
    } else {
        Write-Host "Result: FAILED" -ForegroundColor Red
    }
    Write-Host ""
    
    Start-Sleep -Seconds 2
}

$endTime = Get-Date
$totalDuration = ($endTime - $startTime).ToString("hh\:mm\:ss")

# Summary
Write-Host ""
Write-Host "=========================================" -ForegroundColor Magenta
Write-Host "  TEST SUMMARY" -ForegroundColor Magenta
Write-Host "=========================================" -ForegroundColor Magenta
Write-Host ""

$results | Format-Table -AutoSize

Write-Host ""
Write-Host "Total Duration: $totalDuration" -ForegroundColor Yellow

$passed = ($results | Where-Object { $_.Status -eq "PASS" }).Count
$failed = ($results | Where-Object { $_.Status -eq "FAIL" }).Count

Write-Host "Passed: $passed" -ForegroundColor Green
Write-Host "Failed: $failed" -ForegroundColor Red
Write-Host ""

# Generate simple report
$reportFile = Join-Path $OutputDir "test-report-$(Get-Date -Format 'yyyy-MM-dd-HHmm').txt"
$results | Format-Table -AutoSize | Out-File -FilePath $reportFile
Write-Host "Report saved to: $reportFile" -ForegroundColor Cyan
Write-Host ""
