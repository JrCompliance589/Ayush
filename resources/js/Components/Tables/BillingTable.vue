<!-- <script setup>
import { ref } from 'vue';
import debounce from 'lodash/debounce';
import { router } from '@inertiajs/vue3';
import 'vue3-toastify/dist/index.css';
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
    },
    uuid: {
        type: String,
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

const isLastRow = (index) => {
    return index === props.rows.data.length - 1;
}

const search = debounce(() => {
    isSearching.value = true;
    runSearch();
}, 1000);

const runSearch = () => {
    router.visit(window.location.pathname, {
        method: 'get',
        data: params.value,
    })
}

const emit = defineEmits(['update:modelValue', 'callback']);
</script>
<template>
    <div class="flex justify-between">
        <div class="bg-white flex items-center shadow-sm h-10 w-full md:w-80 rounded-[0.5rem] mb-6 text-sm">
            <span class="pl-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="m15 15l6 6m-11-4a7 7 0 1 1 0-14a7 7 0 0 1 0 14Z" />
                </svg>
            </span>
            <input @input="search" v-model="params.search" type="text" class="outline-none px-4 w-full bg-inherit"
                :placeholder="$t('Search billing logs')">
            <button v-if="isSearching === false && params.search" @click="clearSearch" type="button" class="pr-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm3.7 12.3c.4.4.4 1 0 1.4c-.4.4-1 .4-1.4 0L12 13.4l-2.3 2.3c-.4.4-1 .4-1.4 0c-.4-.4-.4-1 0-1.4l2.3-2.3l-2.3-2.3c-.4-.4-.4-1 0-1.4c.4-.4 1-.4 1.4 0l2.3 2.3l2.3-2.3c.4-.4 1-.4 1.4 0c.4.4.4 1 0 1.4L13.4 12l2.3 2.3z" />
                </svg>
            </button>
            <span v-if="isSearching" class="pr-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                        <animateTransform attributeName="transform" calcMode="discrete" dur="2.4s"
                            repeatCount="indefinite" type="rotate" values="0 12 12;90 12 12;180 12 12;270 12 12" />
                        <animate attributeName="opacity" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite"
                            values="1;1;0" />
                    </circle>
                    <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                        <animateTransform attributeName="transform" begin="0.2s" calcMode="discrete" dur="2.4s"
                            repeatCount="indefinite" type="rotate" values="30 12 12;120 12 12;210 12 12;300 12 12" />
                        <animate attributeName="opacity" begin="0.2s" dur="0.6s" keyTimes="0;0.5;1"
                            repeatCount="indefinite" values="1;1;0" />
                    </circle>
                    <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                        <animateTransform attributeName="transform" begin="0.4s" calcMode="discrete" dur="2.4s"
                            repeatCount="indefinite" type="rotate" values="60 12 12;150 12 12;240 12 12;330 12 12" />
                        <animate attributeName="opacity" begin="0.4s" dur="0.6s" keyTimes="0;0.5;1"
                            repeatCount="indefinite" values="1;1;0" />
                    </circle>
                </svg>
            </span>
        </div>
    </div>
    <Table :rows="rows">
        <TableHeader>
            <TableHeaderRow>
                <TableHeaderRowItem :position="'first'">{{ $t('Date') }}</TableHeaderRowItem>
                <TableHeaderRowItem :position="'first'" class="hidden sm:table-cell">{{ $t('Organization') }}
                </TableHeaderRowItem>
                <TableHeaderRowItem class="hidden sm:table-cell">{{ $t('Description') }}</TableHeaderRowItem>
                <TableHeaderRowItem class="hidden sm:table-cell">{{ $t('Amount') }}</TableHeaderRowItem>
                <TableHeaderRowItem :position="'last'"></TableHeaderRowItem>
            </TableHeaderRow>
        </TableHeader>
        <TableBody>
            <TableBodyRow v-for="(item, index) in rows.data" :key="index" :class="!isLastRow(index) ? 'border-b' : ''">
                <TableBodyRowItem :position="'first'" class="hidden sm:table-cell">{{ item.created_at }}
                </TableBodyRowItem>
                <TableBodyRowItem :position="'first'" class="capitalize">{{ item.organization.name }}</TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell capitalize">{{ item.description }}</TableBodyRowItem>
                <TableBodyRowItem class="">{{ item.amount }}</TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">
                    <span class="float-right"></span>
                </TableBodyRowItem>
            </TableBodyRow>
        </TableBody>
    </Table>
</template> -->


<!-- ========================================== NEW UI CODE ==================================== -->


<template>
    <div class="space-y-6">
        <!-- Search Bar -->
        <div class="relative">
            <div class="relative group max-w-screen-sm">
                <div
                    class="absolute inset-0 bg-gradient-to-r from-[#ff5100] to-[#ff7733] rounded-2xl opacity-0 group-hover:opacity-10 transition-opacity duration-300">
                </div>
                <div
                    class="relative bg-white flex items-center shadow-lg h-14 rounded-2xl transition-all duration-300 border-2 border-orange-100">
                    <span class="pl-5 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                            class="text-[#ff5100]">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m15 15l6 6m-11-4a7 7 0 1 1 0-14a7 7 0 0 1 0 14Z" />
                        </svg>
                    </span>
                    <input @input="search" v-model="params.search" type="text"
                        class="outline-none px-4 w-full bg-transparent text-gray-700 placeholder-gray-400 font-medium"
                        :placeholder="$t('Search billing logs')">
                    <button v-if="isSearching === false && params.search" @click="clearSearch" type="button"
                        class="pr-4 text-gray-400 hover:text-red-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm3.7 12.3c.4.4.4 1 0 1.4c-.4.4-1 .4-1.4 0L12 13.4l-2.3 2.3c-.4.4-1 .4-1.4 0c-.4-.4-.4-1 0-1.4l2.3-2.3l-2.3-2.3c-.4-.4-.4-1 0-1.4c.4-.4 1-.4 1.4 0l2.3 2.3l2.3-2.3c.4-.4 1-.4 1.4 0c.4.4.4 1 0 1.4L13.4 12l2.3 2.3z" />
                        </svg>
                    </button>
                    <span v-if="isSearching" class="pr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            class="text-[#ff5100]">
                            <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                                <animateTransform attributeName="transform" calcMode="discrete" dur="2.4s"
                                    repeatCount="indefinite" type="rotate"
                                    values="0 12 12;90 12 12;180 12 12;270 12 12" />
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

        <!-- Table Container -->
        <div class="bg-white rounded-3xl shadow-md border-2 border-primary/10 p-4 overflow-hidden">
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <!-- Table Header -->
                    <thead class="border-b border-primary/10">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">

                                    <span>{{ $t('Date') }}</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">

                                    <span>{{ $t('Organization') }}</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">

                                    <span>{{ $t('Description') }}</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">

                                    <span>{{ $t('Amount') }}</span>
                                </div>
                            </th>
                        </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody class="divide-y divide-primary/10">
                        <tr v-for="(item, index) in rows.data" :key="index"
                            class="hover:bg-orange-50/30 transition-all duration-200 group">
                            <!-- Date -->
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="hidden sm:block">
                                        <div class="text-sm font-semibold text-gray-900">{{ formatDate(item.created_at)
                                            }}</div>
                                        <div class="text-xs text-gray-500">{{ formatTime(item.created_at) }}</div>
                                    </div>
                                    <div class="sm:hidden">
                                        <div class="text-sm font-semibold text-gray-900">{{ item.created_at }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Organization -->
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center space-x-3">

                                    <div>
                                        <div class="text-sm font-medium text-gray-900 capitalize">
                                            {{ item.organization.name }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Description -->
                            <td class="px-6 py-5">
                                <div class="flex items-start space-x-2">
                                    <span
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 capitalize border border-blue-100">
                                        {{ item.description }}
                                    </span>
                                </div>
                            </td>

                            <!-- Amount -->
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <div class="flex items-center space-x-1">

                                        <span class="text-base font-bold text-gray-900">{{ item.amount }}</span>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Empty State -->
                        <tr v-if="rows.data.length === 0">
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                            viewBox="0 0 24 24" class="text-gray-400">
                                            <path fill="currentColor"
                                                d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z" />
                                        </svg>
                                    </div>
                                    <div class="text-center">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">No billing records found
                                        </h3>
                                        <p class="text-sm text-gray-500">Your billing history will appear here</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <Pagination class="mt-3" :pagination="rows.meta" />
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import debounce from 'lodash/debounce';
import { router } from '@inertiajs/vue3';
import Pagination from '../Pagination.vue';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object
    },
    uuid: {
        type: String,
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
    router.visit(window.location.pathname, {
        method: 'get',
        data: params.value,
    })
}

// const goToPage = (url) => {
//     if (url) {
//         router.visit(url);
//     }
// }

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
}

const formatTime = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
}

const emit = defineEmits(['update:modelValue', 'callback']);
</script>