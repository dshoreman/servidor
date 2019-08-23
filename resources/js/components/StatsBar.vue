<template>
    <sui-statistics-group v-if="loaded">
        <sui-statistic in-group>
            <sui-statistic-value>
                 {{ hostname }}
            </sui-statistic-value>
            <sui-statistic-label>
                <i class="circular inverted orange linux icon" /> {{ distro }} {{ version }}
            </sui-statistic-label>
        </sui-statistic in-group>
        <!-- todo: add cpu, mem, hdd minigraphs floated on the right -->
        <sui-statistic in-group>
            <sui-statistic-label>
                <sui-icon name="microchip" /> CPU Usage
            </sui-statistic-label>
            <sui-statistic-value>
                {{ cpu_usage }}%
            </sui-statistic-value>
        </sui-statistic in-group>
    </sui-statistics-group>
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
                this.distro = data.os.distro;
                this.version = data.os.version;

                this.loaded = true;
            });
        },
    },
}
</script>
