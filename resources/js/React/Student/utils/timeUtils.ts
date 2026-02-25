/**
 * timeUtils.ts — Eastern-time display helpers
 *
 * All class times in FROST are stored and served in America/New_York (Eastern).
 * Always display them with an explicit timezone label so students in other
 * timezones (CST, MST, PST, etc.) know the time is Eastern.
 *
 * Usage:
 *   formatEasternTime("2025-07-14T14:00:00Z")   → "10:00 AM EST"
 *   formatEasternDateTime("2025-07-14T14:00:00Z") → "July 14, 2025 10:00 AM EST"
 *   formatEasternTimeOnly("2025-07-14T14:00:00Z") → "10:00 AM EST"
 */

const EASTERN_TZ = "America/New_York";

/**
 * Return the correct "EST" or "EDT" abbreviation for a given date/time string.
 * Eastern Standard Time runs Nov–Mar; Eastern Daylight Time runs Mar–Nov.
 */
function getEasternLabel(isoString: string): string {
    try {
        const parts = new Intl.DateTimeFormat("en-US", {
            timeZone: EASTERN_TZ,
            timeZoneName: "short",
        }).formatToParts(new Date(isoString));
        const tz = parts.find((p) => p.type === "timeZoneName");
        return tz?.value ?? "ET";
    } catch {
        return "ET";
    }
}

/**
 * Format a time-only string in Eastern time with label.
 * e.g. "10:00 AM EDT"
 */
export function formatEasternTimeOnly(isoString: string): string {
    if (!isoString) return "N/A";
    try {
        const time = new Date(isoString).toLocaleString("en-US", {
            timeZone: EASTERN_TZ,
            hour: "numeric",
            minute: "2-digit",
            hour12: true,
        });
        return `${time} ${getEasternLabel(isoString)}`;
    } catch {
        return isoString;
    }
}

/**
 * Format a full date + time string in Eastern time with label.
 * e.g. "July 14, 2025 10:00 AM EDT"
 */
export function formatEasternDateTime(isoString: string): string {
    if (!isoString) return "N/A";
    try {
        const dateTime = new Date(isoString).toLocaleString("en-US", {
            timeZone: EASTERN_TZ,
            year: "numeric",
            month: "long",
            day: "numeric",
            hour: "numeric",
            minute: "2-digit",
            hour12: true,
        });
        return `${dateTime} ${getEasternLabel(isoString)}`;
    } catch {
        return isoString;
    }
}

/**
 * Format a date-only string in Eastern time (no time component).
 * Pins to Eastern timezone to avoid midnight-rollover bugs for CST/PST students.
 * e.g. "July 14, 2025"
 */
export function formatEasternDate(isoString: string): string {
    if (!isoString) return "N/A";
    try {
        return new Date(isoString).toLocaleString("en-US", {
            timeZone: EASTERN_TZ,
            year: "numeric",
            month: "long",
            day: "numeric",
        });
    } catch {
        return isoString;
    }
}

/** Alias — matches the original helper name used in existing components */
export { formatEasternTimeOnly as formatEasternTime };
