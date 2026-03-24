<!-- <template>
    <AppLayout>
        <div class="p-8 rounded-[5px] text-[#000] overflow-y-scroll">
            <div class="flex justify-between">
                <div>
                    <h1 v-if="props.faq === null" class="text-xl mb-1">{{ $t('Create FAQ') }}</h1>
                    <h1 v-else class="text-xl mb-1">{{ $t('Update FAQ') }}</h1>
                    <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                        </svg>
                        <span v-if="props.faq === null" class="ml-1 mt-1">{{ $t('Create FAQ') }}</span>
                        <span v-else class="ml-1 mt-1">{{ $t('Update FAQ') }}</span>
                    </p>
                </div>
                <div>
                    <Link href="/admin/faqs"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-300 flex items-center space-x-2">
                    <ArrowLeft class="w-4 h-4" />
                    <span>{{ $t('Back') }}</span></Link>
                </div>
            </div>
            <form @submit.prevent="submitForm()" class="bg-white border py-5 px-5 rounded-[0.5rem]">
                <div class="sm:flex border-b py-5">
                    <div class="hidden sm:block sm:w-[40%] mb-1">
                        <h1 class="text-sm text-gray-500 tracking-[0px]">{{ $t('Question') }}</h1>
                    </div>
                    <div class="sm:w-[60%] sm:flex space-x-6">
                        <div class="sm:w-[80%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormTextArea v-model="form.question" :name="''" :error="form.errors.question"
                                :type="'text'" :textAreaRows="4" :class="'sm:col-span-6'" />
                        </div>
                    </div>
                </div>
                <div class="sm:flex border-b py-5">
                    <div class="hidden sm:block sm:w-[40%] mb-1">
                        <h1 class="text-sm text-gray-500 tracking-[0px]">{{ $t('Answer') }}</h1>
                    </div>
                    <div class="sm:w-[60%] sm:flex space-x-6">
                        <div class="sm:w-[80%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormTextArea v-model="form.answer" :name="''" :error="form.errors.answer" :type="'text'"
                                :textAreaRows="4" :class="'sm:col-span-6'" />
                        </div>
                    </div>
                </div>
                <div class="sm:flex border-b py-5">
                    <div class="hidden sm:block w-[40%] mb-1">
                        <h1 class="text-sm text-gray-500 tracking-[0px]">{{ $t('Status') }}</h1>
                    </div>
                    <div class="sm:w-[60%] sm:flex space-x-6">
                        <div class="sm:w-[80%] grid gap-x-6 gap-y-4 sm:grid-cols-1">
                            <FormSelect v-model="form.status" :options="statusOptions" :error="form.errors.status"
                                :name="''" :class="'sm:col-span-3'" :placeholder="$t('Select status')" />
                        </div>
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
import FormTextArea from '@/Components/FormTextArea.vue';
import FormSelect from '@/Components/FormSelect.vue';
import { ArrowLeft } from "lucide-vue-next";

const props = defineProps({ title: String, faq: Object });

const getDetail = (value, key) => {
    if (value) {
        const item = JSON.parse(value);
        return item?.[key] ?? null;
    } else {
        return null;
    }
}

const form = useForm({
    question: props.faq?.question,
    answer: props.faq?.answer,
    status: props.faq?.status,
})

const statusOptions = ref([
    { value: 0, label: 'Hide' },
    { value: 1, label: 'Display' }
]);

const submitForm = async () => {
    const url = props.faq ? window.location.pathname : '/admin/faqs';

    form[props.faq ? 'put' : 'post'](url, {
        preserveScroll: true,
    });
};
</script> -->


<!-- ========================================= NEW UI CODE ==================================== -->

<template>
    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50/30 p-4 md:p-8">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="bg-white rounded-3xl shadow-md border-2 border-primary/10 p-6 md:p-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-14 h-14 bg-gradient-to-br from-[#ff5100] to-[#ff7733] rounded-2xl flex items-center justify-center shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41c0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">
                                    {{ props.faq === null ? $t('Create FAQ') : $t('Update FAQ') }}
                                </h1>
                                <p class="text-sm text-gray-500 mt-1 flex items-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="currentColor"
                                            d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8s8 3.59 8 8s-3.59 8-8 8z" />
                                    </svg>
                                    <span>
                                        {{ props.faq === null ? $t(`Add a new frequently asked question`) : $t(`Modify
                                        existing FAQ`) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <Link href="/admin/faqs"
                            class="px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 rounded-xl font-medium transition-all duration-200 border-2 border-gray-200 hover:border-gray-300 flex items-center space-x-2 shadow-sm w-fit">
                        <ArrowLeft class="w-4 h-4" />
                        <span>{{ $t('Back') }}</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Form Container -->
            <form @submit.prevent="submitForm()" class="bg-white rounded-3xl shadow-md border-2 border-primary/10">
                <!-- Question Section -->
                <div class="p-6 md:p-8 border-b-2 border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:space-x-12">
                        <div class="lg:w-2/5 mb-6 lg:mb-0">
                            <div class="flex items-start space-x-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-[#ff5100] to-[#ff7733] rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-white">
                                        <path fill="currentColor"
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41c0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $t('Question') }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $t('Enter the frequently asked question')
                                        }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="lg:w-3/5">
                            <div class="relative">
                                <FormTextArea v-model="form.question" :name="''" :error="form.errors.question"
                                    :type="'text'" :textAreaRows="4" :class="'w-full'"
                                    :placeholder="$t('What is your question?')" />
                                <div v-if="form.question"
                                    class="absolute bottom-3 right-3 text-xs text-gray-400 bg-white px-2 py-1 rounded-lg">
                                    {{ form.question.length }} {{ $t('characters') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Answer Section -->
                <div class="p-6 md:p-8 border-b-2 border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:space-x-12">
                        <div class="lg:w-2/5 mb-6 lg:mb-0">
                            <div class="flex items-start space-x-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-[#ff5100] to-[#ff7733] rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-white">
                                        <path fill="currentColor"
                                            d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $t('Answer') }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $t('Provide a clear and helpful answer')
                                        }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="lg:w-3/5">
                            <div class="relative">
                                <FormTextArea v-model="form.answer" :name="''" :error="form.errors.answer"
                                    :type="'text'" :textAreaRows="4" :class="'w-full'"
                                    :placeholder="$t('Your detailed answer here...')" />
                                <div v-if="form.answer"
                                    class="absolute bottom-3 right-3 text-xs text-gray-400 bg-white px-2 py-1 rounded-lg">
                                    {{ form.answer.length }} {{ $t('characters') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Section -->
                <div class="p-6 md:p-8">
                    <div class="flex flex-col lg:flex-row lg:space-x-12">
                        <div class="lg:w-2/5 mb-6 lg:mb-0">
                            <div class="flex items-start space-x-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-[#ff5100] to-[#ff7733] rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-white">
                                        <path fill="currentColor"
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $t('Status') }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $t('Control FAQ visibility') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="lg:w-3/5">
                            <FormSelect v-model="form.status" :options="statusOptions" :error="form.errors.status"
                                :name="''" :class="'w-full'" :placeholder="$t('Select status')" />

                            <!-- Status Info Cards -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                                <div :class="[
                                    'p-4 rounded-xl border-2 transition-all duration-200',
                                    form.status === 1
                                        ? 'bg-gradient-to-br from-green-50 to-green-100 border-green-300'
                                        : 'bg-gray-50 border-gray-200'
                                ]">
                                    <div class="flex items-center space-x-2">
                                        <div :class="[
                                            'w-8 h-8 rounded-lg flex items-center justify-center',
                                            form.status === 1
                                                ? 'bg-green-500'
                                                : 'bg-gray-400'
                                        ]">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" class="text-white">
                                                <path fill="currentColor"
                                                    d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5s5 2.24 5 5s-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3s3-1.34 3-3s-1.34-3-3-3z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $t('Display') }}</p>
                                            <p class="text-xs text-gray-500">{{ $t('Visible to users') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div :class="[
                                    'p-4 rounded-xl border-2 transition-all duration-200',
                                    form.status === 0
                                        ? 'bg-gradient-to-br from-red-50 to-red-100 border-red-300'
                                        : 'bg-gray-50 border-gray-200'
                                ]">
                                    <div class="flex items-center space-x-2">
                                        <div :class="[
                                            'w-8 h-8 rounded-lg flex items-center justify-center',
                                            form.status === 0
                                                ? 'bg-red-500'
                                                : 'bg-gray-400'
                                        ]">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" class="text-white">
                                                <path fill="currentColor"
                                                    d="M12 7c2.76 0 5 2.24 5 5c0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75c-1.73-4.39-6-7.5-11-7.5c-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28l.46.46A11.804 11.804 0 0 0 1 12c1.73 4.39 6 7.5 11 7.5c1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22L21 20.73L3.27 3L2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65c0 1.66 1.34 3 3 3c.22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53c-2.76 0-5-2.24-5-5c0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15l.02-.16c0-1.66-1.34-3-3-3l-.17.01z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $t('Hide') }}</p>
                                            <p class="text-xs text-gray-500">{{ $t('Hidden from users') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="px-6 md:px-8 pb-6 md:pb-8 pt-4 border-t-2 border-gray-100">
                    <div class="flex justify-end">
                        <button type="submit" :disabled="form.processing"
                            :class="['px-8 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff6622] hover:from-[#ff6622] hover:to-[#ff5100] text-white rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center space-x-2', { 'opacity-50 cursor-not-allowed': form.processing }]">
                            <svg v-if="form.processing" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" class="animate-spin">
                                <path fill="currentColor"
                                    d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                    opacity=".5" />
                                <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3s3 1.34 3 3s-1.34 3-3 3zm3-10H5V5h10v4z" />
                            </svg>
                            <span v-if="form.processing">{{ $t('Saving...') }}</span>
                            <span v-else>{{ $t('Save') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import { ref } from 'vue';
import { Link, useForm } from "@inertiajs/vue3";
import FormTextArea from '@/Components/FormTextArea.vue';
import FormSelect from '@/Components/FormSelect.vue';
import { ArrowLeft } from "lucide-vue-next";

const props = defineProps({
    title: String,
    faq: Object
});

const getDetail = (value, key) => {
    if (value) {
        const item = JSON.parse(value);
        return item?.[key] ?? null;
    } else {
        return null;
    }
}

const form = useForm({
    question: props.faq?.question,
    answer: props.faq?.answer,
    status: props.faq?.status,
})

const statusOptions = ref([
    { value: 0, label: 'Hide' },
    { value: 1, label: 'Display' }
]);

const submitForm = async () => {
    const url = props.faq ? window.location.pathname : '/admin/faqs';

    form[props.faq ? 'put' : 'post'](url, {
        preserveScroll: true,
    });
};
</script>