# 🔴 ACTIVE ISSUE: Instructor Classroom Intermittent Exit

**Date Reported:** January 13, 2026  
**Reporter:** User  
**Severity:** MEDIUM - Disruptive but not blocking  
**Status:** 🔍 UNDER OBSERVATION

---

## Problem Description

**Instructor classroom periodically exits and returns to bulletin board (dashboard), then automatically returns to classroom.**

### Symptoms
- ❌ Classroom view unexpectedly exits to bulletin board
- ✅ Automatically returns to classroom after brief moment
- ⚠️ Occurs randomly, not on every poll
- ⚠️ Unpredictable pattern

### User Impact
- Disruptive to instructor during live class
- Potential loss of focus/context
- Confusing user experience
- May interrupt ongoing actions

---

## Technical Analysis

### Suspected Root Cause

**Polling State Flicker** - Intermittent data causing route/view switching

### Polling Architecture
```
Instructor Dashboard has 4 separate polls:

1. Instructor Poll (30s interval)
   - GET /admin/instructors/instructor/data
   - Returns: instructor profile, active sessions
   
2. Classroom Poll (15s interval)  
   - GET /admin/instructors/classroom/data
   - Returns: courseDates, students, InstUnit
   
3. Chat Poll (3s interval)
   - GET /admin/instructors/chat
   - Returns: messages
   
4. Student Data Poll
   - GET /admin/instructors/data/students
   - Returns: student list with attendance
```

### Likely Causes

#### 1. **InstUnit Intermittently Null**
If `InstUnit` is null in classroom poll, UI thinks class ended:
```typescript
// ClassroomInterface logic
if (!instUnit) {
    // Redirect to bulletin board
    navigate('/admin/instructors');
}
```

**Why InstUnit might disappear:**
- Database query timing issue
- Transaction not committed yet
- Concurrent update during poll
- Cache invalidation race condition

#### 2. **CourseDate Missing**
If `CourseDate` becomes null:
```typescript
if (!courseDate) {
    // No active class
    return <BulletinBoard />;
}
```

**Why CourseDate might disappear:**
- Time-based query (if `starts_at` check too strict)
- Timezone issue
- End time calculation error

#### 3. **Authentication Flicker**
If session validation fails intermittently:
```typescript
// Route guard
if (!isAuthenticated || !isInstructor) {
    redirect('/login');
}
```

#### 4. **React State Race Condition**
Multiple polls updating state simultaneously:
```typescript
// Poll 1 updates: instUnit = null
// UI re-renders → redirect to dashboard
// Poll 2 updates: instUnit = {...}  
// UI re-renders → redirect back to classroom
```

---

## Investigation Steps

### 1. Monitor Network Requests
**Watch Developer Tools Network Tab:**
- Check poll responses for null/missing data
- Look for failed requests (500, 404)
- Check response timing patterns

**Command:**
```bash
# Enable Laravel query log
tail -f storage/logs/laravel.log | grep "classroom/data"
```

### 2. Add Debug Logging
**File:** `app/Http/Controllers/Admin/Instructors/InstructorDashboardController.php`

Add to `getClassroomData()`:
```php
Log::info('Classroom Poll Response', [
    'inst_units_count' => count($instUnits ?? []),
    'course_dates_count' => count($courseDates ?? []),
    'timestamp' => now()->toIso8601String(),
]);
```

### 3. Check Database Queries
**Look for slow queries:**
```sql
-- Check InstUnit query performance
EXPLAIN SELECT * FROM inst_units WHERE completed_at IS NULL;

-- Check CourseDate query
EXPLAIN SELECT * FROM course_dates 
WHERE DATE(starts_at) = CURRENT_DATE 
AND created_by = ?;
```

### 4. Frontend Console Logging
**File:** `resources/js/React/Admin/Instructor/Interfaces/ClassroomInterface.tsx`

Add logging:
```typescript
useEffect(() => {
    console.log('[ClassroomInterface] Data Update', {
        hasInstUnit: !!instructorData?.instUnit,
        hasCourseDate: !!classroomData?.courseDates?.length,
        timestamp: new Date().toISOString(),
    });
}, [instructorData, classroomData]);
```

---

## Possible Solutions

### Solution 1: Add Debouncing
**Prevent rapid state changes:**
```typescript
const debouncedInstUnit = useDebounce(instructorData?.instUnit, 1000);

if (!debouncedInstUnit) {
    // Wait 1 second before redirecting
}
```

### Solution 2: Add State Stability Check
**Only redirect if missing for 2 consecutive polls:**
```typescript
const [missingInstUnitCount, setMissingInstUnitCount] = useState(0);

useEffect(() => {
    if (!instUnit) {
        setMissingInstUnitCount(prev => prev + 1);
    } else {
        setMissingInstUnitCount(0);
    }
}, [instUnit]);

// Only exit if missing for 2+ polls
if (missingInstUnitCount >= 2) {
    navigate('/admin/instructors');
}
```

### Solution 3: Fix Backend Race Condition
**Add database transaction locking:**
```php
DB::transaction(function () use ($instUnitId) {
    $instUnit = InstUnit::lockForUpdate()
        ->where('id', $instUnitId)
        ->first();
    
    // Process...
});
```

### Solution 4: Cache Classroom State
**Cache last valid state for 60 seconds:**
```php
$cacheKey = "classroom_state_{$instructorId}";
$cachedState = Cache::get($cacheKey);

if (!$instUnit && $cachedState) {
    // Return cached state instead of null
    return $cachedState;
}

if ($instUnit) {
    Cache::put($cacheKey, $instUnit, 60); // 60 seconds
}
```

---

## Data to Collect

### When Issue Occurs:
1. ✅ Check browser console for errors
2. ✅ Check Network tab for poll responses
3. ✅ Note exact time of occurrence
4. ✅ Check if any action was taken before flicker
5. ✅ Check database for InstUnit.completed_at changes
6. ✅ Check Laravel logs for exceptions

### Questions to Answer:
- Does it happen at specific times? (e.g., exactly 15s intervals = poll timing)
- Does it happen after specific actions? (start class, end lesson, etc.)
- Does it happen more with certain courses?
- Does it happen when students join/leave?

---

## Temporary Workaround

**For immediate relief, increase poll intervals to reduce race condition likelihood:**

**File:** `resources/js/React/Admin/Instructor/Interfaces/ClassroomInterface.tsx`

```typescript
// Change from 15s to 30s
const { data: classroomData } = useQuery({
    queryKey: ['classroom-data'],
    queryFn: fetchClassroomData,
    refetchInterval: 30000, // Was 15000
});
```

---

## Testing Plan

### Reproduce Issue:
1. Start classroom session
2. Monitor for 10 minutes
3. Note frequency of flickering
4. Check console/network during flicker

### Test Fixes:
1. Apply debouncing → Test for 10 min
2. Apply stability check → Test for 10 min  
3. Compare flicker frequency before/after

---

## Related Files

### Frontend
- `resources/js/React/Admin/Instructor/Interfaces/ClassroomInterface.tsx`
- `resources/js/React/Admin/Instructor/pages/Dashboard.tsx`
- `resources/js/React/Admin/Instructor/hooks/useInstructorData.ts`
- `resources/js/React/Admin/Instructor/hooks/useClassroomData.ts`

### Backend
- `app/Http/Controllers/Admin/Instructors/InstructorDashboardController.php`
- `app/Services/Frost/Instructors/InstructorDataArrayService.php`
- `app/Services/Frost/Instructors/ClassroomDataArrayService.php`

### Models
- `app/Models/InstUnit.php`
- `app/Models/CourseDate.php`

---

## Priority

**Current:** MEDIUM - Monitor and collect data  
**Escalate to HIGH if:** Occurs more than 3 times per 10-minute session

---

## Action Items

- [x] Issue documented
- [ ] Enable debug logging
- [ ] Monitor for 24 hours
- [ ] Collect console logs during flicker
- [ ] Check database query performance
- [ ] Implement stability check (if confirmed)
- [ ] Test solution for 48 hours
- [ ] Mark resolved if no flickers

---

## Notes

**From User:** "its not doing it on every poll but randomly so we will keep an eye out on that"

This suggests:
- Not deterministic
- Not time-based (not every poll interval)
- May be data-dependent (only when certain conditions met)
- Could be external trigger (another instructor action, student activity, cron job)

**Next Update:** Report back with frequency and pattern observations

---

**Last Updated:** January 13, 2026  
**Assigned To:** Development Team  
**Tracking ID:** ISSUE-2026-01-13-CLASSROOM-FLICKER
