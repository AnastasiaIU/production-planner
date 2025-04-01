<script setup>
import LoadingSpinner from '../components/LoadingSpinner.vue';
import ProductionGraph from '../components/ProductionGraph.vue';
import OutputsList from '../components/OutputsList.vue';
</script>

<template>
    <main class="container-fluid d-flex flex-column flex-grow-1 p-0">
        <LoadingSpinner></LoadingSpinner>
        <section class="row flex-grow-1 column-gap-3 gap-3 m-3 p-0">
            <ProductionGraph></ProductionGraph>
            <OutputsList></OutputsList>
        </section>
    </main>
</template>