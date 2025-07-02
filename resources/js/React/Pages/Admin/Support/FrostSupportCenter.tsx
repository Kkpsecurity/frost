import React, { useEffect, useState } from "react";
import SupportStudentDashboard from "./components/SupportStudentDashboard";
import SearchStudent from "./components/SearchStudent";
import SearchResults from "./components/SearchResults";
import { MessageConsoleType, StudentType } from "../../../Config/types";
import MessageConsole from "../Instructors/FrostTraining/Partials/MessageConsole";
import TitleBar from "./components/TitleBar";
import apiClient from "../../../Config/axios";
import { FormProvider, useForm } from "react-hook-form";
import { useSupportCenterHook } from "../../../Hooks/Admin/SupportCenterHooks";
import PageLoader from "../../../Components/Widgets/PageLoader";
import { Alert } from "react-bootstrap";

type ClassDataType = {
    student: StudentType;
    classIsLive: boolean;
};

const FrostSupportCenter = ({ debug = false }) => {

    /**
     * Student data state
     */
    const [student, setStudent] = useState<StudentType | null>(null);

    /**
     * State for the class data
     */
    const [isActive, setIsActive] = useState<boolean>(false);

    /**
     * State for the search result
     */
    const [searchResult, setSearchResult] = useState<StudentType[] | null>(
        null
    );

    const [searchText, setSearchText] = useState<string | null>(null);
    const [mc, setMC] = useState<MessageConsoleType | null>(null);

    const handleSearchSubmit = async (data) => {
        try {
            const response = await apiClient.post(
                "admin/frost-support/search",
                {
                    qsearch: data.qsearch,
                }
            );
            if (response.data) {
                console.log("FrostSupportCenter", response);
                setSearchResult(response.data.students.data);
                setSearchText(data.qsearch);
            }
        } catch (error) {
            console.error(error);
        }
    };

    interface Event {
        preventDefault: () => void;
        target: { id: string };
    }

    const SelectStudent = (e: Event) => {
        e.preventDefault();
        const studentId = parseInt(e.target.id);

        if (searchResult && Array.isArray(searchResult)) {
            // Ensure that searchResult is defined and is an array
            const selectedStudent = searchResult.find(
                (student: StudentType) => student.id === studentId
            );

            if (selectedStudent) {
                // Check if the selected student is found
                setStudent(selectedStudent);
            } else {
                console.error("Selected student not found.");
            }
        } else {
            console.error("Search result data is missing or invalid.");
        }
    };  

    const methods = useForm();
    const { data, status, error } = useSupportCenterHook(student?.id);

    // if (status === 'loading') return <PageLoader base_url={window.location.origin} />;
    if (status === "error")
        return (
            <Alert variant="danger">Error: {(error as Error).message}</Alert>
        );

    const newSearch = () => {
        setStudent(null);
        setSearchResult(null);
    };

    const BackToSearch = () => {
        newSearch();
    };

    useEffect(() => {
        if (data) {
            setIsActive(data.classIsLive);
        }
    }, [data]);

    return (
        <section className="content">
            <div className="container-fluid">
                <div className="row">
                    <div className="col-md-12">
                        <TitleBar
                            student={student}
                            searchResult={searchResult}
                            newSearch={newSearch}
                        />
                        
                        {mc && mc?.status && mc?.message ? (
                            <MessageConsole
                                status={mc?.status}
                                message={mc?.message}
                            />
                        ) : null}

                        {student ? (
                            <SupportStudentDashboard
                                debug={debug}
                                isActive={isActive}
                                setIsActive={setIsActive}
                                classData={data}
                            />
                        ) : searchResult ? (
                            <SearchResults
                                debug={debug}
                                searchResult={searchResult}
                                searchInput={searchText}
                                SelectStudent={SelectStudent}
                                BackToSearch={BackToSearch}
                            />
                        ) : (
                            <div
                                className="container mt-5"
                                style={{ borderRadius: 25 }}
                            >
                                <FormProvider {...methods}>
                                    <SearchStudent
                                        handleSearchSubmit={handleSearchSubmit}
                                        debug={debug}
                                    />
                                </FormProvider>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </section>
    );
};

export default FrostSupportCenter;
