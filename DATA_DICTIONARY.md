# DATA DICTIONARY
## OptiCrew Cleaning Service Management System

**Document Version:** 1.0
**Date:** November 4, 2025
**Database Engine:** MySQL 10.4.32-MariaDB
**Framework:** Laravel 10
**Purpose:** Thesis Chapter 3 - Database Documentation

---

## TABLE OF CONTENTS

### 1. CORE SYSTEM TABLES
- 1.1 [users](#11-users)

### 2. CLIENT MANAGEMENT TABLES
- 2.1 [clients](#21-clients)
- 2.2 [client_appointments](#22-client_appointments)
- 2.3 [quotations](#23-quotations)
- 2.4 [feedback](#24-feedback)
- 2.5 [contracted_clients](#25-contracted_clients)

### 3. EMPLOYEE MANAGEMENT TABLES
- 3.1 [employees](#31-employees)
- 3.2 [attendances](#32-attendances)
- 3.3 [employee_performance](#33-employee_performance)
- 3.4 [day_offs](#34-day_offs)

### 4. TASK & SCHEDULING TABLES
- 4.1 [tasks](#41-tasks)
- 4.2 [locations](#42-locations)
- 4.3 [holidays](#43-holidays)

### 5. OPTIMIZATION SYSTEM TABLES
- 5.1 [optimization_runs](#51-optimization_runs)
- 5.2 [optimization_generations](#52-optimization_generations)
- 5.3 [optimization_teams](#53-optimization_teams)
- 5.4 [optimization_team_members](#54-optimization_team_members)
- 5.5 [scenario_analyses](#55-scenario_analyses)
- 5.6 [invalid_tasks](#56-invalid_tasks)

### 6. MONITORING & ALERTS TABLES
- 6.1 [alerts](#61-alerts)
- 6.2 [performance_flags](#62-performance_flags)

### 7. ADMINISTRATIVE TABLES
- 7.1 [notifications](#71-notifications)
- 7.2 [company_settings](#72-company_settings)
- 7.3 [cars](#73-cars)

### 8. SYSTEM TABLES
- 8.1 [jobs](#81-jobs)
- 8.2 [migrations](#82-migrations)
- 8.3 [personal_access_tokens](#83-personal_access_tokens)

### 9. APPENDICES
- [Appendix A: Data Type Reference](#appendix-a-data-type-reference)
- [Appendix B: Relationship Diagram](#appendix-b-relationship-diagram)
- [Appendix C: Naming Conventions](#appendix-c-naming-conventions)

---

## 1. CORE SYSTEM TABLES

### 1.1 users

**Description:** Central authentication and authorization table for all system users including administrators, employees, and external clients. This table implements role-based access control (RBAC) and serves as the foundation for the entire user management system.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique user identifier |
| name | VARCHAR | 255 | NOT NULL | - | User's full name |
| username | VARCHAR | 255 | NULLABLE, UNIQUE | NULL | Username for login (optional) |
| email | VARCHAR | 255 | NOT NULL, UNIQUE | - | User's email address for authentication |
| profile_picture | VARCHAR | 255 | NULLABLE | NULL | File path to user's profile image |
| phone | VARCHAR | 255 | NULLABLE | NULL | Contact phone number |
| location | VARCHAR | 255 | NULLABLE | NULL | User's location/address |
| email_verified_at | TIMESTAMP | - | NULLABLE | NULL | Email verification timestamp |
| password | VARCHAR | 255 | NOT NULL | - | Bcrypt hashed password |
| role | ENUM | - | NOT NULL | - | User role: 'admin', 'employee', 'external_client', 'company' |
| remember_token | VARCHAR | 100 | NULLABLE | NULL | Token for "remember me" functionality |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |
| deleted_at | TIMESTAMP | - | NULLABLE | NULL | Soft delete timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE: `email`
- UNIQUE: `username` (where not null)

**Relationships:**
- One-to-One with `employees` (employees.user_id → users.id)
- One-to-One with `clients` (clients.user_id → users.id)
- One-to-One with `contracted_clients` (contracted_clients.user_id → users.id)
- One-to-Many with `notifications` (notifications.user_id → users.id)
- One-to-Many with `holidays` (holidays.created_by → users.id)
- One-to-Many with `alerts` (alerts.acknowledged_by → users.id)
- One-to-Many with `client_appointments` (approved_by, rejected_by → users.id)
- One-to-Many with `quotations` (reviewed_by, quoted_by, converted_by → users.id)

**Business Rules:**
- Email must be unique across all users
- Password must be hashed using bcrypt (Laravel default)
- Role determines access level and available features in the system
- email_verified_at is set after successful email verification during signup
- Soft deletes are implemented (deleted_at column) for data retention

---

## 2. CLIENT MANAGEMENT TABLES

### 2.1 clients

**Description:** Stores detailed information about clients (both personal and company) who request cleaning services. This table extends the users table with client-specific information including personal details, security questions, and billing information.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique client identifier |
| user_id | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | Reference to users table |
| first_name | VARCHAR | 255 | NOT NULL | - | Client's first name |
| last_name | VARCHAR | 255 | NOT NULL | - | Client's last name |
| middle_initial | VARCHAR | 5 | NULLABLE | NULL | Client's middle initial |
| birthdate | DATE | - | NULLABLE | NULL | Client's date of birth |
| security_question_1 | VARCHAR | 255 | NULLABLE | NULL | First security question |
| security_answer_1 | VARCHAR | 255 | NULLABLE | NULL | Answer to first security question |
| security_question_2 | VARCHAR | 255 | NULLABLE | NULL | Second security question |
| security_answer_2 | VARCHAR | 255 | NULLABLE | NULL | Answer to second security question |
| client_type | ENUM | - | NULLABLE | NULL | Type: 'personal' or 'company' |
| company_name | VARCHAR | 255 | NULLABLE | NULL | Company name (for company clients) |
| email | VARCHAR | 255 | NULLABLE | NULL | Client's email address |
| phone_number | VARCHAR | 255 | NULLABLE | NULL | Client's contact number |
| business_id | VARCHAR | 20 | NULLABLE | NULL | Business registration ID |
| street_address | VARCHAR | 255 | NULLABLE | NULL | Street address |
| postal_code | VARCHAR | 10 | NULLABLE | NULL | Postal/ZIP code |
| city | VARCHAR | 100 | NULLABLE | NULL | City name |
| district | VARCHAR | 100 | NULLABLE | NULL | District/region |
| address | TEXT | - | NULLABLE | NULL | Full address |
| billing_address | TEXT | - | NULLABLE | NULL | Billing address (if different) |
| einvoice_number | VARCHAR | 100 | NULLABLE | NULL | Electronic invoice number |
| is_active | TINYINT(1) | - | NOT NULL | 1 | Client active status |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |
| deleted_at | TIMESTAMP | - | NULLABLE | NULL | Soft delete timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `user_id` references `users(id)`

**Relationships:**
- Many-to-One with `users` (user_id → users.id) ON DELETE CASCADE
- One-to-Many with `client_appointments` (client_appointments.client_id → clients.id)
- One-to-Many with `feedback` (feedback.client_id → clients.id)
- One-to-Many with `tasks` (tasks.client_id → clients.id)

**Business Rules:**
- user_id links to external_client users in the users table
- client_type determines if the client is an individual or company
- company_name is required when client_type is 'company'
- security_questions are used for account recovery
- is_active flag allows deactivating clients without deletion
- Soft deletes preserve historical data

---

### 2.2 client_appointments

**Description:** Manages booking requests and appointments from clients for cleaning services. This table handles both personal and company inquiries, tracks service details, quotations, and appointment status throughout the workflow from pending to completed.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique appointment identifier |
| client_id | BIGINT UNSIGNED | - | FOREIGN KEY, NOT NULL | - | Reference to clients table |
| is_company_inquiry | TINYINT(1) | - | NOT NULL | 0 | Flag for company inquiry vs personal |
| booking_type | VARCHAR | 255 | NOT NULL | - | Type of booking (one-time, recurring, etc.) |
| service_type | VARCHAR | 255 | NOT NULL | - | Main service type requested |
| company_service_types | LONGTEXT | - | NULLABLE, JSON | NULL | Array of service types for company bookings |
| service_date | DATE | - | NOT NULL | - | Scheduled service date |
| service_time | TIME | - | NOT NULL | - | Scheduled service time |
| is_sunday | TINYINT(1) | - | NOT NULL | 0 | Flag if service is on Sunday |
| is_holiday | TINYINT(1) | - | NOT NULL | 0 | Flag if service is on holiday |
| number_of_units | INT | - | NOT NULL | - | Number of units to be cleaned |
| unit_size | VARCHAR | 255 | NOT NULL | - | Size of each unit (small, medium, large) |
| unit_details | LONGTEXT | - | NULLABLE, JSON | NULL | Detailed unit information |
| cabin_name | VARCHAR | 255 | NOT NULL | - | Cabin/location name |
| special_requests | TEXT | - | NULLABLE | NULL | Client's special requests |
| other_concerns | TEXT | - | NULLABLE | NULL | Additional concerns or notes |
| quotation | DECIMAL | 10,2 | NOT NULL | - | Base quotation amount |
| vat_amount | DECIMAL | 10,2 | NOT NULL | - | VAT/tax amount |
| total_amount | DECIMAL | 10,2 | NOT NULL | - | Total amount (quotation + VAT) |
| status | ENUM | - | NOT NULL | 'pending' | Status: 'pending', 'approved', 'rejected', 'completed', 'cancelled' |
| assigned_team_id | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | Assigned optimization team |
| recommended_team_id | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | Recommended team from optimization |
| rejection_reason | TEXT | - | NULLABLE | NULL | Reason if appointment rejected |
| approved_by | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | User who approved appointment |
| approved_at | TIMESTAMP | - | NULLABLE | NULL | Approval timestamp |
| rejected_by | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | User who rejected appointment |
| rejected_at | TIMESTAMP | - | NULLABLE | NULL | Rejection timestamp |
| client_notified | TINYINT(1) | - | NOT NULL | 0 | Flag if client has been notified |
| notified_at | TIMESTAMP | - | NULLABLE | NULL | Notification timestamp |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `client_id` references `clients(id)`
- FOREIGN KEY: `assigned_team_id` references `optimization_teams(id)`
- FOREIGN KEY: `recommended_team_id` references `optimization_teams(id)`
- FOREIGN KEY: `approved_by` references `users(id)`
- FOREIGN KEY: `rejected_by` references `users(id)`

**Relationships:**
- Many-to-One with `clients` (client_id → clients.id) ON DELETE CASCADE
- Many-to-One with `optimization_teams` (assigned_team_id → optimization_teams.id) ON DELETE SET NULL
- Many-to-One with `optimization_teams` (recommended_team_id → optimization_teams.id) ON DELETE SET NULL
- Many-to-One with `users` (approved_by → users.id) ON DELETE SET NULL
- Many-to-One with `users` (rejected_by → users.id) ON DELETE SET NULL

**Business Rules:**
- is_company_inquiry determines if multiple service types are available
- company_service_types is a JSON array used only when is_company_inquiry = 1
- is_sunday and is_holiday affect pricing calculations
- total_amount must equal quotation + vat_amount
- Status workflow: pending → approved/rejected → completed/cancelled
- assigned_team_id is set when admin assigns a team
- recommended_team_id comes from optimization algorithm
- Client notification tracking ensures clients are informed of status changes

---

### 2.3 quotations

**Description:** Manages quotation requests from potential clients through the website landing page. This table captures detailed information about service inquiries, property details, location, and tracks the quotation lifecycle from initial request to conversion to appointment.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique quotation identifier |
| booking_type | ENUM | - | NOT NULL | - | Type: 'personal' or 'company' |
| cleaning_services | LONGTEXT | - | NULLABLE, JSON | NULL | Array of requested cleaning services |
| date_of_service | DATE | - | NULLABLE | NULL | Preferred service date |
| duration_of_service | INT | - | NULLABLE | NULL | Expected service duration in hours |
| type_of_urgency | VARCHAR | 255 | NULLABLE | NULL | Urgency level (standard, urgent, emergency) |
| property_type | VARCHAR | 255 | NOT NULL | - | Type of property (residential, commercial, etc.) |
| floors | INT | - | NOT NULL | 1 | Number of floors |
| rooms | INT | - | NOT NULL | 1 | Number of rooms |
| people_per_room | INT | - | NULLABLE | NULL | Average people per room |
| floor_area | DECIMAL | 10,2 | NULLABLE | NULL | Total floor area |
| area_unit | VARCHAR | 255 | NULLABLE | NULL | Unit of measurement (sqm, sqft) |
| location_type | VARCHAR | 255 | NULLABLE | NULL | Type of location |
| street_address | VARCHAR | 255 | NULLABLE | NULL | Street address |
| postal_code | VARCHAR | 10 | NULLABLE | NULL | Postal code |
| city | VARCHAR | 255 | NULLABLE | NULL | City name |
| district | VARCHAR | 255 | NULLABLE | NULL | District/region |
| latitude | DECIMAL | 10,7 | NULLABLE | NULL | Geographic latitude |
| longitude | DECIMAL | 10,7 | NULLABLE | NULL | Geographic longitude |
| company_name | VARCHAR | 255 | NULLABLE | NULL | Company name (for company bookings) |
| client_name | VARCHAR | 255 | NOT NULL | - | Contact person name |
| phone_number | VARCHAR | 255 | NOT NULL | - | Contact phone number |
| email | VARCHAR | 255 | NOT NULL | - | Contact email address |
| estimated_price | DECIMAL | 10,2 | NULLABLE | NULL | System-calculated estimated price |
| vat_amount | DECIMAL | 10,2 | NULLABLE | NULL | VAT/tax amount |
| total_price | DECIMAL | 10,2 | NULLABLE | NULL | Total price including VAT |
| pricing_notes | TEXT | - | NULLABLE | NULL | Notes about pricing |
| status | ENUM | - | NOT NULL | 'pending_review' | Status: 'pending_review', 'under_review', 'quoted', 'accepted', 'rejected', 'converted' |
| reviewed_by | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | Admin who reviewed quotation |
| reviewed_at | TIMESTAMP | - | NULLABLE | NULL | Review timestamp |
| quoted_by | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | Admin who provided final quote |
| quoted_at | TIMESTAMP | - | NULLABLE | NULL | Quote provided timestamp |
| admin_notes | TEXT | - | NULLABLE | NULL | Internal admin notes |
| rejection_reason | TEXT | - | NULLABLE | NULL | Reason if quotation rejected |
| appointment_id | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | Linked appointment if converted |
| converted_by | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | Admin who converted to appointment |
| converted_at | TIMESTAMP | - | NULLABLE | NULL | Conversion timestamp |
| client_responded_at | TIMESTAMP | - | NULLABLE | NULL | Client response timestamp |
| client_message | TEXT | - | NULLABLE | NULL | Message from client |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |
| deleted_at | TIMESTAMP | - | NULLABLE | NULL | Soft delete timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `reviewed_by` references `users(id)`
- FOREIGN KEY: `quoted_by` references `users(id)`
- FOREIGN KEY: `converted_by` references `users(id)`
- FOREIGN KEY: `appointment_id` references `client_appointments(id)`

**Relationships:**
- Many-to-One with `users` (reviewed_by → users.id)
- Many-to-One with `users` (quoted_by → users.id)
- Many-to-One with `users` (converted_by → users.id)
- One-to-One with `client_appointments` (appointment_id → client_appointments.id)

**Business Rules:**
- Status workflow: pending_review → under_review → quoted → accepted/rejected → converted
- cleaning_services is a JSON array containing selected service types
- latitude/longitude enable distance-based pricing and routing
- estimated_price is auto-calculated based on property details and services
- When status = 'converted', appointment_id must be populated
- booking_type determines if company_name is required
- Soft deletes preserve quotation history

---

### 2.4 feedback

**Description:** Collects and stores detailed customer feedback and ratings for completed cleaning services. This table enables quality monitoring and performance analysis through multi-dimensional rating criteria.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique feedback identifier |
| client_id | BIGINT UNSIGNED | - | FOREIGN KEY, NOT NULL | - | Reference to clients table |
| service_type | VARCHAR | 255 | NOT NULL | - | Type of service provided |
| overall_rating | INT | - | NOT NULL | - | Overall satisfaction rating (1-5) |
| quality_rating | INT | - | NOT NULL | - | Quality of work rating (1-5) |
| cleanliness_rating | INT | - | NOT NULL | - | Cleanliness standard rating (1-5) |
| punctuality_rating | INT | - | NOT NULL | - | Punctuality rating (1-5) |
| professionalism_rating | INT | - | NOT NULL | - | Staff professionalism rating (1-5) |
| value_rating | INT | - | NOT NULL | - | Value for money rating (1-5) |
| comments | TEXT | - | NOT NULL | - | Client's written feedback |
| would_recommend | TINYINT(1) | - | NOT NULL | 0 | Would recommend service (yes/no) |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `client_id` references `clients(id)`

**Relationships:**
- Many-to-One with `clients` (client_id → clients.id) ON DELETE CASCADE

**Business Rules:**
- All rating fields use a 1-5 scale (1 = poor, 5 = excellent)
- Rating dimensions: overall, quality, cleanliness, punctuality, professionalism, value
- comments field is required to provide context for ratings
- would_recommend indicates Net Promoter Score (NPS) intent
- Feedback is typically collected after service completion
- Used for employee performance evaluation and service quality monitoring

---

### 2.5 contracted_clients

**Description:** Manages long-term contracted clients with recurring cleaning services. These are typically commercial clients with ongoing service agreements. This table stores contract details and location information for scheduled recurring tasks.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique contracted client identifier |
| user_id | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | Reference to users table |
| name | VARCHAR | 255 | NOT NULL, UNIQUE | - | Client/company name |
| email | VARCHAR | 255 | NULLABLE | NULL | Contact email address |
| phone | VARCHAR | 255 | NULLABLE | NULL | Contact phone number |
| address | VARCHAR | 255 | NULLABLE | NULL | Physical address |
| business_id | VARCHAR | 255 | NULLABLE | NULL | Business registration ID |
| contract_start | DATE | - | NULLABLE | NULL | Contract start date |
| contract_end | DATE | - | NULLABLE | NULL | Contract end date |
| latitude | DECIMAL | 10,8 | NULLABLE | NULL | Location latitude coordinate |
| longitude | DECIMAL | 11,8 | NULLABLE | NULL | Location longitude coordinate |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |
| deleted_at | TIMESTAMP | - | NULLABLE | NULL | Soft delete timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE KEY: `name`
- FOREIGN KEY: `user_id` references `users(id)`

**Relationships:**
- Many-to-One with `users` (user_id → users.id) ON DELETE CASCADE
- One-to-Many with `locations` (locations.contracted_client_id → contracted_clients.id)
- One-to-Many with `tasks` (tasks.client_id may reference this through location relationship)

**Business Rules:**
- name must be unique across all contracted clients
- user_id links to company role users in the users table
- contract_start and contract_end define the active contract period
- latitude/longitude enable route optimization for scheduled tasks
- Contracted clients have multiple locations defined in the locations table
- Soft deletes preserve contract history
- Active contracts have contract_end >= current_date

---

## 3. EMPLOYEE MANAGEMENT TABLES

### 3.1 employees

**Description:** Stores employee-specific information including skills, availability, efficiency metrics, and employment details. This table extends the users table for users with the 'employee' role and is central to the task assignment and optimization system.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique employee identifier |
| user_id | BIGINT UNSIGNED | - | FOREIGN KEY, NOT NULL | - | Reference to users table |
| skills | LONGTEXT | - | NOT NULL, JSON | - | Array of employee skills/certifications |
| is_active | TINYINT(1) | - | NOT NULL | 1 | Employee active status |
| is_day_off | TINYINT(1) | - | NOT NULL | 0 | Currently on day off flag |
| is_busy | TINYINT(1) | - | NOT NULL | 0 | Currently busy/unavailable flag |
| efficiency | DECIMAL | 3,2 | NOT NULL | 1.00 | Efficiency multiplier (0.5 to 2.0) |
| has_driving_license | TINYINT(1) | - | NOT NULL | 0 | Has valid driving license |
| years_of_experience | INT | - | NOT NULL | 0 | Years of cleaning experience |
| salary_per_hour | DECIMAL | 8,2 | NOT NULL | 13.00 | Hourly wage rate |
| months_employed | INT | - | NOT NULL | 0 | Months employed for efficiency calculation |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |
| deleted_at | TIMESTAMP | - | NULLABLE | NULL | Soft delete timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `user_id` references `users(id)`

**Relationships:**
- One-to-One with `users` (user_id → users.id) ON DELETE CASCADE
- One-to-Many with `attendances` (attendances.employee_id → employees.id)
- One-to-Many with `employee_performance` (employee_performance.employee_id → employees.id)
- One-to-Many with `day_offs` (day_offs.employee_id → employees.id)
- One-to-Many with `optimization_team_members` (optimization_team_members.employee_id → employees.id)
- One-to-Many with `performance_flags` (performance_flags.employee_id → employees.id)

**Business Rules:**
- user_id must link to a user with role = 'employee'
- skills is a JSON array containing skill codes (e.g., ["deep_cleaning", "window_cleaning"])
- efficiency ranges from 0.5 (slower) to 2.0 (faster than standard)
- efficiency is used in task duration calculations and optimization
- is_active = 0 excludes employee from task assignment
- is_day_off = 1 indicates employee is currently on leave
- is_busy = 1 indicates employee is unavailable for new tasks
- has_driving_license determines eligibility to be team driver
- months_employed used to calculate experience-based efficiency adjustments
- salary_per_hour used for cost calculations in optimization
- Soft deletes preserve employment history

---

### 3.2 attendances

**Description:** Tracks employee clock-in and clock-out records with geolocation data for verification. This table monitors employee work hours, calculates total minutes worked, and supports location-based attendance validation.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique attendance record identifier |
| employee_id | BIGINT UNSIGNED | - | FOREIGN KEY, NOT NULL | - | Reference to employees table |
| clock_in | TIMESTAMP | - | NULLABLE | NULL | Clock-in timestamp |
| clock_out | TIMESTAMP | - | NULLABLE | NULL | Clock-out timestamp |
| clock_in_latitude | DECIMAL | 10,8 | NULLABLE | NULL | Latitude at clock-in location |
| clock_in_longitude | DECIMAL | 11,8 | NULLABLE | NULL | Longitude at clock-in location |
| clock_out_latitude | DECIMAL | 10,8 | NULLABLE | NULL | Latitude at clock-out location |
| clock_out_longitude | DECIMAL | 11,8 | NULLABLE | NULL | Longitude at clock-out location |
| clock_in_distance | DECIMAL | 8,2 | NULLABLE | NULL | Distance from office at clock-in (meters) |
| clock_out_distance | DECIMAL | 8,2 | NULLABLE | NULL | Distance from office at clock-out (meters) |
| total_minutes_worked | INT | - | NULLABLE | NULL | Total minutes worked (auto-calculated) |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE KEY: `unique_employee_clock_in` (employee_id, clock_in)
- FOREIGN KEY: `employee_id` references `employees(id)`

**Relationships:**
- Many-to-One with `employees` (employee_id → employees.id) ON DELETE CASCADE

**Business Rules:**
- clock_in must be recorded before clock_out
- total_minutes_worked = (clock_out - clock_in) in minutes
- Unique constraint on (employee_id, clock_in) prevents duplicate clock-ins
- clock_in_distance and clock_out_distance validate attendance location
- Geolocation coordinates enable verification of attendance authenticity
- Distances are calculated from company office location
- Used for payroll calculation and work hour verification
- Records with only clock_in indicate employee is currently working

---

### 3.3 employee_performance

**Description:** Aggregates daily performance metrics for each employee based on completed tasks. This table tracks task completion rates and performance scores to measure employee efficiency and productivity over time.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique performance record identifier |
| employee_id | BIGINT UNSIGNED | - | FOREIGN KEY, NOT NULL | - | Reference to employees table |
| date | DATE | - | NOT NULL | - | Performance record date |
| tasks_completed | INT | - | NOT NULL | 0 | Number of tasks completed on date |
| total_performance_score | DECIMAL | 8,4 | NOT NULL | 0.0000 | Sum of all task performance scores |
| average_performance | DECIMAL | 8,4 | NOT NULL | 0.0000 | Average performance score for the day |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE KEY: `employee_performance_employee_id_date_unique` (employee_id, date)
- FOREIGN KEY: `employee_id` references `employees(id)`

**Relationships:**
- Many-to-One with `employees` (employee_id → employees.id) ON DELETE CASCADE

**Business Rules:**
- One record per employee per date (enforced by unique constraint)
- tasks_completed incremented when employee completes a task
- Performance score > 1.0 indicates faster than estimated (better performance)
- Performance score < 1.0 indicates slower than estimated (needs improvement)
- average_performance = total_performance_score / tasks_completed
- Performance scores calculated by comparing actual vs estimated task duration
- Used for employee evaluation and efficiency adjustment
- Daily aggregation enables performance trend analysis

---

### 3.4 day_offs

**Description:** Manages employee time-off requests and approved leave days. This table tracks vacation days, sick leave, personal days, and other types of absences to ensure proper scheduling and resource allocation.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique day-off record identifier |
| employee_id | BIGINT UNSIGNED | - | FOREIGN KEY, NOT NULL | - | Reference to employees table |
| date | DATE | - | NOT NULL | - | Date of the day off |
| reason | VARCHAR | 255 | NULLABLE | NULL | Reason for day off |
| type | ENUM | - | NOT NULL | 'personal' | Type: 'vacation', 'sick', 'personal', 'other' |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE KEY: `day_offs_employee_id_date_unique` (employee_id, date)
- FOREIGN KEY: `employee_id` references `employees(id)`

**Relationships:**
- Many-to-One with `employees` (employee_id → employees.id) ON DELETE CASCADE

**Business Rules:**
- One record per employee per date (enforced by unique constraint)
- Employee cannot be assigned tasks on days marked as day_off
- type categorizes the absence for HR tracking
- 'sick' type may require medical documentation
- 'vacation' type counted against annual leave quota
- Optimization system checks this table before assigning tasks
- Integration with is_day_off flag in employees table

---

## 4. TASK & SCHEDULING TABLES

### 4.1 tasks

**Description:** Central table for all cleaning tasks including one-time appointments and recurring contracted services. This table manages task lifecycle, scheduling, team assignments, and tracks task progress from creation to completion. It is the core entity for the optimization algorithm.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique task identifier |
| location_id | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | Reference to locations table |
| client_id | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | Reference to clients table |
| task_description | TEXT | - | NOT NULL | - | Detailed task description |
| rate_type | VARCHAR | 255 | NOT NULL | 'Normal' | Rate type: 'Normal', 'Sunday', 'Holiday', etc. |
| estimated_duration_minutes | INT | - | NOT NULL | - | Estimated task duration in minutes |
| actual_duration | INT | - | NULLABLE | NULL | Actual time taken in minutes (auto-calculated) |
| scheduled_date | DATE | - | NOT NULL | - | Date task is scheduled |
| scheduled_time | TIME | - | NULLABLE | NULL | Time task is scheduled to start |
| duration | INT | - | NULLABLE | NULL | Task duration in minutes |
| travel_time | INT | - | NOT NULL | 0 | Travel time to location in minutes |
| required_equipment | LONGTEXT | - | NULLABLE, JSON | NULL | Array of required equipment |
| required_skills | LONGTEXT | - | NULLABLE, JSON | NULL | Array of required skills |
| status | ENUM | - | NOT NULL | 'Pending' | Status: 'Pending', 'Scheduled', 'In Progress', 'On Hold', 'Completed', 'Cancelled' |
| on_hold_reason | VARCHAR | 255 | NULLABLE | NULL | Reason if task is on hold |
| on_hold_timestamp | TIMESTAMP | - | NULLABLE | NULL | When task was put on hold |
| arrival_status | TINYINT(1) | - | DEFAULT | 0 | Team arrival confirmation flag |
| assigned_team_id | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | Reference to optimization_teams table |
| reassigned_at | TIMESTAMP | - | NULLABLE | NULL | Timestamp of last reassignment |
| reassignment_reason | TEXT | - | NULLABLE | NULL | Reason for reassignment |
| optimization_run_id | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | Reference to optimization_runs table |
| assigned_by_generation | INT | - | NULLABLE | NULL | Generation number that assigned this task |
| started_at | TIMESTAMP | - | NULLABLE | NULL | When task was started |
| completed_at | TIMESTAMP | - | NULLABLE | NULL | When task was completed |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |
| deleted_at | TIMESTAMP | - | NULLABLE | NULL | Soft delete timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `location_id` references `locations(id)`
- FOREIGN KEY: `client_id` references `clients(id)`
- FOREIGN KEY: `assigned_team_id` references `optimization_teams(id)`
- FOREIGN KEY: `optimization_run_id` references `optimization_runs(id)`
- INDEX: `idx_tasks_scheduled_date` (scheduled_date)
- INDEX: `idx_tasks_deleted_at` (deleted_at)
- INDEX: `idx_tasks_assigned_team_id` (assigned_team_id)
- INDEX: `idx_tasks_location_id` (location_id)
- INDEX: `idx_tasks_client_id` (client_id)
- COMPOSITE INDEX: `idx_tasks_scheduled_deleted` (scheduled_date, deleted_at)

**Relationships:**
- Many-to-One with `locations` (location_id → locations.id)
- Many-to-One with `clients` (client_id → clients.id)
- Many-to-One with `optimization_teams` (assigned_team_id → optimization_teams.id)
- Many-to-One with `optimization_runs` (optimization_run_id → optimization_runs.id)
- One-to-Many with `alerts` (alerts.task_id → tasks.id)
- One-to-Many with `performance_flags` (performance_flags.task_id → tasks.id)
- One-to-Many with `invalid_tasks` (invalid_tasks.task_id → tasks.id)

**Business Rules:**
- Either location_id (for contracted clients) or client_id (for appointments) must be set
- Status workflow: Pending → Scheduled → In Progress → Completed/Cancelled
- estimated_duration_minutes calculated based on location type or appointment details
- actual_duration calculated from started_at and completed_at timestamps
- rate_type affects task priority and cost calculations
- travel_time used in route optimization for team scheduling
- required_skills must match team member skills for valid assignment
- assigned_team_id set by optimization algorithm or manual assignment
- optimization_run_id tracks which optimization run assigned the task
- Tasks can be reassigned if team is unavailable or task requirements change
- arrival_status = 1 when team confirms arrival at location
- Soft deletes allow task cancellation without data loss
- Performance calculated as: estimated_duration_minutes / actual_duration

---

### 4.2 locations

**Description:** Defines specific service locations for contracted clients. Each location represents a physical site requiring recurring cleaning services and includes location details, service rates, and duration specifications.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique location identifier |
| contracted_client_id | BIGINT UNSIGNED | - | FOREIGN KEY, NOT NULL | - | Reference to contracted_clients table |
| location_name | VARCHAR | 255 | NOT NULL | - | Name/identifier for the location |
| location_type | VARCHAR | 255 | NOT NULL | - | Type of location (office, school, etc.) |
| base_cleaning_duration_minutes | INT | - | NOT NULL | - | Standard cleaning duration in minutes |
| normal_rate_per_hour | DECIMAL | 10,2 | NULLABLE | NULL | Regular hourly rate |
| sunday_holiday_rate | DECIMAL | 10,2 | NULLABLE | NULL | Sunday/holiday hourly rate |
| deep_cleaning_rate | DECIMAL | 10,2 | NULLABLE | NULL | Deep cleaning hourly rate |
| light_deep_cleaning_rate | DECIMAL | 10,2 | NULLABLE | NULL | Light deep cleaning hourly rate |
| student_rate | DECIMAL | 10,2 | NULLABLE | NULL | Student housing hourly rate |
| student_sunday_holiday_rate | DECIMAL | 10,2 | NULLABLE | NULL | Student Sunday/holiday rate |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |
| deleted_at | TIMESTAMP | - | NULLABLE | NULL | Soft delete timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `contracted_client_id` references `contracted_clients(id)`

**Relationships:**
- Many-to-One with `contracted_clients` (contracted_client_id → contracted_clients.id) ON DELETE CASCADE
- One-to-Many with `tasks` (tasks.location_id → locations.id)

**Business Rules:**
- Each contracted client can have multiple service locations
- base_cleaning_duration_minutes used as estimate for recurring tasks
- Multiple rate types support different service levels and schedules
- sunday_holiday_rate typically higher than normal_rate_per_hour
- student_rate specific for student housing contracts
- location_type determines applicable cleaning requirements
- Soft deletes preserve historical location data
- Used by task creation system to generate recurring tasks

---

### 4.3 holidays

**Description:** Manages company-recognized holidays that affect task scheduling, employee availability, and service rates. This table enables the system to automatically adjust schedules and apply holiday pricing.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique holiday identifier |
| date | DATE | - | NOT NULL, UNIQUE | - | Holiday date |
| name | VARCHAR | 255 | NOT NULL | - | Holiday name |
| created_by | BIGINT UNSIGNED | - | FOREIGN KEY, NOT NULL | - | Admin user who created the holiday |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE KEY: `holidays_date_unique` (date)
- FOREIGN KEY: `created_by` references `users(id)`

**Relationships:**
- Many-to-One with `users` (created_by → users.id) ON DELETE CASCADE

**Business Rules:**
- Each date can only have one holiday (enforced by unique constraint)
- System checks this table when scheduling tasks
- Holiday rates automatically applied to tasks scheduled on these dates
- Employees may have restricted availability on holidays
- Integration with client_appointments (is_holiday flag)
- Integration with tasks (rate_type adjustment)
- Created by admin users for annual planning

---

## 5. OPTIMIZATION SYSTEM TABLES

### 5.1 optimization_runs

**Description:** Core table for the genetic algorithm-based optimization system. Each record represents a complete optimization run that generates optimal team assignments and schedules for a specific service date using evolutionary algorithms.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique optimization run identifier |
| service_date | DATE | - | NOT NULL | - | Date being optimized |
| triggered_by_task_id | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | Task that triggered the optimization |
| status | ENUM | - | NOT NULL | 'running' | Status: 'running', 'completed', 'failed' |
| is_saved | TINYINT(1) | - | DEFAULT | 0 | Whether results are saved/applied |
| what_if_scenario_id | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | Reference to scenario analysis if applicable |
| total_tasks | INT | - | NOT NULL | - | Number of tasks to be assigned |
| total_teams | INT | - | NOT NULL | - | Number of teams generated |
| total_employees | INT | - | NOT NULL | - | Number of available employees |
| employee_allocation_data | LONGTEXT | - | NULLABLE, JSON | NULL | Detailed employee allocation information |
| greedy_result_data | LONGTEXT | - | NULLABLE, JSON | NULL | Initial greedy algorithm results |
| final_fitness_score | DECIMAL | 8,4 | NULLABLE | NULL | Final fitness score of best solution |
| generations_run | INT | - | NOT NULL | 0 | Number of generations completed |
| error_message | TEXT | - | NULLABLE | NULL | Error details if status = 'failed' |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |
| deleted_at | TIMESTAMP | - | NULLABLE | NULL | Soft delete timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `triggered_by_task_id` references `tasks(id)`
- FOREIGN KEY: `what_if_scenario_id` references `scenario_analyses(id)`

**Relationships:**
- Many-to-One with `tasks` (triggered_by_task_id → tasks.id)
- Many-to-One with `scenario_analyses` (what_if_scenario_id → scenario_analyses.id)
- One-to-Many with `optimization_generations` (optimization_generations.optimization_run_id → optimization_runs.id)
- One-to-Many with `optimization_teams` (optimization_teams.optimization_run_id → optimization_runs.id)
- One-to-Many with `tasks` (tasks.optimization_run_id → optimization_runs.id)

**Business Rules:**
- One optimization run per service date (unless running what-if scenarios)
- Status workflow: running → completed/failed
- Genetic algorithm runs multiple generations to find optimal solution
- final_fitness_score represents overall quality (lower is better)
- Fitness considers: total distance, workload balance, skill matching, time constraints
- is_saved = 1 means teams and assignments are applied to actual schedule
- employee_allocation_data contains skill matching and availability info
- greedy_result_data stores initial solution before genetic optimization
- triggered_by_task_id tracks what event initiated the optimization
- Soft deletes allow keeping optimization history

---

### 5.2 optimization_generations

**Description:** Stores data for each generation in the genetic algorithm optimization process. This table enables tracking the evolution of solutions and analyzing how the algorithm converges to optimal schedules.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique generation identifier |
| optimization_run_id | BIGINT UNSIGNED | - | FOREIGN KEY, NOT NULL | - | Reference to optimization_runs table |
| generation_number | INT | - | NOT NULL | - | Generation sequence number (0-based) |
| best_fitness | DECIMAL | 8,4 | NOT NULL | - | Best fitness score in this generation |
| average_fitness | DECIMAL | 8,4 | NOT NULL | - | Average fitness across population |
| worst_fitness | DECIMAL | 8,4 | NOT NULL | - | Worst fitness score in this generation |
| is_improvement | TINYINT(1) | - | NOT NULL | 0 | Whether this generation improved over previous |
| best_schedule_data | LONGTEXT | - | NOT NULL, JSON | - | Complete schedule data for best solution |
| population_summary | LONGTEXT | - | NULLABLE, JSON | NULL | Summary statistics of entire population |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- COMPOSITE INDEX: `opt_gen_run_gen_idx` (optimization_run_id, generation_number)

**Relationships:**
- Many-to-One with `optimization_runs` (optimization_run_id → optimization_runs.id)

**Business Rules:**
- Each generation represents one iteration of the genetic algorithm
- generation_number starts at 0 (initial population)
- best_fitness should generally decrease (improve) over generations
- is_improvement = 1 when best_fitness < previous generation's best_fitness
- best_schedule_data contains: team assignments, task sequences, routes
- population_summary includes: diversity metrics, selection stats
- Used for algorithm tuning and performance analysis
- Enables visualization of optimization convergence

---

### 5.3 optimization_teams

**Description:** Represents teams generated by the optimization algorithm for a specific service date. Each team consists of multiple employees and an assigned vehicle, created to efficiently complete assigned tasks.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique optimization team identifier |
| optimization_run_id | BIGINT UNSIGNED | - | FOREIGN KEY, NOT NULL | - | Reference to optimization_runs table |
| team_index | INT | - | NOT NULL | - | Team number within the optimization run |
| service_date | DATE | - | NOT NULL | - | Date this team is scheduled |
| car_id | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | Assigned vehicle |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `optimization_run_id` references `optimization_runs(id)`
- FOREIGN KEY: `car_id` references `cars(id)`

**Relationships:**
- Many-to-One with `optimization_runs` (optimization_run_id → optimization_runs.id)
- Many-to-One with `cars` (car_id → cars.id)
- One-to-Many with `optimization_team_members` (optimization_team_members.optimization_team_id → optimization_teams.id)
- One-to-Many with `tasks` (tasks.assigned_team_id → optimization_teams.id)
- One-to-Many with `client_appointments` (client_appointments.assigned_team_id → optimization_teams.id)

**Business Rules:**
- Teams created by optimization algorithm based on task requirements
- team_index provides ordering within a single optimization run
- Each team assigned a car if available and team has licensed driver
- Team size typically 2-4 employees based on task requirements
- Team composition considers skill matching and workload balance
- service_date must match the optimization_run's service_date
- Teams are temporary until optimization is saved (is_saved = 1)

---

### 5.4 optimization_team_members

**Description:** Junction table linking employees to optimization teams. This table defines team composition, ensuring each team has the necessary skills and that employees are not assigned to multiple teams on the same date.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique team member assignment identifier |
| optimization_team_id | BIGINT UNSIGNED | - | FOREIGN KEY, NOT NULL | - | Reference to optimization_teams table |
| employee_id | BIGINT UNSIGNED | - | FOREIGN KEY, NOT NULL | - | Reference to employees table |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE KEY: `unique_team_member` (optimization_team_id, employee_id)
- FOREIGN KEY: `optimization_team_id` references `optimization_teams(id)`
- FOREIGN KEY: `employee_id` references `employees(id)`

**Relationships:**
- Many-to-One with `optimization_teams` (optimization_team_id → optimization_teams.id)
- Many-to-One with `employees` (employee_id → employees.id)

**Business Rules:**
- Each employee can only be on one team per optimization run (enforced by unique constraint)
- Team member selection based on: skills, efficiency, availability, driving license
- Optimization algorithm considers employee efficiency when forming teams
- At least one team member with has_driving_license = 1 if car assigned
- Team members must be active (is_active = 1) and not on day off
- Cannot assign employee to multiple teams for same service_date

---

### 5.5 scenario_analyses

**Description:** Stores what-if scenario simulations for schedule optimization. This table enables administrators to test different scenarios (e.g., employee absences, additional tasks) without affecting actual schedules.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique scenario analysis identifier |
| service_date | DATE | - | NOT NULL | - | Date being analyzed |
| scenario_type | VARCHAR | 255 | NOT NULL | - | Type of scenario (e.g., 'employee_absence', 'task_addition') |
| parameters | LONGTEXT | - | NOT NULL, JSON | - | Scenario parameters and assumptions |
| modified_schedule | LONGTEXT | - | NOT NULL, JSON | - | Resulting optimized schedule |
| impact_analysis | LONGTEXT | - | NOT NULL, JSON | - | Analysis of scenario impact |
| recommendations | LONGTEXT | - | NULLABLE, JSON | NULL | System-generated recommendations |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- COMPOSITE INDEX: `scenario_analyses_service_date_scenario_type_index` (service_date, scenario_type)

**Relationships:**
- One-to-Many with `optimization_runs` (optimization_runs.what_if_scenario_id → scenario_analyses.id)

**Business Rules:**
- Enables proactive planning and contingency testing
- scenario_type examples: 'employee_absence', 'task_addition', 'car_unavailable'
- parameters contains: removed employees, added tasks, constraints
- modified_schedule shows how system adapts to scenario
- impact_analysis includes: workload changes, cost differences, completion time
- recommendations suggest actions to mitigate negative impacts
- Does not affect actual schedules unless manually applied
- Helps managers make informed decisions

---

### 5.6 invalid_tasks

**Description:** Tracks tasks that could not be assigned during optimization due to constraint violations or resource unavailability. This table helps identify scheduling problems and resource gaps.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique invalid task record identifier |
| optimization_result_id | BIGINT UNSIGNED | - | NULLABLE, INDEX | NULL | Reference to optimization_runs (deprecated column) |
| task_id | BIGINT UNSIGNED | - | NOT NULL, INDEX | - | Reference to tasks table |
| rejection_reason | VARCHAR | 255 | NOT NULL | - | Reason task could not be assigned |
| task_details | LONGTEXT | - | NULLABLE, JSON | NULL | Task information snapshot |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `invalid_tasks_task_id_index` (task_id)
- INDEX: `invalid_tasks_optimization_result_id_index` (optimization_result_id)

**Relationships:**
- Many-to-One with `tasks` (task_id → tasks.id)

**Business Rules:**
- Created when optimization cannot assign a task to any team
- Common rejection_reasons:
  - 'no_employees_with_required_skills'
  - 'all_employees_busy'
  - 'insufficient_time_in_work_day'
  - 'location_unreachable'
- task_details preserves task state at time of rejection
- Admin must resolve issue and re-run optimization
- Used for capacity planning and resource allocation analysis

---

## 6. MONITORING & ALERTS TABLES

### 6.1 alerts

**Description:** Manages system-generated alerts for task delays, issues, and important events requiring administrator attention. This table supports proactive monitoring and quick response to operational problems.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique alert identifier |
| task_id | BIGINT UNSIGNED | - | FOREIGN KEY, NOT NULL | - | Reference to tasks table |
| alert_type | VARCHAR | 255 | NOT NULL | - | Type of alert (delay, issue, etc.) |
| delay_minutes | INT | - | NULLABLE | NULL | Minutes of delay if applicable |
| reason | TEXT | - | NULLABLE | NULL | Detailed reason for alert |
| triggered_at | TIMESTAMP | - | NOT NULL | current_timestamp() | When alert was triggered |
| acknowledged_at | TIMESTAMP | - | NULLABLE | NULL | When alert was acknowledged |
| acknowledged_by | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | User who acknowledged alert |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `task_id` references `tasks(id)` ON DELETE CASCADE
- FOREIGN KEY: `acknowledged_by` references `users(id)`

**Relationships:**
- Many-to-One with `tasks` (task_id → tasks.id) ON DELETE CASCADE
- Many-to-One with `users` (acknowledged_by → users.id)

**Business Rules:**
- Automatically generated when tasks are delayed or issues detected
- alert_type examples: 'task_delay', 'team_not_arrived', 'task_on_hold'
- delay_minutes calculated from scheduled vs actual time
- triggered_at set to current timestamp on creation
- Unacknowledged alerts (acknowledged_at = NULL) shown in dashboard
- Admin must acknowledge alert after reviewing/resolving
- Integration with notification system for real-time alerts

---

### 6.2 performance_flags

**Description:** Identifies tasks with significant performance variances from estimates. This table supports quality control by flagging tasks that took much longer or shorter than expected, requiring administrative review.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique performance flag identifier |
| task_id | BIGINT UNSIGNED | - | FOREIGN KEY, NOT NULL | - | Reference to tasks table |
| employee_id | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | Employee flagged for performance |
| team_id | BIGINT UNSIGNED | - | NULLABLE | NULL | Team ID from optimization_teams |
| flag_type | VARCHAR | 255 | NOT NULL | - | Type of flag (over_time, under_time, etc.) |
| estimated_minutes | INT | - | NULLABLE | NULL | Estimated task duration |
| actual_minutes | INT | - | NULLABLE | NULL | Actual task duration |
| variance_minutes | INT | - | NULLABLE | NULL | Difference between actual and estimated |
| flagged_at | TIMESTAMP | - | NOT NULL | current_timestamp() | When flag was created |
| reviewed | TINYINT(1) | - | NOT NULL | 0 | Whether flag has been reviewed |
| reviewed_by | BIGINT UNSIGNED | - | FOREIGN KEY, NULLABLE | NULL | User who reviewed flag |
| reviewed_at | TIMESTAMP | - | NULLABLE | NULL | Review timestamp |
| review_notes | TEXT | - | NULLABLE | NULL | Notes from review |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `task_id` references `tasks(id)`
- FOREIGN KEY: `employee_id` references `employees(id)`
- FOREIGN KEY: `reviewed_by` references `users(id)`

**Relationships:**
- Many-to-One with `tasks` (task_id → tasks.id)
- Many-to-One with `employees` (employee_id → employees.id)
- Many-to-One with `users` (reviewed_by → users.id)

**Business Rules:**
- Created when variance_minutes exceeds threshold (e.g., ±30% of estimate)
- flag_type examples: 'significantly_over_time', 'significantly_under_time'
- variance_minutes = actual_minutes - estimated_minutes
- Positive variance = task took longer than expected
- Negative variance = task completed faster than expected
- Requires admin review to determine if legitimate or issue
- review_notes capture explanation (e.g., "unexpected damage", "efficient team")
- Used to adjust employee efficiency scores and future estimates

---

## 7. ADMINISTRATIVE TABLES

### 7.1 notifications

**Description:** Stores in-app notifications for users across all roles. This table supports the notification system that keeps users informed about appointments, schedule changes, system alerts, and other important events.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique notification identifier |
| user_id | BIGINT UNSIGNED | - | FOREIGN KEY, NOT NULL | - | Reference to users table |
| type | VARCHAR | 255 | NOT NULL | - | Notification type/category |
| title | VARCHAR | 255 | NOT NULL | - | Notification title |
| message | TEXT | - | NOT NULL | - | Notification message content |
| data | LONGTEXT | - | NULLABLE, JSON | NULL | Additional structured data |
| read_at | TIMESTAMP | - | NULLABLE | NULL | When notification was read |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- COMPOSITE INDEX: `notifications_user_id_read_at_index` (user_id, read_at)

**Relationships:**
- Many-to-One with `users` (user_id → users.id)

**Business Rules:**
- type examples: 'appointment_approved', 'schedule_updated', 'task_assigned'
- data contains contextual information (task_id, appointment_id, etc.)
- read_at = NULL for unread notifications
- Composite index optimizes fetching unread notifications per user
- Notifications can be marked as read individually or in bulk
- Different notification types for different user roles
- Integration with real-time notification system

---

### 7.2 company_settings

**Description:** Stores configurable system-wide settings and parameters for the OptiCrew application. This table enables administrators to modify system behavior without code changes.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique setting identifier |
| key | VARCHAR | 255 | NOT NULL, UNIQUE | - | Setting key/name |
| value | TEXT | - | NULLABLE | NULL | Setting value |
| type | VARCHAR | 255 | NOT NULL | 'string' | Data type of value |
| description | TEXT | - | NULLABLE | NULL | Description of setting purpose |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE KEY: `company_settings_key_unique` (key)

**Business Rules:**
- key must be unique across all settings
- type indicates how to parse value: 'string', 'integer', 'decimal', 'boolean', 'json'
- Common settings:
  - 'company_name', 'company_address', 'company_phone'
  - 'office_latitude', 'office_longitude' (for attendance verification)
  - 'work_start_time', 'work_end_time'
  - 'max_attendance_distance_meters'
  - 'vat_rate'
  - 'optimization_max_generations'
  - 'optimization_population_size'
- Settings cached in application for performance
- Admin interface for easy modification

---

### 7.3 cars

**Description:** Manages company vehicles available for team assignments. This table tracks vehicle availability for route planning and team transportation.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique car identifier |
| car_name | VARCHAR | 255 | NOT NULL | - | Car name/identifier |
| is_available | TINYINT(1) | - | NOT NULL | 1 | Car availability status |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |

**Indexes:**
- PRIMARY KEY: `id`

**Relationships:**
- One-to-Many with `optimization_teams` (optimization_teams.car_id → cars.id)

**Business Rules:**
- is_available = 0 when car is under maintenance or unavailable
- Only available cars assigned to teams during optimization
- Team must have at least one member with has_driving_license = 1 to be assigned a car
- Car assignment enables teams to cover larger service areas
- Used in route optimization calculations

---

## 8. SYSTEM TABLES

### 8.1 jobs

**Description:** Laravel queue jobs table for background processing. This table manages queued jobs for asynchronous task execution such as sending emails, processing optimization algorithms, and generating reports.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique job identifier |
| queue | VARCHAR | 255 | NOT NULL | - | Queue name |
| payload | LONGTEXT | - | NOT NULL | - | Serialized job data |
| attempts | TINYINT UNSIGNED | - | NOT NULL | - | Number of execution attempts |
| reserved_at | INT UNSIGNED | - | NULLABLE | NULL | Unix timestamp when job reserved |
| available_at | INT UNSIGNED | - | NOT NULL | - | Unix timestamp when job available |
| created_at | INT UNSIGNED | - | NOT NULL | - | Unix timestamp of creation |

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `jobs_queue_index` (queue)

**Business Rules:**
- Laravel framework manages this table automatically
- Jobs processed by queue workers
- attempts tracks retry count for failed jobs
- reserved_at prevents duplicate processing
- Common queued jobs:
  - Optimization algorithm execution
  - Email notifications
  - Report generation
  - Data exports

---

### 8.2 migrations

**Description:** Laravel migrations tracking table. This system table records which database migrations have been executed to manage database schema versioning.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | INT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique migration identifier |
| migration | VARCHAR | 255 | NOT NULL | - | Migration file name |
| batch | INT | - | NOT NULL | - | Migration batch number |

**Indexes:**
- PRIMARY KEY: `id`

**Business Rules:**
- Laravel framework manages this table automatically
- Each migration file recorded after successful execution
- batch number groups migrations run together
- Used for database version control
- Enables rollback functionality

---

### 8.3 personal_access_tokens

**Description:** Laravel Sanctum personal access tokens for API authentication. This table manages API tokens for mobile app access and external integrations.

**Columns:**

| Column Name | Data Type | Length | Constraints | Default | Description |
|-------------|-----------|--------|-------------|---------|-------------|
| id | BIGINT UNSIGNED | - | PRIMARY KEY, AUTO_INCREMENT | - | Unique token identifier |
| tokenable_type | VARCHAR | 255 | NOT NULL | - | Polymorphic type (User class) |
| tokenable_id | BIGINT UNSIGNED | - | NOT NULL | - | User ID |
| name | VARCHAR | 255 | NOT NULL | - | Token name/description |
| token | VARCHAR | 64 | NOT NULL, UNIQUE | - | Hashed token value |
| abilities | TEXT | - | NULLABLE | NULL | Token permissions (JSON array) |
| last_used_at | TIMESTAMP | - | NULLABLE | NULL | Last usage timestamp |
| expires_at | TIMESTAMP | - | NULLABLE | NULL | Token expiration timestamp |
| created_at | TIMESTAMP | - | NULLABLE | NULL | Record creation timestamp |
| updated_at | TIMESTAMP | - | NULLABLE | NULL | Record last update timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE KEY: `personal_access_tokens_token_unique` (token)
- INDEX: (tokenable_type, tokenable_id)

**Business Rules:**
- Laravel Sanctum manages this table automatically
- tokens used for API authentication (mobile app, external services)
- token value is hashed for security
- abilities define token-specific permissions
- expires_at enables time-limited tokens
- last_used_at tracks token activity

---

## APPENDIX A: DATA TYPE REFERENCE

### MySQL/MariaDB Data Types Used

#### Numeric Types

| Data Type | Description | Usage in OptiCrew |
|-----------|-------------|-------------------|
| TINYINT(1) | Boolean (0 or 1) | Flags like is_active, is_available, is_company_inquiry |
| INT | 32-bit integer | Counts like tasks_completed, duration_minutes, attempts |
| BIGINT UNSIGNED | 64-bit unsigned integer | Primary keys and foreign keys (auto-increment IDs) |
| DECIMAL(m,n) | Fixed-point decimal | Monetary values (rates, prices), coordinates, scores |

**Decimal Precision Patterns:**
- DECIMAL(3,2): Values 0.00 to 9.99 (efficiency: 0.50 to 2.00)
- DECIMAL(8,2): Monetary values up to 999,999.99 (salaries, distances)
- DECIMAL(8,4): Performance scores with high precision
- DECIMAL(10,2): Larger monetary values (quotations, rates)
- DECIMAL(10,7): Latitude coordinates (-90.0000000 to 90.0000000)
- DECIMAL(10,8): High-precision latitude (attendances)
- DECIMAL(11,8): Longitude coordinates (-180.00000000 to 180.00000000)

#### String Types

| Data Type | Description | Usage in OptiCrew |
|-----------|-------------|-------------------|
| VARCHAR(n) | Variable-length string | Names, emails, short descriptions (n = 5 to 255) |
| TEXT | Long text up to 64KB | Descriptions, reasons, comments, notes |
| LONGTEXT | Very long text up to 4GB | JSON data, serialized objects, large content |

#### Date & Time Types

| Data Type | Description | Usage in OptiCrew |
|-----------|-------------|-------------------|
| DATE | Date only (YYYY-MM-DD) | scheduled_date, birthdate, contract_start, service_date |
| TIME | Time only (HH:MM:SS) | scheduled_time, service_time |
| TIMESTAMP | Date and time with timezone | created_at, updated_at, deleted_at, event timestamps |

#### Special Types

| Data Type | Description | Usage in OptiCrew |
|-----------|-------------|-------------------|
| ENUM | Enumerated list | role, status, type, booking_type (predefined options) |
| JSON (LONGTEXT with CHECK) | JSON data with validation | Arrays like skills, required_skills, cleaning_services, parameters |

### JSON Field Patterns

JSON fields in OptiCrew use MariaDB's JSON validation:
```sql
LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
CHECK (json_valid(`field_name`))
```

**Common JSON Structures:**

1. **skills** (employees table):
   ```json
   ["deep_cleaning", "window_cleaning", "carpet_cleaning"]
   ```

2. **required_skills** (tasks table):
   ```json
   ["deep_cleaning"]
   ```

3. **cleaning_services** (quotations table):
   ```json
   ["Regular Cleaning", "Deep Cleaning", "Window Cleaning"]
   ```

4. **unit_details** (client_appointments table):
   ```json
   [
     {"unit_number": "A1", "size": "large", "special_notes": "..."},
     {"unit_number": "A2", "size": "medium", "special_notes": "..."}
   ]
   ```

5. **employee_allocation_data** (optimization_runs table):
   ```json
   {
     "available_employees": [1, 2, 3, 4],
     "unavailable_employees": [5],
     "skill_matrix": {...}
   }
   ```

---

## APPENDIX B: RELATIONSHIP DIAGRAM

### Entity Relationship Overview

#### Core User Relationships
```
users (1) ──→ (1) employees
users (1) ──→ (1) clients
users (1) ──→ (1) contracted_clients
users (1) ──→ (0..n) notifications
users (1) ──→ (0..n) holidays [created_by]
users (1) ──→ (0..n) alerts [acknowledged_by]
```

#### Client Service Flow
```
clients (1) ──→ (0..n) client_appointments
clients (1) ──→ (0..n) feedback
clients (1) ──→ (0..n) tasks

quotations (1) ──→ (0..1) client_appointments [converted]

client_appointments (n) ──→ (0..1) optimization_teams [assigned_team]
client_appointments (n) ──→ (0..1) optimization_teams [recommended_team]
```

#### Employee Tracking
```
employees (1) ──→ (0..n) attendances
employees (1) ──→ (0..n) employee_performance
employees (1) ──→ (0..n) day_offs
employees (1) ──→ (0..n) optimization_team_members
employees (1) ──→ (0..n) performance_flags
```

#### Task Scheduling
```
contracted_clients (1) ──→ (0..n) locations
locations (1) ──→ (0..n) tasks

tasks (n) ──→ (0..1) optimization_teams [assigned_team]
tasks (n) ──→ (0..1) optimization_runs
tasks (1) ──→ (0..n) alerts
tasks (1) ──→ (0..n) performance_flags
tasks (1) ──→ (0..n) invalid_tasks
```

#### Optimization System
```
optimization_runs (1) ──→ (0..n) optimization_generations
optimization_runs (1) ──→ (0..n) optimization_teams
optimization_runs (1) ──→ (0..n) tasks [assigned tasks]
optimization_runs (n) ──→ (0..1) scenario_analyses

optimization_teams (1) ──→ (0..n) optimization_team_members
optimization_teams (n) ──→ (0..1) cars
optimization_teams (1) ──→ (0..n) tasks [assigned tasks]
optimization_teams (1) ──→ (0..n) client_appointments [assigned appointments]

optimization_team_members (n) ──→ (1) employees
```

### Key Cascade Behaviors

**ON DELETE CASCADE** (child deleted when parent deleted):
- users → employees, clients, contracted_clients
- employees → attendances, employee_performance, day_offs
- clients → client_appointments, feedback
- contracted_clients → locations
- locations → tasks
- tasks → alerts

**ON DELETE SET NULL** (reference cleared when parent deleted):
- optimization_teams → client_appointments (assigned_team_id, recommended_team_id)

---

## APPENDIX C: NAMING CONVENTIONS

### Table Naming Standards

1. **Plural nouns in snake_case**: `users`, `employees`, `client_appointments`
2. **Junction tables**: `optimization_team_members`, `personal_access_tokens`
3. **No prefixes**: Tables not prefixed with project name (Laravel convention)

### Column Naming Standards

1. **Primary keys**: Always named `id` (BIGINT UNSIGNED AUTO_INCREMENT)
2. **Foreign keys**: Format `{table_singular}_id` (e.g., `employee_id`, `client_id`)
3. **Timestamps**:
   - `created_at`: Record creation timestamp
   - `updated_at`: Last modification timestamp
   - `deleted_at`: Soft delete timestamp
   - Event-specific: `{event}_at` (e.g., `approved_at`, `triggered_at`)
4. **Boolean flags**: Prefix with `is_` or `has_` (e.g., `is_active`, `has_driving_license`)
5. **Status fields**: Named `status` with ENUM type
6. **JSON fields**: Descriptive names ending in `_data` or plural (e.g., `skills`, `employee_allocation_data`)

### Index Naming Standards

1. **Primary key**: Implicit naming (id)
2. **Foreign keys**: `{table}_{column}_foreign` (e.g., `employees_user_id_foreign`)
3. **Unique keys**: `{table}_{column}_unique` (e.g., `company_settings_key_unique`)
4. **Regular indexes**: `idx_{table}_{column}` (e.g., `idx_tasks_scheduled_date`)
5. **Composite indexes**: `{table}_{col1}_{col2}_index` or custom descriptive name

### Enum Value Standards

1. **Lowercase with underscores**: `external_client`, `deep_cleaning`, `pending_review`
2. **Status values**: Progressive workflow (e.g., `pending` → `scheduled` → `completed`)
3. **Role values**: `admin`, `employee`, `external_client`, `company`

### Constraint Naming Standards

1. **Foreign key constraints**: `{table}_{column}_foreign`
2. **Unique constraints**: `{table}_{column}_unique` or `unique_{descriptor}`
3. **Check constraints**: Implicit for JSON validation

### General Conventions

1. **Character set**: utf8mb4 (full Unicode support including emojis)
2. **Collation**: utf8mb4_unicode_ci (case-insensitive Unicode)
3. **Engine**: InnoDB (supports foreign keys and transactions)
4. **Soft deletes**: Implemented via `deleted_at` timestamp column
5. **Laravel timestamps**: Most tables include `created_at` and `updated_at`

---

**Document End**

*This data dictionary provides comprehensive documentation of the OptiCrew database schema for thesis Chapter 3. All information is derived from the actual database structure (opticrew.sql) as of November 3, 2025.*
