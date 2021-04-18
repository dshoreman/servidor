<template>
    <sui-modal size="tiny" v-model="visible">
        <sui-modal-header>{{ title }}</sui-modal-header>
        <sui-progress attached top :percent="done" />
        <sui-modal-content>
            <sui-list>
                <sui-list-item v-for="step in steps" :key="step.name">
                    <sui-icon :name="step.icon" color="green" size="large" />
                    <sui-list-content>
                        {{ step.text }}
                    </sui-list-content>
                </sui-list-item>
            </sui-list>
        </sui-modal-content>
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
            visible: 'progress/visible',
        }),
    },
};
</script>
