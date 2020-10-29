<template>
    <sui-card-group :items-per-row="2">

        <sui-card
            :class="!tpl.disabled && 'link ' + tpl.colour"
            @click="$emit('selected', tpl)"
            v-for="tpl in templates"
            :key="tpl.name"
        >
            <template-card :template="tpl" />
        </sui-card>

    </sui-card-group>
</template>

<script>
import TemplateCard from './TemplateCard';

export default {
    components: {
        TemplateCard,
    },
    data() {
        return {
            templates: [{
                icon: 'archive',
                colour: 'brown',
                disabled: true,
                name: 'Archive',
                text: 'Redirect all requests on a domain to an archived copy on Wayback Machine.',
            }, {
                icon: 'question mark',
                colour: 'grey',
                name: 'Clean Slate',
                text: "Don't setup an application component for now, just create an empty project.",
            }, {
                icon: 'docker',
                colour: 'blue',
                disabled: true,
                name: 'Docker',
                text: 'Install and configure a docker container to run automatically on system boot.',
            }, {
                icon: 'html5',
                colour: 'orange',
                name: 'HTML',
                text: 'Configure nginx to serve static content such as HTML, CSS and Javascript.',
            }, {
                icon: 'php',
                colour: 'violet',
                name: 'PHP',
                text: 'Like HTML projects, but with added support for dynamic PHP content.',
            }, {
                icon: 'laravel',
                colour: 'red',
                name: 'Laravel',
                text: 'Modified PHP project with hooks for artisan migrations and composer/npm.',
            }],
        };
    },
};
</script>
