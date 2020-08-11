<template>
    <sui-table compact selectable :inverted="darkMode" class="files" v-if="!files.error">
        <thead>
            <tr>
                <th>Filename</th>
                <th>Permissions</th>
                <th>Owner</th>
                <th>Group</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <file-row v-for="file in files" :key="file.id"
                :file="file" :path="path" @rename="toggleRename" />
        </tbody>

        <sui-modal size="tiny" v-model="promptRename">
            <sui-modal-header>Renaming {{ oldFile.filename }}</sui-modal-header>
            <sui-modal-content>
                <sui-input class="fluid" v-model="newPath" @keyup.enter="rename"
                    style="border: 1px solid rgba(34, 36, 38, 0.15)" />
            </sui-modal-content>
            <sui-modal-actions>
                <sui-button positive @click.native="rename" content="OK" />
            </sui-modal-actions>
        </sui-modal>
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
    data() {
        return {
            newPath: '',
            oldFile: {},
            promptRename: false,
        };
    },
    props: [
        'files',
        'path',
    ],
    components: {
        FileRow,
    },
    methods: {
        rename() {
            this.$store.dispatch('files/rename', {
                file: this.oldFile,
                newPath: this.newPath,
            }).then(() => {
                this.promptRename = false;
            });
        },
        toggleRename(file) {
            this.oldFile = file;
            this.newPath = `${file.filepath}/${file.filename}`;

            this.promptRename = true;
        },
    },
};
</script>
