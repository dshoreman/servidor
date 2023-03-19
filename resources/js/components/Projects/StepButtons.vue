<template>
    <div>
        <sui-divider hidden />

        <sui-button @click="$emit('cancel')"
                    inverted color="red" v-if="darkMode"
                    content="Cancel"
                    icon="close"
                    label-position="left"
                    type="button" />
        <sui-button @click="$emit('cancel')"
                    v-else negative
                    content="Cancel"
                    icon="close"
                    label-position="left"
                    type="button" />

        <sui-button primary floated="right"
                    :inverted="darkMode"
                    content="Next"
                    icon="right arrow"
                    label-position="right" />

    </div>
</template>
