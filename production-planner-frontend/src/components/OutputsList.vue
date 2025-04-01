<script setup>

import axios from "axios"
import { API_ENDPOINTS } from "@/utils/config"
import { ref, onMounted } from 'vue'

// The order in which categories should be displayed in the dropdown
const categoryOrder = [
    'Raw Resources',
    'Tier 0',
    'Tier 2',
    'Tier 3',
    'Tier 4',
    'Tier 5',
    'Tier 6',
    'Tier 7',
    'Tier 8',
    'Tier 9',
    'MAM',
    'Equipment'
]

const dropdownSearch = ref('')
const hasNoElements = ref(false)
const isLoading = ref(true)
const error = ref(null)
const producibleItemsRaw = ref({})
const producibleItems = ref({})

async function getProducibleItems() {
    try {
        const results = await axios.get(API_ENDPOINTS.producibleItems)
        producibleItemsRaw.value = results.data
    } catch (error) {
        console.error(error)
        error.value = "An error occurred. Try to refresh the page."
    } finally {
        isLoading.value = false
    }
}

/**
 * Groups and sorts producible items by category and display order.
 */
function groupAndSortItems() {
    const groupedItems = {}

    // Group items by category
    producibleItemsRaw.value.forEach((item) => {
        const category = item.category || 'Uncategorized'
        if (!groupedItems[category]) groupedItems[category] = []
        groupedItems[category].push(item)
    });

    // Sort items within each category by display_order
    for (const category in groupedItems) {
        groupedItems[category].sort((a, b) => a.display_order - b.display_order)
    }

    // Sort the categories based on the custom order
    const sortedGroupedItems = {};
    categoryOrder.forEach((category) => {
        if (groupedItems[category]) {
            sortedGroupedItems[category] = groupedItems[category]
        }
    });

    // Include any remaining categories not in categoryOrder at the end
    Object.keys(groupedItems)
        .filter((category) => !categoryOrder.includes(category))
        .sort()
        .forEach((remainingCategory) => {
            sortedGroupedItems[remainingCategory] = groupedItems[remainingCategory]
        });

    producibleItems.value = sortedGroupedItems
}

function filterDropdown() {
    const categories = document.querySelectorAll(".dropdown-header")

    categories.forEach((category) => {
        const categoryItems = getCategoryItems(category)
        let hasVisibleItems = false

        categoryItems.forEach((item) => {
            if (getItemVisibility(item, dropdownSearch.toLowerCase())) hasVisibleItems = true
        })

        changeVisibility(category, hasVisibleItems)

        hasNoElements.value = !hasVisibleItems
    })
}

/**
 * Retrieves all items under a given category element.
 *
 * @param {HTMLElement} category The category element whose items are to be retrieved.
 * @returns {HTMLElement[]} An array of item elements under the specified category.
 */
function getCategoryItems(category) {
    const categoryItems = [];
    let nextElement = category.nextElementSibling;

    // Collect all items under this category
    while (nextElement && nextElement.tagName === 'LI') {
        categoryItems.push(nextElement);
        nextElement = nextElement.nextElementSibling;
    }

    return categoryItems;
}

function getImageUrl(fileName) {
    return new URL(`../assets/images/${fileName}`, import.meta.url).href
}

onMounted(async () => {
    await getProducibleItems()
    groupAndSortItems()
})

</script>

<template>
    <aside class="col-md-4 d-flex flex-column p-0">
        <section class="card d-flex flex-column flex-grow-1">
            <div id="outputsDropdown" class="dropdown d-flex justify-content-between align-items-center p-2"
                @hide.bs.dropdown="filterDropdown">
                <p class="h5 m-0">Outputs</p>
                <a id="addItemBtn" class="btn btn-secondary dropdown-toggle" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Add item
                </a>
                <ul id="dropdownMenu" class="dropdown-menu dropdown-menu-end overflow-auto">
                    <li>
                        <form class="px-3 py-2">
                            <input v-model="dropdownSearch" type="text" class="form-control"
                                placeholder="Search items..." @keyup.up="filterDropdown" aria-label="Search items">
                        </form>
                    </li>
                    <li>
                        <hr class="dropdown-divider mb-0">
                    </li>
                    <div id="dropdownItems">
                        <div v-if="error" class="text-center text-red mt-2">
                            <p>{{ error }}</p>
                        </div>
                        <div v-if="isLoading" class="text-center text-muted mt-2">
                            <p>Loading...</p>
                        </div>
                        <div v-if="hasNoElements" class="text-center text-muted mt-2">
                            <p>No results found.</p>
                        </div>
                        <div v-for="items, category in producibleItems" :key="category[0]">
                            <h6 class="dropdown-header">{{ category }}</h6>
                            <li v-for="item in items" :key="item.id">
                                <a class="dropdown-item" :data-item-id="`${item.id}`">
                                    <img :src="getImageUrl(item.icon_name)" alt="" class="list-item-image">{{ item.display_name }}
                                </a>
                            </li>
                        </div>
                    </div>
                </ul>
            </div>
            <hr class="mb-2 mt-0">
            <form class="d-flex flex-column needs-validation" id="planForm" method="post" novalidate>
                <div id="savePlan">
                    <div class="d-flex flex-wrap p-2 pt-0 gap-2 align-items-center justify-content-between">
                        <input type="hidden" name="createPlanId" id="createPlanId">
                        <div class="form-group">
                            <input type="text" name="planName" class="form-control" id="planName"
                                placeholder="Enter name for the plan" aria-label="Plan name" required>
                            <div class="invalid-feedback" id="planNamePrompt"></div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="savePlanBtn">Create new plan</button>
                    </div>
                    <hr class="mb-2 mt-0">
                </div>
                <ul id="outputsList" class="list-group flex-grow-1 border-0"></ul>
            </form>
        </section>
    </aside>
</template>

<style scoped>
.dropdown-menu {
    max-height: 45vh;
}

.dropdown-header {
    font-size: 1.2rem;
    color: white;
    padding: 0.5rem 1rem;
    margin-top: 0;
    background: var(--color-grey);
}

.list-item-image {
    height: 50px;
    width: 50px;
    margin-right: 10px;
}
</style>