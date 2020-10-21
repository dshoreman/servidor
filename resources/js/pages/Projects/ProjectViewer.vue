<template>

    <div>

        <h2 is="sui-header" dividing>{{ project.name }}</h2>

        <h3 is="sui-header">Primary Application</h3>

        <sui-segment placeholder v-if="!project.applications.length">

            <h4>Select a template to get started:</h4>

            <ul>
                <li>None</li>
                <li>HTML</li>
                <li>PHP+HTML</li>
                <li>Laravel</li>
                <li>Symfony</li>
            </ul>

        </sui-segment>
    </div>

</template>

<script>
export default {
    data() {
        return {
            project: {
                name: 'Some Project',
                applications: [],
                redirects: [],
            },
        };
    },
};
</script>
