import React from 'react'

const ActiveLessonPanel = ({
    isZoomReady,
    actionLoading,
    isPaused,
    breaksRemaining,
    breaksAllowed,
    breaksTaken,
    activeLessonId,
    courseDateId,
    postLessonAction,
}: {
    isZoomReady: boolean;
    actionLoading: boolean;
    isPaused: boolean;
    breaksRemaining?: number;
    breaksAllowed?: number;
    breaksTaken?: number;
    activeLessonId: number | null;
    courseDateId: number | null;
    postLessonAction: (url: string, lessonId: number) => Promise<void>;
}) => {
    return (
        <div className="m-3">
            <button
                className="btn btn-sm btn-warning w-100"
                title="Pause class (take a break)"
                disabled={
                    !isZoomReady ||
                    actionLoading ||
                    isPaused ||
                    (breaksRemaining !==
                        undefined &&
                        breaksRemaining <= 0)
                }
                onClick={async () => {
                    console.log(
                        "🖱️ Pause button clicked",
                        {
                            activeLessonId,
                            courseDateId,
                        },
                    );
                    await postLessonAction(
                        "/admin/instructors/lessons/pause",
                        activeLessonId,
                    );
                }}
            >
                <i className="fas fa-pause me-1" />
                {actionLoading
                    ? "Pausing..."
                    : "Pause Class"}
                {breaksRemaining !==
                    undefined &&
                    breaksRemaining > 0 && (
                        <span className="ms-1">
                            ({breaksRemaining}{" "}
                            left)
                        </span>
                    )}
            </button>
            {typeof breaksAllowed ===
                "number" && (
                    <div className="mt-1">
                        <small className="text-white-50">
                            Breaks:{" "}
                            {breaksTaken ?? 0}/
                            {breaksAllowed}
                            {typeof breaksRemaining ===
                                "number"
                                ? ` (${breaksRemaining} remaining)`
                                : ""}
                        </small>
                    </div>
                )}
        </div>
    )
}

export default ActiveLessonPanel
