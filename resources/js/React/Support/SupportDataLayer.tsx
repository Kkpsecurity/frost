import React, { useEffect, useState } from 'react';
import axios from 'axios';

interface StudentSearchResult {
    id: number;
    name: string;
    email: string;
    phone?: string;
    status: string;
    total_orders: number;
    total_spent: number;
    last_activity: string;
}

interface SupportStats {
    overview: {
        total_students: number;
        active_students: number;
        total_courses: number;
        active_courses: number;
        total_orders: number;
        pending_orders: number;
        revenue_today: number;
        revenue_month: number;
    };
    support_metrics: {
        open_tickets: number;
        resolved_tickets_today: number;
        customer_satisfaction: string;
        average_resolution_time: string;
        pending_refunds: number;
        flagged_accounts: number;
    };
    system_health: {
        database_status: string;
        cache_status: string;
        queue_status: string;
        storage_usage: string;
        memory_usage: string;
        last_backup: string;
        uptime: string;
    };
}

const SupportDataLayer = () => {
    const [mounted, setMounted] = useState(false);
    const [searchQuery, setSearchQuery] = useState("");
    const [searchResults, setSearchResults] = useState<StudentSearchResult[]>(
        []
    );
    const [isSearching, setIsSearching] = useState(false);
    const [stats, setStats] = useState<SupportStats | null>(null);
    const [statsLoading, setStatsLoading] = useState(true);

    useEffect(() => {
        console.log("🔧 SupportDataLayer mounted successfully");
        console.log("📍 Current URL:", window.location.pathname);
        console.log(
            "📍 Current container:",
            document.getElementById("support-dashboard-container")
        );
        setMounted(true);
        loadStats();
    }, []);

    const loadStats = async () => {
        try {
            setStatsLoading(true);
            const response = await axios.get("/admin/frost-support/stats");
            if (response.data.success) {
                setStats(response.data.data);
            }
        } catch (error) {
            console.error("Failed to load stats:", error);
        } finally {
            setStatsLoading(false);
        }
    };

    const handleSearch = async (query: string) => {
        if (query.length < 2) {
            setSearchResults([]);
            return;
        }

        try {
            setIsSearching(true);
            const response = await axios.get(
                "/admin/frost-support/search-students",
                {
                    params: { query },
                }
            );

            if (response.data.success) {
                setSearchResults(response.data.data);
            }
        } catch (error) {
            console.error("Search failed:", error);
            setSearchResults([]);
        } finally {
            setIsSearching(false);
        }
    };

    const handleSearchChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = e.target.value;
        setSearchQuery(value);

        // Debounce search
        clearTimeout((window as any).searchTimeout);
        (window as any).searchTimeout = setTimeout(() => {
            handleSearch(value);
        }, 300);
    };

    if (!mounted) {
        return (
            <div className="container-fluid">
                <div className="row">
                    <div className="col-12 text-center p-4">
                        <i className="fas fa-spinner fa-spin fa-2x text-success"></i>
                        <p className="mt-2 text-success">
                            <strong>React Component Initializing...</strong>
                        </p>
                        <small className="text-muted">
                            SupportDataLayer is mounting
                        </small>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="container-fluid support-center-dashboard">
            {/* Header with Success Alert */}
            <div className="row mb-4">
                <div className="col-12">
                    <div className="alert alert-success">
                        <i className="fas fa-check-circle mr-2"></i>
                        <strong>
                            🚀 Frost Support Center Dashboard Active!
                        </strong>
                        <br />
                        <small>
                            ✅ Component mounted at:{" "}
                            {new Date().toLocaleTimeString()}
                        </small>
                        <br />
                        <small>📍 Route: {window.location.pathname}</small>
                    </div>
                </div>
            </div>

            {/* Google-style Search Box */}
            <div className="row mb-4">
                <div className="col-12">
                    <div className="card">
                        <div className="card-body">
                            <div className="d-flex justify-content-center">
                                <div
                                    style={{ width: "100%", maxWidth: "600px" }}
                                >
                                    <div className="input-group input-group-lg">
                                        <div className="input-group-prepend">
                                            <span className="input-group-text bg-white border-right-0">
                                                <i className="fas fa-search text-muted"></i>
                                            </span>
                                        </div>
                                        <input
                                            type="text"
                                            className="form-control border-left-0"
                                            placeholder="Search students by name, email, or phone..."
                                            value={searchQuery}
                                            onChange={handleSearchChange}
                                            style={{
                                                borderRadius: "0 25px 25px 0",
                                                fontSize: "16px",
                                                padding: "12px 16px",
                                            }}
                                        />
                                        {isSearching && (
                                            <div className="input-group-append">
                                                <span className="input-group-text bg-white border-left-0">
                                                    <i className="fas fa-spinner fa-spin text-primary"></i>
                                                </span>
                                            </div>
                                        )}
                                    </div>

                                    {/* Search Results */}
                                    {searchResults.length > 0 && (
                                        <div className="mt-3">
                                            <div className="card">
                                                <div className="card-header">
                                                    <h6 className="mb-0">
                                                        <i className="fas fa-users mr-2"></i>
                                                        Search Results (
                                                        {searchResults.length})
                                                    </h6>
                                                </div>
                                                <div className="card-body p-0">
                                                    <div className="table-responsive">
                                                        <table className="table table-hover mb-0">
                                                            <thead className="thead-light">
                                                                <tr>
                                                                    <th>
                                                                        Student
                                                                    </th>
                                                                    <th>
                                                                        Contact
                                                                    </th>
                                                                    <th>
                                                                        Status
                                                                    </th>
                                                                    <th>
                                                                        Orders
                                                                    </th>
                                                                    <th>
                                                                        Total
                                                                        Spent
                                                                    </th>
                                                                    <th>
                                                                        Last
                                                                        Activity
                                                                    </th>
                                                                    <th>
                                                                        Actions
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                {searchResults.map(
                                                                    (
                                                                        student
                                                                    ) => (
                                                                        <tr
                                                                            key={
                                                                                student.id
                                                                            }
                                                                        >
                                                                            <td>
                                                                                <div>
                                                                                    <strong>
                                                                                        {
                                                                                            student.name
                                                                                        }
                                                                                    </strong>
                                                                                    <br />
                                                                                    <small className="text-muted">
                                                                                        ID:{" "}
                                                                                        {
                                                                                            student.id
                                                                                        }
                                                                                    </small>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div>
                                                                                    <div>
                                                                                        {
                                                                                            student.email
                                                                                        }
                                                                                    </div>
                                                                                    {student.phone && (
                                                                                        <small className="text-muted">
                                                                                            {
                                                                                                student.phone
                                                                                            }
                                                                                        </small>
                                                                                    )}
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                    className={`badge badge-${
                                                                                        student.status ===
                                                                                        "active"
                                                                                            ? "success"
                                                                                            : "secondary"
                                                                                    }`}
                                                                                >
                                                                                    {
                                                                                        student.status
                                                                                    }
                                                                                </span>
                                                                            </td>
                                                                            <td>
                                                                                {
                                                                                    student.total_orders
                                                                                }
                                                                            </td>
                                                                            <td>
                                                                                $
                                                                                {student.total_spent.toFixed(
                                                                                    2
                                                                                )}
                                                                            </td>
                                                                            <td>
                                                                                {
                                                                                    student.last_activity
                                                                                }
                                                                            </td>
                                                                            <td>
                                                                                <button
                                                                                    className="btn btn-sm btn-primary"
                                                                                    onClick={() =>
                                                                                        window.open(
                                                                                            `/admin/students/${student.id}`,
                                                                                            "_blank"
                                                                                        )
                                                                                    }
                                                                                >
                                                                                    <i className="fas fa-eye"></i>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    )
                                                                )}
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Support Stats Dashboard */}
            <div className="row mb-4">
                <div className="col-12">
                    <h4 className="mb-3">
                        <i className="fas fa-chart-line mr-2"></i>
                        Support Dashboard
                    </h4>
                </div>
            </div>

            {statsLoading ? (
                <div className="row mb-4">
                    <div className="col-12 text-center">
                        <i className="fas fa-spinner fa-spin fa-2x text-primary"></i>
                        <p className="mt-2">Loading statistics...</p>
                    </div>
                </div>
            ) : (
                <>
                    {/* Overview Stats */}
                    <div className="row mb-4">
                        <div className="col-lg-3 col-md-6">
                            <div className="small-box bg-info">
                                <div className="inner">
                                    <h3>
                                        {stats?.overview.total_students || 0}
                                    </h3>
                                    <p>Total Students</p>
                                </div>
                                <div className="icon">
                                    <i className="fas fa-users"></i>
                                </div>
                            </div>
                        </div>

                        <div className="col-lg-3 col-md-6">
                            <div className="small-box bg-success">
                                <div className="inner">
                                    <h3>
                                        {stats?.overview.active_students || 0}
                                    </h3>
                                    <p>Active Students</p>
                                </div>
                                <div className="icon">
                                    <i className="fas fa-user-check"></i>
                                </div>
                            </div>
                        </div>

                        <div className="col-lg-3 col-md-6">
                            <div className="small-box bg-warning">
                                <div className="inner">
                                    <h3>
                                        {stats?.overview.total_courses || 0}
                                    </h3>
                                    <p>Total Courses</p>
                                </div>
                                <div className="icon">
                                    <i className="fas fa-graduation-cap"></i>
                                </div>
                            </div>
                        </div>

                        <div className="col-lg-3 col-md-6">
                            <div className="small-box bg-primary">
                                <div className="inner">
                                    <h3>
                                        {stats?.overview.pending_orders || 0}
                                    </h3>
                                    <p>Pending Orders</p>
                                </div>
                                <div className="icon">
                                    <i className="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Support Metrics */}
                    <div className="row mb-4">
                        <div className="col-lg-3 col-md-6">
                            <div className="small-box bg-danger">
                                <div className="inner">
                                    <h3>
                                        {stats?.support_metrics.open_tickets ||
                                            0}
                                    </h3>
                                    <p>Open Tickets</p>
                                </div>
                                <div className="icon">
                                    <i className="fas fa-exclamation-circle"></i>
                                </div>
                            </div>
                        </div>

                        <div className="col-lg-3 col-md-6">
                            <div className="small-box bg-success">
                                <div className="inner">
                                    <h3>
                                        {stats?.support_metrics
                                            .resolved_tickets_today || 0}
                                    </h3>
                                    <p>Resolved Today</p>
                                </div>
                                <div className="icon">
                                    <i className="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>

                        <div className="col-lg-3 col-md-6">
                            <div className="small-box bg-info">
                                <div className="inner">
                                    <h3>
                                        {stats?.support_metrics
                                            .customer_satisfaction || "N/A"}
                                    </h3>
                                    <p>Satisfaction</p>
                                </div>
                                <div className="icon">
                                    <i className="fas fa-star"></i>
                                </div>
                            </div>
                        </div>

                        <div className="col-lg-3 col-md-6">
                            <div className="small-box bg-warning">
                                <div className="inner">
                                    <h3>
                                        {stats?.support_metrics
                                            .average_resolution_time || "N/A"}
                                    </h3>
                                    <p>Avg Resolution</p>
                                </div>
                                <div className="icon">
                                    <i className="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Classroom Stats */}
                    <div className="row mb-4">
                        <div className="col-12">
                            <h4 className="mb-3">
                                <i className="fas fa-chalkboard-teacher mr-2"></i>
                                Classroom Statistics
                            </h4>
                        </div>
                    </div>

                    <div className="row mb-4">
                        <div className="col-lg-3 col-md-6">
                            <div className="small-box bg-teal">
                                <div className="inner">
                                    <h3>
                                        {stats?.overview.active_courses || 0}
                                    </h3>
                                    <p>Active Classes</p>
                                </div>
                                <div className="icon">
                                    <i className="fas fa-chalkboard"></i>
                                </div>
                            </div>
                        </div>

                        <div className="col-lg-3 col-md-6">
                            <div className="small-box bg-indigo">
                                <div className="inner">
                                    <h3>
                                        {Math.round(
                                            (stats?.overview.active_students ||
                                                0) /
                                                Math.max(
                                                    stats?.overview
                                                        .active_courses || 1,
                                                    1
                                                )
                                        )}
                                    </h3>
                                    <p>Avg Class Size</p>
                                </div>
                                <div className="icon">
                                    <i className="fas fa-users-class"></i>
                                </div>
                            </div>
                        </div>

                        <div className="col-lg-3 col-md-6">
                            <div className="small-box bg-purple">
                                <div className="inner">
                                    <h3>
                                        $
                                        {(
                                            stats?.overview.revenue_month || 0
                                        ).toLocaleString()}
                                    </h3>
                                    <p>Monthly Revenue</p>
                                </div>
                                <div className="icon">
                                    <i className="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>

                        <div className="col-lg-3 col-md-6">
                            <div className="small-box bg-pink">
                                <div className="inner">
                                    <h3>
                                        $
                                        {(
                                            stats?.overview.revenue_today || 0
                                        ).toLocaleString()}
                                    </h3>
                                    <p>Today's Revenue</p>
                                </div>
                                <div className="icon">
                                    <i className="fas fa-cash-register"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </>
            )}

            {/* System Health and Tools */}
            <div className="row">
                <div className="col-md-6">
                    <div className="card">
                        <div className="card-header">
                            <h3 className="card-title">
                                <i className="fas fa-heartbeat mr-2"></i>
                                System Health
                            </h3>
                        </div>
                        <div className="card-body">
                            {stats ? (
                                <>
                                    <div className="d-flex justify-content-between mb-2">
                                        <span>Database:</span>
                                        <span
                                            className={`badge badge-${
                                                stats.system_health
                                                    .database_status ===
                                                "healthy"
                                                    ? "success"
                                                    : "danger"
                                            }`}
                                        >
                                            {
                                                stats.system_health
                                                    .database_status
                                            }
                                        </span>
                                    </div>
                                    <div className="d-flex justify-content-between mb-2">
                                        <span>Cache:</span>
                                        <span
                                            className={`badge badge-${
                                                stats.system_health
                                                    .cache_status === "healthy"
                                                    ? "success"
                                                    : "danger"
                                            }`}
                                        >
                                            {stats.system_health.cache_status}
                                        </span>
                                    </div>
                                    <div className="d-flex justify-content-between mb-2">
                                        <span>Storage Usage:</span>
                                        <span className="badge badge-info">
                                            {stats.system_health.storage_usage}
                                        </span>
                                    </div>
                                    <div className="d-flex justify-content-between mb-2">
                                        <span>Memory Usage:</span>
                                        <span className="badge badge-warning">
                                            {stats.system_health.memory_usage}
                                        </span>
                                    </div>
                                    <div className="d-flex justify-content-between mb-2">
                                        <span>Uptime:</span>
                                        <span className="badge badge-success">
                                            {stats.system_health.uptime}
                                        </span>
                                    </div>
                                    <div className="d-flex justify-content-between">
                                        <span>Last Backup:</span>
                                        <span className="badge badge-secondary">
                                            {stats.system_health.last_backup}
                                        </span>
                                    </div>
                                </>
                            ) : (
                                <p className="text-muted">
                                    Loading system health...
                                </p>
                            )}
                        </div>
                    </div>
                </div>

                <div className="col-md-6">
                    <div className="card">
                        <div className="card-header">
                            <h3 className="card-title">
                                <i className="fas fa-tools mr-2"></i>
                                Support Tools
                            </h3>
                        </div>
                        <div className="card-body">
                            <p>
                                <strong>✅ React Component:</strong>{" "}
                                Successfully mounted and rendering
                            </p>
                            <p>
                                <strong>✅ Container:</strong> Found and
                                attached to DOM
                            </p>
                            <p>
                                <strong>✅ Route Detection:</strong>{" "}
                                {window.location.pathname}
                            </p>
                            <p>
                                <strong>✅ Time:</strong>{" "}
                                {new Date().toLocaleString()}
                            </p>
                            <hr />
                            <div className="btn-group-vertical w-100">
                                <button
                                    className="btn btn-primary mb-2"
                                    onClick={() => loadStats()}
                                >
                                    <i className="fas fa-sync mr-1"></i>
                                    Refresh Stats
                                </button>
                                <button
                                    className="btn btn-info mb-2"
                                    onClick={() =>
                                        window.open("/admin/students", "_blank")
                                    }
                                >
                                    <i className="fas fa-users mr-1"></i>
                                    Manage Students
                                </button>
                                <button
                                    className="btn btn-warning mb-2"
                                    onClick={() =>
                                        window.open("/admin/courses", "_blank")
                                    }
                                >
                                    <i className="fas fa-graduation-cap mr-1"></i>
                                    Manage Courses
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default SupportDataLayer;
