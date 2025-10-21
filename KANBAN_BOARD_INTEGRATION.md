# Kanban Board Database Integration

## Overview
The Kanban board has been successfully connected to the database to display real tasks with team assignments, priorities, and status tracking.

---

## Features Implemented

### 1. Database-Driven Task Display
- Tasks are loaded directly from the database
- Real-time data from `tasks` table
- Includes team assignments from `optimization_teams` and `optimization_team_members`

### 2. Automatic Priority Assignment
Tasks are automatically prioritized based on:
- **Urgent (Red)**: Tasks with `arrival_status = true`
- **High (Orange)**: Tasks scheduled for today
- **Medium (Yellow)**: Tasks scheduled for tomorrow
- **Normal (Green)**: All other tasks

### 3. Team Member Avatars
- Shows real team members assigned to each task
- Avatar displays initials of team members
- Hover tooltip shows full name and role
- **Placeholder ready for future profile pictures**
- Falls back to team name if no members assigned

### 4. Drag & Drop Status Updates
- Drag tasks between columns (To Do, In Progress, Completed)
- Automatically updates status in database
- Optimistic UI updates (instant feedback)
- Error handling with rollback on failure

### 5. Status Mapping
**Kanban → Database**:
- `todo` → `Pending`
- `inprogress` → `In Progress`
- `completed` → `Completed`

**Database → Kanban**:
- `Pending` → `todo`
- `In Progress` / `In-Progress` → `inprogress`
- `Completed` → `completed`

---

## Files Modified

### 1. **app/Http/Controllers/TaskController.php**

**Lines 125-211**: Enhanced task data preparation for Kanban board

**Key Additions**:
```php
// Get team members
$teamMembers = [];
$teamName = null;
if ($task->assigned_team_id) {
    $optimizationTeam = \App\Models\OptimizationTeam::with('members.employee')
        ->find($task->assigned_team_id);

    if ($optimizationTeam) {
        $teamName = $optimizationTeam->team_name;
        $teamMembers = $optimizationTeam->members()
            ->with('employee')
            ->get()
            ->map(function($member) {
                return [
                    'id' => $member->employee->id,
                    'name' => $member->employee->first_name . ' ' . $member->employee->last_name,
                    'role' => $member->employee->role,
                    'picture' => null  // Placeholder for future images
                ];
            })
            ->toArray();
    }
}

// Automatic priority assignment
$priority = 'Normal';
$priorityColor = 'bg-[#2FBC0020] text-[#2FBC00]';

if ($task->arrival_status) {
    $priority = 'Urgent';
    $priorityColor = 'bg-[#FE1E2820] text-[#FE1E28]';
} else {
    $taskDate = Carbon::parse($task->scheduled_date);
    $today = Carbon::today();
    $tomorrow = Carbon::tomorrow();

    if ($taskDate->isSameDay($today)) {
        $priority = 'High';
        $priorityColor = 'bg-[#FF7F0020] text-[#FF7F00]';
    } elseif ($taskDate->isSameDay($tomorrow)) {
        $priority = 'Medium';
        $priorityColor = 'bg-[#FFB70020] text-[#FFB700]';
    }
}
```

**Lines 447-486**: New API endpoint for status updates
```php
public function updateStatus(Request $request, $taskId)
{
    $request->validate([
        'status' => 'required|string|in:Pending,In Progress,Completed,On Hold'
    ]);

    try {
        $task = Task::findOrFail($taskId);
        $task->status = $request->status;
        $task->save();

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully',
            'task' => [
                'id' => $task->id,
                'status' => $task->status
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update task status: ' . $e->getMessage()
        ], 500);
    }
}
```

### 2. **resources/views/components/kanbanboard.blade.php**

**Line 1-3**: Accept tasks from database
```php
@props(['tasks' => []])

<div x-data="kanbanBoard(@js($tasks))" x-init="init()">
```

**Lines 34-53**: Team avatar component with real data
```html
<!-- Team Avatar Component - Using real team members -->
<div class="flex -space-x-2">
    <template x-for="(member, mIndex) in task.teamMembers" :key="member.id">
        <div class="relative group">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xs font-bold border-2 border-white dark:border-gray-800 shadow-sm"
                :title="member.name + ' (' + member.role + ')'">
                <span x-text="member.name.split(' ').map(n => n[0]).join('').substring(0, 2)"></span>
            </div>
            <!-- Tooltip showing name and role -->
            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
                <span x-text="member.name"></span>
                <span class="block text-gray-300" x-text="member.role"></span>
            </div>
        </div>
    </template>
    <!-- Show team name if no members -->
    <template x-if="!task.teamMembers || task.teamMembers.length === 0">
        <span class="text-xs text-gray-500 dark:text-gray-400 italic" x-text="task.team"></span>
    </template>
</div>
```

**Lines 76-92**: Initialize with database tasks
```javascript
function kanbanBoard(initialTasks = []) {
    return {
        columns: [
            { name: 'To Do', status: 'todo' },
            { name: 'In Progress', status: 'inprogress' },
            { name: 'Completed', status: 'completed' }
        ],
        tasks: initialTasks,  // From database
        draggedTaskId: null,
        showModal: false,
        editForm: { id: null, title: '', priority: '' },

        init() {
            console.log('Kanban board loaded with', this.tasks.length, 'tasks from database');
            this.updateTheme();
        },
```

**Lines 99-155**: Enhanced drag-drop with database updates
```javascript
async dropTask(event, newStatus) {
    event.preventDefault();
    const task = this.tasks.find(t => t.id === this.draggedTaskId);

    if (!task || task.status === newStatus) {
        this.draggedTaskId = null;
        return; // No change needed
    }

    const oldStatus = task.status;

    // Optimistically update UI
    task.status = newStatus;

    // Map Kanban status to database status
    let dbStatus = 'Pending';
    switch (newStatus) {
        case 'todo':
            dbStatus = 'Pending';
            break;
        case 'inprogress':
            dbStatus = 'In Progress';
            break;
        case 'completed':
            dbStatus = 'Completed';
            break;
    }

    // Update in database
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await fetch(`/tasks/${task.id}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: dbStatus })
        });

        if (!response.ok) {
            throw new Error('Failed to update task status');
        }

        const data = await response.json();
        console.log('Task status updated:', data);

    } catch (error) {
        console.error('Error updating task status:', error);
        // Revert UI on error
        task.status = oldStatus;
        alert('Failed to update task status. Please try again.');
    }

    this.draggedTaskId = null;
}
```

### 3. **routes/web.php**

**Lines 47-48**: New route for status updates
```php
// Update task status (Kanban board drag & drop)
Route::patch('/tasks/{taskId}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
```

---

## Task Data Structure

Each task in the Kanban board now includes:

```javascript
{
    id: 123,                          // Database task ID
    client: "Kakslauttanen",          // Client name
    title: "Deep Cleaning (Arrival)", // Task description
    team: "Team 1",                   // Team name
    teamMembers: [                    // Array of team members
        {
            id: 1,
            name: "Maria Korhonen",
            role: "Driver",
            picture: null             // Placeholder for future images
        },
        {
            id: 5,
            name: "Anna Mäkinen",
            role: "Cleaner",
            picture: null
        }
    ],
    date: "October 21, 2024",         // Formatted date
    time: "8:00 AM",                  // Formatted time
    priority: "Urgent",               // Calculated priority
    priorityColor: "bg-[#FE1E2820] text-[#FE1E28]", // Color class
    status: "todo",                   // Kanban status
    arrival_status: true,             // Urgent flag
    location: "Cabin A01"             // Location name
}
```

---

## Priority Color Codes

| Priority | Background Color | Text Color | When Applied |
|----------|-----------------|------------|--------------|
| **Urgent** | `#FE1E2820` (Red bg) | `#FE1E28` (Red text) | `arrival_status = true` |
| **High** | `#FF7F0020` (Orange bg) | `#FF7F00` (Orange text) | Scheduled for today |
| **Medium** | `#FFB70020` (Yellow bg) | `#FFB700` (Yellow text) | Scheduled for tomorrow |
| **Normal** | `#2FBC0020` (Green bg) | `#2FBC00` (Green text) | All other tasks |

---

## API Endpoints

### Update Task Status
**Endpoint**: `PATCH /tasks/{taskId}/status`

**Request**:
```json
{
    "status": "In Progress"
}
```

**Response (Success)**:
```json
{
    "success": true,
    "message": "Task status updated successfully",
    "task": {
        "id": 123,
        "status": "In Progress"
    }
}
```

**Response (Error)**:
```json
{
    "success": false,
    "message": "Failed to update task status: Task not found"
}
```

**Valid Status Values**:
- `Pending`
- `In Progress`
- `Completed`
- `On Hold`

---

## How It Works

### 1. Page Load
1. Controller fetches all tasks from database
2. Loads team assignments from `optimization_teams` and `optimization_team_members`
3. Calculates priority based on arrival status and date
4. Maps database status to Kanban status
5. Passes data to Blade component via `@js($tasks)`
6. Alpine.js initializes Kanban board with data

### 2. Drag & Drop
1. User drags task card to new column
2. Alpine.js updates UI immediately (optimistic update)
3. Makes PATCH request to `/tasks/{taskId}/status`
4. Backend updates `status` in `tasks` table
5. Returns success/error response
6. On error: Reverts UI to previous state

### 3. Team Avatar Display
1. Checks if task has `teamMembers` array
2. For each member, creates circular avatar with initials
3. Initials = first letter of first name + first letter of last name
4. Hover shows tooltip with full name and role
5. If no members: Shows team name as text

---

## Database Relationships Used

```
tasks
├── location (belongsTo) → locations
│   └── contractedClient (belongsTo) → contracted_clients
├── client (belongsTo) → clients
└── assigned_team_id → optimization_teams
    └── members → optimization_team_members
        └── employee → employees
```

---

## Future Enhancements (Ready for Implementation)

### Profile Pictures
The avatar component is ready for profile pictures. When you add employee photos:

**Update in Controller** (Line 153):
```php
'picture' => $member->employee->profile_picture_url ?? null
```

**Update Avatar HTML**:
```html
<template x-if="member.picture">
    <img :src="member.picture" :alt="member.name"
         class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-800 shadow-sm">
</template>
<template x-if="!member.picture">
    <!-- Current initials display -->
    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600...">
        <span x-text="member.name.split(' ').map(n => n[0]).join('').substring(0, 2)"></span>
    </div>
</template>
```

---

## Testing the Integration

### 1. Check Tasks Load from Database
1. Navigate to Admin Tasks page
2. Scroll to Kanban board section
3. Open browser console (F12)
4. Should see: `Kanban board loaded with X tasks from database`
5. Verify tasks appear in correct columns

### 2. Test Drag & Drop
1. Drag a task from "To Do" to "In Progress"
2. Console should show: `Task status updated: {success: true, ...}`
3. Refresh page
4. Task should still be in "In Progress" column (persisted)

### 3. Verify Priority Colors
1. Create task with arrival status = true → Should be **Red (Urgent)**
2. Create task for today → Should be **Orange (High)**
3. Create task for tomorrow → Should be **Yellow (Medium)**
4. Create task for future date → Should be **Green (Normal)**

### 4. Check Team Avatars
1. Tasks with assigned teams should show avatar circles
2. Hover over avatar → Should show employee name and role
3. Tasks without teams should show "Unassigned" text

### 5. Verify Database Updates
```sql
-- Check status after drag-drop
SELECT id, task_description, status, scheduled_date
FROM tasks
WHERE status = 'In Progress'
ORDER BY id DESC
LIMIT 5;
```

---

## Troubleshooting

### Problem: Tasks not showing in Kanban board
**Check**:
1. Are there tasks in the database? `SELECT COUNT(*) FROM tasks;`
2. Check browser console for errors
3. Verify `$tasks` is being passed to component in `admin-tasks.blade.php`

### Problem: Drag & drop not working
**Check**:
1. Browser console for JavaScript errors
2. CSRF token is present: `meta[name="csrf-token"]` in layout
3. Route is registered: `php artisan route:list | grep tasks`

### Problem: Team members not showing
**Check**:
1. Tasks have `assigned_team_id` set
2. Teams exist in `optimization_teams` table
3. Team members exist in `optimization_team_members` table
4. Employees exist in `employees` table

### Problem: Wrong priority colors
**Check**:
1. `arrival_status` field in tasks table
2. `scheduled_date` is correct format (YYYY-MM-DD)
3. Server timezone matches expected timezone

---

## Summary

The Kanban board is now fully integrated with the database:
✅ Displays real tasks from database
✅ Shows actual team members with avatars
✅ Automatic priority assignment
✅ Drag & drop updates database
✅ Error handling with rollback
✅ Ready for profile pictures
✅ Dark mode support

All data flows from: `tasks` table → Controller → Blade component → Alpine.js → User Interface

No more sample data - everything is live and connected!
