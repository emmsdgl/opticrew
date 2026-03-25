# Algorithm Comparison - Test Case Guide

## Current Database State
- **11 active employees** (8 drivers, 3 non-drivers)
- **1 personal client** (Emmaus Digol)
- **2 contracted clients** (Kakslauttanen: 58 locations, Aikamatkat: 25 locations)
- **0 tasks** (need to create)

---

## What You Need to Create

### STEP 1: Create 9 More Employees (to reach 20 total)

Go to: **Admin Panel > Accounts > Create Employee**
URL: `/admin/accounts/create` (select Employee type)

Create these 9 employees. Use realistic Finnish-style names:

| #  | Name              | Email                        | Username       | Has License | Efficiency | Experience | Salary/hr |
|----|-------------------|------------------------------|----------------|-------------|------------|------------|-----------|
| 12 | Mika Korhonen     | mika.korhonen@opticrew.fi    | mika.korhonen  | Yes         | 1.00       | 2          | 13.00     |
| 13 | Sanna Virtanen    | sanna.virtanen@opticrew.fi   | sanna.virtanen | No          | 1.00       | 1          | 12.00     |
| 14 | Petri Mäkelä      | petri.makela@opticrew.fi     | petri.makela   | Yes         | 1.00       | 3          | 14.00     |
| 15 | Laura Nieminen    | laura.nieminen@opticrew.fi   | laura.nieminen | No          | 1.00       | 1          | 12.00     |
| 16 | Jari Heikkinen    | jari.heikkinen@opticrew.fi   | jari.heikkinen | Yes         | 1.00       | 2          | 13.00     |
| 17 | Tiina Laine       | tiina.laine@opticrew.fi      | tiina.laine    | No          | 1.00       | 1          | 12.00     |
| 18 | Antti Koskinen    | antti.koskinen@opticrew.fi   | antti.koskinen | Yes         | 1.00       | 2          | 13.00     |
| 19 | Hanna Järvinen    | hanna.jarvinen@opticrew.fi   | hanna.jarvinen | No          | 1.00       | 1          | 12.00     |
| 20 | Ville Salonen     | ville.salonen@opticrew.fi    | ville.salonen  | Yes         | 1.00       | 2          | 13.00     |

**Password for all:** `Password123!`
**Skills:** Select at least "Cleaning" (or whatever skills are available)

After this you'll have: **20 employees** (13 drivers, 7 non-drivers)

---

### STEP 2: Create 35 Tasks for ONE Date

Go to: **Admin Panel > Tasks** (the task calendar)
URL: `/admin/tasks`

Pick a **single future date** for all tasks (e.g., `2026-03-28` or any date that works).
Create tasks under **Kakslauttanen** (contracted client with 58 locations).

**Important:** All 35 tasks must be on the SAME date so the algorithm can optimize them together.

#### Tasks to Create (35 total)

Create tasks by selecting cabins from the Kakslauttanen calendar. Pick "Daily Room Cleaning" or "Deep Cleaning" as service type.

**Batch 1 - Small Cabins (12 tasks, ~60 min each):**
Select these locations: Small Cabin #1 through Small Cabin #12

**Batch 2 - Medium Cabins (6 tasks, ~60 min each):**
Select: Medium Cabin #1 through Medium Cabin #6

**Batch 3 - Big Cabins (7 tasks, ~60 min each):**
Select: Big Cabin #1 through Big Cabin #7

**Batch 4 - Igloos (10 tasks, ~45 min each):**
Select: Igloo #1 through Igloo #10

**Arrival Status:** For variety, mark some as "Arrival" (arrival_status = 1):
- Set Arrival: Small Cabin #1-4, Medium Cabin #1-2, Big Cabin #1-2, Igloo #1-3
- Set Departure or Daily Clean for the rest

**Rate Type:** Use "Normal" for all

This gives you **35 tasks** with mixed durations (60 and 45 minutes).

---

## Running the 3 Setups

Go to: `http://localhost/opticrew/algorithm-comparison/`

The page will show your scheduled date in the dropdown. Configure:

### Table 16 - First Setup
- **Employees:** 10
- **Tasks:** 10
- **Runs:** 10
- **Date:** (your chosen date)

### Table 17 - Second Setup
- **Employees:** 20
- **Tasks:** 15
- **Runs:** 10
- **Date:** (same date)

### Table 18 - Third Setup
- **Employees:** 20
- **Tasks:** 35
- **Runs:** 10
- **Date:** (same date)

Click **"Run All 3 Setups"** and wait for results.

---

## Expected Results

Based on your thesis document, the Hybrid algorithm should generally show:
- **Higher fitness rate** in larger setups (Setup 3 especially)
- **Faster convergence** (fewer generations) due to rule-based preprocessing
- **Comparable or slightly higher run time** than Traditional GA (preprocessing adds overhead)

The Traditional GA should show:
- **Lower fitness** especially as complexity increases
- **More generations needed** (slower convergence)
- **May miss task assignments** (no preprocessing to validate/filter)

---

## Troubleshooting

**"No scheduled tasks found"**
- Tasks must have status "Pending" or "Scheduled" and a future date

**"Insufficient data"**
- Check that employees are active (is_active = 1)
- Check that tasks have a location_id assigned

**Results seem too similar**
- This is normal for small setups (10 tasks)
- The difference becomes more visible at 35 tasks (Setup 3)
- Run multiple times - GA is stochastic (random element)

**Page doesn't load**
- Make sure XAMPP Apache is running
- Access via: http://localhost/opticrew/algorithm-comparison/
