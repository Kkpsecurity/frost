export const getCsrfToken = (): string | undefined => {
    const meta = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;
    if (meta?.content) return meta.content;

    const win = window as unknown as { csrfToken?: string };
    if (win.csrfToken) return String(win.csrfToken);

    const m = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/i);
    if (m) return decodeURIComponent(m[1]);
    return undefined;
};
