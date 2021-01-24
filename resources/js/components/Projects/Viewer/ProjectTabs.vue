<template>

    <sui-list divided relaxed>
        <sui-list-item v-for="app in project.applications" :key="app.id">

            <sui-list-icon
                size="large"
                :name="appIcon.name"
                :color="appIcon.color" />

            <sui-list-content>
                <a is="sui-list-header">{{ app.domain_name }}</a>
                <a is="sui-list-description">{{ app.template }}</a>
            </sui-list-content>

        </sui-list-item>
        <sui-list-item v-for="redir in project.redirects" :key="redir.id">

            <sui-list-icon
                size="large"
                name="archive"
                color="brown" />

            <sui-list-content>
                <a is="sui-list-header">{{ redir.domain_name }}</a>
                <a is="sui-list-description">Archive</a>
            </sui-list-content>

        </sui-list-item>
    </sui-list>

</template>

<style scoped>
.ui.list.divided {
    margin-top: 10px;
}
</style>

<script>
export default {
    props: {
        project: {
            type: Object,
            default: () => ({}),
        },
    },
};
</script>
