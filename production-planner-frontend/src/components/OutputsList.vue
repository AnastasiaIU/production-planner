<script setup>

import axios from "axios"
import { API_ENDPOINTS } from "@/utils/config"
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { Dropdown } from "@/utils/Dropdown.js"
import { getImageUrl } from "@/utils/domHelper.js"
import { useProdPlanStore } from "@/stores/productionPlan"
import { useAuthStore } from "@/stores/auth"
import { useRouter } from 'vue-router'

const router = useRouter()
const itemsRaw = ref({})
const itemsGrouped = ref({})
const isLoading = ref(true)
const error = ref(null)
const dropdownSearch = ref('')
const hasNoElements = ref(false)
const dropdown = new Dropdown()
const planName = ref('')
const prodPlanStore = useProdPlanStore()
const authStore = useAuthStore()

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

onMounted(async () => {
    await fetchItems()
    groupAndSortItems()
    await nextTick()

    const dropdownElement = document.getElementById('outputsDropdown')
    dropdownElement.addEventListener("hide.bs.dropdown", () => {
        if (dropdownSearch.value) {
            dropdownSearch.value = ''
            filterDropdown() // Reset the dropdown items
        }
    })

    if (prodPlanStore.currentPlan) {
        planName.value = prodPlanStore.currentPlan.display_name
        Object.entries(prodPlanStore.currentPlan.items).forEach(async item => {
            const dropdownElement = document.querySelector(`[data-item-id="${item[0]}"]`);
            await dropdown.appendItemToOutputsList(dropdownElement, item[1])
        })
    }
})

onBeforeUnmount(() => {
    const dropdownElement = document.getElementById('outputsDropdown')
    dropdownElement.removeEventListener('hide.bs.dropdown', filterDropdown)

    prodPlanStore.currentPlan = null
})

function filterDropdown() {
    const categories = document.querySelectorAll(".dropdown-header")
    let anyVisible = false

    categories.forEach((category) => {
        const categoryItems = dropdown.getCategoryItems(category)
        let hasVisibleItems = false

        categoryItems.forEach((item) => {
            if (dropdown.getItemVisibility(item, dropdownSearch.value)) hasVisibleItems = true
        })

        dropdown.changeVisibility(category, hasVisibleItems)

        if (hasVisibleItems) anyVisible = true
    })

    hasNoElements.value = !anyVisible
}

async function fetchItems() {
    try {
        const response = await axios.get(API_ENDPOINTS.getProducibleItems)
        itemsRaw.value = response.data
    } catch (e) {
        console.error('Error fetching items:', e)
        error.value = 'Failed to load items.'
    } finally {
        isLoading.value = false
    }
}

function groupAndSortItems() {
    const groupedItems = {}

    // Group items by category
    itemsRaw.value.forEach((item) => {
        const category = item.category || 'Uncategorized'
        if (!groupedItems[category]) groupedItems[category] = []
        groupedItems[category].push(item)
    })

    // Sort items within each category by display_order
    for (const category in groupedItems) {
        groupedItems[category].sort((a, b) => a.display_order - b.display_order)
    }

    // Sort the categories based on the custom order
    const sortedGroupedItems = {}
    categoryOrder.forEach((category) => {
        if (groupedItems[category]) {
            sortedGroupedItems[category] = groupedItems[category]
        }
    })

    // Include any remaining categories not in categoryOrder at the end
    Object.keys(groupedItems)
        .filter((category) => !categoryOrder.includes(category))
        .sort()
        .forEach((remainingCategory) => {
            sortedGroupedItems[remainingCategory] = groupedItems[remainingCategory]
        })

    itemsGrouped.value = sortedGroupedItems
}

function clearPlan() {
    prodPlanStore.currentPlan = null
    planName.value = ''
    dropdown.clearOutputsList()
}

function validateForm(input, prompt, form, message = '', valid = true) {
    prompt.textContent = message
    input.setCustomValidity(valid ? '' : 'error')
    form.classList.add('was-validated')
}

async function handleSubmittion() {
    try {
        const form = document.getElementById('planForm')
        const input = document.getElementById('planName')
        const inputPrompt = document.getElementById('planNamePrompt')
        validateForm(input, inputPrompt, form)

        planName.value = planName.value.trim()
        await nextTick()

        if (planName.value === '') {
            validateForm(input, inputPrompt, form, 'The name cannot be empty.', false)
            return
        }

        const outputsList = document.getElementById('outputsList')

        if (outputsList.children.length === 0) {
            validateForm(input, inputPrompt, form, 'Please add at least one item to the plan.', false)
            return
        }

        const listItems = Array.from(outputsList.querySelectorAll('li'))
        const items = listItems.map(listItem => {
            const itemId = listItem.dataset.itemId
            const input = listItem.querySelector(".quantity-input")

            let quantity = input.value.replace(',', '.')
            quantity = parseFloat(quantity)

            return {
                item_id: itemId,
                amount: quantity
            }
        });

        const planData = {
            created_by: authStore.user.id,
            display_name: planName.value,
            items: items
        }

        if (prodPlanStore.currentPlan) {
            await prodPlanStore.update(prodPlanStore.currentPlan.id, planData)
        } else {
            await prodPlanStore.create(planData)
        }

        router.push('/plans')

    } catch (error) {
        console.error('An error occurred: ', error)
    }
}

</script>

<template>
    <aside class="col-md-4 d-flex flex-column p-0">
        <section class="card d-flex flex-column flex-grow-1">
            <div id="outputsDropdown" class="dropdown d-flex justify-content-between align-items-center p-2">
                <div class="d-flex flex-row flex-wrap text-nowrap gap-3">
                    <p class="h5 my-auto">Outputs</p>
                    <button v-if="authStore.isAuthenticated" type="submit" class="btn btn-primary" id="savePlanBtn"
                        @click="clearPlan">Start new plan</button>
                </div>
                <a id="addItemBtn" class="btn btn-secondary dropdown-toggle text-nowrap" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Add item
                </a>
                <ul id="dropdownMenu" class="dropdown-menu dropdown-menu-end overflow-auto">
                    <li>
                        <form class="px-3 py-2" @submit.prevent>
                            <input v-model="dropdownSearch" type="text" class="form-control"
                                placeholder="Search items..." @keyup="filterDropdown" aria-label="Search items">
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
                        <div v-for="items, category in itemsGrouped" :key="category[0]">
                            <h6 class="dropdown-header">{{ category }}</h6>
                            <li v-for="item in items" :key="item.id"
                                @click="dropdown.appendItemToOutputsList($event.target)">
                                <a class="dropdown-item" :data-item-id="`${item.id}`">
                                    <img :src="getImageUrl(item.icon_name)" alt="" class="list-item-image">{{
                                        item.display_name }}
                                </a>
                            </li>
                        </div>
                    </div>
                </ul>
            </div>
            <hr class="mb-2 mt-0">
            <form class="d-flex flex-column needs-validation" id="planForm" @submit.prevent="handleSubmittion"
                novalidate>
                <div v-if="authStore.isAuthenticated" id="savePlan">
                    <div class="d-flex flex-wrap p-2 pt-0 gap-2 align-items-center justify-content-between">
                        <input type="hidden" name="createPlanId" id="createPlanId">
                        <div class="form-group">
                            <input v-model="planName" type="text" name="planName" class="form-control" id="planName"
                                placeholder="Enter name for the plan" @keydown.enter.prevent aria-label="Plan name"
                                required>
                            <div class="invalid-feedback" id="planNamePrompt">The name cannot be empty.</div>
                        </div>
                        <button type="submit" class="btn btn-primary text-nowrap" id="savePlanBtn">Save plan</button>
                    </div>
                    <hr class="mb-2 mt-0">
                </div>
                <ul id="outputsList" class="list-group flex-grow-1 border-0"></ul>
            </form>
        </section>
    </aside>
</template>

<style>
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

.show-element {
    display: block;
}

.hide-element {
    display: none;
}

.quantity-input {
    width: 120px;
}
</style>