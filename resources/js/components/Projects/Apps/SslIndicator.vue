<template>
    <sui-label size="tiny" :color="txt(...colours)" :title="txt(...details)">
        <sui-icon :name="txt(...icons)" /> {{ txt(...statuses) }}
    </sui-label>
</template>

<script>
export default {
    props: {
        app: { type: Object, default: () => ({}) },
    },
    data: () => ({
        icons: ['lock', 'unlock'],
        colours: ['green', 'red'],
        statuses: ['SSL Enabled', 'SSL Disabled'],
        details: [
            'SSL is enabled!',
            'SSL is not enabled. Data may be insecure.',
        ],
    }),
    methods: {
        txt(good, bad) {
            if (this.app.config.ssl) {
                return good;
            }

            return bad;
        },
    },
};
</script>
