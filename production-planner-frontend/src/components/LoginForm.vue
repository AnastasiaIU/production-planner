<script setup>

import { useRouter } from 'vue-router'
import { ref } from 'vue'
import { useLoginStore } from '@/stores/login'
import { useAuthStore } from "@/stores/auth"
import { onMounted } from 'vue'

const router = useRouter()
const loginStore = useLoginStore()
const authStore = useAuthStore()
const email = ref('')
const password = ref('')
const emailPrompt = ref('Email cannot be empty.')
const passwordPrompt = ref('Please provide password.')
const passwordType = ref('password')
const regForm = ref(null)
const showToast = ref(false)

onMounted(() => {
    if (loginStore.wasRegistrated) {
        showToast.value = true

        setTimeout(() => {
            showToast.value = false
        }, 3000)

        loginStore.setWasRegistrated(false)
    }
})

function showPassword(event) {
    const type = event.target.checked ? 'text' : 'password'
    passwordType.value = type
}

function resetValidation() {
    const emailInput = document.getElementById('loginEmail')
    const passwordInput = document.getElementById('loginPassword')

    emailInput.setCustomValidity('')
    passwordInput.setCustomValidity('')

    if (regForm.value.classList.contains('was-validated')) {
        regForm.value.classList.remove('was-validated')
    }
}

function setEmailValidity(message, validation) {
    emailPrompt.value = message;
    const emailInput = document.getElementById('loginEmail')
    emailInput.setCustomValidity(validation)
}

function setPasswordValidity(message, validation) {
    passwordPrompt.value = message;
    const passwordInput = document.getElementById('loginPassword')
    passwordInput.setCustomValidity(validation)
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
            setPasswordValidity('Please provide password.', 'error')
            validateForm()
            return
        }

        validateForm()

        if (!regForm.value.checkValidity()) {
            return
        }

        await authStore.login({ email: email.value, password: password.value })
        await authStore.fetchUser()

        router.push('/plans')

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
        <form class="d-flex flex-column gap-2 needs-validation" ref="regForm" id="loginForm" method="post"
            @submit.prevent="handleSubmittion" novalidate>
            <div>
                <img src="/src/assets/images/ficsit-checkmarktm_64.png" class="img-fluid image-height-40"
                    alt="Logo FICSIT Checkmark">
                <span class="h3 align-middle ms-2 dark-grey-text">Production Planner</span>
            </div>
            <p class="h5 mb-3 medium-grey-text">Log in to your account</p>
            <div class="form-group">
                <label for="loginEmail">Email address</label>
                <input v-model="email" type="email" name="email" class="form-control" id="loginEmail"
                    aria-describedby="emailHelp" placeholder="Enter email" @keyup="resetValidation" required>
                <div class="invalid-feedback" id="loginEmailPrompt">{{ emailPrompt }}</div>
            </div>
            <div class="form-group mb-2">
                <label for="loginPassword">Password</label>
                <input v-model="password" :type="passwordType" name="password" class="form-control" id="loginPassword"
                    placeholder="Password" @keyup="resetValidation" required>
                <div class="invalid-feedback" id="loginPasswordPrompt">{{ passwordPrompt }}</div>
            </div>
            <div class="d-flex align-items-center mb-4">
                <input class="checkbox me-2" type="checkbox" value="" id="showPasswordCheck"
                    @change="showPassword($event)">
                <label class="" for="showPasswordCheck">Show password</label>
            </div>
            <button type="submit" class="btn btn-primary mb-3">Log in</button>
            <p>Don't have an account?
                <a class="link-opacity-75-hover">
                    <router-link to="/register" @click.prevent="router.push(`/register`)">
                        Sign up
                    </router-link>
                </a>.
            </p>
        </form>
        <div v-if="showToast" class="toast-message">Registration successful! You can now log in.</div>
    </section>
</template>

<style>
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
</style>