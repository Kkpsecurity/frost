import React from "react";

type DocumentsMap = Record<string, string>;

interface TabDocumentationProps {
    courseAuthId: number;
}

const cardStyle: React.CSSProperties = {
    backgroundColor: "#2c3e50",
    border: "1px solid #34495e",
    borderRadius: "0.5rem",
};

const mutedText: React.CSSProperties = { color: "#95a5a6" };

const TabDocumentation: React.FC<TabDocumentationProps> = ({
    courseAuthId,
}) => {
    const [isLoading, setIsLoading] = React.useState(false);
    const [error, setError] = React.useState<string | null>(null);
    const [documents, setDocuments] = React.useState<DocumentsMap>({});

    React.useEffect(() => {
        let isCancelled = false;

        const run = async () => {
            if (!courseAuthId) return;

            setIsLoading(true);
            setError(null);

            try {
                const response = await fetch(
                    `/classroom/course/documents?course_auth_id=${courseAuthId}`,
                    {
                        method: "GET",
                        headers: { Accept: "application/json" },
                    },
                );

                const payload = await response.json();

                if (!response.ok || !payload?.success) {
                    const message =
                        payload?.error ||
                        `Failed to load documents (HTTP ${response.status})`;
                    throw new Error(message);
                }

                const docs: DocumentsMap = payload?.data?.documents || {};

                if (!isCancelled) {
                    setDocuments(docs);
                }
            } catch (e: any) {
                if (!isCancelled) {
                    setError(e?.message || "Failed to load documents");
                    setDocuments({});
                }
            } finally {
                if (!isCancelled) {
                    setIsLoading(false);
                }
            }
        };

        run();

        return () => {
            isCancelled = true;
        };
    }, [courseAuthId]);

    const entries = React.useMemo(() => {
        return Object.entries(documents || {}).sort(([a], [b]) =>
            a.localeCompare(b),
        );
    }, [documents]);

    return (
        <div className="documentation-tab">
            <h3 style={{ color: "white", marginBottom: "1.5rem" }}>
                <i className="fas fa-folder-open me-2"></i>
                Course Documentation
            </h3>

            <div className="row g-3">
                <div className="col-12 col-lg-8">
                    <div className="card" style={cardStyle}>
                        <div className="card-body">
                            <h6 style={{ color: "white", fontWeight: 600 }}>
                                <i
                                    className="fas fa-file-pdf me-2"
                                    style={{ color: "#e74c3c" }}
                                ></i>
                                PDFs & Handouts
                            </h6>

                            <div className="mt-2" style={mutedText}>
                                Files shown here come from the course’s public
                                docs folder.
                            </div>

                            {isLoading && (
                                <div className="mt-3" style={mutedText}>
                                    Loading documents…
                                </div>
                            )}

                            {!isLoading && error && (
                                <div className="mt-3 text-danger">{error}</div>
                            )}

                            {!isLoading && !error && entries.length === 0 && (
                                <div className="mt-3" style={mutedText}>
                                    No documents found for this course yet.
                                </div>
                            )}

                            {!isLoading && !error && entries.length > 0 && (
                                <div className="mt-3">
                                    <div className="list-group">
                                        {entries.map(([filename, url]) => (
                                            <div
                                                key={filename}
                                                className="list-group-item d-flex align-items-center justify-content-between"
                                                style={{
                                                    backgroundColor:
                                                        "rgba(0,0,0,0.15)",
                                                    border: "1px solid rgba(255,255,255,0.08)",
                                                    color: "#ecf0f1",
                                                }}
                                            >
                                                <div
                                                    style={{
                                                        display: "flex",
                                                        alignItems: "center",
                                                        gap: "0.6rem",
                                                        minWidth: 0,
                                                    }}
                                                >
                                                    <i
                                                        className="fas fa-file-pdf"
                                                        style={{
                                                            color: "#e74c3c",
                                                        }}
                                                    ></i>
                                                    <div
                                                        style={{
                                                            overflow: "hidden",
                                                            textOverflow:
                                                                "ellipsis",
                                                            whiteSpace:
                                                                "nowrap",
                                                        }}
                                                        title={filename}
                                                    >
                                                        {filename}
                                                    </div>
                                                </div>

                                                <a
                                                    className="btn btn-sm btn-outline-info"
                                                    href={url}
                                                    target="_blank"
                                                    rel="noreferrer"
                                                >
                                                    <i className="fas fa-external-link-alt me-1"></i>
                                                    Open
                                                </a>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                <div className="col-12 col-lg-4">
                    <div className="card" style={cardStyle}>
                        <div className="card-body">
                            <h6 style={{ color: "white", fontWeight: 600 }}>
                                <i
                                    className="fas fa-circle-info me-2"
                                    style={{ color: "#3498db" }}
                                ></i>
                                Tips
                            </h6>

                            <ul className="mb-0" style={{ color: "#ecf0f1" }}>
                                <li>
                                    Open PDFs in a new tab and keep this page
                                    open.
                                </li>
                                <li>
                                    If you don’t see expected docs, the course
                                    may not have a docs folder yet.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default TabDocumentation;
