import React from "react";
import { t } from "@/i18n";
import { useStudent } from "../../context/StudentContext";
import type { LicenseHistoryItem } from "../../context/StudentContext";

interface TabLicenseHistoryProps {
    courseAuthId: number;
}

// ── helpers ──────────────────────────────────────────────────────────────────

function statusBadge(status: LicenseHistoryItem["renewal_status"]) {
    const map: Record<
        LicenseHistoryItem["renewal_status"],
        { label: string; bg: string; color: string }
    > = {
        expired: { label: "EXPIRED", bg: "#c0392b", color: "#fff" },
        expiring_soon: { label: "EXPIRING SOON", bg: "#e67e22", color: "#fff" },
        renewal_open: { label: "RENEWAL OPEN", bg: "#2980b9", color: "#fff" },
        current: { label: "CURRENT", bg: "#27ae60", color: "#fff" },
    };
    const { label, bg, color } = map[status] ?? map.current;
    return (
        <span
            style={{
                backgroundColor: bg,
                color,
                padding: "2px 8px",
                borderRadius: 4,
                fontSize: "0.7rem",
                fontWeight: 700,
                letterSpacing: "0.05em",
                whiteSpace: "nowrap",
            }}
        >
            {label}
        </span>
    );
}

function fmtDate(iso: string | null | undefined): string {
    if (!iso) return "—";
    try {
        return new Date(iso).toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
        });
    } catch {
        return iso;
    }
}

// ── component ─────────────────────────────────────────────────────────────────

const TabLicenseHistory: React.FC<TabLicenseHistoryProps> = () => {
    const student = useStudent();
    const history = student?.licenseHistory ?? [];

    const alerts = history.filter(
        (item) => item.renewal_status === "expired" || item.renewal_status === "expiring_soon",
    );

    const cardStyle: React.CSSProperties = {
        backgroundColor: "#2c3e50",
        border: "1px solid #34495e",
        borderRadius: 6,
        padding: "1.25rem",
        marginBottom: "1rem",
    };

    const thStyle: React.CSSProperties = {
        backgroundColor: "#1a252f",
        color: "#95a5a6",
        fontSize: "0.75rem",
        fontWeight: 700,
        textTransform: "uppercase",
        letterSpacing: "0.06em",
        padding: "0.6rem 1rem",
        borderBottom: "1px solid #34495e",
    };

    const tdStyle: React.CSSProperties = {
        padding: "0.7rem 1rem",
        borderBottom: "1px solid #2c3e50",
        color: "#ecf0f1",
        fontSize: "0.875rem",
        verticalAlign: "middle",
    };

    return (
        <div>
            <h5 style={{ color: "#ecf0f1", marginBottom: "1.25rem" }}>
                <i className="fas fa-id-card me-2" style={{ color: "#3498db" }}></i>
                {t("classroom.tabLicenseHistory")}
            </h5>

            {/* Alert banners for urgent statuses */}
            {alerts.map((item) => (
                <div
                    key={item.course_auth_id}
                    style={{
                        backgroundColor:
                            item.renewal_status === "expired" ? "#922b21" : "#7d4f0b",
                        border: `1px solid ${item.renewal_status === "expired" ? "#c0392b" : "#e67e22"}`,
                        borderRadius: 6,
                        padding: "0.75rem 1rem",
                        marginBottom: "0.75rem",
                        color: "#fff",
                        display: "flex",
                        alignItems: "center",
                        gap: "0.6rem",
                        fontSize: "0.875rem",
                    }}
                >
                    <i
                        className={
                            item.renewal_status === "expired"
                                ? "fas fa-times-circle"
                                : "fas fa-exclamation-triangle"
                        }
                    ></i>
                    <span>
                        <strong>{item.course_name}</strong>
                        {item.renewal_status === "expired"
                            ? ` — ${t("classroom.licenseRenewalAlert")}: expired ${fmtDate(item.expire_date)}.`
                            : ` — ${t("classroom.licenseRenewalAlert")}: expires ${fmtDate(item.expire_date)} (${item.days_until_expiry} days).`}
                    </span>
                </div>
            ))}

            {/* Main table */}
            <div style={cardStyle}>
                {history.length === 0 ? (
                    <p style={{ color: "#7f8c8d", margin: 0 }}>
                        {t("classroom.licenseHistoryEmpty")}
                    </p>
                ) : (
                    <div style={{ overflowX: "auto" }}>
                        <table style={{ width: "100%", borderCollapse: "collapse" }}>
                            <thead>
                                <tr>
                                    <th style={thStyle}>Course</th>
                                    <th style={thStyle}>Type</th>
                                    <th style={thStyle}>Completed</th>
                                    <th style={thStyle}>Expires</th>
                                    <th style={thStyle}>Status</th>
                                    <th style={thStyle}>Renewal Eligible</th>
                                </tr>
                            </thead>
                            <tbody>
                                {history.map((item) => (
                                    <tr
                                        key={item.course_auth_id}
                                        style={{
                                            backgroundColor:
                                                item.renewal_status === "expired"
                                                    ? "rgba(192,57,43,0.12)"
                                                    : item.renewal_status === "expiring_soon"
                                                        ? "rgba(230,126,34,0.10)"
                                                        : "transparent",
                                        }}
                                    >
                                        <td style={tdStyle}>
                                            {item.course_name}
                                            {!item.is_passed && (
                                                <span
                                                    style={{
                                                        marginLeft: 6,
                                                        color: "#95a5a6",
                                                        fontSize: "0.75rem",
                                                    }}
                                                >
                                                    (not passed)
                                                </span>
                                            )}
                                        </td>
                                        <td style={{ ...tdStyle, textAlign: "center" }}>
                                            {item.course_type ? (
                                                <span
                                                    style={{
                                                        backgroundColor:
                                                            item.course_type === "D"
                                                                ? "#1e8449"
                                                                : "#1a5276",
                                                        color: "#fff",
                                                        padding: "2px 8px",
                                                        borderRadius: 4,
                                                        fontSize: "0.75rem",
                                                        fontWeight: 700,
                                                    }}
                                                >
                                                    {item.course_type}
                                                </span>
                                            ) : (
                                                "—"
                                            )}
                                        </td>
                                        <td style={tdStyle}>{fmtDate(item.completed_at)}</td>
                                        <td style={tdStyle}>{fmtDate(item.expire_date)}</td>
                                        <td style={{ ...tdStyle, textAlign: "center" }}>
                                            {statusBadge(item.renewal_status)}
                                        </td>
                                        <td style={{ ...tdStyle, color: "#95a5a6" }}>
                                            {fmtDate(item.renewal_eligible_from)}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        </div>
    );
};

export default TabLicenseHistory;
