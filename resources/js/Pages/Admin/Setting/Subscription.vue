<!-- <template>
    <AppLayout>
        <div>
            <h2 class="text-xl mb-1">{{ $t('Subscription settings') }}</h2>
            <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                </svg>
                <span class="ml-1 mt-1">{{ $t('Configure how your subscriptions are set') }}</span>
            </p>
        </div>
        <form @submit.prevent="submitForm()">
            <div class="space-y-12">
                <div class="pb-12">
                    <div class="pt-5">
                        <div class="flex gap-x-10 md:w-2/3 pb-6 border-b mb-4">
                            <div class="w-[80%]">
                                <span>{{ $t('Enable custom payment amounts') }}</span>
                                <div class="text-xs text-slate-700 flex items-center">
                                    <span>{{ $t(`Allow users to make custom payments that can be applied to their
                                        subscription fees automatically.`) }}</span>
                                </div>
                            </div>
                            <div class="w-[20%]">
                                <FormToggleSwitch v-model="form.enable_custom_payment" class="float-right" />
                            </div>
                        </div>
                    </div>
                    <div class="grid gap-3 grid-cols-2 pb-6 md:w-2/3 border-b mb-4">
                        <FormInput v-model="form.trial_period" :name="$t('Trial period (in days)')"
                            :error="form.errors.trial_period" :type="'number'" :class="'col-span-2'" />
                        <span class="col-span-2 text-xs">{{ $t(`Please note that if you put zero then the user will be
                            prompted to first subscribe before using the app`) }}</span>
                    </div>
                    <div class="mb-4">
                        <h2 class="text-base text-gray-900">{{ $t('Trial period limits') }}</h2>
                        <p class="text-sm leading-6 mb-5">{{ $t('Define the usage limits during the trial period') }}
                        </p>
                    </div>
                    <div class="bg-orange-100 p-2 rounded-md shadow-sm md:w-2/3 mb-4 flex items-center gap-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                        </svg>
                        <p class="text-sm leading-6">{{ $t('For unlimited usage, set -1 as the value') }}</p>
                    </div>
                    <div class="grid gap-3 grid-cols-2 pb-6 border-b md:w-2/3">
                        <FormInput v-model="form.trial_limits.messages" :name="$t('Message limit')"
                            :error="form.errors?.['trial_limits.messages']" :type="'number'" :class="'col-span-1'" />
                        <FormInput v-model="form.trial_limits.campaigns" :name="$t('Campaign limit')"
                            :error="form.errors?.['trial_limits.campaigns']" :type="'number'" :class="'col-span-1'" />
                        <FormInput v-model="form.trial_limits.contacts" :name="$t('Contacts limit')"
                            :error="form.errors?.['trial_limits.contacts']" :type="'number'" :class="'col-span-1'" />
                        <FormInput v-model="form.trial_limits.automated_replies" :name="$t('Automated Replies limit')"
                            :error="form.errors?.['trial_limits.automated_replies']" :type="'number'"
                            :class="'col-span-1'" />
                        <FormInput v-model="form.trial_limits.users" :name="$t('User limit')"
                            :error="form.errors?.['trial_limits.users']" :type="'number'" :class="'col-span-1'" />
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-x-6 md:w-2/3">
                        <button type="button" class="text-sm leading-6 text-gray-900">{{ $t('Cancel') }}</button>
                        <button
                            :class="['inline-flex justify-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2', { 'opacity-50': isLoading }]"
                            :disabled="isLoading">
                            <svg v-if="isLoading" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                    opacity=".5" />
                                <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                    <animateTransform attributeName="transform" dur="1s" from="0 12 12"
                                        repeatCount="indefinite" to="360 12 12" type="rotate" />
                                </path>
                            </svg>
                            <span v-else>{{ $t('Save') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </AppLayout>
</template>
<script setup>
import AppLayout from "./Layout/App.vue";
import { useForm } from "@inertiajs/vue3";
import { defineProps, ref } from 'vue';
import FormInput from '@/Components/FormInput.vue';
import FormToggleSwitch from '@/Components/FormToggleSwitch.vue';

const props = defineProps({
    config: {
        type: Object,
        required: true
    }
});

const getValueByKey = (key) => {
    const found = props.config.find(item => item.key === key);
    return found ? found.value : '';
};

const limits = getValueByKey('trial_limits') ? JSON.parse(getValueByKey('trial_limits')) : {};

const isLoading = ref(false);
const form = useForm({
    enable_custom_payment: getValueByKey('enable_custom_payment') == 1 || getValueByKey('enable_custom_payment') == '' ? true : false,
    trial_period: getValueByKey('trial_period'),
    trial_limits: {
        users: limits?.users ?? '-1',
        messages: limits?.messages ?? '-1',
        contacts: limits?.contacts ?? '-1',
        campaigns: limits?.campaigns ?? '-1',
        automated_replies: limits?.automated_replies ?? '-1'
    }
})

const submitForm = async () => {
    form.put('/admin/settings?type=subscription', {
        preserveScroll: true,
    });
};
</script> -->

<!-- ============================================ NEW UI CODE ============================================= -->


<template>
    <AppLayout>
        <div class="p-6 md:p-8 space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <form @submit.prevent="submitForm()" class="w-full mx-auto space-y-6">

                <!-- Custom Payment Settings Card -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                    <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Subscription Settings') }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Configure how your subscriptions are set') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Enable Custom Payment -->
                        <div
                            class="flex items-center justify-between p-4 rounded-xl bg-slate-50/50 hover:bg-slate-50 transition-colors">
                            <div class="flex-1">
                                <div class="font-semibold text-slate-900 mb-1">{{ $t('Enable custom payment amounts') }}
                                </div>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t(`Allow users to make custom payments that can be applied to their
                                        subscription fees automatically.`) }}</span>
                                </div>
                            </div>
                            <FormToggleSwitch v-model="form.enable_custom_payment" />
                        </div>
                    </div>
                </div>

                <!-- Trial Period Card -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                    <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Trial Period') }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Configure trial period duration') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Trial Period Input -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('Trial Duration') }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t(`Set to 0 to require immediate subscription`) }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3">
                                <FormInput v-model="form.trial_period" :name="$t('Trial period (in days)')"
                                    :error="form.errors.trial_period" :type="'number'" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trial Period Limits Card -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                    <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14c-1.66 0-3-1.34-3-3s1.34-3 3-3s3 1.34 3 3s-1.34 3-3 3z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Trial Period Limits') }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Define the usage limits during the trial period') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Info Alert -->
                        <div
                            class="bg-gradient-to-r from-orange-50 to-orange-100/50 border-2 border-orange-200 rounded-2xl p-4 flex items-start space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                class="text-[#ff5100] flex-shrink-0 mt-0.5">
                                <path fill="currentColor"
                                    d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                            </svg>
                            <p class="text-sm font-medium text-gray-700">
                                {{ $t('For unlimited usage, set -1 as the value') }}
                            </p>
                        </div>

                        <!-- Limits Grid -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('Usage Limits') }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Set limits for trial period features') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <FormInput v-model="form.trial_limits.messages" :name="$t('Message limit')"
                                    :error="form.errors?.['trial_limits.messages']" :type="'number'" />
                                <FormInput v-model="form.trial_limits.campaigns" :name="$t('Campaign limit')"
                                    :error="form.errors?.['trial_limits.campaigns']" :type="'number'" />
                                <FormInput v-model="form.trial_limits.contacts" :name="$t('Contacts limit')"
                                    :error="form.errors?.['trial_limits.contacts']" :type="'number'" />
                                <FormInput v-model="form.trial_limits.automated_replies"
                                    :name="$t('Automated Replies limit')"
                                    :error="form.errors?.['trial_limits.automated_replies']" :type="'number'" />
                                <FormInput v-model="form.trial_limits.users" :name="$t('User limit')"
                                    :error="form.errors?.['trial_limits.users']" :type="'number'" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-4 pt-4">
                    <button type="button"
                        class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition-all duration-200">
                        {{ $t('Cancel') }}
                    </button>
                    <button type="submit"
                        class="group px-8 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="isLoading">
                        <span v-if="isLoading" class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                    opacity=".5" />
                                <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                    <animateTransform attributeName="transform" dur="1s" from="0 12 12"
                                        repeatCount="indefinite" to="360 12 12" type="rotate" />
                                </path>
                            </svg>
                            {{ $t('Saving...') }}
                        </span>
                        <span v-else class="flex items-center gap-2">
                            {{ $t('Save Settings') }}
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="m9.55 18l-5.7-5.7l1.425-1.425L9.55 15.15l9.175-9.175L20.15 7.4z" />
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./Layout/App.vue";
import { useForm } from "@inertiajs/vue3";
import { defineProps, ref } from 'vue';
import FormInput from '@/Components/FormInput.vue';
import FormToggleSwitch from '@/Components/FormToggleSwitch.vue';

const props = defineProps({
    config: {
        type: Object,
        required: true
    }
});

const getValueByKey = (key) => {
    const found = props.config.find(item => item.key === key);
    return found ? found.value : '';
};

const limits = getValueByKey('trial_limits') ? JSON.parse(getValueByKey('trial_limits')) : {};

const isLoading = ref(false);
const form = useForm({
    enable_custom_payment: getValueByKey('enable_custom_payment') == 1 || getValueByKey('enable_custom_payment') == '' ? true : false,
    trial_period: getValueByKey('trial_period'),
    trial_limits: {
        users: limits?.users ?? '-1',
        messages: limits?.messages ?? '-1',
        contacts: limits?.contacts ?? '-1',
        campaigns: limits?.campaigns ?? '-1',
        automated_replies: limits?.automated_replies ?? '-1'
    }
})

const submitForm = async () => {
    form.put('/admin/settings?type=subscription', {
        preserveScroll: true,
    });
};
</script>