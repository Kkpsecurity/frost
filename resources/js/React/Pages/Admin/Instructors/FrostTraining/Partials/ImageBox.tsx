import React, { useEffect } from "react";
import { useFormContext } from "react-hook-form";
import TextHidden from "../../../../../Components/FormElements/TextHidden";

interface ImageBoxProps {
    image: string; // Image URL
    hasImage: boolean; // Whether it does not have the default image
    imgType: string; // 'headshot' or 'idcard'
    validationMode: string; // 'validate' or 'decline'
}

const dimensions = {
    width: "300px",
    height: "200px",
};

const ImageBox = ({
    image,
    hasImage,
    imgType,
    validationMode, 
}: ImageBoxProps) => {
    const { register } = useFormContext();

    console.log("ImageBoxProps: ",  validationMode);

    return (
        <div className="border image shadow" style={{ width: dimensions.width }}>
            <img
                src={image}
                alt={`${imgType} preview`}
                id={imgType}
                style={{
                    height: dimensions.height,
                    margin: "10px",
                    background: "#cccccc", // Provides a background color for images with transparency
                    display: "block", // Ensures the image is centered correctly                                        
                }}
            />
          
            {validationMode == "decline" && (
                <span className="form-check">
                    <input
                        type="checkbox"
                        {...register(`${imgType}_delete`)}
                        id={`${imgType}_delete`}
                        className="form-check-input"
                    />
                    <label className="form-check-label" htmlFor={`${imgType}_delete`}>
                        Mark For Deletion
                    </label>
                </span>
            )}

            <button
                type="submit"
                className="btn btn-primary btn-sm"
                style={{ margin: "10px" }}
                onClick={() => {console.log("Validate Image")}}
                disabled={!hasImage}
            >
                Validate Image
            </button>
        </div>
    );
};

export default ImageBox;
