<script setup>
import { ref } from 'vue';
import debounce from 'lodash/debounce';
import { router, useForm } from '@inertiajs/vue3';
import AlertModal from '@/Components/AlertModal.vue';
import { useAlertModal } from '@/Composables/useAlertModal';
import Pagination from '../Pagination.vue';
import axios from 'axios';
import Dropdown from '../Dropdown.vue';
import DropdownItemGroup from '../DropdownItemGroup.vue';
import DropdownItem from '../DropdownItem.vue';

const props = defineProps({
    rows: { type: Object, required: true },
    filters: { type: Object },
    type: { type: String },
    showDeleteBtn: { type: Boolean, default: true },
    showRole: { type: Boolean, default: false }
});

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();
const form = useForm({ test: null });

const deleteAction = (key) => {
    form.delete(props.type === 'admin' ? '/admin/team/users/' + key : '/admin/users/' + key);
};

const params = ref({ search: props.filters.search });
const isSearching = ref(false);
const emit = defineEmits(['delete']);

function deleteItem(id) {
    emit('delete', id);
}

const clearSearch = () => {
    params.value.search = null;
    runSearch();
};

const search = debounce(() => {
    isSearching.value = true;
    runSearch();
}, 1000);

const statusLabel = (status) => {
    if (status === 1) return 'Active';
    if (status === 0) return 'Inactive';
    return status;
};

const getStatusColor = (status) => {
    if (status === 1) return 'bg-emerald-50 text-emerald-700 border-emerald-100';
    if (status === 0) return 'bg-red-50 text-red-700 border-red-100';
    return 'bg-gray-50 text-gray-700 border-gray-100';
};

const getRoleBadgeColor = (role) => {
    const colors = {
        'admin': 'bg-purple-50 text-purple-700 border-purple-100',
        'user': 'bg-blue-50 text-blue-700 border-blue-100',
        'manager': 'bg-amber-50 text-amber-700 border-amber-100'
    };
    return colors[role?.toLowerCase()] || 'bg-gray-50 text-gray-700 border-gray-100';
};

const runSearch = () => {
    const url = window.location.pathname;
    router.visit(url, { method: 'get', data: params.value });
};

// Balance Modal State
const isBalanceModalOpen = ref(false);
const isSuccessModalOpen = ref(false);
const selectedUserId = ref(null);
const balanceForm = useForm({ amount: null });

const openBalanceModal = (id) => {
    selectedUserId.value = id;
    isBalanceModalOpen.value = true;
};

const closeBalanceModal = () => {
    isBalanceModalOpen.value = false;
    balanceForm.reset();
};

const submitBalance = async () => {
    if (!selectedUserId.value) return;
    try {
        await axios.post(`/api/users/${selectedUserId.value}/increment-balance`, {
            amount: balanceForm.amount,
        });
        closeBalanceModal();
        isSuccessModalOpen.value = true;
        runSearch();
    } catch (error) {
        console.error(error);
    }
};

// Debit Modal State
const isDebitModalOpen = ref(false);
const debitForm = useForm({ amount: null });

const openDebitModal = (id) => {
    selectedUserId.value = id;
    isDebitModalOpen.value = true;
};

const closeDebitModal = () => {
    isDebitModalOpen.value = false;
    debitForm.reset();
};

const submitDebit = async () => {
    if (!selectedUserId.value) return;
    try {
        await axios.post(`/api/users/${selectedUserId.value}/decrement-balance`, {
            amount: debitForm.amount,
        });
        closeDebitModal();
        isSuccessModalOpen.value = true;
        runSearch();
    } catch (error) {
        console.error(error);
        alert(error.response?.data?.message || 'Failed to debit balance');
    }
};

// Balance History Modal State
const isHistoryModalOpen = ref(false);
const balanceHistory = ref([]);

const openHistoryModal = async (id) => {
    selectedUserId.value = id;
    isHistoryModalOpen.value = true;
    try {
        const response = await axios.get(`/api/users/${id}/balance-history`);
        balanceHistory.value = response.data.balance_history;
    } catch (error) {
        console.error(error);
    }
};

const closeHistoryModal = () => {
    isHistoryModalOpen.value = false;
    balanceHistory.value = [];
};

// Price Modal State
const isPriceModalOpen = ref(false);
const priceForm = useForm({
    marketing_price: null,
    utility_price: null,
    auth_price: null,
});

const openPriceModal = (id) => {
    selectedUserId.value = id;
    isPriceModalOpen.value = true;
    axios.get(`/api/users/${id}/prices`)
        .then(res => {
            priceForm.marketing_price = res.data.marketing_price;
            priceForm.utility_price = res.data.utility_price;
            priceForm.auth_price = res.data.auth_price;
        })
        .catch(err => console.error(err));
};

const closePriceModal = () => {
    isPriceModalOpen.value = false;
    priceForm.reset();
};

const submitPrice = async () => {
    if (!selectedUserId.value) return;
    try {
        await axios.post(`/api/users/${selectedUserId.value}/update-prices`, {
            marketing_price: priceForm.marketing_price,
            utility_price: priceForm.utility_price,
            auth_price: priceForm.auth_price,
        });
        closePriceModal();
        isSuccessModalOpen.value = true;
        runSearch();
    } catch (error) {
        console.error(error);
        alert(error.response?.data?.message || 'Failed to update prices');
    }
};

// Dropdown State
const openDropdownId = ref(null);

const toggleDropdown = (id) => {
    openDropdownId.value = openDropdownId.value === id ? null : id;
};

// Subscription Expiry Modal State
const isExpiryModalOpen = ref(false);
const expiryForm = useForm({ valid_until: null });
const subscriptionInfo = ref(null);
const expiryLoading = ref(false);

const openExpiryModal = async (id) => {
    selectedUserId.value = id;
    expiryLoading.value = true;
    try {
        const response = await axios.get(`/api/users/${id}/subscription`);
        subscriptionInfo.value = response.data;
        if (response.data.has_subscription && response.data.subscription.valid_until) {
            expiryForm.valid_until = response.data.subscription.valid_until;
        }
        isExpiryModalOpen.value = true;
    } catch (error) {
        console.error(error);
        alert('Failed to fetch subscription info');
    } finally {
        expiryLoading.value = false;
    }
};

const closeExpiryModal = () => {
    isExpiryModalOpen.value = false;
    expiryForm.reset();
    subscriptionInfo.value = null;
};

const submitExpiry = async () => {
    if (!selectedUserId.value) return;
    try {
        await axios.post(`/api/users/${selectedUserId.value}/update-subscription-expiry`, {
            valid_until: expiryForm.valid_until,
        });
        closeExpiryModal();
        isSuccessModalOpen.value = true;
        runSearch();
    } catch (error) {
        console.error(error);
        alert(error.response?.data?.message || 'Failed to update subscription expiry');
    }
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
                        :placeholder="$t('Search users')">
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
                                    <span>{{ $t('Name') }}</span>
                                </div>
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden sm:table-cell">
                                <div class="flex items-center space-x-2">
                                    <span>{{ $t('Email') }}</span>
                                </div>
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden sm:table-cell">
                                <div class="flex items-center space-x-2">
                                    <span>{{ $t('Phone') }}</span>
                                </div>
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden sm:table-cell">
                                <div class="flex items-center space-x-2">
                                    <span>{{ $t('Balance') }}</span>
                                </div>
                            </th>
                            <th v-if="type === 'admin' || showRole === true"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden sm:table-cell">
                                <div class="flex items-center space-x-2">
                                    <span>{{ $t('Role') }}</span>
                                </div>
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden sm:table-cell">
                                <div class="flex items-center space-x-2">
                                    <span>{{ $t('Status') }}</span>
                                </div>
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden lg:table-cell">
                                <div class="flex items-center space-x-2">
                                    <span>{{ $t('Last Updated') }}</span>
                                </div>
                            </th>
                            <th v-if="type !== 'admin'"
                                class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <span>{{ $t('Actions') }}</span>
                            </th>
                        </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody class="divide-y divide-orange-100">
                        <tr v-for="(item, index) in rows.data" :key="index"
                            class="hover:bg-orange-50/30 transition-all duration-200 group">
                            <!-- Name -->
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-10 h-10 rounded-full bg-gradient-to-br from-[#ff5100] to-[#ff7733] flex items-center justify-center text-white font-bold text-sm">
                                        {{ item.full_name?.charAt(0).toUpperCase() || 'U' }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 capitalize">
                                            {{ item.full_name }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="px-6 py-5 whitespace-nowrap hidden sm:table-cell">
                                <div class="text-sm text-gray-600">
                                    {{ item.email }}
                                </div>
                            </td>

                            <!-- Phone -->
                            <td class="px-6 py-5 whitespace-nowrap hidden sm:table-cell">
                                <div class="text-sm text-gray-600">
                                    {{ item.phone ?? $t('N/A') }}
                                </div>
                            </td>

                            <!-- Balance -->
                            <td class="px-6 py-5 whitespace-nowrap hidden sm:table-cell">
                                <div class="flex items-center space-x-2">
                                    <span
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-orange-50 text-[#ff5100] border border-orange-100">
                                        {{ item.balance ?? 0 }}
                                    </span>
                                </div>
                            </td>

                            <!-- Role -->
                            <td v-if="type === 'admin' || showRole === true"
                                class="px-6 py-5 whitespace-nowrap hidden sm:table-cell">
                                <span
                                    :class="['inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium border capitalize', getRoleBadgeColor(item.role)]">
                                    {{ item.role }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-5 whitespace-nowrap hidden sm:table-cell">
                                <span
                                    :class="['inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium border', getStatusColor(item.status)]">
                                    {{ statusLabel(item.status) }}
                                </span>
                            </td>

                            <!-- Last Updated -->
                            <td class="px-6 py-5 whitespace-nowrap hidden lg:table-cell">
                                <span class="text-sm text-gray-600">
                                    {{ item.updated_at }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-5 whitespace-nowrap text-right">
                                <Dropdown v-if="item.role != 'admin'" :align="'right'" class="mt-2">
                                    <button
                                        class="inline-flex w-full justify-center rounded-md text-sm font-medium text-black hover:bg-opacity-30">
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
                                            <DropdownItem
                                                :href="type === 'admin' ? '/admin/team/users/' + item.id : '/admin/users/' + item.id">
                                                {{ $t('View/edit') }}</DropdownItem>
                                            <DropdownItem as="button" @click="openBalanceModal(item.id)">{{ $t(`Add
                                                Balance`) }}
                                            </DropdownItem>
                                            <DropdownItem as="button" @click="openDebitModal(item.id)">
                                                {{ $t(`Debit Balance`) }}
                                            </DropdownItem>
                                            <DropdownItem :href="'/admin/country-pricing?user_id=' + item.id">
                                                {{ $t(`Edit Prices`) }}
                                            </DropdownItem>
                                            <DropdownItem as="button" @click="openExpiryModal(item.id)">
                                                {{ $t(`Edit Plan Expiry`) }}
                                            </DropdownItem>
                                            <DropdownItem as="button" @click="openHistoryModal(item.id)">{{ $t(`Balance
                                                History`) }}
                                            </DropdownItem>
                                            <DropdownItem v-if="showDeleteBtn" as="button" @click="openAlert(item.id)">
                                                {{
                                                    $t('Delete') }}</DropdownItem>
                                        </DropdownItemGroup>
                                    </template>
                                </Dropdown>
                            </td>
                        </tr>

                        <!-- Empty State -->
                        <tr v-if="rows.data.length === 0">
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                            viewBox="0 0 24 24" class="text-gray-400">
                                            <path fill="currentColor"
                                                d="M12 4a4 4 0 0 1 4 4a4 4 0 0 1-4 4a4 4 0 0 1-4-4a4 4 0 0 1 4-4m0 10c4.42 0 8 1.79 8 4v2H4v-2c0-2.21 3.58-4 8-4z" />
                                        </svg>
                                    </div>
                                    <div class="text-center">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $t('No users found') }}
                                        </h3>
                                        <p class="text-sm text-gray-500">{{ $t('Your users will appear here') }}</p>
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

    <!-- Delete Modal -->
    <AlertModal v-model="isOpenAlert" @confirm="() => confirmAlert(deleteAction)" :label="$t('Delete row')"
        :description="$t('Are you sure you want to delete this row? This action can not be undone')" />

    <!-- Add Balance Modal -->
    <div v-if="isBalanceModalOpen" @click.self="closeBalanceModal"
        class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4">
        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all animate-in fade-in zoom-in duration-300">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900">{{ $t('Add Balance') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $t('Enter the amount to add to user balance') }}</p>
            </div>
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('Amount') }}</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">₹</span>
                    <input v-model="balanceForm.amount" type="number" step="0.01" :placeholder="$t('0.00')"
                        class="w-full pl-9 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#ff5100] focus:ring-4 focus:ring-orange-50 transition-all outline-none" />
                </div>
            </div>
            <div class="p-6 border-t border-gray-100 flex justify-end space-x-3">
                <button @click="closeBalanceModal"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                    {{ $t('Cancel') }}
                </button>
                <button @click="submitBalance"
                    class="px-5 py-2.5 bg-primary text-white font-medium rounded-xl transition-all hover:shadow-lg">
                    {{ $t('Add Balance') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Debit Balance Modal -->
    <div v-if="isDebitModalOpen" @click.self="closeDebitModal"
        class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4">
        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all animate-in fade-in zoom-in duration-300">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900">{{ $t('Debit Balance') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $t('Enter the amount to deduct from user balance') }}</p>
            </div>
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('Amount') }}</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">₹</span>
                    <input v-model="debitForm.amount" type="number" step="0.01" :placeholder="$t('0.00')"
                        class="w-full pl-9 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-4 focus:ring-red-50 transition-all outline-none" />
                </div>
            </div>
            <div class="p-6 border-t border-gray-100 flex justify-end space-x-3">
                <button @click="closeDebitModal"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                    {{ $t('Cancel') }}
                </button>
                <button @click="submitDebit"
                    class="px-5 py-2.5 bg-primary text-white font-medium rounded-xl transition-all hover:shadow-lg">
                    {{ $t('Debit') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Prices Modal -->
    <div v-if="isPriceModalOpen" @click.self="closePriceModal"
        class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4">
        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all animate-in fade-in zoom-in duration-300">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900">{{ $t('Edit Message Prices') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $t('Update pricing for different message types') }}</p>
            </div>
            <div class="p-6 space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('Marketing Price') }}</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">₹</span>
                        <input v-model="priceForm.marketing_price" type="number" step="0.01" :placeholder="$t('0.00')"
                            class="w-full pl-9 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#ff5100] focus:ring-4 focus:ring-orange-50 transition-all outline-none" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('Utility Price') }}</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">₹</span>
                        <input v-model="priceForm.utility_price" type="number" step="0.01" :placeholder="$t('0.00')"
                            class="w-full pl-9 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#ff5100] focus:ring-4 focus:ring-orange-50 transition-all outline-none" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('Auth Price') }}</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">₹</span>
                        <input v-model="priceForm.auth_price" type="number" step="0.01" :placeholder="$t('0.00')"
                            class="w-full pl-9 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#ff5100] focus:ring-4 focus:ring-orange-50 transition-all outline-none" />
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-100 flex justify-end space-x-3">
                <button @click="closePriceModal"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                    {{ $t('Cancel') }}
                </button>
                <button @click="submitPrice"
                    class="px-5 py-2.5 bg-primary text-white font-medium rounded-xl transition-all hover:shadow-lg">
                    {{ $t('Save Changes') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Balance History Modal -->
    <div v-if="isHistoryModalOpen" @click.self="closeHistoryModal"
        class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4">
        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl transform transition-all animate-in fade-in zoom-in duration-300 max-h-[90vh] flex flex-col">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900">{{ $t('Balance History') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $t('Complete transaction history for this user') }}</p>
            </div>
            <div class="p-6 overflow-auto flex-1">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b-2 border-orange-100">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    {{ $t('Date') }}
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    {{ $t('Amount') }}
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    {{ $t('Balance After') }}
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    {{ $t('Type') }}
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    {{ $t('Note') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="record in balanceHistory" :key="record.id"
                                class="hover:bg-orange-50/30 transition-colors">
                                <td class="px-4 py-4 text-sm text-gray-900">
                                    {{ new Date(record.created_at).toLocaleString() }}
                                </td>
                                <td class="px-4 py-4">
                                    <span
                                        :class="['inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold', record.type === 'credit' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-700 border border-red-100']">
                                        {{ record.type === 'credit' ? '+' : '-' }}{{ record.amount }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-orange-50 text-[#ff5100] border border-orange-100">
                                        {{ record.balance_after }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-sm font-medium text-gray-900 capitalize">{{ record.type }}</span>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600">
                                    {{ record.note ?? '-' }}
                                </td>
                            </tr>
                            <tr v-if="balanceHistory.length === 0">
                                <td colspan="5" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                viewBox="0 0 24 24" class="text-gray-400">
                                                <path fill="currentColor"
                                                    d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z" />
                                            </svg>
                                        </div>
                                        <div class="text-center">
                                            <h3 class="text-sm font-semibold text-gray-900">{{ $t('No history found') }}
                                            </h3>
                                            <p class="text-xs text-gray-500">{{ $t(`Transaction history will appear
                                                here`) }}</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="p-6 border-t border-gray-100 flex justify-end">
                <button @click="closeHistoryModal"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                    {{ $t('Close') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Subscription Expiry Modal -->
    <div v-if="isExpiryModalOpen" @click.self="closeExpiryModal"
        class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4">
        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all animate-in fade-in zoom-in duration-300">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900">{{ $t('Edit Plan Expiry') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $t('Update the subscription expiry date for this user') }}</p>
            </div>
            <div class="p-6">
                <div v-if="subscriptionInfo && subscriptionInfo.has_subscription" class="space-y-4">
                    <div class="bg-orange-50 border border-orange-100 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-600">{{ $t('Current Plan') }}</span>
                            <span class="text-sm font-bold text-gray-900">{{ subscriptionInfo.subscription.plan_name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">{{ $t('Status') }}</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium capitalize"
                                :class="subscriptionInfo.subscription.status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-50 text-gray-700 border border-gray-100'">
                                {{ subscriptionInfo.subscription.status }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('Expiry Date') }}</label>
                        <input v-model="expiryForm.valid_until" type="date"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#ff5100] focus:ring-4 focus:ring-orange-50 transition-all outline-none" />
                    </div>
                </div>
                <div v-else class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" class="text-gray-400">
                            <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-1">{{ $t('No subscription found') }}</h3>
                    <p class="text-xs text-gray-500">{{ $t('This user does not have an active subscription') }}</p>
                </div>
            </div>
            <div class="p-6 border-t border-gray-100 flex justify-end space-x-3">
                <button @click="closeExpiryModal"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                    {{ $t('Cancel') }}
                </button>
                <button v-if="subscriptionInfo && subscriptionInfo.has_subscription" @click="submitExpiry"
                    class="px-5 py-2.5 bg-primary text-white font-medium rounded-xl transition-all hover:shadow-lg">
                    {{ $t('Save Changes') }}
                </button>
            </div>
        </div>
    </div>
</template>