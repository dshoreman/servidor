<template>
    <sui-modal :class="darkMode ? 'inverted' : ''" size="tiny" v-model="visible">
        <sui-modal-header>{{ title }}</sui-modal-header>
        <sui-progress attached top :percent="done" />
        <sui-modal-content>
            <sui-list>
                <sui-list-item v-for="step in steps" :key="step.name">
                    <sui-icon :name="step.icon" :color="step.colour" size="large" />
                    <sui-list-content>
                        {{ step.text }}
                    </sui-list-content>
                </sui-list-item>
            </sui-list>
        </sui-modal-content>
        <sui-modal-actions v-if="button">

            <sui-button v-if="'action' in button" :inverted="darkMode"
                :color="button.colour" :content="button.text"
                @click="$store.dispatch(button.action)" />

            <router-link is="sui-button" :to="button.route" :inverted="darkMode" v-else
                :color="button.colour" :content="button.text" />

        </sui-modal-actions>
    </sui-modal>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
    computed: {
        ...mapGetters({
            done: 'progress/done',
            title: 'progress/title',
            steps: 'progress/steps',
            button: 'progress/button',
            visible: 'progress/visible',
        }),
    },
};
</script>
