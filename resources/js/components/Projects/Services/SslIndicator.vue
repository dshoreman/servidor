<template>
    <sui-label size="tiny" :color="txt(...colours)" :title="txt(...details)">
        <sui-icon :name="txt(...icons)" /> {{ txt(...statuses) }}
    </sui-label>
</template>

<script>
export default {
    props: {
        service: { type: Object, default: () => ({}) },
    },
    data: () => ({
        icons: ['lock', 'unlock'],
        colours: ['green', 'orange', 'red'],
        statuses: ['SSL Enabled', 'SSL Disabled'],
        details: [
            'SSL is enabled and HTTP links are redirected!',
            'SSL is enabled, but HTTP links are not redirected.',
            'SSL is not enabled. Data may be insecure.',
        ],
    }),
    methods: {
        txt(good, ok, bad = '') {
            if (this.service.config.ssl && this.service.config.sslRedirect) {
                return good;
            }
            if ('' === bad) {
                return this.service.config.ssl ? good : ok;
            }

            return this.service.config.ssl ? ok : bad;
        },
    },
};
</script>
