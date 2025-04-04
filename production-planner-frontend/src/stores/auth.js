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
        user.value = response.data
        return response
    }

    async function register(credentials) {
        await axios.post(API_ENDPOINTS.register, credentials)
    }

    async function login(credentials) {
        const response = await axios.post(API_ENDPOINTS.login, credentials)
        token.value = response.data.token
        localStorage.setItem("token", token.value)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
        return response
    }

    function logout() {
        user.value = null
        token.value = null

        localStorage.removeItem("token")

        Object.keys(localStorage)
            .filter(key => key.startsWith('pinia-'))
            .forEach(key => localStorage.removeItem(key))

        delete axios.defaults.headers.common['Authorization']

        localStorage.removeItem("auth")
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
}, { persist: true })