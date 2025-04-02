<script setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'

const nav_items = reactive([
    { to: '/', name: 'Planner', class: 'nav-link active', aria_current: 'page' },
    { to: '/plans', name: 'My Plans', class: 'nav-link', aria_current: '' }
])

const router = useRouter()

function openPage(path) {
    nav_items.forEach(item => {
        if (item.to === path) {
            item.class = 'nav-link active'
            item.aria_current = 'page'
        } else {
            item.class = 'nav-link'
            item.aria_current = ''
        }
    })

    router.push(path)
}
</script>

<template>
    <nav class="navbar nav-underline navbar-expand-lg justify-content-end" data-bs-theme="dark">
        <div class="container-fluid">
            <router-link class="navbar-brand text-white" to="/" @click.prevent="openPage(`/`)">
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
                        <li class="nav-item">
                            <router-link v-bind:class="nav_item.class" v-bind:aria-current="nav_item.aria_current"
                                v-bind:to="nav_item.to" @click.prevent="openPage(nav_item.to)">
                                {{ nav_item.name }}
                            </router-link>
                        </li>
                    </template>
                </ul>
                <div>
                    <router-link type="button" class="btn btn-primary" to="/login" @click.prevent="openPage(`/login`)">
                        Log in
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