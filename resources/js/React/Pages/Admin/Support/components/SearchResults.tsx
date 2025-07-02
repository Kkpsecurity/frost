import React, { useEffect, useState } from "react";
import { StudentType } from "../../../../Config/types";
import "../searchStyles.css";

const SearchResults = ({
    searchResult,
    searchInput,
    SelectStudent,
    BackToSearch,
    debug = false,
}) => {
    // Initialize a state variable to hold the search value
    const [searchValue, setSearchValue] = useState<string | null>(null);
    const [students, setStudents] = useState<StudentType[] | null>(null);

    useEffect(() => {
        setSearchValue(searchInput);
        setStudents(searchResult);
    }, [searchInput, searchResult]);

    return (
        <div className="container-fluid p-5">
            <div className="row g-5">
                <div className="col-lg-12">
                    <hgroup className="mb-4">
                        <h1>Search Results</h1>
                        <h2 className="lead">
                            <strong className="text-danger">
                                {students?.length}
                            </strong>{" "}
                            results were found for the search for{" "}
                            <strong className="text-danger">
                                {searchValue}
                            </strong>
                        </h2>
                    </hgroup>
                    {students && students?.length <= 0 ? (
                        <div className="alert alert-danger">
                            No Search result found for:{" "}
                            <strong>{searchValue}</strong>
                            <br />
                            <a
                                href="#"
                                onClick={() => {
                                    BackToSearch();
                                    setSearchValue(null);
                                }}
                            >
                                Back to Search
                            </a>
                        </div>
                    ) : (
                        <section className="col-xs-12 col-sm-6 col-md-12">
                            {students &&
                                students.map((item) => (
                                    <article
                                        className="search-result row mb-4"
                                        key={item.id}
                                    >
                                        <div className="col-xs-12 col-md-3">
                                            <a
                                                href="#"
                                                title={item.fname}
                                                className="thumbnail"
                                            >
                                                <img
                                                    src={item.avatar}
                                                    alt={
                                                        item.fname +
                                                        " " +
                                                        item.lname
                                                    }
                                                    className="img-thumbnail rounded-circle"
                                                />
                                            </a>
                                        </div>
                                        <div className="col-xs-12 col-md-7">
                                            <div className="row">
                                                <div className="col-9">
                                                    <a
                                                        href="#"
                                                        title={
                                                            item.fname +
                                                            " " +
                                                            item.lname
                                                        }
                                                        className="text-decoration-none"
                                                    >
                                                        {item.fname +
                                                            " " +
                                                            item.lname}
                                                    </a>
                                                    <p>{item.email}</p>
                                                </div>
                                                <div className="col-3">
                                                    <a
                                                        id={item.id.toString()}
                                                        href={`#`}
                                                        className="btn btn-primary"
                                                        onClick={(e) =>
                                                            SelectStudent(e)
                                                        }
                                                    >
                                                        View Student
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                ))}
                        </section>
                    )}
                </div>
            </div>
        </div>
    );
};

export default SearchResults;
