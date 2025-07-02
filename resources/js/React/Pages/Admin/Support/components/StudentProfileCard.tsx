import React from "react";

const StudentProfileCard = ({ student, debug = false }) => {
    if(!student) return null;   
    
    return (
        <div className="card card-primary card-outline">
            <div className="card-body box-profile">
                <div className="text-center">
                    <img
                        className="profile-user-img img-fluid img-circle"
                        src={student.avatar}
                        alt={student.lname}
                    />
                </div>

                <h3 className="profile-username text-center">
                    {student.fname} {student.lname}
                </h3>
                
                <p className="text-muted text-center">{student.email}</p>
            </div>
        </div>
    );
};

export default StudentProfileCard;
