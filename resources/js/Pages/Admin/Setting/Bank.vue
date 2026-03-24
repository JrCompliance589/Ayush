<template>
    <AppLayout>
        <div class="p-6 md:p-8 space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">{{ $t('Bank Accounts') }}</h1>
                    <p class="text-sm text-slate-600 mt-1">{{ $t('Manage your bank account information') }}</p>
                </div>
                <button @click="openCreateModal" type="button"
                    class="group px-6 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 transition-all duration-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M19 12.998h-6v6h-2v-6H5v-2h6v-6h2v6h6z" />
                    </svg>
                    {{ $t('Add Bank Account') }}
                </button>
            </div>

            <!-- Filters and Search -->
            <div
                class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <path fill="currentColor"
                                    d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5A6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5S14 7.01 14 9.5S11.99 14 9.5 14z" />
                            </svg>
                            <input v-model="filters.search" @input="debouncedSearch" type="text"
                                :placeholder="$t('Search by account name, bank name, account number...')"
                                class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <select v-model="filters.status" @change="fetchBankInfo"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200">
                            <option value="">{{ $t('All Status') }}</option>
                            <option value="active">{{ $t('Active') }}</option>
                            <option value="inactive">{{ $t('Inactive') }}</option>
                            <option value="closed">{{ $t('Closed') }}</option>
                        </select>
                    </div>

                    <!-- Primary Filter -->
                    <div>
                        <select v-model="filters.is_primary" @change="fetchBankInfo"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200">
                            <option value="">{{ $t('All Accounts') }}</option>
                            <option value="true">{{ $t('Primary Only') }}</option>
                            <option value="false">{{ $t('Non-Primary') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Bank Accounts List -->
            <div v-if="isLoading" class="flex items-center justify-center py-12">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                        opacity=".5" />
                    <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                        <animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite"
                            to="360 12 12" type="rotate" />
                    </path>
                </svg>
            </div>

            <div v-else-if="bankAccounts.length === 0"
                class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <div
                    class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-slate-100 to-orange-50 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                        class="text-slate-400">
                        <path fill="currentColor"
                            d="M20 8H4V6h16m0 12H4v-6h16m0-8H4c-1.11 0-2 .89-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ $t('No bank accounts found') }}</h3>
                <p class="text-slate-600 mb-6">{{ $t('Get started by adding your first bank account') }}</p>
                <button @click="openCreateModal"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M19 12.998h-6v6h-2v-6H5v-2h6v-6h2v6h6z" />
                    </svg>
                    {{ $t('Add Bank Account') }}
                </button>
            </div>

            <div v-else class="grid grid-cols-1 gap-4">
                <TransitionGroup name="list">
                    <div v-for="bank in bankAccounts" :key="bank.id"
                        class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-4 flex-1">
                                    <!-- Bank Icon -->
                                    <div
                                        class="p-3 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-xl shadow-lg shadow-[#ff5100]/20 shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" class="text-white">
                                            <path fill="currentColor"
                                                d="M20 8H4V6h16m0 12H4v-6h16m0-8H4c-1.11 0-2 .89-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Z" />
                                        </svg>
                                    </div>

                                    <!-- Bank Details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h3 class="text-lg font-bold text-slate-900">{{ bank.account_name }}</h3>
                                            <span v-if="bank.is_primary"
                                                class="px-2.5 py-1 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white text-xs font-semibold rounded-full">
                                                {{ $t('Primary') }}
                                            </span>
                                            <span :class="getStatusClass(bank.status)"
                                                class="px-2.5 py-1 text-xs font-semibold rounded-full">
                                                {{ $t(bank.status.charAt(0).toUpperCase() + bank.status.slice(1)) }}
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                                            <div>
                                                <p class="text-xs text-slate-500 mb-1">{{ $t('Bank Name') }}</p>
                                                <p class="text-sm font-semibold text-slate-900">{{ bank.bank_name }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 mb-1">{{ $t('Account Number') }}</p>
                                                <p class="text-sm font-mono font-semibold text-slate-900">
                                                    {{ maskAccountNumber(bank.account_number) }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 mb-1">{{ $t('IFSC Code') }}</p>
                                                <p class="text-sm font-mono font-semibold text-slate-900">{{
                                                    bank.ifsc_code }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 mb-1">{{ $t('Account Type') }}</p>
                                                <p class="text-sm font-semibold text-slate-900 capitalize">{{
                                                    bank.account_type }}</p>
                                            </div>
                                        </div>

                                        <div class="mt-4 pt-4 border-t border-slate-100">
                                            <p class="text-xs text-slate-500">{{ $t('Branch') }}: <span
                                                    class="text-slate-900 font-medium">{{ bank.branch }}</span></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-2">
                                    <button @click="openEditModal(bank)"
                                        class="p-2 text-slate-600 hover:text-[#ff5100] hover:bg-orange-50 rounded-lg transition-all duration-200"
                                        :title="$t('Edit')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 0 0-1.41 0l-1.83 1.83l3.75 3.75l1.83-1.83z" />
                                        </svg>
                                    </button>
                                    <button @click="confirmDelete(bank)"
                                        class="p-2 text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200"
                                        :title="$t('Delete')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </TransitionGroup>
            </div>

            <!-- Pagination -->
            <div v-if="pagination && pagination.totalPages > 1"
                class="flex items-center justify-between bg-white rounded-2xl border border-slate-200 p-4">
                <div class="text-sm text-slate-600">
                    {{ $t('Showing') }} {{ ((pagination.page - 1) * pagination.limit) + 1 }}
                    {{ $t('to') }}
                    {{ Math.min(pagination.page * pagination.limit, pagination.total) }}
                    {{ $t('of') }}
                    {{ pagination.total }}
                    {{ $t('results') }}
                </div>
                <div class="flex items-center gap-2">
                    <button @click="changePage(pagination.page - 1)" :disabled="pagination.page === 1"
                        class="px-4 py-2 border border-slate-200 rounded-lg text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                        {{ $t('Previous') }}
                    </button>
                    <div class="flex items-center gap-1">
                        <button v-for="page in visiblePages" :key="page" @click="changePage(page)"
                            :class="[page === pagination.page ? 'bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white shadow-lg shadow-[#ff5100]/20' : 'text-slate-700 hover:bg-slate-50']"
                            class="w-10 h-10 rounded-lg font-medium transition-all duration-200">
                            {{ page }}
                        </button>
                    </div>
                    <button @click="changePage(pagination.page + 1)"
                        :disabled="pagination.page === pagination.totalPages"
                        class="px-4 py-2 border border-slate-200 rounded-lg text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                        {{ $t('Next') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <BankInfoModal v-if="showModal" :bank="selectedBank" :isEdit="isEditMode" @close="closeModal"
            @success="handleSuccess" />

        <!-- Delete Confirmation Modal -->
        <DeleteConfirmModal v-if="showDeleteModal" :bank="bankToDelete" @close="showDeleteModal = false"
            @confirm="handleDelete" />
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AppLayout from "./Layout/App.vue";
import axios from 'axios';
import { debounce } from 'lodash';
import BankInfoModal from '../../../Components/BankInfoModal.vue';
import DeleteConfirmModal from '../../../Components/DeleteConfirmModal.vue';
const base_url = import.meta.env.VITE_BACKEND_API_URL;

const bankAccounts = ref([]);
const isLoading = ref(false);
const showModal = ref(false);
const showDeleteModal = ref(false);
const selectedBank = ref(null);
const bankToDelete = ref(null);
const isEditMode = ref(false);
const pagination = ref(null);

const filters = ref({
    search: '',
    status: '',
    is_primary: '',
    page: 1,
    limit: 10
});

const fetchBankInfo = async () => {
    isLoading.value = true;
    try {
        const params = new URLSearchParams();
        if (filters.value.search) params.append('search', filters.value.search);
        if (filters.value.status) params.append('status', filters.value.status);
        if (filters.value.is_primary) params.append('is_primary', filters.value.is_primary);
        params.append('page', filters.value.page);
        params.append('limit', filters.value.limit);

        const response = await axios.get(`${base_url}/bank-info?${params.toString()}`);
        bankAccounts.value = response.data.data.data;
        pagination.value = response.data.data.pagination;
    } catch (error) {
        console.error('Error fetching bank info:', error);
    } finally {
        isLoading.value = false;
    }
};

const debouncedSearch = debounce(() => {
    filters.value.page = 1;
    fetchBankInfo();
}, 500);

const openCreateModal = () => {
    selectedBank.value = null;
    isEditMode.value = false;
    showModal.value = true;
};

const openEditModal = (bank) => {
    selectedBank.value = bank;
    isEditMode.value = true;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedBank.value = null;
};

const handleSuccess = () => {
    closeModal();
    fetchBankInfo();
};

const confirmDelete = (bank) => {
    bankToDelete.value = bank;
    showDeleteModal.value = true;
};

const handleDelete = async () => {
    try {
        await axios.delete(`${base_url}/bank-info/${bankToDelete.value.id}`);
        showDeleteModal.value = false;
        bankToDelete.value = null;
        fetchBankInfo();
    } catch (error) {
        console.error('Error deleting bank info:', error);
    }
};

const maskAccountNumber = (accountNumber) => {
    if (!accountNumber) return '';
    const visible = accountNumber.slice(-4);
    return '••••' + visible;
};

const getStatusClass = (status) => {
    const classes = {
        active: 'bg-green-100 text-green-700',
        inactive: 'bg-yellow-100 text-yellow-700',
        closed: 'bg-red-100 text-red-700'
    };
    return classes[status] || 'bg-slate-100 text-slate-700';
};

const changePage = (page) => {
    filters.value.page = page;
    fetchBankInfo();
};

const visiblePages = computed(() => {
    if (!pagination.value) return [];
    const total = pagination.value.totalPages;
    const current = pagination.value.page;
    const pages = [];

    if (total <= 7) {
        for (let i = 1; i <= total; i++) {
            pages.push(i);
        }
    } else {
        if (current <= 4) {
            for (let i = 1; i <= 5; i++) pages.push(i);
            pages.push('...');
            pages.push(total);
        } else if (current >= total - 3) {
            pages.push(1);
            pages.push('...');
            for (let i = total - 4; i <= total; i++) pages.push(i);
        } else {
            pages.push(1);
            pages.push('...');
            for (let i = current - 1; i <= current + 1; i++) pages.push(i);
            pages.push('...');
            pages.push(total);
        }
    }

    return pages;
});

onMounted(() => {
    fetchBankInfo();
});
</script>

<style scoped>
.list-enter-active,
.list-leave-active {
    transition: all 0.3s ease;
}

.list-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}

.list-leave-to {
    opacity: 0;
    transform: translateY(10px);
}
</style>