import React, { useMemo } from "react";
import {
    useReactTable,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    flexRender,
    createColumnHelper,
    ColumnDef,
} from "@tanstack/react-table";

interface AssignmentHistoryRecord {
    course_date_id: number;
    date: string;
    time: string;
    course_name: string;
    unit_name: string;
    unit_code: string;
    day_number: string;
    assignment_status: 'assigned' | 'unassigned' | 'completed';
    status_color: string;
    instructor: string | null;
    assistant: string | null;
    assigned_at: string | null;
    completed_at: string | null;
    duration: string;
    inst_unit_id: number | null;
}

interface AssignmentHistoryTableProps {
    data: AssignmentHistoryRecord[];
}

const columnHelper = createColumnHelper<AssignmentHistoryRecord>();

const AssignmentHistoryTable: React.FC<AssignmentHistoryTableProps> = ({ data }) => {
    const columns = useMemo<ColumnDef<AssignmentHistoryRecord, any>[]>(
        () => [
            columnHelper.accessor('date', {
                header: 'Date',
                cell: info => (
                    <div className="text-nowrap">
                        <div
                            className="fw-bold"
                            style={{ color: "var(--frost-text-color, #374151)" }}
                        >
                            {info.getValue()}
                        </div>
                        <small
                            style={{ color: "var(--frost-base-color, #6b7280)" }}
                        >
                            {info.row.original.time}
                        </small>
                    </div>
                ),
            }),
            columnHelper.accessor('course_name', {
                header: 'Course',
                cell: info => (
                    <div>
                        <div
                            className="fw-bold"
                            style={{ color: "var(--frost-text-color, #374151)" }}
                        >
                            {info.getValue()}
                        </div>
                        <small
                            style={{ color: "var(--frost-base-color, #6b7280)" }}
                        >
                            {info.row.original.unit_code}
                        </small>
                    </div>
                ),
            }),
            columnHelper.accessor('day_number', {
                header: 'Day',
                cell: info => (
                    <span
                        className="badge"
                        style={{
                            backgroundColor: "var(--frost-info-color, #17aac9)",
                            color: "white"
                        }}
                    >
                        {info.getValue()}
                    </span>
                ),
            }),
            columnHelper.accessor('assignment_status', {
                header: 'Status',
                cell: info => {
                    const status = info.getValue();
                    const color = info.row.original.status_color;
                    const bgColor = {
                        'primary': 'var(--frost-primary-color, #3b82f6)',
                        'success': 'var(--frost-success-color, #22c55e)',
                        'warning': 'var(--frost-warning-color, #f59e0b)',
                    }[color] || 'var(--frost-secondary-color, #6b7280)';

                    return (
                        <span
                            className="badge"
                            style={{
                                backgroundColor: bgColor,
                                color: "white"
                            }}
                        >
                            {status.toUpperCase()}
                        </span>
                    );
                },
                filterFn: 'includesString',
            }),
            columnHelper.accessor('instructor', {
                header: 'Instructor',
                cell: info => (
                    <div>
                        {info.getValue() ? (
                            <div>
                                <div
                                    className="fw-bold"
                                    style={{ color: "var(--frost-text-color, #374151)" }}
                                >
                                    {info.getValue()}
                                </div>
                                {info.row.original.assistant && (
                                    <small
                                        style={{ color: "var(--frost-base-color, #6b7280)" }}
                                    >
                                        Assist: {info.row.original.assistant}
                                    </small>
                                )}
                            </div>
                        ) : (
                            <span
                                style={{
                                    color: "var(--frost-base-color, #6b7280)",
                                    fontStyle: "italic"
                                }}
                            >
                                Not assigned
                            </span>
                        )}
                    </div>
                ),
            }),
            columnHelper.accessor('assigned_at', {
                header: 'Assigned',
                cell: info => (
                    <div className="text-nowrap">
                        {info.getValue() ? (
                            <small
                                style={{ color: "var(--frost-text-color, #374151)" }}
                            >
                                {info.getValue()}
                            </small>
                        ) : (
                            <span
                                style={{ color: "var(--frost-base-color, #6b7280)" }}
                            >
                                —
                            </span>
                        )}
                    </div>
                ),
            }),
            columnHelper.accessor('completed_at', {
                header: 'Completed',
                cell: info => (
                    <div className="text-nowrap">
                        {info.getValue() ? (
                            <small
                                style={{ color: "var(--frost-success-color, #22c55e)" }}
                            >
                                {info.getValue()}
                            </small>
                        ) : (
                            <span
                                style={{ color: "var(--frost-base-color, #6b7280)" }}
                            >
                                —
                            </span>
                        )}
                    </div>
                ),
            }),
            columnHelper.accessor('duration', {
                header: 'Duration',
                cell: info => (
                    <span
                        style={{ color: "var(--frost-base-color, #6b7280)" }}
                    >
                        {info.getValue()}
                    </span>
                ),
            }),
        ],
        []
    );

    const table = useReactTable({
        data,
        columns,
        getCoreRowModel: getCoreRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        getSortedRowModel: getSortedRowModel(),
        initialState: {
            pagination: {
                pageSize: 10,
            },
            sorting: [
                {
                    id: 'date',
                    desc: true,
                },
            ],
        },
    });

    const handleStatusFilter = (status: string) => {
        table.getColumn('assignment_status')?.setFilterValue(status === 'all' ? '' : status);
    };

    return (
        <div
            className="card"
            style={{
                backgroundColor: "#f3f4f6",
                border: "1px solid var(--frost-light-primary-color, #e2e8f0)",
                boxShadow: "var(--frost-shadow-sm, 0 1px 3px rgba(0, 0, 0, 0.1))",
            }}
        >
            <div
                className="card-header"
                style={{
                    backgroundColor: "var(--frost-secondary-color, #394867)",
                    color: "var(--frost-white-color, #ffffff)",
                }}
            >
                <div className="d-flex justify-content-between align-items-center">
                    <h6 className="card-title mb-0">
                        <i className="fas fa-history me-2"></i>
                        Course Assignment History
                    </h6>
                    <div className="d-flex gap-2">
                        <button
                            className="btn btn-sm btn-outline-light"
                            onClick={() => handleStatusFilter('all')}
                        >
                            All
                        </button>
                        <button
                            className="btn btn-sm btn-outline-light"
                            onClick={() => handleStatusFilter('unassigned')}
                        >
                            Unassigned
                        </button>
                        <button
                            className="btn btn-sm btn-outline-light"
                            onClick={() => handleStatusFilter('assigned')}
                        >
                            Assigned
                        </button>
                        <button
                            className="btn btn-sm btn-outline-light"
                            onClick={() => handleStatusFilter('completed')}
                        >
                            Completed
                        </button>
                    </div>
                </div>
            </div>

            <div className="card-body p-0">
                <div className="table-responsive">
                    <table
                        className="table table-hover mb-0"
                        style={{ minWidth: '800px' }}
                    >
                        <thead
                            style={{
                                backgroundColor: "var(--frost-light-bg-color, #f8fafc)",
                            }}
                        >
                            {table.getHeaderGroups().map(headerGroup => (
                                <tr key={headerGroup.id}>
                                    {headerGroup.headers.map(header => (
                                        <th
                                            key={header.id}
                                            style={{
                                                cursor: header.column.getCanSort() ? 'pointer' : 'default',
                                                userSelect: 'none',
                                                padding: '0.5rem 0.75rem',
                                                borderBottom: "1px solid var(--frost-light-primary-color, #e2e8f0)",
                                                color: "var(--frost-text-color, #374151)",
                                                fontWeight: "600",
                                            }}
                                            onClick={header.column.getToggleSortingHandler()}
                                        >
                                            <div className="d-flex align-items-center justify-content-between">
                                                <span>
                                                    {flexRender(
                                                        header.column.columnDef.header,
                                                        header.getContext()
                                                    )}
                                                </span>
                                                {header.column.getCanSort() && (
                                                    <i
                                                        className={`fas ${
                                                            header.column.getIsSorted() === 'asc'
                                                                ? 'fa-sort-up'
                                                                : header.column.getIsSorted() === 'desc'
                                                                ? 'fa-sort-down'
                                                                : 'fa-sort'
                                                        }`}
                                                        style={{
                                                            opacity: 0.6,
                                                            color: "var(--frost-text-color, #374151)",
                                                            fontSize: '0.8rem'
                                                        }}
                                                    ></i>
                                                )}
                                            </div>
                                        </th>
                                    ))}
                                </tr>
                            ))}
                        </thead>
                        <tbody>
                            {table.getRowModel().rows.map(row => (
                                <tr key={row.id}>
                                    {row.getVisibleCells().map(cell => (
                                        <td
                                            key={cell.id}
                                            style={{
                                                padding: '0.75rem',
                                                borderBottom: "1px solid var(--frost-light-primary-color, #e2e8f0)",
                                            }}
                                        >
                                            {flexRender(
                                                cell.column.columnDef.cell,
                                                cell.getContext()
                                            )}
                                        </td>
                                    ))}
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* Pagination */}
            <div
                className="card-footer"
                style={{
                    backgroundColor: "var(--frost-light-bg-color, #f8fafc)",
                    borderTop: "1px solid var(--frost-light-primary-color, #e2e8f0)",
                }}
            >
                <div className="d-flex justify-content-between align-items-center">
                    <div className="d-flex align-items-center gap-2">
                        <button
                            className="btn btn-sm btn-outline-secondary"
                            onClick={() => table.setPageIndex(0)}
                            disabled={!table.getCanPreviousPage()}
                        >
                            <i className="fas fa-angle-double-left"></i>
                        </button>
                        <button
                            className="btn btn-sm btn-outline-secondary"
                            onClick={() => table.previousPage()}
                            disabled={!table.getCanPreviousPage()}
                        >
                            <i className="fas fa-angle-left"></i>
                        </button>
                        <button
                            className="btn btn-sm btn-outline-secondary"
                            onClick={() => table.nextPage()}
                            disabled={!table.getCanNextPage()}
                        >
                            <i className="fas fa-angle-right"></i>
                        </button>
                        <button
                            className="btn btn-sm btn-outline-secondary"
                            onClick={() => table.setPageIndex(table.getPageCount() - 1)}
                            disabled={!table.getCanNextPage()}
                        >
                            <i className="fas fa-angle-double-right"></i>
                        </button>
                    </div>

                    <span className="text-muted small">
                        Page {table.getState().pagination.pageIndex + 1} of{' '}
                        {table.getPageCount()} ({data.length} total records)
                    </span>

                    <select
                        className="form-select form-select-sm"
                        style={{ width: 'auto' }}
                        value={table.getState().pagination.pageSize}
                        onChange={e => {
                            table.setPageSize(Number(e.target.value))
                        }}
                    >
                        {[10, 20, 30, 50].map(pageSize => (
                            <option key={pageSize} value={pageSize}>
                                Show {pageSize}
                            </option>
                        ))}
                    </select>
                </div>
            </div>
        </div>
    );
};

export default AssignmentHistoryTable;
