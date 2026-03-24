<!-- <script setup>
import { ref } from 'vue';
import debounce from 'lodash/debounce';
import { Link, router } from "@inertiajs/vue3";
import Table from '@/Components/Table.vue';
import TableHeader from '@/Components/TableHeader.vue';
import TableHeaderRow from '@/Components/TableHeaderRow.vue';
import TableHeaderRowItem from '@/Components/TableHeaderRowItem.vue';
import TableBody from '@/Components/TableBody.vue';
import TableBodyRow from '@/Components/TableBodyRow.vue';
import TableBodyRowItem from '@/Components/TableBodyRowItem.vue';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object
    }
});

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
            :placeholder="$t('Search here')">
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
    <Table :rows="rows">
        <TableHeader>
            <TableHeaderRow>
                <TableHeaderRowItem :position="'first'">{{ $t('Name') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Subject') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Last updated') }}</TableHeaderRowItem>
                <TableHeaderRowItem :position="'last'"></TableHeaderRowItem>
            </TableHeaderRow>
        </TableHeader>
        <TableBody>
            <TableBodyRow v-for="(item, index) in rows.data" :key="index">
                <TableBodyRowItem :position="'first'" class="capitalize">
                    <Link :href="'/admin/settings/email-template/' + item.id">
                    {{ item.name }}
                    </Link>
                </TableBodyRowItem>
                <TableBodyRowItem>
                    <Link :href="'/admin/settings/email-template/' + item.id">
                    <div
                        class="py-1 px-2 bg-gray-50 rounded-[5px] border border-dashed w-[20em] truncate text-xs capitalize">
                        {{ item.subject }}
                    </div>
                    </Link>
                </TableBodyRowItem>
                <TableBodyRowItem>
                    <Link :href="'/admin/settings/email-template/' + item.id">
                    {{ item.updated_at }}
                    </Link>
                </TableBodyRowItem>
                <TableBodyRowItem :position="'last'">
                    <div class="flex items-center mt-3">
                        <Link :href="'/admin/settings/email-template/' + item.id">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor" fill-rule="evenodd"
                                d="M9.97 7.47a.75.75 0 0 1 1.06 0l4 4a.75.75 0 0 1 0 1.06l-4 4a.75.75 0 1 1-1.06-1.06L13.44 12L9.97 8.53a.75.75 0 0 1 0-1.06Z"
                                clip-rule="evenodd" />
                        </svg>
                        </Link>
                    </div>
                </TableBodyRowItem>
            </TableBodyRow>
        </TableBody>
    </Table>
</template> -->



<!-- ============================================ NEW UI CODE ============================================= -->


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
                    :placeholder="$t('Search templates...')">
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

    <!-- Table -->
    <div class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-50 to-orange-50/20">
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">
                            {{ $t('Name') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">
                            {{ $t('Subject') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">
                            {{ $t('Last updated') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">
                            {{ $t('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    <tr v-for="(item, index) in rows.data" :key="index"
                        class="group hover:bg-slate-50/50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <Link :href="'/admin/settings/email-template/' + item.id" class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-slate-100 to-slate-50 rounded-lg group-hover:from-[#ff5100]/10 group-hover:to-[#ff7340]/5 transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    class="text-[#ff5100] transition-colors">
                                    <path fill="currentColor"
                                        d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5l-8-5V6l8 5l8-5v2z" />
                                </svg>
                            </div>
                            <span class="text-sm font-semibold text-slate-900 capitalize">{{ item.name }}</span>
                            </Link>
                        </td>
                        <td class="px-6 py-4">
                            <Link :href="'/admin/settings/email-template/' + item.id">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-slate-50 to-slate-100/50 rounded-lg border border-slate-200 max-w-md group-hover:border-[#ff5100]/20 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    class="text-slate-500 shrink-0">
                                    <path fill="currentColor"
                                        d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z" />
                                </svg>
                                <span class="text-xs text-slate-700 truncate capitalize">{{ item.subject }}</span>
                            </div>
                            </Link>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <Link :href="'/admin/settings/email-template/' + item.id">
                            <div class="flex items-center gap-2 text-sm text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    class="text-slate-400">
                                    <path fill="currentColor"
                                        d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm4.2 14.2L11 13V7h1.5v5.2l4.5 2.7l-.8 1.3z" />
                                </svg>
                                {{ item.updated_at }}
                            </div>
                            </Link>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <Link :href="'/admin/settings/email-template/' + item.id">
                            <button type="button"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white text-sm font-medium rounded-lg shadow-md shadow-[#ff5100]/20 hover:shadow-lg hover:shadow-[#ff5100]/30 transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M5 19h1.4l8.625-8.625l-1.4-1.4L5 17.6V19ZM19.3 8.925l-4.25-4.2l1.4-1.4q.575-.575 1.413-.575t1.412.575l1.4 1.4q.575.575.6 1.388t-.55 1.387L19.3 8.925ZM17.85 10.4L7.25 21H3v-4.25l10.6-10.6l4.25 4.25Zm-3.525-.725l-.7-.7l1.4 1.4l-.7-.7Z" />
                                </svg>
                                {{ $t('Edit') }}
                            </button>
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <Pagination class="mt-3" :pagination="rows.meta" />
        <!-- <div v-if="rows.links && rows.links.length > 3"
            class="mt-6 flex items-center justify-between border-t border-slate-200 pt-4">
            <div class="text-sm text-slate-600">
                {{ $t('Showing') }} <span class="font-semibold text-slate-900">{{ rows.from }}</span> {{ $t('to') }}
                <span class="font-semibold text-slate-900">{{ rows.to }}</span> {{ $t('of') }}
                <span class="font-semibold text-slate-900">{{ rows.total }}</span> {{ $t('results') }}
            </div>
            <div class="flex gap-1">
                <Link v-for="(link, index) in rows.links" :key="index" :href="link.url" :class="[
                    'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200',
                    link.active
                        ? 'bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white shadow-md shadow-[#ff5100]/20'
                        : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50',
                    !link.url ? 'opacity-50 cursor-not-allowed' : ''
                ]" :disabled="!link.url" v-html="link.label" />
            </div>
        </div> -->
    </div>
</template>

<script setup>
import { ref } from 'vue';
import debounce from 'lodash/debounce';
import { Link, router } from "@inertiajs/vue3";
import Pagination from '../Pagination.vue';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object
    }
});

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
</script>

<style scoped>
/* Custom scrollbar for table */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>