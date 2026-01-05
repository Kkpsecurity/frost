<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Live Classroom - Screen Share</title>

    {{-- Zoom Meeting SDK CSS --}}
    <link type="text/css" rel="stylesheet" href="https://source.zoom.us/3.8.10/css/bootstrap.css" />
    <link type="text/css" rel="stylesheet" href="https://source.zoom.us/3.8.10/css/react-select.css" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #1a1a2e;
            color: #fff;
            overflow: hidden;
        }

        #zoom-container {
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f0f1e;
        }

        .zoom-status {
            text-align: center;
            padding: 2rem;
            max-width: 500px;
        }

        .zoom-status h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #3498db;
        }

        .zoom-status p {
            font-size: 1rem;
            color: #bdc3c7;
            margin-bottom: 1rem;
        }

        .zoom-error {
            color: #e74c3c;
        }

        .zoom-waiting {
            color: #f39c12;
        }

        .loading-spinner {
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            border-top: 3px solid #3498db;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 1rem auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .zoom-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Override Zoom SDK styles to ensure full viewport */
        #zmmtg-root {
            width: 100% !important;
            height: 100% !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
        }
    </style>
</head>
<body>
    <div id="zoom-container">
        @if($error)
            <div class="zoom-status">
                <div class="zoom-icon">📹</div>
                <h2 class="{{ $zoom ? 'zoom-waiting' : 'zoom-error' }}">
                    {{ $zoom ? 'Waiting for Live Session' : 'Connection Error' }}
                </h2>
                <p>{{ $error }}</p>
                @if($zoom && $zoom['status'] === 'disabled')
                    <div class="loading-spinner"></div>
                    <p style="font-size: 0.875rem; color: #7f8c8d; margin-top: 1rem;">
                        The instructor will start the session shortly. This page will automatically connect when ready.
                    </p>
                @endif
            </div>
        @else
            <div class="zoom-status">
                <div class="loading-spinner"></div>
                <h2>Connecting to Live Session...</h2>
                <p>Please wait while we connect you to the classroom</p>
            </div>
        @endif
    </div>

    {{-- Zoom Meeting SDK JS --}}
    <script src="https://source.zoom.us/3.8.10/lib/vendor/react.min.js"></script>
    <script src="https://source.zoom.us/3.8.10/lib/vendor/react-dom.min.js"></script>
    <script src="https://source.zoom.us/3.8.10/lib/vendor/redux.min.js"></script>
    <script src="https://source.zoom.us/3.8.10/lib/vendor/redux-thunk.min.js"></script>
    <script src="https://source.zoom.us/3.8.10/lib/vendor/lodash.min.js"></script>
    <script src="https://source.zoom.us/zoom-meeting-3.8.10.min.js"></script>

    <script>
        // Zoom configuration from Laravel
        const zoomConfig = @json($zoom);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        console.log('Zoom Screen Share Portal Initialized', {
            isReady: zoomConfig?.is_ready,
            status: zoomConfig?.status,
            hasSdkKey: !!zoomConfig?.sdk_key,
            hasMeetingNumber: !!zoomConfig?.meeting_number,
        });

        // Initialize Zoom Meeting SDK
        const { ZoomMtg } = window;

        ZoomMtg.setZoomJSLib('https://source.zoom.us/3.8.10/lib', '/av');
        ZoomMtg.preLoadWasm();
        ZoomMtg.prepareWebSDK();

        // Disable "Join Audio by Computer" dialog
        ZoomMtg.i18n.load('en-US');
        ZoomMtg.i18n.reload('en-US');

        /**
         * Join Zoom meeting with signature authentication
         */
        async function joinMeeting() {
            if (!zoomConfig || !zoomConfig.is_ready) {
                console.warn('Zoom not ready, waiting...');
                return;
            }

            try {
                // Generate signature via Laravel API
                const signatureResponse = await fetch(zoomConfig.signature_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        meeting_number: zoomConfig.meeting_number,
                        role: 0, // 0 = participant, 1 = host
                    }),
                });

                if (!signatureResponse.ok) {
                    throw new Error(`Signature generation failed: ${signatureResponse.status}`);
                }

                const signatureData = await signatureResponse.json();

                if (!signatureData.success || !signatureData.signature) {
                    throw new Error(signatureData.message || 'Failed to generate signature');
                }

                console.log('Signature generated successfully');

                // Join the meeting
                ZoomMtg.init({
                    leaveUrl: window.location.origin + '/classroom',
                    success: function(initResult) {
                        console.log('Zoom SDK initialized', initResult);

                        ZoomMtg.join({
                            meetingNumber: zoomConfig.meeting_number,
                            userName: zoomConfig.user_name,
                            signature: signatureData.signature,
                            sdkKey: zoomConfig.sdk_key,
                            passWord: zoomConfig.meeting_passcode || '',
                            success: function(joinResult) {
                                console.log('Successfully joined meeting', joinResult);
                            },
                            error: function(joinError) {
                                console.error('Failed to join meeting', joinError);
                                showError('Failed to join the live session. Please refresh the page.');
                            },
                        });
                    },
                    error: function(initError) {
                        console.error('Failed to initialize Zoom SDK', initError);
                        showError('Failed to initialize the meeting client. Please refresh the page.');
                    },
                });

            } catch (error) {
                console.error('Error joining Zoom meeting:', error);
                showError(`Connection error: ${error.message}`);
            }
        }

        /**
         * Display error message
         */
        function showError(message) {
            const container = document.getElementById('zoom-container');
            container.innerHTML = `
                <div class="zoom-status">
                    <div class="zoom-icon">⚠️</div>
                    <h2 class="zoom-error">Connection Failed</h2>
                    <p>${message}</p>
                    <button onclick="location.reload()"
                            style="margin-top: 1rem; padding: 0.75rem 1.5rem; background: #3498db;
                                   border: none; border-radius: 4px; color: white; cursor: pointer;
                                   font-size: 1rem;">
                        Retry Connection
                    </button>
                </div>
            `;
        }

        /**
         * Auto-retry logic when Zoom is disabled
         */
        let retryInterval = null;

        if (zoomConfig && !zoomConfig.is_ready && zoomConfig.status === 'disabled') {
            console.log('Zoom disabled, will poll for status updates...');

            // Poll every 10 seconds to check if instructor has enabled Zoom
            retryInterval = setInterval(function() {
                console.log('Checking Zoom status...');
                // Refresh the page to get updated status
                location.reload();
            }, 10000); // 10 seconds
        } else if (zoomConfig && zoomConfig.is_ready) {
            // Zoom is ready, join immediately
            console.log('Zoom ready, joining meeting...');
            joinMeeting();
        } else if (!zoomConfig) {
            // No zoom config at all (authentication error, etc.)
            console.error('No Zoom configuration available');
        }

        // Clean up interval on page unload
        window.addEventListener('beforeunload', function() {
            if (retryInterval) {
                clearInterval(retryInterval);
            }
        });
    </script>
</body>
</html>
