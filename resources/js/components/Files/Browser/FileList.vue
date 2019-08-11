<template>
    <sui-table compact selectable class="files">
        <thead>
            <tr>
                <th>Filename</th>
                <th>Permissions</th>
                <th>Owner</th>
                <th>Group</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="file in files" @click="open(file)">
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
        </tbody>
    </sui-table>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
    props: [
        'files',
    ],
    computed: {
        ...mapGetters([
            'currentPath',
        ]),
    },
    methods: {
        icon: function (file) {
            if (file.isLink) {
                return 'linkify';
            }
            if (file.isDir) {
                return 'folder';
            }
            if (file.isFile) {
                return 'file';
            }
        },
        open: function (file) {
            if (file.isFile) {
                return this.$router.push({
                    name: 'files.edit',
                    query: {
                        f: this.currentPath + '/' + file.filename,
                    },
                });
            }

            if (!file.isDir) {
                return;
            }

            this.$emit('set-path', file)
        },
    },
}
</script>
