<template>
    <sui-table compact>
        <thead>
            <tr>
                <th>Filename</th>
                <th>Permissions</th>
                <th>Owner</th>
                <th>Group</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="file in files" @click="$emit('set-path', file)">
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
export default {
    props: [
        'files',
    ],
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
        }
    },
}
</script>
