<template>
    <div>
        <file-browser path="/var/www" />
    </div>
</template>

<script>
import FileBrowser from '../components/Files/Browser.vue';

export default {
    components: {
        FileBrowser,
    },
}
</script>
