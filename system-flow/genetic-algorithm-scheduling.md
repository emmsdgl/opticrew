# Genetic Algorithm with Rule-Based Scheduling & Task Allocation

This document describes how the hybrid Genetic Algorithm (GA) with rule-based pre-processing is applied in the Castcrew (OptiCrew) platform for workforce scheduling and task allocation.

---

## 1. Architecture Overview

The optimization system uses a **two-phase hybrid approach**:

```
Phase 1: Rule-Based Pre-Processing
    ├── Task prioritization (Rule 3)
    ├── Task validation
    ├── Workforce calculation
    ├── Employee allocation by client (Rule 1)
    └── Team formation (Rules 2 & 5)

Phase 2: Genetic Algorithm Optimization
    ├── Population initialization (seeded from greedy solution)
    ├── Fitness evaluation
    ├── Selection (tournament)
    ├── Crossover (uniform)
    ├── Mutation (swap / insert / scramble)
    └── Convergence (early stopping with patience)
```

Rule-based logic ensures hard constraints are always satisfied, while the GA optimizes the soft objectives (workload balance, efficiency, utilization).

---

## 2. Rule-Based Pre-Processing

### 2.1 Processing Chain

The `RuleBasedPreProcessor` executes the following steps in order:

| Step | Rule | Operation |
|------|------|-----------|
| 1 | Rule 3 | Sort tasks by priority |
| 2 | — | Validate tasks (location, date, time) |
| 3 | — | Calculate minimum workforce |
| 4 | Rule 1 | Allocate employees by client |

### 2.2 Rule 1: Exclusive Client Allocation

**Constraint**: Each employee is assigned to ONE contracted client per day.

**Implementation** (`RuleBasedPreProcessor`):
1. Group tasks by contracted client
2. Calculate proportional employee distribution based on task count per client
3. Assign employees to clients, prioritizing maintaining existing team compositions
4. No employee appears in tasks for multiple clients on the same day

**Why**: Prevents confusion, reduces travel time, and maintains client-specific knowledge.

### 2.3 Rule 2: Team Composition

**Constraint**: Every team must have at least one driver.

**Implementation** (`TeamFormationService`):
```
Team = 1 Driver + 1-2 Non-Drivers
Minimum size: 2
Maximum size: 3
```

Process:
1. Separate employees into drivers and non-drivers
2. Form teams by pairing one driver with one or two non-drivers
3. If no driver is available for a team, the team cannot be formed

### 2.4 Rule 3: Task Prioritization

**Constraint**: Guest-arrival tasks have the highest scheduling priority.

**Implementation** (`RuleBasedPreProcessor`):
- **Primary sort**: `arrival_status` DESC (arrival tasks first)
- **Secondary sort**: `scheduled_time` ASC (earlier tasks first)

This ensures that time-sensitive tasks (hotel guest arrivals) are always processed before routine cleaning.

### 2.5 Rule 4: Saved Schedule Detection

**Constraint**: If a schedule has already been saved for a date, new tasks should be added without full re-optimization.

**Implementation** (`OptimizationService`):
1. Check if an `OptimizationRun` with `is_saved = true` exists for the target date
2. If yes, attempt to assign pending tasks to existing teams
3. Only trigger full GA optimization if real-time addition fails to place all tasks

### 2.6 Rule 5: Maximum Employee Utilization

**Constraint**: Create the maximum number of teams to utilize ALL available employees.

**Implementation** (`TeamFormationService`):
- Prefer pairs (2 members) over trios (3 members) — more teams serve more locations
- Only form one trio if the employee count is odd
- No employee is left unassigned unless insufficient drivers exist

### 2.7 Rule 9: Locked Teams

**Constraint**: Previously saved team compositions should be preserved during re-optimization.

**Implementation** (`GeneticAlgorithmOptimizer`):
- Locked teams are passed as parameters to the optimizer
- The GA skips mutation and crossover operations on locked team assignments
- Only unlocked (new) task assignments are optimized

---

## 3. Workforce Calculation

### 3.1 Five-Step Formula

The `WorkforceCalculator` determines the optimal number of employees using:

**Step (a) — Task Duration Estimation**:
```
Duration = Floor Area (m²) / Cleaning Speed (m²/hour)
Default Cleaning Speed: 10 m²/hour
```

**Step (b) — Total Hours per Client**:
```
Total Hours = Σ(task durations) + travel time overhead
```

**Step (c) — Minimum Workforce**:
```
Minimum = ⌈ Total Hours / (Available Hours × Utilization Rate) ⌉
Default Available Hours: 8 hours/day
Default Utilization Rate: 85%
```

**Step (d) — Budget-Constrained Maximum**:
```
Max Affordable = ⌊ Budget Limit / (Hourly Wage × Available Hours + Benefits) ⌋
```

**Step (e) — Final Workforce**:
```
Final = max(baseline, min(calculated_need, budget_max))
```

---

## 4. Genetic Algorithm

### 4.1 Chromosome Representation

Each **individual** (chromosome) represents a complete schedule:

```
Individual = {
    schedule: {
        team_0: [task_3, task_7, task_1],   // Tasks assigned to team 0
        team_1: [task_2, task_5],            // Tasks assigned to team 1
        team_2: [task_4, task_6, task_8],    // Tasks assigned to team 2
    },
    fitness: 0.847
}
```

Each gene represents a task-to-team assignment. The chromosome length equals the total number of tasks.

### 4.2 Population Initialization

**Population Management** (`Population.php`):
- **Size**: Configurable (default: 20 individuals, via `GA_POPULATION_SIZE`)
- **Seeding**: Initial population is seeded from a **greedy solution** produced by rule-based pre-processing
- **Diversity**: Remaining individuals are generated with random task-to-team mutations of the seed

### 4.3 Fitness Function

The `FitnessCalculator` uses a **multiplicative fitness function**:

```
Fitness = Base × Constraint × Completion × TaskBalance
```

**Component Breakdown**:

| Component | Formula | Purpose |
|-----------|---------|---------|
| **Base Fitness** | `1 / (1 + workloadStdDev)` | Rewards balanced workload distribution across teams |
| **Constraint Multiplier** | `0.5` if 12-hour violated; `0.7-1.0` if deadline missed | Penalizes constraint violations |
| **Completion Multiplier** | `(assigned / total)^4` | Heavy penalty for unassigned tasks |
| **Task Balance** | Exponential decay based on task count stdDev | Rewards even task count distribution |

**Hard Constraints Tracked**:
- **12-Hour Rule**: Maximum 12 working hours per employee per day (Finnish labor compliance)
- **3 PM Deadline**: Arrival/urgent tasks must be completed by 15:00 (900 minutes)

**Constants**:
```php
MAX_HOURS_PER_DAY = 12
DEADLINE_TIME_MINUTES = 900  // 3:00 PM
```

### 4.4 Selection

**Method**: Tournament Selection
- **Tournament Size**: Configurable (default: 3, via `GA_TOURNAMENT_SIZE`)
- Randomly select `k` individuals from the population
- The individual with the highest fitness wins and becomes a parent
- Repeated to select two parents for each offspring

### 4.5 Crossover

**Method**: Uniform Crossover (`CrossoverOperator.php`)

```
Parent A:  [Team0, Team1, Team0, Team2, Team1]  (task-to-team mapping)
Parent B:  [Team1, Team0, Team2, Team0, Team2]

Mask:      [  0  ,   1  ,   0  ,   1  ,   0  ]  (random 50/50)

Child:     [Team0, Team0, Team0, Team0, Team1]  (inherit from A or B based on mask)
```

- **Rate**: Applied to all offspring (100%)
- **Repair Strategy**: After crossover, if any task is unassigned or duplicated, the repair function assigns orphaned tasks to the **least loaded team** (balance-aware)
- **Alternative**: Order crossover is available for preserving task sequences within teams

### 4.6 Mutation

**Three Mutation Types** (`MutationOperator.php`):

| Type | Operation | When Useful |
|------|-----------|-------------|
| **Swap** | Exchange two tasks between different teams | Exploring neighborhood solutions |
| **Insert** | Move task from most loaded → least loaded team | Improving balance |
| **Scramble** | Reorder tasks within a single team | Optimizing intra-team scheduling |

- **Rate**: 20% per individual (configurable via `GA_MUTATION_RATE`)
- **Selection**: One mutation type is randomly chosen per application
- **Locked Teams**: Mutations skip locked team assignments (Rule 9)

### 4.7 Convergence & Termination

**Stopping Criteria**:
1. **Maximum Generations**: Default 100 (configurable via `GA_MAX_GENERATIONS`)
2. **Early Stopping**: If no fitness improvement for `patience` consecutive generations (default: 15, via `GA_PATIENCE`)

**Generation Tracking** (`OptimizationGeneration` model):
Each generation records:
- `generation_number`
- `best_fitness`, `average_fitness`, `worst_fitness`
- `is_improvement`: Whether this generation improved on the previous best
- `best_schedule_data`: JSON snapshot of the best schedule

---

## 5. Team Efficiency in Optimization

### 5.1 Efficiency Calculation

The `TeamEfficiencyCalculator` scores each team for the fitness function:

```
Efficiency = Base × (1 + Skill Bonus) × (1 + Experience Bonus) × Size Adjustment
```

| Factor | Calculation | Range |
|--------|-------------|-------|
| **Base** | Average of individual employee efficiencies | 0-1 |
| **Skill Diversity** | Bonus for diverse skill sets | 0-15% |
| **Junior-Senior Synergy** | Pairing experienced with junior employees | +7.5% |
| **Experienced Team** | Average experience > 5 years | +4.5% |
| **Size Penalty** | -5% per member over optimal (2) | Negative |
| **Final Range** | Capped | 0.5x - 2.0x |

### 5.2 Impact on Scheduling

Teams with higher efficiency scores:
- Can handle more tasks in the same time window
- Are preferred for high-priority tasks (arrival status)
- Reduce the overall workforce requirement

---

## 6. Configuration Parameters

All GA and workforce parameters are configurable via environment variables and the `config/optimization.php` file:

### Genetic Algorithm Parameters

| Parameter | Env Variable | Default | Description |
|-----------|-------------|---------|-------------|
| Population Size | `GA_POPULATION_SIZE` | 20 | Number of individuals per generation |
| Max Generations | `GA_MAX_GENERATIONS` | 100 | Maximum iterations |
| Mutation Rate | `GA_MUTATION_RATE` | 0.10 | Probability of mutation per individual |
| Tournament Size | `GA_TOURNAMENT_SIZE` | 3 | Selection pressure |
| Patience | `GA_PATIENCE` | 15 | Early stopping threshold |

### Workforce Parameters

| Parameter | Env Variable | Default | Description |
|-----------|-------------|---------|-------------|
| Cleaning Speed | — | 10 m²/hour | Area cleaning rate |
| Available Hours | — | 8 hours/day | Working hours per employee |
| Utilization Rate | — | 85% | Target utilization |
| Budget Limit | `WORKFORCE_BUDGET_LIMIT` | — | Maximum labor cost |
| Work Start | `WORK_START_TIME` | — | Shift start time |
| Work End | `WORK_END_TIME` | — | Shift end time |

### Admin-Configurable Settings (via Settings Page)

| Setting | Default | Range |
|---------|---------|-------|
| Overtime Threshold | 8 hours | 1-24 |
| Geofence Radius | 100 meters | 10-1000 |
| Task Approval Grace Period | 30 minutes | 5-240 |
| Reassignment Grace Period | 30 minutes | 5-240 |
| Unstaffed Escalation Timeout | 60 minutes | 10-480 |

---

## 7. Optimization Flow (End-to-End)

```
┌──────────────────────────────────────────────────────────────┐
│                    OPTIMIZATION TRIGGER                       │
│            (Admin clicks "Optimize" or new task added)        │
└────────────────────────┬─────────────────────────────────────┘
                         │
                         ▼
┌──────────────────────────────────────────────────────────────┐
│              RULE 4: CHECK SAVED SCHEDULE                     │
│  Is there a saved schedule for this date?                     │
│  YES → Try real-time task addition to existing teams          │
│  NO  → Proceed to full optimization                           │
└────────────────────────┬─────────────────────────────────────┘
                         │
          ┌──────────────┴──────────────┐
          ▼                             ▼
┌─────────────────────┐    ┌────────────────────────┐
│  Real-Time Addition  │    │  Full Optimization      │
│  Assign to existing  │    │                          │
│  teams               │    │  ┌────────────────────┐  │
│                      │    │  │ PHASE 1: RULES     │  │
│  All placed? ────YES─┼──▶ │  │ Sort by priority   │  │
│       │              │    │  │ Validate tasks     │  │
│       NO             │    │  │ Calculate workforce│  │
│       │              │    │  │ Allocate employees │  │
│       ▼              │    │  │ Form teams         │  │
│  Fall through to ────┼──▶ │  └────────┬───────────┘  │
│  full optimization   │    │           │               │
└─────────────────────┘    │  ┌────────▼───────────┐  │
                            │  │ PHASE 2: GA        │  │
                            │  │                    │  │
                            │  │ Initialize pop.    │  │
                            │  │ ┌────────────────┐ │  │
                            │  │ │ For each gen:  │ │  │
                            │  │ │  Evaluate      │ │  │
                            │  │ │  Select        │ │  │
                            │  │ │  Crossover     │ │  │
                            │  │ │  Mutate        │ │  │
                            │  │ │  Check stop    │ │  │
                            │  │ └────────────────┘ │  │
                            │  │                    │  │
                            │  │ Return best        │  │
                            │  └────────┬───────────┘  │
                            │           │               │
                            └───────────┼───────────────┘
                                        │
                                        ▼
                            ┌───────────────────────┐
                            │   SAVE RESULTS         │
                            │   OptimizationRun      │
                            │   OptimizationTeam     │
                            │   Task assignments     │
                            │   Generation history   │
                            └───────────────────────┘
```

---

## 8. Disruption Handling

### 8.1 Scenario Manager

When real-time disruptions occur, the `ScenarioManager` handles re-scheduling:

| Scenario | Handler | Strategy |
|----------|---------|----------|
| Emergency Task | `EmergencyTaskScenario` | Priority insertion, may trigger re-optimization |
| Employee Absence | `EmployeeAbsenceScenario` | Reassign tasks to remaining team members or other teams |
| Vehicle Breakdown | `VehicleBreakdownScenario` | Reroute affected teams, swap vehicles if available |
| Time Constraint | `TimeConstraintScenario` | Reschedule tasks exceeding deadlines |

### 8.2 Impact Analysis

The `ImpactAnalyzer` evaluates disruption impact:
1. Identifies affected tasks and teams
2. Recalculates fitness score with the disruption applied
3. Compares pre/post disruption fitness
4. Recommends recovery strategy (minor adjustment vs. full re-optimization)

---

## 9. Performance Characteristics

| Metric | Typical Value | Notes |
|--------|--------------|-------|
| Population Size | 20 | Balances diversity vs. computation time |
| Convergence | 30-60 generations | Usually early-stops before max |
| Optimization Time | 2-10 seconds | Depends on task count and team count |
| Fitness Score Range | 0.0 - 1.0 | Higher is better |
| Typical Best Fitness | 0.75 - 0.95 | For well-constrained problems |

---

## 10. Rule Summary

| Rule | Description | Enforcement |
|------|-------------|-------------|
| Rule 1 | Exclusive client allocation per employee per day | Pre-processing |
| Rule 2 | Every team must include at least one driver | Team formation |
| Rule 3 | Arrival tasks have highest priority | Task sorting |
| Rule 4 | Saved schedules are preserved; new tasks added in real-time | Optimization service |
| Rule 5 | Maximize employee utilization (all employees assigned) | Team formation |
| Rule 8 | Real-time schedule extension before full re-optimization | Optimization service |
| Rule 9 | Locked teams are preserved during re-optimization | GA operators |
| 12-Hour | Maximum 12 working hours per employee per day | Fitness penalty |
| 3 PM Deadline | Arrival tasks must complete by 15:00 | Fitness penalty |
