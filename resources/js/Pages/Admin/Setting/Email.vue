<!-- <template>
    <AppLayout>
        <div>
            <h2 class="text-xl mb-1">{{ $t('Mailer settings') }}</h2>
            <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                </svg>
                <span class="ml-1 mt-1">{{ $t('Configure email accounts') }}</span>
            </p>
        </div>
        <form @submit.prevent="submitForm()">
            <div class="space-y-12">
                <div class="pb-12">
                    <div class="pb-10">
                        <div class="grid gap-6 grid-cols-2 pb-6 md:w-2/3">
                            <FormSelect v-model="form.mail_config.driver" :name="$t('Method')" :type="'text'"
                                :options="methods" :class="'col-span-2'" />
                        </div>

                        <div v-if="form.mail_config.driver === 'mailgun'" class="grid gap-6 grid-cols-2 pb-10 md:w-2/3">
                            <FormInput v-model="form.mail_config.mg_domain"
                                :error="form.errors['mail_config.mg_domain']" :name="$t('Mailgun domain')"
                                :type="'text'" :class="'col-span-1'" />
                            <FormInput v-model="form.mail_config.mg_secret"
                                :error="form.errors['mail_config.mg_secret']" :name="$t('Mailgun secret')"
                                :type="'text'" :class="'col-span-1'" />
                        </div>

                        <div v-else-if="form.mail_config.driver === 'ses'" class="grid gap-6 grid-cols-2 pb-6 md:w-2/3">
                            <FormInput v-model="form.mail_config.ses_key" :error="form.errors['mail_config.ses_key']"
                                :name="$t('AWS access key id')" :type="'text'" :class="'col-span-1'" />
                            <FormInput v-model="form.mail_config.ses_secret"
                                :error="form.errors['mail_config.ses_secret']" :name="$t('AWS secret access key')"
                                :type="'text'" :class="'col-span-1'" />
                            <FormInput v-model="form.mail_config.ses_region"
                                :error="form.errors['mail_config.ses_region']" :name="$t('AWS default region')"
                                :type="'text'" :class="'col-span-2'" />
                        </div>

                        <div v-else-if="form.mail_config.driver === 'smtp'"
                            class="grid gap-6 grid-cols-2 pb-6 md:w-2/3">
                            <FormInput v-model="form.mail_config.host" :error="form.errors['mail_config.host']"
                                :name="$t('Host')" :type="'text'" :class="'col-span-1'" />
                            <FormInput v-model="form.mail_config.port" :error="form.errors['mail_config.port']"
                                :name="$t('Port')" :type="'text'" :class="'col-span-1'" />
                            <FormInput v-model="form.mail_config.username" :error="form.errors['mail_config.username']"
                                :name="$t('Username')" :type="'text'" :class="'col-span-1'" />
                            <FormInput v-model="form.mail_config.password" :error="form.errors['mail_config.password']"
                                :name="$t('Password')" :type="'password'" :class="'col-span-1'" />
                        </div>

                        <div class="grid gap-6 grid-cols-2 pb-3 md:w-2/3">
                            <FormInput v-model="form.mail_config.from_name" :name="$t('Mail from name')"
                                :error="form.errors['mail_config.from_name']" :type="'text'" :class="'col-span-1'" />
                            <FormInput v-model="form.mail_config.from_address" :name="$t('Mail from address')"
                                :error="form.errors['mail_config.from_address']" :type="'email'"
                                :class="'col-span-1'" />
                            <FormInput v-model="form.mail_config.reply_to_name" :name="$t('Reply to name')"
                                :error="form.errors['mail_config.reply_to_name']" :type="'text'"
                                :class="'col-span-1'" />
                            <FormInput v-model="form.mail_config.reply_to_address" :name="$t('Reply to address')"
                                :error="form.errors['mail_config.reply_to_address']" :type="'email'"
                                :class="'col-span-1'" />
                        </div>

                        <div class="grid grid-cols-2 pb-6 border-b md:w-2/3">
                            <div class="relative flex gap-x-3 mt-4 col-span-2">
                                <div class="flex items-center">
                                    <label @click="toggleSmtp()" for="myCheckbox" class="cursor-pointer">
                                        <div class="w-4 h-4 border border-gray-400 rounded-md flex items-center justify-center"
                                            :class="form.smtp_email_active ? 'bg-[#000]' : ''">
                                            <svg v-if="form.smtp_email_active" class="w-4 h-4"
                                                :class="form.smtp_email_active ? 'text-white' : ''" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    </label>
                                    <span @click="toggleSmtp()" class="ml-2 text-[14px] cursor-pointer">{{ $t(`Activate
                                        email`) }}</span>
                                </div>
                            </div>
                            <div class="relative flex gap-x-3 mt-4 col-span-2">
                                <div class="flex items-center">
                                    <label @click="toggleVerification()" for="myCheckbox" class="cursor-pointer">
                                        <div class="w-4 h-4 border border-gray-400 rounded-md flex items-center justify-center"
                                            :class="{ 'bg-[#000]': form.verify_email }">
                                            <svg v-if="form.verify_email" class="w-4 h-4"
                                                :class="form.verify_email ? 'text-white' : ''" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    </label>
                                    <span @click="toggleVerification()" class="ml-2 text-[14px] cursor-pointer">{{
                                        $t('Require email verification for new accounts') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-1 flex items-center justify-end gap-x-6 md:w-2/3 pt-2">
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
            </div>
        </form>
    </AppLayout>
</template>
<script setup>
import AppLayout from "./Layout/App.vue";
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';

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

const getMailboxSettings = (key) => {
    const mailConfig = JSON.parse(getValueByKey('mail_config'));

    // Check if mailConfig is not null, is an object, and not an array
    if (mailConfig !== null && typeof mailConfig === 'object' && !Array.isArray(mailConfig)) {
        return mailConfig[key] ?? null;
    }

    return null;
}

const isLoading = ref(false);
const form = useForm({
    mail_config: {
        driver: getMailboxSettings('driver') ?? undefined,
        from_address: getMailboxSettings('from_address') ?? undefined,
        from_name: getMailboxSettings('from_name') ?? undefined,
        reply_to_name: getMailboxSettings('reply_to_name') ?? undefined,
        reply_to_address: getMailboxSettings('reply_to_address') ?? undefined,
        mg_domain: getMailboxSettings('mg_domain') ?? undefined,
        mg_secret: getMailboxSettings('mg_secret') ?? undefined,
        ses_key: getMailboxSettings('ses_key') ?? undefined,
        ses_secret: getMailboxSettings('ses_secret') ?? undefined,
        ses_region: getMailboxSettings('ses_region') ?? undefined,
        port: getMailboxSettings('port') ?? undefined,
        host: getMailboxSettings('host') ?? undefined,
        username: getMailboxSettings('username') ?? undefined,
        password: getMailboxSettings('password') ?? undefined,
        mail_config: getMailboxSettings('password') ?? undefined,
    },
    smtp_email_active: getValueByKey('smtp_email_active') === '1' ? true : false,
    verify_email: getValueByKey('verify_email') === '1' ? true : false,
})

const methods = [
    { label: 'SMTP', value: 'smtp' },
    { label: 'Mailgun', value: 'mailgun' },
    { label: 'Amazon SES', value: 'ses' },
]

const toggleSmtp = () => {
    form.smtp_email_active = !form.smtp_email_active;
};

const toggleVerification = () => {
    form.verify_email = !form.verify_email;
};

const submitForm = async () => {
    form.put('/admin/settings?type=email', {
        preserveScroll: true,
    })
};
</script> -->


<!-- ============================================ NEW UI CODE ============================================= -->

<template>
    <AppLayout>
        <div class="space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <form @submit.prevent="submitForm()" class="w-full mx-auto space-y-6">

                <!-- Mail Configuration Card -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                    <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5l-8-5V6l8 5l8-5v2z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Mailer Settings') }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Configure email accounts') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Mail Driver Selection -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('Email Provider') }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Select your email service provider') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3">
                                <FormSelect v-model="form.mail_config.driver" :name="$t('Method')" :type="'text'"
                                    :options="methods" />
                            </div>
                        </div>

                        <!-- Mailgun Configuration -->
                        <transition name="slide-fade">
                            <div v-if="form.mail_config.driver === 'mailgun'">
                                <div class="border-t border-slate-100 mb-6"></div>
                                <div class="grid md:grid-cols-5 gap-6 items-start">
                                    <div class="md:col-span-2 space-y-1">
                                        <label class="text-sm font-semibold text-slate-900">{{ $t('Mailgun Credentials')
                                            }}</label>
                                        <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                                <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                            </svg>
                                            <span>{{ $t('Your Mailgun domain and API secret') }}</span>
                                        </div>
                                    </div>
                                    <div class="md:col-span-3 grid grid-cols-1 gap-4">
                                        <FormInput v-model="form.mail_config.mg_domain"
                                            :error="form.errors['mail_config.mg_domain']" :name="$t('Mailgun domain')"
                                            :type="'text'" />
                                        <FormInput v-model="form.mail_config.mg_secret"
                                            :error="form.errors['mail_config.mg_secret']" :name="$t('Mailgun secret')"
                                            :type="'text'" />
                                    </div>
                                </div>
                            </div>
                        </transition>

                        <!-- Amazon SES Configuration -->
                        <transition name="slide-fade">
                            <div v-if="form.mail_config.driver === 'ses'">
                                <div class="border-t border-slate-100 mb-6"></div>
                                <div class="grid md:grid-cols-5 gap-6 items-start">
                                    <div class="md:col-span-2 space-y-1">
                                        <label class="text-sm font-semibold text-slate-900">{{ $t('AWS SES Credentials')
                                            }}</label>
                                        <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                                <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                            </svg>
                                            <span>{{ $t('AWS access credentials and region') }}</span>
                                        </div>
                                    </div>
                                    <div class="md:col-span-3 grid grid-cols-1 gap-4">
                                        <FormInput v-model="form.mail_config.ses_key"
                                            :error="form.errors['mail_config.ses_key']" :name="$t('AWS access key id')"
                                            :type="'text'" />
                                        <FormInput v-model="form.mail_config.ses_secret"
                                            :error="form.errors['mail_config.ses_secret']"
                                            :name="$t('AWS secret access key')" :type="'text'" />
                                        <FormInput v-model="form.mail_config.ses_region"
                                            :error="form.errors['mail_config.ses_region']"
                                            :name="$t('AWS default region')" :type="'text'" />
                                    </div>
                                </div>
                            </div>
                        </transition>

                        <!-- SMTP Configuration -->
                        <transition name="slide-fade">
                            <div v-if="form.mail_config.driver === 'smtp'">
                                <div class="border-t border-slate-100 mb-6"></div>
                                <div class="grid md:grid-cols-5 gap-6 items-start">
                                    <div class="md:col-span-2 space-y-1">
                                        <label class="text-sm font-semibold text-slate-900">{{ $t('SMTP Credentials')
                                            }}</label>
                                        <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                                <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                            </svg>
                                            <span>{{ $t('SMTP server connection details') }}</span>
                                        </div>
                                    </div>
                                    <div class="md:col-span-3 grid grid-cols-1 gap-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <FormInput v-model="form.mail_config.host"
                                                :error="form.errors['mail_config.host']" :name="$t('Host')"
                                                :type="'text'" />
                                            <FormInput v-model="form.mail_config.port"
                                                :error="form.errors['mail_config.port']" :name="$t('Port')"
                                                :type="'text'" />
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <FormInput v-model="form.mail_config.username"
                                                :error="form.errors['mail_config.username']" :name="$t('Username')"
                                                :type="'text'" />
                                            <FormInput v-model="form.mail_config.password"
                                                :error="form.errors['mail_config.password']" :name="$t('Password')"
                                                :type="'password'" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>

                <!-- Email Identity Card -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                    <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4s-4 1.79-4 4s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Email Identity') }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Configure sender and reply-to information') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- From Details -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('From Details') }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Name and email address for outgoing emails') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3 grid grid-cols-2 gap-4">
                                <FormInput v-model="form.mail_config.from_name" :name="$t('Mail from name')"
                                    :error="form.errors['mail_config.from_name']" :type="'text'" />
                                <FormInput v-model="form.mail_config.from_address" :name="$t('Mail from address')"
                                    :error="form.errors['mail_config.from_address']" :type="'email'" />
                            </div>
                        </div>

                        <div class="border-t border-slate-100"></div>

                        <!-- Reply To Details -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('Reply To Details') }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Name and email for reply-to address') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3 grid grid-cols-2 gap-4">
                                <FormInput v-model="form.mail_config.reply_to_name" :name="$t('Reply to name')"
                                    :error="form.errors['mail_config.reply_to_name']" :type="'text'" />
                                <FormInput v-model="form.mail_config.reply_to_address" :name="$t('Reply to address')"
                                    :error="form.errors['mail_config.reply_to_address']" :type="'email'" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Settings Card -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                    <div class="p-6 space-y-4">
                        <!-- Activate Email Toggle -->
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
                                    <h3 class="text-base font-bold text-slate-900">{{ $t('Activate Email') }}</h3>
                                    <div class="flex items-start gap-1.5 text-xs text-slate-600 mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                            <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                        </svg>
                                        <span>{{ $t('Enable or disable email functionality') }}</span>
                                    </div>
                                </div>
                            </div>
                            <FormToggleSwitch v-model="form.smtp_email_active" />
                        </div>

                        <!-- Email Verification Toggle -->
                        <div
                            class="flex items-center justify-between p-4 rounded-xl bg-slate-50/50 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="p-2 bg-gradient-to-br from-[#ff5100]/10 to-[#ff7340]/10 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="currentColor"
                                            d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12c5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-bold text-slate-900">{{ $t('Email Verification') }}</h3>
                                    <div class="flex items-start gap-1.5 text-xs text-slate-600 mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                            <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                        </svg>
                                        <span>{{ $t('Require email verification for new accounts') }}</span>
                                    </div>
                                </div>
                            </div>
                            <FormToggleSwitch v-model="form.verify_email" />
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
import FormSelect from '@/Components/FormSelect.vue';
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

const getMailboxSettings = (key) => {
    const mailConfig = JSON.parse(getValueByKey('mail_config'));

    // Check if mailConfig is not null, is an object, and not an array
    if (mailConfig !== null && typeof mailConfig === 'object' && !Array.isArray(mailConfig)) {
        return mailConfig[key] ?? null;
    }

    return null;
}

const isLoading = ref(false);
const form = useForm({
    mail_config: {
        driver: getMailboxSettings('driver') ?? undefined,
        from_address: getMailboxSettings('from_address') ?? undefined,
        from_name: getMailboxSettings('from_name') ?? undefined,
        reply_to_name: getMailboxSettings('reply_to_name') ?? undefined,
        reply_to_address: getMailboxSettings('reply_to_address') ?? undefined,
        mg_domain: getMailboxSettings('mg_domain') ?? undefined,
        mg_secret: getMailboxSettings('mg_secret') ?? undefined,
        ses_key: getMailboxSettings('ses_key') ?? undefined,
        ses_secret: getMailboxSettings('ses_secret') ?? undefined,
        ses_region: getMailboxSettings('ses_region') ?? undefined,
        port: getMailboxSettings('port') ?? undefined,
        host: getMailboxSettings('host') ?? undefined,
        username: getMailboxSettings('username') ?? undefined,
        password: getMailboxSettings('password') ?? undefined,
        mail_config: getMailboxSettings('password') ?? undefined,
    },
    smtp_email_active: getValueByKey('smtp_email_active') === '1' ? true : false,
    verify_email: getValueByKey('verify_email') === '1' ? true : false,
})

const methods = [
    { label: 'SMTP', value: 'smtp' },
    { label: 'Mailgun', value: 'mailgun' },
    { label: 'Amazon SES', value: 'ses' },
]

const submitForm = async () => {
    form.put('/admin/settings?type=email', {
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