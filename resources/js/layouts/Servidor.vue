<template>
    <div>
        <sui-menu inverted vertical class="visible sidebar" fixed="left">
            <sui-menu-item>
                <router-link :to="{ name: 'dashboard' }">
                    <sui-icon name="server" size="big"></sui-icon> Servidor
                </router-link>
            </sui-menu-item>

            <main-menu use_new=true />

            <sui-menu-menu position="right" v-if="!loggedIn">
                <sui-menu-item>
                    <router-link :to="{ name: 'login' }">
                        Login
                    </router-link>
                </sui-menu-item>
                <sui-menu-item>
                    <router-link :to="{ name: 'register' }">
                        Register
                    </router-link>
                </sui-menu-item>
            </sui-menu-menu>

            <div class="right menu" v-else>
                <sui-dropdown item :text="user.name">
                    <sui-dropdown-menu>
                        <a is="sui-dropdown-item" @click.prevent="logout">
                            Logout
                        </a>
                    </sui-dropdown-menu>
                </sui-dropdown>
            </div>
        </sui-menu>

        <section class="main">
            <stats-bar id="stats-bar" />

            <sui-container id="content">
                <router-view />
            </sui-container>
        </section>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapState } from 'vuex';

export default {
    computed: {
        ...mapGetters([
            'loggedIn',
        ]),
        ...mapState({
            user: state => state.Auth.user,
        }),
    },
    created () {
        this.$store.dispatch('fetchProfile');
    },
    methods: {
        logout () {
            this.$store.dispatch('logout').then(response => {
                this.$nextTick(() => {
                    this.$router.push({ name: 'login' });
                });
            });
        },
    },
}
</script>
