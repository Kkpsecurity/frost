import React from "react";
import { StudentUnitType } from "../../../../Config/types";
import { OverlayTrigger, Tooltip } from "react-bootstrap";
import DayReport from "./DayReport";
import ShowListOfDays from "./ShowListOfDays";

const LiveClassSupport = ({ classData, activeLesson, selectedCourseId }) => {
    const [showDayReport, setShowDayReport] = React.useState<boolean>(false);
    const [selectUnitId, setSelectUnitId] = React.useState<number | null>(null);
    const [selectDate, setSelectDate] = React.useState<string | null>(null);
    const [selectDay, setSelectDay] = React.useState<number | null>(null);

    const getDayOfWeek = (timestamp: number) => {
        const days = [
            "Sunday",
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday",
        ];

        const date = new Date(timestamp * 1000);
        return days[date.getDay()];
    };

    /**
     * Renders a tooltip for displaying information about 'Completed Day'.
     *
     * @param {Object} props - The properties passed to the Tooltip component.
     * @returns {Tooltip} - The Tooltip component with the provided information.
     */
    const renderTooltip = () => (
        <Tooltip id="button-tooltip">
            The current list shows student attendance history by day, detailing
            completed activities, pass/fail status, and reasons for any
            failures, such as challenges missed or tardiness. It's designed to
            provide a quick overview of daily student performance.
        </Tooltip>
    );

    const viewDayReport = (unit_id: number, date: string) => {
        setShowDayReport(true);
        setSelectUnitId(unit_id);
        setSelectDate(date);
        
        // Adjusting the day of the week so Monday is 0, Tuesday is 1, ..., Friday is 4
        let dayOfWeek = new Date(date).getDay() - 1; // This makes Sunday -1 and Saturday 5
        if (dayOfWeek === -1) {
            // If the date is Sunday, adjust to 6 to then be set to a value outside of 0-4
            dayOfWeek = 6;
        }
    
        setSelectDay(dayOfWeek);
    };
    

    return (
        <div className="container mt-4">
            <h2 className="d-flex justify-content-between align-items-center ">
                Classroom Overview
                <OverlayTrigger placement="bottom" overlay={renderTooltip}>
                    <small>
                        <pre>
                            <i className="fa fa-question-circle"></i>
                        </pre>
                    </small>
                </OverlayTrigger>
            </h2>

            {showDayReport ? (
                <DayReport
                    classData={classData}
                    selectUnitId={selectUnitId}
                    selectDay={selectDay}
                    selectDate={selectDate}
                    activeLesson={activeLesson}
                    selectedCourseId={selectedCourseId}
                    setShowDayReport={setShowDayReport}
                    showDayReport={showDayReport}
                />
            ) : (
                <ShowListOfDays
                    classData={classData}
                    getDayOfWeek={getDayOfWeek}
                    viewDayReport={viewDayReport}
                />
            )}
        </div>
    );
};

export default LiveClassSupport;
