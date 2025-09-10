import React, { useEffect, useState } from 'react';
import ClassroomDashboard from './ClassroomDashboard';
import type { DashboardData } from './types/dashboard';

const ClassroomDataLayer: React.FC = () => {
    const [dashboardData, setDashboardData] = useState<DashboardData | null>(null);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        try {
            console.log("🎓 ClassroomDataLayer: Component rendering...");

            // Get data from props div
            const propsElement = document.getElementById('props');
            if (!propsElement) {
                throw new Error('Props element not found');
            }

            const rawData = propsElement.getAttribute('data-dashboard-data');
            if (!rawData) {
                throw new Error('Dashboard data not found in props');
            }

            const data: DashboardData = JSON.parse(rawData);
            console.log("🎓 ClassroomDataLayer: Data loaded", data);

            setDashboardData(data);
        } catch (e) {
            console.error("🚨 ClassroomDataLayer: Error loading data", e);
            setError(e instanceof Error ? e.message : 'Unknown error occurred');
        }
    }, []);

    if (error) {
        return (
            <div className="alert alert-danger" role="alert">
                Error loading dashboard: {error}
            </div>
        );
    }

    if (!dashboardData) {
        return (
            <div className="d-flex justify-content-center align-items-center" style={{ minHeight: '60vh' }}>
                <div className="spinner-border text-primary" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
            </div>
        );
    }

    return <ClassroomDashboard data={dashboardData} />;
};

export default ClassroomDataLayer;
