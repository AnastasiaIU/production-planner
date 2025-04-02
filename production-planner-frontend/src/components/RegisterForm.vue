<script setup>

import { ref } from 'vue'
import { useLoginStore } from '@/stores/login'
import { useRouter } from 'vue-router'
import { API_ENDPOINTS } from '@/utils/config'
import axios from "axios"

const router = useRouter()
const loginStore = useLoginStore()
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const emailPrompt = ref('Email cannot be empty.')
const passwordPrompt = ref('Please provide passwords.')
const passwordType = ref('password')
const regForm = ref(null)

function showPassword(event) {
    const type = event.target.checked ? 'text' : 'password'
    passwordType.value = type
}

function resetValidation() {
    const emailInput = document.getElementById('inputEmail')
    const passwordInput = document.getElementById('inputPassword')
    const confirmPasswordInput = document.getElementById('inputConfirmPassword')

    emailInput.setCustomValidity('')
    passwordInput.setCustomValidity('')
    confirmPasswordInput.setCustomValidity('')

    if (regForm.value.classList.contains('was-validated')) {
        regForm.value.classList.remove('was-validated')
    }
}

function setEmailValidity(message, validation) {
    emailPrompt.value = message;
    const emailInput = document.getElementById('inputEmail')
    emailInput.setCustomValidity(validation)
}

function setPasswordValidity(message, validation) {
    passwordPrompt.value = message;
    const passwordInput = document.getElementById('inputPassword')
    const confirmPasswordInput = document.getElementById('inputConfirmPassword')
    passwordInput.setCustomValidity(validation)
    confirmPasswordInput.setCustomValidity(validation)
}

function validateForm() {
    regForm.value.classList.add('was-validated')
}

async function handleSubmittion() {
    try {
        resetValidation()

        if (email.value === '') {
            setEmailValidity('Email cannot be empty', 'error')
            validateForm()
            return
        } else {
            setEmailValidity('Invalid email.', '')
        }

        if (password.value === '') {
            setPasswordValidity('Please provide passwords.', 'error')
            validateForm()
            return
        }

        if (password.value !== confirmPassword.value) {
            setPasswordValidity('Passwords do not match', 'error')
            validateForm()
            return
        }

        validateForm()

        if (!regForm.value.checkValidity()) {
            return
        }

        await axios.post(API_ENDPOINTS.register, {
            email: email.value,
            password: password.value
        })

        loginStore.setWasRegistrated(true)

        router.push('/login')
    } catch (error) {
        console.error('An error occurred during authentication:', error)
        setEmailValidity('', 'error')
        setPasswordValidity(error?.response?.data?.error || 'An error occurred during authentication', 'error')
        validateForm()
    }
}
</script>

<template>
    <section class="card col-md-6 col-lg-5 col-xl-4 p-4 m-4">
        <form class="d-flex flex-column gap-2 needs-validation" ref="regForm" method="post"
            @submit.prevent="handleSubmittion" novalidate>
            <div>
                <img src="/src/assets/images/ficsit-checkmarktm_64.png" class="img-fluid image-height-40"
                    alt="Logo FICSIT Checkmark">
                <span class="h3 align-middle ms-2 dark-grey-text">Production Planner</span>
            </div>
            <p class="h5 mb-3 medium-grey-text">Create a new account</p>
            <div class="d-flex flex-column gap-3 mb-2">
                <div class="form-group">
                    <label for="inputEmail">Email address</label>
                    <input v-model="email" type="email" name="email" class="form-control" id="inputEmail"
                        aria-describedby="emailHelp" placeholder="Enter email" @keyup="resetValidation" required>
                    <div class="invalid-feedback" id="inputEmailPrompt">
                        {{ emailPrompt }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword">Password</label>
                    <input v-model="password" :type="passwordType" name="password" class="form-control"
                        id="inputPassword" placeholder="Password" @keyup="resetValidation" required>
                </div>
                <div class="form-group">
                    <label for="inputConfirmPassword">Confirm password</label>
                    <input v-model="confirmPassword" :type="passwordType" name="confirmPassword" class="form-control"
                        id="inputConfirmPassword" placeholder="Confirm password" @keyup="resetValidation" required>
                    <div class="invalid-feedback" id="confirmPasswordPrompt">
                        {{ passwordPrompt }}
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center mb-4">
                <input class="checkbox me-2" type="checkbox" value="" id="showPasswordCheck"
                    @change="showPassword($event)">
                <label class="" for="showPasswordCheck">
                    Show passwords
                </label>
            </div>
            <button type="submit" class="btn btn-primary mb-3" id="registerBtn">Register</button>
        </form>
    </section>
</template>