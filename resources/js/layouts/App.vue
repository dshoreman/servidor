<template>
    <sui-container class="main">
        <stats-bar hostname="{ $stats->hostname }}"
                   distro="{ $stats->os->distro }}"
                   version="{ $stats->os->version }}">
        </stats-bar>

        <main-menu></main-menu>

        <router-view />
    </sui-container>
</template>
