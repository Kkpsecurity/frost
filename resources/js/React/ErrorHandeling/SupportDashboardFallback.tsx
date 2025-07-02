import React, {useEffect} from "react";

const SupportDaashboardFallback = () => {

    useEffect(() => {
        setTimeout(() => {
            window.location.reload();
        }, 60000);
    }, []);

    return (
        <div className="d-flex vh-100 fs-24 vw-100 bg-dark m-b-0 justify-content-center text-white align-items-center">
            The Support Dashboard is Unavaliable please try again later! This screen will refresh in 60 seconds.
        </div>
    );
};

export default SupportDaashboardFallback;
