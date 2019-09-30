<template>
    <el-form :model="loginForm" status-icon :rules="rules" ref="loginForm" class="login-form">
        <el-form-item prop="email">
            <el-input
                    ref="email"
                    v-model="loginForm.email"
                    placeholder="Email"
                    name="email"
                    type="text"
                    tabindex="1"
                    autocomplete="on"
            />
        </el-form-item>

        <el-tooltip v-model="capsTooltip" content="Caps lock is On" placement="right" manual>
            <el-form-item prop="password">
                <el-input
                        ref="password"
                        v-model="loginForm.password"
                        placeholder="Password"
                        name="password"
                        type="password"
                        tabindex="2"
                        autocomplete="on"
                        @keyup.native="checkCapslock"
                        @blur="capsTooltip = false"
                        @keyup.enter.native="handleLogin"
                />
            </el-form-item>
        </el-tooltip>

        <el-button :loading="loading" type="primary" style="width:100%;margin-bottom:30px;"
                   @click.native.prevent="handleLogin">Login
        </el-button>
    </el-form>
</template>

<script>
    export default {
        name: 'Login',
        data() {
            return {
                loginForm: {
                    email: '',
                    password: ''
                },
                capsTooltip: false,
                loading: false,
                rules: {}
            };
        },
        mounted() {
            if (this.loginForm.email === '') {
                this.$refs.email.focus()
            } else if (this.loginForm.password === '') {
                this.$refs.password.focus()
            }
        },
        methods: {
            checkCapslock({shiftKey, key} = {}) {
                if (key && key.length === 1) {
                    this.capsTooltip = shiftKey && (key >= 'a' && key <= 'z') || !shiftKey && (key >= 'A' && key <= 'Z');
                }
                if (key === 'CapsLock' && this.capsTooltip === true) {
                    this.capsTooltip = false
                }
            },
            handleLogin() {
                this.loading = true
            }
        }
    }
</script>

<style lang="scss">
    .login-form {
        position: relative;
        width: 520px;
        max-width: 100%;
        padding: 200px 35px 0;
        margin: 0 auto;
        overflow: hidden;
    }
</style>