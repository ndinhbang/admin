<template>
  <ValidationObserver ref="observer" slim>
    <el-form :model="loginForm" ref="loginForm" class="login-form" slot-scope="{ validate }">
      <ValidationProvider rules="required|email" name="email" vid="email" slim>
        <el-form-item prop="email" slot-scope="{ errors }" :error="errors[0]">
          <el-input
            ref="email"
            v-model.trim="loginForm.email"
            placeholder="Email"
            name="email"
            type="text"
            tabindex="1"
            autocomplete="on"
          />
        </el-form-item>
      </ValidationProvider>

      <el-tooltip v-model="capsTooltip" content="Caps lock is On" placement="right" manual>
        <ValidationProvider rules="required|min:8" name="password" vid="password" slim>
          <el-form-item prop="password" slot-scope="{ errors }" :error="errors[0]">
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
        </ValidationProvider>
      </el-tooltip>

      <el-button type="primary" :loading="loading" @click.native.prevent="validate().then(handleLogin)">Login
      </el-button>
    </el-form>
  </ValidationObserver>
</template>

<script>
    import {extend, ValidationObserver, ValidationProvider} from 'vee-validate'
    import {email, min, required} from 'vee-validate/dist/rules'
    import Auth from '../../api/auth'
    import {showMsg} from '../../utils/helpers'

    export default {
        name: 'LoginForm',
        components: {
            ValidationObserver,
            ValidationProvider
        },
        data() {
            return {
                loginForm: {
                    email: '',
                    password: ''
                },
                capsTooltip: false,
                loading: false
            };
        },
        created() {
            // Add the vee-validate rules
            extend('required', {
                ...required,
                message(field) {
                    return `The ${field} is required`;
                }
            })
            extend('email', {
                ...email,
                message(field) {
                    return `The ${field} is not an email`;
                }
            })
            extend('min', {
                ...min,
                message(field, props) {
                    return `The ${field} can not be less than ${props.length} characters`;
                }
            })
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
            async handleLogin() {
                this.loading = true
                try {
                    const {data} = await Auth.login(this.loginForm)
                    await this.$store.dispatch('auth/login', data)
                    const response = await Auth.getCurrentUser()
                    // continue after done
                    this.$store.dispatch('auth/setCurrentUser', response.data)
                    // redirect to dashboard
                    this.$router.push({name: 'home'})
                } catch (err) {
                    showMsg(err)
                    console.log(err)
                }
                this.loading = false
            }
        },
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