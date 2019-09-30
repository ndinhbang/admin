<template>
    <el-container>
        <el-main>
            <el-row type="flex" class="row-bg" justify="center">
                <!--<el-col :span="6">-->
                    <LoginForm></LoginForm>
                <!--</el-col>-->
            </el-row>
        </el-main>
    </el-container>
</template>

<script>
    // @ is an alias to /src
    import LoginForm from '@/components/auth/LoginForm.vue'

    export default {
        name: 'Login',
        components: {
            LoginForm
        }
    }
</script>