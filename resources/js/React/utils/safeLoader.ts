/**
 * Error boundary wrapper for dynamically loaded components
 * Prevents one component failure from breaking the entire page
 */

export function safeRequire(componentPath: string, componentName: string = componentPath) {
    try {
        import(componentPath).catch((error) => {
            console.error(`Failed to load component ${componentName}:`, error);
        });
    } catch (error) {
        console.error(`Failed to load component ${componentName}:`, error);
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
