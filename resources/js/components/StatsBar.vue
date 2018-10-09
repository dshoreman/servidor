<template lang="html">
    <sui-statistics-group>
        <sui-statistic in-group>
            <sui-statistic-value>
                 {{ hostname }}
            </sui-statistic-value>
            <sui-statistic-label>
                <i class="circular inverted orange linux icon" /> Ubuntu 18.04
            </sui-statistic-label>
        </sui-statistic in-group>
        <!-- todo: add cpu, mem, hdd minigraphs floated on the right -->
    </sui-statistics-group>
</template>

<script>
export default {
  props: ['hostname']
}
</script>
