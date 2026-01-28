import React from 'react';
import TabDetails from './OfflineTabSystem/TabDetails';
import TabSelfStudy from './OfflineTabSystem/TabSelfStudy';
import TabDocumentation from './OfflineTabSystem/TabDocumentation';

const OfflineTabSystem = ({ lessons, activeTab, setActiveTab, ...props }) => {
  return (
    <>
      <div className="tabs-navigation" style={{ backgroundColor: '#2c3e50', borderBottom: '2px solid #34495e', padding: '0 1.5rem' }}>
        <div className="d-flex">
          <button className={`tab-button ${activeTab === 'details' ? 'active' : ''}`} onClick={() => setActiveTab('details')} style={{ backgroundColor: activeTab === 'details' ? '#34495e' : 'transparent', color: activeTab === 'details' ? 'white' : '#95a5a6', border: 'none', padding: '1rem 1.5rem', cursor: 'pointer', fontWeight: activeTab === 'details' ? '600' : '400', borderBottom: activeTab === 'details' ? '3px solid #3498db' : 'none', transition: 'all 0.2s' }}>
            <i className="fas fa-info-circle me-2"></i>
            Details
          </button>
          <button className={`tab-button ${activeTab === 'self-study' ? 'active' : ''}`} onClick={() => setActiveTab('self-study')} style={{ backgroundColor: activeTab === 'self-study' ? '#34495e' : 'transparent', color: activeTab === 'self-study' ? 'white' : '#95a5a6', border: 'none', padding: '1rem 1.5rem', cursor: 'pointer', fontWeight: activeTab === 'self-study' ? '600' : '400', borderBottom: activeTab === 'self-study' ? '3px solid #3498db' : 'none', transition: 'all 0.2s' }}>
            <i className="fas fa-graduation-cap me-2"></i>
            Self Study
          </button>
          <button className={`tab-button ${activeTab === 'documentation' ? 'active' : ''}`} onClick={() => setActiveTab('documentation')} style={{ backgroundColor: activeTab === 'documentation' ? '#34495e' : 'transparent', color: activeTab === 'documentation' ? 'white' : '#95a5a6', border: 'none', padding: '1rem 1.5rem', cursor: 'pointer', fontWeight: activeTab === 'documentation' ? '600' : '400', borderBottom: activeTab === 'documentation' ? '3px solid #3498db' : 'none', transition: 'all 0.2s' }}>
            <i className="fas fa-file-alt me-2"></i>
            Documentation
          </button>
        </div>
      </div>
      <div className="tab-content flex-grow-1 p-4" style={{ overflowY: 'auto' }}>
        {activeTab === 'details' && <TabDetails lessons={lessons} {...props} />}
        {activeTab === 'self-study' && <TabSelfStudy lessons={lessons} {...props} />}
        {activeTab === 'documentation' && <TabDocumentation lessons={lessons} {...props} />}
      </div>
    </>
  );
};

export default OfflineTabSystem;
                                            <div className="col-12">
                                                <div
                                                    className="card"
                                                    style={{
                                                        backgroundColor:
                                                            "#2c3e50",
                                                        border: "2px solid #3498db",
                                                        borderRadius: "0.5rem",
                                                    }}
                                                >
                                                    <div
                                                        className="card-body"
                                                        style={{
                                                            padding: "2rem",
                                                        }}
                                                    >
                                                        <h5
                                                            style={{
                                                                color: "white",
                                                                marginBottom:
                                                                    "1rem",
                                                                fontWeight:
                                                                    "600",
                                                            }}
                                                        >
                                                            <i
                                                                className="fas fa-info-circle me-2"
                                                                style={{
                                                                    color: "#3498db",
                                                                }}
                                                            ></i>
                                                            What is Self-Study
                                                            Mode?
                                                        </h5>
                                                        <p
                                                            style={{
                                                                color: "#ecf0f1",
                                                                fontSize:
                                                                    "1rem",
                                                                lineHeight:
                                                                    "1.6",
                                                                marginBottom:
                                                                    "1.5rem",
                                                            }}
                                                        >
                                                            Self-study mode
                                                            allows you to watch
                                                            recorded video
                                                            lessons
                                                            independently,
                                                            outside of live
                                                            instructor-led
                                                            classes. This
                                                            feature is designed
                                                            to help you succeed
                                                            in your coursework.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Two Main Purposes */}
                                        <div className="row mb-4">
                                            {/* Purpose 1: Make Up Failed Lessons */}
                                            <div className="col-md-6 mb-3">
                                                <div
                                                    className="card h-100"
                                                    style={{
                                                        backgroundColor:
                                                            "#27ae60",
                                                        border: "none",
                                                        borderRadius: "0.5rem",
                                                    }}
                                                >
                                                    <div
                                                        className="card-body"
                                                        style={{
                                                            padding: "1.5rem",
                                                        }}
                                                    >
                                                        <div className="d-flex align-items-center mb-3">
                                                            <div
                                                                style={{
                                                                    width: "50px",
                                                                    height: "50px",
                                                                    borderRadius:
                                                                        "50%",
                                                                    backgroundColor:
                                                                        "rgba(255,255,255,0.2)",
                                                                    display:
                                                                        "flex",
                                                                    alignItems:
                                                                        "center",
                                                                    justifyContent:
                                                                        "center",
                                                                    marginRight:
                                                                        "1rem",
                                                                }}
                                                            >
                                                                <i
                                                                    className="fas fa-redo-alt"
                                                                    style={{
                                                                        fontSize:
                                                                            "1.5rem",
                                                                        color: "white",
                                                                    }}
                                                                ></i>
                                                            </div>
                                                            <h6
                                                                style={{
                                                                    color: "white",
                                                                    margin: 0,
                                                                    fontWeight:
                                                                        "600",
                                                                    fontSize:
                                                                        "1.1rem",
                                                                }}
                                                            >
                                                                Purpose 1: Make
                                                                Up Failed
                                                                Lessons
                                                            </h6>
                                                        </div>
                                                        <p
                                                            style={{
                                                                color: "rgba(255,255,255,0.95)",
                                                                fontSize:
                                                                    "0.95rem",
                                                                lineHeight:
                                                                    "1.5",
                                                                marginBottom:
                                                                    "1rem",
                                                            }}
                                                        >
                                                            Use self-study to
                                                            review and master
                                                            content after
                                                            failing a live
                                                            lesson. This helps
                                                            you prepare before
                                                            retaking the live
                                                            class.
                                                        </p>
                                                        <div
                                                            style={{
                                                                backgroundColor:
                                                                    "rgba(255,255,255,0.15)",
                                                                padding: "1rem",
                                                                borderRadius:
                                                                    "0.375rem",
                                                                marginTop:
                                                                    "1rem",
                                                            }}
                                                        >
                                                            <div
                                                                style={{
                                                                    color: "white",
                                                                    fontWeight:
                                                                        "600",
                                                                    marginBottom:
                                                                        "0.5rem",
                                                                    display:
                                                                        "flex",
                                                                    alignItems:
                                                                        "center",
                                                                }}
                                                            >
                                                                <i className="fas fa-gift me-2"></i>
                                                                Hour Refund
                                                                Policy
                                                            </div>
                                                            <p
                                                                style={{
                                                                    color: "rgba(255,255,255,0.95)",
                                                                    fontSize:
                                                                        "0.875rem",
                                                                    marginBottom:
                                                                        "0.5rem",
                                                                    lineHeight:
                                                                        "1.5",
                                                                }}
                                                            >
                                                                ✅{" "}
                                                                <strong>
                                                                    Your hours
                                                                    are
                                                                    refunded!
                                                                </strong>
                                                            </p>
                                                            <ol
                                                                style={{
                                                                    color: "rgba(255,255,255,0.9)",
                                                                    fontSize:
                                                                        "0.85rem",
                                                                    paddingLeft:
                                                                        "1.25rem",
                                                                    marginBottom: 0,
                                                                    lineHeight:
                                                                        "1.6",
                                                                }}
                                                            >
                                                                <li>
                                                                    Fail a live
                                                                    lesson
                                                                </li>
                                                                <li>
                                                                    Complete it
                                                                    successfully
                                                                    in
                                                                    self-study
                                                                </li>
                                                                <li>
                                                                    Retake and
                                                                    pass the
                                                                    live class
                                                                </li>
                                                                <li>
                                                                    <strong>
                                                                        Result:
                                                                        Hours
                                                                        refunded
                                                                        to your
                                                                        quota!
                                                                    </strong>
                                                                </li>
                                                            </ol>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Purpose 2: Get a Head Start */}
                                            <div className="col-md-6 mb-3">
                                                <div
                                                    className="card h-100"
                                                    style={{
                                                        backgroundColor:
                                                            "#3498db",
                                                        border: "none",
                                                        borderRadius: "0.5rem",
                                                    }}
                                                >
                                                    <div
                                                        className="card-body"
                                                        style={{
                                                            padding: "1.5rem",
                                                        }}
                                                    >
                                                        <div className="d-flex align-items-center mb-3">
                                                            <div
                                                                style={{
                                                                    width: "50px",
                                                                    height: "50px",
                                                                    borderRadius:
                                                                        "50%",
                                                                    backgroundColor:
                                                                        "rgba(255,255,255,0.2)",
                                                                    display:
                                                                        "flex",
                                                                    alignItems:
                                                                        "center",
                                                                    justifyContent:
                                                                        "center",
                                                                    marginRight:
                                                                        "1rem",
                                                                }}
                                                            >
                                                                <i
                                                                    className="fas fa-rocket"
                                                                    style={{
                                                                        fontSize:
                                                                            "1.5rem",
                                                                        color: "white",
                                                                    }}
                                                                ></i>
                                                            </div>
                                                            <h6
                                                                style={{
                                                                    color: "white",
                                                                    margin: 0,
                                                                    fontWeight:
                                                                        "600",
                                                                    fontSize:
                                                                        "1.1rem",
                                                                }}
                                                            >
                                                                Purpose 2: Get a
                                                                Head Start
                                                            </h6>
                                                        </div>
                                                        <p
                                                            style={{
                                                                color: "rgba(255,255,255,0.95)",
                                                                fontSize:
                                                                    "0.95rem",
                                                                lineHeight:
                                                                    "1.5",
                                                                marginBottom:
                                                                    "1rem",
                                                            }}
                                                        >
                                                            Preview lessons
                                                            before attending the
                                                            live class. This
                                                            helps you come
                                                            prepared and get
                                                            more value from
                                                            instructor-led
                                                            sessions.
                                                        </p>
                                                        <div
                                                            style={{
                                                                backgroundColor:
                                                                    "rgba(255,255,255,0.15)",
                                                                padding: "1rem",
                                                                borderRadius:
                                                                    "0.375rem",
                                                                marginTop:
                                                                    "1rem",
                                                            }}
                                                        >
                                                            <div
                                                                style={{
                                                                    color: "white",
                                                                    fontWeight:
                                                                        "600",
                                                                    marginBottom:
                                                                        "0.5rem",
                                                                    display:
                                                                        "flex",
                                                                    alignItems:
                                                                        "center",
                                                                }}
                                                            >
                                                                <i className="fas fa-clock me-2"></i>
                                                                Hour Usage
                                                                Policy
                                                            </div>
                                                            <p
                                                                style={{
                                                                    color: "rgba(255,255,255,0.95)",
                                                                    fontSize:
                                                                        "0.875rem",
                                                                    marginBottom: 0,
                                                                    lineHeight:
                                                                        "1.5",
                                                                }}
                                                            >
                                                                ⚠️{" "}
                                                                <strong>
                                                                    Video hours
                                                                    are consumed
                                                                </strong>{" "}
                                                                (no refund)
                                                            </p>
                                                            <p
                                                                style={{
                                                                    color: "rgba(255,255,255,0.85)",
                                                                    fontSize:
                                                                        "0.85rem",
                                                                    marginTop:
                                                                        "0.5rem",
                                                                    marginBottom: 0,
                                                                    lineHeight:
                                                                        "1.5",
                                                                }}
                                                            >
                                                                Head-start
                                                                viewing uses
                                                                your quota
                                                                permanently.
                                                                Only remediation
                                                                (failed →
                                                                self-study →
                                                                passed)
                                                                qualifies for
                                                                hour refunds.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Getting Started Instructions */}
                                        <div className="row mb-4">
                                            <div className="col-12">
                                                <div
                                                    className="card"
                                                    style={{
                                                        backgroundColor:
                                                            "#34495e",
                                                        border: "none",
                                                        borderRadius: "0.5rem",
                                                    }}
                                                >
                                                    <div
                                                        className="card-body"
                                                        style={{
                                                            padding: "1.5rem",
                                                        }}
                                                    >
                                                        <h6
                                                            style={{
                                                                color: "white",
                                                                marginBottom:
                                                                    "1rem",
                                                                fontWeight:
                                                                    "600",
                                                            }}
                                                        >
                                                            <i
                                                                className="fas fa-play-circle me-2"
                                                                style={{
                                                                    color: "#3498db",
                                                                }}
                                                            ></i>
                                                            How to Get Started
                                                        </h6>
                                                        <div className="row">
                                                            <div className="col-md-4 mb-3">
                                                                <div
                                                                    style={{
                                                                        display:
                                                                            "flex",
                                                                        alignItems:
                                                                            "flex-start",
                                                                    }}
                                                                >
                                                                    <div
                                                                        style={{
                                                                            width: "30px",
                                                                            height: "30px",
                                                                            borderRadius:
                                                                                "50%",
                                                                            backgroundColor:
                                                                                "#3498db",
                                                                            display:
                                                                                "flex",
                                                                            alignItems:
                                                                                "center",
                                                                            justifyContent:
                                                                                "center",
                                                                            marginRight:
                                                                                "0.75rem",
                                                                            flexShrink: 0,
                                                                        }}
                                                                    >
                                                                        <strong
                                                                            style={{
                                                                                color: "white",
                                                                                fontSize:
                                                                                    "0.875rem",
                                                                            }}
                                                                        >
                                                                            1
                                                                        </strong>
                                                                    </div>
                                                                    <div>
                                                                        <div
                                                                            style={{
                                                                                color: "white",
                                                                                fontWeight:
                                                                                    "600",
                                                                                fontSize:
                                                                                    "0.9rem",
                                                                                marginBottom:
                                                                                    "0.25rem",
                                                                            }}
                                                                        >
                                                                            Select
                                                                            a
                                                                            Lesson
                                                                        </div>
                                                                        <div
                                                                            style={{
                                                                                color: "#95a5a6",
                                                                                fontSize:
                                                                                    "0.85rem",
                                                                            }}
                                                                        >
                                                                            Browse
                                                                            lessons
                                                                            in
                                                                            the
                                                                            sidebar
                                                                            and
                                                                            click
                                                                            the
                                                                            "Start
                                                                            Lesson"
                                                                            button
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div className="col-md-4 mb-3">
                                                                <div
                                                                    style={{
                                                                        display:
                                                                            "flex",
                                                                        alignItems:
                                                                            "flex-start",
                                                                    }}
                                                                >
                                                                    <div
                                                                        style={{
                                                                            width: "30px",
                                                                            height: "30px",
                                                                            borderRadius:
                                                                                "50%",
                                                                            backgroundColor:
                                                                                "#3498db",
                                                                            display:
                                                                                "flex",
                                                                            alignItems:
                                                                                "center",
                                                                            justifyContent:
                                                                                "center",
                                                                            marginRight:
                                                                                "0.75rem",
                                                                            flexShrink: 0,
                                                                        }}
                                                                    >
                                                                        <strong
                                                                            style={{
                                                                                color: "white",
                                                                                fontSize:
                                                                                    "0.875rem",
                                                                            }}
                                                                        >
                                                                            2
                                                                        </strong>
                                                                    </div>
                                                                    <div>
                                                                        <div
                                                                            style={{
                                                                                color: "white",
                                                                                fontWeight:
                                                                                    "600",
                                                                                fontSize:
                                                                                    "0.9rem",
                                                                                marginBottom:
                                                                                    "0.25rem",
                                                                            }}
                                                                        >
                                                                            Review
                                                                            Preview
                                                                        </div>
                                                                        <div
                                                                            style={{
                                                                                color: "#95a5a6",
                                                                                fontSize:
                                                                                    "0.85rem",
                                                                            }}
                                                                        >
                                                                            Check
                                                                            lesson
                                                                            details
                                                                            and
                                                                            your
                                                                            remaining
                                                                            video
                                                                            time
                                                                            quota
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div className="col-md-4 mb-3">
                                                                <div
                                                                    style={{
                                                                        display:
                                                                            "flex",
                                                                        alignItems:
                                                                            "flex-start",
                                                                    }}
                                                                >
                                                                    <div
                                                                        style={{
                                                                            width: "30px",
                                                                            height: "30px",
                                                                            borderRadius:
                                                                                "50%",
                                                                            backgroundColor:
                                                                                "#3498db",
                                                                            display:
                                                                                "flex",
                                                                            alignItems:
                                                                                "center",
                                                                            justifyContent:
                                                                                "center",
                                                                            marginRight:
                                                                                "0.75rem",
                                                                            flexShrink: 0,
                                                                        }}
                                                                    >
                                                                        <strong
                                                                            style={{
                                                                                color: "white",
                                                                                fontSize:
                                                                                    "0.875rem",
                                                                            }}
                                                                        >
                                                                            3
                                                                        </strong>
                                                                    </div>
                                                                    <div>
                                                                        <div
                                                                            style={{
                                                                                color: "white",
                                                                                fontWeight:
                                                                                    "600",
                                                                                fontSize:
                                                                                    "0.9rem",
                                                                                marginBottom:
                                                                                    "0.25rem",
                                                                            }}
                                                                        >
                                                                            Begin
                                                                            Learning
                                                                        </div>
                                                                        <div
                                                                            style={{
                                                                                color: "#95a5a6",
                                                                                fontSize:
                                                                                    "0.85rem",
                                                                            }}
                                                                        >
                                                                            Click
                                                                            "Begin
                                                                            Lesson"
                                                                            to
                                                                            start
                                                                            your
                                                                            video
                                                                            session
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Video Quota Reminder */}
                                        <div
                                            className="alert"
                                            style={{
                                                backgroundColor:
                                                    "rgba(241, 196, 15, 0.15)",
                                                border: "1px solid rgba(241, 196, 15, 0.3)",
                                                borderRadius: "0.5rem",
                                                padding: "1rem",
                                            }}
                                        >
                                            <div className="d-flex align-items-start">
                                                <i
                                                    className="fas fa-clock"
                                                    style={{
                                                        color: "#f1c40f",
                                                        fontSize: "1.25rem",
                                                        marginRight: "0.75rem",
                                                        marginTop: "0.125rem",
                                                    }}
                                                ></i>
                                                <div>
                                                    <div
                                                        style={{
                                                            color: "#f1c40f",
                                                            fontWeight: "600",
                                                            marginBottom:
                                                                "0.25rem",
                                                        }}
                                                    >
                                                        Your Video Time Quota
                                                    </div>
                                                    <div
                                                        style={{
                                                            color: "#ecf0f1",
                                                            fontSize: "0.9rem",
                                                        }}
                                                    >
                                                        You have a total of{" "}
                                                        <strong>
                                                            10 hours
                                                        </strong>{" "}
                                                        of video watch time.
                                                        Monitor your usage
                                                        carefully and prioritize
                                                        remediation to earn hour
                                                        refunds. Check your
                                                        remaining time in the
                                                        sidebar before starting
                                                        each lesson.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Call to Action */}
                                        <div className="text-center mt-4">
                                            <div
                                                style={{
                                                    color: "#95a5a6",
                                                    fontSize: "1.1rem",
                                                    marginBottom: "0.5rem",
                                                }}
                                            >
                                                <i className="fas fa-arrow-left me-2"></i>
                                                Select a lesson from the sidebar
                                                to begin your self-study session
                                            </div>
                                        </div>

                                        <div className="row">
                                            <div
                                                className="col-12 mb-4"
                                                style={{ display: "none" }}
                                            >
                                                <div
                                                    className="card"
                                                    style={{
                                                        backgroundColor:
                                                            "#2c3e50",
                                                        border: "none",
                                                    }}
                                                >
                                                    <div
                                                        className="card-header"
                                                        style={{
                                                            backgroundColor:
                                                                "#34495e",
                                                            borderBottom:
                                                                "1px solid rgba(255,255,255,0.1)",
                                                        }}
                                                    >
                                                        <h6
                                                            className="mb-0"
                                                            style={{
                                                                color: "white",
                                                            }}
                                                        >
                                                            <i className="fas fa-video me-2"></i>
                                                            Video Lessons
                                                        </h6>
                                                    </div>
                                                    <div className="card-body">
                                                        <p
                                                            style={{
                                                                color: "#95a5a6",
                                                            }}
                                                        >
                                                            Your recorded video
                                                            lessons will appear
                                                            here. Select a
                                                            lesson from the
                                                            sidebar to begin.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div className="col-12 mb-4">
                                                <div
                                                    className="card"
                                                    style={{
                                                        backgroundColor:
                                                            "#2c3e50",
                                                        border: "none",
                                                    }}
                                                >
                                                    <div
                                                        className="card-header"
                                                        style={{
                                                            backgroundColor:
                                                                "#34495e",
                                                            borderBottom:
                                                                "1px solid rgba(255,255,255,0.1)",
                                                        }}
                                                    >
                                                        <h6
                                                            className="mb-0"
                                                            style={{
                                                                color: "white",
                                                            }}
                                                        >
                                                            <i className="fas fa-tasks me-2"></i>
                                                            Practice Exercises
                                                        </h6>
                                                    </div>
                                                    <div className="card-body">
                                                        <p
                                                            style={{
                                                                color: "#95a5a6",
                                                            }}
                                                        >
                                                            Complete practice
                                                            exercises to
                                                            reinforce your
                                                            learning.
                                                        </p>
                                                        {/* TODO: Add exercises component */}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </>
                                )}

                                {/* Lesson Preview Screen - Shows when Start Lesson button is clicked */}
                                {viewMode === "preview" &&
                                    previewLessonId !== null &&
                                    (() => {
                                        const lesson = lessons.find(
                                            (l) => l.id === previewLessonId,
                                        );
                                        if (!lesson) return null;

                                        // Get quota from hook - use mock data as fallback
                                        const quotaTotal =
                                            quota?.total_hours || 10.0;
                                        const quotaUsed =
                                            quota?.used_hours || 0.0;
                                        const quotaRemaining =
                                            quota?.remaining_hours ||
                                            quotaTotal - quotaUsed;
                                        const lessonHours =
                                            (lesson.duration_minutes || 0) / 60;
                                        const quotaAfterLesson =
                                            quotaRemaining - lessonHours;
                                        const quotaPercentage =
                                            (quotaRemaining / quotaTotal) * 100;

                                        // Show loading state if quota is still loading
                                        if (isLoadingQuota) {
                                            return (
                                                <div className="text-center py-5">
                                                    <div
                                                        className="spinner-border text-light"
                                                        role="status"
                                                    >
                                                        <span className="visually-hidden">
                                                            Loading quota...
                                                        </span>
                                                    </div>
                                                    <p
                                                        className="mt-3"
                                                        style={{
                                                            color: "#95a5a6",
                                                        }}
                                                    >
                                                        Loading quota
                                                        information...
                                                    </p>
                                                </div>
                                            );
                                        }

                                        // Color coding for quota
                                        const getQuotaColor = (
                                            percentage: number,
                                        ) => {
                                            if (percentage >= 60)
                                                return "#2ecc71";
                                            if (percentage >= 30)
                                                return "#f39c12";
                                            return "#e74c3c";
                                        };

                                        return (
                                            <div className="lesson-preview">
                                                {/* Back Button */}
                                                <button
                                                    className="btn btn-outline-light mb-4"
                                                    onClick={() => {
                                                        setViewMode("list");
                                                        setPreviewLessonId(
                                                            null,
                                                        );
                                                    }}
                                                    style={{
                                                        border: "2px solid rgba(255,255,255,0.3)",
                                                        padding:
                                                            "0.5rem 1.5rem",
                                                    }}
                                                >
                                                    <i className="fas fa-arrow-left me-2"></i>
                                                    Back to Lesson List
                                                </button>

                                                <h4
                                                    className="mb-4"
                                                    style={{
                                                        color: "white",
                                                        fontSize: "1.75rem",
                                                        fontWeight: "600",
                                                    }}
                                                >
                                                    <i
                                                        className="fas fa-eye me-2"
                                                        style={{
                                                            color: "#3498db",
                                                        }}
                                                    ></i>
                                                    Lesson Preview
                                                </h4>

                                                {/* Quota Warning Banner - Show at top if insufficient */}
                                                {quotaAfterLesson < 0 && (
                                                    <div
                                                        className="alert mb-4"
                                                        style={{
                                                            backgroundColor:
                                                                "rgba(231, 76, 60, 0.15)",
                                                            border: "2px solid #e74c3c",
                                                            borderRadius:
                                                                "0.5rem",
                                                            padding: "1.25rem",
                                                        }}
                                                    >
                                                        <div className="d-flex align-items-center">
                                                            <i
                                                                className="fas fa-exclamation-triangle"
                                                                style={{
                                                                    color: "#e74c3c",
                                                                    fontSize:
                                                                        "2rem",
                                                                    marginRight:
                                                                        "1rem",
                                                                }}
                                                            ></i>
                                                            <div>
                                                                <h6
                                                                    style={{
                                                                        color: "#e74c3c",
                                                                        fontWeight:
                                                                            "600",
                                                                        marginBottom:
                                                                            "0.25rem",
                                                                    }}
                                                                >
                                                                    Insufficient
                                                                    Video Quota
                                                                </h6>
                                                                <p
                                                                    style={{
                                                                        color: "#ecf0f1",
                                                                        marginBottom: 0,
                                                                        fontSize:
                                                                            "0.95rem",
                                                                    }}
                                                                >
                                                                    You need{" "}
                                                                    <strong>
                                                                        {Math.abs(
                                                                            quotaAfterLesson,
                                                                        ).toFixed(
                                                                            2,
                                                                        )}{" "}
                                                                        more
                                                                        hours
                                                                    </strong>{" "}
                                                                    to complete
                                                                    this lesson.
                                                                    Consider
                                                                    completing
                                                                    remediation
                                                                    lessons to
                                                                    earn quota
                                                                    refunds.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                )}

                                                {/* Main Content Row */}
                                                <div className="row g-4">
                                                    {/* Left Column - Lesson Details */}
                                                    <div className="col-md-8">
                                                        {/* Lesson Header Card */}
                                                        <div
                                                            className="card mb-4"
                                                            style={{
                                                                backgroundColor:
                                                                    "#2c3e50",
                                                                border: "none",
                                                                borderRadius:
                                                                    "0.5rem",
                                                                boxShadow:
                                                                    "0 4px 6px rgba(0,0,0,0.2)",
                                                            }}
                                                        >
                                                            <div
                                                                className="card-header"
                                                                style={{
                                                                    backgroundColor:
                                                                        "#34495e",
                                                                    borderBottom:
                                                                        "2px solid rgba(52, 152, 219, 0.3)",
                                                                    borderRadius:
                                                                        "0.5rem 0.5rem 0 0",
                                                                    padding:
                                                                        "1.25rem",
                                                                }}
                                                            >
                                                                <h5
                                                                    className="mb-0"
                                                                    style={{
                                                                        color: "white",
                                                                        fontWeight:
                                                                            "600",
                                                                        fontSize:
                                                                            "1.25rem",
                                                                    }}
                                                                >
                                                                    <i
                                                                        className="fas fa-book me-2"
                                                                        style={{
                                                                            color: "#3498db",
                                                                        }}
                                                                    ></i>
                                                                    {
                                                                        lesson.title
                                                                    }
                                                                </h5>
                                                            </div>
                                                            <div
                                                                className="card-body"
                                                                style={{
                                                                    padding:
                                                                        "1.5rem",
                                                                }}
                                                            >
                                                                {/* Description */}
                                                                <div className="mb-4">
                                                                    <h6
                                                                        style={{
                                                                            color: "#95a5a6",
                                                                            fontSize:
                                                                                "0.75rem",
                                                                            fontWeight:
                                                                                "600",
                                                                            marginBottom:
                                                                                "0.75rem",
                                                                            letterSpacing:
                                                                                "0.05em",
                                                                            textTransform:
                                                                                "uppercase",
                                                                        }}
                                                                    >
                                                                        <i className="fas fa-align-left me-2"></i>
                                                                        Description
                                                                    </h6>
                                                                    <p
                                                                        style={{
                                                                            color: "#ecf0f1",
                                                                            fontSize:
                                                                                "1rem",
                                                                            lineHeight:
                                                                                "1.7",
                                                                            marginBottom: 0,
                                                                        }}
                                                                    >
                                                                        {lesson.description ||
                                                                            "This lesson covers important concepts and skills that will help you progress in your coursework."}
                                                                    </p>
                                                                </div>

                                                                {/* Lesson Stats Grid */}
                                                                <div className="row g-3">
                                                                    <div className="col-md-4">
                                                                        <div
                                                                            style={{
                                                                                backgroundColor:
                                                                                    "#34495e",
                                                                                padding:
                                                                                    "1rem",
                                                                                borderRadius:
                                                                                    "0.375rem",
                                                                                border: "1px solid rgba(255,255,255,0.1)",
                                                                                textAlign:
                                                                                    "center",
                                                                            }}
                                                                        >
                                                                            <div
                                                                                style={{
                                                                                    color: "#3498db",
                                                                                    fontSize:
                                                                                        "1.75rem",
                                                                                    marginBottom:
                                                                                        "0.5rem",
                                                                                }}
                                                                            >
                                                                                <i className="far fa-clock"></i>
                                                                            </div>
                                                                            <div
                                                                                style={{
                                                                                    color: "white",
                                                                                    fontSize:
                                                                                        "1.25rem",
                                                                                    fontWeight:
                                                                                        "600",
                                                                                    marginBottom:
                                                                                        "0.25rem",
                                                                                }}
                                                                            >
                                                                                {
                                                                                    lesson.duration_minutes
                                                                                }{" "}
                                                                                min
                                                                            </div>
                                                                            <div
                                                                                style={{
                                                                                    color: "#95a5a6",
                                                                                    fontSize:
                                                                                        "0.8rem",
                                                                                }}
                                                                            >
                                                                                Duration
                                                                                (
                                                                                {lessonHours.toFixed(
                                                                                    2,
                                                                                )}{" "}
                                                                                hours)
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div className="col-md-4">
                                                                        <div
                                                                            style={{
                                                                                backgroundColor:
                                                                                    "#34495e",
                                                                                padding:
                                                                                    "1rem",
                                                                                borderRadius:
                                                                                    "0.375rem",
                                                                                border: "1px solid rgba(255,255,255,0.1)",
                                                                                textAlign:
                                                                                    "center",
                                                                            }}
                                                                        >
                                                                            <div
                                                                                style={{
                                                                                    color: lesson.is_completed
                                                                                        ? "#2ecc71"
                                                                                        : "#f39c12",
                                                                                    fontSize:
                                                                                        "1.75rem",
                                                                                    marginBottom:
                                                                                        "0.5rem",
                                                                                }}
                                                                            >
                                                                                <i
                                                                                    className={
                                                                                        lesson.is_completed
                                                                                            ? "fas fa-check-circle"
                                                                                            : "fas fa-circle-notch"
                                                                                    }
                                                                                ></i>
                                                                            </div>
                                                                            <div
                                                                                style={{
                                                                                    color: "white",
                                                                                    fontSize:
                                                                                        "1.25rem",
                                                                                    fontWeight:
                                                                                        "600",
                                                                                    marginBottom:
                                                                                        "0.25rem",
                                                                                }}
                                                                            >
                                                                                {lesson.is_completed
                                                                                    ? "Completed"
                                                                                    : "Not Started"}
                                                                            </div>
                                                                            <div
                                                                                style={{
                                                                                    color: "#95a5a6",
                                                                                    fontSize:
                                                                                        "0.8rem",
                                                                                }}
                                                                            >
                                                                                Status
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div className="col-md-4">
                                                                        <div
                                                                            style={{
                                                                                backgroundColor:
                                                                                    "#34495e",
                                                                                padding:
                                                                                    "1rem",
                                                                                borderRadius:
                                                                                    "0.375rem",
                                                                                border: "1px solid rgba(255,255,255,0.1)",
                                                                                textAlign:
                                                                                    "center",
                                                                            }}
                                                                        >
                                                                            <div
                                                                                style={{
                                                                                    color: "#9b59b6",
                                                                                    fontSize:
                                                                                        "1.75rem",
                                                                                    marginBottom:
                                                                                        "0.5rem",
                                                                                }}
                                                                            >
                                                                                <i className="fas fa-video"></i>
                                                                            </div>
                                                                            <div
                                                                                style={{
                                                                                    color: "white",
                                                                                    fontSize:
                                                                                        "1.25rem",
                                                                                    fontWeight:
                                                                                        "600",
                                                                                    marginBottom:
                                                                                        "0.25rem",
                                                                                }}
                                                                            >
                                                                                Video
                                                                            </div>
                                                                            <div
                                                                                style={{
                                                                                    color: "#95a5a6",
                                                                                    fontSize:
                                                                                        "0.8rem",
                                                                                }}
                                                                            >
                                                                                Format
                                                                                Type
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {/* What You'll Learn */}
                                                        <div
                                                            className="card mb-4"
                                                            style={{
                                                                backgroundColor:
                                                                    "#2c3e50",
                                                                border: "none",
                                                                borderRadius:
                                                                    "0.5rem",
                                                                boxShadow:
                                                                    "0 4px 6px rgba(0,0,0,0.2)",
                                                            }}
                                                        >
                                                            <div
                                                                className="card-header"
                                                                style={{
                                                                    backgroundColor:
                                                                        "#34495e",
                                                                    borderBottom:
                                                                        "1px solid rgba(255,255,255,0.1)",
                                                                    borderRadius:
                                                                        "0.5rem 0.5rem 0 0",
                                                                    padding:
                                                                        "1rem",
                                                                }}
                                                            >
                                                                <h6
                                                                    className="mb-0"
                                                                    style={{
                                                                        color: "white",
                                                                        fontWeight:
                                                                            "600",
                                                                    }}
                                                                >
                                                                    <i
                                                                        className="fas fa-graduation-cap me-2"
                                                                        style={{
                                                                            color: "#2ecc71",
                                                                        }}
                                                                    ></i>
                                                                    What You'll
                                                                    Learn
                                                                </h6>
                                                            </div>
                                                            <div
                                                                className="card-body"
                                                                style={{
                                                                    padding:
                                                                        "1.25rem",
                                                                }}
                                                            >
                                                                <ul
                                                                    style={{
                                                                        color: "#ecf0f1",
                                                                        fontSize:
                                                                            "0.95rem",
                                                                        lineHeight:
                                                                            "1.8",
                                                                        paddingLeft:
                                                                            "1.5rem",
                                                                        marginBottom: 0,
                                                                    }}
                                                                >
                                                                    <li className="mb-2">
                                                                        <strong>
                                                                            Core
                                                                            Concepts:
                                                                        </strong>{" "}
                                                                        Understand
                                                                        fundamental
                                                                        principles
                                                                        and key
                                                                        terminology
                                                                    </li>
                                                                    <li className="mb-2">
                                                                        <strong>
                                                                            Practical
                                                                            Skills:
                                                                        </strong>{" "}
                                                                        Apply
                                                                        knowledge
                                                                        through
                                                                        real-world
                                                                        examples
                                                                        and
                                                                        scenarios
                                                                    </li>
                                                                    <li className="mb-2">
                                                                        <strong>
                                                                            Best
                                                                            Practices:
                                                                        </strong>{" "}
                                                                        Learn
                                                                        industry-standard
                                                                        approaches
                                                                        and
                                                                        techniques
                                                                    </li>
                                                                    <li className="mb-2">
                                                                        <strong>
                                                                            Assessment
                                                                            Prep:
                                                                        </strong>{" "}
                                                                        Prepare
                                                                        for
                                                                        quizzes
                                                                        and
                                                                        evaluations
                                                                        on this
                                                                        material
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        {/* Prerequisites & Requirements */}
                                                        <div
                                                            className="card"
                                                            style={{
                                                                backgroundColor:
                                                                    "#2c3e50",
                                                                border: "none",
                                                                borderRadius:
                                                                    "0.5rem",
                                                                boxShadow:
                                                                    "0 4px 6px rgba(0,0,0,0.2)",
                                                            }}
                                                        >
                                                            <div
                                                                className="card-header"
                                                                style={{
                                                                    backgroundColor:
                                                                        "#34495e",
                                                                    borderBottom:
                                                                        "1px solid rgba(255,255,255,0.1)",
                                                                    borderRadius:
                                                                        "0.5rem 0.5rem 0 0",
                                                                    padding:
                                                                        "1rem",
                                                                }}
                                                            >
                                                                <h6
                                                                    className="mb-0"
                                                                    style={{
                                                                        color: "white",
                                                                        fontWeight:
                                                                            "600",
                                                                    }}
                                                                >
                                                                    <i
                                                                        className="fas fa-list-check me-2"
                                                                        style={{
                                                                            color: "#f39c12",
                                                                        }}
                                                                    ></i>
                                                                    Prerequisites
                                                                    &
                                                                    Requirements
                                                                </h6>
                                                            </div>
                                                            <div
                                                                className="card-body"
                                                                style={{
                                                                    padding:
                                                                        "1.25rem",
                                                                }}
                                                            >
                                                                <div className="mb-3">
                                                                    <div className="d-flex align-items-start mb-2">
                                                                        <i
                                                                            className="fas fa-check-circle me-2 mt-1"
                                                                            style={{
                                                                                color: "#2ecc71",
                                                                                fontSize:
                                                                                    "1rem",
                                                                            }}
                                                                        ></i>
                                                                        <span
                                                                            style={{
                                                                                color: "#ecf0f1",
                                                                                fontSize:
                                                                                    "0.95rem",
                                                                            }}
                                                                        >
                                                                            No
                                                                            prior
                                                                            experience
                                                                            required
                                                                            -
                                                                            suitable
                                                                            for
                                                                            all
                                                                            skill
                                                                            levels
                                                                        </span>
                                                                    </div>
                                                                    <div className="d-flex align-items-start mb-2">
                                                                        <i
                                                                            className="fas fa-check-circle me-2 mt-1"
                                                                            style={{
                                                                                color: "#2ecc71",
                                                                                fontSize:
                                                                                    "1rem",
                                                                            }}
                                                                        ></i>
                                                                        <span
                                                                            style={{
                                                                                color: "#ecf0f1",
                                                                                fontSize:
                                                                                    "0.95rem",
                                                                            }}
                                                                        >
                                                                            Stable
                                                                            internet
                                                                            connection
                                                                            for
                                                                            video
                                                                            streaming
                                                                        </span>
                                                                    </div>
                                                                    <div className="d-flex align-items-start mb-2">
                                                                        <i
                                                                            className="fas fa-check-circle me-2 mt-1"
                                                                            style={{
                                                                                color: "#2ecc71",
                                                                                fontSize:
                                                                                    "1rem",
                                                                            }}
                                                                        ></i>
                                                                        <span
                                                                            style={{
                                                                                color: "#ecf0f1",
                                                                                fontSize:
                                                                                    "0.95rem",
                                                                            }}
                                                                        >
                                                                            Sufficient
                                                                            video
                                                                            quota
                                                                            (
                                                                            {lessonHours.toFixed(
                                                                                2,
                                                                            )}{" "}
                                                                            hours
                                                                            required)
                                                                        </span>
                                                                    </div>
                                                                    <div className="d-flex align-items-start">
                                                                        <i
                                                                            className="fas fa-check-circle me-2 mt-1"
                                                                            style={{
                                                                                color: "#2ecc71",
                                                                                fontSize:
                                                                                    "1rem",
                                                                            }}
                                                                        ></i>
                                                                        <span
                                                                            style={{
                                                                                color: "#ecf0f1",
                                                                                fontSize:
                                                                                    "0.95rem",
                                                                            }}
                                                                        >
                                                                            Quiet
                                                                            environment
                                                                            for
                                                                            focused
                                                                            learning
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {/* Right Column - Quota & Actions */}
                                                    <div className="col-md-4">
                                                        {/* Quota Status Card */}
                                                        <div
                                                            className="card mb-4"
                                                            style={{
                                                                backgroundColor:
                                                                    "#34495e",
                                                                border:
                                                                    "3px solid " +
                                                                    getQuotaColor(
                                                                        quotaPercentage,
                                                                    ),
                                                                borderRadius:
                                                                    "0.5rem",
                                                                boxShadow:
                                                                    "0 4px 6px rgba(0,0,0,0.2)",
                                                            }}
                                                        >
                                                            <div
                                                                className="card-header"
                                                                style={{
                                                                    backgroundColor:
                                                                        "#2c3e50",
                                                                    borderBottom:
                                                                        "2px solid " +
                                                                        getQuotaColor(
                                                                            quotaPercentage,
                                                                        ),
                                                                    borderRadius:
                                                                        "0.5rem 0.5rem 0 0",
                                                                    padding:
                                                                        "1rem",
                                                                }}
                                                            >
                                                                <h6
                                                                    className="mb-0"
                                                                    style={{
                                                                        color: "white",
                                                                        fontWeight:
                                                                            "600",
                                                                    }}
                                                                >
                                                                    <i
                                                                        className="fas fa-hourglass-half me-2"
                                                                        style={{
                                                                            color: getQuotaColor(
                                                                                quotaPercentage,
                                                                            ),
                                                                        }}
                                                                    ></i>
                                                                    Video Quota
                                                                </h6>
                                                            </div>
                                                            <div
                                                                className="card-body"
                                                                style={{
                                                                    padding:
                                                                        "1.25rem",
                                                                }}
                                                            >
                                                                {/* Current Quota */}
                                                                <div className="mb-3">
                                                                    <div className="d-flex justify-content-between mb-2">
                                                                        <span
                                                                            style={{
                                                                                color: "#95a5a6",
                                                                                fontSize:
                                                                                    "0.85rem",
                                                                            }}
                                                                        >
                                                                            Current
                                                                            Remaining
                                                                        </span>
                                                                        <span
                                                                            style={{
                                                                                color: "white",
                                                                                fontWeight:
                                                                                    "600",
                                                                                fontSize:
                                                                                    "1rem",
                                                                            }}
                                                                        >
                                                                            {quotaRemaining.toFixed(
                                                                                1,
                                                                            )}
                                                                            h
                                                                        </span>
                                                                    </div>
                                                                    <div
                                                                        style={{
                                                                            width: "100%",
                                                                            height: "12px",
                                                                            backgroundColor:
                                                                                "#1e293b",
                                                                            borderRadius:
                                                                                "6px",
                                                                            overflow:
                                                                                "hidden",
                                                                            border: "1px solid rgba(255,255,255,0.2)",
                                                                        }}
                                                                    >
                                                                        <div
                                                                            style={{
                                                                                width: `${quotaPercentage}%`,
                                                                                height: "100%",
                                                                                backgroundColor:
                                                                                    getQuotaColor(
                                                                                        quotaPercentage,
                                                                                    ),
                                                                                transition:
                                                                                    "width 0.3s ease",
                                                                            }}
                                                                        ></div>
                                                                    </div>
                                                                    <div className="text-center mt-2">
                                                                        <small
                                                                            style={{
                                                                                color: "#95a5a6",
                                                                                fontSize:
                                                                                    "0.75rem",
                                                                            }}
                                                                        >
                                                                            {quotaRemaining.toFixed(
                                                                                1,
                                                                            )}{" "}
                                                                            of{" "}
                                                                            {quotaTotal.toFixed(
                                                                                1,
                                                                            )}{" "}
                                                                            hours
                                                                            available
                                                                        </small>
                                                                    </div>
                                                                </div>

                                                                <hr
                                                                    style={{
                                                                        borderColor:
                                                                            "rgba(255,255,255,0.1)",
                                                                        margin: "1rem 0",
                                                                    }}
                                                                />

                                                                {/* Quota Breakdown */}
                                                                <div className="mb-2">
                                                                    <div className="d-flex justify-content-between mb-2">
                                                                        <span
                                                                            style={{
                                                                                color: "#95a5a6",
                                                                                fontSize:
                                                                                    "0.85rem",
                                                                            }}
                                                                        >
                                                                            <i
                                                                                className="fas fa-video me-1"
                                                                                style={{
                                                                                    color: "#3498db",
                                                                                }}
                                                                            ></i>
                                                                            This
                                                                            Lesson
                                                                        </span>
                                                                        <span
                                                                            style={{
                                                                                color: "#3498db",
                                                                                fontWeight:
                                                                                    "600",
                                                                                fontSize:
                                                                                    "0.95rem",
                                                                            }}
                                                                        >
                                                                            {lessonHours.toFixed(
                                                                                2,
                                                                            )}
                                                                            h
                                                                        </span>
                                                                    </div>
                                                                    <div className="d-flex justify-content-between mb-2">
                                                                        <span
                                                                            style={{
                                                                                color: "#95a5a6",
                                                                                fontSize:
                                                                                    "0.85rem",
                                                                            }}
                                                                        >
                                                                            <i
                                                                                className="fas fa-minus-circle me-1"
                                                                                style={{
                                                                                    color: "#e74c3c",
                                                                                }}
                                                                            ></i>
                                                                            After
                                                                            Completion
                                                                        </span>
                                                                        <span
                                                                            style={{
                                                                                color:
                                                                                    quotaAfterLesson >=
                                                                                    0
                                                                                        ? "#2ecc71"
                                                                                        : "#e74c3c",
                                                                                fontWeight:
                                                                                    "600",
                                                                                fontSize:
                                                                                    "0.95rem",
                                                                            }}
                                                                        >
                                                                            {quotaAfterLesson.toFixed(
                                                                                2,
                                                                            )}
                                                                            h
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                                {/* Quota Status Message */}
                                                                <div
                                                                    className="mt-3 p-2"
                                                                    style={{
                                                                        backgroundColor:
                                                                            quotaAfterLesson <
                                                                            0
                                                                                ? "rgba(231, 76, 60, 0.15)"
                                                                                : quotaPercentage <
                                                                                    30
                                                                                  ? "rgba(243, 156, 18, 0.15)"
                                                                                  : "rgba(46, 204, 113, 0.15)",
                                                                        borderRadius:
                                                                            "0.375rem",
                                                                        textAlign:
                                                                            "center",
                                                                        border:
                                                                            "1px solid " +
                                                                            (quotaAfterLesson <
                                                                            0
                                                                                ? "#e74c3c"
                                                                                : quotaPercentage <
                                                                                    30
                                                                                  ? "#f39c12"
                                                                                  : "#2ecc71"),
                                                                    }}
                                                                >
                                                                    <i
                                                                        className={
                                                                            quotaAfterLesson <
                                                                            0
                                                                                ? "fas fa-exclamation-triangle"
                                                                                : quotaPercentage <
                                                                                    30
                                                                                  ? "fas fa-exclamation-circle"
                                                                                  : "fas fa-check-circle"
                                                                        }
                                                                        style={{
                                                                            color:
                                                                                quotaAfterLesson <
                                                                                0
                                                                                    ? "#e74c3c"
                                                                                    : quotaPercentage <
                                                                                        30
                                                                                      ? "#f39c12"
                                                                                      : "#2ecc71",
                                                                            fontSize:
                                                                                "1.5rem",
                                                                            display:
                                                                                "block",
                                                                            marginBottom:
                                                                                "0.5rem",
                                                                        }}
                                                                    ></i>
                                                                    <div
                                                                        style={{
                                                                            color:
                                                                                quotaAfterLesson <
                                                                                0
                                                                                    ? "#e74c3c"
                                                                                    : quotaPercentage <
                                                                                        30
                                                                                      ? "#f39c12"
                                                                                      : "#2ecc71",
                                                                            fontWeight:
                                                                                "600",
                                                                            fontSize:
                                                                                "0.85rem",
                                                                        }}
                                                                    >
                                                                        {quotaAfterLesson <
                                                                        0
                                                                            ? "Insufficient Quota"
                                                                            : quotaPercentage <
                                                                                30
                                                                              ? "Low Quota"
                                                                              : "Quota Available"}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {/* Action Buttons */}
                                                        <div
                                                            className="card"
                                                            style={{
                                                                backgroundColor:
                                                                    "#2c3e50",
                                                                border: "none",
                                                                borderRadius:
                                                                    "0.5rem",
                                                                boxShadow:
                                                                    "0 4px 6px rgba(0,0,0,0.2)",
                                                            }}
                                                        >
                                                            <div
                                                                className="card-body"
                                                                style={{
                                                                    padding:
                                                                        "1.25rem",
                                                                }}
                                                            >
                                                                <button
                                                                    className="btn btn-lg w-100 mb-3"
                                                                    disabled={
                                                                        quotaAfterLesson <
                                                                        0
                                                                    }
                                                                    onClick={async () => {
                                                                        console.log(
                                                                            "Begin lesson:",
                                                                            lesson.id,
                                                                            lesson.title,
                                                                        );

                                                                        // Convert duration_minutes to seconds
                                                                        const videoDurationSeconds =
                                                                            lesson.duration_minutes *
                                                                            60;

                                                                        // Start session via hook
                                                                        const result =
                                                                            await startSession(
                                                                                lesson.id,
                                                                                courseAuthId,
                                                                                videoDurationSeconds,
                                                                                lesson.title,
                                                                            );

                                                                        if (
                                                                            result.success
                                                                        ) {
                                                                            // Session started - go to player
                                                                            setViewMode(
                                                                                "player",
                                                                            );
                                                                        } else {
                                                                            // Show error
                                                                            alert(
                                                                                `Failed to start session: ${result.error}`,
                                                                            );
                                                                        }
                                                                    }}
                                                                    style={{
                                                                        backgroundColor:
                                                                            quotaAfterLesson <
                                                                            0
                                                                                ? "#7f8c8d"
                                                                                : "#2ecc71",
                                                                        color: "white",
                                                                        border: "none",
                                                                        padding:
                                                                            "0.875rem",
                                                                        fontSize:
                                                                            "1.1rem",
                                                                        fontWeight:
                                                                            "600",
                                                                        borderRadius:
                                                                            "0.375rem",
                                                                        cursor:
                                                                            quotaAfterLesson <
                                                                            0
                                                                                ? "not-allowed"
                                                                                : "pointer",
                                                                        opacity:
                                                                            quotaAfterLesson <
                                                                            0
                                                                                ? 0.6
                                                                                : 1,
                                                                        transition:
                                                                            "all 0.2s",
                                                                    }}
                                                                    onMouseEnter={(
                                                                        e,
                                                                    ) => {
                                                                        if (
                                                                            quotaAfterLesson >=
                                                                            0
                                                                        ) {
                                                                            e.currentTarget.style.backgroundColor =
                                                                                "#27ae60";
                                                                            e.currentTarget.style.transform =
                                                                                "translateY(-2px)";
                                                                            e.currentTarget.style.boxShadow =
                                                                                "0 6px 12px rgba(46,204,113,0.4)";
                                                                        }
                                                                    }}
                                                                    onMouseLeave={(
                                                                        e,
                                                                    ) => {
                                                                        if (
                                                                            quotaAfterLesson >=
                                                                            0
                                                                        ) {
                                                                            e.currentTarget.style.backgroundColor =
                                                                                "#2ecc71";
                                                                            e.currentTarget.style.transform =
                                                                                "translateY(0)";
                                                                            e.currentTarget.style.boxShadow =
                                                                                "none";
                                                                        }
                                                                    }}
                                                                >
                                                                    <i className="fas fa-play-circle me-2"></i>
                                                                    Begin Lesson
                                                                </button>

                                                                <button
                                                                    className="btn btn-lg w-100"
                                                                    onClick={() => {
                                                                        setViewMode(
                                                                            "list",
                                                                        );
                                                                        setPreviewLessonId(
                                                                            null,
                                                                        );
                                                                    }}
                                                                    style={{
                                                                        backgroundColor:
                                                                            "transparent",
                                                                        color: "white",
                                                                        border: "2px solid rgba(255,255,255,0.3)",
                                                                        padding:
                                                                            "0.875rem",
                                                                        fontSize:
                                                                            "1rem",
                                                                        fontWeight:
                                                                            "600",
                                                                        borderRadius:
                                                                            "0.375rem",
                                                                        transition:
                                                                            "all 0.2s",
                                                                    }}
                                                                    onMouseEnter={(
                                                                        e,
                                                                    ) => {
                                                                        e.currentTarget.style.backgroundColor =
                                                                            "rgba(255,255,255,0.1)";
                                                                        e.currentTarget.style.borderColor =
                                                                            "white";
                                                                    }}
                                                                    onMouseLeave={(
                                                                        e,
                                                                    ) => {
                                                                        e.currentTarget.style.backgroundColor =
                                                                            "transparent";
                                                                        e.currentTarget.style.borderColor =
                                                                            "rgba(255,255,255,0.3)";
                                                                    }}
                                                                >
                                                                    <i className="fas fa-times me-2"></i>
                                                                    Cancel
                                                                </button>

                                                                {quotaAfterLesson <
                                                                    0 && (
                                                                    <div className="mt-3 text-center">
                                                                        <small
                                                                            style={{
                                                                                color: "#e74c3c",
                                                                                fontSize:
                                                                                    "0.8rem",
                                                                                fontStyle:
                                                                                    "italic",
                                                                            }}
                                                                        >
                                                                            <i className="fas fa-info-circle me-1"></i>
                                                                            Complete
                                                                            remediation
                                                                            lessons
                                                                            to
                                                                            earn
                                                                            quota
                                                                            refunds
                                                                        </small>
                                                                    </div>
                                                                )}
                                                            </div>
                                                        </div>

                                                        {/* Tips Card */}
                                                        <div
                                                            className="card mt-4"
                                                            style={{
                                                                backgroundColor:
                                                                    "#34495e",
                                                                border: "1px solid rgba(52, 152, 219, 0.3)",
                                                                borderRadius:
                                                                    "0.5rem",
                                                            }}
                                                        >
                                                            <div
                                                                className="card-body"
                                                                style={{
                                                                    padding:
                                                                        "1rem",
                                                                }}
                                                            >
                                                                <h6
                                                                    style={{
                                                                        color: "#3498db",
                                                                        fontWeight:
                                                                            "600",
                                                                        fontSize:
                                                                            "0.9rem",
                                                                        marginBottom:
                                                                            "0.75rem",
                                                                    }}
                                                                >
                                                                    <i className="fas fa-lightbulb me-2"></i>
                                                                    Study Tips
                                                                </h6>
                                                                <ul
                                                                    style={{
                                                                        color: "#ecf0f1",
                                                                        fontSize:
                                                                            "0.8rem",
                                                                        lineHeight:
                                                                            "1.6",
                                                                        paddingLeft:
                                                                            "1.25rem",
                                                                        marginBottom: 0,
                                                                    }}
                                                                >
                                                                    <li className="mb-2">
                                                                        Take
                                                                        notes
                                                                        during
                                                                        the
                                                                        video
                                                                    </li>
                                                                    <li className="mb-2">
                                                                        You can
                                                                        rewind
                                                                        if you
                                                                        miss
                                                                        something
                                                                    </li>
                                                                    <li className="mb-2">
                                                                        Complete
                                                                        in one
                                                                        sitting
                                                                        for best
                                                                        retention
                                                                    </li>
                                                                    <li>
                                                                        Review
                                                                        material
                                                                        before
                                                                        live
                                                                        class
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        );
                                    })()}

                                {/* Video Player Mode */}
                                {viewMode === "player" &&
                                    selectedLesson &&
                                    activeSession && (
                                        <div
                                            className="video-player-mode"
                                            style={{ padding: "20px" }}
                                        >
                                            {/* Video Player Header */}
                                            <div className="d-flex justify-content-between align-items-center mb-3">
                                                <h4
                                                    style={{
                                                        color: "white",
                                                        margin: 0,
                                                    }}
                                                >
                                                    <i className="fas fa-play-circle me-2"></i>
                                                    {selectedLesson.title}
                                                </h4>
                                                <button
                                                    className="btn btn-outline-light btn-sm"
                                                    onClick={() => {
                                                        setViewMode("list");
                                                        setPreviewLessonId(
                                                            null,
                                                        );
                                                    }}
                                                >
                                                    <i className="fas fa-arrow-left me-2"></i>
                                                    Back to Lessons
                                                </button>
                                            </div>

                                            {/* Session Info Display */}
                                            <div
                                                className="alert alert-info mb-3"
                                                style={{
                                                    backgroundColor:
                                                        "rgba(52, 152, 219, 0.1)",
                                                    borderColor: "#3498db",
                                                }}
                                            >
                                                <div className="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i className="fas fa-clock me-2"></i>
                                                        <strong>
                                                            Active Session
                                                        </strong>
                                                    </div>
                                                    <div className="text-end">
                                                        <small className="d-block">
                                                            Time Remaining:{" "}
                                                            {
                                                                activeSession.time_remaining_minutes
                                                            }{" "}
                                                            min
                                                        </small>
                                                        <small className="d-block">
                                                            Pause Time Left:{" "}
                                                            {
                                                                activeSession.pause_remaining_minutes
                                                            }{" "}
                                                            min
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Secure Video Player */}
                                            <SecureVideoPlayer
                                                activeSession={activeSession}
                                                lesson={selectedLesson}
                                                videoUrl={`/storage/lessons/${selectedLesson.id}/video.mp4`}
                                                completionThreshold={
                                                    completionThreshold
                                                }
                                                simulationMode={true}
                                                simulationSpeed={10}
                                                pauseWarningSeconds={
                                                    pauseWarningSeconds
                                                }
                                                pauseAlertSound={
                                                    pauseAlertSound
                                                }
                                                onComplete={() => {
                                                    // Handle lesson completion
                                                    completeSession();
                                                    setViewMode("list");
                                                    // Refresh lessons to update status
                                                    window.location.reload();
                                                }}
                                                onProgress={(data) => {
                                                    console.log(
                                                        "Progress update:",
                                                        data,
                                                    );
                                                }}
                                                onError={(error) => {
                                                    alert(error);
                                                }}
                                            />

                                            {/* Lesson Description */}
                                            {selectedLesson.description && (
                                                <div
                                                    className="card mt-3"
                                                    style={{
                                                        backgroundColor:
                                                            "#2c3e50",
                                                        border: "none",
                                                    }}
                                                >
                                                    <div className="card-body">
                                                        <h6
                                                            style={{
                                                                color: "white",
                                                            }}
                                                        >
                                                            Lesson Description
                                                        </h6>
                                                        <p
                                                            style={{
                                                                color: "#95a5a6",
                                                            }}
                                                        >
                                                            {
                                                                selectedLesson.description
                                                            }
                                                        </p>
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    )}
                            </div>
                        )}

                        {activeTab === "documentation" && (
                            <div className="documentation-tab">
                                <h4 className="mb-4" style={{ color: "white" }}>
                                    Course Documentation
                                </h4>

                                <div
                                    className="card"
                                    style={{
                                        backgroundColor: "#2c3e50",
                                        border: "none",
                                    }}
                                >
                                    <div
                                        className="card-header"
                                        style={{
                                            backgroundColor: "#34495e",
                                            borderBottom:
                                                "1px solid rgba(255,255,255,0.1)",
                                        }}
                                    >
                                        <h6
                                            className="mb-0"
                                            style={{ color: "white" }}
                                        >
                                            <i className="fas fa-folder me-2"></i>
                                            Available Documents
                                        </h6>
                                    </div>
                                    <div className="card-body">
                                        {getDocumentsForCourse().length > 0 ? (
                                            <div
                                                className="list-group"
                                                style={{
                                                    backgroundColor:
                                                        "transparent",
                                                }}
                                            >
                                                {getDocumentsForCourse().map(
                                                    (doc, idx) => (
                                                        <a
                                                            key={idx}
                                                            href={doc.url}
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            className="list-group-item d-flex justify-content-between align-items-center"
                                                            style={{
                                                                backgroundColor:
                                                                    "#34495e",
                                                                border: "1px solid rgba(255,255,255,0.1)",
                                                                color: "white",
                                                                marginBottom:
                                                                    "0.5rem",
                                                                textDecoration:
                                                                    "none",
                                                            }}
                                                        >
                                                            <div
                                                                style={{
                                                                    flex: 1,
                                                                }}
                                                            >
                                                                <i
                                                                    className="fas fa-file-pdf me-2"
                                                                    style={{
                                                                        color: "#e74c3c",
                                                                    }}
                                                                ></i>
                                                                {doc.name}
                                                            </div>
                                                            <button
                                                                className="btn btn-sm btn-outline-light"
                                                                onClick={(
                                                                    e,
                                                                ) => {
                                                                    e.preventDefault();
                                                                    window.open(
                                                                        doc.url,
                                                                        "_blank",
                                                                    );
                                                                }}
                                                            >
                                                                <i className="fas fa-download"></i>
                                                            </button>
                                                        </a>
                                                    ),
                                                )}
                                            </div>
                                        ) : (
                                            <p style={{ color: "#95a5a6" }}>
                                                No documents available for this
                                                course.
                                            </p>
                                        )}
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
  )
}

export default OfflineTabSystem;
