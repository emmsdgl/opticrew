@echo off
REM Quick Test Script for OptiCrew Security Improvements
REM Run this from the project root: quick_test.bat

echo ========================================
echo OptiCrew Security Quick Test
echo ========================================
echo.

echo [1/5] Checking if web server is running...
curl -s http://localhost > nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Web server not running! Start XAMPP Apache first.
    pause
    exit /b 1
)
echo [OK] Web server is running
echo.

echo [2/5] Running automated security tests...
php test_security.php
if %errorlevel% neq 0 (
    echo [WARNING] Some tests failed. Review above.
    pause
)
echo.

echo [3/5] Checking if database migrations are needed...
php artisan migrate:status
echo.

echo [4/5] Clearing cache...
php artisan optimize:clear
echo [OK] Cache cleared
echo.

echo [5/5] Testing database connection...
php artisan tinker --execute="echo 'DB Connection: '; try { DB::connection()->getPdo(); echo 'OK'; } catch (Exception \$e) { echo 'FAILED: ' . \$e->getMessage(); }"
echo.

echo ========================================
echo Quick Test Complete!
echo ========================================
echo.
echo Next steps:
echo   1. Review TESTING_SECURITY_IMPROVEMENTS.md for detailed tests
echo   2. Run: php artisan migrate (if needed)
echo   3. Test in browser: http://localhost/tasks
echo.

pause
