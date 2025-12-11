/** @format */

function generateSignature(
  ZoomMtg,
  meetingNumber,
  passcode,
  userName,
  userEmail,
  sdkKey,
  sdkSecret,
  role = 0
) {
  return new Promise((resolve, reject) => {
    // Use Zoom's built-in function to generate the SDK signature
    ZoomMtg.generateSDKSignature({
      meetingNumber: meetingNumber,
      sdkKey: sdkKey,
      sdkSecret: sdkSecret,
      role: role,
      success: function (res) {
        console.log("Signature generated successfully:", res);

        const meetingConfig = {
          mn: meetingNumber,
          name: userName,
          email: userEmail,
          pwd: passcode,
          pcd: passcode,
          role: role,
          signature: res.result,
        };

        resolve(meetingConfig); // Resolve the promise with meetingConfig
      },
      error: function (err) {
        console.error("Failed to generate signature:", err);
        reject(err); // Reject the promise with the error
      },
    });
  });
}

module.exports = generateSignature;
