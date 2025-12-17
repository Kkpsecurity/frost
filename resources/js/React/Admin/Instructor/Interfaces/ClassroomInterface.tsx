import React, { useMemo, useState } from "react";

interface ClassroomInterfaceProps {
  instructorData?: any;
  classroomData?: any;
  chatData?: any;
}

/**
 * Layout-only attempt (Bootstrap + FontAwesome):
 * - Left sidebar: 280px (collapsed 60px)
 * - Center: flex
 * - Right sidebar: 300px (collapsed 60px)
 * - Bottom chat: full width
 *
 * NOTE: This is just the frame. No data wiring.
 */
const ClassroomInterface: React.FC<ClassroomInterfaceProps> = ({
  instructorData,
  classroomData,
  chatData,
}) => {
  const instUnit = instructorData?.instUnit;
  const instructor = instructorData?.instructor_user;

  const [leftCollapsed, setLeftCollapsed] = useState(false);
  const [rightCollapsed, setRightCollapsed] = useState(false);

  const studentCount = useMemo(
    () => classroomData?.student_count || 0,
    [classroomData]
  );

  if (!instUnit) {
    return (
      <div className="alert alert-warning m-4">
        <h4>No Active Classroom</h4>
        <p className="mb-0">Please start a class to enter the classroom interface.</p>
      </div>
    );
  }

  return (
    <div className="classroom-interface container-fluid p-0">
      {/* Header */}
      <div className="row g-0 mb-3 px-3">
        <div className="col-12 d-flex align-items-start justify-content-between gap-3">
          <div>
            <h2 className="mb-1">🎓 Live Classroom - {instUnit?.course_unit_name}</h2>
            <div className="text-muted">
              Instructor: {instructor?.fname} {instructor?.lname}
            </div>
          </div>

          {/* Quick toggles */}
          <div className="d-flex gap-2">
            <button
              type="button"
              className={`btn btn-sm ${leftCollapsed ? "btn-outline-secondary" : "btn-secondary"}`}
              onClick={() => setLeftCollapsed((v) => !v)}
              title={leftCollapsed ? "Expand lessons" : "Collapse lessons"}
            >
              <i className="fas fa-book" />
              <span className="ms-2 d-none d-md-inline">Lessons</span>
            </button>

            <button
              type="button"
              className={`btn btn-sm ${rightCollapsed ? "btn-outline-secondary" : "btn-secondary"}`}
              onClick={() => setRightCollapsed((v) => !v)}
              title={rightCollapsed ? "Expand students" : "Collapse students"}
            >
              <i className="fas fa-users" />
              <span className="ms-2 d-none d-md-inline">Students</span>
            </button>
          </div>
        </div>
      </div>

      {/* Main 3-panel row */}
      <div className="row g-0 mb-3">
        <div className="col-12">
          <div className="classroom-grid">
            {/* LEFT: Lessons */}
            <aside className={`panel panel-left ${leftCollapsed ? "is-collapsed" : ""}`}>
              <div className="card h-100">
                <div className="card-header bg-secondary text-white d-flex align-items-center justify-content-between">
                  <div className="d-flex align-items-center gap-2">
                    <i className="fas fa-book" />
                    {!leftCollapsed && <h6 className="mb-0">Lessons</h6>}
                  </div>

                  <button
                    type="button"
                    className="btn btn-sm btn-light"
                    onClick={() => setLeftCollapsed((v) => !v)}
                    title={leftCollapsed ? "Expand" : "Collapse"}
                  >
                    <i className={`fas ${leftCollapsed ? "fa-angle-right" : "fa-angle-left"}`} />
                  </button>
                </div>

                <div className="card-body panel-scroll">
                  {leftCollapsed ? (
                    <div className="collapsed-hint">
                      <i className="fas fa-list-check" />
                      <div className="small text-muted mt-2">Lessons</div>
                    </div>
                  ) : (
                    <div className="alert alert-info py-2 px-3 mb-0">
                      <small>Lesson progression and tracking will be displayed here.</small>
                    </div>
                  )}
                </div>
              </div>
            </aside>

            {/* CENTER: Tools */}
            <main className="panel panel-center">
              <div className="card h-100">
                <div className="card-header bg-secondary text-white d-flex align-items-center justify-content-between">
                  <div className="d-flex align-items-center gap-2">
                    <i className="fas fa-tv" />
                    <h6 className="mb-0">Teaching Tools</h6>
                  </div>

                  <div className="d-flex gap-2">
                    <button type="button" className="btn btn-sm btn-light" title="Screen share">
                      <i className="fas fa-display" />
                    </button>
                    <button type="button" className="btn btn-sm btn-light" title="Camera">
                      <i className="fas fa-video" />
                    </button>
                    <button type="button" className="btn btn-sm btn-light" title="Mic">
                      <i className="fas fa-microphone" />
                    </button>
                    <button type="button" className="btn btn-sm btn-light" title="Settings">
                      <i className="fas fa-gear" />
                    </button>
                  </div>
                </div>

                <div className="card-body">
                  <div className="stage">
                    <i className="fas fa-video fa-4x text-muted mb-3" />
                    <p className="text-muted text-center mb-0">
                      <strong>Screen Share / Video Area</strong>
                      <br />
                      <small>Teaching tools and presentation area</small>
                    </p>
                  </div>
                </div>
              </div>
            </main>

            {/* RIGHT: Students */}
            <aside className={`panel panel-right ${rightCollapsed ? "is-collapsed" : ""}`}>
              <div className="card h-100">
                <div className="card-header bg-secondary text-white d-flex align-items-center justify-content-between">
                  <div className="d-flex align-items-center gap-2">
                    <i className="fas fa-users" />
                    {!rightCollapsed && <h6 className="mb-0">Students ({studentCount})</h6>}
                  </div>

                  <button
                    type="button"
                    className="btn btn-sm btn-light"
                    onClick={() => setRightCollapsed((v) => !v)}
                    title={rightCollapsed ? "Expand" : "Collapse"}
                  >
                    <i className={`fas ${rightCollapsed ? "fa-angle-left" : "fa-angle-right"}`} />
                  </button>
                </div>

                <div className="card-body panel-scroll">
                  {rightCollapsed ? (
                    <div className="collapsed-hint">
                      <i className="fas fa-user-check" />
                      <div className="small text-muted mt-2">Roster</div>
                    </div>
                  ) : (
                    <div className="alert alert-info py-2 px-3 mb-0">
                      <small>Student list and real-time status will appear here.</small>
                    </div>
                  )}
                </div>
              </div>
            </aside>
          </div>
        </div>
      </div>

      {/* Bottom: Live Chat */}
      <div className="row g-0">
        <div className="col-12">
          <div className="card">
            <div className="card-header bg-secondary text-white d-flex align-items-center gap-2">
              <i className="fas fa-comments" />
              <h6 className="mb-0">💬 Live Chat</h6>
            </div>
            <div className="card-body" style={{ minHeight: 200 }}>
              <div className="alert alert-info py-2 px-3 mb-0">
                <small>Real-time chat messages will appear here.</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Layout CSS (inline for now; move to SCSS later) */}
      <style>{`
        .classroom-grid{
          display:grid;
          grid-template-columns: 280px 1fr 300px;
          gap: 0;
          align-items: stretch;
          min-height: 620px;
        }

        .panel{ min-width: 0; }

        .panel-left.is-collapsed{ width: 60px; }
        .panel-right.is-collapsed{ width: 60px; }

        /* When collapsed, clamp the grid columns */
        .panel-left.is-collapsed{ grid-column: 1; }
        .panel-center{ grid-column: 2; }
        .panel-right.is-collapsed{ grid-column: 3; }

        .panel-left.is-collapsed ~ .panel-center{ }

        .panel-left.is-collapsed{ }

        /* Use CSS variables for simpler width control */
        .classroom-grid{
          --leftW: ${leftCollapsed ? "60px" : "280px"};
          --rightW: ${rightCollapsed ? "60px" : "300px"};
          grid-template-columns: var(--leftW) 1fr var(--rightW);
        }

        .panel-scroll{
          max-height: 600px;
          overflow: auto;
        }

        .stage{
          min-height: 560px;
          background: #f8f9fa;
          border-radius: 8px;
          border: 1px solid rgba(0,0,0,.06);
          display:flex;
          align-items:center;
          justify-content:center;
          flex-direction:column;
          padding: 24px;
        }

        .collapsed-hint{
          height: 100%;
          min-height: 520px;
          display:flex;
          flex-direction:column;
          align-items:center;
          justify-content:center;
          text-align:center;
        }

        /* Small screens: stack panels */
        @media (max-width: 991.98px){
          .classroom-grid{
            grid-template-columns: 1fr;
          }
          .panel-left,.panel-center,.panel-right{ grid-column: auto; }
        }
      `}</style>

      {/* Debug strip (optional) */}
      <div className="row mt-3">
        <div className="col-12">
          <div className="text-muted small">
            Layout state: left={leftCollapsed ? "collapsed" : "open"}, right={
              rightCollapsed ? "collapsed" : "open"
            }
            {chatData ? " • chatData present" : ""}
          </div>
        </div>
      </div>
    </div>
  );
};

export default ClassroomInterface;
