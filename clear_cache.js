/**
 * Clear React Query Cache & Check Current Data
 *
 * Run this in the browser console when on /admin/instructors
 */
console.log("🧹 Clearing React Query cache...");

// Find the query client instance
const queryClient = window.ReactQuery || window.__REACT_QUERY_CLIENT__;

if (queryClient) {
    // Clear specific instructor data cache
    queryClient.invalidateQueries({ queryKey: ["instructor-data"] });
    queryClient.removeQueries({ queryKey: ["instructor-data"] });

    // Clear classroom data cache
    queryClient.invalidateQueries({ queryKey: ["classroom-data"] });
    queryClient.removeQueries({ queryKey: ["classroom-data"] });

    // Clear chat cache
    queryClient.invalidateQueries({ queryKey: ["chat-messages"] });
    queryClient.removeQueries({ queryKey: ["chat-messages"] });

    console.log("✅ Cache cleared! Page should refresh data automatically.");
} else {
    console.log("❌ Could not find React Query client");
    console.log("💡 Try refreshing the page instead");
}

// Also check current cache contents
console.log("📊 Current cache contents:");
if (queryClient) {
    const cache = queryClient.getQueryCache();
    const queries = cache.getAll();

    queries.forEach(query => {
        console.log(`- ${JSON.stringify(query.queryKey)}: `, query.state);
    });
}

// Instructions for manual check
console.log(`
🔍 Next Steps:
1. Check browser network tab for polling requests to:
   - /admin/instructors/instructor/data
   - /admin/instructors/classroom/data
2. Look for "instUnit" field in responses
3. If instUnit is null but ClassroomInterface still shows, there's a React state issue
`);
