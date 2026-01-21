import axios from 'axios';

const apiClient = axios.create({
    baseURL: "",
    timeout: 10000,
    withCredentials: true, // Include cookies in requests
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
    },
});

// Add request interceptor to include CSRF token
apiClient.interceptors.request.use((config) => {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token;
    }
    return config;
});

export default apiClient;
