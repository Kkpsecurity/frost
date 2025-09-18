import React from "react";

interface StudentPurchaseTableProps {
    courseAuths?: any[];
    table: any;
    globalFilter: string;
    setGlobalFilter: (filter: string) => void;
    flexRender: (component: any, context: any) => React.ReactNode;
}

const StudentPurchaseTable: React.FC<StudentPurchaseTableProps> = ({
    courseAuths = [],
    table,
    globalFilter,
    setGlobalFilter,
    flexRender,
}) => {
    return (
        <div
            className="card frost-primary-bg shadow-lg border-0"
            style={{ borderRadius: "15px", overflow: "hidden" }}
        >
            <div
                className="card-header bg-gradient text-white py-4"
                style={{
                    background:
                        "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
                }}
            >
                <h5 className="card-title mb-1 fw-bold fs-4">
                    <i className="fas fa-shopping-cart me-3"></i>
                    My Course Purchases
                </h5>
                <p className="mb-0 opacity-90 small">
                    Track your learning progress and continue your courses
                </p>
            </div>
            <div className="card-body p-0">
                {courseAuths.length === 0 ? (
                    <div className="text-center py-5">
                        <i
                            className="fas fa-graduation-cap fs-1 mb-3"
                            style={{ color: "#64748b" }}
                        ></i>
                        <h4 style={{ color: "#94a3b8" }}>
                            No course authorizations found
                        </h4>
                        <p style={{ color: "#64748b" }}>
                            Contact your administrator to get enrolled in
                            courses.
                        </p>
                    </div>
                ) : (
                    <>
                        {/* Search/Filter Bar */}
                        <div
                            className="row mb-4 p-4"
                            style={{
                                background:
                                    "linear-gradient(135deg, #1e293b 0%, #334155 100%)",
                                borderRadius: "12px",
                                margin: "0 0 1rem 0",
                                boxShadow: "0 4px 16px rgba(0,0,0,0.2)",
                            }}
                        >
                            <div className="col-md-6">
                                <div className="input-group">
                                    <span
                                        className="input-group-text text-white"
                                        style={{
                                            background:
                                                "linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)",
                                            border: "none",
                                        }}
                                    >
                                        <i className="fas fa-search"></i>
                                    </span>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Search courses..."
                                        value={globalFilter ?? ""}
                                        onChange={(e) =>
                                            setGlobalFilter(e.target.value)
                                        }
                                        style={{
                                            backgroundColor: "#334155",
                                            border: "none",
                                            color: "#f1f5f9",
                                            fontSize: "0.9rem",
                                        }}
                                    />
                                </div>
                            </div>
                            <div className="col-md-6 d-flex justify-content-end align-items-center">
                                <small
                                    style={{
                                        color: "#94a3b8",
                                        fontSize: "0.8rem",
                                    }}
                                >
                                    Showing {table.getRowModel().rows.length} of{" "}
                                    {courseAuths.length} courses
                                </small>
                            </div>
                        </div>

                        {/* TanStack Table - Dark Mode */}
                        <div
                            className="table-responsive"
                            style={{
                                background:
                                    "linear-gradient(135deg, #1e293b 0%, #334155 100%)",
                                borderRadius: "12px",
                                overflow: "hidden",
                                boxShadow: "0 8px 32px rgba(0,0,0,0.3)",
                            }}
                        >
                            <table className="table table-hover mb-0 table-dark">
                                <thead
                                    style={{
                                        background:
                                            "linear-gradient(135deg, #0f172a 0%, #1e293b 100%)",
                                        borderBottom: "2px solid #3b82f6",
                                    }}
                                >
                                    {table
                                        .getHeaderGroups()
                                        .map((headerGroup) => (
                                            <tr
                                                key={headerGroup.id}
                                                className="text-white"
                                            >
                                                {headerGroup.headers.map(
                                                    (header) => (
                                                        <th
                                                            key={header.id}
                                                            className="py-3 px-3 fw-bold border-0"
                                                            style={{
                                                                fontSize:
                                                                    "0.85rem",
                                                                letterSpacing:
                                                                    "0.8px",
                                                                textTransform:
                                                                    "uppercase",
                                                                cursor: header.column.getCanSort()
                                                                    ? "pointer"
                                                                    : "default",
                                                                userSelect:
                                                                    "none",
                                                                transition:
                                                                    "all 0.2s ease",
                                                                fontWeight:
                                                                    "600",
                                                            }}
                                                            onClick={header.column.getToggleSortingHandler()}
                                                            onMouseEnter={(
                                                                e
                                                            ) => {
                                                                if (
                                                                    header.column.getCanSort()
                                                                ) {
                                                                    e.currentTarget.style.backgroundColor =
                                                                        "rgba(59, 130, 246, 0.2)";
                                                                }
                                                            }}
                                                            onMouseLeave={(
                                                                e
                                                            ) => {
                                                                if (
                                                                    header.column.getCanSort()
                                                                ) {
                                                                    e.currentTarget.style.backgroundColor =
                                                                        "transparent";
                                                                }
                                                            }}
                                                        >
                                                            <div className="d-flex align-items-center justify-content-between">
                                                                {flexRender(
                                                                    header
                                                                        .column
                                                                        .columnDef
                                                                        .header,
                                                                    header.getContext()
                                                                )}
                                                                {header.column.getCanSort() && (
                                                                    <span className="ms-2">
                                                                        {header.column.getIsSorted() ===
                                                                        "asc" ? (
                                                                            <i className="fas fa-sort-up"></i>
                                                                        ) : header.column.getIsSorted() ===
                                                                          "desc" ? (
                                                                            <i className="fas fa-sort-down"></i>
                                                                        ) : (
                                                                            <i className="fas fa-sort opacity-50"></i>
                                                                        )}
                                                                    </span>
                                                                )}
                                                            </div>
                                                        </th>
                                                    )
                                                )}
                                            </tr>
                                        ))}
                                </thead>
                                <tbody>
                                    {table
                                        .getRowModel()
                                        .rows.map((row, index) => (
                                            <tr
                                                key={row.id}
                                                className="border-0"
                                                style={{
                                                    background:
                                                        index % 2 === 0
                                                            ? "linear-gradient(135deg, #1e293b 0%, #334155 100%)"
                                                            : "linear-gradient(135deg, #334155 0%, #475569 100%)",
                                                    transition: "all 0.3s ease",
                                                    borderLeft: `3px solid ${
                                                        row.original
                                                            .completed_at
                                                            ? "#10b981"
                                                            : row.original
                                                                  .start_date
                                                            ? "#3b82f6"
                                                            : "#f59e0b"
                                                    }`,
                                                    color: "#f8fafc",
                                                }}
                                                onMouseEnter={(e) => {
                                                    e.currentTarget.style.background =
                                                        "linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)";
                                                    e.currentTarget.style.transform =
                                                        "translateX(3px)";
                                                    e.currentTarget.style.boxShadow =
                                                        "0 6px 20px rgba(59, 130, 246, 0.4)";
                                                }}
                                                onMouseLeave={(e) => {
                                                    e.currentTarget.style.background =
                                                        index % 2 === 0
                                                            ? "linear-gradient(135deg, #1e293b 0%, #334155 100%)"
                                                            : "linear-gradient(135deg, #334155 0%, #475569 100%)";
                                                    e.currentTarget.style.transform =
                                                        "translateX(0)";
                                                    e.currentTarget.style.boxShadow =
                                                        "none";
                                                }}
                                            >
                                                {row
                                                    .getVisibleCells()
                                                    .map((cell) => (
                                                        <td
                                                            key={cell.id}
                                                            className="py-2 px-3 border-0"
                                                            style={{
                                                                fontSize:
                                                                    "0.9rem",
                                                            }}
                                                        >
                                                            {flexRender(
                                                                cell.column
                                                                    .columnDef
                                                                    .cell,
                                                                cell.getContext()
                                                            )}
                                                        </td>
                                                    ))}
                                            </tr>
                                        ))}
                                </tbody>
                            </table>
                        </div>

                        {/* Table Footer with Row Count */}
                        {table.getRowModel().rows.length === 0 &&
                            globalFilter && (
                                <div className="text-center py-4">
                                    <i
                                        className="fas fa-search fs-1 mb-3"
                                        style={{ color: "#64748b" }}
                                    ></i>
                                    <h5 style={{ color: "#94a3b8" }}>
                                        No courses match your search
                                    </h5>
                                    <button
                                        className="btn btn-sm text-white"
                                        onClick={() => setGlobalFilter("")}
                                        style={{
                                            background:
                                                "linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)",
                                            border: "none",
                                            borderRadius: "8px",
                                            padding: "8px 16px",
                                        }}
                                    >
                                        Clear Search
                                    </button>
                                </div>
                            )}
                    </>
                )}
            </div>
        </div>
    );
};

export default StudentPurchaseTable;
