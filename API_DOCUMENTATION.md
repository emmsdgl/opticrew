# OptiCrew API Documentation

**Version:** 2.0 (Based on Genetic Algorithm + Rule-Based Optimization)
**Last Updated:** October 20, 2025
**Base URL:** `http://localhost/opticrew`
**API Base URL:** `http://localhost/opticrew/api`

---

## 📌 Table of Contents

1. [Authentication](#authentication)
2. [Admin/Employer Dashboard APIs](#adminemployer-dashboard-apis)
3. [Employee Dashboard APIs](#employee-dashboard-apis)
4. [Client Dashboard APIs](#client-dashboard-apis)
5. [Data Models](#data-models)
6. [Error Handling](#error-handling)
7. [Testing Guide](#testing-guide)

---

## 🔐 Authentication

All API endpoints require authentication using Laravel Sanctum.

###Token Authentication

```javascript
// Include in request headers
headers: {
    'Authorization': 'Bearer YOUR_ACCESS_TOKEN',
    'Content-Type': 'application/json',
    'Accept': 'application/json'
}
```

---

## 👨‍💼 Admin/Employer Dashboard APIs

### 1. Create Tasks & Trigger Optimization

**Endpoint:** `POST /tasks`
**Description:** Create tasks for a future date and trigger optimization

**Request Body:**
```json
{
    "client": "contracted_1",  // Format: "contracted_{id}" or "client_{id}"
    "serviceDate": "2025-10-25",
    "serviceType": "Cabin Cleaning",
    "cabinsList": [
        {
            "cabin": "Cabin A1"
        },
        {
            "cabin": "Cabin A2"
        }
    ],
    "arrivalStatus": true  // TRUE if guest arriving, FALSE otherwise
}
```

**Response:**
```json
{
    "message": "Tasks created and optimized successfully!",
    "tasks_created": 2,
    "schedules": {
        "contracted_1": {
            "teams": [...],
            "fitness_score": 0.85
        }
    },
    "statistics": {
        "total_tasks": 2,
        "total_employees": 6,
        "total_teams": 2
    },
    "is_real_time_addition": false
}
```

**Real-Time Addition Response** (if task is added for TODAY with existing saved schedule):
```json
{
    "status": "success",
    "message": "Task added to existing team",
    "assigned_team_id": 3,
    "team_members": ["John Doe", "Jane Smith"],
    "team_current_hours": 8.5,
    "task_added_hours": 1.5
}
```

---

### 2. Save Schedule

**Endpoint:** `POST /admin/optimization/save-schedule`
**Description:** Mark an optimization schedule as saved (is_saved = TRUE)

**Request Body:**
```json
{
    "service_date": "2025-10-25"
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Schedule saved successfully",
    "optimization_run_id": 123
}
```

---

### 3. Get Optimization Results

**Endpoint:** `GET /admin/optimization/{optimizationRunId}/results`
**Description:** Get detailed results for a specific optimization run

**Response:**
```json
{
    "optimization_run": {
        "id": 123,
        "service_date": "2025-10-25",
        "status": "completed",
        "is_saved": true,
        "total_tasks": 10,
        "total_teams": 3,
        "total_employees": 8,
        "final_fitness_score": 0.87,
        "generations_run": 45,
        "employee_allocation": {...},
        "greedy_result": {...}
    }
}
```

---

### 4. Re-optimize Schedule

**Endpoint:** `POST /admin/optimization/reoptimize`
**Description:** Re-run optimization for a specific date

**Request Body:**
```json
{
    "service_date": "2025-10-25"
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Re-optimization completed successfully",
    "statistics": {...},
    "schedules": {...}
}
```

---

### 5. Get Unacknowledged Alerts

**Endpoint:** `GET /api/admin/alerts/unacknowledged`
**Description:** Get all alerts that haven't been acknowledged (for real-time notifications)

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "alert_id": 1,
            "task_id": 45,
            "alert_type": "task_delayed",
            "delay_minutes": 35,
            "reason": "Guest still in cabin",
            "task_description": "Cabin Cleaning",
            "location": "Cabin A1",
            "triggered_at": "2025-10-20 14:30:00"
        }
    ]
}
```

---

### 6. Acknowledge Alert

**Endpoint:** `POST /api/admin/alerts/{alertId}/acknowledge`
**Description:** Mark an alert as acknowledged

**Response:**
```json
{
    "success": true,
    "message": "Alert acknowledged"
}
```

---

## 👷 Employee Dashboard APIs

### 1. Get Employee Tasks

**Endpoint:** `GET /api/employee/tasks`
**Description:** Get all tasks assigned to the employee's team for a specific date

**Query Parameters:**
- `date` (required): YYYY-MM-DD format
- `employee_id` (required): Employee ID

**Example:** `GET /api/employee/tasks?date=2025-10-20&employee_id=5`

**Response:**
```json
{
    "success": true,
    "data": {
        "date": "2025-10-20",
        "tasks": [
            {
                "task_id": 45,
                "description": "Cabin Cleaning",
                "location": "Cabin A1",
                "status": "Pending",
                "scheduled_time": "08:00:00",
                "estimated_duration": 60,
                "arrival_status": true,
                "started_at": null,
                "completed_at": null
            },
            {
                "task_id": 46,
                "description": "Cabin Cleaning",
                "location": "Cabin A2",
                "status": "In Progress",
                "scheduled_time": "09:30:00",
                "estimated_duration": 45,
                "arrival_status": false,
                "started_at": "2025-10-20 09:35:00",
                "completed_at": null
            }
        ],
        "total_tasks": 2
    }
}
```

---

### 2. Get Task Details

**Endpoint:** `GET /api/tasks/{taskId}`
**Description:** Get detailed information about a specific task

**Response:**
```json
{
    "success": true,
    "data": {
        "task_id": 45,
        "description": "Cabin Cleaning",
        "location": {
            "id": 12,
            "name": "Cabin A1"
        },
        "status": "Pending",
        "scheduled_date": "2025-10-20",
        "scheduled_time": "08:00:00",
        "estimated_duration": 60,
        "actual_duration": null,
        "arrival_status": true,
        "started_at": null,
        "completed_at": null,
        "on_hold_reason": null,
        "team_members": [
            {
                "id": 5,
                "name": "John Doe",
                "has_drivers_license": true
            },
            {
                "id": 8,
                "name": "Jane Smith",
                "has_drivers_license": false
            }
        ]
    }
}
```

---

### 3. Start Task

**Endpoint:** `POST /api/tasks/{taskId}/start`
**Description:** Mark task as "In Progress" and record start time

**Response:**
```json
{
    "success": true,
    "message": "Task started successfully",
    "data": {
        "task_id": 45,
        "status": "In Progress",
        "started_at": "2025-10-20 08:05:00"
    }
}
```

---

### 4. Put Task On Hold

**Endpoint:** `POST /api/tasks/{taskId}/hold`
**Description:** Mark task as "On Hold" with a reason. Triggers alert if delay > 30 minutes.

**Request Body:**
```json
{
    "reason": "Guest still in cabin"
}
```

**Possible reasons:**
- "Guest still in cabin"
- "DND sign active"
- "Missing supplies"
- "Equipment malfunction"
- "Other"

**Response:**
```json
{
    "success": true,
    "message": "Task put on hold",
    "data": {
        "task_id": 45,
        "status": "On Hold",
        "reason": "Guest still in cabin",
        "delay_minutes": 35,
        "alert_triggered": true
    }
}
```

---

### 5. Complete Task

**Endpoint:** `POST /api/tasks/{taskId}/complete`
**Description:** Mark task as "Completed". Auto-calculates actual duration and creates performance flag if exceeded.

**Response:**
```json
{
    "success": true,
    "message": "Task completed successfully",
    "data": {
        "task_id": 45,
        "status": "Completed",
        "estimated_duration": 60,
        "actual_duration": 75,
        "variance_minutes": 15,
        "completed_at": "2025-10-20 09:20:00",
        "performance_flagged": true
    }
}
```

---

## 👥 Client Dashboard APIs

### 1. Get Client Service History

**Endpoint:** `GET /api/client/services`
**Description:** Get service history for the authenticated client

**Query Parameters:**
- `start_date` (optional): YYYY-MM-DD
- `end_date` (optional): YYYY-MM-DD

**Response:**
```json
{
    "success": true,
    "data": {
        "services": [
            {
                "service_date": "2025-10-15",
                "service_type": "Cabin Cleaning",
                "locations": ["Cabin A1", "Cabin A2"],
                "total_tasks": 2,
                "completed_tasks": 2,
                "status": "Completed"
            }
        ],
        "total_services": 1
    }
}
```

---

## 📊 Data Models

### Task Status Values
- `Pending` - Task created, not yet assigned
- `Scheduled` - Task assigned to a team
- `In Progress` - Employee has started the task
- `On Hold` - Task paused (with reason)
- `Completed` - Task finished
- `Cancelled` - Task cancelled

### On Hold Reasons
- `Guest still in cabin`
- `DND sign active`
- `Missing supplies`
- `Equipment malfunction`
- `Other`

### Alert Types
- `task_delayed` - Task on hold for > 30 minutes
- `duration_exceeded` - Task took longer than estimated

### Performance Flag Types
- `duration_exceeded` - Actual duration > estimated duration

---

## ⚠️ Error Handling

All API errors follow this format:

```json
{
    "success": false,
    "message": "Error description here"
}
```

### HTTP Status Codes
- `200` - Success
- `400` - Bad Request (validation error)
- `401` - Unauthorized (invalid token)
- `404` - Not Found
- `422` - Unprocessable Entity (validation failed)
- `500` - Server Error

---

## 🧪 Testing Guide

### Testing with cURL

**1. Create Tasks:**
```bash
curl -X POST http://localhost/opticrew/tasks \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "client": "contracted_1",
    "serviceDate": "2025-10-25",
    "serviceType": "Cabin Cleaning",
    "cabinsList": [{"cabin": "Cabin A1"}],
    "arrivalStatus": true
  }'
```

**2. Start Task:**
```bash
curl -X POST http://localhost/opticrew/api/tasks/45/start \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**3. Complete Task:**
```bash
curl -X POST http://localhost/opticrew/api/tasks/45/complete \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Testing with Postman

1. Import the cURL commands above
2. Set up environment variables:
   - `BASE_URL`: http://localhost/opticrew
   - `API_TOKEN`: Your authentication token
3. Test each endpoint sequentially

---

## 🔄 Workflow Example

### Admin Creates Tasks → Employees Complete Them

**Step 1: Admin creates tasks (2025-10-25)**
```
POST /tasks
```

**Step 2: System runs optimization**
- Rule-based preprocessing
- Genetic algorithm optimization
- Returns proposed schedule (is_saved = FALSE)

**Step 3: Admin reviews and saves schedule**
```
POST /admin/optimization/save-schedule
```

**Step 4: Employee checks tasks for the day**
```
GET /api/employee/tasks?date=2025-10-25&employee_id=5
```

**Step 5: Employee starts first task**
```
POST /api/tasks/45/start
```

**Step 6: Employee encounters issue, puts on hold**
```
POST /api/tasks/45/hold
Body: {"reason": "Guest still in cabin"}
```

**Step 7: System triggers alert (delay > 30 min)**
- Alert created in database
- Admin receives notification

**Step 8: Employee resumes and completes task**
```
POST /api/tasks/45/complete
```

**Step 9: System auto-calculates duration**
- `actual_duration` = 75 minutes
- `estimated_duration` = 60 minutes
- Creates performance flag (duration exceeded)

**Step 10: Nightly job runs**
```bash
php artisan optimize:reconcile
```
- Analyzes all completed tasks from yesterday
- Updates location base durations if patterns detected
- Updates employee performance metrics

---

## 📝 Notes for Frontend Team

### Admin Dashboard Requirements
1. **Task Calendar** - Show tasks by date with color coding by status
2. **Optimization Results View** - Display teams, tasks per team, fitness score
3. **Save/Re-optimize Buttons** - Allow admin to save or re-run optimization
4. **Alert Dashboard** - Real-time alerts for delayed tasks (refresh every 30s)

### Employee Dashboard Requirements
1. **Task List** - Show today's tasks ordered by scheduled_time
2. **Task Detail View** - Show location, duration, team members
3. **Action Buttons** - Start, Hold (with reason picker), Complete
4. **Status Indicators** - Color-coded status badges

### Client Dashboard Requirements
1. **Service History** - Table of past services
2. **Service Status** - Current service progress (if any)
3. **Feedback Form** - (existing functionality)

---

## 🆘 Support

For questions or issues, contact the development team or refer to:
- GitHub: https://github.com/emmsdgl/opticrew
- Laravel Documentation: https://laravel.com/docs

---

**End of API Documentation**
