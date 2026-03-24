<!-- <template>
    <AppLayout>
        <div class="flex justify-between">
            <div>
                <h2 class="text-xl mb-1">{{ $t('Frontend pages') }} | {{ $t('Edit') }}</h2>
                <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                    </svg>
                    <span class="ml-1 mt-1">{{ $t('Configure pages') }}</span>
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <Link href="/admin/settings/pages" class="hover:bg-white rounded-full p-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path fill="black"
                        d="M19 11H7.14l3.63-4.36a1 1 0 1 0-1.54-1.28l-5 6a1.19 1.19 0 0 0-.09.15c0 .05 0 .08-.07.13A1 1 0 0 0 4 12a1 1 0 0 0 .07.36c0 .05 0 .08.07.13a1.19 1.19 0 0 0 .09.15l5 6A1 1 0 0 0 10 19a1 1 0 0 0 .64-.23a1 1 0 0 0 .13-1.41L7.14 13H19a1 1 0 0 0 0-2" />
                </svg>
                </Link>
                <button @click="deletePage()" class="hover:bg-white rounded-full p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <path fill="red"
                            d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6zM8 9h8v10H8zm7.5-5l-1-1h-5l-1 1H5v2h14V4z" />
                    </svg>
                </button>
                <button @click="submitForm()"
                    class="rounded-md bg-indigo-600 px-3 py-1 text-sm text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{
                        $t('Save') }}</button>
            </div>
        </div>
        <form @submit.prevent="submitForm()">
            <FormInput v-model="form.name" :name="''" :error="form.errors.name" :type="'text'"
                :class="'sm:col-span-6'" />
            <div class="text-sm mb-4 mt-3 rounded-md bg-gray-200 px-1 w-fit">Slug: /{{ formattedName }}</div>
            <div class="bg-white">
                <QuillEditor v-model:content="form.content" toolbar="essential" contentType="html" theme="snow" />
            </div>
        </form>
    </AppLayout>
</template>
<script setup>
import AppLayout from "../Layout/App.vue";
import { computed } from 'vue'
import { Link, useForm } from "@inertiajs/vue3";
import FormInput from '@/Components/FormInput.vue';
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({ page: Object });

const form = useForm({
    _method: 'put',
    name: props.page.name,
    content: props.page.content,
})

const submitForm = async () => {
    const url = window.location.pathname;

    form.put(url, {
        preserveScroll: true,
    });
};

const deletePage = async () => {
    const url = window.location.pathname;

    form.delete(url, {
        onBefore: () => confirm('Are you sure you want to delete this page?'),
        preserveScroll: true,
    });
};

const formattedName = computed(() => {
    return form.name.trim().toLowerCase().replace(/\s+/g, '-')
})
</script> -->

<!-- ===================================================== New Ui Code ====================================================== -->


<template>
    <AppLayout>
        <div class="p-6 md:p-8 space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <!-- Frontend Page Editor Card -->
            <div class="group bg-white rounded-2xl border border-slate-200 shadow-md">
                <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Frontend pages') }} | {{ $t('Edit')
                                }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Configure pages') }}</span>
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

                            <button @click="deletePage()" type="button"
                                class="p-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl border border-red-200 transition-all duration-200"
                                title="Delete Page">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6zM8 9h8v10H8zm7.5-5l-1-1h-5l-1 1H5v2h14V4z" />
                                </svg>
                            </button>

                            <Link href="/admin/settings/pages"
                                class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl border border-slate-200 transition-all duration-200"
                                title="Back to Pages">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M20 11H7.83l5.59-5.59L12 4l-8 8l8 8l1.41-1.41L7.83 13H20v-2z" />
                            </svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <form @submit.prevent="submitForm()" class="space-y-6">
                        <!-- Page Name -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('Page Name') }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('The display name for this page') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3">
                                <FormInput v-model="form.name" :name="''" :error="form.errors.name" :type="'text'" />
                            </div>
                        </div>

                        <!-- Page Slug -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('Page URL') }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Auto-generated URL slug') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3">
                                <div
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-slate-50 to-slate-100/50 rounded-lg border border-slate-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        class="text-slate-500">
                                        <path fill="currentColor"
                                            d="M16 6H3v2h13V6zm-6 6H3v2h7v-2zm6 4c-.55 0-1 .45-1 1s.45 1 1 1s1-.45 1-1s-.45-1-1-1zM3 16h5v-2H3v2zm13-10c-.55 0-1 .45-1 1s.45 1 1 1s1-.45 1-1s-.45-1-1-1zM8 6c-.55 0-1 .45-1 1s.45 1 1 1s1-.45 1-1s-.45-1-1-1zm8 6c-.55 0-1 .45-1 1s.45 1 1 1s1-.45 1-1s-.45-1-1-1z" />
                                    </svg>
                                    <span class="text-sm font-mono text-slate-700">/{{ formattedName }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-slate-100"></div>

                        <!-- Page Content Editor -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('Page Content') }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Create your page content') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3">
                                <div
                                    class="rounded-xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-all duration-200">
                                    <QuillEditor v-model:content="form.content" toolbar="essential" contentType="html"
                                        theme="snow" class="page-editor" />
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
import { computed } from 'vue'
import { Link, useForm } from "@inertiajs/vue3";
import FormInput from '@/Components/FormInput.vue';
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({
    page: Object
});

const form = useForm({
    _method: 'put',
    name: props.page.name,
    content: props.page.content,
})

const submitForm = async () => {
    const url = window.location.pathname;

    form.put(url, {
        preserveScroll: true,
    });
};

const deletePage = async () => {
    const url = window.location.pathname;

    form.delete(url, {
        onBefore: () => confirm('Are you sure you want to delete this page?'),
        preserveScroll: true,
    });
};

const formattedName = computed(() => {
    return form.name.trim().toLowerCase().replace(/\s+/g, '-')
})
</script>

<style>
/* Custom Quill Editor Styling */
.page-editor .ql-toolbar {
    background: linear-gradient(to right, #f8fafc, #fff7ed20);
    border: none;
    border-bottom: 1px solid #e2e8f0;
    border-radius: 0;
    padding: 12px;
}

.page-editor .ql-container {
    border: none;
    font-size: 14px;
    min-height: 500px;
}

.page-editor .ql-editor {
    padding: 20px;
    min-height: 500px;
}

.page-editor .ql-toolbar .ql-stroke {
    stroke: #64748b;
}

.page-editor .ql-toolbar .ql-fill {
    fill: #64748b;
}

.page-editor .ql-toolbar button:hover .ql-stroke,
.page-editor .ql-toolbar button.ql-active .ql-stroke {
    stroke: #ff5100;
}

.page-editor .ql-toolbar button:hover .ql-fill,
.page-editor .ql-toolbar button.ql-active .ql-fill {
    fill: #ff5100;
}

.page-editor .ql-editor.ql-blank::before {
    color: #94a3b8;
    font-style: normal;
}
</style>