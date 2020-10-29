<template>
    <div>
        <sui-divider hidden />

        <sui-button negative @click="toggleCancelPrompt"
                    content="Cancel"
                    icon="close"
                    label-position="left"
                    type="button" />

        <sui-button primary floated="right"
                    content="Next"
                    icon="right arrow"
                    label-position="right" />

        <sui-modal basic size="tiny" v-model="showCancel">
            <sui-modal-header>Are you sure?</sui-modal-header>
            <sui-modal-content>
                <p>If you cancel now, all changes will be lost.</p>
            </sui-modal-content>
            <sui-modal-actions>
                <sui-button type="button" basic inverted negative @click="cancel">
                    Discard changes
                </sui-button>
                <sui-button type="button" basic inverted negative @click="cancelAndReturn">
                    Discard and go back
                </sui-button>
                <sui-button type="button" basic inverted @click="toggleCancelPrompt">
                    Continue editing
                </sui-button>
            </sui-modal-actions>
        </sui-modal>
    </div>
</template>

<script>
export default {
    data() {
        return {
            showCancel: false,
        };
    },
    methods: {
        cancel() {
            this.$emit('cancelled');
            this.showCancel = false;
        },
        cancelAndReturn() {
            this.cancel();
            this.$router.push({
                name: 'projects',
            });
        },
        toggleCancelPrompt() {
            this.showCancel = !this.showCancel;
        },
    },
};
</script>
