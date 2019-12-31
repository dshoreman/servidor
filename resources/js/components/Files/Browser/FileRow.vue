<template>
    <tr @click="open(file)">
        <sui-table-cell collapsing>
            <sui-icon :color="iconColour()" :name="icon()" /> {{ file.filename }}
            <span v-if="file.isLink">
                <sui-icon name="alternate long arrow right" /> {{ file.target }}
            </span>
        </sui-table-cell>
        <sui-table-cell :data-tooltip="file.perms.text" data-position="right center"
            collapsing>{{ file.perms.octal }}</sui-table-cell>
        <td>{{ file.owner }}</td>
        <td>{{ file.group }}</td>
    </tr>
</template>

<script>
export default {
    props: [
        'file',
        'path',
    ],
    methods: {
        icon: function () {
            if (this.file.isLink) {
                return 'linkify';
            }
            if (this.file.isDir) {
                return 'folder';
            }
            if (this.file.isFile) {
                return 'file';
            }
        },
        iconColour: function () {
            if (this.file.isDir) {
                return 'blue';
            }
            if (this.file.isFile) {
                return 'violet';
            }
        },
        open: function () {
            let prefix = this.path == '/' ? '' : this.path,
                target = prefix + '/' + this.file.filename,
                route = { name: 'files' };

            if (this.file.isFile) {
                route.name = 'files.edit';
                route.query = { f: target };
            } else {
                route.params = { path: target };
            }

            this.$router.push(route);
        },
    },
}
</script>
