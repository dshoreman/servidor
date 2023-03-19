<template>
    <sui-form @submit.prevent="save()" :inverted="darkMode">

        <sui-form-fields>
            <sui-form-field :width="9">
                <label>Rule Template</label>
                <sui-dropdown selection v-model="ruleTpl"
                    :options="[{ text: 'Archive (via WayBack Machine)', value: 'archive' },
                               { text: 'Custom', value: 'custom' }]" />
            </sui-form-field>

            <sui-form-field :width="7" :error="'type' in errors">
                <label>Redirect Type</label>
                <sui-dropdown :options="[{ text: 'Temporary', value: 302 },
                                         { text: 'Permanent', value: 301 }]"
                                         selection v-model="type" />
                <sui-label basic color="red" pointing="left" v-if="'type' in errors">
                    {{ errors['type'][0] }}
                </sui-label>
            </sui-form-field>
        </sui-form-fields>

        <sui-form-field :error="'target' in errors">
            <label>{{ 'archive' === ruleTpl ? 'Archived' : 'Target' }} Domain</label>
            <sui-input v-model="target" placeholder="example.com" required />
            <sui-label basic color="red" pointing v-if="'target' in errors">
                {{ errors['target'][0] }}
            </sui-label>
        </sui-form-field>

        <sui-form-field>
            <sui-checkbox toggle v-model="appendRequest" label="Append source request URI" />
        </sui-form-field>

        <sui-form-field v-show="'archive' === ruleTpl">
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

        <sui-form-field v-show="target">
            <label>Rule Preview</label>
            <code>{{ realTarget }}</code>
        </sui-form-field>

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
            ruleTpl: 'custom',
            appendRequest: false,
            type: 301,
        };
    },
    computed: {
        realTarget() {
            let { date, target, time } = this;

            date = date.replaceAll('-', '');
            time = time.replaceAll(':', '');

            if ('archive' === this.ruleTpl) {
                target = `https://web.archive.org/web/${date}${time}/${target}`;
            }

            if (this.appendRequest) {
                target += '$request_uri';
            }

            return target;
        },
    },
    methods: {
        save() {
            const { domain, realTarget, type } = this;

            this.$emit('next', { domain, target: realTarget, type });
        },
    },
};
</script>
