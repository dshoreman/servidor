<template>
    <sui-modal basic size="tiny" v-model="show">
        <sui-modal-header>Are you sure?</sui-modal-header>
        <sui-modal-content>
            <p>If you cancel now, all changes will be lost.</p>
        </sui-modal-content>
        <sui-modal-actions>
            <sui-button type="button" basic inverted negative @click="discard">
                Discard changes
            </sui-button>
            <sui-button type="button" basic inverted negative
                v-if="canGoBack" @click="discardAndReturn">
                Discard and go back
            </sui-button>
            <sui-button type="button" basic inverted @click="cancel">
                Continue editing
            </sui-button>
        </sui-modal-actions>
    </sui-modal>
</template>

<script>
export default {
    data() {
        return {
            backRoute: '',
            callbacks: {},
            show: false,
            canGoBack: false,
        };
    },
    methods: {
        cancel() {
            this.callbacks.abort();
            this.hide();
        },
        discard() {
            this.callbacks.discard();
            this.hide();
        },
        discardAndReturn() {
            this.callbacks.discard();
            this.$emit('leavebypass');
            this.$router.push({ name: this.back });
        },
        hide() {
            this.show = false;
        },
        prompt(discard, abort, back) {
            /* eslint-disable no-empty-function */
            this.callbacks.discard = discard ? discard : () => { };
            this.callbacks.abort = abort ? abort : () => { };
            /* eslint-enable no-empty-function */

            this.canGoBack = !!back;
            this.back = back;
            this.show = true;
        },
    },
};
</script>
