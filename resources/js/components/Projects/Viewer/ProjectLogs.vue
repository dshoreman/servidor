<template>

    <sui-segment attached :inverted="darkMode" v-if="logNames.length">

        <sui-menu pointing secondary :inverted="darkMode">
            <a is="sui-menu-item" v-for="(title, key) in service.logs" :key="key"
                :active="activeLog === key" :content="title"
                @click="viewLog(key)" />
        </sui-menu>

        <pre>{{ logContent }}</pre>

    </sui-segment>

</template>

<script>
export default {
    mounted() {
        this.initLog();
    },
    props: {
        service: { type: Object, default: () => ({}) },
        project: { type: Object, default: () => ({}) },
    },
    data() {
        return {
            activeLog: '',
            logContent: '',
        };
    },
    computed: {
        logNames() {
            return Object.keys(this.service.logs);
        },
    },
    methods: {
        initLog() {
            if (this.logNames.length) {
                this.viewLog(this.logNames[0]);
            } else {
                this.logContent = '';
                this.activeLog = '';
            }
        },
        viewLog(key) {
            this.logContent = 'Loading...';
            this.activeLog = key;

            axios
                .get(`/api/projects/${this.project.id}/logs/${key}.service-${this.service.id}.log`)
                .then(response => {
                    this.logContent = '' === response.data.trim()
                        ? "Log file is empty or doesn't exist."
                        : response.data;
                }).catch(() => {
                    this.logContent = `Failed to load ${this.activeLog} log!`;
                });
        },
    },
};
</script>
