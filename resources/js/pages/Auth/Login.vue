<template>
    <sui-grid textAlign="center" verticalAlign="middle">
        <sui-grid-column>
            <h2 is="sui-header">
                <sui-icon name="server" /> Sign in to Servidor
            </h2>
            <sui-form @submit.prevent="login">
                <sui-segment stacked inverted>
                    <sui-message negative v-if="error.msg"
                        :header="error.title" :content="error.msg" />

                    <sui-form-field>
                        <sui-input inverted transparent required type="email"
                            icon="user" icon-position="left" v-model="username"
                            placeholder="Email address" autofocus />
                    </sui-form-field>
                    <sui-form-field>
                        <sui-input inverted transparent required type="password"
                            icon="lock" icon-position="left" v-model="password"
                            placeholder="Password" />
                    </sui-form-field>
                    <sui-form-field class="left aligned">
                        <sui-checkbox toggle label="Remember Me" />
                    </sui-form-field>
                    <sui-button positive fluid type="submit" size="large">
                        Login
                    </sui-button>
                </sui-segment>
            </sui-form>

            <sui-message class="inverted">
                <router-link :to="{ name: 'password.request' }" class="centered">
                    Forgot Your Password?
                </router-link>
            </sui-message>
        </sui-grid-column>
        <link href="/css/theme.dark.css" rel="stylesheet" type="text/css">
        <link href="/css/app.css" rel="stylesheet" type="text/css">
        <link href="/css/theme.dark-custom.css" rel="stylesheet" type="text/css">
    </sui-grid>
</template>

<script>
import { mapGetters, mapMutations } from 'vuex';

export default {
    data() {
        return {
            username: '',
            password: '',
        };
    },
    mounted() {
        document.body.classList.add('login');
    },
    beforeRouteLeave(to, from, next) {
        document.body.classList.remove('login');

        return next();
    },
    computed: {
        ...mapGetters({
            error: 'authMsg',
        }),
    },
    methods: {
        ...mapMutations([
            'setAlert',
        ]),
        login() {
            this.$store.dispatch('login', {
                username: this.username,
                password: this.password,
            }).then(() => {
                this.$router.push({ name: 'dashboard' });
            });
        },
    },
};
</script>
