<template>
    <sui-form @submit.prevent="save()">

        <sui-form-field :error="'redirects.0.target' in errors">

            <label>Archived Domain</label>

            <sui-input v-model="target" placeholder="example.com" required />

            <sui-label basic color="red" pointing v-if="'redirects.0.target' in errors">
                {{ errors['redirects.0.target'][0] }}
            </sui-label>

        </sui-form-field>

        <sui-form-fields>
            <sui-form-field :width="10">
                <label>Archive Date</label>
                <sui-form-fields>
                    <sui-form-field :width="9">
                        <sui-input type="date" v-model="date" />
                    </sui-form-field>
                    <sui-form-field :width="7">
                        <sui-input type="time" v-model="time" step="1" />
                    </sui-form-field>
                </sui-form-fields>
            </sui-form-field>
            <sui-form-field :width="6" :error="'redirects.0.type' in errors">
                <label>Redirect Type</label>
                <sui-dropdown :options="[{ text: 'Temporary', value: 302 },
                                         { text: 'Permanent', value: 301 }]"
                                         selection v-model="type" />
                <sui-label basic color="red" pointing="left" v-if="'redirects.0.type' in errors">
                    {{ errors['redirects.0.type'][0] }}
                </sui-label>
            </sui-form-field>
        </sui-form-fields>

        <step-buttons @cancel="$emit('cancel')" />

    </sui-form>
</template>

<script>
import StepButtons from '../StepButtons';

export default {
    components: {
        StepButtons,
    },
    props: {
        domain: { type: String, default: '' },
        errors: { type: Object, default: () => ({}) },
    },
    data() {
        return {
            date: '',
            time: '',
            target: '',
            type: 301,
        };
    },
    methods: {
        save() {
            const { domain, type } = this;
            let { date, target, time } = this;

            date = date.replaceAll('-', '');
            time = time.replaceAll(':', '');
            target = `https://web.archive.org/web/${date}${time}/${target}`;

            this.$emit('next', { domain, target, type });
        },
    },
};
</script>
