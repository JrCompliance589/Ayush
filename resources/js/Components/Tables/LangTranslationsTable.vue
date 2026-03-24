<!-- <script setup>
import { ref } from 'vue';
import debounce from 'lodash/debounce';
import { router, useForm } from '@inertiajs/vue3';
import FormTextArea from '@/Components/FormTextArea.vue';

const props = defineProps({
    rows: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object
    },
    language: {
        type: Object
    }
});

const selectedString = ref(null);

const emit = defineEmits(['edit', 'delete']);

const params = ref({
    search: props.filters.search,
});

const isSearching = ref(false);

const clearSearch = () => {
    params.value.search = null;
    runSearch();
}

const search = debounce(() => {
    isSearching.value = true;
    runSearch();
}, 1000);

const runSearch = () => {
    const url = window.location.pathname;

    router.visit(url, {
        method: 'get',
        data: params.value,
    })
}

const form2 = useForm({
    'translation': null
});

const openTextArea = (key, translation) => {
    selectedString.value = key;
    form2.translation = translation;
}

const submitForm = async () => {
    form2.post('/admin/translations/' + props.language.code + '/' + selectedString.value, {
        preserveScroll: true,
        onFinish: () => {
            selectedString.value = null;
        }
    })
};
</script>
<template>
    <div
        class="md:bg-white flex items-center border border-primary md:border-none md:shadow-sm h-12 md:h-10 w-full md:w-80 rounded-[0.5rem] mb-6 text-xl md:text-sm">
        <span class="pl-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m15 15l6 6m-11-4a7 7 0 1 1 0-14a7 7 0 0 1 0 14Z" />
            </svg>
        </span>
        <input @input="search" v-model="params.search" type="text" class="outline-none px-4 w-full"
            :placeholder="$t('Search translation string')">
        <button v-if="isSearching === false && params.search" @click="clearSearch" type="button" class="pr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path fill="currentColor"
                    d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm3.7 12.3c.4.4.4 1 0 1.4c-.4.4-1 .4-1.4 0L12 13.4l-2.3 2.3c-.4.4-1 .4-1.4 0c-.4-.4-.4-1 0-1.4l2.3-2.3l-2.3-2.3c-.4-.4-.4-1 0-1.4c.4-.4 1-.4 1.4 0l2.3 2.3l2.3-2.3c.4-.4 1-.4 1.4 0c.4.4.4 1 0 1.4L13.4 12l2.3 2.3z" />
            </svg>
        </button>
        <span v-if="isSearching" class="pr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                    <animateTransform attributeName="transform" calcMode="discrete" dur="2.4s" repeatCount="indefinite"
                        type="rotate" values="0 12 12;90 12 12;180 12 12;270 12 12" />
                    <animate attributeName="opacity" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite"
                        values="1;1;0" />
                </circle>
                <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                    <animateTransform attributeName="transform" begin="0.2s" calcMode="discrete" dur="2.4s"
                        repeatCount="indefinite" type="rotate" values="30 12 12;120 12 12;210 12 12;300 12 12" />
                    <animate attributeName="opacity" begin="0.2s" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite"
                        values="1;1;0" />
                </circle>
                <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                    <animateTransform attributeName="transform" begin="0.4s" calcMode="discrete" dur="2.4s"
                        repeatCount="indefinite" type="rotate" values="60 12 12;150 12 12;240 12 12;330 12 12" />
                    <animate attributeName="opacity" begin="0.4s" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite"
                        values="1;1;0" />
                </circle>
            </svg>
        </span>
    </div>

    <div
        class="grid grid-cols-2 bg-gray-50 px-2 md:border-b-0 border-[#d1d5db] text-sm rounded-tr-[8px] rounded-tl-[8px]">
        <div class="px-2 py-3 max-w-[25em] truncate">{{ $t('Key') }}</div>
        <div class="px-2 py-3 truncate">{{ $t('Translation') }}</div>
    </div>
    <div v-for="(item, index) in props.rows" :key="index">
        <div
            class="grid grid-cols-2 bg-white px-2 hover:bg-slate-50 md:border-b-0 md:border-t border-[#d1d5db] text-sm border-b cursor-pointer">
            <div class="p-2 flex items-center">
                <div class="max-w-[24em] truncate">{{ item.Key }}</div>
            </div>
            <div class="p-2 truncate flex items-center w-full">
                <div class="float-left truncate" :class="selectedString != item.Key ? 'max-w-[25em]' : 'w-full'">
                    <FormTextArea v-if="selectedString === item.Key" v-model="form2.translation" :name="''"
                        :showLabel="false" :class="'w-full'" />
                    <span v-else @click="openTextArea(item.Key, item.Translation)">{{ item.Translation }}</span>
                </div>

                <div v-if="selectedString === item.Key" class="ml-auto hover:bg-gray-200 p-2 rounded-full"
                    @click="submitForm()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.5">
                            <path
                                d="M14.25 8.75c-.5 2.5-2.385 4.854-5.03 5.38A6.25 6.25 0 0 1 3.373 3.798C5.187 1.8 8.25 1.25 10.75 2.25" />
                            <path d="m5.75 7.75l2.5 2.5l6-6.5" />
                        </g>
                    </svg>
                </div>
                <div v-else class="ml-auto hover:bg-gray-200 p-2 rounded-full"
                    @click="openTextArea(item.Key, item.Translation)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                        <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.5">
                            <path
                                d="M9.533 11.15A1.823 1.823 0 0 0 9 12.438V15h2.578c.483 0 .947-.192 1.289-.534l7.6-7.604a1.822 1.822 0 0 0 0-2.577l-.751-.751a1.822 1.822 0 0 0-2.578 0z" />
                            <path
                                d="M21 12c0 4.243 0 6.364-1.318 7.682C18.364 21 16.242 21 12 21c-4.243 0-6.364 0-7.682-1.318C3 18.364 3 16.242 3 12c0-4.243 0-6.364 1.318-7.682C5.636 3 7.758 3 12 3" />
                        </g>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template> -->


<!-- ========================================== NEW UI CODE ==================================== -->


<template>
    <!-- Search Bar -->
    <div class="mb-6">
        <div class="relative max-w-screen-sm">
            <div
                class="flex items-center bg-white border border-slate-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden">
                <span class="pl-4 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                        <path fill="currentColor" d="m15 15l6 6m-11-4a7 7 0 1 1 0-14a7 7 0 0 1 0 14Z"
                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </span>
                <input @input="search" v-model="params.search" type="text"
                    class="outline-none px-4 py-3 w-full text-sm text-slate-900 placeholder-slate-400"
                    :placeholder="$t('Search translation string...')">
                <button v-if="isSearching === false && params.search" @click="clearSearch" type="button"
                    class="pr-4 text-slate-400 hover:text-slate-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm3.7 12.3c.4.4.4 1 0 1.4c-.4.4-1 .4-1.4 0L12 13.4l-2.3 2.3c-.4.4-1 .4-1.4 0c-.4-.4-.4-1 0-1.4l2.3-2.3l-2.3-2.3c-.4-.4-.4-1 0-1.4c.4-.4 1-.4 1.4 0l2.3 2.3l2.3-2.3c.4-.4 1-.4 1.4 0c.4.4.4 1 0 1.4L13.4 12l2.3 2.3z" />
                    </svg>
                </button>
                <span v-if="isSearching" class="pr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        class="text-[#ff5100]">
                        <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                            <animateTransform attributeName="transform" calcMode="discrete" dur="2.4s"
                                repeatCount="indefinite" type="rotate" values="0 12 12;90 12 12;180 12 12;270 12 12" />
                            <animate attributeName="opacity" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite"
                                values="1;1;0" />
                        </circle>
                        <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                            <animateTransform attributeName="transform" begin="0.2s" calcMode="discrete" dur="2.4s"
                                repeatCount="indefinite" type="rotate"
                                values="30 12 12;120 12 12;210 12 12;300 12 12" />
                            <animate attributeName="opacity" begin="0.2s" dur="0.6s" keyTimes="0;0.5;1"
                                repeatCount="indefinite" values="1;1;0" />
                        </circle>
                        <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                            <animateTransform attributeName="transform" begin="0.4s" calcMode="discrete" dur="2.4s"
                                repeatCount="indefinite" type="rotate"
                                values="60 12 12;150 12 12;240 12 12;330 12 12" />
                            <animate attributeName="opacity" begin="0.4s" dur="0.6s" keyTimes="0;0.5;1"
                                repeatCount="indefinite" values="1;1;0" />
                        </circle>
                    </svg>
                </span>
            </div>
        </div>
    </div>

    <!-- Translations Table -->
    <div class="overflow-hidden rounded-xl border border-slate-200">
        <!-- Table Header -->
        <div class="grid grid-cols-2 bg-gradient-to-r from-slate-50 to-orange-50/20 border-b border-slate-200">
            <div class="px-6 py-4 text-xs font-bold text-slate-700 uppercase tracking-wider">
                {{ $t('Key') }}
            </div>
            <div class="px-6 py-4 text-xs font-bold text-slate-700 uppercase tracking-wider">
                {{ $t('Translation') }}
            </div>
        </div>

        <!-- Table Body -->
        <div v-for="(item, index) in props.rows" :key="index">
            <div
                class="grid grid-cols-2 bg-white hover:bg-slate-50/50 transition-colors duration-200 border-b border-slate-100">
                <!-- Key Column -->
                <div class="px-6 py-4 flex items-center">
                    <div class="flex items-center gap-3 max-w-full">
                        <!-- <div class="p-2 bg-gradient-to-br from-slate-100 to-slate-50 rounded-lg shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                class="text-slate-600">
                                <path fill="currentColor"
                                    d="M7 14q-.825 0-1.412-.588Q5 12.825 5 12t.588-1.413Q6.175 10 7 10t1.413.587Q9 11.175 9 12q0 .825-.587 1.412Q7.825 14 7 14Zm0 4q-2.5 0-4.25-1.75T1 12q0-2.5 1.75-4.25T7 6q1.675 0 3.038.825Q11.4 7.65 12.2 9H21l3 3l-4.5 4.5l-2-1.5l-2 1.5l-2.125-1.5H12.2q-.8 1.35-2.162 2.175Q8.675 18 7 18Z" />
                            </svg>
                        </div> -->
                        <span class="text-sm font-mono text-slate-700 truncate">{{ item.Key }}</span>
                    </div>
                </div>

                <!-- Translation Column -->
                <div class="px-6 py-4 flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <FormTextArea v-if="selectedString === item.Key" v-model="form2.translation" :name="''"
                            :showLabel="false" class="w-full" />
                        <span v-else @click="openTextArea(item.Key, item.Translation)"
                            class="text-sm text-slate-900 cursor-pointer hover:text-[#ff5100] transition-colors block truncate">
                            {{ item.Translation || '-' }}
                        </span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 shrink-0">
                        <button v-if="selectedString === item.Key" @click="submitForm()"
                            class="p-2 rounded-lg bg-gradient-to-r from-green-50 to-emerald-50 hover:from-green-100 hover:to-emerald-100 text-green-600 transition-all duration-200"
                            title="Save">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16">
                                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="1.5">
                                    <path
                                        d="M14.25 8.75c-.5 2.5-2.385 4.854-5.03 5.38A6.25 6.25 0 0 1 3.373 3.798C5.187 1.8 8.25 1.25 10.75 2.25" />
                                    <path d="m5.75 7.75l2.5 2.5l6-6.5" />
                                </g>
                            </svg>
                        </button>
                        <button v-else @click="openTextArea(item.Key, item.Translation)"
                            class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-600 transition-all duration-200"
                            title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M5 19h1.4l8.625-8.625l-1.4-1.4L5 17.6V19ZM19.3 8.925l-4.25-4.2l1.4-1.4q.575-.575 1.413-.575t1.412.575l1.4 1.4q.575.575.6 1.388t-.55 1.387L19.3 8.925ZM17.85 10.4L7.25 21H3v-4.25l10.6-10.6l4.25 4.25Zm-3.525-.725l-.7-.7l1.4 1.4l-.7-.7Z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import debounce from 'lodash/debounce';
import { router, useForm } from '@inertiajs/vue3';
import FormTextArea from '@/Components/FormTextArea.vue';

const props = defineProps({
    rows: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object
    },
    language: {
        type: Object
    }
});

const selectedString = ref(null);

const params = ref({
    search: props.filters.search,
});

const isSearching = ref(false);

const clearSearch = () => {
    params.value.search = null;
    runSearch();
}

const search = debounce(() => {
    isSearching.value = true;
    runSearch();
}, 1000);

const runSearch = () => {
    const url = window.location.pathname;

    router.visit(url, {
        method: 'get',
        data: params.value,
    })
}

const form2 = useForm({
    'translation': null
});

const openTextArea = (key, translation) => {
    selectedString.value = key;
    form2.translation = translation;
}

const submitForm = async () => {
    form2.post('/admin/translations/' + props.language.code + '/' + selectedString.value, {
        preserveScroll: true,
        onFinish: () => {
            selectedString.value = null;
        }
    })
};
</script>

<style scoped>
/* Ensure textarea fills properly */
:deep(textarea) {
    min-height: 80px;
    resize: vertical;
}
</style>