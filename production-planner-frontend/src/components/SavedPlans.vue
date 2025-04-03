<script setup>

import { authStore } from "@/stores/auth"
import { useRouter } from 'vue-router'

const router = useRouter()
const authStore = useAuthStore()

</script>

<template>
    <section class="card col-lg-8 col-xl-8 p-3 m-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <p class="h5 m-0 mb-1 dark-grey-text">Saved plans</p>
            <a class="btn btn-secondary mb-1" id="importBtn">Import from JSON</a>
        </div>
        <ul id="plansList" class="list-group flex-grow-1 border-0 gap-3"></ul>
    </section>
</template>