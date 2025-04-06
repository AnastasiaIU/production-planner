<script setup>

import { ref, onMounted } from 'vue'
import { useUserStore } from '@/stores/user'

const userStore = useUserStore()
const newUser = ref({ email: '', password: '', role: 'Regular' })
const toastMessage = ref('')
const deletingUser = ref(null)
const isError = ref(false)
const showToast = ref(false)

onMounted(async () => {
    try {
        await userStore.fetchAll()
    } catch (error) {
        console.error('An error occurred:', error)
    }
})

async function handleCreate() {
    try {
        newUser.value.email = newUser.value.email.trim()
        newUser.value.password = newUser.value.password.trim()

        const inputEmail = document.getElementById('newUserEmail')
        const inputPassword = document.getElementById('newUserPassword')

        if (newUser.value.email === '') {
            inputEmail.classList.add('is-invalid')
            toastMessage.value = 'Email cannot be empty.'
            displayToast(true)
            return
        }

        if (newUser.value.password === '') {
            inputPassword.classList.add('is-invalid')
            toastMessage.value = 'Password cannot be empty.'
            displayToast(true)
            return
        }

        if (!isValidEmail(newUser.value.email)) {
            inputEmail.classList.add('is-invalid')
            toastMessage.value = 'Please enter a valid email address.'
            displayToast(true)
            return
        }

        await userStore.create(newUser.value)

        toastMessage.value = 'User created successfully!'
        displayToast(false)

    } catch (error) {
        console.error('An error occurred: ', error)
        const input = document.getElementById('newUserEmail')
        input.classList.add('is-invalid')
        toastMessage.value = error?.response?.data?.error || 'An error occurred while updating the user.'
        displayToast(true)
    }
}

function displayToast(showError) {
    isError.value = showError
    showToast.value = true
    setTimeout(() => {
        showToast.value = false
        isError.value = false
        toastMessage.value = ''
    }, 3000)
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return emailRegex.test(email)
}

function resetValidation(element) {
    if (element.classList.contains('is-invalid')) {
        element.classList.remove('is-invalid')
    }
}

async function handleUpdate(user) {
    try {
        if (user.email === '') {
            const input = document.querySelector('input[name="email"]')
            input.classList.add('is-invalid')
            toastMessage.value = 'Email cannot be empty.'
            displayToast(true)
            return
        }

        user.email = user.email.trim()
        if (user.password) {
            user.password = user.password.trim()
        }

        let userData = null

        if (user.password) {
            userData = {
                email: user.email,
                password: user.password,
                role: user.role
            }
        } else {
            userData = {
                email: user.email,
                role: user.role
            }
        }

        await userStore.update(user.id, userData)

        toastMessage.value = 'User updated successfully!'
        displayToast(false)

    } catch (error) {
        console.error('An error occurred: ', error)
        const input = document.querySelector('input[name="email"]')
        input.classList.add('is-invalid')
        toastMessage.value = error?.response?.data?.error || 'An error occurred while updating the user.'
        displayToast(true)
    }
}

async function handleDelete() {
    try {
        await userStore.remove(deletingUser.value.id)

        toastMessage.value = 'User deleted successfully!'
        displayToast(false)

    } catch (error) {
        console.error('An error occurred:', error)

        toastMessage.value = 'An error occurred while deleting the user.'
        displayToast(true)

    } finally {
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'))
        modal?.hide()
    }
}

</script>

<template>
    <div
        class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Manage Users</h1>
    </div>
    <div>
        <div class="mt-4">
            <h3>Add New User</h3>
            <div class="col-10 d-flex gap-2">
                <input id="newUserEmail" v-model="newUser.email" placeholder="Email" class="form-control" @keyup="resetValidation($event.target)" />
                <input id="newUserPassword" v-model="newUser.password" placeholder="Password" class="form-control" @keyup="resetValidation($event.target)" />
                <select v-model="newUser.role" class="form-control">
                    <option value="Admin">Admin</option>
                    <option value="Regular">Regular</option>
                </select>
                <button class="btn btn-success" @click="handleCreate">Create</button>
            </div>
        </div>
        <h3 class="mb-3 mt-4">User List</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in userStore.users" :key="user.id">
                    <td>{{ user.id }}</td>
                    <td><input v-model="user.email" type="email" name="email" class="form-control"
                            aria-describedby="emailHelp" placeholder="Enter email" @keyup="resetValidation($event.target)"></td>
                    <td><input v-model="user.password" type="text" name="password" class="form-control"
                            placeholder="Enter new password"></td>
                    <td><select v-model="user.role" class="form-control">
                            <option value="Admin">Admin</option>
                            <option value="Regular">Regular</option>
                        </select></td>
                    <td class="no-border">
                        <button class="btn btn-sm btn-warning mx-2" @click="handleUpdate(user)">Update</button>
                        <button class="btn btn-sm btn-danger ms-1" data-bs-toggle="modal" data-bs-target="#deleteModal"
                            @click="deletingUser = user">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="delete" @submit.prevent="handleDelete">
                        <input type="hidden" name="planId">
                        <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div v-if="showToast" :class="{ 'toast-error': isError }" class="toast-message">{{ toastMessage }}</div>
</template>

<style scoped>
td {
    vertical-align: middle;
}

.no-border {
    border: none !important;
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