<template>
    <section class="card col d-flex flex-column m-0 p-0 flex-grow-1 overflow-scroll">
        <section class="p-3 d-flex flex-column flex-grow-1">
            <section id="productionGraph" class="d-flex flex-column flex-grow-1 p-0 gap-5"></section>
        </section>
    </section>
</template>

<style scope>
.overflow-scroll::-webkit-scrollbar {
    display: none; /* Hide scrollbar for Chrome, Safari, and Opera */
}

.overflow-scroll {
    -ms-overflow-style: none;  /* Hide scrollbar for Internet Explorer and Edge */
    scrollbar-width: none;  /* Hide scrollbar for Firefox */
}

#productionGraph {
    overflow-y: auto;
    overflow-x: auto;
}
</style>