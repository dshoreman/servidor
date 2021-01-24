<template>
    <div>
        <sui-divider hidden />

        <sui-button negative @click="$emit('cancel')"
                    content="Cancel"
                    icon="close"
                    label-position="left"
                    type="button" />

        <sui-button primary floated="right"
                    content="Next"
                    icon="right arrow"
                    label-position="right" />

    </div>
</template>
