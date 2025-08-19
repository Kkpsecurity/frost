/**
 * Laravel Helper utilities for React components
 */

export const getCsrfToken = (): string => {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    return token || '';
};

export const getBaseUrl = (): string => {
    return window.location.origin;
};

export const getApiUrl = (path: string): string => {
    const baseUrl = getBaseUrl();
    return `${baseUrl}/api/v1${path}`;
};
