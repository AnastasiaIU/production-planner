import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { API_ENDPOINTS } from '@/utils/config'

export const useProdPlanStore = defineStore('productionPlan', () => {
    const plans = ref([])
    const currentPlan = ref(null)

    async function fetchAll(userId) {
        const response = await axios.get(`${API_ENDPOINTS.getProdPlans}`, userId)
        plans.value = response.data
    }

    async function fetchOne(planId) {
        const response = await axios.get(`${API_ENDPOINTS.getProdPlan}`, planId)
        currentPlan.value = response.data
    }

    return { plans, currentPlan, fetchAll, fetchOne }
})