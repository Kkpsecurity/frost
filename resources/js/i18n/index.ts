/**
 * Lightweight i18n utility for the Student React app.
 *
 * Usage:
 *   import { t } from '@/i18n';
 *
 *   t('classroom.joinClass')           // "Join Class" | "Unirse a la Clase"
 *   t('dashboard.welcome', { name })   // "Welcome back, Alice!"
 *
 * Locale is resolved from window.APP_LOCALE (injected by site-wrapper.blade.php).
 * Falls back to 'en' if the key is missing in the active locale.
 */

import en from './en.json';
import es from './es.json';

type Translations = Record<string, unknown>;

const catalogs: Record<string, Translations> = { en, es };

/**
 * Resolve a dot-notated key against a flat/nested object.
 */
function resolve(obj: Translations, key: string): string | null {
    const parts = key.split('.');
    let cursor: unknown = obj;
    for (const part of parts) {
        if (cursor === null || typeof cursor !== 'object') return null;
        cursor = (cursor as Record<string, unknown>)[part];
    }
    return typeof cursor === 'string' ? cursor : null;
}

/**
 * Replace {{placeholder}} tokens in a translation string.
 */
function interpolate(str: string, params: Record<string, string | number>): string {
    return str.replace(/\{\{(\w+)\}\}/g, (_, key) =>
        params[key] !== undefined ? String(params[key]) : `{{${key}}}`
    );
}

/**
 * Get the active locale from the window global set by Laravel.
 * Defaults to 'en'.
 */
export function getLocale(): string {
    return (typeof window !== 'undefined' && (window as any).APP_LOCALE) || 'en';
}

/**
 * Translate a dot-notated key, with optional interpolation params.
 * Falls back to the 'en' catalog, then the raw key, so nothing ever throws.
 */
export function t(key: string, params?: Record<string, string | number>): string {
    const locale = getLocale();
    const catalog = catalogs[locale] ?? catalogs['en'];

    const raw =
        resolve(catalog, key) ??
        resolve(catalogs['en'], key) ??
        key;

    return params ? interpolate(raw, params) : raw;
}

export default t;
