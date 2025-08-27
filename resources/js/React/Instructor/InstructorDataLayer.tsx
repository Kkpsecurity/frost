import React from "react";
import { useQuery } from "@tanstack/react-query";
import { fetchHelpers } from "./utils/instructorApi";

const InstructorDataLayer: React.FC = () => {
    const {
        data: config,
        isLoading: loadingConfig,
        error: configError,
    } = useQuery({
        queryKey: ["laravelConfig"],
        queryFn: fetchHelpers.laravelConfig,
    });

    const {
        data: validation,
        isLoading: loadingValidation,
        error: validationError,
    } = useQuery({
        queryKey: ["instructorValidation"],
        queryFn: fetchHelpers.instructorValidation,
    });

    const loading = loadingConfig || loadingValidation;

    if (loading) {
        return (
            <div className="container-fluid">
                <div className="row">
                    <div className="col-12 text-center p-4">
                        <i className="fas fa-spinner fa-spin fa-2x text-primary"></i>
                        <p className="mt-2">
                            <strong>Loading instructor data...</strong>
                        </p>
                    </div>
                </div>
            </div>
        );
    }

    if (configError || validationError) {
        return (
            <div className="alert alert-danger">
                <strong>Failed to load instructor data</strong>
                <div className="mt-2">
                    <pre style={{ whiteSpace: "pre-wrap" }}>
                        {String(configError || validationError)}
                    </pre>
                </div>
            </div>
        );
    }

    // Default view rendered from the data layer — the shape is intentionally generic
    return (
        <div className="container-fluid instructor-dashboard">
            <div className="row mb-3">
                <div className="col-12">
                    <div className="alert alert-info">
                        <strong>Instructor Data Layer</strong>
                        <div className="small text-muted">
                            Loaded default view from data layer
                        </div>
                    </div>
                </div>
            </div>

            <div className="row">
                <div className="col-md-6">
                    <div className="card">
                        <div className="card-header">Laravel Config</div>
                        <div className="card-body">
                            <pre style={{ whiteSpace: "pre-wrap" }}>
                                {JSON.stringify(config, null, 2)}
                            </pre>
                        </div>
                    </div>
                </div>

                <div className="col-md-6">
                    <div className="card">
                        <div className="card-header">Instructor Validation</div>
                        <div className="card-body">
                            <pre style={{ whiteSpace: "pre-wrap" }}>
                                {JSON.stringify(validation, null, 2)}
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default InstructorDataLayer;
