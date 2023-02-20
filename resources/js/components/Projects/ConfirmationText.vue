<template>
    <div v-if="app.template != 'Clean Slate'">
        <p>
            When you continue, the new project will be created with a
            <strong>{{ app.template }}</strong> application.
        </p>
        <p>
            Code will be pulled from the <code>{{ repoUri }}</code>
            repository on <strong>{{ app.config.source.provider }}</strong> using:<br>
            <strong>{{ app.config.source.repository }}</strong>.
        </p>
        <p>
            The project will be configured to track the
            <code>{{ app.config.source.branch }}</code> branch.
        </p>
        <p>
            If it's enabled, the {{ app.template }} application
            will be accessible at <code>{{ app.domain }}</code>.
        </p>
    </div>
</template>

<script>
export default {
    props: {
        app: {
            type: Object,
            default: () => ({}),
        },
        providers: { type: Array, default: () => []},
    },
    computed: {
        repoUri() {
            const provider = this.providers.find(p => p.name === this.app.config.source.provider);

            return provider.urlFormat.replace('%REPO%', this.app.config.source.repository);
        },
    },
};
</script>
