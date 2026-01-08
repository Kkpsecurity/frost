/**
 * Support Documentation Center Component
 * Displays the documentation and help center for site usage
 */

import React from 'react';
import { useQuery } from '@tanstack/react-query';
import { queryKeys } from "../../utils/queryConfig";

interface DocCategory {
    id: number;
    title: string;
    description: string;
    icon: string;
    articlesCount: number;
    lastUpdated: string;
}

interface RecentDoc {
    id: number;
    title: string;
    category: string;
    views: number;
    lastUpdated: string;
    isPopular?: boolean;
}

const FrostSupportCenter: React.FC = () => {
    // Fetch documentation categories
    const { data: categories, isLoading: categoriesLoading } = useQuery({
        queryKey: ["docs", "categories"],
        queryFn: async (): Promise<DocCategory[]> => {
            // Mock data for now - replace with actual API call
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve([
                        {
                            id: 1,
                            title: "Getting Started",
                            description:
                                "Learn the basics of using the Frost platform",
                            icon: "fas fa-rocket",
                            articlesCount: 8,
                            lastUpdated: "2 days ago",
                        },
                        {
                            id: 2,
                            title: "Course Management",
                            description:
                                "How to create, edit, and manage courses",
                            icon: "fas fa-graduation-cap",
                            articlesCount: 12,
                            lastUpdated: "1 week ago",
                        },
                        {
                            id: 3,
                            title: "Student Management",
                            description:
                                "Managing student accounts and enrollments",
                            icon: "fas fa-users",
                            articlesCount: 15,
                            lastUpdated: "3 days ago",
                        },
                        {
                            id: 4,
                            title: "Payment & Orders",
                            description:
                                "Processing payments and managing orders",
                            icon: "fas fa-credit-card",
                            articlesCount: 10,
                            lastUpdated: "5 days ago",
                        },
                        {
                            id: 5,
                            title: "System Settings",
                            description:
                                "Configuring system settings and preferences",
                            icon: "fas fa-cogs",
                            articlesCount: 6,
                            lastUpdated: "1 week ago",
                        },
                        {
                            id: 6,
                            title: "Troubleshooting",
                            description:
                                "Common issues and how to resolve them",
                            icon: "fas fa-wrench",
                            articlesCount: 9,
                            lastUpdated: "4 days ago",
                        },
                    ]);
                }, 800);
            });
        },
    });

    // Fetch recent/popular documentation
    const { data: recentDocs, isLoading: docsLoading } = useQuery({
        queryKey: ["docs", "recent"],
        queryFn: async (): Promise<RecentDoc[]> => {
            // Mock data for now - replace with actual API call
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve([
                        {
                            id: 1,
                            title: "How to Create a New Course",
                            category: "Course Management",
                            views: 245,
                            lastUpdated: "2 days ago",
                            isPopular: true,
                        },
                        {
                            id: 2,
                            title: "Processing Student Payments",
                            category: "Payment & Orders",
                            views: 189,
                            lastUpdated: "1 day ago",
                            isPopular: true,
                        },
                        {
                            id: 3,
                            title: "Setting Up Admin Users",
                            category: "Getting Started",
                            views: 156,
                            lastUpdated: "3 days ago",
                        },
                        {
                            id: 4,
                            title: "Managing Student Enrollments",
                            category: "Student Management",
                            views: 134,
                            lastUpdated: "2 days ago",
                        },
                        {
                            id: 5,
                            title: "Configuring Email Settings",
                            category: "System Settings",
                            views: 98,
                            lastUpdated: "1 week ago",
                        },
                    ]);
                }, 600);
            });
        },
    });

    if (categoriesLoading) {
        return (
            <div
                className="d-flex justify-content-center align-items-center"
                style={{ minHeight: "400px" }}
            >
                <div className="text-center">
                    <i className="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p className="mt-2">Loading Documentation...</p>
                </div>
            </div>
        );
    }

    return (
        <div className="documentation-center">
            {/* Header */}
            <div className="row mb-4">
                <div className="col-12">
                    <h2 className="h4 mb-2">
                        <i className="fas fa-book text-primary"></i>{" "}
                        Documentation Center
                    </h2>
                    <p className="text-muted">
                        Learn how to use the Frost platform with our
                        comprehensive guides and tutorials.
                    </p>
                </div>
            </div>

            {/* Search Bar */}
            <div className="row mb-4">
                <div className="col-md-8 mx-auto">
                    <div className="input-group">
                        <input
                            type="text"
                            className="form-control form-control-lg"
                            placeholder="Search documentation..."
                        />
                        <div className="input-group-append">
                            <button
                                className="btn btn-primary btn-lg"
                                type="button"
                            >
                                <i className="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {/* Documentation Categories */}
            <div className="row mb-5">
                {categories?.map((category) => (
                    <div key={category.id} className="col-lg-4 col-md-6 mb-4">
                        <div className="card h-100 shadow-sm border-0">
                            <div className="card-body text-center">
                                <div className="mb-3">
                                    <i
                                        className={`${category.icon} fa-3x text-primary`}
                                    ></i>
                                </div>
                                <h5 className="card-title">{category.title}</h5>
                                <p className="card-text text-muted">
                                    {category.description}
                                </p>
                                <div className="row text-center mt-3">
                                    <div className="col-6">
                                        <small className="text-muted">
                                            <strong>
                                                {category.articlesCount}
                                            </strong>
                                            <br />
                                            Articles
                                        </small>
                                    </div>
                                    <div className="col-6">
                                        <small className="text-muted">
                                            Updated
                                            <br />
                                            {category.lastUpdated}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div className="card-footer bg-transparent border-0">
                                <button className="btn btn-outline-primary btn-block">
                                    Browse Articles
                                </button>
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            {/* Popular/Recent Articles */}
            <div className="row">
                <div className="col-12">
                    <h4 className="mb-3">
                        <i className="fas fa-star text-warning"></i> Popular
                        Articles
                    </h4>

                    {docsLoading ? (
                        <div className="text-center py-4">
                            <i className="fas fa-spinner fa-spin fa-lg text-primary"></i>
                            <p className="mt-2">Loading articles...</p>
                        </div>
                    ) : (
                        <div className="card">
                            <div className="card-body p-0">
                                <div className="table-responsive">
                                    <table className="table table-hover mb-0">
                                        <thead className="thead-light">
                                            <tr>
                                                <th>Article</th>
                                                <th>Category</th>
                                                <th>Views</th>
                                                <th>Updated</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {recentDocs?.map((doc) => (
                                                <tr key={doc.id}>
                                                    <td>
                                                        <div className="d-flex align-items-center">
                                                            {doc.isPopular && (
                                                                <i className="fas fa-fire text-danger mr-2"></i>
                                                            )}
                                                            <div>
                                                                <strong>
                                                                    {doc.title}
                                                                </strong>
                                                                {doc.isPopular && (
                                                                    <span className="badge badge-danger ml-2">
                                                                        Popular
                                                                    </span>
                                                                )}
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span className="badge badge-secondary">
                                                            {doc.category}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <i className="fas fa-eye text-muted mr-1"></i>
                                                        {doc.views}
                                                    </td>
                                                    <td className="text-muted">
                                                        {doc.lastUpdated}
                                                    </td>
                                                    <td>
                                                        <button className="btn btn-sm btn-outline-primary">
                                                            Read Article
                                                        </button>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>

            {/* Quick Actions */}
            <div className="row mt-5">
                <div className="col-12">
                    <h4 className="mb-3">
                        <i className="fas fa-tools text-info"></i> Quick Actions
                    </h4>
                </div>
                <div className="col-md-4 mb-3">
                    <button className="btn btn-outline-success btn-lg btn-block">
                        <i className="fas fa-plus-circle mr-2"></i>
                        Create New Article
                    </button>
                </div>
                <div className="col-md-4 mb-3">
                    <button className="btn btn-outline-info btn-lg btn-block">
                        <i className="fas fa-edit mr-2"></i>
                        Manage Categories
                    </button>
                </div>
                <div className="col-md-4 mb-3">
                    <button className="btn btn-outline-warning btn-lg btn-block">
                        <i className="fas fa-chart-bar mr-2"></i>
                        View Analytics
                    </button>
                </div>
            </div>
        </div>
    );
};

export default FrostSupportCenter;
