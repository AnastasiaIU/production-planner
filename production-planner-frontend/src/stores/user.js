import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { API_ENDPOINTS } from '@/utils/config'

export const useUserStore = defineStore('pinia-user', () => {
    const users = ref([])

    async function fetchAll() {
        const response = await axios.get(API_ENDPOINTS.crUser)
        users.value = response.data
    }

    async function create(userData) {
        console.log(users.value)
        console.log(users.value[0])
        const response = await axios.post(API_ENDPOINTS.crUser, userData)
        users.value.push(response.data)
        console.log(response.data)
        return response
    }

    async function update(userId, userData) {
        const response = await axios.put(API_ENDPOINTS.udUser(userId), userData)
        const index = users.value.findIndex(p => p.id === userId)
        if (index !== -1) users.value[index] = response.data
        return response
    }

    async function remove(userId) {
        await axios.delete(API_ENDPOINTS.udUser(userId))
        users.value = users.value.filter(p => p.id !== userId)
    }

    return { users, fetchAll, create, update, remove }
})