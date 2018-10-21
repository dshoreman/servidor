<template>
<div>
    <sui-input placeholder="Search Groups..." class="huge fluid" v-model="search" />

    <sui-list divided relaxed>
        <sui-list-item v-for="group in filteredGroups" :key="group.id"
            v-if="group.name.includes(search)">
            <sui-list-icon name="users" size="large" vertical-align="middle"></sui-list-icon>
            <sui-list-content>
                <a is="sui-list-header">{{ group.name }}</a>
                <sui-list-description>
                    <sui-list bulleted horizontal>
                        <span v-for="(user, id) in group.users.split(',')"
                            :key="id" is="sui-list-item">
                            {{ user }}
                        </span>
                    </sui-list>
                </sui-list-description>
            </sui-list-content>
        </sui-list-item>
    </sui-list>
</div>
</template>

<script>
export default {
    data () {
        return {
            groups: [],
            search: '',
        };
    },
    mounted () {
        this.fetchGroups();
    },
    computed: {
        filteredGroups() {
            return this.groups.filter(group => group.name.includes(this.search));
        },
    },
    methods: {
        fetchGroups() {
            axios.get('/api/system/groups').then(response => {
                this.groups = response.data;
            });
        },
    },
}
</script>
