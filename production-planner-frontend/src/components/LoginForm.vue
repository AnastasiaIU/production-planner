<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()
</script>

<template>
    <section class="card col-md-6 col-lg-5 col-xl-4 p-4 m-4">
        <form class="d-flex flex-column gap-2 needs-validation" id="loginForm" method="post" novalidate>
            <div>
                <img src="/src/assets/images/ficsit-checkmarktm_64.png" class="img-fluid image-height-40"
                    alt="Logo FICSIT Checkmark">
                <span class="h3 align-middle ms-2 dark-grey-text">Production Planner</span>
            </div>
            <p class="h5 mb-3 medium-grey-text">Log in to your account</p>
            <div class="form-group">
                <label for="loginEmail">Email address</label>
                <input type="email" name="email" class="form-control" id="loginEmail" aria-describedby="emailHelp"
                    placeholder="Enter email" required>
                <div class="invalid-feedback" id="loginEmailPrompt">Email address cannot be empty.</div>
            </div>
            <div class="form-group mb-2">
                <label for="loginPassword">Password</label>
                <input type="password" name="password" class="form-control" id="loginPassword" placeholder="Password"
                    required>
                <div class="invalid-feedback" id="loginPasswordPrompt">
                    Password cannot be empty.
                </div>
            </div>
            <div class="d-flex align-items-center mb-4">
                <input class="checkbox me-2" type="checkbox" value="" id="showPasswordCheck">
                <label class="" for="showPasswordCheck">
                    Show password
                </label>
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
    </section>
</template>