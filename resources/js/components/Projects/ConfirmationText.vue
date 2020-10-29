<template>
    <div>
        <p>
            When you continue, the new project will be created with a
            <strong>{{ app.template }}</strong> application.
        </p>
        <p>
            Code will be pulled from the <code>{{ source.repo }}</code>
            repository on <strong>{{ source.host }}</strong> using:<br>
            <strong>{{ app.source_repo }}</strong>.
        </p>
        <p>
            If it's enabled, the {{ app.template }} application
            will be accessible at <code>{{ app.domain }}</code>.
        </p>
        <p>
            The project will be configured to track the
            <code>{{ app.source_branch }}</code> branch.
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
        source: {
            type: Object,
            default: () => ({}),
        },
    },
};
</script>
