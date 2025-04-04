import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useSpinnerStore = defineStore('pinia-loading-spinner', () => {
    const isLoading = ref(false)

    function show() {
        isLoading.value = true
    }

    function hide() {
        setTimeout(() => isLoading.value = false, 1000)
    }

    return { isLoading, show, hide }
})