import axios from "axios";
import { useEffect, useState } from "react";

/**
 * Decodes some basic element dealing with json strings
 * @param {*} str
 * @returns
 */
export const htmlEntities = (str) => {
    return String(str)
        .replace(/&amp;/g, "&")
        .replace(/&lt;/g, "<")
        .replace(/&gt;/g, ">")
        .replace(/&quot;/g, '"');
};

/**
 *
 * @param {*} needle
 * @param {*} haystack
 * @returns
 */
export const inArray = (needle, haystack) => {
    var length = haystack.length;
    for (var i = 0; i < length; i++) {
        if (haystack[i] == needle) return true;
    }

    return false;
};

/**
 *
 * @param {*} str
 * @param {*} seed
 * @returns
 */
export const Hash = (str, seed = 0) => {
    let h1 = 0xdeadbeef ^ seed,
        h2 = 0x41c6ce57 ^ seed;

    for (let i = 0, ch; i < str.length; i++) {
        ch = str.charCodeAt(i);
        h1 = Math.imul(h1 ^ ch, 2654435761);
        h2 = Math.imul(h2 ^ ch, 1597334677);
    }

    h1 =
        Math.imul(h1 ^ (h1 >>> 16), 2246822507) ^
        Math.imul(h2 ^ (h2 >>> 13), 3266489909);
    h2 =
        Math.imul(h2 ^ (h2 >>> 16), 2246822507) ^
        Math.imul(h1 ^ (h1 >>> 13), 3266489909);
    return 4294967296 * (2097151 & h2) + (h1 >>> 0);
};

/**
 * Remove The HTTP or HTTPs Form URL
 * @param {*} url
 */
export const removeHttp = (url) => {
    const h = ["http://", "https://"];
    h.forEach((ht) => {
        if (url.startsWith(ht)) {
            return url.replace(ht, "");
        } else {
            return url;
        }
    });
};

/**
 * Check if the file exist
 * @param fileUrl
 * @returns
 */
export const fileExist = async (fileUrl: string) => {
    try {
        const response = await axios.head(fileUrl);
        console.log("fileExist: ", response.status, response.headers["content-length"], "");
        if (response.status === 200 && Number(response.headers["content-length"]) > 0 && response.headers["content-type"] === "image/png") {
            return true;
        } else {
            return false;
        }

    } catch (error) {
        return false;
    }
}


/**
 * return formated time
 * @param totalSeconds
 * @returns
 */
export const toHHMMSS = (totalSeconds: number) => {
    var sec_num = totalSeconds;
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    return hours+':'+minutes+':'+seconds;
}

export const formatPhone = (phone: string | null) => {
    if (phone) {
        return phone.replace(/(\d{3})(\d{3})(\d{4})/, "($1) $2-$3");
    }

    return "";
};


