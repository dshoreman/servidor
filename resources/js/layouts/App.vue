<template>
    <sui-container class="main">

        <sui-menu fixed inverted>
            <router-link header :to="{ name: 'dashboard' }" is="sui-menu-item">
                <sui-icon name="server" size="big"></sui-icon> Servidor
            </router-link>

            <sui-menu-menu position="right" v-if="!loggedIn">
                <router-link :to="{ name: 'login' }" is="sui-menu-item">
                    Login
                </router-link>
                <router-link :to="{ name: 'register' }" is="sui-menu-item">
                    Register
                </router-link>
            </sui-menu-menu>

            <div class="right menu" v-else>
                <sui-dropdown item text="Auth::user()->name">
                    <sui-dropdown-menu>
                        <a is="sui-dropdown-item" href="/logout"
                            @click.prevent="$refs.logoutForm.submit">
                            Logout
                        </a>

                        <form ref="logoutForm" action="/logout" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </sui-dropdown-menu>
                </sui-dropdown>
            </div>
        </sui-menu>

        <stats-bar />

        <main-menu></main-menu>

        <router-view />
    </sui-container>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
    computed: {
        ...mapGetters([
            'loggedIn',
        ]),
    },
}
</script>
