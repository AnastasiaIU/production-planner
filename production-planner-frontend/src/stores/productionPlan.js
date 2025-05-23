import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { API_ENDPOINTS } from '@/utils/config'

export const useProdPlanStore = defineStore('pinia-production-plan', () => {
    const plans = ref([])
    const currentPlan = ref(null)

    function setCurrentPlan(plan) {
        currentPlan.value = plan
    }

    async function fetchAll(userId) {
        const response = await axios.get(API_ENDPOINTS.getProdPlansForUser(userId))
        plans.value = response.data
    }

    async function fetchOne(planId) {
        const response = await axios.get(API_ENDPOINTS.rudProdPlan(planId))
        currentPlan.value = response.data
    }

    async function create(planData) {
        const response = await axios.post(API_ENDPOINTS.createProdPlan, planData)
        plans.value.push(response.data)
        return response
    }

    async function update(planId, planData) {
        const response = await axios.put(API_ENDPOINTS.rudProdPlan(planId), planData)
        const index = plans.value.findIndex(p => p.id === planId)
        if (index !== -1) plans.value[index] = response.data
        return response
    }

    async function remove(planId) {
        await axios.delete(API_ENDPOINTS.rudProdPlan(planId))
        plans.value = plans.value.filter(p => p.id !== planId)
        currentPlan.value = null
    }

    return { plans, currentPlan, setCurrentPlan, fetchAll, fetchOne, create, update, remove }
}, { persist: true })