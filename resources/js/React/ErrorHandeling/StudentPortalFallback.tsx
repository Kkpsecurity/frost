import React from "react";


const StudentPortalFallback = () => {

    // will reload the page after
    React.useEffect(() => {
        setTimeout(() => {
            window.location.reload();
        }, 30 * 1000);
    }, []);

    return (
        <div className="d-flex vh-100 fs-24 vw-100 bg-dark mb-0 justify-content-center text-white align-items-center">
            <div className="text-center">
                <h1 className="display-4 text-white">The Student Portal is Unavailable</h1>
                <p className="lead text-white-50">Please try again later!</p>
            </div>
        </div>
    );
};

export default StudentPortalFallback;
