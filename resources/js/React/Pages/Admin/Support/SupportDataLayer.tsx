import React, { useState } from "react";
import FrostSupportCenter from "./FrostSupportCenter";

const SupportDataLayer = ({ debug = false }) => {
    if (debug) console.log("SupportDataLayer");    

    return (
        <div className="container-fluid p-5">
            <div className="row">
                <div className="col-md-12">
                    <section className="content">
                        <FrostSupportCenter debug={debug} />
                    </section>
                </div>
            </div>
        </div>
    );
};

export default SupportDataLayer;
