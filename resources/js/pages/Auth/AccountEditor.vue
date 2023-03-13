<template>
    <sui-grid container>
        <sui-grid-column>
            <sui-form @submit.prevent="save()">
                <h2>Account Settings</h2>

                <sui-segment>
                    <sui-form-field :error="'name' in errors">
                        <label>Account Name</label>
                        <sui-input required v-model="data.name"
                            :placeholder="user.name" />
                        <sui-label basic color="red" pointing
                            v-if="'name' in errors">
                            {{ errors['name'][0] }}
                        </sui-label>
                    </sui-form-field>

                    <sui-form-field :error="'email' in errors">
                        <label>Email Address</label>
                        <sui-input required type="email" v-model="data.email"
                            :placeholder="user.email" />
                        <sui-label basic color="red" pointing
                            v-if="'email' in errors">
                            {{ errors['email'][0] }}
                        </sui-label>
                    </sui-form-field>
                </sui-segment>

                <sui-segment>
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
                </sui-segment>

                <sui-button primary floated="right"
                            content="Save changes" />

            </sui-form>
        </sui-grid-column>
    </sui-grid>
</template>

<script>
import { mapGetters, mapState } from 'vuex';

export default {
    data() {
        return {
            data: {},
            errors: {},
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
};
</script>
