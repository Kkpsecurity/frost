<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/frostplayer.css') }}">
    <title>Zoom Player</title>
</head>

@php
    $courseAuthId = $content['course_auth_id'] ?? null;
    $courseDateId = $content['course_date_id'] ?? null;

    if (!$courseAuthId || !$courseDateId) {
        exit('<div class="alert alert-danger">Invalid course authentication or course date ID.</div>');
    }

    \Log::info('Course Auth ID:', ['courseAuthId' => $courseAuthId]);

    $courseAuth = App\Models\CourseAuth::find($courseAuthId);
    if (!$courseAuth) {
        exit('<div class="alert alert-danger">Course authentication not found.</div>');
    }

    $course = App\Models\Course::find($courseAuth->course_id);
    if (!$course) {
        exit('<div class="alert alert-danger">Course not found.</div>');
    }

    $zoom = $course->ZoomCreds;
    if (!$zoom) {
        exit('<div class="alert alert-danger">Zoom credentials not found.</div>');
    }

    $config = config('zoom');
    $zoomVersion = '3.8.5';
    $meetingNumber = $zoom->pmi;

    try {
        $password = Crypt::decrypt($zoom->zoom_password);
    } catch (Illuminate\Contracts\Encryption\DecryptException $e) {
        $password = '';
    }

    try {
        $passcode = Crypt::decrypt($zoom->zoom_passcode);
    } catch (Illuminate\Contracts\Encryption\DecryptException $e) {
        $passcode = '';
    }

    $userName = \Auth::user()->fullname();
    $userEmail = $zoom->zoom_email;
    $clientID = $config['api_key'] ?? '';
@endphp

<body>
    <!-- Loader Element -->
    <div id="loader">
        <div>
            <div class="spinner"></div>
            <span>Loading...</span>
        </div>
    </div>

    <div id="message-console"></div>
    <div id="zmmtg-root"></div>

    <script>
        let meetingConfig = {};

        /**
         * Remove Loader
         */
        function removeLoader() {
            const loader = document.getElementById('loader');
            if (loader) {
                loader.style.display = 'none';
                console.log("Zoom: ✅ Loader removed successfully.");
            } else {
                console.warn("Zoom: ⚠️ Loader element not found.");
            }
        }

        /**
         * Display Messages
         */
        function showMessage(message, type) {
            const messageConsole = document.getElementById('message-console');
            if (messageConsole) {
                messageConsole.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
            } else {
                console.warn("Zoom: ⚠️ Message console element not found.");
            }
        }

        /**
         * Load a Single Script with Retry Mechanism
         */
        function loadScript(src, retries = 3) {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = src;
                script.async = true;
                script.onload = () => {
                    console.log(`Zoom: ✅ Loaded: ${src}`);
                    resolve();
                };
                script.onerror = () => {
                    if (retries > 1) {
                        console.warn(`Zoom: ⚠️ Failed: ${src}. Retrying... (${retries - 1} left)`);
                        setTimeout(() => resolve(loadScript(src, retries - 1)), 1000);
                    } else {
                        console.error(`Zoom: ❌ Failed to load: ${src} after multiple attempts.`);
                        reject(new Error(`Failed to load script: ${src}`));
                    }
                };
                document.body.appendChild(script);
            });
        }

        /**
         * Load All Scripts Sequentially
         */
        function loadAllScripts(callback) {
            const zoomVersion = "{{ $zoomVersion }}";
            const scripts = [
                `https://source.zoom.us/${zoomVersion}/lib/vendor/react.min.js`,
                `https://source.zoom.us/${zoomVersion}/lib/vendor/react-dom.min.js`,
                `https://source.zoom.us/${zoomVersion}/lib/vendor/lodash.min.js`,
                `https://source.zoom.us/${zoomVersion}/lib/vendor/redux.min.js`,
                `https://source.zoom.us/${zoomVersion}/lib/vendor/redux-thunk.min.js`,
                `https://source.zoom.us/${zoomVersion}/zoom-meeting-${zoomVersion}.min.js`
            ];

            console.log("Zoom: 📡 Starting script load...");

            scripts.reduce((promise, src) => promise.then(() => loadScript(src)), Promise.resolve())
                .then(() => {
                    console.log("Zoom: 🚀 All scripts loaded successfully.");
                    if (callback) callback();
                    removeLoader(); // ✅ Only remove loader when scripts have loaded
                })
                .catch(error => {
                    console.error("Zoom: ❌ Script Load Error:", error);
                    showMessage('Zoom SDK failed to load.', 'danger');
                });
        }

        /**
         * Prepare Web SDK
         */
        function websdkready() {
            ZoomMtg.preLoadWasm();
            ZoomMtg.prepareWebSDK();
            ZoomMtg.i18n.load('en-US');
            ZoomMtg.i18n.reload('en-US');

            console.log("Zoom: 📡 Preparing Web SDK...");

            meetingConfig = {
                mn: "{{ $meetingNumber }}",
                name: "{{ $userName }}",
                email: "{{ $userEmail }}",
                pwd: "{{ $password }}",
                pcd: "{{ $passcode }}",
                role: 0
            };

            fetch(`{{ url('/api/zoom/signature') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        meetingNumber: meetingConfig.mn,
                        role: meetingConfig.role
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.signature) {
                        console.log("Zoom: ✅ Signature received:", data.signature);
                        meetingConfig.signature = data.signature;
                        startMeeting();
                    } else {
                        throw new Error(data.error || "Zoom: ❌ Unknown error fetching signature.");
                    }
                })
                .catch(error => {
                    console.error("Zoom: ❌ Signature Fetch Error:", error);
                    showMessage('Failed to retrieve Zoom signature.', 'danger');
                });
        }

        /**
         * Start Meeting
         */
        function startMeeting() {
            const courseAuthId = @json($courseAuthId);
            const courseDateId = @json($courseDateId);

            console.log("Zoom: 📡 Initializing Zoom Meeting...");

            try {
                ZoomMtg.init({
                    leaveUrl: `{{ url('/classroom/portal/zoom/screen_share') }}/${courseAuthId}/${courseDateId}`,
                    isSupportAV: true,
                    success: function() {
                        try {
                            removeLoader();
                            showMessage('Joining the Zoom meeting...', 'success');

                            console.log("Zoom: 🛠 Debugging Meeting Config");
                            console.log("Zoom: 📡 Meeting Number:", meetingConfig.mn);
                            console.log("Zoom: 👤 User Name:", meetingConfig.name);
                            console.log("Zoom: 🔑 Signature:", meetingConfig.signature);
                            console.log("Zoom: 🔐 SDK Key:", "{{ $clientID }}");
                            console.log("Zoom: 📧 User Email:", meetingConfig.email);
                            console.log("Zoom: 🔑 Password/Passcode:", meetingConfig.pcd);

                            try {
                                ZoomMtg.join({
                                    meetingNumber: meetingConfig.mn,
                                    userName: meetingConfig.name,
                                    signature: meetingConfig.signature,
                                    sdkKey: "{{ $clientID }}",
                                    userEmail: meetingConfig.email,
                                    passWord: meetingConfig.pcd,
                                    success: res => {
                                        ZoomMtg.getAttendeeslist({});
                                        ZoomMtg.getCurrentUser({
                                            success: function (res) {
                                                console.log("success getCurrentUser", res.result.currentUser);
                                            },
                                        });
                                        showMessage('Join Meeting Success.', 'success');

                                        setTimeout(() => {
                                            const messageConsole = document.getElementById('message-console');
                                            if (messageConsole) {
                                                messageConsole.innerHTML = '';
                                            }
                                        }, 5000);
                                        console.log('Zoom: ✅ Join Meeting Success', res);
                                    },
                                    error: res => {
                                        console.error('Zoom: ❌ Join Meeting Error', res);
                                        showMessage('Failed to join the Zoom meeting.', 'danger');
                                    }
                                });
                            } catch (joinError) {
                                console.error("Zoom: ❌ Unexpected error during ZoomMtg.join()", joinError);
                                showMessage('An unexpected error occurred while joining the meeting.',
                                'danger');
                            }

                        } catch (innerError) {
                            console.error("Zoom: ❌ Error during meeting initialization:", innerError);
                            showMessage('An unexpected error occurred during meeting initialization.',
                            'danger');
                        }
                    },
                    error: function(res) {
                        console.error('Zoom: ❌ Zoom SDK Init Error', res);
                        showMessage('Failed to initialize Zoom meeting.', 'danger');
                        removeLoader();
                    }
                });

            } catch (initError) {
                console.error("Zoom: ❌ Critical Error Initializing Zoom SDK", initError);
                showMessage('A critical error occurred while initializing Zoom.', 'danger');
            }
        }


        /**
         * Initialize Zoom Environment
         */
        function initZoomEnv() {
            console.log("Zoom: 📡 Initializing Zoom SDK...");

            loadAllScripts(() => {
                if (typeof ZoomMtg !== 'undefined') {
                    console.log("Zoom: ✅ Zoom SDK is ready.");
                    websdkready();
                } else {
                    console.error("Zoom: ❌ Zoom SDK is undefined. Ensure scripts are loading correctly.");
                }
            });
        }

        /**
         * Start Execution After DOM is Fully Loaded
         */
        document.addEventListener("DOMContentLoaded", () => {
            console.log("Zoom: 🔥 DOMContentLoaded Event Fired: Initializing Zoom SDK...");
            initZoomEnv();
        });
    </script>


</body>
</html>