#!/bin/bash
# ============================================
# Quick Test Runner with Summary
# ============================================

echo ""
echo "========================================"
echo "  🧪 RUNNING LARAVEL TESTS"
echo "========================================"
echo ""

# Create reports directory
mkdir -p tests/reports

# Run tests with testdox format
php artisan test --testsuite=Unit,Feature --exclude-group=api

EXIT_CODE=$?

echo ""
echo "========================================"
echo "  📊 REPORTS GENERATED"
echo "========================================"
echo ""
echo "📁 Reports location: tests/reports/"
echo "   • HTML Report  : testdox.html"
echo "   • XML Report   : junit.xml"
echo "   • Text Summary : testdox.txt"
echo ""

if [ $EXIT_CODE -eq 0 ]; then
    echo "✅ All tests passed!"
else
    echo "❌ Some tests failed!"
fi

echo ""

exit $EXIT_CODE
