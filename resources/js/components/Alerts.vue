<template>
    <div v-if="alerts.length" class="alerts">
        <sui-message v-for="alert in alerts" :key="alert.message"
                     :header="alert.title" v-if="alert.message"
                     :success="alert.isSuccess" :error="!alert.isSuccess"
                     class="visible">
            <p>{{ alert.message }}</p>
        </sui-message>
    </div>
</template>

<script>
export default {
    props: {
        alerts: {
            type: Array,
            default: () => ([]),
        },
    },
};
</script>
