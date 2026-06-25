import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Global Axios Interceptor to handle 401 Unauthenticated / Session Expired responses
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && (error.response.status === 401 || error.response.status === 419)) {
            const data = error.response.data;
            if (data && data.redirect) {
                window.location.href = data.redirect;
            } else {
                window.location.href = '/login';
            }
        }
        return Promise.reject(error);
    }
);
