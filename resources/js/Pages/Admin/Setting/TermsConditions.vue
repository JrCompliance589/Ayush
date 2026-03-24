<template>
    <AppLayout>
        <div class="p-6 md:p-8 space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">{{ $t('Terms & Conditions') }}</h1>
                    <p class="text-sm text-slate-600 mt-1">{{ $t('Manage terms and conditions for different document types') }}</p>
                </div>
                <button @click="openCreateModal" type="button"
                    class="group px-6 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 transition-all duration-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M19 12.998h-6v6h-2v-6H5v-2h6v-6h2v6h6z" />
                    </svg>
                    {{ $t('Add Terms & Conditions') }}
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
                                :placeholder="$t('Search by name or category...')"
                                class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200">
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <select v-model="filters.category" @change="fetchTermsConditions"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200">
                            <option value="">{{ $t('All Categories') }}</option>
                            <option value="quotation">{{ $t('Quotation') }}</option>
                            <option value="proforma_invoice">{{ $t('Proforma Invoice') }}</option>
                            <option value="invoice">{{ $t('Invoice') }}</option>
                            <option value="general">{{ $t('General') }}</option>
                            <option value="other">{{ $t('Other') }}</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <select v-model="filters.status" @change="fetchTermsConditions"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200">
                            <option value="">{{ $t('All Status') }}</option>
                            <option value="active">{{ $t('Active') }}</option>
                            <option value="inactive">{{ $t('Inactive') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Terms & Conditions List -->
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

            <div v-else-if="termsConditionsList.length === 0"
                class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <div
                    class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-slate-100 to-orange-50 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                        class="text-slate-400">
                        <path fill="currentColor"
                            d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ $t('No terms & conditions found') }}</h3>
                <p class="text-slate-600 mb-6">{{ $t('Get started by adding your first terms & conditions') }}</p>
                <button @click="openCreateModal"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M19 12.998h-6v6h-2v-6H5v-2h6v-6h2v6h6z" />
                    </svg>
                    {{ $t('Add Terms & Conditions') }}
                </button>
            </div>

            <div v-else class="grid grid-cols-1 gap-4">
                <TransitionGroup name="list">
                    <div v-for="tc in termsConditionsList" :key="tc.id"
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
                                                d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
                                        </svg>
                                    </div>

                                    <!-- Details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h3 class="text-lg font-bold text-slate-900">{{ tc.name }}</h3>
                                            <span v-if="tc.is_primary"
                                                class="px-2.5 py-1 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white text-xs font-semibold rounded-full">
                                                {{ $t('Primary') }}
                                            </span>
                                            <span :class="getStatusClass(tc.status)"
                                                class="px-2.5 py-1 text-xs font-semibold rounded-full">
                                                {{ $t(tc.status.charAt(0).toUpperCase() + tc.status.slice(1)) }}
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-4 mb-4">
                                            <div
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 rounded-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" class="text-slate-600">
                                                    <path fill="currentColor"
                                                        d="M10 4h4v4h-4V4zm0 6h4v10h-4V10zM4 14h4v6H4v-6zm12 0h4v6h-4v-6z" />
                                                </svg>
                                                <span class="text-sm font-semibold text-slate-900">
                                                    {{ formatCategory(tc.category) }}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-1.5 text-sm text-slate-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24">
                                                    <path fill="currentColor"
                                                        d="M9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" />
                                                </svg>
                                                <span>{{ tc.terms.length }} {{ $t('Terms') }}</span>
                                            </div>
                                        </div>

                                        <!-- Terms Preview -->
                                        <div class="space-y-2">
                                            <div v-for="(term, index) in tc.terms.slice(0, 3)" :key="index"
                                                class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                                                <div class="flex items-start gap-2">
                                                    <span
                                                        class="flex-shrink-0 w-5 h-5 bg-[#ff5100] text-white text-xs font-bold rounded-full flex items-center justify-center">
                                                        {{ index + 1 }}
                                                    </span>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-semibold text-slate-900 mb-1">{{
                                                            term.title }}</p>
                                                        <p class="text-xs text-slate-600 line-clamp-2">{{
                                                            term.description }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <button v-if="tc.terms.length > 3" @click="openViewModal(tc)"
                                                class="text-sm text-[#ff5100] hover:text-[#ff7340] font-medium flex items-center gap-1">
                                                <span>{{ $t('View all') }} {{ tc.terms.length }} {{ $t('terms') }}</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24">
                                                    <path fill="currentColor" d="M8.59 16.59L13.17 12L8.59 7.41L10 6l6 6l-6 6l-1.41-1.41z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-2">
                                    <button @click="openViewModal(tc)"
                                        class="p-2 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                                        :title="$t('View')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5s5 2.24 5 5s-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3s3-1.34 3-3s-1.34-3-3-3z" />
                                        </svg>
                                    </button>
                                    <button @click="openEditModal(tc)"
                                        class="p-2 text-slate-600 hover:text-[#ff5100] hover:bg-orange-50 rounded-lg transition-all duration-200"
                                        :title="$t('Edit')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 0 0-1.41 0l-1.83 1.83l3.75 3.75l1.83-1.83z" />
                                        </svg>
                                    </button>
                                    <button @click="confirmDelete(tc)"
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
        <TermsConditionsModal v-if="showModal" :termsConditions="selectedTermsConditions" :isEdit="isEditMode"
            @close="closeModal" @success="handleSuccess" />

        <!-- View Modal -->
        <ViewTermsConditionsModal v-if="showViewModal" :termsConditions="viewTermsConditions" @close="closeViewModal" />

        <!-- Delete Confirmation Modal -->
        <DeleteConfirmModal v-if="showDeleteModal" :termsConditions="termsConditionsToDelete"
            @close="showDeleteModal = false" @confirm="handleDelete" />
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AppLayout from "./Layout/App.vue";
import TermsConditionsModal from '../../../Components/TermsConditionsModal.vue';
import ViewTermsConditionsModal from '../../../Components/ViewTermsConditionsModal.vue';
import DeleteConfirmModal from '../../../Components/DeleteTermsConditionsModal.vue';
import axios from 'axios';
import { debounce } from 'lodash';
const base_url = import.meta.env.VITE_BACKEND_API_URL;

const termsConditionsList = ref([]);
const isLoading = ref(false);
const showModal = ref(false);
const showViewModal = ref(false);
const showDeleteModal = ref(false);
const selectedTermsConditions = ref(null);
const viewTermsConditions = ref(null);
const termsConditionsToDelete = ref(null);
const isEditMode = ref(false);
const pagination = ref(null);

const filters = ref({
    search: '',
    category: '',
    status: '',
    page: 1,
    limit: 10
});

const fetchTermsConditions = async () => {
    isLoading.value = true;
    try {
        const params = new URLSearchParams();
        if (filters.value.search) params.append('search', filters.value.search);
        if (filters.value.category) params.append('category', filters.value.category);
        if (filters.value.status) params.append('status', filters.value.status);
        params.append('page', filters.value.page);
        params.append('limit', filters.value.limit);

        const response = await axios.get(`${base_url}/terms-conditions?${params.toString()}`);
        termsConditionsList.value = response.data.data.data;
        pagination.value = response.data.data.pagination;
    } catch (error) {
        console.error('Error fetching terms & conditions:', error);
    } finally {
        isLoading.value = false;
    }
};

const debouncedSearch = debounce(() => {
    filters.value.page = 1;
    fetchTermsConditions();
}, 500);

const openCreateModal = () => {
    selectedTermsConditions.value = null;
    isEditMode.value = false;
    showModal.value = true;
};

const openEditModal = (tc) => {
    selectedTermsConditions.value = tc;
    isEditMode.value = true;
    showModal.value = true;
};

const openViewModal = (tc) => {
    viewTermsConditions.value = tc;
    showViewModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedTermsConditions.value = null;
};

const closeViewModal = () => {
    showViewModal.value = false;
    viewTermsConditions.value = null;
};

const handleSuccess = () => {
    closeModal();
    fetchTermsConditions();
};

const confirmDelete = (tc) => {
    termsConditionsToDelete.value = tc;
    showDeleteModal.value = true;
};

const handleDelete = async () => {
    try {
        await axios.delete(`${base_url}/terms-conditions/${termsConditionsToDelete.value.id}`);
        showDeleteModal.value = false;
        termsConditionsToDelete.value = null;
        fetchTermsConditions();
    } catch (error) {
        console.error('Error deleting terms & conditions:', error);
    }
};

const formatCategory = (category) => {
    const categories = {
        quotation: 'Quotation',
        proforma_invoice: 'Proforma Invoice',
        invoice: 'Invoice',
        general: 'General',
        other: 'Other'
    };
    return categories[category] || category;
};

const getStatusClass = (status) => {
    const classes = {
        active: 'bg-green-100 text-green-700',
        inactive: 'bg-yellow-100 text-yellow-700'
    };
    return classes[status] || 'bg-slate-100 text-slate-700';
};

const changePage = (page) => {
    filters.value.page = page;
    fetchTermsConditions();
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
    fetchTermsConditions();
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