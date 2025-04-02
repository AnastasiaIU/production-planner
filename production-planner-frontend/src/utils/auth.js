import axios from "axios";

/**
 * Sets or removes the Authorization header in axios defaults
 * @param {string|null} token - The JWT token to set in the Authorization header. If null, removes the header
 */
export const setAuthToken = (token) => {
    if (token) {
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
    } else {
        delete axios.defaults.headers.common["Authorization"];
    }
};

/**
 * Retrieves the authentication token from localStorage
 * @returns {string|null} The stored JWT token or null if no token exists
 */
export const getAuthToken = () => {
    return localStorage.getItem("token");
};