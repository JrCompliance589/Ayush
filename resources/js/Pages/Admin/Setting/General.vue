<!-- <template>
    <AppLayout>
        <div>
            <h2 class="text-xl mb-1">{{ $t('General settings') }}</h2>
            <p class="mb-6 flex items-center text-sm leading-6">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                </svg>
                <span class="ml-1 mt-1">{{ $t('Configure general account information') }}</span>
            </p>
        </div>
        <form @submit.prevent="submitForm()" enctype="multipart/form-data">
            <div class="space-y-12">
                <div class="pb-12">
                    <div class="grid gap-6 grid-cols-2 pb-10 border-b md:w-2/3">
                        <FormInput v-model="form.company_name" :name="$t('Organization/company name')"
                            :error="form.errors.company_name" :type="'text'" :class="'col-span-2'" />
                        <FormInput v-model="form.phone" :name="$t('Phone')" :error="form.errors.phone" :type="'text'"
                            :class="'col-span-1'" />
                        <FormInput v-model="form.email" :name="$t('Email')" :error="form.errors.email" :type="'text'"
                            :class="'col-span-1'" />
                        <FormTextArea v-model="form.address" :name="$t('Address')" :showLabel="true"
                            :error="form.errors.address" :class="'col-span-2'" />
                    </div>

                    <div class="pt-5">
                        <h2 class="text-base text-gray-900">{{ $t('Social media accounts') }}</h2>
                        <p class="text-sm leading-6 mb-5">{{ $t('Add your social media account links') }}</p>

                        <div class="grid gap-6 grid-cols-2 pb-10 border-b md:w-2/3">
                            <FormInput v-model="form.socials.facebook" :name="'Facebook'" :type="'text'"
                                :class="'col-span-1'" />
                            <FormInput v-model="form.socials.twitter" :name="'Twitter'" :type="'text'"
                                :class="'col-span-1'" />
                            <FormInput v-model="form.socials.instagram" :name="'Instagram'" :type="'text'"
                                :class="'col-span-1'" />
                            <FormInput v-model="form.socials.slack" :name="'Slack'" :type="'text'"
                                :class="'col-span-1'" />
                            <FormInput v-model="form.socials.linkedin" :name="'LinkedIn'" :type="'text'"
                                :class="'col-span-1'" />
                        </div>
                    </div>

                    <div class="pt-5">
                        <h2 class="text-base text-gray-900">{{ $t('Frontend Settings') }}</h2>
                        <p class="text-sm leading-6 mb-5">{{ $t('Show/hide the main frontend page') }}</p>

                        <div class="flex gap-x-10 md:w-2/3 pb-10 border-b">
                            <div class="w-[80%]">
                                <span>{{ $t('Use the current default frontend') }}</span>
                                <div class="text-xs text-slate-700 flex items-center">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                        viewBox="0 0 24 24">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Toggle on/off to show/hide the main frontend page') }}</span>
                                </div>
                            </div>
                            <div class="w-[20%]">
                                <FormToggleSwitch v-model="form.display_frontend" class="float-right" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-5">
                        <h2 class="text-base text-gray-900">{{ $t('Site logo and favicon') }}</h2>
                        <p class="text-sm leading-6 mb-5">{{ $t('Add your logo and favicon') }}</p>

                        <div class="grid gap-6 grid-cols-2 pb-10 border-b md:w-2/3">
                            <FormImageLogo v-model="form.logo" :name="$t('Site logo')" :error="form.errors.logo"
                                :label="$t('Upload logo')" :imageUrl="getImageUrl('logo')" :class="'col-span-2'" />
                            <FormImageFavicon v-model="form.favicon" :name="$t('Favicon')" :error="form.errors.favicon"
                                :label="$t('Upload logo')" :imageUrl="getImageUrl('favicon')" :class="'col-span-2'" />
                        </div>
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
import { defineProps, ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import FormImageFavicon from '@/Components/FormImageFavicon.vue';
import FormImageLogo from '@/Components/FormImageLogo.vue';
import FormInput from '@/Components/FormInput.vue';
import FormTextArea from '@/Components/FormTextArea.vue';
import FormToggleSwitch from '@/Components/FormToggleSwitch.vue';

const props = defineProps({
    config: {
        type: Array,
        required: true
    }
});

const getValueByKey = (key) => {
    const found = props.config.find(item => item.key === key);
    return found ? found.value : '';
};

const getImageUrl = (value) => {
    const file = getValueByKey(value);
    return file != '' && file != null ? '/media/' + file : null;
}


const socials = JSON.parse(getValueByKey('socials'));
const isLoading = ref(false);

const form = useForm({
    _method: 'put',
    company_name: getValueByKey('company_name'),
    phone: getValueByKey('phone'),
    email: getValueByKey('email'),
    address: getValueByKey('address'),
    recaptcha_active: getValueByKey('recaptcha_active') === '1',
    recaptcha_site_key: getValueByKey('recaptcha_site_key'),
    recaptcha_secret_key: getValueByKey('recaptcha_secret_key'),
    google_analytics_tracking_id: getValueByKey('google_analytics_tracking_id'),
    google_maps_api_key: getValueByKey('google_maps_api_key'),
    app_environment: getValueByKey('app_environment'),
    display_frontend: getValueByKey('display_frontend') == 1 || getValueByKey('display_frontend') == '' ? true : false,
    logo: null,
    favicon: null,
    socials: {
        facebook: socials?.facebook,
        twitter: socials?.twitter,
        instagram: socials?.instagram,
        slack: socials?.slack,
        linkedin: socials?.linkedin
    }
})

const submitForm = async () => {
    form.post('/admin/settings?type=general', {
        preserveScroll: true,
    })
};
</script> -->


<!-- ============================================ NEW UI CODE ============================================= -->

<template>
    <AppLayout>
        <div class="space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <form @submit.prevent="submitForm()" enctype="multipart/form-data" class="w-full mx-auto space-y-6">

                <!-- Company Information Card -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                    <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Company Information') }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Configure general account information') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Company Name & Phone -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('Basic Details') }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Company name and contact information') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3 grid grid-cols-1 gap-4">
                                <FormInput v-model="form.company_name" :name="$t('Organization/company name')"
                                    :error="form.errors.company_name" :type="'text'" />
                                <div class="grid grid-cols-2 gap-4">
                                    <FormInput v-model="form.phone" :name="$t('Phone')" :error="form.errors.phone"
                                        :type="'text'" />
                                    <FormInput v-model="form.email" :name="$t('Email')" :error="form.errors.email"
                                        :type="'text'" />
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-slate-100"></div>

                        <!-- Address -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('Address') }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Your company physical address') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3">
                                <FormTextArea v-model="form.address" :name="$t('Address')" :showLabel="true"
                                    :error="form.errors.address" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Media Card -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                    <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81c1.66 0 3-1.34 3-3s-1.34-3-3-3s-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65c0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Social Media Accounts') }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Add your social media account links') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid md:grid-cols-2 gap-4">
                            <FormInput v-model="form.socials.facebook" :name="'Facebook'" :type="'text'" />
                            <FormInput v-model="form.socials.twitter" :name="'Twitter'" :type="'text'" />
                            <FormInput v-model="form.socials.instagram" :name="'Instagram'" :type="'text'" />
                            <FormInput v-model="form.socials.slack" :name="'Slack'" :type="'text'" />
                            <FormInput v-model="form.socials.linkedin" :name="'LinkedIn'" :type="'text'" />
                        </div>
                    </div>
                </div>

                <!-- Frontend Settings Card -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                    <div class="p-6">
                        <div
                            class="flex items-center justify-between p-4 rounded-xl bg-slate-50/50 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="p-2 bg-gradient-to-br from-[#ff5100]/10 to-[#ff7340]/10 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="currentColor"
                                            d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9s9-4.03 9-9c0-.46-.04-.92-.1-1.36c-.98 1.37-2.58 2.26-4.4 2.26c-2.98 0-5.4-2.42-5.4-5.4c0-1.81.89-3.42 2.26-4.4C12.92 3.04 12.46 3 12 3z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-bold text-slate-900">{{ $t('Frontend Display') }}</h3>
                                    <div class="flex items-start gap-1.5 text-xs text-slate-600 mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                            <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                        </svg>
                                        <span>{{ $t('Toggle on/off to show/hide the main frontend page') }}</span>
                                    </div>
                                </div>
                            </div>
                            <FormToggleSwitch v-model="form.display_frontend" />
                        </div>
                    </div>
                </div>

                <!-- Logo & Favicon Card -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                    <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Site Logo and Favicon') }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Add your logo and favicon') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Logo Upload -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('Site Logo') }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Your company logo for branding') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3">
                                <FormImageLogo v-model="form.logo" :name="$t('Site logo')" :error="form.errors.logo"
                                    :label="$t('Upload logo')" :imageUrl="getImageUrl('logo')" />
                            </div>
                        </div>

                        <div class="border-t border-slate-100"></div>

                        <!-- Favicon Upload -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('Favicon') }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Browser tab icon (16x16 or 32x32 px)') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3">
                                <FormImageFavicon v-model="form.favicon" :name="$t('Favicon')"
                                    :error="form.errors.favicon" :label="$t('Upload logo')"
                                    :imageUrl="getImageUrl('favicon')" />
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
import { defineProps, ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import FormImageFavicon from '@/Components/FormImageFavicon.vue';
import FormImageLogo from '@/Components/FormImageLogo.vue';
import FormInput from '@/Components/FormInput.vue';
import FormTextArea from '@/Components/FormTextArea.vue';
import FormToggleSwitch from '@/Components/FormToggleSwitch.vue';

const props = defineProps({
    config: {
        type: Array,
        required: true
    }
});

const getValueByKey = (key) => {
    const found = props.config.find(item => item.key === key);
    return found ? found.value : '';
};

const getImageUrl = (value) => {
    const file = getValueByKey(value);
    return file != '' && file != null ? '/media/' + file : null;
}

const socials = JSON.parse(getValueByKey('socials'));
const isLoading = ref(false);

const form = useForm({
    _method: 'put',
    company_name: getValueByKey('company_name'),
    phone: getValueByKey('phone'),
    email: getValueByKey('email'),
    address: getValueByKey('address'),
    recaptcha_active: getValueByKey('recaptcha_active') === '1',
    recaptcha_site_key: getValueByKey('recaptcha_site_key'),
    recaptcha_secret_key: getValueByKey('recaptcha_secret_key'),
    google_analytics_tracking_id: getValueByKey('google_analytics_tracking_id'),
    google_maps_api_key: getValueByKey('google_maps_api_key'),
    app_environment: getValueByKey('app_environment'),
    display_frontend: getValueByKey('display_frontend') == 1 || getValueByKey('display_frontend') == '' ? true : false,
    logo: null,
    favicon: null,
    socials: {
        facebook: socials?.facebook,
        twitter: socials?.twitter,
        instagram: socials?.instagram,
        slack: socials?.slack,
        linkedin: socials?.linkedin
    }
})

const submitForm = async () => {
    form.post('/admin/settings?type=general', {
        preserveScroll: true,
    })
};
</script>

<style scoped>
.slide-fade-enter-active {
    transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
    transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    transform: translateY(-10px);
    opacity: 0;
}
</style>
