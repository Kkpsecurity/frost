<!-- Step 1: Search for a student -->
<style>
    .user-profile-card {
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
    }

    .user-profile-card .avatar {
        display: flex;
        justify-content: center;
        align-items: center;        
        width: 100%;
        max-width: 200px;
        margin: 0 auto;
    }

    .user-profile-card .avatar img {
        width: 100%;
        max-width: 200px;
        height: auto;
        border-radius: 50%;
    }

    .user-profile-card .user-info {
        text-align: center;
    }

    .user-profile-card .user-info h3 {
        font-size: 1.5rem;
        font-weight: bold;
    }

    .user-profile-card .user-info p {
        font-size: 1.2rem;
    }

    .user-profile-card .user-info p.bolder {
        font-weight: bold;
    }
</style>

<div id="step1" class="container mt-3" style="display: none;">
    <h4 class="mb-4">Step 1: Search for a Student</h4>
    <div class="row">
        <div class="col-md-6">
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="studentSearch" placeholder="Search for a student...">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit" onclick="searchStudents()">Search</button>
                </div>
            </div>
        </div>
    </div>
    <div class="student-list-container" id="matchingStudentsList"></div>
</div>

<!-- Step 2: Select a student and create a ticket -->
<div id="step2" class="container mt-3" style="display: none;">
    <h4 class="mb-4">Step 2: Select a Student and Create a Ticket</h4>
    <!-- Display selected student information here -->
    <div id="selectedStudentInfo"></div>
    @include('admin.frost.support.dashboard.student')

</div>

<!-- Step 3: Active view for the ticket -->
<div id="step3" class="container mt-3" style="display: none;">
    <h4 class="mb-4">Step 3: Active View for the Ticket <span id="studentName"></span></h4>
    <!-- Display active ticket information here -->
    <div id="activeTicketInfo"></div>
</div>

<script>
    let profileData = [];

    function searchStudents() {
        var searchInput = document.getElementById('studentSearch').value;

        // Make an AJAX request to fetch matching students
        fetch('{{ route('admin.frost-support.dashboard.search.students') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    searchInput: searchInput
                }),
            })
            .then(response => response.json())
            .then(data => {
                console.log('Matching students:', data.students);
                // Check if data.students is an object and has the expected structure
                if (typeof data.students === 'object' && Array.isArray(data.students.data)) {
                    var studentList = data.students.data.map(student => {
                        return {
                            id: student.id,
                            fname: student.fname,
                            lname: student.lname,
                            email: student.email,
                        };
                    });

                    displayMatchingStudents(studentList);
                } else {
                    console.error('Invalid response structure. Expected an object with a "data" array.');
                }
            })
            .catch(error => {
                console.error('Error fetching matching students:', error);
            });
    }

    function displayMatchingStudents(students) {
        console.log('Displaying matching students:', students);

        var matchingStudentsList = document.getElementById('matchingStudentsList');
        matchingStudentsList.innerHTML = '';
        if (students.length > 0) {
            // Display matching students
            students.forEach(student => {
                var listItem = document.createElement('li');
                listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                listItem.textContent = `${student.fname} ${student.lname} - ${student.email}`;

                // Set the selected student's ID as a data attribute
                listItem.setAttribute('data-student-id', student.id);

                var selectButton = document.createElement('button');
                selectButton.className = 'btn btn-primary';
                selectButton.textContent = 'Select';
                selectButton.onclick = function() {
                    // Set the selected student's name for display
                    document.getElementById('studentSearch').value = `${student.fname} ${student.lname}`;

                    // Set the selected student's name for Step 3
                    document.getElementById('studentName').textContent =
                        `${student.fname} ${student.lname}`;

                    nextStep(student.id);
                };

                listItem.appendChild(selectButton);
                matchingStudentsList.appendChild(listItem);
            });
        } else {
            // Display a message if no students were found
            var listItem = document.createElement('li');
            listItem.className = 'list-group-item';
            listItem.textContent = 'No students found';

            matchingStudentsList.appendChild(listItem);
        }
    }

    function initializeSteps() {
        // Check if we have a step 1
        if (document.getElementById('step1')) {
            localStorage.setItem('step', 1);
            document.getElementById('step1').style.display = 'block';
        }
    }

    function nextStep(studentId) {
        const currentStep = parseInt(localStorage.getItem('step'));

        if (currentStep === 1) {
            // Hide step 1
            document.getElementById('step1').style.display = 'none';

            // Show step 2
            document.getElementById('step2').style.display = 'block';

            // Update the step in storage
            localStorage.setItem('step', 2);

            if (studentId) {
                fetch(`{{ url('admin/frost-support/dashboard/get-student-data') }}/${studentId}`, {
                        method: 'GET',
                    })
                    .then(response => response.json()) // Convert the response to JSON
                    .then(data => {
                        console.log('Student data:', data);
                        profileData = data.student;

                        const userProfileCard = document.querySelector('.user-profile-card');
                        userProfileCard.innerHTML = `
                            <div class="card-body">
                                <h5 class="card-title">User Profile</h5>
                                <div class="avatar">
                                    <img src="${profileData.avatar}" alt="${profileData.fname} ${profileData.lname}" class="img-fluid" />
                                </div>
                                <div class="user-info">
                                    <h3>Name: ${profileData.fname} ${profileData.lname}</h3>
                                    <p class="lead bolder">Email: ${profileData.email}</p>
                                </div>
                            </div>
                        `;

                    })
                    .catch(error => {
                        console.error('Error fetching student data:', error);
                    });
            }

        } else if (currentStep === 2) {
            // Hide step 2
            document.getElementById('step2').style.display = 'none';

            // Show step 3
            document.getElementById('step3').style.display = 'block';

            // Update the step in storage
            localStorage.setItem('step', 3);
        }
    }

    initializeSteps();
</script>
