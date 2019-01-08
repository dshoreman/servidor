<template>
    <div class="ui middle aligned center aligned grid">
        <div class="column">
            <h2 class="ui teal header centered">
                <i class="server icon"></i>
                Sign in to Servidor
            </h2>
            <form class="ui form" @submit.prevent="login" method="POST" action="/login">
                <div class="ui stacked segment">
                    <sui-message negative v-if="error">
                        <sui-message-header>
                            We couldn't get you logged in :(
                        </sui-message-header>
                        {{ error }}
                    </sui-message>
                    <div class="field">
                        <div class="ui left icon input" type="email" placeholder="Email address">
                            <input id="email" type="email" name="email"
                                v-model="username" placeholder="E-mail address"
                                required autofocus>
                            <i class="user icon"></i>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui left icon input" type="password" placeholder="Password">
                            <input id="password" type="password" name="password"
                                v-model="password" required
                                placeholder="Password" />
                            <i class="lock icon"></i>
                        </div>
                    </div>
                    <div class="field left aligned">
                        <div class="ui toggle checkbox">
                            <input type="checkbox" name="remember">
                            <label for="remember">Remember Me</label>
                        </div>
                    </div>
                    <button class="ui teal fluid large button" type="submit">
                        Login
                    </button>
                </div>
            </form>

            <div class="ui message">
                <router-link :to="{ name: 'password.request' }" class="centered">
                    Forgot Your Password?
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data () {
        return {
            error: null,
            username: '',
            password: '',
        };
    },
    mounted () {
        document.body.classList.add('login');
    },
    beforeRouteLeave (to, from, next) {
        document.body.classList.remove('login');

        return next();
    },
    methods:  {
        login () {
            axios.post('/api/login', {
                username: this.username,
                password: this.password,
            }).then(response => {
                let token = response.data.access_token;
                window.axios.defaults.headers.common['Authorization'] = 'Bearer '+token;

                this.$router.push('/');
            }).catch(error => {
                this.error = error.response.data.message;
            });
        },
    },
};
</script>

