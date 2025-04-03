import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from "axios";
import { API_ENDPOINTS } from "@/utils/config";
import { computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null)
    const token = ref(null)
    const isAuthenticated = computed(() => !!token.value)

    async function fetchUser() {
        const response = await axios.get(API_ENDPOINTS.me)
        user.value = response.data.user
        return response
    }

    async function register(credentials) {
        await axios.post(API_ENDPOINTS.register, credentials)
    }

    async function login(credentials) {
        const response = await axios.post(API_ENDPOINTS.login, credentials)
        user.value = response.data.user
        token.value = response.data.token
        localStorage.setItem("token", token.value)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
        return response
    }

    function logout() {
        user.value = null
        token.value = null
        localStorage.removeItem("token")
        delete axios.defaults.headers.common['Authorization']
    }

    async function initializeAuth() {
        const storedToken = localStorage.getItem("token")
        if (storedToken) {
            token.value = storedToken
            axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
            try {
                await fetchUser()
            } catch (error) {
                console.error("Failed to fetch user on initialization:", error)
                logout()
            }
        }
    }

    return { user, token, isAuthenticated, fetchUser, register, login, logout, initializeAuth }
})