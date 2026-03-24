<!-- <template>
    <AppLayout>
        <div class="p-8 rounded-[5px] text-[#000] overflow-y-scroll">
            <div class="flex justify-between">
                <div>
                    <h1 v-if="props.plan === null" class="text-xl mb-1">{{ $t('Create plan') }}</h1>
                    <h1 v-else class="text-xl mb-1">{{ $t('Update plan') }}</h1>
                    <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                        </svg>
                        <span v-if="props.plan === null" class="ml-1 mt-1">{{ $t('Create plan') }}</span>
                        <span v-else class="ml-1 mt-1">{{ $t('Update plan') }}</span>
                    </p>
                </div>
                <div>
                    <Link href="/admin/plans"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-300 flex items-center space-x-2">
                    <ArrowLeft class="w-4 h-4" />
                    <span>{{ $t('Back') }}</span></Link>
                </div>
            </div>
            <form @submit.prevent="submitForm()" class="bg-white border py-5 px-5 rounded-[0.5rem]">
                <div class="sm:flex border-b py-5">
                    <div class="hidden sm:block sm:w-[40%] mb-1">
                        <h1 class="text-sm text-gray-500 tracking-[0px]">{{ $t('Name') }}</h1>
                    </div>
                    <div class="sm:w-[60%] sm:flex space-x-6">
                        <div class="sm:w-[80%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormInput v-model="form.name" :name="$t('Name')" :error="form.errors.name" :type="'text'"
                                :class="'sm:col-span-6'" />
                        </div>
                    </div>
                </div>
                <div class="sm:flex border-b py-5">
                    <div class="hidden sm:block sm:w-[40%] mb-1">
                        <h1 class="text-sm text-gray-500 tracking-[0px]">{{ $t('Status') }}</h1>
                    </div>
                    <div class="sm:w-[60%] sm:flex space-x-6">
                        <div class="sm:w-[80%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormSelect v-model="form.status" :options="statusOptions" :error="form.errors.status"
                                :name="$t('Status')" :class="'sm:col-span-6'" :placeholder="$t('Select status')" />
                        </div>
                    </div>
                </div>
                <div class="sm:flex border-b py-5">
                    <div class="hidden sm:block w-[40%] mb-1">
                        <h1 class="text-sm text-gray-500 tracking-[0px]">{{ $t('Pricing details') }}</h1>
                    </div>
                    <div class="sm:w-[60%] sm:flex space-x-6">
                        <div class="sm:w-[80%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormInput v-model="form.price" :name="$t('Price')" :error="form.errors.price"
                                :type="'number'" :class="'sm:col-span-3'" />
                            <FormSelect v-model="form.period" :options="periodOptions" :error="form.errors.period"
                                :name="$t('Period')" :class="'sm:col-span-3'" :placeholder="$t('Select period')" />
                        </div>
                    </div>
                </div>
                <div class="sm:flex py-5 border-b">
                    <div class="hidden sm:block w-[40%] mb-1">
                        <h1 class="text-sm text-gray-500 tracking-[0px]">{{ $t('Plan limit') }}</h1>
                    </div>
                    <div class="sm:w-[60%]">
                        <div class="bg-orange-100 p-2 rounded-md shadow-sm sm:w-[80%] mb-4 flex items-center gap-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                            </svg>
                            <p class="text-sm leading-6">{{ $t('For unlimited usage, set -1 as the value') }}</p>
                        </div>
                        <div class="sm:w-[80%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormInput v-model="form.campaign_limit" :name="$t('Campaign limit')"
                                :error="form.errors.campaign_limit" :type="'number'" :class="'sm:col-span-3'" />
                            <FormInput v-model="form.message_limit" :name="$t('Message limit')"
                                :error="form.errors.message_limit" :type="'number'" :class="'sm:col-span-3'" />
                            <FormInput v-model="form.contacts_limit" :name="$t('Contacts limit')"
                                :error="form.errors.contacts_limit" :type="'number'" :class="'sm:col-span-3'" />
                            <FormInput v-model="form.canned_replies_limit" :name="$t('Canned replies limit')"
                                :error="form.errors.canned_replies_limit" :type="'number'" :class="'sm:col-span-3'" />
                            <FormInput v-model="form.team_limit" :name="$t('User limit')"
                                :error="form.errors.team_limit" :type="'number'" :class="'sm:col-span-3'" />
                        </div>
                    </div>
                </div>
                <div class="sm:flex py-5 border-b">
                    <div class="hidden sm:block w-[40%] mb-1">
                        <h1 class="text-sm text-gray-500 tracking-[0px]">{{ $t('Addons access') }}</h1>
                        <p class="text-xs text-slate-700">{{ $t('Select addons that are available in this plan') }}</p>
                    </div>
                    <div class="sm:w-[60%]">
                        <div class="sm:w-[80%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <div v-for="addon in addons" class="sm:col-span-3">
                                <div class="text-sm mb-2">{{ addon }}</div>
                                <FormToggleSwitch v-model="form.addons[addon]" />
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="props.enable_ai_billing == 1 && form.addons['AI Assistant']" class="sm:flex py-5 border-b">
                    <div class="hidden sm:block w-[40%] mb-1">
                        <h1 class="text-sm text-gray-500 tracking-[0px]">{{ $t('AI Assistant Usage Limit') }}</h1>
                    </div>
                    <div class="sm:w-[60%]">
                        <div class="bg-orange-100 p-2 rounded-md shadow-sm sm:w-[80%] mb-4 flex items-center gap-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                            </svg>
                            <p class="text-sm leading-6">{{ $t('For unlimited usage, set -1 as the value') }}</p>
                        </div>
                        <div class="sm:w-[80%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormInput v-model="form.ai_text_response_limit" :name="$t('AI Text Response Limit')"
                                :error="form.errors.ai_text_response_limit" :type="'number'" :class="'sm:col-span-3'" />
                            <FormInput v-model="form.ai_audio_response_limit" :name="$t('AI Audio Response Limit')"
                                :error="form.errors.ai_audio_response_limit" :type="'number'"
                                :class="'sm:col-span-3'" />
                        </div>
                    </div>
                </div>
                <div class="py-5 sm:flex">
                    <div class="w-[40%]">
                        <h1 class="text-sm text-gray-500 tracking-[0px]">{{ $t(`Enable message reception after plan
                            expiration`) }}</h1>
                        <div class="text-xs text-slate-700 flex items-center">
                            <span>{{ $t(`Toggle this setting to allow or block inbound messages when a user\'s
                                subscription plan has ended.`) }}</span>
                        </div>
                    </div>
                    <div class="sm:w-[20%]">
                        <FormToggleSwitch v-model="form.receive_messages_after_expiration" class="float-left" />
                    </div>
                </div>
                <div class="py-6">
                    <button type="submit"
                        class="float-right flex items-center space-x-4 rounded-md bg-black px-3 py-2 text-sm text-white shadow-sm hover:bg-slate-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        {{ $t('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from "./../Layout/App.vue";
import { ref } from 'vue';
import { Link, useForm } from "@inertiajs/vue3";
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import FormToggleSwitch from '@/Components/FormToggleSwitch.vue';
import { trans } from 'laravel-vue-i18n';
import { ArrowLeft } from "lucide-vue-next";

const props = defineProps(['title', 'plan', 'addons', 'enable_ai_billing']);

const getDetail = (value, key) => {
    if (value) {
        const item = JSON.parse(value);
        return item?.[key] ?? null;
    } else {
        return null;
    }
}

const getAddons = (value, key) => {
    if (value) {
        const item = JSON.parse(value);

        return item?.[key] ? item[key] : props.addons.reduce((acc, addon) => {
            acc[addon] = false;
            return acc;
        }, {});
    }

    // Return the props.addons with all values set to false
    return props.addons.reduce((acc, addon) => {
        acc[addon] = false;
        return acc;
    }, {});
};

const form = useForm({
    name: props.plan?.name,
    price: props.plan?.price,
    period: props.plan?.period,
    status: props.plan?.status,
    campaign_limit: getDetail(props.plan?.metadata, 'campaign_limit') ?? '-1',
    message_limit: getDetail(props.plan?.metadata, 'message_limit') ?? '-1',
    contacts_limit: getDetail(props.plan?.metadata, 'contacts_limit') ?? '-1',
    canned_replies_limit: getDetail(props.plan?.metadata, 'canned_replies_limit') ?? '-1',
    team_limit: getDetail(props.plan?.metadata, 'team_limit') ?? '-1',
    ai_text_response_limit: getDetail(props.plan?.metadata, 'ai_text_response_limit') ?? '-1',
    ai_audio_response_limit: getDetail(props.plan?.metadata, 'ai_audio_response_limit') ?? '-1',
    receive_messages_after_expiration: getDetail(props.plan?.metadata, 'receive_messages_after_expiration') == 1 || getDetail(props.plan?.metadata, 'receive_messages_after_expiration') == null ? true : false,
    addons: getAddons(props.plan?.metadata, 'addons')
})

const statusOptions = ref([
    { value: 'active', label: trans('active') },
    { value: 'inactive', label: trans('inactive') }
]);

const periodOptions = ref([
    { value: 'monthly', label: trans('Monthly') },
    { value: 'yearly', label: trans('Yearly') }
]);

const submitForm = async () => {
    const url = props.plan ? window.location.pathname : '/admin/plans';

    form[props.plan ? 'put' : 'post'](url, {
        preserveScroll: true,
    });
};
</script> -->


<!-- ============================================= NEW UI CODE ============================================= -->


<template>
    <AppLayout>
        <div class="min-h-screen bg-white p-4 sm:p-6 lg:p-8">
            <!-- Header Section -->
            <div class="w-full mx-auto mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <!-- Title Section -->
                    <div class="space-y-2">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#ff5100] to-[#ff7733] flex items-center justify-center shadow-lg shadow-orange-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8s8 3.59 8 8s-3.59 8-8 8zm.5-13H11v6l5.25 3.15l.75-1.23l-4.5-2.67z" />
                                </svg>
                            </div>
                            <div>
                                <h1 v-if="props.plan === null" class="text-2xl sm:text-3xl font-bold text-gray-900">
                                    {{ $t('Create Plan') }}
                                </h1>
                                <h1 v-else class="text-2xl sm:text-3xl font-bold text-gray-900">
                                    {{ $t('Update Plan') }}
                                </h1>
                                <p class="text-sm text-gray-500 mt-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        class="text-[#ff5100] mr-1">
                                        <path fill="currentColor"
                                            d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8s8 3.59 8 8s-3.59 8-8 8z" />
                                    </svg>
                                    <span v-if="props.plan === null">{{ $t('Configure your new subscription plan')
                                        }}</span>
                                    <span v-else>{{ $t('Modify your subscription plan settings') }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Back Button -->
                    <Link href="/admin/plans"
                        class="inline-flex items-center justify-center space-x-2 px-5 py-3 bg-white hover:bg-gray-50 text-gray-700 rounded-2xl font-medium transition-all duration-200 border-2 border-gray-200 shadow-sm hover:shadow-md group">
                    <ArrowLeft class="w-4 h-4 transition-transform group-hover:-translate-x-1" />
                    <span>{{ $t('Back') }}</span>
                    </Link>
                </div>
            </div>

            <!-- Main Form -->
            <div class="w-full">
                <form @submit.prevent="submitForm()" class="space-y-6">

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Basic Information Card -->
                        <div
                            class="bg-white rounded-3xl shadow-md border-2 border-orange-100 transition-all duration-300 hover:shadow-lg">
                            <div
                                class="bg-gradient-to-r from-orange-50 to-orange-100/50 px-6 py-4 border-b-2 border-orange-100">
                                <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="mr-2 text-[#ff5100]">
                                        <path fill="currentColor"
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3s-3-1.34-3-3s1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22c.03-1.99 4-3.08 6-3.08c1.99 0 5.97 1.09 6 3.08c-1.29 1.94-3.5 3.22-6 3.22z" />
                                    </svg>
                                    {{ $t('Basic Information') }}
                                </h2>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
                                    <!-- Plan Name -->
                                    <div class="space-y-2">
                                        <FormInput v-model="form.name" :name="$t('Name')" :error="form.errors.name"
                                            :type="'text'" />
                                    </div>

                                    <!-- Status -->
                                    <div class="space-y-2">
                                        <FormSelect v-model="form.status" :options="statusOptions"
                                            :error="form.errors.status" :name="$t('Status')"
                                            :placeholder="$t('Select status')" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Details Card -->
                        <div
                            class="bg-white rounded-3xl shadow-md border-2 border-orange-100 transition-all duration-300 hover:shadow-lg">
                            <div
                                class="bg-gradient-to-r from-orange-50 to-orange-100/50 px-6 py-4 border-b-2 border-orange-100">
                                <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="mr-2 text-[#ff5100]">
                                        <path fill="currentColor"
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87c1.96 0 2.4-.98 2.4-1.59c0-.83-.44-1.61-2.67-2.14c-2.48-.6-4.18-1.62-4.18-3.67c0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87c-1.5 0-2.4.68-2.4 1.64c0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z" />
                                    </svg>
                                    {{ $t('Pricing Details') }}
                                </h2>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
                                    <!-- Price -->
                                    <div class="space-y-2">
                                        <FormInput v-model="form.price" :name="$t('Price')" :error="form.errors.price"
                                            :type="'number'" />
                                    </div>

                                    <!-- Period -->
                                    <div class="space-y-2">
                                        <FormSelect v-model="form.period" :options="periodOptions"
                                            :error="form.errors.period" :name="$t('Period')"
                                            :placeholder="$t('Select period')" />
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                    <!-- Plan Limits Card -->
                    <div
                        class="bg-white rounded-3xl shadow-md border-2 border-orange-100 overflow-hidden transition-all duration-300 hover:shadow-lg">
                        <div
                            class="bg-gradient-to-r from-orange-50 to-orange-100/50 px-6 py-4 border-b-2 border-orange-100">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="mr-2 text-[#ff5100]">
                                    <path fill="currentColor"
                                        d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14c-1.66 0-3-1.34-3-3s1.34-3 3-3s3 1.34 3 3s-1.34 3-3 3z" />
                                </svg>
                                {{ $t('Plan Limits') }}
                            </h2>
                        </div>
                        <div class="p-6">
                            <!-- Info Alert -->
                            <div
                                class="bg-gradient-to-r from-orange-50 to-orange-100/50 border-2 border-orange-200 rounded-2xl p-4 mb-6 flex items-start space-x-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-[#ff5100] flex-shrink-0 mt-0.5">
                                    <path fill="currentColor"
                                        d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                </svg>
                                <p class="text-sm font-medium text-gray-700">
                                    {{ $t('For unlimited usage, set -1 as the value') }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Campaign Limit -->
                                <div class="space-y-2">

                                    <FormInput v-model="form.campaign_limit" :name="$t('Campaign limit')"
                                        :error="form.errors.campaign_limit" :type="'number'" />
                                </div>

                                <!-- Message Limit -->
                                <div class="space-y-2">

                                    <FormInput v-model="form.message_limit" :name="$t('Message limit')"
                                        :error="form.errors.message_limit" :type="'number'" />
                                </div>

                                <!-- Contacts Limit -->
                                <div class="space-y-2">

                                    <FormInput v-model="form.contacts_limit" :name="$t('Contacts limit')"
                                        :error="form.errors.contacts_limit" :type="'number'" />
                                </div>

                                <!-- Canned Replies Limit -->
                                <div class="space-y-2">

                                    <FormInput v-model="form.canned_replies_limit" :name="$t('Canned replies limit')"
                                        :error="form.errors.canned_replies_limit" :type="'number'" />
                                </div>

                                <!-- Team Limit -->
                                <div class="space-y-2">
                                    <FormInput v-model="form.team_limit" :name="$t('User limit')"
                                        :error="form.errors.team_limit" :type="'number'" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lifetime Plan Daily Limits Card (Only visible for lifetime plans) -->
                    <div v-if="isLifetimePlan"
                        class="bg-white rounded-3xl shadow-md border-2 border-green-100 overflow-hidden transition-all duration-300 hover:shadow-lg">
                        <div
                            class="bg-gradient-to-r from-green-50 to-green-100/50 px-6 py-4 border-b-2 border-green-100">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="mr-2 text-green-600">
                                    <path fill="currentColor"
                                        d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm4.2 14.2L11 13V7h1.5v5.2l4.5 2.7l-.8 1.3z" />
                                </svg>
                                {{ $t('Daily Usage Limits (Lifetime Plan)') }}
                            </h2>
                            <p class="text-sm text-gray-600 mt-1">{{ $t('Set per-day limits for lifetime subscription users. Messages will be deducted from wallet.') }}</p>
                        </div>
                        <div class="p-6">
                            <!-- Info Alert -->
                            <div
                                class="bg-gradient-to-r from-green-50 to-green-100/50 border-2 border-green-200 rounded-2xl p-4 mb-6 flex items-start space-x-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-green-600 flex-shrink-0 mt-0.5">
                                    <path fill="currentColor"
                                        d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                </svg>
                                <p class="text-sm font-medium text-gray-700">
                                    {{ $t('Lifetime plans are valid for 15 years. Users can purchase addons to increase daily limits. For unlimited usage, set -1 as the value.') }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Daily Campaign Limit -->
                                <div class="space-y-2">
                                    <FormInput v-model="form.daily_campaign_limit" :name="$t('Daily Campaign Limit')"
                                        :error="form.errors.daily_campaign_limit" :type="'number'" />
                                    <p class="text-xs text-gray-500">{{ $t('Maximum campaigns a user can create per day') }}</p>
                                </div>

                                <!-- Daily Contacts Limit -->
                                <div class="space-y-2">
                                    <FormInput v-model="form.daily_contacts_limit" :name="$t('Daily Contacts Limit')"
                                        :error="form.errors.daily_contacts_limit" :type="'number'" />
                                    <p class="text-xs text-gray-500">{{ $t('Maximum contacts a user can include in campaigns per day') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Addons Access Card -->
                    <div
                        class="bg-white rounded-3xl shadow-md border-2 border-orange-100 overflow-hidden transition-all duration-300 hover:shadow-lg">
                        <div
                            class="bg-gradient-to-r from-orange-50 to-orange-100/50 px-6 py-4 border-b-2 border-orange-100">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="mr-2 text-[#ff5100]">
                                    <path fill="currentColor"
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                </svg>
                                {{ $t('Addons Access') }}
                            </h2>
                            <p class="text-sm text-gray-600 mt-1">{{ $t('Select addons that are available in this plan')
                                }}</p>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div v-for="addon in addons" :key="addon"
                                    class="bg-gradient-to-br from-gray-50 to-white border-2 border-gray-200 rounded-2xl p-4 hover:border-orange-200 transition-all duration-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-semibold text-gray-700">{{ addon }}</span>
                                        <FormToggleSwitch v-model="form.addons[addon]" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AI Assistant Limits Card -->
                    <div v-if="props.enable_ai_billing == 1 && form.addons['AI Assistant']"
                        class="bg-white rounded-3xl shadow-md border-2 border-orange-100 overflow-hidden transition-all duration-300 hover:shadow-lg">
                        <div
                            class="bg-gradient-to-r from-purple-50 to-purple-100/50 px-6 py-4 border-b-2 border-purple-100">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="mr-2 text-purple-600">
                                    <path fill="currentColor"
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                                </svg>
                                {{ $t('AI Assistant Usage Limit') }}
                            </h2>
                        </div>
                        <div class="p-6">
                            <!-- Info Alert -->
                            <div
                                class="bg-gradient-to-r from-purple-50 to-purple-100/50 border-2 border-purple-200 rounded-2xl p-4 mb-6 flex items-start space-x-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-purple-600 flex-shrink-0 mt-0.5">
                                    <path fill="currentColor"
                                        d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                </svg>
                                <p class="text-sm font-medium text-gray-700">
                                    {{ $t('For unlimited usage, set -1 as the value') }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- AI Text Response Limit -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">
                                        {{ $t('AI Text Response Limit') }}
                                    </label>
                                    <FormInput v-model="form.ai_text_response_limit"
                                        :name="$t('AI Text Response Limit')" :error="form.errors.ai_text_response_limit"
                                        :type="'number'" />
                                </div>

                                <!-- AI Audio Response Limit -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">
                                        {{ $t('AI Audio Response Limit') }}
                                    </label>
                                    <FormInput v-model="form.ai_audio_response_limit"
                                        :name="$t('AI Audio Response Limit')"
                                        :error="form.errors.ai_audio_response_limit" :type="'number'" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message Reception Settings Card -->
                    <div
                        class="bg-white rounded-3xl shadow-md border-2 border-orange-100 overflow-hidden transition-all duration-300 hover:shadow-lg">
                        <div
                            class="bg-gradient-to-r from-orange-50 to-orange-100/50 px-6 py-4 border-b-2 border-orange-100">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="mr-2 text-[#ff5100]">
                                    <path fill="currentColor"
                                        d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5l-8-5V6l8 5l8-5v2z" />
                                </svg>
                                {{ $t('Message Reception Settings') }}
                            </h2>
                        </div>
                        <div class="p-6">
                            <div
                                class="bg-gradient-to-br from-gray-50 to-white border-2 border-gray-200 rounded-2xl p-6 hover:border-orange-200 transition-all duration-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 pr-4">
                                        <h3 class="text-base font-bold text-gray-900 mb-2">
                                            {{ $t('Enable message reception after plan expiration') }}
                                        </h3>
                                        <p class="text-sm text-gray-600 leading-relaxed">
                                            {{ $t(`Toggle this setting to allow or block inbound messages when a user\'s
                                            subscription plan has ended.`) }}
                                        </p>
                                    </div>
                                    <FormToggleSwitch v-model="form.receive_messages_after_expiration" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-4">
                        <Link href="/admin/plans"
                            class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl font-medium transition-all duration-200">
                        {{ $t('Cancel') }}
                        </Link>
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff7733] hover:from-[#ff4400] hover:to-[#ff6622] text-white font-semibold rounded-2xl transition-all duration-200 shadow-lg shadow-orange-200 hover:shadow-xl hover:shadow-orange-300 flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3s3 1.34 3 3s-1.34 3-3 3zm3-10H5V5h10v4z" />
                            </svg>
                            <span>{{ $t('Save Plan') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import { ref } from 'vue';
import { Link, useForm } from "@inertiajs/vue3";
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import FormToggleSwitch from '@/Components/FormToggleSwitch.vue';
import { trans } from 'laravel-vue-i18n';
import { ArrowLeft } from "lucide-vue-next";
import { computed } from 'vue';

const props = defineProps(['title', 'plan', 'addons', 'enable_ai_billing']);

const getDetail = (value, key) => {
    if (value) {
        const item = JSON.parse(value);
        return item?.[key] ?? null;
    } else {
        return null;
    }
}

const getAddons = (value, key) => {
    if (value) {
        const item = JSON.parse(value);

        return item?.[key] ? item[key] : props.addons.reduce((acc, addon) => {
            acc[addon] = false;
            return acc;
        }, {});
    }

    return props.addons.reduce((acc, addon) => {
        acc[addon] = false;
        return acc;
    }, {});
};

const form = useForm({
    name: props.plan?.name,
    price: props.plan?.price,
    period: props.plan?.period,
    status: props.plan?.status,
    campaign_limit: getDetail(props.plan?.metadata, 'campaign_limit') ?? '-1',
    message_limit: getDetail(props.plan?.metadata, 'message_limit') ?? '-1',
    contacts_limit: getDetail(props.plan?.metadata, 'contacts_limit') ?? '-1',
    canned_replies_limit: getDetail(props.plan?.metadata, 'canned_replies_limit') ?? '-1',
    team_limit: getDetail(props.plan?.metadata, 'team_limit') ?? '-1',
    ai_text_response_limit: getDetail(props.plan?.metadata, 'ai_text_response_limit') ?? '-1',
    ai_audio_response_limit: getDetail(props.plan?.metadata, 'ai_audio_response_limit') ?? '-1',
    receive_messages_after_expiration: getDetail(props.plan?.metadata, 'receive_messages_after_expiration') == 1 || getDetail(props.plan?.metadata, 'receive_messages_after_expiration') == null ? true : false,
    addons: getAddons(props.plan?.metadata, 'addons'),
    // Lifetime plan specific fields
    daily_campaign_limit: getDetail(props.plan?.metadata, 'daily_campaign_limit') ?? '-1',
    daily_contacts_limit: getDetail(props.plan?.metadata, 'daily_contacts_limit') ?? '-1',
})

const isLifetimePlan = computed(() => form.period === 'lifetime');

const statusOptions = ref([
    { value: 'active', label: trans('active') },
    { value: 'inactive', label: trans('inactive') }
]);

const periodOptions = ref([
    { value: 'monthly', label: trans('Monthly') },
    { value: 'yearly', label: trans('Yearly') },
    { value: 'lifetime', label: trans('Lifetime (15 Years)') }
]);

const submitForm = async () => {
    const url = props.plan ? window.location.pathname : '/admin/plans';

    form[props.plan ? 'put' : 'post'](url, {
        preserveScroll: true,
    });
};
</script>