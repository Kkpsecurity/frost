import React from "react";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faArrowLeft, faArrowRight } from '@fortawesome/free-solid-svg-icons';

const StudentPaginator = ({
    loadPrevStudents,
    currentPage,
    loadMoreStudents,
    TotalPages,
}) => {
    return (
        <div
            className="page-metatdata"
            style={{
                position: "sticky",
                bottom: 0,
                zIndex: 100,
                display: "flex",
                justifyContent: "flex-end",
                alignItems: "center",
                height: "40px",
                background: "#222",
            }}
        >
            <button
                onClick={loadPrevStudents}
                className="btn btn-primary load-more-btn mr-2"
                style={{ margin: "auto" }}
                disabled={currentPage === 1}
            >
                <FontAwesomeIcon icon={faArrowLeft} /> Prev
            </button>
            <button
                onClick={loadMoreStudents}
                className="btn btn-primary load-more-btn"
                style={{ margin: "auto" }}
                disabled={currentPage === TotalPages}
            >
                Next <FontAwesomeIcon icon={faArrowRight} />
            </button>
        </div>
    );
};

export default StudentPaginator;
