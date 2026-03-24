<!-- <template>
    <AppLayout>
        <div class="flex justify-between">
            <div>
                <h2 class="text-xl mb-1">{{ $t('Template') }}: {{ props.template.name }}</h2>
                <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                    </svg>
                    <span class="ml-1 mt-1">{{ $t('Configure email templates') }}</span>
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <button @click="submitForm()"
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{
                        $t('Save') }}</button>
                <Link href="/admin/settings/email-templates"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-300 flex items-center space-x-2">
                <ArrowLeft class="w-4 h-4" />
                <span>{{ $t('Back') }}</span></Link>
            </div>
        </div>
        <form @submit.prevent="submitForm()">
            <FormInput v-model="form.subject" :name="''" :error="form.errors.subject" :type="'text'"
                :class="'sm:col-span-6 mb-4'" />
            <QuillEditor v-model:content="form.body" toolbar="essential" contentType="html" theme="snow" />
        </form>
    </AppLayout>
</template>
<script setup>
import AppLayout from "../Layout/App.vue";
import { Link, useForm } from "@inertiajs/vue3";
import FormInput from '@/Components/FormInput.vue';
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({ template: Object });

const form = useForm({
    _method: 'put',
    subject: props.template.subject,
    body: props.template.body,
})

const submitForm = async () => {
    const url = window.location.pathname;

    form.put(url, {
        preserveScroll: true,
    });
};
</script> -->

<!-- ============================================ NEW UI CODE ============================================= -->


<template>
    <AppLayout>
        <div class="space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <!-- Email Template Editor Card -->
            <div class="group bg-white rounded-2xl border border-slate-200 shadow-md">
                <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                    <div class="flex items-center justify-between">
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
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Template') }}: {{
                                    props.template.name }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Configure email templates') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-3">
                            <button @click="submitForm()" type="button"
                                class="group px-6 py-2.5 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 transition-all duration-200">
                                <span class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="m9.55 18l-5.7-5.7l1.425-1.425L9.55 15.15l9.175-9.175L20.15 7.4z" />
                                    </svg>
                                    {{ $t('Save') }}
                                </span>
                            </button>
                            <Link href="/admin/settings/email-templates"
                                class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-200 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M20 11H7.83l5.59-5.59L12 4l-8 8l8 8l1.41-1.41L7.83 13H20v-2z" />
                            </svg>
                            <span>{{ $t('Back') }}</span>
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <form @submit.prevent="submitForm()" class="space-y-6">
                        <!-- Subject Input -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('Email Subject') }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('The subject line for this email template') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3">
                                <FormInput v-model="form.subject" :name="''" :error="form.errors.subject"
                                    :type="'text'" />
                            </div>
                        </div>

                        <div class="border-t border-slate-100"></div>

                        <!-- Email Body Editor -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('Email Body') }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Compose your email template content') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3">
                                <div
                                    class="rounded-xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-all duration-200">
                                    <QuillEditor v-model:content="form.body" toolbar="essential" contentType="html"
                                        theme="snow" class="email-editor" />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "../Layout/App.vue";
import { Link, useForm } from "@inertiajs/vue3";
import FormInput from '@/Components/FormInput.vue';
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({
    template: Object
});

const form = useForm({
    _method: 'put',
    subject: props.template.subject,
    body: props.template.body,
})

const submitForm = async () => {
    const url = window.location.pathname;

    form.put(url, {
        preserveScroll: true,
    });
};
</script>

<style>
/* Custom Quill Editor Styling */
.email-editor .ql-toolbar {
    background: linear-gradient(to right, #f8fafc, #fff7ed20);
    border: none;
    border-bottom: 1px solid #e2e8f0;
    border-radius: 0;
    padding: 12px;
}

.email-editor .ql-container {
    border: none;
    font-size: 14px;
    min-height: 400px;
}

.email-editor .ql-editor {
    padding: 20px;
    min-height: 400px;
}

.email-editor .ql-toolbar .ql-stroke {
    stroke: #64748b;
}

.email-editor .ql-toolbar .ql-fill {
    fill: #64748b;
}

.email-editor .ql-toolbar button:hover .ql-stroke,
.email-editor .ql-toolbar button.ql-active .ql-stroke {
    stroke: #ff5100;
}

.email-editor .ql-toolbar button:hover .ql-fill,
.email-editor .ql-toolbar button.ql-active .ql-fill {
    fill: #ff5100;
}

.email-editor .ql-editor.ql-blank::before {
    color: #94a3b8;
    font-style: normal;
}
</style>
