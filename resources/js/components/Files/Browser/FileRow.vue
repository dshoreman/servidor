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
        <sui-table-cell collapsing>
            <sui-button compact color="red"
                icon="trash alternate outline"
                @click.stop="remove(file)" />
        </sui-table-cell>
    </tr>
</template>

<script>
export default {
    props: [
        'file',
        'path',
    ],
    methods: {
        icon() {
            if (this.file.isLink) {
                return 'linkify';
            }
            if (this.file.isDir) {
                return 'folder';
            }
            if (this.file.isFile) {
                return 'file';
            }

            return 'question circle';
        },
        iconColour() {
            if (this.file.isDir) {
                return 'blue';
            }
            if (this.file.isFile) {
                return 'violet';
            }

            return 'red';
        },
        open() {
            const prefix = '/' === this.path ? '' : this.path,
                route = { name: 'files' },
                target = `${prefix}/${this.file.filename}`;

            if (this.file.isFile) {
                route.name = 'files.edit';
                route.query = { f: target };
            } else {
                route.params = { path: target };
            }

            this.$router.push(route);
        },
        remove(file) {
            if (!confirm("Deletion is permanent! Are you sure?")) {
                return;
            }
            this.$store.dispatch('files/delete', file);
        },
    },
};
</script>
