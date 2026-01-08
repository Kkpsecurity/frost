import React, { useState } from 'react';
import axios from "axios";

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    avatar: string;
}

interface StudentSearchProps {
    onStudentSelect?: (user: User) => void;
    isAdmin: boolean;
    isSysAdmin: boolean;
}

const StudentSearch: React.FC<StudentSearchProps> = ({
    onStudentSelect,
    isAdmin,
    isSysAdmin,
}) => {
    const [searchQuery, setSearchQuery] = useState("");
    const [results, setResults] = useState<User[]>([]);
    const [isSearching, setIsSearching] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const handleSearch = async (query: string) => {
        if (query.length < 2) {
            setResults([]);
            return;
        }

        setIsSearching(true);
        setError(null);

        try {
            const searchAll = isAdmin || isSysAdmin;
            const response = await axios.get(
                "/admin/api/support/search-users",
                {
                    params: {
                        query,
                        searchAll,
                    },
                }
            );

            if (response.data.success) {
                setResults(response.data.data);
            }
        } catch (err: any) {
            console.error("Search error:", err);
            setError(err.response?.data?.message || "Search failed");
            setResults([]);
        } finally {
            setIsSearching(false);
        }
    };

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = e.target.value;
        setSearchQuery(value);

        clearTimeout((window as any).searchTimeout);
        (window as any).searchTimeout = setTimeout(() => {
            handleSearch(value);
        }, 300);
    };

    const handleSelectUser = (user: User) => {
        if (onStudentSelect) {
            onStudentSelect(user);
        }
        setSearchQuery("");
        setResults([]);
    };

    return (
        <div className="student-search">
            <div className="card">
                <div className="card-header">
                    <h6 className="mb-0">
                        <i className="fas fa-search mr-2"></i>
                        Search {isAdmin || isSysAdmin ? "Users" : "Students"}
                    </h6>
                </div>
                <div className="card-body">
                    <div className="input-group mb-3">
                        <input
                            type="text"
                            className="form-control"
                            placeholder={`Search ${
                                isAdmin || isSysAdmin ? "all users" : "students"
                            } by name or email...`}
                            value={searchQuery}
                            onChange={handleInputChange}
                        />
                        {isSearching && (
                            <div className="input-group-append">
                                <span className="input-group-text">
                                    <i className="fas fa-spinner fa-spin"></i>
                                </span>
                            </div>
                        )}
                    </div>

                    {error && (
                        <div className="alert alert-danger">
                            <i className="fas fa-exclamation-triangle mr-2"></i>
                            {error}
                        </div>
                    )}

                    {!isSearching &&
                        searchQuery.length >= 2 &&
                        results.length === 0 &&
                        !error && (
                            <div className="alert alert-info">
                                <i className="fas fa-info-circle mr-2"></i>
                                No users found matching "{searchQuery}"
                            </div>
                        )}

                    {results.length > 0 && (
                        <div className="row">
                            {results.map((user) => (
                                <div key={user.id} className="col-md-3 mb-3">
                                    <div
                                        className="card h-100 cursor-pointer hover-shadow"
                                        onClick={() => handleSelectUser(user)}
                                        style={{ cursor: "pointer" }}
                                    >
                                        <div className="card-body text-center">
                                            <img
                                                src={user.avatar}
                                                alt={user.name}
                                                className="rounded-circle mb-3"
                                                style={{
                                                    width: "80px",
                                                    height: "80px",
                                                    objectFit: "cover",
                                                }}
                                            />
                                            <h6 className="card-title mb-1">
                                                {user.name}
                                            </h6>
                                            <p className="card-text text-muted small mb-2">
                                                {user.email}
                                            </p>
                                            <span
                                                className={`badge badge-${
                                                    user.role === "admin" ||
                                                    user.role === "sys-admin"
                                                        ? "danger"
                                                        : "primary"
                                                }`}
                                            >
                                                {user.role}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default StudentSearch;
