<template>
    <sui-card-group :items-per-row="2">

        <sui-card
            :class="!tpl.disabled && 'link ' + tpl.colour"
            @click="tpl.disabled || $emit('selected', tpl)"
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
    props: {
        templates: {
            type: Array,
            default: () => [],
        },
    },
};
</script>
