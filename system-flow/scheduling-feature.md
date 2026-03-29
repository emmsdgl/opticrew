# Scheduling Feature

This document describes how the scheduling and task management system works in the Castcrew (OptiCrew) platform, covering the end-to-end flow from appointment creation to task completion.

---

## 1. Overview

The scheduling system manages the full lifecycle of cleaning service appointments:

```
Client Request → Admin Approval → Task Creation → Optimization → Team Assignment → Employee Execution → Completion
```

The system handles two types of clients:
- **Contracted Clients**: Companies with ongoing service agreements (e.g., Kakslauttanen, Aikamatkat)
- **External Clients**: One-time personal or company bookings via the quotation system

---

## 2. Appointment Management

### 2.1 Client Appointments

The `ClientAppointment` model represents a service booking with the following lifecycle:

```
Pending → Approved → Confirmed → Completed
                  ↘ Rejected
         ↘ Cancelled
```

**Key Fields**:
| Field | Description |
|-------|-------------|
| `service_date` / `service_time` | Scheduled date and time |
| `service_type` | Type of cleaning service |
| `booking_type` | Personal or company booking |
| `status` | pending, approved, confirmed, rejected, cancelled |
| `assigned_team_id` | The team assigned after optimization |
| `recommended_team_id` | AI-recommended team (may differ from assigned) |
| `cancellation_type` / `cancellation_fee` | Cancellation tracking |

### 2.2 Booking Rules

The system enforces configurable booking constraints via `CompanySettingService`:

| Setting | Default | Description |
|---------|---------|-------------|
| `minimum_booking_notice_days` | 3 days | How far in advance a booking must be made |
| `minimum_leave_notice_days` | 4 days | Advance notice for employee leave requests |
| `task_approval_grace_period_minutes` | 30 min | Time for employees to approve assigned tasks |
| `reassignment_grace_period_minutes` | 30 min | Time window for task reassignment after leave |
| `unstaffed_escalation_timeout_minutes` | 60 min | Time before unaccepted tasks trigger escalation |

### 2.3 Service Types

The platform supports multiple cleaning service categories:
- Deep Cleaning
- Final Cleaning
- Daily Cleaning
- Snowout Cleaning
- General Cleaning
- Hotel Cleaning

Each service type has configurable quotation PDF templates managed in admin settings.

---

## 3. Task Management

### 3.1 Task Model

Tasks are the operational units generated from appointments. Each task represents a specific cleaning job assigned to a team.

**Key Fields**:
| Field | Description |
|-------|-------------|
| `scheduled_date` / `scheduled_time` | When the task is scheduled |
| `task_description` | Service type description |
| `estimated_duration_minutes` | Expected duration |
| `status` | Pending, In Progress, Completed, On Hold, Reassigned |
| `arrival_status` | Boolean flag for urgent/guest-arrival tasks |
| `assigned_team_id` | FK to OptimizationTeam |
| `optimization_run_id` | FK to the optimization run that created the assignment |
| `employee_approved` | Whether the assigned employee accepted the task |
| `started_by` / `started_at` | Who started and when |
| `completed_by` / `completed_at` | Who completed and when |

### 3.2 Task Lifecycle

```
Task Created → Pending → Employee Approved → In Progress → Completed
                      ↘ Not Approved (within grace period) → Reassigned
                      ↘ Grace Period Expired → Unstaffed Escalation
```

**Employee Approval Workflow**:
1. Task is assigned to a team via optimization
2. Team members receive the assignment notification
3. Employees have a configurable grace period to approve/start the task
4. If unapproved within the grace period, the task enters escalation

### 3.3 Task Priority System

Tasks are prioritized using a two-tier system:
1. **Primary**: `arrival_status` (guest-arrival tasks get highest priority)
2. **Secondary**: `scheduled_time` (earlier tasks come first)

This ensures urgent tasks (e.g., guest arrivals at hotels) are always scheduled before routine cleaning.

---

## 4. Calendar & Timeline Views

### 4.1 Admin Task Calendar

The admin dashboard provides a calendar view with a 3-month window:
- **Past month**: Review completed tasks
- **Current month**: Monitor active and pending tasks
- **Next month**: Preview upcoming schedules

The calendar eager-loads relationships (`location.contractedClient`, `client`, `optimizationTeam.members`) for efficient rendering.

### 4.2 Holiday Management

The `Holiday` model tracks public holidays and company-specific non-working days:
- Holidays are excluded from scheduling
- The calendar UI marks holiday dates visually
- Interview scheduling in recruitment also respects holidays

### 4.3 Employee View

Employees see their assigned tasks in a simplified calendar/list view:
- Today's tasks with status indicators
- Upcoming tasks for the week
- Clock-in/out interface with geofence validation

---

## 5. Team Assignment

### 5.1 Team Composition Rules

Teams are formed according to strict rules:

| Rule | Constraint |
|------|-----------|
| Minimum team size | 2 members |
| Maximum team size | 3 members |
| Driver requirement | At least 1 member must have a valid driving license |
| Composition | 1 driver + 1-2 non-drivers |
| Client exclusivity | Each employee is assigned to ONE client per day |

### 5.2 Team Formation Process

1. **Driver Prioritization**: Employees with driving licenses are allocated first
2. **Pair Formation**: System prefers pairs (2 members) over trios for efficiency
3. **Odd Count Handling**: If employee count is odd, exactly one trio is formed
4. **Client Assignment**: Each formed team is assigned to a single contracted client

### 5.3 Team Efficiency Scoring

Each team receives an efficiency score calculated by:
- **Base**: Average of individual employee efficiencies
- **Skill Diversity Bonus**: Up to +15% for diverse skill sets
- **Experience Synergy**: +7.5% for junior-senior pairs, +4.5% for experienced teams
- **Size Penalty**: -5% per member over optimal size (2)
- **Range**: Capped between 0.5x and 2.0x

---

## 6. Optimization Integration

### 6.1 Triggering Optimization

The optimization can be triggered in two ways:
1. **Manual**: Admin clicks "Optimize Schedule" for a specific date
2. **Real-Time Addition**: When new tasks are added to an already-saved schedule

### 6.2 Saved Schedule Detection (Rules 4 & 8)

Before running a full optimization, the system checks:
1. Does a saved schedule (`is_saved = true`) exist for the target date?
2. If yes, attempt to assign new tasks to existing teams without re-optimizing
3. If unassigned tasks remain after real-time addition, fall through to full optimization

### 6.3 Optimization Output

The optimization produces:
- **OptimizationRun**: Master record with fitness score, generation count, and status
- **OptimizationTeam**: Team compositions with member assignments
- **Task Assignments**: Each task linked to its assigned team
- **Generation History**: Per-generation fitness metrics for analysis

---

## 7. Attendance & Geofencing

### 7.1 Clock-In/Out

Employees clock in and out of tasks using the mobile or web interface:
- Location is captured at clock-in/out time
- The system validates the employee's position against the assigned task's location
- **Geofence validation**: Employee must be within the configured radius (default: 100m) of the client site

### 7.2 Overtime Tracking

- **Overtime Threshold**: Configurable (default: 8 hours per day)
- Hours worked beyond the threshold trigger overtime pay calculation
- The 12-hour daily maximum is enforced by the scheduling algorithm

---

## 8. Disruption Handling

### 8.1 Scenario Management

The system handles real-time disruptions through the `ScenarioManager`:

| Scenario | Response |
|----------|----------|
| Emergency Task | Priority insertion into existing schedule |
| Employee Absence | Task reassignment to available team members |
| Vehicle Breakdown | Rerouting teams without affected vehicle |
| Time Constraint | Re-scheduling tasks that exceed deadlines |

### 8.2 Impact Analysis

The `ImpactAnalyzer` evaluates the effect of disruptions on the schedule:
- Recalculates fitness scores after changes
- Identifies cascade effects (e.g., delayed tasks affecting subsequent assignments)
- Recommends optimal recovery strategy

---

## 9. Quotation System

### 9.1 Automated Quotations

The quotation system integrates with scheduling:
1. Client submits a service request with property details
2. System generates a quotation based on service type, area, and complexity
3. Admin can approve/adjust the quotation
4. Upon confirmation, a task is created in the scheduling system

### 9.2 Quotation Templates

Each service type has a configurable PDF quotation template:
- Templates are uploaded via admin settings
- When `auto_send_enabled` is on, quotations are automatically emailed to clients upon submission
- VAT calculation is included in the quotation

---

## 10. Data Flow Summary

```
┌─────────────┐     ┌──────────────┐     ┌────────────────┐
│   Client     │────▶│  Appointment │────▶│     Task       │
│   Request    │     │  (Approved)  │     │   (Created)    │
└─────────────┘     └──────────────┘     └───────┬────────┘
                                                  │
                                        ┌─────────▼─────────┐
                                        │   Optimization     │
                                        │   (GA + Rules)     │
                                        └─────────┬─────────┘
                                                  │
                                    ┌─────────────▼──────────────┐
                                    │     Team Assignment         │
                                    │  (Teams ← Employees)       │
                                    └─────────────┬──────────────┘
                                                  │
                                    ┌─────────────▼──────────────┐
                                    │   Employee Execution        │
                                    │  (Clock-in → Work → Out)   │
                                    └─────────────┬──────────────┘
                                                  │
                                        ┌─────────▼─────────┐
                                        │    Completion      │
                                        │  (Status Update)   │
                                        └───────────────────┘
```
