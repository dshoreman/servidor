<template>
    <sui-grid container>
        <sui-grid-column>
            <h2>Account Settings</h2>

            <sui-segment>
                <sui-form @submit.prevent="saveChanges()">
                    <sui-form-field :error="'name' in errors">
                        <label>Account Name</label>
                        <sui-input v-model="data.name"
                            :placeholder="user.name" />
                        <sui-label basic color="red" pointing
                            v-if="'name' in errors">
                            {{ errors['name'][0] }}
                        </sui-label>
                    </sui-form-field>

                    <sui-form-field :error="'email' in errors">
                        <label>Email Address</label>
                        <sui-input type="email" v-model="data.email"
                            :placeholder="user.email" />
                        <sui-label basic color="red" pointing
                            v-if="'email' in errors">
                            {{ errors['email'][0] }}
                        </sui-label>
                    </sui-form-field>

                    <sui-button primary type="submit"
                                content="Save changes" />
                </sui-form>
            </sui-segment>

            <sui-segment>
                <sui-form>
                    <sui-form-field>
                        <label>Current Password</label>
                        <sui-input required type="password" v-model="data.currentPassword" />
                        <sui-label basic color="red" pointing
                            v-if="'currentPassword' in errors">
                            {{ errors['currentPassword'][0] }}
                        </sui-label>
                    </sui-form-field>

                    <sui-form-field>
                        <label>New Password</label>
                        <sui-input required type="password" v-model="data.password" />
                        <sui-label basic color="red" pointing
                            v-if="'password' in errors">
                            {{ errors['password'][0] }}
                        </sui-label>
                    </sui-form-field>

                    <sui-form-field>
                        <label>Confirm New Password</label>
                        <sui-input required type="password" v-model="data.confirmPassword" />
                        <sui-label basic color="red" pointing
                            v-if="'confirmPassword' in errors">
                            {{ errors['confirmPassword'][0] }}
                        </sui-label>
                    </sui-form-field>

                    <sui-button primary type="submit"
                                content="Change password" />
                </sui-form>
            </sui-segment>

        </sui-grid-column>
    </sui-grid>
</template>

<script>
import { mapGetters, mapState } from 'vuex';

export default {
    data() {
        return {
            data: {},
            errors: [],
        };
    },
    computed: {
        ...mapGetters({
            projects: 'projects/all',
        }),
        ...mapState({
            user: state => state.Auth.user,
        }),
    },
    methods: {
        async saveChanges() {
            const { name, email } = this.data;

            try {
                await this.$store.dispatch('updateAccount', { name, email });
            } catch (error) {
                this.errors.push(error);
            }
        },
    },
};
</script>
