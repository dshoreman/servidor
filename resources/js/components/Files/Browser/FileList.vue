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
            <file-row v-for="file in files" :key="file.id" :file="file"
                :path="currentPath" @cd="$emit('cd', $event)" />
        </tbody>
    </sui-table>
</template>

<script>
import { mapGetters } from 'vuex';
import FileRow from './FileRow';

export default {
    props: [
        'files',
    ],
    components: {
        FileRow,
    },
    computed: {
        ...mapGetters({
            currentPath: 'files/currentPath',
        }),
    },
}
</script>
