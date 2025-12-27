# ============================================
# Laravel Testing Script with Visualization
# ============================================

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  RUNNING LARAVEL TESTS" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Create reports directory if not exists
$reportsDir = "tests/reports"
if (-not (Test-Path $reportsDir)) {
    New-Item -ItemType Directory -Path $reportsDir -Force | Out-Null
    Write-Host "Created reports directory: $reportsDir" -ForegroundColor Green
    Write-Host ""
}

# Get timestamp
$timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
Write-Host "Started at: $timestamp" -ForegroundColor Yellow
Write-Host ""

# Run tests
Write-Host "Executing tests..." -ForegroundColor Cyan
Write-Host ""
php artisan test --testsuite=Unit,Feature --exclude-group=api

$exitCode = $LASTEXITCODE

# Display summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  TEST EXECUTION SUMMARY" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

if ($exitCode -eq 0) {
    Write-Host "All tests passed!" -ForegroundColor Green
} else {
    Write-Host "Some tests failed!" -ForegroundColor Red
}

Write-Host ""
Write-Host "Generated Reports:" -ForegroundColor Yellow
Write-Host "   HTML Report  : tests/reports/testdox.html" -ForegroundColor White
Write-Host "   XML Report   : tests/reports/junit.xml" -ForegroundColor White
Write-Host "   Text Summary : tests/reports/testdox.txt" -ForegroundColor White
Write-Host ""

# Check if reports exist and display file info
if (Test-Path "$reportsDir/testdox.html") {
    $htmlSize = (Get-Item "$reportsDir/testdox.html").Length
    $sizeKB = [math]::Round($htmlSize/1KB, 2)
    Write-Host "HTML Report generated: $sizeKB KB" -ForegroundColor Green
    Write-Host "To open: start tests/reports/testdox.html" -ForegroundColor Cyan
    Write-Host ""
}

if (Test-Path "$reportsDir/testdox.txt") {
    Write-Host "Text Summary:" -ForegroundColor Yellow
    Write-Host "========================================" -ForegroundColor Gray
    Get-Content "$reportsDir/testdox.txt" -ErrorAction SilentlyContinue | ForEach-Object {
        if ($_ -match "^\s*\[x\]") {
            Write-Host $_ -ForegroundColor Green
        } elseif ($_ -match "^\s*\[ \]") {
            Write-Host $_ -ForegroundColor Red
        } elseif ($_ -match "^[A-Z]") {
            Write-Host $_ -ForegroundColor Cyan
        } else {
            Write-Host $_ -ForegroundColor White
        }
    }
    Write-Host "========================================" -ForegroundColor Gray
    Write-Host ""
}

# Display junit summary if available
if (Test-Path "$reportsDir/junit.xml") {
    try {
        [xml]$junit = Get-Content "$reportsDir/junit.xml"
        $testsuites = $junit.testsuites.testsuite

        Write-Host "Detailed Statistics:" -ForegroundColor Yellow
        Write-Host "========================================" -ForegroundColor Gray

        $totalTests = 0
        $totalFailures = 0
        $totalErrors = 0
        $totalSkipped = 0
        $totalTime = 0.0

        foreach ($suite in $testsuites) {
            $tests = [int]$suite.tests
            $failures = [int]$suite.failures
            $errors = [int]$suite.errors
            $skipped = [int]$suite.skipped
            $time = [double]$suite.time

            $totalTests += $tests
            $totalFailures += $failures
            $totalErrors += $errors
            $totalSkipped += $skipped
            $totalTime += $time

            $status = if ($failures -eq 0 -and $errors -eq 0) { "[PASS]" } else { "[FAIL]" }
            $color = if ($failures -eq 0 -and $errors -eq 0) { "Green" } else { "Red" }
            $timeRound = [math]::Round($time, 2)

            Write-Host "  $status $($suite.name): $tests tests, $failures failures, $errors errors, $skipped skipped ($timeRound s)" -ForegroundColor $color
        }

        Write-Host "----------------------------------------" -ForegroundColor Gray

        $passed = $totalTests - $totalFailures - $totalErrors - $totalSkipped
        $totalTimeRound = [math]::Round($totalTime, 2)

        Write-Host "  TOTAL: $totalTests tests, $passed passed" -ForegroundColor Cyan

        if ($totalFailures -gt 0) {
            Write-Host "         $totalFailures failed" -ForegroundColor Red
        }
        if ($totalErrors -gt 0) {
            Write-Host "         $totalErrors errors" -ForegroundColor Red
        }
        if ($totalSkipped -gt 0) {
            Write-Host "         $totalSkipped skipped" -ForegroundColor Yellow
        }

        Write-Host "         Total time: $totalTimeRound s" -ForegroundColor Gray
        Write-Host "========================================" -ForegroundColor Gray
        Write-Host ""
    }
    catch {
        Write-Host "Could not parse junit.xml" -ForegroundColor Yellow
        Write-Host ""
    }
}

$endTimestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
Write-Host "Finished at: $endTimestamp" -ForegroundColor Yellow
Write-Host ""

# Ask to open HTML report
Write-Host "Open HTML report in browser? (Y/N): " -NoNewline -ForegroundColor Cyan
$response = Read-Host
if ($response -eq 'Y' -or $response -eq 'y') {
    Start-Process "tests/reports/testdox.html"
    Write-Host "Opening HTML report..." -ForegroundColor Green
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

exit $exitCode
