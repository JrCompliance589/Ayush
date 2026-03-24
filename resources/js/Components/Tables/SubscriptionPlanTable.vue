<!-- <script setup>
import { ref } from 'vue';
import debounce from 'lodash/debounce';
import { router } from '@inertiajs/vue3';
import { useForm } from "@inertiajs/vue3";
import AlertModal from '@/Components/AlertModal.vue';
import { useAlertModal } from '@/Composables/useAlertModal';
import 'vue3-toastify/dist/index.css';
import Table from '@/Components/Table.vue';
import TableHeader from '@/Components/TableHeader.vue';
import TableHeaderRow from '@/Components/TableHeaderRow.vue';
import TableHeaderRowItem from '@/Components/TableHeaderRowItem.vue';
import TableBody from '@/Components/TableBody.vue';
import TableBodyRow from '@/Components/TableBodyRow.vue';
import TableBodyRowItem from '@/Components/TableBodyRowItem.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
import DropdownItem from '@/Components/DropdownItem.vue';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object
    }
});

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();

const form = useForm({ 'test': null });

const deleteAction = (key) => {
    form.delete('/admin/plans/' + key);
}

const isLastRow = (index) => {
    return index === props.rows.data.length - 1;
}

const params = ref({
    search: props.filters.search,
});

const isSearching = ref(false);
const emit = defineEmits(['delete']);

function deleteItem(id) {
    emit('delete', id);
}

const clearSearch = () => {
    params.value.search = null;
    runSearch();
}

const search = debounce(() => {
    isSearching.value = true;
    runSearch();
}, 1000);

const runSearch = () => {
    router.visit('/admin/plans', {
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
            :placeholder="$t('Search plans')">
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
                <TableHeaderRowItem class="hidden sm:table-cell">{{ $t('Price') }}</TableHeaderRowItem>
                <TableHeaderRowItem class="hidden sm:table-cell">{{ $t('Period') }}</TableHeaderRowItem>
                <TableHeaderRowItem>
                    <span class="float-right">{{ $t('Status') }}</span>
                </TableHeaderRowItem>
                <TableHeaderRowItem class="hidden sm:table-cell">
                    <span class="float-right">{{ $t('Last updated') }}</span>
                </TableHeaderRowItem>
                <TableHeaderRowItem :position="'last'"></TableHeaderRowItem>
            </TableHeaderRow>
        </TableHeader>
        <TableBody>
            <TableBodyRow v-for="(item, index) in rows.data" :key="index" :class="!isLastRow(index) ? 'border-b' : ''">
                <TableBodyRowItem :position="'first'" class="capitalize">{{ item.name }}</TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">{{ item.price }}</TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">{{ $t(item.period) }}</TableBodyRowItem>
                <TableBodyRowItem class="capitalize">
                    <span class="py-1 float-right rounded-[5px] text-xs px-3 bg-[#ddebf7] text-slate-700">{{
                        $t(item.status) }}</span>
                </TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">
                    <span class="float-right">{{ item.updated_at }}</span>
                </TableBodyRowItem>
                <TableBodyRowItem :position="'last'">
                    <Dropdown :align="'right'" class="mt-2">
                        <button
                            class="inline-flex w-full justify-center rounded-md text-sm font-medium text-black hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75">
                            <span class="hover:bg-[#F6F7F9] hover:rounded-full w-[fit-content] p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M12 16a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2Z" />
                                </svg>
                            </span>
                        </button>
                        <template #items>
                            <DropdownItemGroup>
                                <DropdownItem :href="'/admin/plans/' + item.uuid">{{ $t('View/edit') }}</DropdownItem>
                                <DropdownItem as="button" @click="openAlert(item.uuid)">{{ $t('Delete') }}
                                </DropdownItem>
                            </DropdownItemGroup>
                        </template>
                    </Dropdown>
                </TableBodyRowItem>
            </TableBodyRow>
        </TableBody>
    </Table>

    <AlertModal v-model="isOpenAlert" @confirm="() => confirmAlert(deleteAction)" :label="$t('Delete row')"
        :description="$t('Are you sure you want to delete this row? This action can not be undone')" />
</template> -->


<!-- ============================================= NEW UI CODE ============================================= -->


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
                        :placeholder="$t('Search plans')">
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
        <div class="bg-white rounded-3xl shadow-md border-2 border-primary/10 p-4">
            <!-- Table -->
            <div class="">
                <table class="w-full">
                    <!-- Table Header -->
                    <thead class="border-b border-primary/10">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <span>{{ $t('Name') }}</span>
                                </div>
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden sm:table-cell">
                                <div class="flex items-center space-x-2">
                                    <span>{{ $t('Price') }}</span>
                                </div>
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden md:table-cell">
                                <div class="flex items-center space-x-2">
                                    <span>{{ $t('Period') }}</span>
                                </div>
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden lg:table-cell">
                                <div class="flex items-center space-x-2">
                                    <span>{{ $t('Status') }}</span>
                                </div>
                            </th>
                            <th
                                class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider hidden xl:table-cell">
                                <div class="flex items-center justify-end space-x-2">
                                    <span>{{ $t('Last updated') }}</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <span>{{ $t('Actions') }}</span>
                            </th>
                        </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody class="divide-y divide-primary/10">
                        <tr v-for="(item, index) in rows.data" :key="index"
                            class="hover:bg-orange-50/30 transition-all duration-200 group">
                            <!-- Name -->
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-[#ff5100] to-[#ff7733] rounded-xl flex items-center justify-center shadow-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" class="text-white">
                                            <path fill="currentColor"
                                                d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 capitalize">
                                            {{ item.name }}
                                        </div>
                                        <div class="text-xs text-gray-500 sm:hidden">
                                            {{ item.price }} • {{ $t(item.period) }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Price -->
                            <td class="px-6 py-5 whitespace-nowrap hidden sm:table-cell">
                                <div class="flex items-center space-x-2">
                                    <div
                                        class="flex items-center justify-center w-8 h-8 bg-green-50 rounded-lg border border-green-100">
                                        <IndianRupee class="text-green-600" />
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ item.price }}</span>
                                </div>
                            </td>

                            <!-- Period -->
                            <td class="px-6 py-5 whitespace-nowrap hidden md:table-cell">
                                <div class="flex items-start">
                                    <span
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-purple-50 text-purple-700 capitalize border border-purple-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" class="mr-1.5">
                                            <path fill="currentColor"
                                                d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm4.2 14.2L11 13V7h1.5v5.2l4.5 2.7l-.8 1.3z" />
                                        </svg>
                                        {{ $t(item.period) }}
                                    </span>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-5 whitespace-nowrap hidden lg:table-cell">
                                <div class="flex items-start">
                                    <span :class="[
                                        'inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium capitalize border',
                                        item.status === 'active'
                                            ? 'bg-green-50 text-green-700 border-green-100'
                                            : 'bg-gray-50 text-gray-700 border-gray-100'
                                    ]">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" class="mr-1.5">
                                            <path fill="currentColor"
                                                d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                        </svg>
                                        {{ $t(item.status) }}
                                    </span>
                                </div>
                            </td>

                            <!-- Last Updated -->
                            <td class="px-6 py-5 whitespace-nowrap hidden xl:table-cell">
                                <div class="text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" class="text-gray-400">
                                            <path fill="currentColor"
                                                d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-900">{{ item.updated_at }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center justify-center">
                                    <Dropdown :align="'right'">
                                        <button
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-gray-50 hover:bg-orange-50 text-gray-600 hover:text-[#ff5100] transition-all duration-200 border border-transparent hover:border-orange-100 group">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                    d="M12 16a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2Z" />
                                            </svg>
                                        </button>
                                        <template #items>
                                            <DropdownItemGroup>
                                                <DropdownItem :href="'/admin/plans/' + item.uuid">
                                                    {{ $t('View/edit') }}
                                                </DropdownItem>
                                                <DropdownItem as="button" @click="openAlert(item.uuid)">
                                                    {{ $t('Delete') }}
                                                </DropdownItem>
                                            </DropdownItemGroup>
                                        </template>
                                    </Dropdown>
                                </div>
                            </td>
                        </tr>

                        <!-- Empty State -->
                        <tr v-if="rows.data.length === 0">
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div
                                        class="w-20 h-20 bg-gradient-to-br from-orange-50 to-orange-100 rounded-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                            viewBox="0 0 24 24" class="text-[#ff5100]">
                                            <path fill="currentColor"
                                                d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" />
                                        </svg>
                                    </div>
                                    <div class="text-center">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">No subscription plans found
                                        </h3>
                                        <p class="text-sm text-gray-500">Your subscription plans will appear here</p>
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

    <!-- Alert Modal Component-->
    <AlertModal v-model="isOpenAlert" @confirm="() => confirmAlert(deleteAction)" :label="$t('Delete row')"
        :description="$t('Are you sure you want to delete this row? This action can not be undone')" />
</template>

<script setup>
import { ref } from 'vue';
import debounce from 'lodash/debounce';
import { router } from '@inertiajs/vue3';
import { useForm } from "@inertiajs/vue3";
import AlertModal from '@/Components/AlertModal.vue';
import { useAlertModal } from '@/Composables/useAlertModal';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
import DropdownItem from '@/Components/DropdownItem.vue';
import Pagination from '@/Components/Pagination.vue';
import { IndianRupee } from 'lucide-vue-next';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object
    }
});

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();

const form = useForm({ 'test': null });

const deleteAction = (key) => {
    form.delete('/admin/plans/' + key);
}

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
    router.visit('/admin/plans', {
        method: 'get',
        data: params.value,
    })
}
</script>