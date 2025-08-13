import axios from "axios";

const baseURL = process.env.REACT_APP_API_BASE_URL || location.protocol + "//" + location.host;
const CSRFElement = document.querySelector('[name="csrf-token"]')
if(!CSRFElement) {
  throw new Error("CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token");
}
const csrfToken = CSRFElement.getAttribute("content");

const apiClient = axios.create({
  baseURL: baseURL,
  headers: {
    "x-csrf-token": csrfToken || "", // Ensure csrfToken is not null
    "Content-type": "application/json",
  },
  timeout: 5000, // 5 seconds
});

// Add error handling
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response) {
      // The request was made and the server responded with a status code
      // that falls out of the range of 2xx
      console.error(
        `API Error: ${error.response.status} - ${error.response.statusText}`
      );
    } else if (error.request) {
      // The request was made but no response was received
      console.error("API Error: No response received");
    } else {
      // Something happened in setting up the request that triggered an Error
      console.error("API Error:", error.message);
    }
    return Promise.reject(error);
  }
);

export default apiClient;
