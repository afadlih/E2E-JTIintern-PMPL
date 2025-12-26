# Quick Test Runner - Run only smoke and load tests (fast version)
Write-Host ""
Write-Host "=================================" -ForegroundColor Cyan
Write-Host "K6 Performance Testing" -ForegroundColor Cyan
Write-Host "=================================" -ForegroundColor Cyan
Write-Host ""

$tests = @(
    @{ Name = "Smoke Test"; File = "smoke-test.js"; Duration = "30s" },
    @{ Name = "Load Test"; File = "load-test-basic.js"; Duration = "2m" }
)

$results = @()

foreach ($test in $tests) {
    Write-Host "Running: $($test.Name) ($($test.Duration))..." -ForegroundColor Yellow
    
    $output = & C:\k6\k6.exe run "tests/Performance/$($test.File)" 2>&1 | Out-String
    Write-Host $output
    
    $status = if ($LASTEXITCODE -eq 0) { "PASS" } else { "FAIL" }
    
    $results += [PSCustomObject]@{
        Test = $test.Name
        Status = $status
        File = $test.File
    }
    
    Write-Host ""
    Write-Host "Result: $status" -ForegroundColor $(if ($status -eq "PASS") { "Green" } else { "Red" })
    Write-Host ""
    Write-Host "=================================" -ForegroundColor DarkGray
}

Write-Host ""
Write-Host "SUMMARY:" -ForegroundColor Cyan
$results | Format-Table -AutoSize
Write-Host ""

$passed = ($results | Where-Object { $_.Status -eq "PASS" }).Count
Write-Host "Passed: $passed / $($results.Count)" -ForegroundColor $(if ($passed -eq $results.Count) { "Green" } else { "Yellow" })
