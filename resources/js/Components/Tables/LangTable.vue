<!-- <script setup>
import AlertModal from '@/Components/AlertModal.vue';
import { useForm } from "@inertiajs/vue3";
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

const props = defineProps(['rows', 'defaultLanguage']);

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();

const emit = defineEmits(['edit', 'delete']);

const form = useForm({ 'test': null });

function edit(id) {
    emit('edit', id);
}

function deleteItem(id) {
    emit('delete', id);
}

const deleteAction = (key) => {
    form.delete('/admin/languages/' + key);
}

const isLastRow = (index) => {
    return index === props.rows.data.length - 1;
}

const capitalizeFirstLetter = (str) => {
    return str.charAt(0).toUpperCase() + str.slice(1);
};
</script>
<template>
    <Table :rows="rows">
        <TableHeader>
            <TableHeaderRow>
                <TableHeaderRowItem :position="'first'">{{ $t('Name') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Code') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Status') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Is RTL') }}</TableHeaderRowItem>
                <TableHeaderRowItem :position="'last'"></TableHeaderRowItem>
            </TableHeaderRow>
        </TableHeader>
        <TableBody>
            <TableBodyRow v-for="(item, index) in rows.data" :key="index" :class="!isLastRow(index) ? 'border-b' : ''">
                <TableBodyRowItem :position="'first'">{{ item.name }}</TableBodyRowItem>
                <TableBodyRowItem>{{ item.code }}</TableBodyRowItem>
                <TableBodyRowItem>{{ $t(capitalizeFirstLetter(item.status)) }}</TableBodyRowItem>
                <TableBodyRowItem>{{ item.is_rtl == 1 ? $t('Yes') : $t('No') }}</TableBodyRowItem>
                <TableBodyRowItem>
                    <span v-if="props.defaultLanguage == item.code"
                        class="bg-slate-50 p-1 rounded-md text-[12px]">Default Language</span>
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
                                <DropdownItem v-if="props.defaultLanguage != item.code"
                                    :href="'/admin/languages/' + item.code + '/default'">{{ $t('Set as default') }}
                                </DropdownItem>
                                <DropdownItem :href="'/admin/languages/' + item.code + '/translations'">{{
                                    $t('Translate') }}</DropdownItem>
                                <DropdownItem as="button" @click="edit(item.id)">{{ $t('Edit') }}</DropdownItem>
                                <DropdownItem as="button" @click="openAlert(item.id)" v-if="item.code != 'en'">{{
                                    $t('Delete') }}</DropdownItem>
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


<!-- ========================================== NEW UI CODE ==================================== -->


<template>
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
                            {{ $t('Code') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">
                            {{ $t('Status') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">
                            {{ $t('Is RTL') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">
                            {{ $t('Default') }}
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
                            <div class="flex items-center gap-3">
                                <div
                                    class="p-2 bg-gradient-to-br from-slate-100 to-slate-50 rounded-lg group-hover:from-[#ff5100]/10 group-hover:to-[#ff7340]/5 transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        class="text-[#ff5100] transition-colors">
                                        <path fill="currentColor"
                                            d="m12.87 15.07l-2.54-2.51l.03-.03A17.52 17.52 0 0 0 14.07 6H17V4h-7V2H8v2H1v2h11.17C11.5 7.92 10.44 9.75 9 11.35C8.07 10.32 7.3 9.19 6.69 8h-2c.73 1.63 1.73 3.17 2.98 4.56l-5.09 5.02L4 19l5-5l3.11 3.11l.76-2.04M18.5 10h-2L12 22h2l1.12-3h4.75L21 22h2l-4.5-12m-2.62 7l1.62-4.33L19.12 17h-3.24Z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-slate-900">{{ item.name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-indigo-50 to-indigo-100/50 rounded-lg border border-indigo-200">

                                <span class="text-sm font-mono font-bold text-indigo-700 uppercase">{{ item.code
                                }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span v-if="item.status === 'active'"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 border border-green-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2m-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9Z" />
                                </svg>
                                {{ $t('Active') }}
                            </span>
                            <span v-else
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-slate-50 to-slate-100 text-slate-600 border border-slate-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8s8 3.58 8 8s-3.58 8-8 8z" />
                                </svg>
                                {{ $t('Inactive') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span v-if="item.is_rtl == 1"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-blue-50 to-blue-100/50 text-blue-700 border border-blue-200">

                                {{ $t('Yes') }}
                            </span>
                            <span v-else
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-slate-50 to-slate-100 text-slate-600 border border-slate-200">

                                {{ $t('No') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span v-if="props.defaultLanguage == item.code"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-[#ff5100]/10 to-[#ff7340]/10 text-[#ff5100] border border-[#ff5100]/30">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2L9.19 8.63L2 9.24l5.46 4.73L5.82 21L12 17.27z" />
                                </svg>
                                {{ $t('Default') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <Dropdown :align="'right'">
                                <button
                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg hover:bg-slate-100 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-slate-600">
                                        <path fill="currentColor"
                                            d="M12 16a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2Z" />
                                    </svg>
                                </button>
                                <template #items>
                                    <DropdownItemGroup>
                                        <DropdownItem v-if="props.defaultLanguage != item.code"
                                            :href="'/admin/languages/' + item.code + '/default'">
                                            <div class="flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24">
                                                    <path fill="currentColor"
                                                        d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2L9.19 8.63L2 9.24l5.46 4.73L5.82 21L12 17.27z" />
                                                </svg>
                                                {{ $t('Set as default') }}
                                            </div>
                                        </DropdownItem>
                                        <DropdownItem :href="'/admin/languages/' + item.code + '/translations'">
                                            <div class="flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24">
                                                    <path fill="currentColor"
                                                        d="m12.87 15.07l-2.54-2.51l.03-.03A17.52 17.52 0 0 0 14.07 6H17V4h-7V2H8v2H1v2h11.17C11.5 7.92 10.44 9.75 9 11.35C8.07 10.32 7.3 9.19 6.69 8h-2c.73 1.63 1.73 3.17 2.98 4.56l-5.09 5.02L4 19l5-5l3.11 3.11l.76-2.04M18.5 10h-2L12 22h2l1.12-3h4.75L21 22h2l-4.5-12m-2.62 7l1.62-4.33L19.12 17h-3.24Z" />
                                                </svg>
                                                {{ $t('Translate') }}
                                            </div>
                                        </DropdownItem>
                                        <DropdownItem as="button" @click="edit(item.id)">
                                            <div class="flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24">
                                                    <path fill="currentColor"
                                                        d="M5 19h1.4l8.625-8.625l-1.4-1.4L5 17.6V19ZM19.3 8.925l-4.25-4.2l1.4-1.4q.575-.575 1.413-.575t1.412.575l1.4 1.4q.575.575.6 1.388t-.55 1.387L19.3 8.925ZM17.85 10.4L7.25 21H3v-4.25l10.6-10.6l4.25 4.25Zm-3.525-.725l-.7-.7l1.4 1.4l-.7-.7Z" />
                                                </svg>
                                                {{ $t('Edit') }}
                                            </div>
                                        </DropdownItem>
                                        <DropdownItem as="button" @click="openAlert(item.id)" v-if="item.code != 'en'">
                                            <div class="flex items-center gap-2 text-red-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24">
                                                    <path fill="currentColor"
                                                        d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6zM8 9h8v10H8zm7.5-5l-1-1h-5l-1 1H5v2h14V4z" />
                                                </svg>
                                                {{ $t('Delete') }}
                                            </div>
                                        </DropdownItem>
                                    </DropdownItemGroup>
                                </template>
                            </Dropdown>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="rows.links && rows.links.length > 3"
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
        </div>
    </div>

    <!-- Alert Modal Component-->
    <AlertModal v-model="isOpenAlert" @confirm="() => confirmAlert(deleteAction)" :label="$t('Delete row')"
        :description="$t('Are you sure you want to delete this row? This action can not be undone')" />
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import AlertModal from '@/Components/AlertModal.vue';
import { useForm } from "@inertiajs/vue3";
import { useAlertModal } from '@/Composables/useAlertModal';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
import DropdownItem from '@/Components/DropdownItem.vue';

const props = defineProps(['rows', 'defaultLanguage']);

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();

const emit = defineEmits(['edit', 'delete']);

const form = useForm({ 'test': null });

function edit(id) {
    emit('edit', id);
}

function deleteItem(id) {
    emit('delete', id);
}

const deleteAction = (key) => {
    form.delete('/admin/languages/' + key);
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