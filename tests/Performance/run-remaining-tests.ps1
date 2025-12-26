# Run remaining performance tests
$k6 = "C:\k6\k6.exe"
$testPath = "tests/Performance"

Write-Host "`n=================================" -ForegroundColor Cyan
Write-Host "K6 PERFORMANCE TESTS - REMAINING" -ForegroundColor Cyan
Write-Host "=================================" -ForegroundColor Cyan

# Stress Test
Write-Host "`nRunning: Stress Test (10 minutes)..." -ForegroundColor Yellow
& $k6 run "$testPath/stress-test.js"
$stressResult = $LASTEXITCODE

# Spike Test
Write-Host "`nRunning: Spike Test (2 minutes)..." -ForegroundColor Yellow
& $k6 run "$testPath/spike-test.js"
$spikeResult = $LASTEXITCODE

# Soak Test
Write-Host "`nRunning: Soak Test (12 minutes)..." -ForegroundColor Yellow
& $k6 run "$testPath/soak-test.js"
$soakResult = $LASTEXITCODE

# Summary
Write-Host "`n=================================" -ForegroundColor Cyan
Write-Host "SUMMARY" -ForegroundColor Cyan
Write-Host "=================================" -ForegroundColor Cyan

$tests = @(
    @{Name="Stress Test"; Result=$stressResult}
    @{Name="Spike Test"; Result=$spikeResult}
    @{Name="Soak Test"; Result=$soakResult}
)

foreach ($test in $tests) {
    $status = if ($test.Result -eq 0) { "PASS" } else { "FAIL" }
    $color = if ($test.Result -eq 0) { "Green" } else { "Red" }
    Write-Host "$($test.Name): " -NoNewline
    Write-Host $status -ForegroundColor $color
}

$totalPassed = ($tests | Where-Object { $_.Result -eq 0 }).Count
Write-Host "`nPassed: $totalPassed / $($tests.Count)" -ForegroundColor $(if ($totalPassed -eq $tests.Count) { "Green" } else { "Yellow" })
