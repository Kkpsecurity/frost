import React, { MouseEventHandler } from "react";
import { StudentType } from "../../../../../Config/types";

interface VerifiedStudentToolBarProps {
    student: StudentType;
    viewStudentCard: MouseEventHandler<HTMLButtonElement>;
}

const VerifiedStudentToolBar: React.FC<VerifiedStudentToolBarProps> = ({
    student,
    viewStudentCard,
}) => {
    return (
        <div className="row">
            <div className="col-12 border text-right">
                <button
                    className="btn btn-sm btn-warning m-1"
                    onClick={() => {}}
                >
                    D.N.C
                </button>
                <button
                    className="btn btn-sm btn-danger m-1"
                    onClick={() => {}}
                >
                    Ban
                </button>
            </div>
        </div>
    );
};

export default VerifiedStudentToolBar;
