import React from 'react';
import { useQuery } from '@tanstack/react-query';
import { getCsrfToken } from "../../utils/LaravelHelper";
import { BaseLaravelShape } from '../Types/laravel.types';
// import endpoints from '@/React/utils/endpoints'; // TODO: Create endpoints file

const isDev = process.env.NODE_ENV === 'development';

type AdminConfigResult = Pick<BaseLaravelShape, 'config' | 'settings'>;

// Custom error class for better error handling
class LaravelApiError extends Error {
  constructor(
    message: string,
    public status: number,
    public statusText: string,
    public response?: string
  ) {
    super(message);
    this.name = 'LaravelApiError';
  }
}

/* ========= Laravel Admin Hook (returns only config + settings) ========= */
export const useLaravelAdminHook = () =>
  useQuery<BaseLaravelShape, LaravelApiError, AdminConfigResult>({
    queryKey: ['laravel', 'admin', 'config'],
    queryFn: async ({ signal }) => {
      const startTime = isDev ? performance.now() : 0;

      try {
        const csrf = getCsrfToken();
        const headers: HeadersInit = {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        };
        if (csrf) {
          (headers as Record<string, string>)['X-XSRF-TOKEN'] = csrf;
          (headers as Record<string, string>)['X-CSRF-TOKEN'] = csrf;
        }

        if (isDev) {
          console.log("🔄 Fetching Laravel admin config…", {
              endpoint: "/api/admin/config", // TODO: Move to endpoints file
              csrf: csrf ? "✅ Present" : "❌ Missing",
          });
        }

        const res = await fetch("/api/admin/config", {
            method: "GET",
            headers,
            credentials: "include",
            signal,
        });

        if (!res.ok) {
          const errorText = await res.text().catch(() => 'Unknown error');
          throw new LaravelApiError(
            `Laravel admin config fetch failed: ${res.status} ${res.statusText} - ${errorText}`,
            res.status,
            res.statusText,
            errorText
          );
        }

        const data = (await res.json()) as BaseLaravelShape;

        if (isDev) {
          const endTime = performance.now();
          console.log('✅ Laravel admin config loaded', {
            loadTime: `${(endTime - startTime).toFixed(2)}ms`,
            appName: data.app?.name,
            environment: data.app?.env,
            configKeys: Object.keys(data.config || {}),
            settingsKeys: Object.keys(data.settings || {}),
          });
        }

        return data;
      } catch (error) {
        if (isDev) {
          const endTime = performance.now();
          console.error('❌ Laravel admin config fetch failed', {
            error: error instanceof Error ? error.message : 'Unknown error',
            loadTime: `${(endTime - startTime).toFixed(2)}ms`,
          });
        }
        throw error;
      }
    },
    // Map to ONLY what the caller needs
    select: (data) => ({
      config: data.config ?? {},
      settings: data.settings ?? {},
    }),
    staleTime: 10 * 60 * 1000,
    gcTime: 30 * 60 * 1000,
    retry: (failureCount, error) => {
      if (error instanceof LaravelApiError && (error.status === 401 || error.status === 403)) return false;
      return failureCount < 3;
    },
    retryDelay: (attemptIndex) => Math.min(1000 * 2 ** attemptIndex, 30000),
  });

