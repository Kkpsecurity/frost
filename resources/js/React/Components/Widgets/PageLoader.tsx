import React from "react";

interface Props {
    base_url: string;
}

const PageLoader: React.FC<Props> = ({ base_url }) => {
    return (
        <div
            className="vh-100"
            style={{
                display: "flex",
                justifyContent: "center",
                backgroundColor: "transparent",
            }}
        >
            <div>
                <img src={base_url + "/assets/img/loading.gif"} />
            </div>
        </div>
    );
};

export default PageLoader;
