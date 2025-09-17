# Frost Classroom: React Structure Overview


## 🏗️ Common Architecture Features

### TanStack Query Configuration
All sections share optimized query client settings:
```typescript
const queryClient = new QueryClient({
    defaultOptions: {
        queries: {
            staleTime: 1000 * 60 * 5,      // 5 minutes
            gcTime: 1000 * 60 * 10,        // 10 minutes
            retry: (failureCount, error: any) => {
                if (error?.status >= 400 && error?.status < 500) return false;
                return failureCount < 3;
            },
            refetchOnWindowFocus: false,
        },
        mutations: { retry: 1 },
    },
});
```

### Error Boundaries
Each section includes dedicated error boundaries:
- `StudentErrorBoundary`
- `EntryErrorBoundary` (Instructor)
- `SupportErrorBoundary`

### Route-Based Loading
Smart loading system prevents unnecessary component loading:
- **Student**: Loads on classroom, offline, or lesson viewer routes
- **Instructor**: Loads on `/admin/instructors` route
- **Support**: Loads on `/admin/frost-support` route

### DOM Mounting Strategy
Robust mounting with fallback mechanisms:
1. DOM ready event listener
2. Immediate mounting if DOM already loaded
3. Delayed retry after 1 second if container not found

---

## 🚀 Ready for Development

Each section is fully operational and ready for feature implementation:

1. **✅ Foundation Complete**: All basic structure in place
2. **✅ Debug Panels Active**: Visual confirmation of functionality
3. **✅ Data Layer Ready**: Prepared for API integration
4. **✅ Error Handling**: Comprehensive error boundaries
5. **✅ Development Tools**: React Query DevTools enabled
6. **✅ Route Detection**: Smart loading based on current page
7. **✅ Console Logging**: Detailed debugging information

---

## 📁 File Structure Summary

```
resources/js/React/
├── Student/
│   ├── app.tsx                    # EntryPoint
│   ├── StudentDataLayer.tsx       # DataLayer + Debug Panel
│   └── ErrorBoundry/
├── Instructor/
│   ├── app.tsx                    # EntryPoint
│   ├── InstructorDataLayer.tsx    # DataLayer + Debug Panel
│   └── ErrorBoundry/
├── Support/
│   ├── app.tsx                    # EntryPoint
│   ├── SupportDataLayer.tsx       # DataLayer + Debug Panel
│   └── ErrorBoundry/
└── utils/
    └── routeUtils.ts             # Route detection utilities
```

## 🎯 Next Steps

With the foundation complete, you can now:
1. Add specific business logic to each DataLayer
2. Implement API integrations using TanStack Query
3. Build custom components for each section
4. Add section-specific routing and navigation
5. Implement real-time features and data synchronization

The debug panels will continue to show that each section is active and ready for your specific feature implementations!
