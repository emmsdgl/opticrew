# Task Rejection & Reassignment Policy

Reference document for OptiCrew's task-rejection handling. Captures the full design after iterative refinement.

> **Status:** Designed, **not yet implemented.** This document is the source of truth for the policy. The existing `rejectTask` endpoint and `TaskApprovalObserver` cover only a thin slice of what's described here.

---

## 1. Overview

Employees may, within a defined window, reject an assigned task as a **preference signal** ("I'd prefer a different task"). The system responds with a **layered cascade** that tries successively-larger schedule disruptions only when smaller ones can't accommodate the rejection. A **mass-rejection threshold** above the per-rejection cascade catches operationally abnormal events and routes them to a global re-optimization with admin approval.

The design preserves three properties:

- **Near-optimality** of the schedule, both per-step and globally (degrades gracefully, self-corrects on the next full GA run).
- **Minimal disruption** to non-rejecting employees (at most one accepting employee touched per single rejection).
- **Fairness** between employees (per-employee rejection budget, per-task rejection ceiling, transparent reasons).

---

## 2. Design table

| Component | Decision |
|---|---|
| **Rejection model** | Preference-based — "I'd prefer a different task" |
| **Anti-gaming** | Rejection budget (3/month) + required reason from a fixed list |
| **Window** | Assignment to 24h before start |
| **Past 24h** | Emergency leave only (existing flow) |
| **Try 1 — Bilateral swap** | Auto. Workload preserved both sides. Makespan preserved. No compensation. |
| **Try 2a — Free-slot replacement** | Auto. Receiver fills a *mid-day gap* with Task A (their day-end doesn't move). No compensation. If only end-of-day gaps exist, falls through to Try 2b. |
| **Try 2b — Stretch assignment** | Admin offers Task A to a candidate with computed end-of-day capacity. Candidate opts in. Compensation applies. Ranking by earliest computed completion minimizes the makespan extension. |
| **Try 3 — Manual** | Admin handles directly (override-assign, reschedule, cancel). |
| **Per-task ceiling** | After 3 rejections of the same task, stop the cascade and escalate to admin. |
| **Swap partner's rights** | Can reject the swapped-in task under the same rules (consumes their budget). Cascading rejections capped by per-task ceiling. |
| **Mass-rejection trigger** | 3 rejections OR ≥25% of currently-assigned employees in a 4h window, whichever is higher. Pauses per-rejection cascade. |
| **Mass-rejection response** | Re-run GA over the affected time window with rejections as constraints. In-progress tasks locked. Surface the proposed new schedule to admin for approval before commit. Unfulfillable appointments are flagged to admin for reschedule/cancel decision. |

---

## 3. Key design choices and rationale

### Preference-based, not impediment-based
Rejection in this system means *"I'd prefer a different task,"* not *"I literally can't do this task."* Genuine impediments (illness, emergency) continue to use the existing emergency-leave flow.

### Budget + required reason for anti-gaming
Without limits, preference-based rejection systematically rewards aggressive rejecters and burdens conscientious ones. A monthly budget caps total rejections per employee; a required reason creates accountability and produces data for future preference modeling.

### 24h window
Decisions arrive early enough for the system to find and deliver replacements. Past 24h, only emergency leave applies — too late for preference reshuffling.

### Cascade ordered by smallest deviation first
Try 1 → 2a → 2b → 3 is intentionally ordered by increasing deviation from the GA's original schedule:

- Try 1 (swap): workload-neutral, makespan-neutral, two schedules change
- Try 2a (free-slot mid-day gap): workload shifts modestly, makespan-neutral, one schedule changes
- Try 2b (stretch): workload imbalanced (with consent + compensation), makespan extends, one schedule changes
- Try 3 (manual): admin's call

The system always picks the smallest deviation it can get away with.

### Per-task ceiling = 3
Bounds cascading rejections. If a task is undesirable to the workforce as a whole, it stops bouncing through the cascade after 3 hops and is surfaced for admin attention. Prevents a single task from exhausting the entire workforce's rejection budget.

### Mass-rejection threshold
60% rejection in a window isn't a scheduling problem; it's an operational signal (weather, dispute, payroll issue, GA misconfiguration). Silently absorbing it via repeated stretch assignments produces high cost and admin churn for no good reason. The threshold detects the pattern and routes to a single global re-optimization that admin reviews holistically.

### Mass-rejection re-run is admin-approved, not auto-committed
The GA can solve assignment but can't make compensation decisions or choose which client appointments to reschedule. The admin sees the proposed schedule and any unfulfillable gaps before commit.

### In-progress task locking
Re-runs use rolling-horizon optimization: in-progress and completed tasks are immutable inputs to the new schedule; only future-window tasks (typically next 24h) are re-optimizable. Prevents the algorithm from telling someone mid-task that they've been reassigned.

---

## 4. Scenario walkthroughs

### 4.1 Single rejection — resolved at Try 1

- **9 AM Monday.** Emp1 has Task A (Friday 10 AM, 2h, deep cleaning).
- Emp1 presses Reject. Reason: *"location too far."* Budget consumed: 1/3 this month.
- System looks for swap candidates (Try 1).
- Finds Emp4 currently holding Task X (Friday 1 PM, 2h, daily cleaning, closer location).
  - Both feasibilities pass: Emp1 can do Task X, Emp4 can do Task A.
  - Skill match acceptable, fairness OK.
- Swap executed: Emp1 → Task X, Emp4 → Task A.
- **Two schedules changed. Other 8 employees untouched.** Workload and makespan preserved both sides.

### 4.2 Single rejection — falls through to Try 2b

- Same starting point, but no valid bilateral swap exists (everyone with a swappable task has time conflicts or skill mismatch).
- Try 2a: search for an employee with a mid-day gap at Friday 10 AM — none found (everyone is densely scheduled).
- Try 2b: rank candidates by computed estimated completion time. Top candidate is Emp7 (estimated finish 2:30 PM, 2.5h capacity remaining).
- Admin gets a notification with the recommendation.
- Admin sends offer to Emp7: *"Optional extra task: Task A, 10 AM–12 PM, +€X bonus. Accept / Decline."*
- Emp7 accepts. Task A added to their day with compensation flag set. Payroll updated.
- **Emp1's day is lighter (lost Task A). Emp7's day extends. Other 8 untouched.**

### 4.3 Cascading swap, capped at the per-task ceiling

- Emp1 rejects Task A → Try 1 swaps with Emp4 (Emp4 now has Task A).
- Emp4 rejects Task A. Budget consumed: 1/3 for Emp4. Per-task ceiling for Task A: 2/3.
- Try 1 swaps Emp4 ↔ Emp7 (Emp7 now has Task A).
- Emp7 rejects Task A. Per-task ceiling for Task A: 3/3. **Stop.**
- Task A escalates to admin: *"Task A has been rejected 3 times in succession. Please handle manually."*
- Admin's options: override-assign with explanation, reschedule the appointment, cancel.

### 4.4 Mass rejection on a Friday morning

- 10 AM Friday. Within a 4-hour window, 3 employees have rejected their afternoon tasks (Emp1 Task A 2 PM, Emp2 Task B 2 PM, Emp3 Task C 4 PM).
- **Mass-rejection trigger fires** (3 ≥ threshold).
- Per-rejection cascade is paused.
- GA re-run is scoped to:
  - **Locked:** all completed and in-progress tasks (8–10 AM, 10 AM–12 PM blocks)
  - **Re-optimizable:** all 12 PM–5 PM tasks for Friday
  - **Out of scope:** Saturday onwards
- GA produces a new Friday-afternoon schedule with the rejected pairings forbidden.
- Admin sees: *"Re-optimization complete. 7 task assignments changed. 1 task (Task K, 4 PM) could not be fit — escalation required."*
- Admin reviews, approves the bulk changes, and decides Task K gets rescheduled with the client.
- New notifications go to all affected employees. Operations resume.

---

## 5. Optimality properties

### Near-optimality preserved per-step

| Step | Workload effect | Makespan effect | Near-optimality |
|---|---|---|---|
| Try 1 | Net zero | Preserved | ~0% deviation |
| Try 2a (mid-day gap) | Receiver +1 task, rejecter -1 task | Preserved | Small deviation |
| Try 2b | Receiver +1 task on top of full day | Extended (minimized via ranking) | Larger deviation, compensated |
| Try 3 | Admin's call | Admin's call | Out of algorithmic scope |

### Mass-rejection re-run produces fresh near-optimal

After mass-rejection, the GA re-runs over the affected window with the rejection signals as constraints, producing a globally near-optimal schedule for the *new* workforce/task constraints. May leave some appointments unfulfillable; admin decides cuts.

### Self-correcting on the next full GA run

`TaskCompletionObserver` already maintains `efficiency_rating`. Each completed task feeds updated data into the next full GA run. Drift accumulated by single-slot replacements and stretch assignments washes out when the algorithm next sees the team's actual performance.

### Boundedness

- Per-employee budget caps: 3 rejections / month / employee.
- Per-task ceiling caps: 3 rejections / task before escalation.
- Mass-rejection threshold caps: cascade pauses at 3 rejections OR ≥25% in 4h window.
- Result: no scenario silently absorbs unbounded rejection events.

---

## 6. Implementation considerations

The following do not exist in the codebase yet and would need to be built.

### Database
- `tasks.rejection_reason` (string, nullable) — currently logged only.
- `task_rejections` table — audit trail of every rejection (task_id, employee_id, reason, timestamp).
- `employees.rejection_budget_remaining` or a derived count — for monthly caps.
- `tasks.rejection_count` — denormalized counter for the per-task ceiling.

### Services
- `ReassignmentCascadeService` — orchestrates Try 1 → 2a → 2b → 3.
- `BilateralSwapFinder` — feasible swap pairs ranked by composite fitness.
- `MidDayGapFinder` — distinguishes mid-day gaps from end-of-day extensions.
- `StretchCandidateRanker` — uses computed estimated completion time.
- `MassRejectionDetector` — rolling window counter, threshold check.
- `RollingHorizonReOptimizer` — wraps the existing GA with locked-task constraints.

### Controllers / endpoints
- `rejectTask` — extend existing endpoint to validate window, budget, reason; persist reason; trigger cascade.
- Admin-side endpoints for stretch offer, manual reassignment, mass-rejection approval.

### UI
- Employee: rejection modal with reason dropdown, budget remaining indicator, window-closed messaging past 24h.
- Admin: pending-rejection panel, stretch-offer queue, mass-rejection approval dashboard with delta view.

### Jobs / observers
- `EscalateRejectedTaskJob` (queued, optional) — for any time-based escalation if rejection sits unhandled.
- Update `TaskApprovalObserver` to call the cascade service on rejection.

### Open numeric values
- Rejection budget: 3/month (tunable from operational data).
- Per-task ceiling: 3 (tunable).
- Mass-rejection threshold: 3 OR ≥25% in 4h (tunable).
- Compensation rate for Try 2b: undefined — needs business-side decision (flat bonus, OT multiplier, or surge-style multiplier like the existing `premium_surge_multiplier`).

---

## 7. Notifications matrix

Every cascade event has a corresponding notification. Admin/manager notifications are the primary visibility surface for the rejection system — without them, the cascade runs silently and admins don't know what's happening.

### 7.1 Admin / manager notifications

| Event | Recipient | Priority | Channel | Sample message |
|---|---|---|---|---|
| Employee rejects a task (cascade started) | Admin | Low (FYI) | In-app | "Emp `{employee}` rejected `{task}` (`{reason}`). Auto-resolution in progress." |
| Try 1 succeeds — auto bilateral swap | Admin | Low (FYI) | In-app | "Auto-resolved: `{task}` swapped between Emp `{A}` and Emp `{B}`. No action needed." |
| Try 2a succeeds — auto free-slot replacement | Admin | Low (FYI) | In-app | "Auto-resolved: `{task}` reassigned to Emp `{B}` (filled mid-day gap). No action needed." |
| Try 2b required — stretch offer needed | Admin | Medium | In-app + email | "`{task}` couldn't auto-resolve. `{N}` stretch candidates ranked by computed end-of-day. Please review and offer." |
| Stretch candidate declined | Admin | Medium | In-app | "Emp `{B}` declined the stretch offer for `{task}`. Suggest next candidate or escalate." |
| All stretch candidates declined | Admin | High | In-app + email | "All stretch candidates declined `{task}`. Manual handling required." |
| Try 3 reached — manual handling required | Admin | High | In-app + email | "`{task}` reached Try 3 (manual). Auto-cascade exhausted. Override-assign, reschedule, or cancel?" |
| Per-task ceiling reached (3 rejections) | Admin + Manager | High | In-app + email | "`{task}` has been rejected 3 times. Cascade halted. Please handle manually." |
| Mass-rejection threshold tripped | Admin + Manager | Critical | In-app + email + push | "`{N}` rejections detected in 4h window (threshold: 3 or ≥25%). Cascade paused. Re-optimization preparing." |
| Mass-rejection re-run ready for approval | Admin | High | In-app + email | "Re-optimization complete. `{X}` assignments changed, `{Y}` appointments unfulfillable. Review delta and approve / modify." |
| Unfulfillable appointment surfaced | Admin | High | In-app | "Appointment `{appt}` could not be fit. Options: reschedule with client, cancel, stretch with compensation, override." |
| Stretch offer accepted | Admin | Low (FYI) | In-app | "Emp `{B}` accepted stretch offer for `{task}`. Compensation applied. Payroll updated." |

### 7.2 Employee notifications

For completeness, since the cascade also touches non-rejecting employees:

| Event | Recipient | Priority | Sample message |
|---|---|---|---|
| Rejection accepted | Rejecting employee | Info | "Rejection of `{task}` confirmed. Budget remaining: `{n}`/3 this month." |
| Rejection denied — past window | Rejecting employee | Info | "Rejection window closed (≥ 24h before start required). For genuine impediments, file an emergency leave." |
| Rejection denied — budget exhausted | Rejecting employee | Info | "Monthly rejection budget used (3/3). Resets `{date}`. For genuine impediments, file an emergency leave." |
| Task swapped (Try 1) | Both swapped employees | Medium | "Your `{old_task}` has been swapped to `{new_task}`." |
| Task added (Try 2a) | Receiving employee | Medium | "`{task}` added to your day, filling your gap at `{time}`." |
| Stretch offer | Stretch candidate | Medium | "Optional extra task: `{task}` at `{time}`, compensation `{amount}`. Accept / Decline?" |
| Schedule changed (mass-rejection re-run) | All affected employees | Medium | "Your schedule for `{date}` was updated as part of a re-optimization. New tasks: `{...}`." |

### 7.3 Implementation hooks

These would extend `app/Services/Notification/NotificationService.php`:

- `notifyAdminTaskRejected($task, $employee, $reason)` — fires on any rejection (FYI)
- `notifyAdminCascadeAutoResolved($task, $resolution)` — Try 1 / 2a success
- `notifyAdminStretchOfferRequired($task, $candidates)` — Try 2b
- `notifyAdminPerTaskCeilingReached($task)` — 3 rejections
- `notifyAdminMassRejectionTripped($rejections, $window)` — threshold event
- `notifyAdminMassRejectionApprovalRequired($proposedSchedule, $unfulfillable)` — re-run done
- `notifyEmployeeStretchOffer($task, $compensation)` — opt-in prompt
- `notifyEmployeesSwapped($taskA, $taskB, $empA, $empB)` — both sides

The pattern matches existing methods like `notifyAdminsTeamIncompleteStaffing` and `notifyEmployeesJobOpportunity`.

---

## 8. References

- Existing rejection endpoint: `app/Http/Controllers/Api/TaskStatusController.php::rejectTask`
- Existing observer reacting to `employee_approved` change: `app/Observers/TaskApprovalObserver.php`
- Existing emergency-recovery service: `app/Services/Leave/EmergencyLeaveService.php::triggerActiveRecovery`
- Existing optimizer: `app/Services/Optimization/OptimizationService.php`
- Notification methods: `app/Services/Notification/NotificationService.php`
