<template>
    <section class="card col d-flex flex-column m-0 p-0 flex-grow-1 overflow-scroll">
        <section class="p-3 d-flex flex-column flex-grow-1">
            <section id="productionGraph" class="d-flex flex-column flex-grow-1 p-0 gap-5"></section>
        </section>
    </section>
</template>

<style>
.overflow-scroll::-webkit-scrollbar {
    display: none;
    /* Hide scrollbar for Chrome, Safari, and Opera */
}

.overflow-scroll {
    -ms-overflow-style: none;
    /* Hide scrollbar for Internet Explorer and Edge */
    scrollbar-width: none;
    /* Hide scrollbar for Firefox */
}

#productionGraph {
    overflow-y: auto;
    overflow-x: auto;
}

.graph-element {
    max-width: 80px;
}

.graph-image {
    height: 80px;
    width: 80px;
    padding: 4px;
    background: white;
}

.square {
    border-radius: 5%;
    border: var(--color-blue) 2px solid;
}

.circle {
    border-radius: 50%;
    border: var(--color-grey) 2px solid;
}

.arrow {
    font-size: 2rem;
    margin: 0 10px;
}

.output {
    border: var(--color-green) 2px solid;
}

.output-container {
    padding: 8px;
    background-color: var(--color-pale-green);
    border-radius: 5px;
}

.input-container {
    padding: 16px 16px 16px 0;
    background-color: var(--color-light-blue);
    border-radius: 5px;
}

.input-inner-container {
    padding: 10px 10px 10px 0;
    border-radius: 8px;
    align-items: end !important;
    border-right: 3px solid var(--color-blue);
}
</style>