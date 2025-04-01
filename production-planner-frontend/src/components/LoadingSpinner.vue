<template>
    <div id="loadingSpinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <span role="status" class="text-center">Calculating your graph.<br />Please, wait...</span>
    </div>
</template>

<style scoped>
#loadingSpinner {
    display: none;
    flex-direction: column;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
</style>