<!-- <script setup>
import { ref, computed } from 'vue';
import debounce from 'lodash/debounce';
import { router, useForm, usePage } from "@inertiajs/vue3";
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
    }
});

const isOpenStatusModal = ref(false)
const selectedStatusId = ref(null)

function openStatusModal(id) {
    selectedStatusId.value = id
    isOpenStatusModal.value = true
}

function confirmStatusChange() {
    if (selectedStatusId.value) {
        console.log(selectedStatusId.value);

        form.put('/team/' + selectedStatusId.value + '/status')
    }
    isOpenStatusModal.value = false
}

const user = computed(() => usePage().props.auth.user);

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();

const form = useForm({ 'test': null });

const deleteAction = (key) => {
    form.delete('/team/' + key);
}

const statusAction = (key) => form.patch('/team/' + key + '/status');

const isLastRow = (index) => {
    return index === props.rows.data.length - 1;
}

const params = ref({
    search: props.filters?.search,
});

const isSearching = ref(false);
const emit = defineEmits(['edit', 'delete']);

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
    router.visit('/team', {
        method: 'get',
        data: params.value,
    })
}

function edit(id, role, email) {
    emit('edit', { id: id, role: role, email: email });
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
            :placeholder="$t('Search team')">
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
                <TableHeaderRowItem>{{ $t('Email') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Role') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Status') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Last updated') }}</TableHeaderRowItem>
                <TableHeaderRowItem v-if="user.teams[0].role === 'owner'" :position="'last'"></TableHeaderRowItem>
            </TableHeaderRow>
        </TableHeader>
        <TableBody>
            <TableBodyRow v-for="(item, index) in rows.data" :key="index" :class="!isLastRow(index) ? 'border-b' : ''">
                <TableBodyRowItem :position="'first'" class="capitalize">{{ item.user.first_name + ' ' +
                    item.user.last_name }}</TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">{{ item.user.email }}</TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">{{ $t(item.role) }}</TableBodyRowItem>
                <TableBodyRowItem class="capitalize hidden sm:table-cell">
                    <span class="py-1 rounded-[5px] text-xs px-3 bg-[#ddebf7] text-slate-700">{{ $t(item.status)
                    }}</span>
                </TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">{{ item.updated_at }}</TableBodyRowItem>
                <TableBodyRowItem v-if="user.teams[0].role === 'owner'" :position="'last'">
                    <Dropdown v-if="item.role != 'owner'" :align="'right'" class="mt-2">
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
                                <DropdownItem as="button" @click="edit(item.uuid, item.role, item.user.email)">{{
                                    $t('Edit') }}</DropdownItem>
                                <DropdownItem as="button" @click="openAlert(item.uuid)">{{ $t('Remove user') }}
                                </DropdownItem>
                                <DropdownItem as="button" @click="openStatusModal(item.uuid)">{{ $t('Change status') }}
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

    <div v-if="isOpenStatusModal" class="fixed inset-0 flex items-center justify-center z-50">
        <div class="fixed inset-0 bg-black opacity-50" @click="isOpenStatusModal = false"></div>

        <div class="bg-white rounded-lg shadow-lg p-6 z-10 w-full max-w-md">
            <h2 class="text-lg font-semibold mb-2">{{ $t('Change Status') }}</h2>
            <p class="text-gray-600 mb-4">{{ $t('Are you sure you want to change status?') }}</p>
            <div class="flex justify-end gap-3">
                <button @click="isOpenStatusModal = false" class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300">
                    {{ $t('Cancel') }}
                </button>
                <button @click="confirmStatusChange"
                    class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-500">
                    {{ $t('Confirm') }}
                </button>
            </div>
        </div>
    </div>

</template> -->


<!-- ========================================== NEW UI CODE ==================================== -->


<script setup>
import { ref, computed } from 'vue';
import debounce from 'lodash/debounce';
import { router, useForm, usePage } from "@inertiajs/vue3";
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
import { UserCircle, Mail, Shield, Calendar, Edit, Trash2, MoreVertical } from 'lucide-vue-next';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object
    }
});

const isOpenStatusModal = ref(false)
const selectedStatusId = ref(null)

function openStatusModal(id) {
    selectedStatusId.value = id
    isOpenStatusModal.value = true
}

function confirmStatusChange() {
    if (selectedStatusId.value) {
        console.log(selectedStatusId.value);
        form.put('/team/' + selectedStatusId.value + '/status')
    }
    isOpenStatusModal.value = false
}

const user = computed(() => usePage().props.auth.user);

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();

const form = useForm({ 'test': null });

const deleteAction = (key) => {
    form.delete('/team/' + key);
}

const statusAction = (key) => form.patch('/team/' + key + '/status');

const isLastRow = (index) => {
    return index === props.rows.data.length - 1;
}

const params = ref({
    search: props.filters?.search,
});

const isSearching = ref(false);
const emit = defineEmits(['edit', 'delete', 'permissions']);

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
    router.visit('/team', {
        method: 'get',
        data: params.value,
    })
}

function edit(id, role, email) {
    emit('edit', { id: id, role: role, email: email });
}

function managePermissions(item) {
    emit('permissions', {
        uuid: item.uuid,
        name: item.user.first_name + ' ' + item.user.last_name,
        role: item.role,
        permissions: item.permissions,
    });
}

const capitalizeFirstLetter = (string) => {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

const getStatusConfig = (status) => {
    const configs = {
        'active': {
            bg: 'bg-gradient-to-r from-emerald-500 to-teal-500',
            text: 'text-white'
        },
        'inactive': {
            bg: 'bg-gradient-to-r from-red-500 to-rose-500',
            text: 'text-white'
        },
        'pending': {
            bg: 'bg-gradient-to-r from-amber-500 to-orange-500',
            text: 'text-white'
        },
        'default': {
            bg: 'bg-gradient-to-r from-slate-500 to-gray-500',
            text: 'text-white'
        }
    };
    return configs[status?.toLowerCase()] || configs.default;
}

const getRoleColor = (role) => {
    const colors = {
        'owner': 'bg-purple-100 text-purple-700',
        'manager': 'bg-blue-100 text-blue-700',
        'agent': 'bg-green-100 text-green-700',
    };
    return colors[role?.toLowerCase()] || 'bg-gray-100 text-gray-700';
}
</script>

<template>
    <div class="min-h-screen">
        <!-- Enhanced Search Bar -->
        <div class="mb-8 max-w-screen-sm">
            <div
                class="bg-white rounded-2xl shadow-lg border-2 border-primary/10 p-2 flex items-center gap-3 hover:shadow-xl transition-all duration-300">
                <div class="pl-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="text-primary/40">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                </div>
                <input @input="search" v-model="params.search" type="text"
                    class="flex-1 py-1.5 outline-none bg-transparent text-base font-medium text-gray-700 placeholder:text-gray-400"
                    :placeholder="$t('Search team members...')">
                <button v-if="isSearching === false && params.search" @click="clearSearch" type="button"
                    class="p-2 hover:bg-red-50 rounded-lg transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" class="text-red-500">
                        <circle cx="12" cy="12" r="10" />
                        <path d="m15 9-6 6" />
                        <path d="m9 9 6 6" />
                    </svg>
                </button>
                <span v-if="isSearching" class="pr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        class="animate-spin text-primary">
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

        <!-- Team Table -->
        <div v-if="rows.data.length > 0"
            class="bg-white rounded-3xl shadow-md border-2 border-primary/10 p-4 overflow-hidden">
            <div class="overflow-x-auto">
                <Table :rows="rows">
                    <TableHeader>
                        <TableHeaderRow>
                            <TableHeaderRowItem :position="'first'"
                                class="text-black font-bold uppercase text-xs tracking-wider py-5">
                                {{ $t('Name') }}
                            </TableHeaderRowItem>
                            <TableHeaderRowItem
                                class="hidden sm:table-cell text-black font-bold uppercase text-xs tracking-wider py-5">
                                {{ $t('Email') }}
                            </TableHeaderRowItem>
                            <TableHeaderRowItem
                                class="hidden sm:table-cell text-black font-bold uppercase text-xs tracking-wider py-5">
                                {{ $t('Role') }}
                            </TableHeaderRowItem>
                            <TableHeaderRowItem
                                class="hidden sm:table-cell text-black font-bold uppercase text-xs tracking-wider py-5">
                                {{ $t('Status') }}
                            </TableHeaderRowItem>
                            <TableHeaderRowItem
                                class="hidden sm:table-cell text-black font-bold uppercase text-xs tracking-wider py-5">
                                {{ $t('Last updated') }}
                            </TableHeaderRowItem>
                            <TableHeaderRowItem v-if="user.teams[0].role === 'owner'" :position="'last'">
                            </TableHeaderRowItem>
                        </TableHeaderRow>
                    </TableHeader>
                    <TableBody>
                        <TableBodyRow v-for="(item, index) in rows.data" :key="index"
                            :class="['group hover:bg-orange-50 transition-all duration-300', !isLastRow(index) ? 'border-b-2 border-primary/10' : '']">

                            <TableBodyRowItem :position="'first'" class="py-6">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary/10 to-orange-100 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <UserCircle class="w-6 h-6 text-primary" />
                                    </div>
                                    <span
                                        class="font-bold text-gray-900 group-hover:text-primary transition-colors duration-300 text-base capitalize">
                                        {{ item.user.first_name + ' ' + item.user.last_name }}
                                    </span>
                                </div>
                            </TableBodyRowItem>

                            <TableBodyRowItem class="hidden sm:table-cell py-6">
                                <div class="flex items-center gap-2 text-sm text-gray-600 font-medium">
                                    <Mail class="w-4 h-4 text-gray-400" />
                                    {{ item.user.email }}
                                </div>
                            </TableBodyRowItem>

                            <TableBodyRowItem class="hidden sm:table-cell py-6">
                                <span :class="getRoleColor(item.role)"
                                    class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium capitalize">
                                    <Shield class="w-3 h-3 mr-1" />
                                    {{ $t(item.role) }}
                                </span>
                            </TableBodyRowItem>

                            <TableBodyRowItem class="hidden sm:table-cell py-6">
                                <span :class="[
                                    'inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold capitalize shadow-md',
                                    getStatusConfig(item.status).bg,
                                    getStatusConfig(item.status).text
                                ]">
                                    <span class="w-2 h-2 rounded-full bg-white mr-2 animate-pulse"></span>
                                    {{ $t(item.status) }}
                                </span>
                            </TableBodyRowItem>

                            <TableBodyRowItem class="hidden sm:table-cell py-6">
                                <div class="flex items-center gap-2 text-sm text-gray-600 font-medium">
                                    <Calendar class="w-4 h-4 text-gray-400" />
                                    {{ item.updated_at }}
                                </div>
                            </TableBodyRowItem>

                            <TableBodyRowItem v-if="user.teams[0].role === 'owner'" :position="'last'" class="py-6">
                                <Dropdown v-if="item.role != 'owner'" :align="'right'">
                                    <button
                                        class="p-3 hover:bg-primary/10 rounded-xl transition-all duration-300 group/btn">
                                        <MoreVertical class="w-5 h-5 text-gray-400 group-hover/btn:text-primary" />
                                    </button>
                                    <template #items>
                                        <DropdownItemGroup class="bg-white min-w-max">
                                            <DropdownItem as="button"
                                                @click="edit(item.uuid, item.role, item.user.email)"
                                                class="flex items-center gap-2 text-nowrap min-w-max">
                                                <Edit class="w-4 h-4" />
                                                {{ $t('Edit') }}
                                            </DropdownItem>
                                            <DropdownItem as="button"
                                                @click="managePermissions(item)"
                                                class="flex items-center gap-2 text-nowrap min-w-max">
                                                <Shield class="w-4 h-4" />
                                                {{ $t('Manage Permissions') }}
                                            </DropdownItem>
                                            <DropdownItem as="button" @click="openAlert(item.uuid)"
                                                class="flex items-center gap-2 text-red-600 text-nowrap min-w-max">
                                                <Trash2 class="w-4 h-4" />
                                                {{ $t('Remove user') }}
                                            </DropdownItem>
                                            <DropdownItem as="button" @click="openStatusModal(item.uuid)"
                                                class="flex items-center gap-2 text-nowrap min-w-max">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path
                                                        d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2" />
                                                </svg>
                                                {{ $t('Change status') }}
                                            </DropdownItem>
                                        </DropdownItemGroup>
                                    </template>
                                </Dropdown>
                            </TableBodyRowItem>
                        </TableBodyRow>
                    </TableBody>
                </Table>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="rows.data.length == 0" class="mt-8">
            <div class="bg-white rounded-3xl shadow-2xl border-2 border-primary/10 p-16">
                <div class="max-w-lg mx-auto text-center">
                    <!-- Animated Illustration -->
                    <div class="mb-8 relative inline-block">
                        <div class="absolute inset-0 rounded-full blur-3xl opacity-30 animate-pulse">
                        </div>
                        <div class="relative">
                            <div
                                class="bg-gradient-to-br from-primary/10 via-orange-100 to-red-100 p-10 rounded-[2.5rem] inline-block">
                                <UserCircle class="w-20 h-20 text-primary" stroke-width="1.5" />
                            </div>
                        </div>
                    </div>

                    <h3 class="text-3xl font-bold text-gray-900 mb-4">
                        {{ $t('No team members yet') }}
                    </h3>
                    <p class="text-gray-600 text-lg mb-10 max-w-md mx-auto">
                        Start building your team by inviting members to collaborate with you
                    </p>
                </div>
            </div>
        </div>

        <!-- Alert Modal Component-->
        <AlertModal v-model="isOpenAlert" @confirm="() => confirmAlert(deleteAction)" :label="$t('Delete row')"
            :description="$t('Are you sure you want to delete this row? This action can not be undone')" />

        <!-- Status Change Modal -->
        <div v-if="isOpenStatusModal" class="fixed inset-0 flex items-center justify-center z-50">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="isOpenStatusModal = false"></div>

            <!-- Modal -->
            <div
                class="bg-white rounded-3xl shadow-2xl p-8 z-10 w-full max-w-md border-2 border-primary/10 transform transition-all duration-300">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary/10 to-orange-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-primary">
                            <path
                                d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $t('Change Status') }}</h2>
                </div>
                <p class="text-gray-600 mb-6 text-base">{{ $t('Are you sure you want to change status?') }}</p>
                <div class="flex gap-3">
                    <button @click="isOpenStatusModal = false"
                        class="flex-1 px-6 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition-all duration-300 transform hover:scale-105">
                        {{ $t('Cancel') }}
                    </button>
                    <button @click="confirmStatusChange"
                        class="flex-1 px-6 py-3 rounded-xl bg-gradient-to-r from-primary via-orange-600 to-red-600 text-white font-bold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        {{ $t('Confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.4;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>