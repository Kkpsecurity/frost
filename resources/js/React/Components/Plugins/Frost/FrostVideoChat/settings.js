import apiClient from "../../../../Config/axios";

/**
 * Get Laravel setting Based of student view
 * @param {*} loggedInUserId
 * @param {*} course_date_id
 * @returns
 */
const fetchSiteSettings = async (loggedInUserId, course_date_id) => {
    console.log(`fetchSiteSettings: ${loggedInUserId} ${course_date_id}`);
    try {
        const response = await apiClient.get(
            `frost/data/${loggedInUserId}/${course_date_id}`
        );
        return response.data;
    } catch (error) {
        console.error("Failed to get site settings", error);
        return {};
    }
};

/**
 * Get Laravel setting Based of admin view
 * @returns
 */
const fetchAdminSettings = async () => {
    console.log(`fetchAdminSettings`);
    try {
        const response = await apiClient.get(`admin/frost/data/`);
        return response.data;
    } catch (error) {
        console.error("Failed to get admin settings", error);
        return {};
    }
};

/**
 * Get Agora Token and Channel Name
 * @param {*} loggedInUserId
 * @param {*} course_date_id
 * @param {*} type
 */
const initAgora = async (loggedInUserId, course_date_id, type) => {
    console.log(`initAgora: ${loggedInUserId} ${course_date_id} ${type}`);

    /**
     * Validate the pararm are preset
     */
    if (!loggedInUserId || !course_date_id || !type) {
        console.error(
            "Missing loggedInUserId, course_date_id, type",
            loggedInUserId,
            course_date_id,
            !type
        );
        return {
            agoraConfig: null,
        };
    }

    let siteSettings = {};

    /**
     * Fetch the settings based on the type
     */
    if (type === "instructor") {
        siteSettings = await fetchAdminSettings();
    } else {
        siteSettings = await fetchSiteSettings(loggedInUserId, course_date_id);
    }

    console.log("Site Settings:", siteSettings);
    /**
     * Validate the settings are preset
     */
    if (
        !siteSettings.config?.agora?.app_id ||
        !siteSettings.config?.agora?.rtm?.endpoint
    ) {
        console.log(`Agora Site Settings: ${JSON.stringify(siteSettings)}`);
        return {
            agoraConfig: null,
        };
    }

    /**
     * Prepare the url to fetch the token and channel name
     */
    const agoraAppId = siteSettings.config.agora.app_id;
    const tokenServerUrl = siteSettings.config.agora.rtm.endpoint;

    const body = {
        course_date_id: course_date_id.toString(),
        user_id: loggedInUserId.toString(),
        role: type === "instructor" ? "publisher" : "subscriber",
    };

    const tokenServerRequestUrl = `${tokenServerUrl}`;

    try {
        console.log(`Agora Token Request: ${tokenServerRequestUrl}`, body);
        const response = await fetch(tokenServerRequestUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(body),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const agoraRes = await response.json();

        console.log(`Agora Token: ${JSON.stringify(agoraRes)}`);
        if (!agoraRes.token || !agoraRes.channelName) {
            throw new Error("Error retrieving token data");
        }

        const agoraConfig = {
            mode: "rtc",
            codec: "vp8",
            appID: agoraAppId,
            channelName: agoraRes.channelName,
            token: agoraRes.token,
        };

        return { agoraConfig };
    } catch (error) {
        console.error("Failed to get Agora token", error);
        return {
            agoraConfig: null,
        };
    }
};
export default initAgora;
