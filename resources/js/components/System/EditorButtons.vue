<template>
    <div>
        <sui-button negative icon="trash" type="button" v-if="editing"
            content="Delete" floated="right" @click="$emit('delete')" />

        <sui-button-group fluid>
            <sui-button type="button" @click="$emit('cancel')">Cancel</sui-button>
            <sui-button-or />
            <sui-button type="submit" positive :content="editing ? 'Update' : 'Create'" />
        </sui-button-group>
    </div>
</template>

<script>
export default {
    props: [
        'editing',
    ],
};
</script>
