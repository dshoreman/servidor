<template>
    <div class="ui middle aligned center aligned grid">
        <div class="column">
            <h2 class="ui header centered">
                <i class="server icon"></i>
                Sign in to Servidor
            </h2>
            <form class="ui form" @submit.prevent="login" method="POST" action="/login">
                <div class="ui inverted stacked segment">
                    <sui-message negative v-if="error.msg"
                        :header="error.title"
                        :content="error.msg" />

                    <div class="field">
                        <div class="ui left inverted transparent icon input"
                            type="email" placeholder="Email address">
                            <input id="email" type="email" name="email"
                                v-model="username" placeholder="E-mail address"
                                required autofocus>
                            <i class="user icon"></i>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui left inverted transparent icon input"
                            type="password" placeholder="Password">
                            <input id="password" type="password" name="password"
                                v-model="password" required
                                placeholder="Password" />
                            <i class="lock icon"></i>
                        </div>
                    </div>
                    <div class="field left aligned">
                        <div class="ui toggle checkbox">
                            <input type="checkbox" name="remember" id="remember">
                            <label for="remember">Remember Me</label>
                        </div>
                    </div>
                    <button class="ui positive fluid large button" type="submit">
                        Login
                    </button>
                </div>
            </form>

            <div class="ui inverted message">
                <router-link :to="{ name: 'password.request' }" class="centered">
                    Forgot Your Password?
                </router-link>
            </div>
        </div>
        <link href="/css/theme.dark.css" rel="stylesheet" type="text/css">
        <link href="/css/app.css" rel="stylesheet" type="text/css">
        <link href="/css/theme.dark-custom.css" rel="stylesheet" type="text/css">
    </div>
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
