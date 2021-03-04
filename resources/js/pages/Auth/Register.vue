<template>
    <sui-grid is="themed-page" layout="login" textAlign="center" verticalAlign="middle">
        <sui-grid-column>
            <h2 is="sui-header">
                <sui-icon name="server" /> Create your Servidor Account
            </h2>
            <sui-form @submit.prevent="register">
                <sui-segment stacked inverted>
                    <sui-form-field class="left aligned">
                        <sui-input inverted transparent required autofocus
                                   type="text" id="name" placeholder="Display name"
                                   v-model="name" icon="user" icon-position="left"/>
                    </sui-form-field>

                    <sui-form-field class="left aligned">
                        <sui-input inverted transparent required
                                   type="email" id="email" placeholder="Email address"
                                   v-model="email" icon="envelope" icon-position="left" />
                    </sui-form-field>

                    <sui-form-field class="left aligned">
                        <sui-input inverted transparent required
                                   type="password" id="password" placeholder="Password"
                                   v-model="password" icon="key" icon-position="left" />
                    </sui-form-field>

                    <sui-form-field class="left aligned">
                        <sui-input inverted transparent required
                                   type="password" id="passconfirm" placeholder="Password (again)"
                                   v-model="passwordConfirm" icon="blank" icon-position="left" />
                    </sui-form-field>

                    <sui-button positive fluid type="submit" size="large">
                        Register
                    </sui-button>
                </sui-segment>
            </sui-form>
        </sui-grid-column>
    </sui-grid>
</template>

<script>
export default {
    data() {
        return {
            name: '',
            email: '',
            password: '',
            passwordConfirm: '',
        };
    },
    mounted() {
        document.body.classList.add('login');
    },
    beforeRouteLeave(to, from, next) {
        document.body.classList.remove('login');

        return next();
    },
    methods: {
        register() {
            this.$store.dispatch('register', {
                name: this.name,
                email: this.email,
                password: this.password,
                passwordConfirmation: this.passwordConfirm,
            }).then(() => {
                this.$router.push({ name: 'login' });
            });
        },
    },
};
</script>
