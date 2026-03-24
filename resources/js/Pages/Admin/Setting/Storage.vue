<!-- <template>
    <AppLayout>
        <div>
            <h2 class="text-xl mb-1">{{ $t('Storage settings') }}</h2>
            <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                </svg>
                <span class="ml-1 mt-1">{{ $t('Configure your storage location') }}</span>
            </p>
        </div>
        <form @submit.prevent="submitForm()">
            <div class="space-y-12">
                <div class="pb-12">
                    <div class="grid gap-6 grid-cols-2 pb-10 md:w-2/3"
                        :class="form.storage_system === 'aws' ? '' : 'border-b'">
                        <FormSelect v-model="form.storage_system" :name="$t('File system for storage')" :type="'text'"
                            :options="methods" :error="form.errors.storage_system" :class="'col-span-2'" />
                    </div>

                    <div v-if="form.storage_system === 'aws'" class="grid gap-6 grid-cols-2 pb-10 border-b md:w-2/3">
                        <FormInput v-model="form.aws.access_key" :name="$t('S3 AWS access key')" :type="'text'"
                            :error="form.errors['aws.access_key']" :class="'col-span-1'" />
                        <FormInput v-model="form.aws.secret_key" :name="$t('S3 AWS secret access key')"
                            :type="'password'" :error="form.errors['aws.secret_key']" :class="'col-span-1'" />
                        <FormInput v-model="form.aws.default_region" :name="$t('S3 AWS default region')" :type="'text'"
                            :error="form.errors['aws.default_region']" :class="'col-span-1'" />
                        <FormInput v-model="form.aws.bucket" :name="$t('S3 AWS bucket')" :type="'text'"
                            :error="form.errors['aws.bucket']" :class="'col-span-1'" />
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

const getAWSSettings = (key) => {
    if (getValueByKey('aws')) {
        const AWSConfig = JSON.parse(getValueByKey('aws'));

        // Check if mailConfig is not null, is an object, and not an array
        if (AWSConfig !== null && typeof AWSConfig === 'object' && !Array.isArray(AWSConfig)) {
            return AWSConfig[key] ?? null;
        }
    }

    return null;
}

const isLoading = ref(false);
const form = useForm({
    storage_system: getValueByKey('storage_system'),
    aws: {
        access_key: getAWSSettings('access_key') ?? undefined,
        secret_key: getAWSSettings('secret_key') ?? undefined,
        default_region: getAWSSettings('default_region') ?? undefined,
        bucket: getAWSSettings('bucket') ?? undefined,
    }
})

const methods = [
    { label: 'Local File Storage', value: 'local' },
    { label: 'AWS S3 Bucket', value: 'aws' },
]

const submitForm = async () => {
    form.put('/admin/settings?type=storage', {
        preserveScroll: true,
    });
};
</script> -->


<!-- ============================================ NEW UI CODE ============================================= -->

<template>
    <AppLayout>
        <div class="space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <form @submit.prevent="submitForm()" class="w-full mx-auto space-y-6">

                <!-- Storage System Card -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                    <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M2 20h20v-4H2v4zm2-3h2v2H4v-2zM2 4v4h20V4H2zm4 3H4V5h2v2zm-4 7h20v-4H2v4zm2-3h2v2H4v-2z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Storage Configuration') }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Configure your storage location') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Storage System Selection -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('Storage System') }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Choose between local or cloud storage') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3">
                                <FormSelect v-model="form.storage_system" :name="$t('File system for storage')"
                                    :type="'text'" :options="methods" :error="form.errors.storage_system" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AWS S3 Configuration Card (Conditional) -->
                <transition name="slide-fade">
                    <div v-if="form.storage_system === 'aws'"
                        class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                        <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                            <div class="flex items-center gap-3">
                                <div
                                    class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-white">
                                        <path fill="currentColor"
                                            d="M6.76 7.59v4.99l4.26 2.46l4.26-2.46V7.59L11.02 5.13L6.76 7.59m9.5 5.19l-2.37 1.37l-.02 2.71l2.39-1.39v-2.69m-1.62 5.82l-2.71-1.57l-.03.02l-2.7 1.57l2.73 1.57l2.71-1.59M4.63 7.38l2.72-1.57l2.7 1.57l-2.71 1.56l-2.71-1.56m6.63 10.19l-.02-2.71l-2.37-1.37v2.69l2.39 1.39m-3.77-2.19L5.13 13.9V8.71l2.36 1.36v5.31m11.13 1.25l-2.72 1.57l-.03-5.35l2.75-1.58v5.36m.68-10.25L11.02 2L2.71 6.63v9.25l8.31 4.8l8.28-4.8V6.63Z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-slate-900">{{ $t('AWS S3 Configuration') }}</h2>
                                    <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" class="text-[#ff5100]">
                                            <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                        </svg>
                                        <span>{{ $t('Configure Amazon S3 bucket credentials') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 space-y-6">
                            <!-- AWS Credentials -->
                            <div class="grid md:grid-cols-5 gap-6 items-start">
                                <div class="md:col-span-2 space-y-1">
                                    <label class="text-sm font-semibold text-slate-900">{{ $t('Access Credentials')
                                        }}</label>
                                    <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                            <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                        </svg>
                                        <span>{{ $t('AWS IAM access key and secret key') }}</span>
                                    </div>
                                </div>
                                <div class="md:col-span-3 grid grid-cols-1 gap-4">
                                    <FormInput v-model="form.aws.access_key" :name="$t('S3 AWS access key')"
                                        :type="'text'" :error="form.errors['aws.access_key']" />
                                    <FormInput v-model="form.aws.secret_key" :name="$t('S3 AWS secret access key')"
                                        :type="'password'" :error="form.errors['aws.secret_key']" />
                                </div>
                            </div>

                            <div class="border-t border-slate-100"></div>

                            <!-- AWS Region & Bucket -->
                            <div class="grid md:grid-cols-5 gap-6 items-start">
                                <div class="md:col-span-2 space-y-1">
                                    <label class="text-sm font-semibold text-slate-900">{{ $t('Bucket Details')
                                        }}</label>
                                    <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                            <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                        </svg>
                                        <span>{{ $t('AWS region and bucket name configuration') }}</span>
                                    </div>
                                </div>
                                <div class="md:col-span-3 grid grid-cols-1 gap-4">
                                    <FormInput v-model="form.aws.default_region" :name="$t('S3 AWS default region')"
                                        :type="'text'" :error="form.errors['aws.default_region']" />
                                    <FormInput v-model="form.aws.bucket" :name="$t('S3 AWS bucket')" :type="'text'"
                                        :error="form.errors['aws.bucket']" />
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>

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

const getAWSSettings = (key) => {
    if (getValueByKey('aws')) {
        const AWSConfig = JSON.parse(getValueByKey('aws'));

        // Check if mailConfig is not null, is an object, and not an array
        if (AWSConfig !== null && typeof AWSConfig === 'object' && !Array.isArray(AWSConfig)) {
            return AWSConfig[key] ?? null;
        }
    }

    return null;
}

const isLoading = ref(false);
const form = useForm({
    storage_system: getValueByKey('storage_system'),
    aws: {
        access_key: getAWSSettings('access_key') ?? undefined,
        secret_key: getAWSSettings('secret_key') ?? undefined,
        default_region: getAWSSettings('default_region') ?? undefined,
        bucket: getAWSSettings('bucket') ?? undefined,
    }
})

const methods = [
    { label: 'Local File Storage', value: 'local' },
    { label: 'AWS S3 Bucket', value: 'aws' },
]

const submitForm = async () => {
    form.put('/admin/settings?type=storage', {
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