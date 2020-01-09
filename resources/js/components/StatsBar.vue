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
        <sui-menu-menu position="right">
        <sui-menu-item>
            <sui-statistic size="mini" :data-tooltip="cpuTooltip()" data-position="bottom center">
                <sui-statistic-label>
                    <sui-icon name="tachometer alternate" /> CPU Usage
                </sui-statistic-label>
                <sui-statistic-value>
                    {{ cpu_usage }}% ({{ load_avg['5m'] }})
                </sui-statistic-value>
            </sui-statistic>
        </sui-menu-item>
        <sui-menu-item>
            <sui-statistic size="mini" :data-tooltip="ramTooltip()" data-position="bottom center">
                <sui-statistic-label>
                    <sui-icon name="microchip" /> Free RAM
                </sui-statistic-label>
                <sui-statistic-value>
                    {{ ram.free }}M
                </sui-statistic-value>
            </sui-statistic>
        </sui-menu-item>
        <sui-menu-item>
            <sui-statistic size="mini" :data-tooltip="diskTooltip()" data-position="bottom center">
                <sui-statistic-label>
                    <sui-icon name="disk" /> {{ disk.partition }} Usage
                </sui-statistic-label>
                <sui-statistic-value>
                    <!-- {{ disk.free }}MB -->
                    {{ disk.used_pct }} of {{ disk.total }}G
                </sui-statistic-value>
            </sui-statistic>
        </sui-menu-item>
        </sui-menu-menu>
    </sui-menu>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
    data () {
        return {
            loaded: false,
            cpu_usage: 0,
            disk: {},
            distro: '',
            hostname: '',
            load_avg: {},
            ram: {},
            version: '',
        };
    },
    mounted () {
        this.initStatsBar();

        const refreshStatsBar = () => {
            return setInterval(
                () => this.initStatsBar(),
                1000 * 60
            );
        };

        let refreshStatsBarIntervalId = refreshStatsBar();

        const startStopRefreshStatsBar = () => {
            if(document.hidden) {
                clearInterval(refreshStatsBarIntervalId);
            } else {
                refreshStatsBarIntervalId = refreshStatsBar();
            }
        };

        document.addEventListener('visibilitychange', startStopRefreshStatsBar, false);

        this.$once('hook:beforeDestroy', () => {
            document.removeEventListener('visibilitychange', startStopRefreshStatsBar);
        });
    },
    computed: {
        ...mapGetters([
            'loggedIn',
        ]),
    },
    methods: {
        initStatsBar () {
            if (!this.loggedIn) {
                return;
            }
            axios.get('/api/system-info').then(response => {
                const data = response.data;

                this.hostname = data.hostname;
                this.cpu_usage = data.cpu;
                this.load_avg = data.load_average;
                this.ram = data.ram;
                this.disk = data.disk;
                this.distro = data.os.distro;
                this.version = data.os.version;

                this.loaded = true;
            });
        },
        cpuTooltip () {
            return 'Load average: ' + this.load_avg['1m'] + ', '
                 + this.load_avg['5m'] + ', ' + this.load_avg['15m'];
        },
        ramTooltip () {
            return 'Using ' + this.ram.used + 'M of ' + this.ram.total + 'M';
        },
        diskTooltip () {
            return this.disk.used + 'G used; ' + this.disk.free + 'G free';
        },
    },
};
</script>
