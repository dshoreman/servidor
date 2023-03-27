<template>
    <sui-card-group :items-per-row="2">

        <sui-card
            @click="tpl.disabled || $emit('selected', tpl)"
            v-for="tpl in templates"
            :class="cardClass(tpl)"
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
    methods: {
        cardClass(tpl) {
            let cardClass = this.darkMode ? 'inverted ' : '';

            if (!tpl.disabled) {
                cardClass += `link ${tpl.colour}`;
            }

            return cardClass;
        },
    },
};
</script>
