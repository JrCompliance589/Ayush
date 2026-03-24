<!-- <template>
    <AppLayout>
        <div>
            <h2 class="text-xl mb-1">{{ $t('Social login settings') }}</h2>
            <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                </svg>
                <span class="ml-1 mt-1">{{ $t('Configure social accounts for user login') }}</span>
            </p>
        </div>
        <form class="" @submit.prevent="submitForm()">
            <div class="space-y-6">
                <div class="">
                    <div class="grid gap-6 grid-cols-2 pb-10 border-b md:w-2/3">
                        <FormInput v-model="form.google_login.client_id" :name="$t('Google client id')"
                            :error="form.errors['google_login.client_id']" :type="'text'" :class="'col-span-1'" />
                        <FormInput v-model="form.google_login.client_secret" :name="$t('Google client secret')"
                            :error="form.errors['google_login.client_secret']" :type="'password'"
                            :class="'col-span-1'" />
                        <FormInput v-model="form.facebook_login.client_id" :name="$t('Facebook client id')"
                            :error="form.errors['facebook_login.client_id']" :type="'text'" :class="'col-span-1'" />
                        <FormInput v-model="form.facebook_login.client_secret" :name="$t('Facebook client secret')"
                            :error="form.errors['facebook_login.client_secret']" :type="'password'"
                            :class="'col-span-1'" />
                        <div class="relative flex gap-x-3 col-span-2">
                            <div class="flex items-center">
                                <label @click="toggleGoogleLoginActive()" for="myCheckbox" class="cursor-pointer">
                                    <div class="w-4 h-4 border border-gray-400 rounded-md flex items-center justify-center"
                                        :class="form.allow_google_login ? 'bg-[#000]' : ''">
                                        <svg v-if="form.allow_google_login" class="w-4 h-4"
                                            :class="form.allow_google_login ? 'text-white' : ''" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </label>
                                <span @click="toggleGoogleLoginActive()" class="ml-2 text-[14px] cursor-pointer">{{
                                    $t('Allow google login') }}</span>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3 col-span-2">
                            <div class="flex items-center">
                                <label @click="toggleFacebookLoginActive()" for="myCheckbox" class="cursor-pointer">
                                    <div class="w-4 h-4 border border-gray-400 rounded-md flex items-center justify-center"
                                        :class="form.allow_facebook_login ? 'bg-[#000]' : ''">
                                        <svg v-if="form.allow_facebook_login" class="w-4 h-4"
                                            :class="form.allow_facebook_login ? 'text-white' : ''" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </label>
                                <span @click="toggleFacebookLoginActive()" class="ml-2 text-[14px] cursor-pointer">{{
                                    $t('Allow facebook login') }}</span>
                            </div>
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
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import FormInput from '@/Components/FormInput.vue';

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

const getSocialSettings = (key, value) => {
    const config = JSON.parse(getValueByKey(key));

    if (config !== null && typeof config === 'object' && !Array.isArray(config)) {
        return config[value] ?? null;
    }

    return null;
}

const isLoading = ref(false);
const form = useForm({
    allow_facebook_login: getValueByKey('allow_facebook_login') === '1' ? true : false,
    allow_google_login: getValueByKey('allow_google_login') === '1' ? true : false,
    facebook_login: {
        client_id: getSocialSettings('facebook_login', 'client_id') ?? null,
        client_secret: getSocialSettings('facebook_login', 'client_secret') ?? null,
    },
    google_login: {
        client_id: getSocialSettings('google_login', 'client_id') ?? null,
        client_secret: getSocialSettings('google_login', 'client_secret') ?? null,
    },
})

const toggleFacebookLoginActive = () => {
    form.allow_facebook_login = !form.allow_facebook_login;
};

const toggleGoogleLoginActive = () => {
    form.allow_google_login = !form.allow_google_login;
};

const submitForm = async () => {
    form.put('/admin/settings?type=socials', {
        preserveScroll: true,
    });
};
</script> -->


<!-- ============================================ NEW UI CODE ============================================= -->


<template>
    <AppLayout>
        <div class="space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <form @submit.prevent="submitForm()" class="w-full mx-auto space-y-6">

                <!-- Google Login Card -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                    <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133c-1.147 1.147-2.933 2.4-6.053 2.4c-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0C5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36c2.16-2.16 2.84-5.213 2.84-7.667c0-.76-.053-1.467-.173-2.053H12.48z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Google Login Settings') }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Configure Google OAuth credentials') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Google Credentials -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('OAuth Credentials')
                                }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Client ID and secret from Google Cloud Console') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3 grid grid-cols-1 gap-4">
                                <FormInput v-model="form.google_login.client_id" :name="$t('Google client id')"
                                    :error="form.errors['google_login.client_id']" :type="'text'" />
                                <FormInput v-model="form.google_login.client_secret" :name="$t('Google client secret')"
                                    :error="form.errors['google_login.client_secret']" :type="'password'" />
                            </div>
                        </div>

                        <div class="border-t border-slate-100"></div>

                        <!-- Google Login Toggle -->
                        <div
                            class="flex items-center justify-between p-4 rounded-xl bg-slate-50/50 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="p-2 bg-gradient-to-br from-[#ff5100]/10 to-[#ff7340]/10 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="currentColor"
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-bold text-slate-900">{{ $t('Enable Google Login') }}</h3>
                                    <div class="flex items-start gap-1.5 text-xs text-slate-600 mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                            <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                        </svg>
                                        <span>{{ $t('Allow users to sign in with their Google account') }}</span>
                                    </div>
                                </div>
                            </div>
                            <FormToggleSwitch v-model="form.allow_google_login" />
                        </div>
                    </div>
                </div>

                <!-- Facebook Login Card -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                    <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Facebook Login Settings') }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Configure Facebook OAuth credentials') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Facebook Credentials -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('OAuth Credentials')
                                }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('App ID and secret from Facebook Developers') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3 grid grid-cols-1 gap-4">
                                <FormInput v-model="form.facebook_login.client_id" :name="$t('Facebook client id')"
                                    :error="form.errors['facebook_login.client_id']" :type="'text'" />
                                <FormInput v-model="form.facebook_login.client_secret"
                                    :name="$t('Facebook client secret')"
                                    :error="form.errors['facebook_login.client_secret']" :type="'password'" />
                            </div>
                        </div>

                        <div class="border-t border-slate-100"></div>

                        <!-- Facebook Login Toggle -->
                        <div
                            class="flex items-center justify-between p-4 rounded-xl bg-slate-50/50 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="p-2 bg-gradient-to-br from-[#ff5100]/10 to-[#ff7340]/10 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="currentColor"
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-bold text-slate-900">{{ $t('Enable Facebook Login') }}
                                    </h3>
                                    <div class="flex items-start gap-1.5 text-xs text-slate-600 mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                            <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                        </svg>
                                        <span>{{ $t('Allow users to sign in with their Facebook account') }}</span>
                                    </div>
                                </div>
                            </div>
                            <FormToggleSwitch v-model="form.allow_facebook_login" />
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
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
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

const getSocialSettings = (key, value) => {
    const config = JSON.parse(getValueByKey(key));

    if (config !== null && typeof config === 'object' && !Array.isArray(config)) {
        return config[value] ?? null;
    }

    return null;
}

const isLoading = ref(false);
const form = useForm({
    allow_facebook_login: getValueByKey('allow_facebook_login') === '1' ? true : false,
    allow_google_login: getValueByKey('allow_google_login') === '1' ? true : false,
    facebook_login: {
        client_id: getSocialSettings('facebook_login', 'client_id') ?? null,
        client_secret: getSocialSettings('facebook_login', 'client_secret') ?? null,
    },
    google_login: {
        client_id: getSocialSettings('google_login', 'client_id') ?? null,
        client_secret: getSocialSettings('google_login', 'client_secret') ?? null,
    },
})

const submitForm = async () => {
    form.put('/admin/settings?type=socials', {
        preserveScroll: true,
    });
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