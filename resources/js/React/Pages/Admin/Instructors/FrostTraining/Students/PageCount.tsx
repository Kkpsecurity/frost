import React from "react";

interface PageCountProps {
    currentPage: number;
    lastPage: number;
}

const PageCount = ({ currentPage, lastPage }) => {
    return (
        <div
            style={{
                marginRight: "10px",
                fontSize: "1.2rem",
            }}
            className="col-4"
        >
            Page {currentPage} of {lastPage}
        </div>
    );
};

export default PageCount;
