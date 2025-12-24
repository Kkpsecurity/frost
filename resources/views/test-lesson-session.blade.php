<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Lesson Session API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .test-result { 
            margin-top: 10px; 
            padding: 10px; 
            border-radius: 5px; 
            font-family: monospace;
            white-space: pre-wrap;
        }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Lesson Session API Testing</h1>
        <p class="text-muted">Phase 5 Backend API Verification</p>

        @auth
            <div class="alert alert-info">
                <strong>Logged in as:</strong> {{ Auth::user()->full_name }} (ID: {{ Auth::id() }})
            </div>
        @else
            <div class="alert alert-danger">
                <strong>Not logged in!</strong> Please <a href="/login">login</a> to test the API.
            </div>
        @endauth

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5>Test Setup</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Lesson ID</label>
                        <input type="number" id="lessonId" class="form-control" placeholder="e.g., 1" value="">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Course Auth ID</label>
                        <input type="number" id="courseAuthId" class="form-control" placeholder="e.g., 1" value="">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Video Duration (seconds)</label>
                        <input type="number" id="videoDuration" class="form-control" placeholder="e.g., 600" value="600">
                    </div>
                </div>
                <button class="btn btn-secondary mt-3" onclick="loadTestData()">Auto-Load Test Data</button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <h6>Test 1: Start Session</h6>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-success" onclick="testStartSession()">
                            Start Lesson Session
                        </button>
                        <div id="startResult" class="test-result d-none"></div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h6>Test 2: Update Progress</h6>
                    </div>
                    <div class="card-body">
                        <label>Session ID (from Test 1)</label>
                        <input type="text" id="sessionId" class="form-control mb-2" placeholder="UUID from start session">
                        <label>Playback Progress (seconds)</label>
                        <input type="number" id="progressSeconds" class="form-control mb-2" value="120">
                        <label>Completion %</label>
                        <input type="number" id="completionPercent" class="form-control mb-2" value="20">
                        <button class="btn btn-info" onclick="testUpdateProgress()">
                            Update Progress
                        </button>
                        <div id="progressResult" class="test-result d-none"></div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-warning text-dark">
                        <h6>Test 3: Track Pause Time</h6>
                    </div>
                    <div class="card-body">
                        <label>Pause Minutes</label>
                        <input type="number" id="pauseMinutes" class="form-control mb-2" value="2">
                        <button class="btn btn-warning" onclick="testTrackPause()">
                            Track Pause
                        </button>
                        <div id="pauseResult" class="test-result d-none"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-secondary text-white">
                        <h6>Test 4: Get Session Status</h6>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-secondary" onclick="testSessionStatus()">
                            Get Session Status
                        </button>
                        <div id="statusResult" class="test-result d-none"></div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-danger text-white">
                        <h6>Test 5: Complete Session</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Note: Update progress to 80%+ before completing</p>
                        <button class="btn btn-danger" onclick="testCompleteSession()">
                            Complete Session
                        </button>
                        <div id="completeResult" class="test-result d-none"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h6>Quick Test: Full Workflow</h6>
                    </div>
                    <div class="card-body">
                        <p class="small">Runs all tests in sequence: Start → Progress → Pause → Status → Complete</p>
                        <button class="btn btn-dark" onclick="runFullWorkflow()">
                            Run Full Workflow Test
                        </button>
                        <div id="workflowResult" class="test-result d-none"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        async function loadTestData() {
            try {
                const response = await fetch('/classroom/debug/student');
                const data = await response.json();
                
                if (data.courseAuth) {
                    document.getElementById('courseAuthId').value = data.courseAuth.id;
                }
                
                if (data.lessons && data.lessons.length > 0) {
                    const lesson = data.lessons[0];
                    document.getElementById('lessonId').value = lesson.id;
                    document.getElementById('videoDuration').value = lesson.video_seconds || 600;
                }
                
                alert('Test data loaded! Please verify the values and click "Start Lesson Session"');
            } catch (error) {
                alert('Error loading test data: ' + error.message);
            }
        }

        async function testStartSession() {
            const lessonId = document.getElementById('lessonId').value;
            const courseAuthId = document.getElementById('courseAuthId').value;
            const videoDuration = document.getElementById('videoDuration').value;
            
            const resultDiv = document.getElementById('startResult');
            resultDiv.className = 'test-result info';
            resultDiv.classList.remove('d-none');
            resultDiv.textContent = 'Starting session...';

            try {
                const response = await fetch('/classroom/lesson/start-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        lesson_id: parseInt(lessonId),
                        course_auth_id: parseInt(courseAuthId),
                        video_duration_seconds: parseInt(videoDuration),
                        lesson_title: 'Test Lesson'
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    resultDiv.className = 'test-result success';
                    resultDiv.textContent = `✅ SUCCESS!\n\n${JSON.stringify(data, null, 2)}`;
                    
                    // Auto-fill session ID for other tests
                    if (data.session && data.session.sessionId) {
                        document.getElementById('sessionId').value = data.session.sessionId;
                    }
                } else {
                    resultDiv.className = 'test-result error';
                    resultDiv.textContent = `❌ FAILED\n\n${JSON.stringify(data, null, 2)}`;
                }
            } catch (error) {
                resultDiv.className = 'test-result error';
                resultDiv.textContent = `❌ ERROR: ${error.message}`;
            }
        }

        async function testUpdateProgress() {
            const sessionId = document.getElementById('sessionId').value;
            const playbackSeconds = document.getElementById('progressSeconds').value;
            const completionPercent = document.getElementById('completionPercent').value;
            
            const resultDiv = document.getElementById('progressResult');
            resultDiv.className = 'test-result info';
            resultDiv.classList.remove('d-none');
            resultDiv.textContent = 'Updating progress...';

            try {
                const response = await fetch('/classroom/lesson/update-progress', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        session_id: sessionId,
                        playback_seconds: parseInt(playbackSeconds),
                        completion_percentage: parseFloat(completionPercent)
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    resultDiv.className = 'test-result success';
                    resultDiv.textContent = `✅ SUCCESS!\n\n${JSON.stringify(data, null, 2)}`;
                } else {
                    resultDiv.className = 'test-result error';
                    resultDiv.textContent = `❌ FAILED\n\n${JSON.stringify(data, null, 2)}`;
                }
            } catch (error) {
                resultDiv.className = 'test-result error';
                resultDiv.textContent = `❌ ERROR: ${error.message}`;
            }
        }

        async function testTrackPause() {
            const sessionId = document.getElementById('sessionId').value;
            const pauseMinutes = document.getElementById('pauseMinutes').value;
            
            const resultDiv = document.getElementById('pauseResult');
            resultDiv.className = 'test-result info';
            resultDiv.classList.remove('d-none');
            resultDiv.textContent = 'Tracking pause...';

            try {
                const response = await fetch('/classroom/lesson/track-pause', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        session_id: sessionId,
                        pause_minutes: parseFloat(pauseMinutes)
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    resultDiv.className = 'test-result success';
                    resultDiv.textContent = `✅ SUCCESS!\n\n${JSON.stringify(data, null, 2)}`;
                } else {
                    resultDiv.className = 'test-result error';
                    resultDiv.textContent = `❌ FAILED\n\n${JSON.stringify(data, null, 2)}`;
                }
            } catch (error) {
                resultDiv.className = 'test-result error';
                resultDiv.textContent = `❌ ERROR: ${error.message}`;
            }
        }

        async function testSessionStatus() {
            const sessionId = document.getElementById('sessionId').value;
            
            const resultDiv = document.getElementById('statusResult');
            resultDiv.className = 'test-result info';
            resultDiv.classList.remove('d-none');
            resultDiv.textContent = 'Getting session status...';

            try {
                const response = await fetch(`/classroom/lesson/session-status/${sessionId}`);
                const data = await response.json();
                
                if (data.success) {
                    resultDiv.className = 'test-result success';
                    resultDiv.textContent = `✅ SUCCESS!\n\n${JSON.stringify(data, null, 2)}`;
                } else {
                    resultDiv.className = 'test-result error';
                    resultDiv.textContent = `❌ FAILED\n\n${JSON.stringify(data, null, 2)}`;
                }
            } catch (error) {
                resultDiv.className = 'test-result error';
                resultDiv.textContent = `❌ ERROR: ${error.message}`;
            }
        }

        async function testCompleteSession() {
            const sessionId = document.getElementById('sessionId').value;
            
            const resultDiv = document.getElementById('completeResult');
            resultDiv.className = 'test-result info';
            resultDiv.classList.remove('d-none');
            resultDiv.textContent = 'Completing session...';

            try {
                const response = await fetch('/classroom/lesson/complete-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        session_id: sessionId
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    resultDiv.className = 'test-result success';
                    resultDiv.textContent = `✅ SUCCESS!\n\n${JSON.stringify(data, null, 2)}`;
                } else {
                    resultDiv.className = 'test-result error';
                    resultDiv.textContent = `❌ FAILED\n\n${JSON.stringify(data, null, 2)}`;
                }
            } catch (error) {
                resultDiv.className = 'test-result error';
                resultDiv.textContent = `❌ ERROR: ${error.message}`;
            }
        }

        async function runFullWorkflow() {
            const resultDiv = document.getElementById('workflowResult');
            resultDiv.className = 'test-result info';
            resultDiv.classList.remove('d-none');
            resultDiv.textContent = 'Running full workflow test...\n';

            const log = (msg) => {
                resultDiv.textContent += msg + '\n';
            };

            try {
                // Step 1: Start Session
                log('\n[1/5] Starting session...');
                await testStartSession();
                await sleep(1000);
                
                // Step 2: Update Progress to 85%
                log('\n[2/5] Updating progress to 85%...');
                document.getElementById('completionPercent').value = 85;
                const videoDuration = parseInt(document.getElementById('videoDuration').value);
                document.getElementById('progressSeconds').value = Math.floor(videoDuration * 0.85);
                await testUpdateProgress();
                await sleep(1000);
                
                // Step 3: Track Pause
                log('\n[3/5] Tracking pause time...');
                await testTrackPause();
                await sleep(1000);
                
                // Step 4: Get Status
                log('\n[4/5] Getting session status...');
                await testSessionStatus();
                await sleep(1000);
                
                // Step 5: Complete Session
                log('\n[5/5] Completing session...');
                await testCompleteSession();
                
                resultDiv.className = 'test-result success';
                log('\n✅ FULL WORKFLOW COMPLETE! Check individual test results above.');
                
            } catch (error) {
                resultDiv.className = 'test-result error';
                log(`\n❌ WORKFLOW FAILED: ${error.message}`);
            }
        }

        function sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }
    </script>
</body>
</html>
