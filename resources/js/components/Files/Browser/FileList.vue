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
            <tr v-for="file in files">
                <sui-table-cell collapsing>
                    <sui-icon name="folder" /> {{ file.filename }}
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
}
</script>
