# TanStack Query Setup - Complete Implementation Guide

## 🎯 **Overview**

TanStack Query (React Query) has been successfully initialized across all React applications with consistent configuration and shared utilities.

## 📋 **Apps with TanStack Query**

### ✅ **Implemented**
- **Admin App** (`resources/js/React/Admin/app.tsx`)
- **Student App** (`resources/js/React/Student/app.tsx`)  
- **Instructor App** (`resources/js/React/Instructor/app.tsx`)
- **Support App** (`resources/js/React/Support/app.tsx`)

## 🔧 **Configuration Features**

### **Query Client Settings**
- **Stale Time**: 5 minutes (data stays fresh)
- **Garbage Collection**: 10 minutes (cache cleanup)
- **Smart Retry Logic**: No retry on 4xx errors, up to 3 retries for network/5xx errors
- **Development Tools**: React Query DevTools enabled in development mode

### **Global Error Handling**
- Automatic error logging for mutations
- Integration ready for notification systems
- Consistent error retry behavior

## 💻 **Usage Examples**

### **1. Basic Query Hook**
```typescript
import { useQuery } from '@tanstack/react-query';
import { queryKeys } from '../../../utils/queryConfig';

// In a Student component
const StudentDashboard = () => {
    const { data, isLoading, error } = useQuery({
        queryKey: queryKeys.student.dashboard(),
        queryFn: async () => {
            const response = await fetch('/api/student/dashboard');
            if (!response.ok) throw new Error('Failed to fetch dashboard');
            return response.json();
        },
    });

    if (isLoading) return <div>Loading...</div>;
    if (error) return <div>Error: {error.message}</div>;

    return <div>{/* Dashboard content */}</div>;
};
```

### **2. Mutation Hook with Optimistic Updates**
```typescript
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { queryKeys, mutationKeys } from '../../../utils/queryConfig';

// In an Admin component
const AdminUserManager = () => {
    const queryClient = useQueryClient();
    
    const updateUserMutation = useMutation({
        mutationKey: mutationKeys.admin.updateUser(),
        mutationFn: async (userData: any) => {
            const response = await fetch(`/api/admin/users/${userData.id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(userData),
            });
            if (!response.ok) throw new Error('Failed to update user');
            return response.json();
        },
        onSuccess: () => {
            // Invalidate and refetch users list
            queryClient.invalidateQueries({ queryKey: queryKeys.admin.users() });
        },
    });

    const handleUpdateUser = (userData: any) => {
        updateUserMutation.mutate(userData);
    };

    return <div>{/* User management UI */}</div>;
};
```

### **3. Prefetching Data**
```typescript
import { useEffect } from 'react';
import { useQueryClient } from '@tanstack/react-query';
import { queryKeys, queryUtils } from '../../../utils/queryConfig';

// Prefetch related data when component mounts
const InstructorClassroom = () => {
    const queryClient = useQueryClient();

    useEffect(() => {
        // Prefetch students list when classroom loads
        queryUtils.prefetchQuery(
            queryClient,
            queryKeys.instructor.students(),
            () => fetch('/api/instructor/students').then(res => res.json())
        );
    }, [queryClient]);

    return <div>{/* Classroom content */}</div>;
};
```

## 🛠️ **Development Tools**

### **Browser Console Commands**
```javascript
// View all cached queries and their status
window.tanstackDevTools.logQueries(queryClient);

// Clear all caches (development only)
window.tanstackDevTools.clearAllCaches(queryClient);

// Access React Query DevTools
// Automatically available in development mode (look for React Query icon)
```

### **Query Keys Reference**
```typescript
// Student queries
queryKeys.student.dashboard()           // ['student', 'dashboard']
queryKeys.student.lesson(123)          // ['student', 'lessons', 123]

// Admin queries  
queryKeys.admin.users()                // ['admin', 'users']
queryKeys.admin.course(456)            // ['admin', 'courses', 456]

// Instructor queries
queryKeys.instructor.classes()         // ['instructor', 'classes']
queryKeys.instructor.student(789)     // ['instructor', 'students', 789]

// Support queries
queryKeys.support.tickets()           // ['support', 'tickets']
queryKeys.support.ticket(101)         // ['support', 'tickets', 101]

// Shared queries
queryKeys.shared.profile()            // ['profile']
queryKeys.shared.notifications()      // ['notifications']
```

## 🔄 **Query Invalidation Patterns**

### **After Mutations**
```typescript
// Invalidate specific query
queryClient.invalidateQueries({ queryKey: queryKeys.admin.users() });

// Invalidate all admin queries
queryUtils.invalidateApp(queryClient, 'admin');

// Invalidate with pattern matching
queryClient.invalidateQueries({ queryKey: ['student', 'lessons'] });
```

### **Cross-App Invalidation**
```typescript
// Update shared data that affects multiple apps
const updateProfileMutation = useMutation({
    mutationFn: updateProfile,
    onSuccess: () => {
        // Invalidate profile in all apps
        queryClient.invalidateQueries({ queryKey: queryKeys.shared.profile() });
    },
});
```

## 🚀 **Best Practices**

### **1. Error Handling**
```typescript
const { data, error, isError } = useQuery({
    queryKey: queryKeys.student.lessons(),
    queryFn: fetchLessons,
    throwOnError: false, // Handle errors in component instead of error boundary
});

if (isError) {
    return <ErrorComponent error={error} />;
}
```

### **2. Loading States**
```typescript
const { data, isLoading, isFetching } = useQuery({
    queryKey: queryKeys.admin.dashboard(),
    queryFn: fetchDashboard,
});

// isLoading: true for initial load
// isFetching: true for any fetch (including background refetch)
```

### **3. Optimistic Updates**
```typescript
const createTicketMutation = useMutation({
    mutationFn: createTicket,
    onMutate: async (newTicket) => {
        // Cancel outgoing refetches
        await queryClient.cancelQueries({ queryKey: queryKeys.support.tickets() });
        
        // Snapshot previous value
        const previousTickets = queryClient.getQueryData(queryKeys.support.tickets());
        
        // Optimistically update
        queryClient.setQueryData(queryKeys.support.tickets(), (old: any) => [...old, newTicket]);
        
        return { previousTickets };
    },
    onError: (err, newTicket, context) => {
        // Rollback on error
        queryClient.setQueryData(queryKeys.support.tickets(), context?.previousTickets);
    },
    onSettled: () => {
        // Always refetch after error or success
        queryClient.invalidateQueries({ queryKey: queryKeys.support.tickets() });
    },
});
```

## 📱 **Integration with Laravel**

### **API Response Format**
```php
// Laravel Controller
public function index()
{
    return response()->json([
        'data' => $users,
        'meta' => [
            'total' => $users->count(),
            'page' => request('page', 1),
        ],
    ]);
}
```

### **Frontend Query**
```typescript
const { data } = useQuery({
    queryKey: [...queryKeys.admin.users(), page],
    queryFn: () => fetch(`/api/admin/users?page=${page}`).then(res => res.json()),
    select: (data) => data.data, // Extract data from Laravel response
});
```

## ✅ **Verification**

To verify TanStack Query is working:

1. **Open Browser DevTools**
2. **Look for React Query DevTools** (floating icon in development)
3. **Check console** for TanStack Query logs
4. **Run TypeScript check**: `npx tsc --noEmit` ✅ Passes

## 🎉 **Ready for Use!**

TanStack Query is now fully configured and ready to be used across all React applications. Each app has its own QueryClient instance with shared configuration for consistent behavior.
