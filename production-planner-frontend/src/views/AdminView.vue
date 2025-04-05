<script setup>
import AdminSidebar from '../components/AdminSidebar.vue';
</script>

<template>
    <main class="container-fluid d-flex flex-column flex-grow-1 align-items-center p-0">
        <div class="container-fluid h-100">
            <div class="row h-100">
                <AdminSidebar></AdminSidebar>
                <section class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                    <RouterView />
                </section>
            </div>
        </div>
    </main>
</template>