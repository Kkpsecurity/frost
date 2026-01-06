import React from "react";

interface CapturedPreviewProps {
    photoType: string;
    idcard: string | null;
    headshot: string | null;
}

const CapturedPreview: React.FC<CapturedPreviewProps> = ({
    photoType,
    idcard,
    headshot,
}) => {

    console.log("CapturedPreview: ", idcard);

    return (
        <div
            style={{
                width: '100%', // Outer div takes 100% of parent container width
                display: 'flex',
                justifyContent: 'center', // Centers the image
                alignItems: 'center', // Vertically centers if needed
            }}
        >
            {photoType === "headshot" && headshot && (
                <img src={headshot ?? ""} alt="Headshot" style={{ width: '100%', height: 'auto' }} />
            )}

            {photoType === "idcard" && idcard && (
                <img src={idcard} alt="ID Card" style={{ width: '100%', height: 'auto' }} />
            )}
        </div>
    );
};

export default CapturedPreview;
