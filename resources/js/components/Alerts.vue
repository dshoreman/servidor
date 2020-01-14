<template>
    <div v-if="alerts.length" class="alerts">
        <sui-message v-for="alert in filteredAlerts()" :key="alert.message"
                     :success="alert.isSuccess" :error="!alert.isSuccess"
                     :header="alert.title" class="visible">
            <p>{{ alert.message }}</p>
        </sui-message>
    </div>
</template>

<script>
export default {
    props: {
        alerts: {
            type: Array,
            default: () => [],
        },
    },
    methods: {
        filteredAlerts() {
            return this.alerts.filter(alert => !!alert.message);
        },
    },
};
</script>
