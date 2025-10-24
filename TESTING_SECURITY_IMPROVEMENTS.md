# Testing Security Improvements Guide

This guide will help you verify that all security improvements are working correctly.

---

## Prerequisites

1. Start your XAMPP MySQL and Apache servers
2. Ensure your database is migrated: `php artisan migrate`
3. Have at least 2 test users:
   - 1 Admin user (role='admin')
   - 1 Employee user (role='employee')

---

## Test Suite Overview

- ✅ Test 1: Authorization Policies
- ✅ Test 2: Rate Limiting
- ✅ Test 3: Form Request Validation
- ✅ Test 4: Unique Constraints
- ✅ Test 5: API Error Handling
- ✅ Test 6: Soft Deletes
- ✅ Test 7: CORS Configuration
- ✅ Test 8: Database Transactions
- ✅ Test 9: Config Constants
- ✅ Test 10: Production Readiness

---

## TEST 1: Authorization Policies

### Test 1.1: Task Policy - Employee Cannot Create Tasks

**What to test**: Employees should NOT be able to create tasks (only admins can)

**Steps**:
1. Login as an employee user
2. Navigate to `/tasks` (admin tasks page)
3. Try to create a new task

**Expected Result**:
- ❌ **BEFORE**: Employee could create tasks
- ✅ **AFTER**: Employee sees "Unauthorized" or is redirected

**Manual Test**:
```php
// Run in php artisan tinker
$employee = User::where('role', 'employee')->first();
$task = new App\Models\Task(['task_description' => 'Test']);
Gate::forUser($employee)->authorize('create', $task);
// Should throw: Illuminate\Auth\Access\AuthorizationException
```

### Test 1.2: Task Policy - Employee Can Only Update Assigned Tasks

**Steps**:
1. Login as employee
2. Go to employee tasks dashboard
3. Try to update status of a task NOT assigned to you

**Expected Result**:
- ❌ **BEFORE**: Could update any task
- ✅ **AFTER**: "Unauthorized" error

### Test 1.3: Optimization Policy - Employee Cannot Re-Optimize

**Steps**:
1. Login as employee
2. Try to access optimization endpoints manually

**Manual Test**:
```bash
# Get employee auth token first, then:
curl -X POST http://localhost/admin/optimization/reoptimize \
  -H "Cookie: laravel_session=YOUR_EMPLOYEE_SESSION" \
  -H "Content-Type: application/json"
```

**Expected Result**:
- ✅ **AFTER**: 403 Forbidden or redirect to employee dashboard

---

## TEST 2: Rate Limiting

### Test 2.1: Task Creation Rate Limit (20 per minute)

**What to test**: Creating more than 20 tasks in 1 minute should be blocked

**Steps**:
1. Open browser console (F12)
2. Run this JavaScript code:

```javascript
// Test task creation rate limit
async function testRateLimit() {
    for (let i = 0; i < 25; i++) {
        const response = await fetch('/tasks', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                client: 'contracted_1',
                serviceDate: '2025-10-30',
                cabinsList: [{
                    cabin: 'Test Cabin',
                    serviceType: 'Daily Cleaning',
                    cabinType: 'Standard'
                }]
            })
        });

        console.log(`Request ${i + 1}: ${response.status}`);

        if (response.status === 429) {
            console.log('✅ RATE LIMIT WORKING - Request blocked at:', i + 1);
            const data = await response.json();
            console.log('Response:', data);
            break;
        }
    }
}

testRateLimit();
```

**Expected Result**:
- ❌ **BEFORE**: All 25 requests succeed (could crash server)
- ✅ **AFTER**: First 20 succeed, requests 21+ return `429 Too Many Requests`

### Test 2.2: Optimization Rate Limit (5 per minute)

**Command Line Test**:
```bash
# Run this 6 times quickly
for i in {1..6}; do
  echo "Request $i:"
  curl -X POST http://localhost/admin/optimization/reoptimize \
    -H "Cookie: laravel_session=YOUR_ADMIN_SESSION" \
    -H "Content-Type: application/json" \
    -d '{"service_date":"2025-10-30"}' \
    -w "\nStatus: %{http_code}\n\n"
done
```

**Expected Result**:
- First 5 requests: Status 200
- 6th request: Status 429 (Too Many Requests)

### Test 2.3: API Rate Limit (60 per minute)

**Test with Loop**:
```bash
# Test API endpoint rate limit
for i in {1..65}; do
  curl -X GET "http://localhost/api/employee/tasks" \
    -H "Authorization: Bearer YOUR_API_TOKEN" \
    -s -w "Request $i: %{http_code}\n" \
    -o /dev/null
done
```

**Expected Result**:
- First 60 requests: Status 200
- Requests 61+: Status 429

---

## TEST 3: Form Request Validation

### Test 3.1: Task Creation - Invalid Data

**Test Invalid Price**:
```javascript
// Run in browser console on /tasks page
fetch('/tasks', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        client: 'contracted_1',
        serviceDate: '2025-10-30',
        extraTasks: [{
            type: 'Special Cleaning',
            price: 99999 // INVALID: Exceeds 10,000 EUR limit
        }]
    })
})
.then(r => r.json())
.then(d => console.log('✅ Validation Response:', d));
```

**Expected Result**:
- ❌ **BEFORE**: Accepted any price
- ✅ **AFTER**: Returns 422 with error: `"Extra task price cannot exceed 10,000 EUR."`

### Test 3.2: Task Creation - Past Date

**Test**:
```javascript
fetch('/tasks', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        client: 'contracted_1',
        serviceDate: '2020-01-01', // INVALID: Past date
        cabinsList: [{
            cabin: 'Test',
            serviceType: 'Daily',
            cabinType: 'Standard'
        }]
    })
})
.then(r => r.json())
.then(d => console.log('Response:', d));
```

**Expected Result**:
- ✅ **AFTER**: Returns 422 with error: `"Service date must be today or in the future."`

### Test 3.3: Task Status Update - Invalid Status

**Test**:
```bash
curl -X PATCH http://localhost/tasks/1/status \
  -H "Content-Type: application/json" \
  -H "Cookie: laravel_session=YOUR_SESSION" \
  -d '{"status":"InvalidStatus"}'
```

**Expected Result**:
- ✅ Returns 422 with error about invalid status

---

## TEST 4: Unique Constraints

### Test 4.1: Duplicate Day-Off Prevention

**SQL Test**:
```sql
-- Try to insert duplicate day-off for same employee
INSERT INTO day_offs (employee_id, date, created_at, updated_at)
VALUES (1, '2025-11-01', NOW(), NOW());

-- Try to insert the SAME day-off again
INSERT INTO day_offs (employee_id, date, created_at, updated_at)
VALUES (1, '2025-11-01', NOW(), NOW());
```

**Expected Result**:
- ❌ **BEFORE**: Both inserts succeed (duplicate data)
- ✅ **AFTER**: Second insert fails with error: `Duplicate entry '1-2025-11-01' for key 'unique_employee_day_off'`

### Test 4.2: Duplicate Holiday Prevention

**SQL Test**:
```sql
-- Insert a holiday
INSERT INTO holidays (date, name, created_by, created_at, updated_at)
VALUES ('2025-12-25', 'Christmas', 1, NOW(), NOW());

-- Try to insert another holiday on the same date
INSERT INTO holidays (date, name, created_by, created_at, updated_at)
VALUES ('2025-12-25', 'Xmas', 1, NOW(), NOW());
```

**Expected Result**:
- ✅ **AFTER**: Second insert fails with: `Duplicate entry '2025-12-25' for key 'unique_holiday_date'`

### Test 4.3: Duplicate Team Member Prevention

**SQL Test**:
```sql
-- Add employee to team
INSERT INTO optimization_team_members (optimization_team_id, employee_id, created_at, updated_at)
VALUES (1, 5, NOW(), NOW());

-- Try to add SAME employee to SAME team again
INSERT INTO optimization_team_members (optimization_team_id, employee_id, created_at, updated_at)
VALUES (1, 5, NOW(), NOW());
```

**Expected Result**:
- ✅ **AFTER**: Second insert fails with: `Duplicate entry '1-5' for key 'unique_team_member'`

### Test 4.4: Verify All Constraints Exist

**SQL Query**:
```sql
-- Check if all unique constraints were created
SELECT
    TABLE_NAME,
    INDEX_NAME,
    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) as COLUMNS
FROM
    INFORMATION_SCHEMA.STATISTICS
WHERE
    TABLE_SCHEMA = 'opticrew'
    AND NON_UNIQUE = 0
    AND INDEX_NAME IN (
        'unique_employee_day_off',
        'unique_team_member',
        'unique_employee_clock_in',
        'unique_holiday_date'
    )
GROUP BY
    TABLE_NAME, INDEX_NAME;
```

**Expected Result**:
Should return 4 rows showing all unique indexes exist

---

## TEST 5: API Error Handling

### Test 5.1: Standardized Error Format

**Test - Non-existent Task**:
```bash
curl -X GET http://localhost/api/tasks/99999 \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  | json_pp
```

**Expected Result**:
```json
{
  "success": false,
  "message": "Resource not found"
}
```

- ❌ **BEFORE**: Raw HTML error or inconsistent JSON
- ✅ **AFTER**: Consistent JSON with `success`, `message` fields

### Test 5.2: Validation Error Format

**Test**:
```bash
curl -X POST http://localhost/api/tasks/1/start \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -d '{}' \
  | json_pp
```

**Expected Result**:
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "field_name": ["error message"]
  }
}
```

### Test 5.3: Exception Handling (500 Errors)

**Create an Intentional Error**:

1. Temporarily break a controller:
```php
// In TaskController.php, add at top of store method:
throw new \Exception("Test exception");
```

2. Make a request:
```bash
curl -X POST http://localhost/api/tasks/1/start \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

**Expected Result**:
- ❌ **BEFORE**: Exposes stack trace, file paths (security risk)
- ✅ **AFTER** (with APP_DEBUG=false): Returns clean JSON:
```json
{
  "success": false,
  "message": "An unexpected error occurred"
}
```

3. Remember to remove the test exception!

---

## TEST 6: Soft Deletes

### Test 6.1: User Soft Delete

**Test via Tinker**:
```php
php artisan tinker

// Create a test user
$user = User::create([
    'name' => 'Test Delete User',
    'email' => 'testdelete@example.com',
    'password' => bcrypt('password'),
    'role' => 'employee'
]);

$userId = $user->id;

// Delete the user
$user->delete();

// Try to find the user (should not find)
$found = User::find($userId);
echo $found ? "❌ FAILED: User still found" : "✅ PASSED: User soft deleted";

// Find with trashed
$trashed = User::withTrashed()->find($userId);
echo $trashed ? "✅ PASSED: User in trash" : "❌ FAILED: User permanently deleted";

// Check deleted_at is set
echo "\nDeleted at: " . $trashed->deleted_at;
```

**Expected Result**:
- ✅ User not found with normal query
- ✅ User found with `withTrashed()`
- ✅ `deleted_at` timestamp is set

### Test 6.2: Verify Soft Delete Columns Exist

**SQL Query**:
```sql
-- Check all 7 tables have deleted_at column
SELECT
    TABLE_NAME,
    COLUMN_NAME,
    DATA_TYPE
FROM
    INFORMATION_SCHEMA.COLUMNS
WHERE
    TABLE_SCHEMA = 'opticrew'
    AND COLUMN_NAME = 'deleted_at'
    AND TABLE_NAME IN (
        'users', 'employees', 'tasks', 'optimization_runs',
        'clients', 'contracted_clients', 'locations'
    )
ORDER BY
    TABLE_NAME;
```

**Expected Result**: Should return 7 rows (one for each table)

### Test 6.3: Restore Soft Deleted Record

**Test**:
```php
php artisan tinker

// Get a soft deleted user
$user = User::onlyTrashed()->first();

if ($user) {
    // Restore
    $user->restore();

    // Verify restored
    $restored = User::find($user->id);
    echo $restored ? "✅ PASSED: User restored" : "❌ FAILED";

    // Check deleted_at is NULL
    echo "\nDeleted at: " . ($restored->deleted_at ?? 'NULL (correct)');
}
```

---

## TEST 7: CORS Configuration

### Test 7.1: Verify CORS Headers

**Test Allowed Origin**:
```bash
curl -X OPTIONS http://localhost/api/employee/tasks \
  -H "Origin: http://localhost:3000" \
  -H "Access-Control-Request-Method: GET" \
  -v
```

**Expected Response Headers**:
```
Access-Control-Allow-Origin: http://localhost:3000
Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS
Access-Control-Allow-Credentials: true
Access-Control-Max-Age: 3600
```

### Test 7.2: Reject Unauthorized Origin

**Test**:
```bash
curl -X OPTIONS http://localhost/api/employee/tasks \
  -H "Origin: http://evil-site.com" \
  -H "Access-Control-Request-Method: GET" \
  -v
```

**Expected Result**:
- ❌ **BEFORE**: `Access-Control-Allow-Origin: *` (allows any site)
- ✅ **AFTER**: No `Access-Control-Allow-Origin` header (request blocked)

### Test 7.3: Check Config File

**Verify**:
```bash
php artisan tinker

// Check CORS config
config('cors.allowed_origins');
// Should NOT return ['*']
// Should return array with specific localhost URLs

config('cors.supports_credentials');
// Should return true
```

---

## TEST 8: Database Transactions

### Test 8.1: Transaction Rollback on Error

**Create a Test Controller Method**:
```php
// Add to TaskController.php temporarily
public function testTransaction()
{
    DB::beginTransaction();

    try {
        // Create a task
        Task::create([
            'task_description' => 'Test transaction task',
            'scheduled_date' => now(),
            'estimated_duration_minutes' => 60,
            'status' => 'Pending'
        ]);

        // Intentionally cause an error
        throw new \Exception("Test rollback");

        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Rolled back: ' . $e->getMessage()]);
    }
}
```

**Test**:
```bash
curl http://localhost/test-transaction
```

**Verify**:
```sql
-- Check if task was created
SELECT * FROM tasks WHERE task_description = 'Test transaction task';
```

**Expected Result**:
- ✅ Query returns 0 rows (task was rolled back)
- ❌ **BEFORE**: Task would exist (transaction not rolled back)

### Test 8.2: Verify Transaction Commits Added

**Check OptimizationService.php**:
```bash
cd C:\xampp\htdocs\opticrew
grep -n "DB::commit()" app/Services/Optimization/OptimizationService.php
```

**Expected Output**:
```
63:                    DB::commit(); // Early return fix 1
135:                DB::commit(); // Early return fix 2
238:            DB::commit(); // Main success path
```

- ✅ Should show 3 commit points (including early returns)

---

## TEST 9: Config Constants

### Test 9.1: Alert Threshold from Config

**Test**:
```php
php artisan tinker

// Check default value
config('optimization.alerts.on_hold_threshold_minutes');
// Should return: 30

// Simulate changing in .env
Config::set('optimization.alerts.on_hold_threshold_minutes', 45);

config('optimization.alerts.on_hold_threshold_minutes');
// Should return: 45 (configurable!)
```

### Test 9.2: Max Price from Config

**Test**:
```php
php artisan tinker

// Check max price
config('optimization.pricing.max_extra_task_price');
// Should return: 10000

// Test validation uses this value
$request = new App\Http\Requests\StoreTaskRequest();
$rules = $request->rules();
echo $rules['extraTasks.*.price'];
// Should include: max:10000
```

### Test 9.3: Verify All Config Values

**Run**:
```bash
php artisan config:show optimization
```

**Expected Output** (should show):
```
genetic_algorithm.population_size ..................... 20
genetic_algorithm.max_generations ..................... 100
workforce.max_hours_per_day ........................... 12
alerts.on_hold_threshold_minutes ...................... 30
alerts.duration_exceeded_threshold_percent ............ 20
pricing.max_extra_task_price .......................... 10000
penalties.deadline_violation .......................... 50000
penalties.hour_limit_violation ........................ 100000
penalties.unassigned_task ............................. 10000
```

---

## TEST 10: Production Readiness Check

### Test 10.1: Environment File Validation

**Check Current .env**:
```bash
cd C:\xampp\htdocs\opticrew

# Should have APP_KEY set
grep APP_KEY .env

# Should have APP_DEBUG (check value)
grep APP_DEBUG .env
```

**Expected**:
- ✅ `APP_KEY=base64:...` (not empty)
- ⚠️ `APP_DEBUG=true` (OK for development)
- ⚠️ For production, should be `APP_DEBUG=false`

### Test 10.2: Production Config Exists

**Verify**:
```bash
ls -la .env.production.example
```

**Expected**: File exists with 112 lines

### Test 10.3: Security Checklist

**Run these checks**:

```php
php artisan tinker

// 1. Check APP_KEY is set
echo "APP_KEY: " . (env('APP_KEY') ? '✅ SET' : '❌ MISSING');

// 2. Check debug mode
echo "\nAPP_DEBUG: " . (env('APP_DEBUG') ? '⚠️ TRUE (disable for prod)' : '✅ FALSE');

// 3. Check policies registered
echo "\nPolicies: " . count(Gate::policies()) . " registered";

// 4. Check rate limiting configured
echo "\nThrottle middleware: " . (in_array('throttle', array_keys(app('router')->getMiddleware())) ? '✅ Available' : '❌ Missing');
```

---

## AUTOMATED TEST SCRIPT

Save this as `test_security.php` in your project root:

```php
<?php
// test_security.php
// Run with: php test_security.php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔒 OptiCrew Security Improvements Test Suite\n";
echo "==========================================\n\n";

$passed = 0;
$failed = 0;

// Test 1: APP_KEY exists
echo "Test 1: APP_KEY Configuration... ";
if (env('APP_KEY')) {
    echo "✅ PASSED\n";
    $passed++;
} else {
    echo "❌ FAILED\n";
    $failed++;
}

// Test 2: Policies registered
echo "Test 2: Authorization Policies... ";
$policies = count(Gate::policies());
if ($policies >= 3) {
    echo "✅ PASSED ($policies policies)\n";
    $passed++;
} else {
    echo "❌ FAILED (only $policies policies)\n";
    $failed++;
}

// Test 3: Soft delete columns exist
echo "Test 3: Soft Delete Columns... ";
try {
    $tables = ['users', 'employees', 'tasks', 'optimization_runs',
               'clients', 'contracted_clients', 'locations'];
    $hasColumn = true;
    foreach ($tables as $table) {
        if (!Schema::hasColumn($table, 'deleted_at')) {
            $hasColumn = false;
            break;
        }
    }
    if ($hasColumn) {
        echo "✅ PASSED (all 7 tables)\n";
        $passed++;
    } else {
        echo "❌ FAILED (missing columns)\n";
        $failed++;
    }
} catch (\Exception $e) {
    echo "❌ FAILED (run migrations first)\n";
    $failed++;
}

// Test 4: Config values accessible
echo "Test 4: Configuration Constants... ";
$alertThreshold = config('optimization.alerts.on_hold_threshold_minutes');
$maxPrice = config('optimization.pricing.max_extra_task_price');
if ($alertThreshold === 30 && $maxPrice === 10000) {
    echo "✅ PASSED\n";
    $passed++;
} else {
    echo "❌ FAILED\n";
    $failed++;
}

// Test 5: CORS not allowing all origins
echo "Test 5: CORS Configuration... ";
$allowedOrigins = config('cors.allowed_origins');
if (!in_array('*', $allowedOrigins)) {
    echo "✅ PASSED (wildcard removed)\n";
    $passed++;
} else {
    echo "⚠️ WARNING (still allows all origins)\n";
    $failed++;
}

// Test 6: API middleware includes exception handler
echo "Test 6: API Exception Handler... ";
$apiMiddleware = config('kernel.middlewareGroups.api', app('router')->getMiddlewareGroups()['api']);
$hasHandler = false;
foreach ($apiMiddleware as $middleware) {
    if (str_contains($middleware, 'ApiExceptionHandler')) {
        $hasHandler = true;
        break;
    }
}
if ($hasHandler) {
    echo "✅ PASSED\n";
    $passed++;
} else {
    echo "❌ FAILED\n";
    $failed++;
}

// Summary
echo "\n==========================================\n";
echo "Results: $passed passed, $failed failed\n";
echo $failed === 0 ? "🎉 ALL TESTS PASSED!\n" : "⚠️ Some tests failed\n";
echo "==========================================\n";

exit($failed > 0 ? 1 : 0);
```

**Run it**:
```bash
cd C:\xampp\htdocs\opticrew
php test_security.php
```

---

## QUICK VERIFICATION CHECKLIST

Run these quick checks to verify everything works:

### ✅ Quick Check 1: Policies Exist
```bash
ls app/Policies/*.php
# Should show: TaskPolicy.php, OptimizationRunPolicy.php, EmployeePolicy.php
```

### ✅ Quick Check 2: Migrations Exist
```bash
ls database/migrations/*2025_10_24*.php
# Should show 2 new migration files
```

### ✅ Quick Check 3: Middleware Exists
```bash
ls app/Http/Middleware/ApiExceptionHandler.php
ls app/Http/Traits/ApiResponse.php
# Both should exist
```

### ✅ Quick Check 4: Config Updated
```bash
grep "alerts" config/optimization.php
grep "pricing" config/optimization.php
# Should show the new config sections
```

### ✅ Quick Check 5: Rate Limiting Added
```bash
grep "throttle" routes/web.php | wc -l
# Should show at least 4 lines
```

---

## EXPECTED IMPROVEMENTS SUMMARY

| Feature | Before | After | Test to Verify |
|---------|--------|-------|----------------|
| **Authorization** | Anyone could do anything | Role-based access control | Test 1.1, 1.2, 1.3 |
| **Rate Limiting** | No limits (DoS vulnerable) | Throttled expensive ops | Test 2.1, 2.2, 2.3 |
| **Validation** | Inline validation only | FormRequest classes | Test 3.1, 3.2, 3.3 |
| **Data Integrity** | Duplicates possible | Unique constraints | Test 4.1, 4.2, 4.3 |
| **Error Handling** | Inconsistent responses | Standardized JSON | Test 5.1, 5.2, 5.3 |
| **Soft Deletes** | Permanent deletion | Recoverable | Test 6.1, 6.2, 6.3 |
| **CORS** | Open to all origins | Specific origins only | Test 7.1, 7.2 |
| **Transactions** | Could leak connections | Properly committed/rolled back | Test 8.1, 8.2 |
| **Configuration** | Hardcoded values | Centralized config | Test 9.1, 9.2, 9.3 |

---

## TROUBLESHOOTING

### Issue: "Class 'Gate' not found"
**Solution**: Add at top of test file: `use Illuminate\Support\Facades\Gate;`

### Issue: Migration fails with "table already exists"
**Solution**: This is OK if migrations were run before. The table modifications (adding columns) should still work.

### Issue: Rate limiting not working
**Solution**: Clear cache: `php artisan cache:clear && php artisan config:clear`

### Issue: Policies not enforced
**Solution**:
1. Check `AuthServiceProvider.php` has policies registered
2. Run: `php artisan optimize:clear`
3. Ensure you're logged in as correct role

---

## FINAL PRODUCTION CHECKLIST

Before deploying to production:

- [ ] Run `php artisan migrate` on production database
- [ ] Copy `.env.production.example` to `.env` and fill in real values
- [ ] Set `APP_DEBUG=false` in production `.env`
- [ ] Set `APP_ENV=production`
- [ ] Update CORS `allowed_origins` in `config/cors.php` with your production domain
- [ ] Run `php artisan config:cache` on production
- [ ] Run `php artisan route:cache` on production
- [ ] Run `php artisan optimize` on production
- [ ] Test all endpoints from production domain
- [ ] Monitor logs at `storage/logs/laravel.log`

---

## Need Help?

If any test fails:
1. Check the error message
2. Verify migrations ran: `php artisan migrate:status`
3. Clear cache: `php artisan optimize:clear`
4. Check file permissions (especially `storage/` and `bootstrap/cache/`)
5. Review the specific test's "Expected Result" section above

**All tests passing? 🎉 Your security improvements are working perfectly!**
