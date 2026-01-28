import React from "react";
import OfflineTabsQuickStats from "../OfflineTabsQuickStats";

const TabDetails = ({ lessons, ...props }) => (
    <div className="details-tab">
        {/* ...moved content from details tab... */}
        <h4
            className="mb-4"
            style={{ color: "white", fontSize: "1.75rem", fontWeight: "600" }}
        >
            <i
                className="fas fa-tachometer-alt me-2"
                style={{ color: "#3498db" }}
            ></i>
            Learning Dashboard
        </h4>
        Let Class
        <OfflineTabsQuickStats lessons={lessons} />
        {/* ...rest of details tab content... */}
    </div>
);

export default TabDetails;
