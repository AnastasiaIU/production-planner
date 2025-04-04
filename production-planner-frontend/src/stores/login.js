import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useLoginStore = defineStore('pinia-login', () => {
    const wasRegistrated = ref(false)

    function setWasRegistrated(value) {
        wasRegistrated.value = value
    }

    return { wasRegistrated, setWasRegistrated }
}, { persist: true })