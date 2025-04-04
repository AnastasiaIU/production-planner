<script setup>

import { useAuthStore } from "@/stores/auth"
import { useRouter } from 'vue-router'
import { useProdPlanStore } from "@/stores/productionPlan"
import { ref, onMounted } from 'vue'

const router = useRouter()
const authStore = useAuthStore()
const prodPlanStore = useProdPlanStore()
const showToast = ref(false)
const toastMessage = ref('')
const isError = ref(false)

onMounted(async () => {
    try {
        const user = await authStore.fetchUser()
        await prodPlanStore.fetchAll(user.data.id)
    } catch (error) {
        console.error('An error occurred:', error)
    }
})

async function handleSubmittion() {
    try {
        await prodPlanStore.remove(prodPlanStore.currentPlan.id)

        toastMessage.value = 'Plan deleted successfully!'
        isError.value = false

    } catch (error) {
        console.error('An error occurred:', error)

        isError.value = true
        toastMessage.value = 'An error occurred while deleting the plan.'

    } finally {
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'))
        modal?.hide()

        showToast.value = true

        setTimeout(() => {
            showToast.value = false
            isError.value = false
            toastMessage.value = ''
        }, 3000)
    }
}

function viewPlan(plan) {
    prodPlanStore.setCurrentPlan(plan)
    router.push('/')
}

</script>

<template>
    <section class="card col-12 col-md-8 d-flex">
        <div class="card-header">
            <div class="d-flex w-100 justify-content-between flex-wrap gap-2">
                <div class="d-flex">
                    <p class="h5 dark-grey-text my-auto">Saved plans</p>
                </div>
                <a class="btn btn-secondary mb-1" id="importBtn">Import from JSON</a>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-3 justify-content-between p-3 w-100">
            <div v-if="!prodPlanStore.plans" class="alert alert-light m-0" role="alert">No plans found.</div>
            <div v-else v-for="plan in prodPlanStore.plans" id="plansList"
                class="card d-flex flex-row flex-wrap gap-2 justify-content-between p-2 w-100">
                <div class="d-flex">
                    <p class="h6 my-auto">{{ plan.display_name }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-primary text-nowrap" id="viewBtn" @click="viewPlan(plan)">View/Edit</a>
                    <a class="btn btn-success text-nowrap" id="exportBtn">Export in JSON</a>
                    <a class="btn btn-danger text-nowrap" data-bs-toggle="modal" data-bs-target="#deleteModal"
                        @click="prodPlanStore.currentPlan = plan">Delete</a>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this plan?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="delete" @submit.prevent="handleSubmittion()">
                        <input type="hidden" name="planId">
                        <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div v-if="showToast" :class="{ 'toast-error': isError }" class="toast-message">{{ toastMessage }}</div>
</template>

<style>
.dark-grey-text {
    color: var(--dark-grey);
}

.toast-message {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #28a745;
    color: white;
    padding: 12px 20px;
    border-radius: 5px;
    font-size: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    opacity: 1;
    transition: opacity 0.5s ease-out;
    z-index: 1000;
}

.toast-error {
    background-color: #dc3545;
}
</style>