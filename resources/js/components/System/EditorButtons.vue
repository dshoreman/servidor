<template>
    <div>
        <sui-button negative icon="trash" type="button" v-if="editing"
            content="Delete" floated="right" @click="toggle" />
        <sui-modal basic size="tiny" v-model="confirmOpen">
            <sui-modal-header>Are you sure?</sui-modal-header>
            <sui-modal-content>
                <p>
                    This action is permanent!
                    Do you <em>really</em> want to delete this {{ item }}?
                </p>
                <slot />
            </sui-modal-content>
            <sui-modal-actions>
                <sui-button basic type="button" inverted @click="toggle">Cancel</sui-button>
                <sui-button basic type="button" inverted negative @click="$emit('delete')">
                    Delete
                </sui-button>
            </sui-modal-actions>
        </sui-modal>

        <sui-button-group fluid>
            <sui-button type="button" @click="$emit('cancel')">Cancel</sui-button>
            <sui-button-or />
            <sui-button type="submit" positive :content="editing ? 'Update' : 'Create'" />
        </sui-button-group>
    </div>
</template>

<script>
export default {
    data() {
        return {
            confirmOpen: false,
        };
    },
    props: {
        editing: {
            type: Boolean,
            default: false,
        },
        item: {
            type: String,
            default: 'item',
        },
    },
    methods: {
        toggle() {
            this.confirmOpen = !this.confirmOpen;
        },
    },
};
</script>
