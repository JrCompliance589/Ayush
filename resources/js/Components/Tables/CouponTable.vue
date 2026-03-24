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

    const props = defineProps({
        rows: {
            type: Array,
            required: true,
        },
    });

    const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();

    const emit = defineEmits(['edit', 'delete']);

    const form = useForm({'test': null});

    function edit(id) {
        emit('edit', id);
    }

    function deleteItem(id) {
        emit('delete', id);
    }

    const deleteAction = (key) => {
        form.delete('/admin/coupons/' + key);
    }
</script>
<template>
    <Table :rows="rows">
        <TableHeader>
            <TableHeaderRow>
                <TableHeaderRowItem :position="'first'">{{ $t('Name') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Coupon code') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Percentage') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Quantity') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Status') }}</TableHeaderRowItem>
                <TableHeaderRowItem :position="'last'"></TableHeaderRowItem>
            </TableHeaderRow>
        </TableHeader>
        <TableBody>
            <TableBodyRow v-for="(item, index) in rows.data" :key="index">
                <TableBodyRowItem :position="'first'">{{ item.name }}</TableBodyRowItem>
                <TableBodyRowItem>{{ item.code }}</TableBodyRowItem>
                <TableBodyRowItem>{{ item.percentage }}%</TableBodyRowItem>
                <TableBodyRowItem>{{ item.quantity - item.quantity_redeemed }}</TableBodyRowItem>
                <TableBodyRowItem>{{ $t(item.status) }}</TableBodyRowItem>
                <TableBodyRowItem :position="'last'">
                    <Dropdown :align="'right'" class="mt-2">
                    <button
                        class="inline-flex w-full justify-center rounded-md text-sm font-medium text-black hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75"
                    >
                        <span class="hover:bg-[#F6F7F9] hover:rounded-full w-[fit-content] p-2">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                        >
                            <path
                            fill="currentColor"
                            d="M12 16a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2Z"
                            />
                        </svg>
                        </span>
                    </button>
                    <template #items>
                        <DropdownItemGroup>
                            <DropdownItem as="button" @click="edit(item.id)">{{ $t('Edit') }}</DropdownItem>
                            <DropdownItem as="button" @click="openAlert(item.id)">{{ $t('Delete') }}</DropdownItem>
                        </DropdownItemGroup>
                    </template>
                    </Dropdown>
                </TableBodyRowItem>
            </TableBodyRow>
        </TableBody>
    </Table>

    <AlertModal 
        v-model="isOpenAlert" 
        @confirm="() => confirmAlert(deleteAction)"
        :label = "$t('Delete row')" 
        :description = "$t('Are you sure you want to delete this row? This action can not be undone')"
    />
</template>
   -->


<!-- ============================================ NEW UI CODE ============================================= -->


<template>
    <!-- Table -->
    <div class="">
        <div class="">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-50 to-orange-50/20">
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">
                            {{ $t('Name') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">
                            {{ $t('Coupon code') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">
                            {{ $t('Percentage') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">
                            {{ $t('Quantity') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">
                            {{ $t('Status') }}
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
                                            d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58s1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41s-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4S7 4.67 7 5.5S6.33 7 5.5 7z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-slate-900">{{ item.name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-purple-50 to-purple-100/50 rounded-lg border border-purple-200">
                                <span class="text-sm font-mono font-bold text-purple-700">{{ item.code }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-blue-50 to-blue-100/50 rounded-lg border border-blue-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    class="text-blue-600">
                                    <path fill="currentColor" d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                                </svg>
                                <span class="text-sm font-bold text-blue-700">{{ item.percentage }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-amber-50 to-amber-100/50 rounded-lg border border-amber-200">

                                <span class="text-sm font-bold text-amber-700">{{ item.quantity - item.quantity_redeemed
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
                        <td class="px-6 py-4 whitespace-nowrap text-right">
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
                                        <DropdownItem as="button" @click="edit(item.id)">{{ $t('Edit') }}</DropdownItem>
                                        <DropdownItem as="button" @click="openAlert(item.id)">{{ $t('Delete') }}
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

const props = defineProps({
    rows: {
        type: Array,
        required: true,
    },
});

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
    form.delete('/admin/coupons/' + key);
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