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
            <tr>
                <sui-table-cell collapsing>
                    <sui-icon name="folder" /> bin -> usr/bin
                </sui-table-cell>
                <td>lrwxrwxrwx</td>
                <td>root</td>
                <td>root</td>
            </tr>
            <tr>
                <sui-table-cell collapsing>
                    <sui-icon name="folder" /> etc
                </sui-table-cell>
                <td>drewr-xr-x</td>
                <td>root</td>
                <td>root</td>
            </tr>
            <tr>
                <sui-table-cell collapsing>
                    <sui-icon name="folder" /> home
                </sui-table-cell>
                <td>drwxr-xr-x</td>
                <td>root</td>
                <td>root</td>
            </tr>
            <tr>
                <sui-table-cell collapsing>
                    <sui-icon name="folder" /> var
                </sui-table-cell>
                <td>drwxr-xr-x</td>
                <td>root</td>
                <td>root</td>
            </tr>
        </tbody>
    </sui-table>
</template>

<script>
export default {
}
</script>
