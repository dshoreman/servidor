<template>
    <div v-if="service.template != 'Clean Slate'">
        <p>
            When you continue, the new project will be created with a
            <strong>{{ service.template }}</strong> application.
        </p>
        <p>
            Code will be pulled from the <code>{{ repoUri }}</code>
            repository on <strong>{{ service.config.source.provider }}</strong> using:<br>
            <strong>{{ service.config.source.repository }}</strong>.
        </p>
        <p>
            The project will be configured to track the
            <code>{{ service.config.source.branch }}</code> branch.
        </p>
        <p>
            If it's enabled, the {{ service.template }} application
            will be accessible at <code>{{ service.domain }}</code>.
        </p>
    </div>
</template>

<script>
export default {
    props: {
        service: {
            type: Object,
            default: () => ({}),
        },
        providers: { type: Array, default: () => []},
    },
    computed: {
        repoUri() {
            const provider = this.providers.find(
                p => p.name === this.service.config.source.provider,
            );

            return provider.urlFormat.replace('%REPO%', this.service.config.source.repository);
        },
    },
};
</script>
