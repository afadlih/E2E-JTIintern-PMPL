# Complete Test Runner with Report
$timestamp = Get-Date -Format "yyyy-MM-dd-HHmmss"
$reportFile = "tests/Performance/results/test-results-$timestamp.txt"

Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  K6 Performance Testing Suite" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Target: https://afws.my.id/E2E-JTIintern-PMPL" -ForegroundColor Yellow
Write-Host "Date: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')" -ForegroundColor Yellow
Write-Host ""

# Create results directory
if (-not (Test-Path "tests/Performance/results")) {
    New-Item -ItemType Directory -Path "tests/Performance/results" -Force | Out-Null
}

$tests = @(
    @{ Name = "Smoke Test"; File = "smoke-test.js"; Duration = "30s" },
    @{ Name = "Load Test"; File = "load-test-basic.js"; Duration = "2m" }
)

$results = @()
$startTime = Get-Date

"K6 PERFORMANCE TEST RESULTS" | Out-File -FilePath $reportFile
"================================" | Out-File -FilePath $reportFile -Append
"Target: https://afws.my.id/E2E-JTIintern-PMPL" | Out-File -FilePath $reportFile -Append
"Date: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')" | Out-File -FilePath $reportFile -Append
"" | Out-File -FilePath $reportFile -Append

foreach ($test in $tests) {
    Write-Host "=========================================" -ForegroundColor DarkGray
    Write-Host "Running: $($test.Name) ($($test.Duration))" -ForegroundColor Cyan
    Write-Host "=========================================" -ForegroundColor DarkGray
    Write-Host ""
    
    $testStart = Get-Date
    
    $output = & C:\k6\k6.exe run "tests/Performance/$($test.File)" 2>&1 | Out-String
    Write-Host $output
    
    $testEnd = Get-Date
    $duration = ($testEnd - $testStart).ToString("mm\:ss")
    
    $status = if ($LASTEXITCODE -eq 0) { "PASS" } else { "FAIL" }
    $checksPass = if ($output -match "checks.*?(\d+\.?\d*)%.*?✓\s*(\d+).*?✗\s*(\d+)") { 
        "$($Matches[2])/$([int]$Matches[2] + [int]$Matches[3]) ($($Matches[1])%)" 
    } else { "N/A" }
    $p95 = if ($output -match "http_req_duration.*?p\(95\)=([^\s]+)") { $Matches[1] } else { "N/A" }
    $errorRate = if ($output -match "http_req_failed.*?(\d+\.?\d*)%") { $Matches[1] + "%" } else { "N/A" }
    
    $results += [PSCustomObject]@{
        Test = $test.Name
        Status = $status
        Duration = $duration
        Checks = $checksPass
        "P95 Time" = $p95
        "Error Rate" = $errorRate
    }
    
    # Save to report file
    "" | Out-File -FilePath $reportFile -Append
    "$($test.Name)" | Out-File -FilePath $reportFile -Append
    "-" * 40 | Out-File -FilePath $reportFile -Append
    "Status: $status" | Out-File -FilePath $reportFile -Append
    "Duration: $duration" | Out-File -FilePath $reportFile -Append
    "Checks: $checksPass" | Out-File -FilePath $reportFile -Append
    "P95 Response Time: $p95" | Out-File -FilePath $reportFile -Append
    "Error Rate: $errorRate" | Out-File -FilePath $reportFile -Append
    "" | Out-File -FilePath $reportFile -Append
    
    Write-Host ""
    if ($status -eq "PASS") {
        Write-Host "✓ $($test.Name) PASSED" -ForegroundColor Green
    } else {
        Write-Host "✗ $($test.Name) FAILED" -ForegroundColor Red
    }
    Write-Host ""
    
    Start-Sleep -Seconds 2
}

$endTime = Get-Date
$totalDuration = ($endTime - $startTime).ToString("hh\:mm\:ss")

Write-Host ""
Write-Host "=========================================" -ForegroundColor Magenta
Write-Host "  TEST SUMMARY" -ForegroundColor Magenta
Write-Host "=========================================" -ForegroundColor Magenta
Write-Host ""

$results | Format-Table -AutoSize

$passed = ($results | Where-Object { $_.Status -eq "PASS" }).Count
$total = $results.Count

Write-Host ""
Write-Host "Total Duration: $totalDuration" -ForegroundColor Yellow
Write-Host "Passed: $passed / $total" -ForegroundColor $(if ($passed -eq $total) { "Green" } else { "Yellow" })
Write-Host ""
Write-Host "Report saved to: $reportFile" -ForegroundColor Cyan
Write-Host ""

# Append summary to report
"" | Out-File -FilePath $reportFile -Append
"SUMMARY" | Out-File -FilePath $reportFile -Append
"========================================" | Out-File -FilePath $reportFile -Append
"Total Tests: $total" | Out-File -FilePath $reportFile -Append
"Passed: $passed" | Out-File -FilePath $reportFile -Append
"Failed: $($total - $passed)" | Out-File -FilePath $reportFile -Append
"Success Rate: $([math]::Round(($passed / $total) * 100, 2))%" | Out-File -FilePath $reportFile -Append
"Total Duration: $totalDuration" | Out-File -FilePath $reportFile -Append

if ($passed -eq $total) {
    Write-Host "🎉 ALL TESTS PASSED!" -ForegroundColor Green
} else {
    Write-Host "⚠️  Some tests failed. Review the report for details." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "=========================================" -ForegroundColor Magenta
Write-Host ""
