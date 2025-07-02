// FrostPlayer.js

class FrostPlayer {
    constructor(options) {
        this.meetingNumber = options.meetingNumber;
        this.userName = options.userName;
        this.userEmail = options.userEmail;
        this.clientID = options.clientID;
        this.clientSecret = options.clientSecret;
        this.password = options.password;
        this.passcode = options.passcode;
        this.courseAuthId = options.courseAuthId;
        this.courseDateId = options.courseDateId;
        this.zoomVersion = options.zoomVersion;
        this.onReady = options.onReady;
        this.onMessage = options.onMessage;
    }

    init() {
        this.onMessage('FrostPlayer init started');
        const validationError = this.validateConfig();
        if (validationError) {
            this.onMessage(`Validation Error: ${validationError}`);
            return;
        }

        this.onMessage('Starting script loading process...');
        this.loadScripts()
            .then(() => {
                this.onMessage('Scripts loaded successfully.');
                return this.generateSignature();
            })
            .then(signature => {
                this.onMessage('Signature generated successfully.');
                return this.startMeeting(signature);
            })
            .catch(error => {
                this.onMessage(`Error: ${error.message}`);
            });
    }

    validateConfig() {
        this.onMessage('Validating configuration...');
        if (!this.meetingNumber) return 'Missing meeting number.';
        if (!this.userName) return 'Missing user name.';
        if (!this.userEmail) return 'Missing user email.';
        if (!this.clientID) return 'Missing client ID.';
        if (!this.clientSecret) return 'Missing client secret.';
        if (!this.password) return 'Missing password.';
        if (!this.passcode) return 'Missing passcode.';
        if (!this.courseAuthId) return 'Missing course authentication ID.';
        if (!this.courseDateId) return 'Missing course date ID.';
        if (!this.zoomVersion) return 'Missing Zoom SDK version.';
        return null;
    }

    loadScripts() {
        const scripts = [
            `https://source.zoom.us/${this.zoomVersion}/lib/vendor/react.min.js`,
            `https://source.zoom.us/${this.zoomVersion}/lib/vendor/react-dom.min.js`,
            `https://source.zoom.us/${this.zoomVersion}/lib/vendor/redux.min.js`,
            `https://source.zoom.us/${this.zoomVersion}/lib/vendor/redux-thunk.min.js`,
            `https://source.zoom.us/${this.zoomVersion}/zoom-meeting-${this.zoomVersion}.min.js`
        ];

        return scripts.reduce((promise, src) => {
            return promise.then(() => this.loadScript(src));
        }, Promise.resolve());
    }

    loadScript(src) {
        this.onMessage(`Loading script: ${src}`);
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.onload = () => {
                this.onMessage(`Script loaded successfully: ${src}`);
                resolve();
            };
            script.onerror = () => {
                this.onMessage(`Failed to load script: ${src}`);
                reject(new Error(`Failed to load script: ${src}`));
            };
            document.body.appendChild(script);
        });
    }

    generateSignature() {
        this.onMessage('Generating SDK signature...');
        return new Promise((resolve, reject) => {
            ZoomMtg.generateSDKSignature({
                meetingNumber: this.meetingNumber,
                sdkKey: this.clientID,
                sdkSecret: this.clientSecret,
                role: 0,
                success: (res) => {
                    this.onMessage('SDK signature generated successfully.');
                    resolve(res.result);
                },
                error: (err) => {
                    this.onMessage('Failed to generate SDK signature.');
                    reject(err);
                }
            });
        });
    }

    startMeeting(signature) {
        this.onMessage('Initializing Zoom meeting...');
        ZoomMtg.init({
            leaveUrl: `/classroom/portal/zoom/screen_share/${this.courseAuthId}/${this.courseDateId}`,
            success: () => {
                ZoomMtg.join({
                    meetingNumber: this.meetingNumber,
                    userName: this.userName,
                    signature: signature,
                    sdkKey: this.clientID,
                    userEmail: this.userEmail,
                    passWord: this.passcode,
                    success: (res) => {
                        this.onMessage('Joined Zoom meeting successfully.');
                        this.onReady();
                    },
                    error: (res) => {
                        this.onMessage('Failed to join Zoom meeting.');
                    }
                });
            },
            error: (res) => {
                this.onMessage('Failed to initialize Zoom meeting.');
            }
        });
    }
}

// Attach the class to the global `window` object
window.FrostPlayer = FrostPlayer;
