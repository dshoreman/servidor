<template>
    <tr @click="open(file)">
        <sui-table-cell collapsing>
            <sui-icon :name="icon(file)" /> {{ file.filename }}
            <span v-if="file.isLink">
                <sui-icon name="alternate long arrow right" /> {{ file.target }}
            </span>
        </sui-table-cell>
        <td>{{ file.perms }}</td>
        <td>{{ file.owner }}</td>
        <td>{{ file.group }}</td>
    </tr>
</template>

<script>
import { mapGetters } from 'vuex';

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
        open: function () {
            if (this.file.isFile) {
                return this.$router.push({
                    name: 'files.edit',
                    query: {
                        f: this.path + '/' + this.file.filename,
                    },
                });
            }

            if (!this.file.isDir) {
                return;
            }

            this.$emit('cd', this.file)
        },
    },
}
</script>
