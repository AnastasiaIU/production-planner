<script setup>

import { reactive, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from "@/stores/auth"

const nav_items = reactive([
    { to: '/', name: 'Planner', class: 'nav-link active', aria_current: 'page' },
    { to: '/plans', name: 'My Plans', class: 'nav-link', aria_current: '' },
    { to: '/admin-panel', name: 'Manage', class: 'nav-link', aria_current: '' }
])

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

watch(() => route.path, (newPath) => {
    nav_items.forEach(item => {
        if (
            (item.to === '/' && newPath === '/') || // exact match for homepage
            (item.to !== '/' && newPath.startsWith(item.to)) // startsWith for other routes
        ) {
            item.class = 'nav-link active'
            item.aria_current = 'page'
        } else {
            item.class = 'nav-link'
            item.aria_current = ''
        }
    })
}, { immediate: true })

function handleLogout() {
    authStore.logout()
    router.push('/login')
}
</script>

<template>
    <nav class="navbar nav-underline navbar-expand-lg justify-content-end" data-bs-theme="dark">
        <div class="container-fluid">
            <router-link class="navbar-brand text-white" to="/">
                <img src="/src/assets/images/ficsit-checkmarktm_64.png" class="img-fluid logo me-1"
                    alt="Logo FICSIT Checkmark">
                <span class="h5 align-middle">Production Planner</span>
            </router-link>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between" id="navbarSupportedContent">
                <ul class="navbar-nav mb-2 mb-lg-0 mx-3 gap-2">
                    <template v-for="nav_item in nav_items">
                        <li class="nav-item" v-if="(nav_item.name !== 'My Plans' || authStore.isAuthenticated)
                            && (nav_item.name !== 'Manage' || authStore.user?.role === 'Admin')">
                            <router-link v-bind:class="nav_item.class" v-bind:aria-current="nav_item.aria_current"
                                v-bind:to="nav_item.to">{{ nav_item.name }}
                            </router-link>
                        </li>
                    </template>
                </ul>
                <div>
                    <router-link v-if="!authStore.isAuthenticated" type="button" class="btn btn-primary" to="/login">
                        Log in
                    </router-link>
                    <router-link v-else type="button" class="btn btn-primary" to="/login" @click.prevent="handleLogout">
                        Log out
                    </router-link>
                </div>
            </div>
        </div>
    </nav>
</template>

<style scoped>
nav {
    background-color: var(--color-ficsit-orange);
}

.logo {
    max-width: 100%;
    height: 32px;
}

.nav-link {
    font-weight: bold;
    color: white;
    text-decoration: none;
}
</style>