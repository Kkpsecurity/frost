/**
 * Error boundary wrapper for dynamically loaded components
 * Prevents one component failure from breaking the entire page
 */

export function safeRequire(componentPath: string, componentName: string = componentPath) {
    try {
        require(componentPath);
        console.log(`✅ Successfully loaded: ${componentName}`);
    } catch (error) {
        console.error(`❌ Failed to load component: ${componentName}`, error);

        // Optional: Report to error tracking service
        if (typeof window !== 'undefined' && (window as any).errorTracker) {
            (window as any).errorTracker.report({
                type: 'component_load_error',
                component: componentName,
                error: error,
                route: window.location.pathname
            });
        }
    }
}

/**
 * Load multiple components with error handling
 */
export function safeRequireMultiple(components: Array<{path: string, name?: string}>) {
    components.forEach(({ path, name }) => {
        safeRequire(path, name || path);
    });
}
