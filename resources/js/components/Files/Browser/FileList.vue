<template>
    <sui-table compact selectable :inverted="darkMode" class="files" v-if="!files.error">
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
    <sui-segment class="placeholder" :inverted="darkMode" v-else>
        <sui-header icon>
            <sui-icon v-if="files.error.code == 404" name="search" color="teal" />
            <sui-icon v-else name="bug" color="orange" />
            {{ files.error.msg }}
        </sui-header>
    </sui-segment>
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
