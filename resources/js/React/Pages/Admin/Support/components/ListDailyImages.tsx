import React, { useState } from "react";
import PhotoPreviewStyled from "../../Instructors/FrostTraining/PhotoPreview";
import { Button, ListGroup } from "react-bootstrap";

import "./StudentSearch.css";
import { InstUnitType } from "../../../../Config/types";
import { useFormContext } from "react-hook-form";

interface ListDailyImagesProps {
    validations: {
        headshot?: Record<string, string>;
    };
    handleValidationModeChange: (mode: string) => void;
    handleDeletePhoto: (timestamp: string) => void;
    setCurrentImage: (image: string) => void;
    setShowModal: (show: boolean) => void;
    showModal: boolean;
    instUnit: InstUnitType;
    setPhotoUrl: (photoUrl: string) => void;
}

const ListDailyImages = ({
    validations,
    handleDeletePhoto,
    handleValidationModeChange,
    setCurrentImage,
    setShowModal,
    showModal,
    instUnit,
    setPhotoUrl,
}: ListDailyImagesProps) => {
    const imageDefault = "no-image";
    const today = new Date().toLocaleDateString("en-US");
    const [sliderValue, setSliderValue] = useState(1);
    const { register } = useFormContext();

    const filteredHeadshots = validations.headshot
        ? Object.entries(validations.headshot).filter(
              ([timestamp, headshotUrl]) => {
                  // Ensure validations.headshot is not null or undefined before proceeding
                  if (!validations.headshot) return false;

                  // Convert timestamp to date string
                  const headshotDate = new Date(
                      parseInt(timestamp) * 1000
                  ).toLocaleDateString("en-US");

                  // If there is an active class (instUnit is set), filter by today's date, else include all
                  return instUnit ? headshotDate === today : true;
              }
          )
        : [];

    return <></>;
};

export default ListDailyImages;
