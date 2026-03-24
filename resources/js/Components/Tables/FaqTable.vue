<!-- <script setup>
import { ref } from 'vue';
import debounce from 'lodash/debounce';
import { router, useForm } from '@inertiajs/vue3';
import AlertModal from '@/Components/AlertModal.vue';
import { useAlertModal } from '@/Composables/useAlertModal';
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
    },
});

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();

const emit = defineEmits(['edit', 'delete']);

const form = useForm({ 'test': null });

function deleteItem(id) {
    emit('delete', id);
}

const deleteAction = (key) => {
    form.delete('/admin/faqs/' + key);
}

const isLastRow = (index) => {
    return index === props.rows.data.length - 1;
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
            :placeholder="$t('Search FAQs')">
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
                <TableHeaderRowItem :position="'first'">{{ $t('Question') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Answer') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Status') }}</TableHeaderRowItem>
                <TableHeaderRowItem :position="'last'"></TableHeaderRowItem>
            </TableHeaderRow>
        </TableHeader>
        <TableBody>
            <TableBodyRow v-for="(item, index) in rows.data" :key="index" :class="!isLastRow(index) ? 'border-b' : ''">
                <TableBodyRowItem :position="'first'">{{ item.question }}</TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">
                    <div class="w-[20em] truncate">
                        {{ item.answer }}
                    </div>
                </TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">{{ item.status === 1 ? $t('Displayed') : $t('Hidden') }}
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
                                <DropdownItem :href="'/admin/faqs/' + item.id">{{ $t('View/edit') }}</DropdownItem>
                                <DropdownItem as="button" @click="openAlert(item.id)">{{ $t('Delete') }}</DropdownItem>
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


<!-- ========================================= NEW UI CODE ==================================== -->

<script setup>
import { ref } from 'vue';
import debounce from 'lodash/debounce';
import { router, useForm } from '@inertiajs/vue3';
import AlertModal from '@/Components/AlertModal.vue';
import { useAlertModal } from '@/Composables/useAlertModal';
import Pagination from '../Pagination.vue';
import Dropdown from '../Dropdown.vue';
import DropdownItemGroup from '../DropdownItemGroup.vue';
import DropdownItem from '../DropdownItem.vue';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object
    },
});

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();

const emit = defineEmits(['edit', 'delete']);

const form = useForm({ 'test': null });

function deleteItem(id) {
    emit('delete', id);
}

const deleteAction = (key) => {
    form.delete('/admin/faqs/' + key);
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
    const url = window.location.pathname;

    router.visit(url, {
        method: 'get',
        data: params.value,
    })
}

const getStatusColor = (status) => {
    return status === 1
        ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
        : 'bg-gray-50 text-gray-700 border-gray-100';
};

const getStatusLabel = (status) => {
    return status === 1 ? 'Displayed' : 'Hidden';
};

// Dropdown State
const openDropdownId = ref(null);

const toggleDropdown = (id) => {
    openDropdownId.value = openDropdownId.value === id ? null : id;
};
</script>

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
                        :placeholder="$t('Search FAQs')">
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
        <div class="bg-white rounded-3xl shadow-md border-2 border-orange-100 p-4">
            <!-- Table -->
            <div class="">
                <table class="w-full">
                    <!-- Table Header -->
                    <thead class="border-b border-orange-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <span>{{ $t('Question') }}</span>
                                </div>
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden sm:table-cell">
                                <div class="flex items-center space-x-2">
                                    <span>{{ $t('Answer') }}</span>
                                </div>
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden sm:table-cell">
                                <div class="flex items-center space-x-2">
                                    <span>{{ $t('Status') }}</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <span>{{ $t('Actions') }}</span>
                            </th>
                        </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody class="divide-y divide-orange-100">
                        <tr v-for="(item, index) in rows.data" :key="index"
                            class="hover:bg-orange-50/30 transition-all duration-200 group">
                            <!-- Question -->
                            <td class="px-6 py-5">
                                <div class="flex items-start space-x-3">

                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-semibold text-gray-900 leading-relaxed">
                                            {{ item.question }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Answer -->
                            <td class="px-6 py-5 hidden sm:table-cell">
                                <div class="max-w-md">
                                    <p class="text-sm text-gray-600 line-clamp-2 leading-relaxed">
                                        {{ item.answer }}
                                    </p>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-5 whitespace-nowrap hidden sm:table-cell">
                                <span
                                    :class="['inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium border', getStatusColor(item.status)]">
                                    <span class="w-2 h-2 rounded-full mr-2"
                                        :class="item.status === 1 ? 'bg-emerald-500' : 'bg-gray-400'"></span>
                                    {{ $t(getStatusLabel(item.status)) }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-5 whitespace-nowrap text-right">
                                <Dropdown :align="'right'" class="mt-2">
                                    <button
                                        class="inline-flex w-full justify-center rounded-md text-sm font-medium text-black hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75">
                                        <span class="hover:bg-[#F6F7F9] hover:rounded-full w-[fit-content] p-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                    d="M12 16a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2Z" />
                                            </svg>
                                        </span>
                                    </button>
                                    <template #items>
                                        <DropdownItemGroup>
                                            <DropdownItem :href="'/admin/faqs/' + item.id">{{ $t('View/edit') }}
                                            </DropdownItem>
                                            <DropdownItem as="button" @click="openAlert(item.id)">{{ $t('Delete') }}
                                            </DropdownItem>
                                        </DropdownItemGroup>
                                    </template>
                                </Dropdown>
                            </td>
                        </tr>

                        <!-- Empty State -->
                        <tr v-if="rows.data.length === 0">
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                            viewBox="0 0 24 24" class="text-gray-400">
                                            <path fill="currentColor"
                                                d="M12 4C9.243 4 7 6.243 7 9h2c0-1.654 1.346-3 3-3s3 1.346 3 3c0 1.069-.454 1.465-1.481 2.255c-.382.294-.813.626-1.226 1.038C10.981 13.604 10.995 14.897 11 15v2h2v-2.009c0-.024.023-.601.707-1.284c.32-.32.682-.598 1.031-.867C15.798 12.024 17 11.1 17 9c0-2.757-2.243-5-5-5zm-1 14h2v2h-2z" />
                                        </svg>
                                    </div>
                                    <div class="text-center">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                            {{ $t('No FAQs found') }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            {{ $t('Your FAQs will appear here') }}
                                        </p>
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