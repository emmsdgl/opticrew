# Calendar Features Documentation

## Overview
This document describes the calendar features including current date indicator, past date restrictions, and how to toggle these features for presentations.

---

## Feature 1: Current Date Indicator (TODAY Badge)

### What it does:
- Highlights the current date on the calendar with a blue background
- Displays a "TODAY" badge on the current date
- Uses different colors for light and dark mode

### Visual Indicators:
- **Background**: Blue highlight (`bg-blue-100` in light mode, `bg-blue-900/30` in dark mode)
- **Border**: Blue border (`border-blue-400` in light mode, `border-blue-600` in dark mode)
- **Badge**: White text on blue background with "TODAY" label
- **Date Number**: Blue color (`text-blue-600` in light mode, `text-blue-400` in dark mode)

### Code Location:
File: `resources/views/components/taskcalendar.blade.php`

**Lines 71-75** - TODAY Badge HTML:
```html
<!-- TODAY Badge -->
<template x-if="isToday(date)">
    <span class="text-xs font-bold bg-blue-600 dark:bg-blue-500 text-white px-2 py-0.5 rounded-full">
        TODAY
    </span>
</template>
```

**Lines 612-619** - isToday() Function:
```javascript
// Check if a date is today
isToday(date) {
    if (!date || !date.date) return false;
    const today = new Date();
    return date.date === today.getDate() &&
           this.currentMonth === today.getMonth() &&
           this.currentYear === today.getFullYear();
}
```

---

## Feature 2: Past Date Visual Indicators

### What it does:
- Grays out dates that are in the past
- Reduces opacity to make past dates less prominent
- Changes cursor to "not-allowed" when hovering over past dates
- Dims the date numbers for past dates

### Visual Indicators:
- **Background**: Gray background with reduced opacity (`bg-gray-100 dark:bg-gray-800 opacity-60`)
- **Cursor**: Not-allowed cursor (`cursor-not-allowed`)
- **Date Number**: Grayed out text (`text-gray-400 dark:text-gray-500`)
- **No Hover Effect**: Past dates don't show hover highlight

### Code Location:
File: `resources/views/components/taskcalendar.blade.php`

**Lines 52-57** - Past Date Styling (Calendar Grid):
```html
:class="{
    'bg-blue-100 dark:bg-blue-900/30 border-blue-400 dark:border-blue-600': isToday(date),
    'bg-gray-100 dark:bg-gray-800 opacity-60': isPastDate(date) && !isToday(date),
    'cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900': !isPastDate(date) || isToday(date),
    'cursor-not-allowed': isPastDate(date) && !isToday(date)
}"
```

**Lines 621-628** - isPastDate() Function:
```javascript
// Check if a date is in the past
isPastDate(date) {
    if (!date || !date.date) return false;
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Reset to start of day
    const checkDate = new Date(this.currentYear, this.currentMonth, date.date);
    return checkDate < today;
}
```

---

## Feature 3: Past Date Click Prevention & Warning

### What it does:
- Prevents opening the task creation modal when clicking on past dates
- Shows a warning alert message
- Can be toggled ON/OFF for presentations

### Warning Message:
```
⚠️ You cannot create tasks for previous dates.

Please select today or a future date.
```

### Code Location:
File: `resources/views/components/taskcalendar.blade.php`

**Lines 630-643** - Past Date Validation:
```javascript
openModal(date) {
    // ============================================================
    // PRESENTATION MODE: Toggle past date validation
    // ============================================================
    // To DISABLE past date validation during presentation:
    // Change "true" to "false" in the line below
    const ENABLE_PAST_DATE_VALIDATION = true;
    // ============================================================

    // Check if trying to select a past date
    if (ENABLE_PAST_DATE_VALIDATION && this.isPastDate(date)) {
        alert('⚠️ You cannot create tasks for previous dates.\n\nPlease select today or a future date.');
        return; // Stop execution - do not open modal
    }

    // ... rest of modal opening logic
}
```

---

## How to Toggle Features for Presentation

### Disabling Past Date Validation (Allow Past Date Selection)

**Location**: `resources/views/components/taskcalendar.blade.php` - **Line 636**

**Default (Validation ENABLED)**:
```javascript
const ENABLE_PAST_DATE_VALIDATION = true;
```

**For Presentation (Validation DISABLED)**:
```javascript
const ENABLE_PAST_DATE_VALIDATION = false;
```

### When to Disable:
- **During Demo/Presentation**: If you need to demonstrate creating tasks for past dates
- **For Testing**: When testing historical data scenarios
- **For Data Entry**: When backfilling tasks for previous dates

### When to Keep Enabled:
- **Production Use**: Normal daily operations
- **Security**: Prevent accidental creation of tasks for past dates
- **Data Integrity**: Ensure tasks are only created for current/future dates

---

## Step-by-Step Guide for Presentations

### Before Presentation (If you need to demo past dates):

1. Open the file:
   ```
   resources/views/components/taskcalendar.blade.php
   ```

2. Navigate to **line 636** or search for:
   ```javascript
   ENABLE_PAST_DATE_VALIDATION
   ```

3. Change from:
   ```javascript
   const ENABLE_PAST_DATE_VALIDATION = true;
   ```

   To:
   ```javascript
   const ENABLE_PAST_DATE_VALIDATION = false;
   ```

4. Save the file

5. Refresh your browser (F5 or Ctrl+R)

6. **Test**: Click on a past date - it should now open the modal without warning

### After Presentation (Re-enable validation):

1. Open the same file again

2. Change back to:
   ```javascript
   const ENABLE_PAST_DATE_VALIDATION = true;
   ```

3. Save and refresh

---

## Feature Behavior Summary

| Date Type | Visual Style | Click Behavior (Validation ON) | Click Behavior (Validation OFF) |
|-----------|--------------|-------------------------------|--------------------------------|
| **Today** | Blue highlight + "TODAY" badge | Opens modal ✅ | Opens modal ✅ |
| **Future** | Normal white background | Opens modal ✅ | Opens modal ✅ |
| **Past** | Gray + dimmed + not-allowed cursor | Shows warning ⚠️ | Opens modal ✅ |

---

## Technical Notes

### Date Comparison Logic:
- Uses JavaScript `Date` object for accurate date comparison
- Resets time to `00:00:00` to compare only dates (ignoring time)
- Compares year, month, and day separately for "today" check
- Past dates are any dates before today's date (excluding today)

### Dark Mode Support:
- All features fully support dark mode
- Uses Tailwind CSS dark mode classes (`dark:`)
- Colors automatically adjust based on system/user preference

### Performance:
- Helper functions (`isToday`, `isPastDate`) run efficiently on each calendar cell
- No performance impact even with multiple months displayed
- Date calculations cached within Alpine.js reactive system

---

## Troubleshooting

### Problem: TODAY badge not showing
**Solution**: Check if today's date is within the currently displayed month/year

### Problem: Past dates still clickable after enabling validation
**Solution**:
1. Verify you saved the file after changing the flag
2. Hard refresh the browser (Ctrl+Shift+R or Cmd+Shift+R)
3. Clear browser cache if needed

### Problem: Validation working but visual indicators not showing
**Solution**:
1. Check dark mode vs light mode
2. Verify Tailwind CSS is loaded properly
3. Inspect element to see if classes are applied

### Problem: All dates showing as past dates
**Solution**:
1. Check server time/timezone settings
2. Verify JavaScript Date() is using correct timezone
3. Check if calendar month/year is set correctly

---

## Code Summary

### Functions Added:
1. `isToday(date)` - Returns true if date matches today's date
2. `isPastDate(date)` - Returns true if date is before today

### Constants Added:
1. `ENABLE_PAST_DATE_VALIDATION` - Toggle for past date validation (line 636)

### CSS Classes Used:
- **Today**: `bg-blue-100`, `dark:bg-blue-900/30`, `border-blue-400`, `dark:border-blue-600`
- **Past**: `bg-gray-100`, `dark:bg-gray-800`, `opacity-60`, `cursor-not-allowed`
- **Badge**: `bg-blue-600`, `dark:bg-blue-500`, `text-white`, `rounded-full`

---

## Questions During Presentation?

**Q: "Can users still view tasks on past dates?"**
A: Yes! The validation only prevents CREATING new tasks. Users can still see existing tasks on any date (past, present, or future).

**Q: "What if we need to add a task for yesterday?"**
A: An admin can temporarily disable validation by changing the flag (line 636) from `true` to `false`, create the task, then re-enable it.

**Q: "Does this work across timezones?"**
A: Yes, it uses the browser's local timezone for date comparison, so it will work correctly regardless of user location.

**Q: "Can we customize the warning message?"**
A: Yes! Edit line 641 to change the alert message text.

---

## End of Documentation

For any questions or issues, refer to this document or contact the development team.
