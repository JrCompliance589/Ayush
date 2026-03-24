<template>
    <AppLayout>
        <div class="p-6 md:p-8 space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">{{ $t('TDS Tax Slabs') }}</h1>
                    <p class="text-sm text-slate-600 mt-1">{{ $t('Manage TDS tax rates and thresholds for different sections') }}</p>
                </div>
                <button @click="openCreateModal" type="button"
                    class="group px-6 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 transition-all duration-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M19 12.998h-6v6h-2v-6H5v-2h6v-6h2v6h6z" />
                    </svg>
                    {{ $t('Add TDS Slab') }}
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
                                :placeholder="$t('Search by section code, name...')"
                                class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200">
                        </div>
                    </div>

                    <!-- Service Type Filter -->
                    <div>
                        <select v-model="filters.service_type" @change="fetchTdsTaxSlabs"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200">
                            <option value="">{{ $t('All Service Types') }}</option>
                            <option value="professional_services">{{ $t('Professional Services') }}</option>
                            <option value="technical_services">{{ $t('Technical Services') }}</option>
                            <option value="contractor_services">{{ $t('Contractor Services') }}</option>
                            <option value="rent">{{ $t('Rent') }}</option>
                            <option value="commission">{{ $t('Commission') }}</option>
                            <option value="interest">{{ $t('Interest') }}</option>
                            <option value="salary">{{ $t('Salary') }}</option>
                            <option value="other">{{ $t('Other') }}</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <select v-model="filters.is_active" @change="fetchTdsTaxSlabs"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200">
                            <option value="">{{ $t('All Status') }}</option>
                            <option value="true">{{ $t('Active') }}</option>
                            <option value="false">{{ $t('Inactive') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- TDS Tax Slabs List -->
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

            <div v-else-if="tdsTaxSlabs.length === 0"
                class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <div
                    class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-slate-100 to-orange-50 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                        class="text-slate-400">
                        <path fill="currentColor"
                            d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11zM8 15.01l1.41 1.41L11 14.84V19h2v-4.16l1.59 1.59L16 15.01L12.01 11z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ $t('No TDS tax slabs found') }}</h3>
                <p class="text-slate-600 mb-6">{{ $t('Get started by adding your first TDS tax slab') }}</p>
                <button @click="openCreateModal"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M19 12.998h-6v6h-2v-6H5v-2h6v-6h2v6h6z" />
                    </svg>
                    {{ $t('Add TDS Slab') }}
                </button>
            </div>

            <div v-else class="grid grid-cols-1 gap-4">
                <TransitionGroup name="list">
                    <div v-for="slab in tdsTaxSlabs" :key="slab.id"
                        class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-4 flex-1">
                                    <!-- Icon -->
                                    <div
                                        class="p-3 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-xl shadow-lg shadow-[#ff5100]/20 shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" class="text-white">
                                            <path fill="currentColor"
                                                d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11zM8 15.01l1.41 1.41L11 14.84V19h2v-4.16l1.59 1.59L16 15.01L12.01 11z" />
                                        </svg>
                                    </div>

                                    <!-- Details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h3 class="text-lg font-bold text-slate-900">{{ slab.section_code }}</h3>
                                            <span v-if="slab.is_default"
                                                class="px-2.5 py-1 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white text-xs font-semibold rounded-full">
                                                {{ $t('Default') }}
                                            </span>
                                            <span :class="getStatusClass(slab.is_active)"
                                                class="px-2.5 py-1 text-xs font-semibold rounded-full">
                                                {{ slab.is_active ? $t('Active') : $t('Inactive') }}
                                            </span>
                                        </div>

                                        <p class="text-sm font-medium text-slate-700 mb-3">{{ slab.section_name }}</p>

                                        <div v-if="slab.description" class="text-xs text-slate-600 mb-4 line-clamp-2">
                                            {{ slab.description }}
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                            <div
                                                class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-3 border border-blue-200">
                                                <p class="text-xs text-blue-600 mb-1">{{ $t('Service Type') }}</p>
                                                <p class="text-sm font-semibold text-blue-900">
                                                    {{ formatServiceType(slab.service_type) }}
                                                </p>
                                            </div>
                                            <div
                                                class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-3 border border-green-200">
                                                <p class="text-xs text-green-600 mb-1">{{ $t('Individual Rate') }}</p>
                                                <p class="text-sm font-semibold text-green-900">
                                                    {{ slab.rate_individual }}%
                                                </p>
                                            </div>
                                            <div
                                                class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg p-3 border border-purple-200">
                                                <p class="text-xs text-purple-600 mb-1">{{ $t('Company Rate') }}</p>
                                                <p class="text-sm font-semibold text-purple-900">
                                                    {{ slab.rate_company }}%
                                                </p>
                                            </div>
                                            <div
                                                class="bg-gradient-to-br from-orange-50 to-yellow-50 rounded-lg p-3 border border-orange-200">
                                                <p class="text-xs text-orange-600 mb-1">{{ $t('Threshold') }}</p>
                                                <p class="text-sm font-semibold text-orange-900">
                                                    {{ formatCurrency(slab.threshold_limit) }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-4 text-xs text-slate-600">
                                            <div class="flex items-center gap-1.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 24 24" class="text-[#ff5100]">
                                                    <path fill="currentColor"
                                                        d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z" />
                                                </svg>
                                                <span>{{ $t('From') }}: <span class="font-medium text-slate-900">{{
                                                    formatDate(slab.effective_from) }}</span></span>
                                            </div>
                                            <div v-if="slab.effective_to" class="flex items-center gap-1.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 24 24" class="text-[#ff5100]">
                                                    <path fill="currentColor"
                                                        d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z" />
                                                </svg>
                                                <span>{{ $t('To') }}: <span class="font-medium text-slate-900">{{
                                                    formatDate(slab.effective_to) }}</span></span>
                                            </div>
                                            <span v-else class="text-green-600 font-medium">{{ $t('Indefinite') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-2">
                                    <button @click="openViewModal(slab)"
                                        class="p-2 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                                        :title="$t('View')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5s5 2.24 5 5s-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3s3-1.34 3-3s-1.34-3-3-3z" />
                                        </svg>
                                    </button>
                                    <button @click="openEditModal(slab)"
                                        class="p-2 text-slate-600 hover:text-[#ff5100] hover:bg-orange-50 rounded-lg transition-all duration-200"
                                        :title="$t('Edit')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 0 0-1.41 0l-1.83 1.83l3.75 3.75l1.83-1.83z" />
                                        </svg>
                                    </button>
                                    <button @click="confirmDelete(slab)"
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
        <TdsTaxSlabModal v-if="showModal" :tdsSlab="selectedTdsSlab" :isEdit="isEditMode" @close="closeModal"
            @success="handleSuccess" />

        <!-- View Modal -->
        <ViewTdsTaxSlabModal v-if="showViewModal" :tdsSlab="viewTdsSlab" @close="closeViewModal" />

        <!-- Delete Confirmation Modal -->
        <DeleteTdsTaxSlabModal v-if="showDeleteModal" :tdsSlab="tdsSlabToDelete" @close="showDeleteModal = false"
            @confirm="handleDelete" />
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AppLayout from "./Layout/App.vue";
import TdsTaxSlabModal from '../../../Components/TdsTaxSlabModal.vue';
import ViewTdsTaxSlabModal from '../../../Components/ViewTdsTaxSlabModal.vue';
import DeleteTdsTaxSlabModal from '../../../Components/DeleteTdsTaxSlabModal.vue';
import axios from 'axios';
import { debounce } from 'lodash';

const base_url = import.meta.env.VITE_BACKEND_API_URL;

const tdsTaxSlabs = ref([]);
const isLoading = ref(false);
const showModal = ref(false);
const showViewModal = ref(false);
const showDeleteModal = ref(false);
const selectedTdsSlab = ref(null);
const viewTdsSlab = ref(null);
const tdsSlabToDelete = ref(null);
const isEditMode = ref(false);
const pagination = ref(null);

const filters = ref({
    search: '',
    service_type: '',
    is_active: '',
    page: 1,
    limit: 10
});

const fetchTdsTaxSlabs = async () => {
    isLoading.value = true;
    try {
        const params = new URLSearchParams();
        if (filters.value.search) params.append('search', filters.value.search);
        if (filters.value.service_type) params.append('service_type', filters.value.service_type);
        if (filters.value.is_active) params.append('is_active', filters.value.is_active);
        params.append('page', filters.value.page);
        params.append('limit', filters.value.limit);

        const response = await axios.get(`${base_url}/tds-tax-slabs?${params.toString()}`);
        tdsTaxSlabs.value = response.data.data.data;
        pagination.value = response.data.data.pagination;
    } catch (error) {
        console.error('Error fetching TDS tax slabs:', error);
    } finally {
        isLoading.value = false;
    }
};

const debouncedSearch = debounce(() => {
    filters.value.page = 1;
    fetchTdsTaxSlabs();
}, 500);

const openCreateModal = () => {
    selectedTdsSlab.value = null;
    isEditMode.value = false;
    showModal.value = true;
};

const openEditModal = (slab) => {
    selectedTdsSlab.value = slab;
    isEditMode.value = true;
    showModal.value = true;
};

const openViewModal = (slab) => {
    viewTdsSlab.value = slab;
    showViewModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedTdsSlab.value = null;
};

const closeViewModal = () => {
    showViewModal.value = false;
    viewTdsSlab.value = null;
};

const handleSuccess = () => {
    closeModal();
    fetchTdsTaxSlabs();
};

const confirmDelete = (slab) => {
    tdsSlabToDelete.value = slab;
    showDeleteModal.value = true;
};

const handleDelete = async () => {
    try {
        await axios.delete(`${base_url}/tds-tax-slabs/${tdsSlabToDelete.value.id}`);
        showDeleteModal.value = false;
        tdsSlabToDelete.value = null;
        fetchTdsTaxSlabs();
    } catch (error) {
        console.error('Error deleting TDS tax slab:', error);
    }
};

const formatServiceType = (type) => {
    const types = {
        professional_services: 'Professional Services',
        technical_services: 'Technical Services',
        contractor_services: 'Contractor Services',
        rent: 'Rent',
        commission: 'Commission',
        interest: 'Interest',
        salary: 'Salary',
        other: 'Other'
    };
    return types[type] || type;
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        maximumFractionDigits: 0
    }).format(amount);
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-IN', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};

const getStatusClass = (isActive) => {
    return isActive ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700';
};

const changePage = (page) => {
    filters.value.page = page;
    fetchTdsTaxSlabs();
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
    fetchTdsTaxSlabs();
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

.line-clamp-2 {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>