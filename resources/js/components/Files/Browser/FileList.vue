<template>
    <sui-table compact selectable :inverted="darkMode" class="files">
        <thead>
            <tr>
                <th>Filename</th>
                <th>Permissions</th>
                <th>Owner</th>
                <th>Group</th>
            </tr>
        </thead>
        <tbody>
            <file-row v-for="file in files" :key="file.id"
                :file="file" :path="path" />
        </tbody>
    </sui-table>
</template>

<script>
import FileRow from './FileRow';

export default {
    props: [
        'files',
        'path',
    ],
    components: {
        FileRow,
    },
};
</script>
