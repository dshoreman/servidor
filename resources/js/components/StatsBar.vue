<template>
    <sui-menu inverted fixed="top" v-if="loaded">
        <sui-menu-item>
            <sui-statistic horizontal size="tiny" class="hostname">
                <sui-statistic-value>
                    <i class="circular tiny inverted orange linux icon" />
                     {{ hostname }}
                </sui-statistic-value>
                <sui-statistic-label>
                    on {{ distro }} {{ version }}
                </sui-statistic-label>
            </sui-statistic>
        </sui-menu-item>
        <sui-menu-item>
            <!-- todo: add hdd  -->
            <sui-statistic size="mini">
                <sui-statistic-label>
                    <sui-icon name="microchip" /> CPU Usage
                </sui-statistic-label>
                <sui-statistic-value>
                    {{ cpu_usage }}%
                </sui-statistic-value>
            </sui-statistic>
        </sui-menu-item>
        <sui-menu-item>
            <sui-statistic size="mini">
                <sui-statistic-label>
                    <sui-icon name="microchip" /> Free RAM
                </sui-statistic-label>
                <sui-statistic-value>
                    {{ ram.free }}MB
                </sui-statistic-value>
            </sui-statistic>
        </sui-menu-item>
    </sui-menu>
</template>

<script>
export default {
    data () {
        return {
            loaded: false,
            hostname: '',
            distro: '',
            version: '',
        };
    },
    mounted () {
        this.initStatsBar();
    },
    methods: {
        initStatsBar () {
            axios.get('/api/system-info').then(response => {
                let data = response.data;

                this.hostname = data.hostname;
                this.cpu_usage = data.cpu;
                this.ram = data.ram;
                this.distro = data.os.distro;
                this.version = data.os.version;

                this.loaded = true;
            });
        },
    },
}
</script>
