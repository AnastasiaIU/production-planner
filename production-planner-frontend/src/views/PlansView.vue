<script setup>
import SavedPlans from '../components/SavedPlans.vue';
import ModalDialog from '../components/ModalDialog.vue';
</script>

<template>
    <main class="container-fluid d-flex flex-column flex-grow-1 align-items-center p-0">
        <SavedPlans></SavedPlans>
        <ModalDialog></ModalDialog>
    </main>
</template>