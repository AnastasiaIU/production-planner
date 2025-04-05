<script setup>
import { useRoute } from 'vue-router'

const route = useRoute()
</script>

<template>
    <div class="sidebar border border-right col-md-3 col-lg-2 p-0 bg-body-tertiary">
        <div class="offcanvas-md offcanvas-end bg-body-tertiary" tabindex="-1" id="sidebarMenu"
            aria-labelledby="sidebarMenuLabel">
            <div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <router-link
                            :class="['nav-link', 'd-flex', 'align-items-center', 'gap-2', route.path === '/admin-panel/users' ? 'active' : '']"
                            to="/admin-panel/users" :aria-current="route.path === '/admin-panel/users' ? 'page' : ''">
                            Users
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link
                            :class="['nav-link', 'd-flex', 'align-items-center', 'gap-2', route.path === '/admin-panel/pictures' ? 'active' : '']"
                            to="/admin-panel/pictures" :aria-current="route.path === '/admin-panel/pictures' ? 'page' : ''">
                            Pictures
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link
                            :class="['nav-link', 'd-flex', 'align-items-center', 'gap-2', route.path === '/admin-panel/json' ? 'active' : '']"
                            to="/admin-panel/json" :aria-current="route.path === '/admin-panel/json' ? 'page' : ''">
                            JSON
                        </router-link>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</template>

<style scoped>
.nav-link {
    color: black;
    text-decoration: none;
}

a.active {
    font-weight: bold;
}
</style>