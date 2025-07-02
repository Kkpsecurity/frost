import React, { useState } from "react";

const StudentSearchBox = ({ search, handleSearchSubmit }) => {
    const [input, setInput] = useState<string>("");

    return (
        <div className="input-group">
            <input
                type="text"
                className="form-control"
                placeholder="Search..."
                value={search ? search : input} // Display search if available, otherwise display input
                onChange={(e) => setInput(e.target.value)} // Update input on change
            />
            {input &&
                !search && ( // Show clear button only if input is not empty and search is empty
                    <button
                        className="btn btn-outline-secondary"
                        type="button"
                        onClick={() => setInput("")} // Clear input field
                    >
                        <i className="fa fa-times"></i>
                    </button>
                )}
            {search ? ( // If search is active, show clear search button
                <button
                    className="btn btn-outline-secondary"
                    type="button"
                    onClick={() => handleSearchSubmit({ qsearch: "" })} // Clear search
                >
                    <i className="fa fa-times"></i>
                </button>
            ) : (
                <button // Show search button if search is not active
                    className="btn btn-outline-secondary"
                    type="button"
                    onClick={() =>
                        handleSearchSubmit({
                            qsearch: input,
                        })
                    }
                >
                    <i className="fa fa-search text-white"></i>
                </button>
            )}
        </div>
    );
};

export default StudentSearchBox;
