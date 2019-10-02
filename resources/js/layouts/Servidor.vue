<template>
    <div>
        <sui-menu inverted vertical class="visible sidebar" fixed="left">
            <sui-menu-item>
                <router-link :to="{ name: 'dashboard' }">
                    <sui-icon name="server" size="big"></sui-icon> Servidor
                </router-link>
            </sui-menu-item>

            <main-menu use_new=true />

            <sui-menu class="large" secondary fluid inverted pointing id="user-menu" v-if="!loggedIn">
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
            </sui-menu>

            <sui-menu class="large" secondary fluid inverted vertical pointing id="user-menu" v-else>
                <a is="sui-menu-item">
                    {{ user.name }} <sui-icon name="chevron up" />
                </a>
                <a is="sui-menu-item" @click.prevent="logout">
                    Logout
                </a>
            </sui-menu>
        </sui-menu>

        <section class="main">
            <stats-bar id="stats-bar" />

            <router-view id="content" />

            <div class="version">
                <p>Servidor v{{ version }}</p>
            </div>
        </section>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapState } from 'vuex';

export default {
    props: {
        version: {
            type: String,
            default: "0.0.0",
        },
    },
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
