import React, { useState } from 'react';

interface StudentSearchProps {
    onStudentFound?: (student: any) => void;
}

const StudentSearch: React.FC<StudentSearchProps> = ({ onStudentFound }) => {
    const [searchTerm, setSearchTerm] = useState('');
    const [results, setResults] = useState([]);

    const handleSearch = async () => {
        try {
            // Placeholder for actual search API call
            console.log('Searching for:', searchTerm);
            if (onStudentFound) {
                onStudentFound({ name: searchTerm, id: 123 });
            }
        } catch (error) {
            console.error('Search error:', error);
        }
    };

    return (
        <div className="student-search">
            <div className="card">
                <div className="card-header">
                    <h6>Student Search</h6>
                </div>
                <div className="card-body">
                    <div className="input-group mb-3">
                        <input
                            type="text"
                            className="form-control"
                            placeholder="Search students..."
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                        />
                        <button
                            className="btn btn-primary"
                            type="button"
                            onClick={handleSearch}
                        >
                            Search
                        </button>
                    </div>
                    <div className="search-results">
                        {/* Search results will appear here */}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default StudentSearch;
