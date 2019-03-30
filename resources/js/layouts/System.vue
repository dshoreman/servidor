<template>
    <sui-grid>
        <sui-grid-column :width="4">
            <system-menu></system-menu>
        </sui-grid-column>

        <sui-grid-column :width="12">
            <router-view />
        </sui-grid-column>
    </sui-grid>
</template>
