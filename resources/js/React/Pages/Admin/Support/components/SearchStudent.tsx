import React from "react";
import { useFormContext } from "react-hook-form";
import TextInput from "../../../../Components/FormElements/TextInput";
import "./StudentSearch.css";

const SearchStudent = ({ handleSearchSubmit, debug = false }) => {
    const { register, handleSubmit } = useFormContext(); // Access form methods from context

    return (
        <div className="card card-primary card-outline">
            <form
                onSubmit={handleSubmit(handleSearchSubmit)}
                className="form-search"
            >
                <div className="input-group">
                    <input
                        type="text"
                        name="qsearch"
                        className="form-control float-right mb-0"
                        placeholder="Search Student by name/email"
                        {...register("qsearch", { required: true })}
                    />
                    <div className="input-group-append">
                        <button type="submit" className="btn btn-default">
                            <i className="fas fa-search" />
                        </button>
                    </div>
                </div>
            </form>
        </div>
    );
};

export default SearchStudent;
