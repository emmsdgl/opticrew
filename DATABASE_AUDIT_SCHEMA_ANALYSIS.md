# DATABASE AUDIT & SCHEMA ANALYSIS
## OptiCrew/Fin-noys Cleaning Service Application

**Document Version:** 1.0
**Date:** November 4, 2025
**Laravel Version:** 10
**Database Engine:** MySQL/MariaDB
**Purpose:** Comprehensive database audit for Chapter 3 thesis documentation

---

## TABLE OF CONTENTS

1. [Executive Summary](#executive-summary)
2. [Database Overview](#database-overview)
3. [Core System Tables](#core-system-tables)
4. [Scheduling & Optimization Tables](#scheduling--optimization-tables)
5. [Client Booking Tables](#client-booking-tables)
6. [Administrative Tables](#administrative-tables)
7. [Performance & Monitoring Tables](#performance--monitoring-tables)
8. [Unused/Deprecated Tables](#unuseddeprecated-tables)
9. [Company vs Personal Account Analysis](#company-vs-personal-account-analysis)
10. [Recommendations for Cleanup](#recommendations-for-cleanup)
11. [Entity Relationship Diagram Reference](#entity-relationship-diagram-reference)

---

## EXECUTIVE SUMMARY

### Database Statistics
- **Total Tables:** 31 tables (including Laravel system tables)
- **Application Tables:** 28 tables
- **Core Business Tables:** 7 tables
- **Optimization System Tables:** 8 tables
- **Supporting Tables:** 13 tables
- **Unused/Deprecated Tables:** 3 tables identified

### Key Findings
1. **Company Account Functionality:** Present in multiple tables but can be safely removed
2. **Redundant Tables:** 3 tables (`employee_schedules`, `daily_team_assignments`, `team_members`) replaced by newer optimization tables
3. **Unused Tables:** 2 tables (`scheduling_logs`, `payroll_reports`) created but never implemented
4. **Task Performance History:** Table created but never used (logic integrated into `employee_performance`)

---

## DATABASE OVERVIEW

### All Tables List (Alphabetical)

| # | Table Name | Status | Category | Purpose |
|---|------------|--------|----------|---------|
| 1 | `alerts` | Active | Monitoring | Task delay/issue alerts |
| 2 | `attendances` | Active | Core | Employee clock-in/out records |
| 3 | `cars` | Active | Core | Company vehicles for teams |
| 4 | `client_appointments` | Active | Booking | Client service appointments |
| 5 | `clients` | Active | Core | Personal/company client accounts |
| 6 | `company_settings` | Active | Admin | System settings (geofencing, etc.) |
| 7 | `contracted_clients` | Active | Core | Pre-contracted business clients |
| 8 | `daily_team_assignments` | **DEPRECATED** | Legacy | Old team assignment system |
| 9 | `day_offs` | Active | Admin | Employee day-off requests |
| 10 | `employee_performance` | Active | Monitoring | Daily performance metrics |
| 11 | `employee_schedules` | **DEPRECATED** | Legacy | Old scheduling system |
| 12 | `employees` | Active | Core | Employee profiles |
| 13 | `feedback` | Active | Admin | Client service feedback |
| 14 | `holidays` | Active | Admin | Company holiday calendar |
| 15 | `invalid_tasks` | Active | Optimization | Tasks rejected by optimizer |
| 16 | `jobs` | Active | System | Laravel queue system |
| 17 | `locations` | Active | Core | Client property locations |
| 18 | `notifications` | Active | Admin | User notifications |
| 19 | `optimization_generations` | Active | Optimization | GA generation history |
| 20 | `optimization_runs` | Active | Optimization | Optimization execution records |
| 21 | `optimization_teams` | Active | Optimization | Optimized team assignments |
| 22 | `optimization_team_members` | Active | Optimization | Team member assignments |
| 23 | `payroll_reports` | **UNUSED** | Legacy | Never implemented |
| 24 | `performance_flags` | Active | Monitoring | Task performance issues |
| 25 | `quotations` | Active | Booking | Service quote requests |
| 26 | `scenario_analyses` | Active | Optimization | What-if scenario testing |
| 27 | `scheduling_logs` | **UNUSED** | Legacy | Never implemented |
| 28 | `task_performance_histories` | **DEPRECATED** | Legacy | Replaced by employee_performance |
| 29 | `tasks` | Active | Core | Cleaning tasks/jobs |
| 30 | `team_members` | **DEPRECATED** | Legacy | Old team member system |
| 31 | `users` | Active | Core | User authentication |

---

## CORE SYSTEM TABLES

### 1. `users`
**Purpose:** Central user authentication and authorization table

**Columns:**
```
id                  BIGINT UNSIGNED PRIMARY KEY
name                VARCHAR(255) - Full name
username            VARCHAR(255) UNIQUE NULLABLE - Username for login
email               VARCHAR(255) UNIQUE - Email address
email_verified_at   TIMESTAMP NULLABLE - Email verification timestamp
profile_picture     VARCHAR(255) NULLABLE - Profile image path
phone               VARCHAR(255) NULLABLE - Contact number
location            VARCHAR(255) NULLABLE - User location
password            VARCHAR(255) - Hashed password
role                ENUM('admin', 'employee', 'external_client', 'company')
remember_token      VARCHAR(100) NULLABLE - Remember me token
created_at          TIMESTAMP
updated_at          TIMESTAMP
deleted_at          TIMESTAMP NULLABLE - Soft delete
```

**Relationships:**
- Has one `employees` record (if role = 'employee')
- Has one `clients` record (if role = 'external_client')
- Has one `contracted_clients` record (if role = 'company')
- Has many `notifications`

**Usage:** ACTIVE - Core authentication table used throughout application

**Company-Related Fields:**
- `role` ENUM includes 'company' value

---

### 2. `employees`
**Purpose:** Store employee profiles and work-related information

**Columns:**
```
id                      BIGINT UNSIGNED PRIMARY KEY
user_id                 BIGINT UNSIGNED FOREIGN KEY -> users(id)
skills                  JSON - Employee skill set ['cleaning', 'driving', etc.]
is_active               BOOLEAN DEFAULT true - Currently employed
is_day_off              BOOLEAN DEFAULT false - On day off today
is_busy                 BOOLEAN DEFAULT false - Currently occupied
efficiency              DECIMAL(3,2) DEFAULT 1.00 - Performance multiplier (0.5-2.0)
has_driving_license     BOOLEAN DEFAULT false - Can drive company vehicle
years_of_experience     INT DEFAULT 0 - Work experience
months_employed         INT DEFAULT 0 - Tenure at company
salary_per_hour         DECIMAL(8,2) DEFAULT 13.00 - Hourly wage (EUR)
created_at              TIMESTAMP
updated_at              TIMESTAMP
deleted_at              TIMESTAMP NULLABLE - Soft delete
```

**Relationships:**
- Belongs to `users`
- Has many `day_offs`
- Has many `attendances`
- Has many `employee_performance` records
- Has many `optimization_team_members`

**Usage:** ACTIVE - Core employee management table

**Note:** `full_name` column removed (migration 2025_10_25_154640) - now uses `users.name`

---

### 3. `clients`
**Purpose:** Store external client information (both personal and company inquiries)

**Columns:**
```
id                      BIGINT UNSIGNED PRIMARY KEY
user_id                 BIGINT UNSIGNED NULLABLE FOREIGN KEY -> users(id)
client_type             ENUM('personal', 'company') NULLABLE
company_name            VARCHAR(255) NULLABLE - For company clients
business_id             VARCHAR(20) NULLABLE - Finnish business ID (Y-tunnus)
email                   VARCHAR(255) NULLABLE - For non-registered clients
phone_number            VARCHAR(255) NULLABLE - Contact number
first_name              VARCHAR(255) NULLABLE - Personal client first name
last_name               VARCHAR(255) NULLABLE - Personal client last name
middle_initial          VARCHAR(10) NULLABLE - Middle initial
birthdate               DATE NULLABLE - Date of birth
street_address          VARCHAR(255) NULLABLE - Street address
postal_code             VARCHAR(10) NULLABLE - Postal/ZIP code
city                    VARCHAR(100) NULLABLE - City name
district                VARCHAR(100) NULLABLE - District/area
address                 TEXT NULLABLE - Full address
billing_address         TEXT NULLABLE - Separate billing address
einvoice_number         VARCHAR(100) NULLABLE - E-invoice number
is_active               BOOLEAN DEFAULT true - Active client
security_question_1     VARCHAR(255) NULLABLE - Security question 1
security_answer_1       VARCHAR(255) NULLABLE - Security answer 1
security_question_2     VARCHAR(255) NULLABLE - Security question 2
security_answer_2       VARCHAR(255) NULLABLE - Security answer 2
created_at              TIMESTAMP
updated_at              TIMESTAMP
deleted_at              TIMESTAMP NULLABLE - Soft delete
```

**Relationships:**
- Belongs to `users` (nullable - allows non-registered clients)
- Has many `client_appointments`
- Has many `tasks`
- Has many `feedback` records

**Usage:** ACTIVE - Used for client appointment bookings and quotations

**Company-Related Fields:**
- `client_type` ENUM('personal', 'company')
- `company_name` VARCHAR(255) NULLABLE
- `business_id` VARCHAR(20) NULLABLE

**Note:** `email` and `phone_number` re-added (migration 2025_10_26_183123) to support non-registered clients who book through quotation system

---

### 4. `contracted_clients`
**Purpose:** Store pre-contracted business clients with ongoing service agreements

**Columns:**
```
id                  BIGINT UNSIGNED PRIMARY KEY
user_id             BIGINT UNSIGNED NULLABLE FOREIGN KEY -> users(id)
name                VARCHAR(255) UNIQUE - Company name
email               VARCHAR(255) NULLABLE - Contact email
phone               VARCHAR(255) NULLABLE - Contact phone
address             VARCHAR(255) NULLABLE - Company address
business_id         VARCHAR(255) NULLABLE - Business registration ID
latitude            DECIMAL(10,8) NULLABLE - Location latitude
longitude           DECIMAL(11,8) NULLABLE - Location longitude
contract_start      DATE NULLABLE - Contract start date
contract_end        DATE NULLABLE - Contract end date
created_at          TIMESTAMP
updated_at          TIMESTAMP
deleted_at          TIMESTAMP NULLABLE - Soft delete
```

**Relationships:**
- Belongs to `users` (nullable)
- Has many `locations`
- Has many `daily_team_assignments`
- Has many `tasks`

**Usage:** ACTIVE - Core business logic table for contracted clients (e.g., student housing complexes)

**Note:** Different from `clients` table - these are guaranteed recurring business contracts

---

### 5. `locations`
**Purpose:** Define specific cleaning locations/units within contracted client properties

**Columns:**
```
id                              BIGINT UNSIGNED PRIMARY KEY
contracted_client_id            BIGINT UNSIGNED FOREIGN KEY -> contracted_clients(id)
location_name                   VARCHAR(255) - Location identifier (e.g., "Cabin A1")
location_type                   VARCHAR(255) - Type of space (e.g., "studio", "apartment")
base_cleaning_duration_minutes  INT - Standard cleaning duration
normal_rate_per_hour            DECIMAL(10,2) NULLABLE - Regular rate
sunday_holiday_rate             DECIMAL(10,2) NULLABLE - Weekend/holiday rate
deep_cleaning_rate              DECIMAL(10,2) NULLABLE - Deep clean rate
light_deep_cleaning_rate        DECIMAL(10,2) NULLABLE - Light deep clean rate
student_rate                    DECIMAL(10,2) NULLABLE - Student worker rate
student_sunday_holiday_rate     DECIMAL(10,2) NULLABLE - Student weekend rate
created_at                      TIMESTAMP
updated_at                      TIMESTAMP
deleted_at                      TIMESTAMP NULLABLE - Soft delete
```

**Relationships:**
- Belongs to `contracted_clients`
- Has many `tasks`

**Usage:** ACTIVE - Defines cleanable units with pricing information

**Note:** `number_of_cabins` column removed (migration 2025_10_21_191533) - no longer needed

---

### 6. `cars`
**Purpose:** Company vehicle inventory for team assignments

**Columns:**
```
id              BIGINT UNSIGNED PRIMARY KEY
car_name        VARCHAR(255) - Vehicle identifier (e.g., "Van 1")
is_available    BOOLEAN DEFAULT true - Available for assignment
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

**Relationships:**
- Has many `daily_team_assignments`
- Has many `optimization_teams`

**Usage:** ACTIVE - Used in team assignment and optimization

---

### 7. `tasks`
**Purpose:** Individual cleaning tasks/jobs to be completed

**Columns:**
```
id                          BIGINT UNSIGNED PRIMARY KEY
location_id                 BIGINT UNSIGNED NULLABLE FOREIGN KEY -> locations(id)
client_id                   BIGINT UNSIGNED NULLABLE FOREIGN KEY -> clients(id)
task_description            TEXT - Task details
rate_type                   VARCHAR(255) DEFAULT 'Normal' - 'Normal' or 'Student'
arrival_status              BOOLEAN DEFAULT false - Guest arriving (priority flag)
estimated_duration_minutes  INT - Estimated completion time
scheduled_date              DATE - When task is scheduled
scheduled_time              TIME NULLABLE - Specific time slot
duration                    INT NULLABLE - Duration in minutes
travel_time                 INT DEFAULT 0 - Travel time to location (minutes)
status                      ENUM('Pending','Scheduled','In Progress','On Hold','Completed','Cancelled')
on_hold_reason              VARCHAR(255) NULLABLE - Reason for delay
on_hold_timestamp           TIMESTAMP NULLABLE - When put on hold
actual_duration             INT NULLABLE - Actual time taken (auto-calculated)
started_at                  TIMESTAMP NULLABLE - Task start time
completed_at                TIMESTAMP NULLABLE - Task completion time
assigned_team_id            BIGINT UNSIGNED NULLABLE FOREIGN KEY
reassigned_at               TIMESTAMP NULLABLE - When reassigned
reassignment_reason         TEXT NULLABLE - Why reassigned
required_equipment          JSON NULLABLE - Equipment needed
required_skills             JSON NULLABLE - Skills required
optimization_run_id         BIGINT UNSIGNED NULLABLE FOREIGN KEY -> optimization_runs(id)
assigned_by_generation      INT NULLABLE - GA generation number
created_at                  TIMESTAMP
updated_at                  TIMESTAMP
deleted_at                  TIMESTAMP NULLABLE - Soft delete
```

**Relationships:**
- Belongs to `locations` (nullable)
- Belongs to `clients` (nullable)
- Belongs to `daily_team_assignments` (old system)
- Belongs to `optimization_teams` (new system)
- Belongs to `optimization_runs`
- Has many `alerts`
- Has many `performance_flags`
- Has many `invalid_tasks` records

**Usage:** ACTIVE - Central task management table

**Note:** `latitude` and `longitude` columns removed (migration 2025_10_29_074600) - coordinates moved to `contracted_clients` table

---

## SCHEDULING & OPTIMIZATION TABLES

### 8. `optimization_runs`
**Purpose:** Track genetic algorithm optimization executions

**Columns:**
```
id                          BIGINT UNSIGNED PRIMARY KEY
service_date                DATE - Date being optimized
triggered_by_task_id        BIGINT UNSIGNED NULLABLE FOREIGN KEY -> tasks(id)
status                      ENUM('running','completed','failed') DEFAULT 'running'
is_saved                    BOOLEAN DEFAULT false - Admin saved this schedule
total_tasks                 INT - Number of tasks to assign
total_teams                 INT - Number of teams formed
total_employees             INT - Employees available
employee_allocation_data    JSON NULLABLE - Rule-based phase data
greedy_result_data          JSON NULLABLE - Greedy algorithm phase
final_fitness_score         DECIMAL(8,4) NULLABLE - Best fitness achieved
generations_run             INT DEFAULT 0 - Number of GA generations
error_message               TEXT NULLABLE - Error details if failed
created_at                  TIMESTAMP
updated_at                  TIMESTAMP
deleted_at                  TIMESTAMP NULLABLE - Soft delete
```

**Relationships:**
- Has many `optimization_generations`
- Has many `optimization_teams`
- Has many `tasks`

**Usage:** ACTIVE - Core optimization system table

---

### 9. `optimization_generations`
**Purpose:** Store each generation's results during genetic algorithm execution

**Columns:**
```
id                      BIGINT UNSIGNED PRIMARY KEY
optimization_run_id     BIGINT UNSIGNED FOREIGN KEY -> optimization_runs(id)
generation_number       INT - Generation index (0, 1, 2, ...)
best_fitness            DECIMAL(8,4) - Best fitness in this generation
average_fitness         DECIMAL(8,4) - Average fitness of population
worst_fitness           DECIMAL(8,4) - Worst fitness in population
is_improvement          BOOLEAN DEFAULT false - Better than previous generation
best_schedule_data      JSON - Best schedule of this generation
population_summary      JSON NULLABLE - Summary of all 20 schedules
created_at              TIMESTAMP
updated_at              TIMESTAMP
```

**Relationships:**
- Belongs to `optimization_runs`

**Usage:** ACTIVE - GA algorithm tracking and analysis

**Indexes:**
- Composite index on (optimization_run_id, generation_number)

---

### 10. `optimization_teams`
**Purpose:** Store optimized team compositions (replaces `daily_team_assignments`)

**Columns:**
```
id                      BIGINT UNSIGNED PRIMARY KEY
optimization_run_id     BIGINT UNSIGNED FOREIGN KEY -> optimization_runs(id)
team_index              INT - Team number (1, 2, 3, etc.)
service_date            DATE - Date this team is scheduled
car_id                  BIGINT UNSIGNED NULLABLE FOREIGN KEY -> cars(id)
what_if_scenario_id     BIGINT UNSIGNED NULLABLE - NULL for baseline, ID for what-if
created_at              TIMESTAMP
updated_at              TIMESTAMP
```

**Relationships:**
- Belongs to `optimization_runs`
- Belongs to `cars`
- Has many `optimization_team_members`
- Has many `tasks` (as assigned_team_id)
- Has many `client_appointments`

**Usage:** ACTIVE - Modern team assignment system

**Note:** This table replaces `daily_team_assignments`

---

### 11. `optimization_team_members`
**Purpose:** Define which employees are in each optimization team (replaces `team_members`)

**Columns:**
```
id                      BIGINT UNSIGNED PRIMARY KEY
optimization_team_id    BIGINT UNSIGNED FOREIGN KEY -> optimization_teams(id)
employee_id             BIGINT UNSIGNED FOREIGN KEY -> employees(id)
created_at              TIMESTAMP
updated_at              TIMESTAMP
```

**Relationships:**
- Belongs to `optimization_teams`
- Belongs to `employees`

**Usage:** ACTIVE - Modern team membership tracking

**Unique Constraint:** (optimization_team_id, employee_id) - Prevents duplicate assignments

**Note:** This table replaces `team_members`

---

### 12. `invalid_tasks`
**Purpose:** Track tasks rejected during optimization process

**Columns:**
```
id                      BIGINT UNSIGNED PRIMARY KEY
optimization_result_id  BIGINT UNSIGNED NULLABLE FOREIGN KEY
task_id                 BIGINT UNSIGNED FOREIGN KEY -> tasks(id)
rejection_reason        VARCHAR(255) - Why task was rejected
task_details            JSON NULLABLE - Task data snapshot
created_at              TIMESTAMP
updated_at              TIMESTAMP
```

**Relationships:**
- Belongs to `tasks`

**Usage:** ACTIVE - Optimization debugging and analysis

---

### 13. `scenario_analyses`
**Purpose:** Store what-if scenario simulations

**Columns:**
```
id                  BIGINT UNSIGNED PRIMARY KEY
service_date        DATE - Date being analyzed
scenario_type       VARCHAR(255) - Type of scenario
parameters          JSON - Scenario parameters
modified_schedule   JSON - Modified schedule data
impact_analysis     JSON - Impact metrics
recommendations     JSON NULLABLE - Suggested actions
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

**Relationships:**
- None (standalone analysis table)

**Usage:** ACTIVE - What-if analysis feature

**Index:** Composite index on (service_date, scenario_type)

---

### 14. `day_offs`
**Purpose:** Track employee vacation/sick days/time off

**Columns:**
```
id              BIGINT UNSIGNED PRIMARY KEY
employee_id     BIGINT UNSIGNED FOREIGN KEY -> employees(id)
date            DATE - Day off date
reason          VARCHAR(255) NULLABLE - Reason for time off
type            ENUM('vacation','sick','personal','other') DEFAULT 'personal'
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

**Relationships:**
- Belongs to `employees`

**Usage:** ACTIVE - Used in optimization to exclude unavailable employees

**Unique Constraint:** (employee_id, date) - Prevents duplicate entries

---

## CLIENT BOOKING TABLES

### 15. `client_appointments`
**Purpose:** Store client service appointment bookings

**Columns:**
```
id                      BIGINT UNSIGNED PRIMARY KEY
client_id               BIGINT UNSIGNED FOREIGN KEY -> clients(id)
booking_type            VARCHAR(255) - 'personal' or 'company'
is_company_inquiry      BOOLEAN DEFAULT false - Company service inquiry
service_type            VARCHAR(255) - Type of service requested
company_service_types   JSON NULLABLE - Multiple services for companies
service_date            DATE - Requested service date
service_time            TIME - Requested service time
is_sunday               BOOLEAN DEFAULT false - Weekend booking
is_holiday              BOOLEAN DEFAULT false - Holiday booking
number_of_units         INT - Number of units to clean
unit_size               VARCHAR(255) - Size category ('40-60', '60-90', etc.)
unit_details            JSON NULLABLE - Detailed unit information
cabin_name              VARCHAR(255) - Room/unit identifier
special_requests        TEXT NULLABLE - Additional requests
other_concerns          TEXT NULLABLE - Other client concerns
quotation               DECIMAL(10,2) - Price excluding VAT
vat_amount              DECIMAL(10,2) - VAT amount (24%)
total_amount            DECIMAL(10,2) - Total including VAT
status                  ENUM('pending','approved','rejected','completed','cancelled')
assigned_team_id        BIGINT UNSIGNED NULLABLE FOREIGN KEY -> optimization_teams(id)
recommended_team_id     BIGINT UNSIGNED NULLABLE FOREIGN KEY -> optimization_teams(id)
rejection_reason        TEXT NULLABLE - Why rejected
approved_by             BIGINT UNSIGNED NULLABLE FOREIGN KEY -> users(id)
approved_at             TIMESTAMP NULLABLE
rejected_by             BIGINT UNSIGNED NULLABLE FOREIGN KEY -> users(id)
rejected_at             TIMESTAMP NULLABLE
client_notified         BOOLEAN DEFAULT false - Client notification sent
notified_at             TIMESTAMP NULLABLE
created_at              TIMESTAMP
updated_at              TIMESTAMP
```

**Relationships:**
- Belongs to `clients`
- Belongs to `optimization_teams` (assigned_team_id)
- Belongs to `optimization_teams` (recommended_team_id)
- Belongs to `users` (approved_by)
- Belongs to `users` (rejected_by)

**Usage:** ACTIVE - Client booking system

**Company-Related Fields:**
- `booking_type` VARCHAR(255) - includes 'company'
- `is_company_inquiry` BOOLEAN
- `company_service_types` JSON - for company multi-service requests

---

### 16. `quotations`
**Purpose:** Service quote requests from landing page (non-authenticated users)

**Columns:**
```
id                      BIGINT UNSIGNED PRIMARY KEY
booking_type            ENUM('personal', 'company')
cleaning_services       JSON NULLABLE - Services requested
date_of_service         DATE NULLABLE - Preferred date (personal only)
duration_of_service     INT NULLABLE - Duration in hours/days (personal only)
type_of_urgency         VARCHAR(255) NULLABLE - Urgency level (personal only)
property_type           VARCHAR(255) - Type of property
floors                  INT DEFAULT 1 - Number of floors
rooms                   INT DEFAULT 1 - Number of rooms
people_per_room         INT NULLABLE - Occupancy
floor_area              DECIMAL(10,2) NULLABLE - Total area
area_unit               VARCHAR(255) NULLABLE - 'm2' or 'sqft'
location_type           VARCHAR(255) NULLABLE - 'current' or 'select'
street_address          VARCHAR(255) NULLABLE
postal_code             VARCHAR(10) NULLABLE
city                    VARCHAR(255) NULLABLE
district                VARCHAR(255) NULLABLE
latitude                DECIMAL(10,7) NULLABLE
longitude               DECIMAL(10,7) NULLABLE
company_name            VARCHAR(255) NULLABLE - Company name (company only)
client_name             VARCHAR(255) - Contact person name
phone_number            VARCHAR(255) - Contact phone
email                   VARCHAR(255) - Contact email
estimated_price         DECIMAL(10,2) NULLABLE - Admin quote (excl. VAT)
vat_amount              DECIMAL(10,2) NULLABLE - VAT amount
total_price             DECIMAL(10,2) NULLABLE - Total price
pricing_notes           TEXT NULLABLE - Admin pricing notes
status                  ENUM('pending_review','under_review','quoted','accepted','rejected','converted')
reviewed_by             BIGINT UNSIGNED NULLABLE FOREIGN KEY -> users(id)
reviewed_at             TIMESTAMP NULLABLE
quoted_by               BIGINT UNSIGNED NULLABLE FOREIGN KEY -> users(id)
quoted_at               TIMESTAMP NULLABLE
admin_notes             TEXT NULLABLE - Internal admin notes
rejection_reason        TEXT NULLABLE - Rejection reason
appointment_id          BIGINT UNSIGNED NULLABLE FOREIGN KEY -> client_appointments(id)
converted_by            BIGINT UNSIGNED NULLABLE FOREIGN KEY -> users(id)
converted_at            TIMESTAMP NULLABLE
client_responded_at     TIMESTAMP NULLABLE
client_message          TEXT NULLABLE - Client response message
created_at              TIMESTAMP
updated_at              TIMESTAMP
deleted_at              TIMESTAMP NULLABLE - Soft delete
```

**Relationships:**
- Belongs to `users` (reviewed_by, quoted_by, converted_by)
- Belongs to `client_appointments` (when converted)

**Usage:** ACTIVE - Landing page quotation system

**Company-Related Fields:**
- `booking_type` ENUM includes 'company'
- `company_name` VARCHAR(255) NULLABLE

**Indexes:**
- booking_type
- status
- email
- created_at

---

## ADMINISTRATIVE TABLES

### 17. `notifications`
**Purpose:** User notification system

**Columns:**
```
id              BIGINT UNSIGNED PRIMARY KEY
user_id         BIGINT UNSIGNED FOREIGN KEY -> users(id)
type            VARCHAR(255) - Notification type
title           VARCHAR(255) - Notification heading
message         TEXT - Notification content
data            JSON NULLABLE - Additional data (IDs, links)
read_at         TIMESTAMP NULLABLE - When marked as read
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

**Relationships:**
- Belongs to `users`

**Usage:** ACTIVE - System notifications

**Index:** Composite index on (user_id, read_at)

---

### 18. `holidays`
**Purpose:** Company holiday calendar

**Columns:**
```
id              BIGINT UNSIGNED PRIMARY KEY
date            DATE UNIQUE - Holiday date
name            VARCHAR(255) - Holiday name
created_by      BIGINT UNSIGNED FOREIGN KEY -> users(id)
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

**Relationships:**
- Belongs to `users` (created_by)

**Usage:** ACTIVE - Used in scheduling and pricing (holiday rates)

**Unique Constraint:** date

---

### 19. `feedback`
**Purpose:** Client service feedback and ratings

**Columns:**
```
id                          BIGINT UNSIGNED PRIMARY KEY
client_id                   BIGINT UNSIGNED FOREIGN KEY -> clients(id)
service_type                VARCHAR(255) - Service type reviewed
overall_rating              INT - Overall rating (1-5)
quality_rating              INT - Quality rating (1-5)
cleanliness_rating          INT - Cleanliness rating (1-5)
punctuality_rating          INT - Punctuality rating (1-5)
professionalism_rating      INT - Professionalism rating (1-5)
value_rating                INT - Value for money rating (1-5)
comments                    TEXT - Additional comments
would_recommend             BOOLEAN DEFAULT false - Would recommend service
created_at                  TIMESTAMP
updated_at                  TIMESTAMP
```

**Relationships:**
- Belongs to `clients`

**Usage:** ACTIVE - Service quality tracking

---

### 20. `company_settings`
**Purpose:** Application configuration settings

**Columns:**
```
id              BIGINT UNSIGNED PRIMARY KEY
key             VARCHAR(255) UNIQUE - Setting key
value           TEXT NULLABLE - Setting value
type            VARCHAR(255) DEFAULT 'string' - Value type
description     TEXT NULLABLE - Setting description
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

**Default Settings:**
- `office_latitude`: 14.5995 (Manila, Philippines - testing location)
- `office_longitude`: 120.9842
- `geofence_radius`: 100 (meters)

**Usage:** ACTIVE - System configuration (geofencing, etc.)

**Unique Constraint:** key

---

## PERFORMANCE & MONITORING TABLES

### 21. `attendances`
**Purpose:** Employee clock-in/clock-out records with geofencing

**Columns:**
```
id                      BIGINT UNSIGNED PRIMARY KEY
employee_id             BIGINT UNSIGNED FOREIGN KEY -> employees(id)
clock_in                TIMESTAMP NULLABLE - Clock-in time
clock_in_latitude       DECIMAL(10,8) NULLABLE - Clock-in GPS latitude
clock_in_longitude      DECIMAL(11,8) NULLABLE - Clock-in GPS longitude
clock_in_distance       DECIMAL(8,2) NULLABLE - Distance from office (meters)
clock_out               TIMESTAMP NULLABLE - Clock-out time
clock_out_latitude      DECIMAL(10,8) NULLABLE - Clock-out GPS latitude
clock_out_longitude     DECIMAL(11,8) NULLABLE - Clock-out GPS longitude
clock_out_distance      DECIMAL(8,2) NULLABLE - Distance from office (meters)
total_minutes_worked    INT NULLABLE - Auto-calculated work duration
created_at              TIMESTAMP
updated_at              TIMESTAMP
```

**Relationships:**
- Belongs to `employees`

**Usage:** ACTIVE - Time tracking with geofencing validation

**Unique Constraint:** (employee_id, clock_in)

---

### 22. `employee_performance`
**Purpose:** Daily employee performance aggregation

**Columns:**
```
id                          BIGINT UNSIGNED PRIMARY KEY
employee_id                 BIGINT UNSIGNED FOREIGN KEY -> employees(id)
date                        DATE - Performance date
tasks_completed             INT DEFAULT 0 - Tasks completed today
total_performance_score     DECIMAL(8,4) DEFAULT 0 - Total score
average_performance         DECIMAL(8,4) DEFAULT 0 - Score > 1.0 = faster, < 1.0 = slower
created_at                  TIMESTAMP
updated_at                  TIMESTAMP
```

**Relationships:**
- Belongs to `employees`

**Usage:** ACTIVE - Performance tracking and efficiency calculation

**Unique Constraint:** (employee_id, date)

**Index:** date

---

### 23. `performance_flags`
**Purpose:** Flag tasks with performance issues

**Columns:**
```
id                  BIGINT UNSIGNED PRIMARY KEY
task_id             BIGINT UNSIGNED FOREIGN KEY -> tasks(id)
employee_id         BIGINT UNSIGNED NULLABLE FOREIGN KEY -> employees(id)
team_id             BIGINT UNSIGNED NULLABLE - optimization_teams reference
flag_type           VARCHAR(255) - Flag type (duration_exceeded, quality_issue)
estimated_minutes   INT NULLABLE - Original estimate
actual_minutes      INT NULLABLE - Actual time taken
variance_minutes    INT NULLABLE - Difference
flagged_at          TIMESTAMP - When flagged
reviewed            BOOLEAN DEFAULT false - Admin reviewed
reviewed_by         BIGINT UNSIGNED NULLABLE FOREIGN KEY -> users(id)
reviewed_at         TIMESTAMP NULLABLE
review_notes        TEXT NULLABLE - Admin notes
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

**Relationships:**
- Belongs to `tasks`
- Belongs to `employees`
- Belongs to `users` (reviewed_by)

**Usage:** ACTIVE - Performance issue tracking

**Indexes:**
- (task_id, flag_type)
- reviewed

---

### 24. `alerts`
**Purpose:** System-generated alerts for task delays/issues

**Columns:**
```
id                  BIGINT UNSIGNED PRIMARY KEY
task_id             BIGINT UNSIGNED FOREIGN KEY -> tasks(id)
alert_type          VARCHAR(255) - Alert type
delay_minutes       INT NULLABLE - Delay amount
reason              TEXT NULLABLE - Alert reason
triggered_at        TIMESTAMP - When alert was triggered
acknowledged_at     TIMESTAMP NULLABLE - When admin acknowledged
acknowledged_by     BIGINT UNSIGNED NULLABLE FOREIGN KEY -> users(id)
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

**Relationships:**
- Belongs to `tasks`
- Belongs to `users` (acknowledged_by)

**Usage:** ACTIVE - Real-time task monitoring

**Index:** (task_id, alert_type)

---

## UNUSED/DEPRECATED TABLES

### 25. `scheduling_logs` (UNUSED)
**Purpose:** Originally intended to store scheduling algorithm reasoning

**Columns:**
```
id              BIGINT UNSIGNED PRIMARY KEY
schedule_date   DATE
log_data        LONGTEXT - JSON scheduling data
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

**Status:** NEVER IMPLEMENTED
**Created:** Migration 2025_10_05_133300
**Usage:** Table created but no code references found
**Recommendation:** SAFE TO DROP

---

### 26. `payroll_reports` (UNUSED)
**Purpose:** Originally intended to store generated payroll reports

**Columns:**
```
id                  BIGINT UNSIGNED PRIMARY KEY
employee_id         BIGINT UNSIGNED FOREIGN KEY -> employees(id)
pay_period_start    DATE
pay_period_end      DATE
total_hours         DECIMAL(8,2)
total_pay           DECIMAL(10,2)
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

**Status:** NEVER IMPLEMENTED
**Created:** Migration 2025_10_08_162339
**Usage:** Table created but no code references found
**Model:** No model file exists
**Recommendation:** SAFE TO DROP (payroll calculated on-demand from attendances)

---

### 27. `task_performance_histories` (DEPRECATED)
**Purpose:** Originally intended to track task completion history

**Columns:**
```
id                          BIGINT UNSIGNED PRIMARY KEY
task_id                     BIGINT UNSIGNED FOREIGN KEY -> tasks(id)
estimated_duration_minutes  INT
actual_duration_minutes     INT
completed_at                TIMESTAMP
created_at                  TIMESTAMP
updated_at                  TIMESTAMP
```

**Status:** REPLACED BY `employee_performance` TABLE
**Created:** Migration 2025_10_02_130207
**Replacement Logic:** Performance tracking integrated into `employee_performance` with daily aggregation
**Recommendation:** SAFE TO DROP (functionality moved to employee_performance table)

---

### 28. `employee_schedules` (DEPRECATED)
**Purpose:** Old scheduling system for employee work dates

**Columns:**
```
id              BIGINT UNSIGNED PRIMARY KEY
employee_id     BIGINT UNSIGNED FOREIGN KEY -> employees(id)
work_date       DATE
is_day_off      BOOLEAN DEFAULT false
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

**Status:** REPLACED BY `day_offs` TABLE
**Created:** Migration 2025_10_02_130207
**Replacement:** `day_offs` table with better structure
**Unique Constraint:** (employee_id, work_date)
**Model:** EmployeeSchedule.php exists but unused
**Recommendation:** SAFE TO DROP (replaced by day_offs table)

---

### 29. `daily_team_assignments` (DEPRECATED)
**Purpose:** Old team assignment system

**Columns:**
```
id                      BIGINT UNSIGNED PRIMARY KEY
assignment_date         DATE
car_id                  BIGINT UNSIGNED NULLABLE FOREIGN KEY -> cars(id)
contracted_client_id    BIGINT UNSIGNED FOREIGN KEY -> contracted_clients(id)
created_at              TIMESTAMP
updated_at              TIMESTAMP
```

**Status:** REPLACED BY `optimization_teams` TABLE
**Created:** Migration 2025_10_02_130207
**Replacement:** Modern `optimization_teams` with better optimization integration
**Recommendation:** SAFE TO DROP AFTER DATA MIGRATION (if any legacy data exists)

---

### 30. `team_members` (DEPRECATED)
**Purpose:** Old team membership tracking

**Columns:**
```
id              BIGINT UNSIGNED PRIMARY KEY
daily_team_id   BIGINT UNSIGNED FOREIGN KEY -> daily_team_assignments(id)
employee_id     BIGINT UNSIGNED FOREIGN KEY -> employees(id)
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

**Status:** REPLACED BY `optimization_team_members` TABLE
**Created:** Migration 2025_10_02_130207
**Replacement:** Modern `optimization_team_members`
**Recommendation:** SAFE TO DROP AFTER DATA MIGRATION (if any legacy data exists)

---

### 31. `jobs` (LARAVEL SYSTEM TABLE)
**Purpose:** Laravel queue system for background jobs

**Columns:**
```
id              BIGINT UNSIGNED PRIMARY KEY
queue           VARCHAR(255) - Queue name
payload         LONGTEXT - Job payload
attempts        TINYINT UNSIGNED - Retry attempts
reserved_at     INT UNSIGNED NULLABLE - Job reservation time
available_at    INT UNSIGNED - When job becomes available
created_at      INT UNSIGNED
```

**Status:** ACTIVE LARAVEL SYSTEM TABLE
**Created:** Migration 2025_10_17_111652
**Usage:** Required for Laravel queue system (optimization runs use queues)
**Recommendation:** KEEP - Required for system operation

---

## COMPANY VS PERSONAL ACCOUNT ANALYSIS

### Current State: Dual Account Types

The application currently supports both **Personal** and **Company** client accounts through multiple pathways:

#### 1. Company Account Implementation Points

**A. User Roles (`users` table)**
```sql
role ENUM('admin', 'employee', 'external_client', 'company')
```
- Added in migration: 2025_10_29_152501
- Allows company users to log in

**B. Client Type (`clients` table)**
```sql
client_type ENUM('personal', 'company') NULLABLE
company_name VARCHAR(255) NULLABLE
business_id VARCHAR(20) NULLABLE
```
- Supports both personal and company client profiles
- Company fields: `company_name`, `business_id`

**C. Client Appointments (`client_appointments` table)**
```sql
booking_type VARCHAR(255) -- 'personal' or 'company'
is_company_inquiry BOOLEAN DEFAULT false
company_service_types JSON NULLABLE
```
- Allows companies to book multiple service types
- Company-specific inquiry flag

**D. Quotations (`quotations` table)**
```sql
booking_type ENUM('personal', 'company')
company_name VARCHAR(255) NULLABLE
```
- Landing page quote requests support companies
- Company-specific fields in quotation form

**E. Contracted Clients (`contracted_clients` table)**
```sql
user_id BIGINT UNSIGNED NULLABLE -- Links to company user account
```
- Added in migration: 2025_10_29_135821
- Links pre-contracted clients to user accounts

---

### Impact Analysis: Removing Company Signup

If you decide to **REMOVE Company signup functionality** and keep **ONLY Personal accounts**:

#### Tables to Modify

**1. `users` table**
```sql
-- REMOVE 'company' from role enum
ALTER TABLE users MODIFY COLUMN role
ENUM('admin', 'employee', 'external_client') NOT NULL;
```
**Migration Impact:** Reverse migration 2025_10_29_152501

---

**2. `clients` table**
```sql
-- REMOVE company-related columns
ALTER TABLE clients DROP COLUMN client_type;
ALTER TABLE clients DROP COLUMN company_name;
ALTER TABLE clients DROP COLUMN business_id;
```
**Affected Columns:**
- `client_type` ENUM('personal', 'company')
- `company_name` VARCHAR(255)
- `business_id` VARCHAR(20)

**Migration Impact:** Create new migration to remove these columns

---

**3. `client_appointments` table**
```sql
-- REMOVE company-specific columns
ALTER TABLE client_appointments DROP COLUMN booking_type;
ALTER TABLE client_appointments DROP COLUMN is_company_inquiry;
ALTER TABLE client_appointments DROP COLUMN company_service_types;
```
**Affected Columns:**
- `booking_type` VARCHAR(255)
- `is_company_inquiry` BOOLEAN
- `company_service_types` JSON

**Migration Impact:** Create new migration to remove these columns

---

**4. `quotations` table**
```sql
-- CHANGE booking_type to fixed 'personal'
ALTER TABLE quotations MODIFY COLUMN booking_type VARCHAR(255) DEFAULT 'personal';
ALTER TABLE quotations DROP COLUMN company_name;

-- OR remove enum and just assume all are personal
ALTER TABLE quotations DROP COLUMN booking_type;
ALTER TABLE quotations DROP COLUMN company_name;
```
**Affected Columns:**
- `booking_type` ENUM('personal', 'company')
- `company_name` VARCHAR(255)

**Migration Impact:** Simplify quotations to personal-only

---

**5. `contracted_clients` table**
```sql
-- REMOVE user_id link (or keep for future use)
ALTER TABLE contracted_clients DROP FOREIGN KEY contracted_clients_user_id_foreign;
ALTER TABLE contracted_clients DROP COLUMN user_id;
```
**Decision Point:**
- **Option A:** Remove `user_id` completely - contracted clients managed only by admin
- **Option B:** Keep `user_id` for future expansion - no harm in keeping it

**Recommendation:** Keep `user_id` NULLABLE but don't expose company signup UI

---

#### Application Code Changes Required

**1. Controllers to Update:**
- `ClientRegistrationController.php` - Remove company registration logic
- `ClientAppointmentController.php` - Remove company booking logic
- `QuotationController.php` - Simplify to personal-only quotes
- `ProfileController.php` - Remove company profile management

**2. Views to Update:**
- Registration forms - Remove company option
- Appointment booking forms - Remove company booking type
- Quotation forms - Remove company fields
- Profile pages - Remove company information display

**3. Models to Update:**
- `Client.php` - Remove company-related attributes from fillable
- `ClientAppointment.php` - Remove company-related attributes
- `Quotation.php` - Update validation rules
- `User.php` - Update role validation

**4. Validation Rules:**
- Remove company field validations
- Update enum validations
- Simplify booking type logic

---

### Recommendations

#### Option 1: Full Removal (Clean Approach)
**Pros:**
- Cleaner database schema
- Simpler codebase
- Focused on personal clients only
- Easier to maintain

**Cons:**
- Permanent decision - hard to revert
- Loses potential business expansion path

**Recommended If:** You're certain company signup will NEVER be needed

---

#### Option 2: Soft Removal (Flexible Approach)
**Keep the columns but:**
- Remove UI for company signup
- Hide company options in forms
- Keep database structure intact
- Set default values to 'personal'

**Pros:**
- Easy to re-enable if needed
- Minimal database changes
- Future-proof design
- Low risk

**Cons:**
- "Dead" columns in database
- Slight code complexity

**Recommended If:** You might want company signup in future (post-thesis)

---

#### Our Recommendation: **Option 2 (Soft Removal)**

**Rationale:**
1. You're in thesis phase - focus on core functionality
2. Keep database flexible for future expansion
3. Minimal disruption to existing code
4. Easy to document as "future enhancement" in thesis
5. No data loss risk

**Implementation Steps:**
1. **Frontend:** Remove company signup UI elements
2. **Backend:** Default all bookings to 'personal'
3. **Validation:** Accept only 'personal' type
4. **Database:** Keep columns but mark as deprecated in documentation
5. **Documentation:** Note as "reserved for future use"

---

## RECOMMENDATIONS FOR CLEANUP

### High Priority: Safe to Remove

**1. Drop Unused Tables**
```sql
-- Never implemented
DROP TABLE IF EXISTS scheduling_logs;
DROP TABLE IF EXISTS payroll_reports;
DROP TABLE IF EXISTS task_performance_histories;
```
**Rationale:** No code references, no data loss risk

---

**2. Drop Deprecated Tables (After Data Migration)**
```sql
-- Old scheduling system
DROP TABLE IF EXISTS team_members;
DROP TABLE IF EXISTS daily_team_assignments;
DROP TABLE IF EXISTS employee_schedules;
```
**Rationale:** Replaced by newer tables
**Action Required:** Migrate any legacy data first (if exists)

---

### Medium Priority: Consider Simplification

**3. Simplify Client Tables**
- Remove company-related columns from `clients`
- Remove company-related columns from `client_appointments`
- Simplify `quotations` to personal-only

**Rationale:** Focus on core personal client functionality for thesis

---

**4. Consolidate Performance Tables**
- `employee_performance` already aggregates task performance
- `performance_flags` and `alerts` could potentially be merged
- Consider if both are needed or if one comprehensive monitoring table suffices

---

### Low Priority: Optimization Opportunities

**5. Add Missing Indexes**
```sql
-- Already good index coverage from migration 2025_10_27_200441
-- Consider additional indexes based on query patterns:

ALTER TABLE tasks ADD INDEX idx_tasks_status_date (status, scheduled_date);
ALTER TABLE optimization_runs ADD INDEX idx_runs_date_status (service_date, status);
```

---

**6. Review JSON Columns**
Many tables use JSON columns. Consider if frequently queried JSON fields should be promoted to regular columns:
- `tasks.required_skills`
- `tasks.required_equipment`
- `client_appointments.unit_details`
- `quotations.cleaning_services`

**Trade-off:** Flexibility vs. Query Performance

---

## ENTITY RELATIONSHIP DIAGRAM REFERENCE

### Core Relationships

```
users (1) ----< (1) clients
users (1) ----< (1) employees
users (1) ----< (1) contracted_clients
users (1) ----< (*) notifications

employees (1) ----< (*) day_offs
employees (1) ----< (*) attendances
employees (1) ----< (*) employee_performance
employees (1) ----< (*) optimization_team_members

contracted_clients (1) ----< (*) locations
contracted_clients (1) ----< (*) tasks

clients (1) ----< (*) client_appointments
clients (1) ----< (*) tasks
clients (1) ----< (*) feedback

locations (1) ----< (*) tasks

optimization_runs (1) ----< (*) optimization_generations
optimization_runs (1) ----< (*) optimization_teams
optimization_runs (1) ----< (*) tasks

optimization_teams (1) ----< (*) optimization_team_members
optimization_teams (1) ----< (*) tasks (as assigned_team_id)
optimization_teams (1) ----< (*) client_appointments

tasks (1) ----< (*) alerts
tasks (1) ----< (*) performance_flags
tasks (1) ----< (*) invalid_tasks

cars (1) ----< (*) optimization_teams
```

---

### Table Dependency Hierarchy

**Level 1: Independent Tables**
- `users`
- `cars`
- `holidays`
- `company_settings`

**Level 2: Depends on Users**
- `employees` (→ users)
- `clients` (→ users)
- `contracted_clients` (→ users)
- `notifications` (→ users)

**Level 3: Depends on Level 2**
- `locations` (→ contracted_clients)
- `day_offs` (→ employees)
- `attendances` (→ employees)
- `employee_performance` (→ employees)
- `client_appointments` (→ clients)
- `feedback` (→ clients)

**Level 4: Task Management**
- `optimization_runs` (→ tasks)
- `tasks` (→ locations, clients, contracted_clients)

**Level 5: Optimization System**
- `optimization_generations` (→ optimization_runs)
- `optimization_teams` (→ optimization_runs, cars)
- `optimization_team_members` (→ optimization_teams, employees)
- `invalid_tasks` (→ tasks)

**Level 6: Monitoring**
- `alerts` (→ tasks)
- `performance_flags` (→ tasks, employees)

---

## DATA DICTIONARY SUMMARY

### Column Type Standards

**ID Columns:**
- `BIGINT UNSIGNED` for all primary keys and foreign keys
- Auto-increment for primary keys

**Strings:**
- `VARCHAR(255)` for most text fields
- `VARCHAR(100)` for cities, names
- `VARCHAR(20)` for codes, phone numbers
- `TEXT` for long-form content
- `LONGTEXT` for JSON payloads

**Numbers:**
- `INT` for counts, durations (minutes)
- `DECIMAL(8,2)` for hourly rates, small amounts
- `DECIMAL(10,2)` for prices, larger amounts
- `DECIMAL(3,2)` for multipliers (efficiency: 0.5-2.0)
- `DECIMAL(8,4)` for fitness scores

**Coordinates:**
- `DECIMAL(10,8)` for latitude
- `DECIMAL(11,8)` for longitude

**Dates/Times:**
- `DATE` for dates only
- `TIME` for times only
- `TIMESTAMP` for datetime values
- `TIMESTAMP NULLABLE` for optional datetime fields

**Flags:**
- `BOOLEAN` for yes/no fields
- `ENUM` for fixed choice lists

**Complex Data:**
- `JSON` for arrays and objects

---

## APPENDIX A: Migration Timeline

| Date | Migration | Purpose |
|------|-----------|---------|
| 2025-10-02 | create_all_tables | Initial schema |
| 2025-10-05 | scheduling_logs | Unused logging table |
| 2025-10-08 | time_tracking_and_reporting | Attendance system |
| 2025-10-16 | optimization_runs | GA optimization |
| 2025-10-16 | optimization_tracking | Task optimization link |
| 2025-10-16 | opt_gen_run_gen_idx | Optimization indexes |
| 2025-10-17 | invalid_tasks | Rejected tasks |
| 2025-10-17 | scenario_analyses | What-if scenarios |
| 2025-10-17 | jobs_table | Laravel queue |
| 2025-10-17 | optimization_columns | Employee/task fields |
| 2025-10-17 | day_offs | Time-off tracking |
| 2025-10-20 | new_optimization_fields | Real-time tracking |
| 2025-10-20 | fix_tasks_status_enum | Status field update |
| 2025-10-20 | optimization_teams_members | New team system |
| 2025-10-21 | finnish_address_fields | Client address fields |
| 2025-10-21 | remove_number_of_cabins | Locations cleanup |
| 2025-10-21 | pricing_columns | Location pricing |
| 2025-10-22 | client_appointments | Booking system |
| 2025-10-22 | holidays | Holiday calendar |
| 2025-10-24 | soft_deletes | Audit trail |
| 2025-10-24 | unique_constraints | Data integrity |
| 2025-10-25 | profile_picture_users | User profiles |
| 2025-10-25 | username_users | Username login |
| 2025-10-25 | remove_redundant_columns | Data cleanup |
| 2025-10-25 | company_settings | Configuration |
| 2025-10-25 | geofencing_attendances | GPS tracking |
| 2025-10-26 | business_id_clients | Company field |
| 2025-10-26 | salary_per_hour | Employee wages |
| 2025-10-26 | update_salary_default | Wage update |
| 2025-10-26 | service_inquiry_fields | Company bookings |
| 2025-10-26 | make_user_id_nullable | Guest clients |
| 2025-10-26 | contact_fields_clients | Client contact |
| 2025-10-26 | notifications | Notification system |
| 2025-10-27 | feedback | Client feedback |
| 2025-10-27 | unit_details_appointments | Booking details |
| 2025-10-27 | performance_indexes | Query optimization |
| 2025-10-29 | coordinates_contracted_clients | Location coords |
| 2025-10-29 | remove_coordinates_tasks | Task coords cleanup |
| 2025-10-29 | rate_type_tasks | Rate types |
| 2025-10-29 | company_role_link | Company accounts |
| 2025-10-29 | contact_fields_contracted | Company contacts |
| 2025-10-29 | company_role_users | Company user role |
| 2025-10-29 | quotations | Quote requests |

---

## APPENDIX B: Model-Table Mapping

| Model | Table | Status |
|-------|-------|--------|
| User | users | Active |
| Employee | employees | Active |
| Client | clients | Active |
| ContractedClient | contracted_clients | Active |
| Location | locations | Active |
| Car | cars | Active |
| Task | tasks | Active |
| EmployeeSchedule | employee_schedules | Deprecated |
| DailyTeamAssignment | daily_team_assignments | Deprecated |
| TeamMember | team_members | Deprecated |
| OptimizationRun | optimization_runs | Active |
| OptimizationGeneration | optimization_generations | Active |
| OptimizationTeam | optimization_teams | Active |
| OptimizationTeamMember | optimization_team_members | Active |
| InvalidTask | invalid_tasks | Active |
| ScenarioAnalysis | scenario_analyses | Active |
| DayOff | day_offs | Active |
| Attendance | attendances | Active |
| EmployeePerformance | employee_performance | Active |
| PerformanceFlag | performance_flags | Active |
| Alert | alerts | Active |
| ClientAppointment | client_appointments | Active |
| Quotation | quotations | Active |
| Holiday | holidays | Active |
| Notification | notifications | Active |
| Feedback | feedback | Active |
| N/A | company_settings | Active (no model) |
| N/A | jobs | Active (Laravel) |
| N/A | scheduling_logs | Unused |
| N/A | payroll_reports | Unused |
| N/A | task_performance_histories | Deprecated |

---

## APPENDIX C: Foreign Key Constraints

### Critical Cascading Deletes

**Users → Related Records**
- `employees.user_id` → CASCADE
- `clients.user_id` → CASCADE
- `contracted_clients.user_id` → CASCADE
- `notifications.user_id` → CASCADE

**Employees → Related Records**
- `day_offs.employee_id` → CASCADE
- `attendances.employee_id` → CASCADE
- `employee_performance.employee_id` → CASCADE
- `optimization_team_members.employee_id` → CASCADE

**Tasks → Related Records**
- `alerts.task_id` → CASCADE
- `performance_flags.task_id` → CASCADE
- `invalid_tasks.task_id` → CASCADE

**Optimization System**
- `optimization_generations.optimization_run_id` → CASCADE
- `optimization_teams.optimization_run_id` → CASCADE
- `optimization_team_members.optimization_team_id` → CASCADE

### SET NULL Behavior

**Nullable References:**
- `tasks.assigned_team_id` → SET NULL
- `tasks.optimization_run_id` → SET NULL
- `tasks.location_id` → SET NULL (when location deleted)
- `optimization_teams.car_id` → SET NULL (when car deleted)

---

## CONCLUSION

This database audit reveals a **well-structured but evolving** schema with:

1. **Strong core architecture** - User, employee, client, and task management
2. **Advanced optimization system** - Genetic algorithm integration
3. **Comprehensive monitoring** - Performance tracking and alerting
4. **Flexible booking system** - Multiple client entry points

### Immediate Actions for Thesis:

1. **Remove unused tables** - Clean up 3 unused/deprecated tables
2. **Simplify company logic** - Remove or soft-hide company signup features
3. **Update documentation** - Use this audit for Chapter 3 Data Dictionary
4. **Generate ERD** - Use relationship section to create visual diagram

### Database Health: 8.5/10
- Excellent structure and relationships
- Good indexing strategy
- Minor cleanup needed
- Well-documented through migrations

---

**Document End**

*For questions or clarifications, refer to migration files in `database/migrations/` and model files in `app/Models/`*
