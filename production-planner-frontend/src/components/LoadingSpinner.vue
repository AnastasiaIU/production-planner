<script setup>
import { useSpinnerStore } from '@/stores/loadingSpinner';

const loadingSpinner = useSpinnerStore();
</script>

<template>
    <div id="loadingSpinner" v-if="loadingSpinner.isLoading">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <span role="status" class="text-center">Calculating your graph.<br />Please, wait...</span>
    </div>
</template>

<style scoped>
#loadingSpinner {
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(255, 255, 255, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
</style>