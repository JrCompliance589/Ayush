<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import {
    AlertCircle,
    TrendingUp,
    TrendingDown,
    Clock,
    FileText,
    Filter,
    RefreshCw,
    Loader2,
    X,
    Eye,
    ChevronDown,
    ChevronUp,
    IndianRupee,
    Calendar,
    Building2,
    Receipt,
    CreditCard,
    Minus
} from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
    customerId: {
        type: [String, Number],
        default: null
    }
});

const apiBaseUrl = import.meta.env.VITE_BACKEND_API_URL;

const emit = defineEmits(['error']);

// State
const loading = ref(false);
const showModal = ref(false);
const showFilters = ref(false);
const selectedTab = ref('all');
const selectedException = ref(null);
const showExceptionDetail = ref(false);
const expandedSections = ref({
    summary: true,
    exceptions: true
});

// Filters
const filters = ref({
    page: 1,
    limit: 10,
    exception_type: '',
    amount_type: '',
    status: 'pending',
    customer_id: props.customerId || '',
    search: '',
    from_date: '',
    to_date: '',
    sort_by: 'created_at',
    sort_order: 'DESC'
});

// Data
const exceptionsData = ref({
    exceptions: [],
    pagination: {
        totalItems: 0,
        currentPage: 1,
        totalPages: 0,
        limit: 10
    }
});

const summaryData = ref({
    pending: { count: 0, total_amount: '0.00' },
    advance: { count: 0, total_amount: '0.00' },
    repayment: { count: 0, total_amount: '0.00' },
    net_amount: '0.00'
});

// Computed
const netAmountColor = computed(() => {
    const netAmount = parseFloat(summaryData.value.net_amount);
    if (netAmount > 0) return 'text-red-600';
    if (netAmount < 0) return 'text-green-600';
    return 'text-gray-600';
});

const totalExceptions = computed(() => {
    return summaryData.value.pending.count +
        summaryData.value.advance.count +
        summaryData.value.repayment.count;
});

// Methods
const formatCurrency = (value) => {
    if (!value && value !== 0) return '0.00';
    return new Intl.NumberFormat('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(Math.abs(value));
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-IN', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getExceptionTypeColor = (type) => {
    const colors = {
        pending: 'bg-orange-100 text-orange-700 border-orange-300',
        advance: 'bg-blue-100 text-blue-700 border-blue-300',
        repayment: 'bg-purple-100 text-purple-700 border-purple-300'
    };
    return colors[type] || colors.pending;
};

const getAmountTypeColor = (type) => {
    const colors = {
        excess: 'bg-green-100 text-green-700 border-green-300',
        short: 'bg-red-100 text-red-700 border-red-300',
        adjusted: 'bg-gray-100 text-gray-700 border-gray-300'
    };
    return colors[type] || colors.adjusted;
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-700 border-yellow-300',
        resolved: 'bg-green-100 text-green-700 border-green-300',
        cancelled: 'bg-gray-100 text-gray-700 border-gray-300'
    };
    return colors[status] || colors.pending;
};

const buildQueryParams = () => {
    const params = { ...filters.value };
    if (props.customerId) {
        params.customer_id = props.customerId;
    }
    const filtered = Object.entries(params).filter(([_, v]) => v !== '');
    return new URLSearchParams(filtered).toString();
};

const fetchSummary = async () => {
    try {
        const params = new URLSearchParams({
            status: filters.value.status,
            ...(filters.value.from_date && { from_date: filters.value.from_date }),
            ...(filters.value.to_date && { to_date: filters.value.to_date }),
            ...(props.customerId && { customer_id: props.customerId })
        });

        const response = await axios.get(`${apiBaseUrl}/payment-exceptions/summary?${params}`);
        const result =  response.data;

        if (result.success && result.data) {
            summaryData.value = result.data;
        }
    } catch (error) {
        console.error('Error fetching summary:', error);
        emit('error', { type: 'fetchSummary', error });
    }
};

const fetchExceptions = async () => {
    try {
        loading.value = true;
        const params = buildQueryParams();
        const response = await axios.get(`${apiBaseUrl}/payment-exceptions?${params}`);
        const result =  response.data;

        if (result.success && result.data) {
            exceptionsData.value = result.data;
        }
    } catch (error) {
        console.error('Error fetching exceptions:', error);
        emit('error', { type: 'fetchExceptions', error });
    } finally {
        loading.value = false;
    }
};

const fetchAllData = async () => {
    await Promise.all([fetchSummary(), fetchExceptions()]);
};

const applyFilters = () => {
    filters.value.page = 1;
    fetchAllData();
};

const resetFilters = () => {
    filters.value = {
        page: 1,
        limit: 10,
        exception_type: '',
        amount_type: '',
        status: 'pending',
        customer_id: props.customerId || '',
        search: '',
        from_date: '',
        to_date: '',
        sort_by: 'created_at',
        sort_order: 'DESC'
    };
    applyFilters();
};

const changePage = (page) => {
    filters.value.page = page;
    fetchExceptions();
};

const viewExceptionDetails = (exception) => {
    selectedException.value = exception;
    showExceptionDetail.value = true;
};

const closeExceptionDetail = () => {
    showExceptionDetail.value = false;
    selectedException.value = null;
};

const toggleSection = (section) => {
    expandedSections.value[section] = !expandedSections.value[section];
};

const openModal = () => {
    showModal.value = true;
    document.body.style.overflow = 'hidden';
    fetchAllData();
};

const closeModal = () => {
    showModal.value = false;
    document.body.style.overflow = '';
};

const viewSummaryDetails = (type) => {
    filters.value.exception_type = type;
    filters.value.page = 1;
    selectedTab.value = type;
    fetchExceptions();
};

// Lifecycle
onMounted(() => {
    fetchSummary();
});

watch(() => props.customerId, (newVal) => {
    if (newVal) {
        filters.value.customer_id = newVal;
        fetchSummary();
    }
});
</script>

<template>
    <!-- Main Card UI -->
 
        <!-- Net Amount -->
        <div class="relative bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg p-4 border border-gray-300">
            <div class="flex items-center justify-between mb-2">
                <div class="bg-gray-500 rounded-lg p-2">
                    <Minus class="w-4 h-4 text-white" />
                </div>
                <FileText class="w-4 h-4 text-gray-600" />
            </div>
            <p class="text-xs text-gray-700 font-medium mb-1">Exception Amount</p>
            <p :class="netAmountColor" class="text-lg font-bold">
                <!-- {{ parseFloat(summaryData.net_amount) >= 0 ? '+' : '-' }} -->
                ₹{{
                    formatCurrency(summaryData.net_amount) }}
            </p>
            <p class="text-xs text-gray-600 mt-1">{{ totalExceptions }} total</p>
            <!-- Header with Title and Button -->
            <div class="flex items-center justify-between absolute bottom-4 right-4">
                <button @click="openModal"
                    class="px-3 py-1.5 bg-[#ff5100] text-white text-xs rounded-md hover:bg-[#e64900] transition font-medium flex items-center gap-1.5 shadow-sm hover:shadow-md">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    View Details
                </button>
            </div>
        </div>

    <!-- Full Details Modal -->
    <transition enter-active-class="transition-opacity duration-300"
        leave-active-class="transition-opacity duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            @click.self="closeModal">
            <transition enter-active-class="transition-all duration-300 ease-out"
                leave-active-class="transition-all duration-200 ease-in" enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100" leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95">
                <div v-if="showModal"
                    class="bg-white rounded-xl shadow-2xl w-full max-w-7xl max-h-[90vh] overflow-hidden flex flex-col">
                    <!-- Modal Header -->
                    <div
                        class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-[#ff5100] to-[#ff7340]">
                        <div class="flex items-center gap-3">
                            <div class="bg-white/20 backdrop-blur-sm rounded-lg p-2">
                                <AlertCircle class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">Payment Exceptions Details</h2>
                                <p class="text-white/80 text-sm mt-0.5">
                                    {{ customerId ? 'Customer Specific' : 'All Customers' }}
                                </p>
                            </div>
                        </div>
                        <button @click="closeModal" class="p-2 hover:bg-white/20 rounded-lg transition text-white">
                            <X class="w-6 h-6" />
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="flex-1 overflow-y-auto p-6 bg-gray-50">
                        <!-- Loading State -->
                        <div v-if="loading && exceptionsData.exceptions.length === 0"
                            class="flex justify-center items-center py-12">
                            <Loader2 class="w-8 h-8 animate-spin text-[#ff5100]" />
                            <span class="ml-3 text-gray-600 font-medium">Loading payment exceptions...</span>
                        </div>

                        <!-- Content -->
                        <div v-else>
                            <!-- Filters -->
                            <div class="bg-white rounded-xl shadow-sm mb-6 border border-gray-200">
                                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
                                    <button @click="showFilters = !showFilters"
                                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                        <Filter class="w-4 h-4" />
                                        {{ showFilters ? 'Hide' : 'Show' }} Filters
                                    </button>
                                </div>
                                <transition enter-active-class="transition-all duration-300"
                                    leave-active-class="transition-all duration-200"
                                    enter-from-class="opacity-0 max-h-0" enter-to-class="opacity-100 max-h-96"
                                    leave-from-class="opacity-100 max-h-96" leave-to-class="opacity-0 max-h-0">
                                    <div v-show="showFilters" class="p-6 bg-gray-50">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Exception
                                                    Type</label>
                                                <select v-model="filters.exception_type"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5100] focus:border-[#ff5100]">
                                                    <option value="">All Types</option>
                                                    <option value="pending">Pending</option>
                                                    <option value="advance">Advance</option>
                                                    <option value="repayment">Repayment</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Amount
                                                    Type</label>
                                                <select v-model="filters.amount_type"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5100] focus:border-[#ff5100]">
                                                    <option value="">All</option>
                                                    <option value="excess">Excess</option>
                                                    <option value="short">Short</option>
                                                    <option value="adjusted">Adjusted</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                                <select v-model="filters.status"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5100] focus:border-[#ff5100]">
                                                    <option value="">All</option>
                                                    <option value="pending">Pending</option>
                                                    <option value="resolved">Resolved</option>
                                                    <option value="cancelled">Cancelled</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                                                <input type="text" v-model="filters.search"
                                                    placeholder="Invoice, Customer..."
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5100] focus:border-[#ff5100]" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Start
                                                    Date</label>
                                                <input type="date" v-model="filters.from_date"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5100] focus:border-[#ff5100]" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">End
                                                    Date</label>
                                                <input type="date" v-model="filters.to_date"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5100] focus:border-[#ff5100]" />
                                            </div>
                                            <div class="lg:col-span-2 flex items-end gap-2">
                                                <button @click="applyFilters"
                                                    class="flex-1 px-4 py-2 bg-[#ff5100] text-white rounded-lg hover:bg-[#e64900] transition font-medium">
                                                    Apply Filters
                                                </button>
                                                <button @click="resetFilters"
                                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                                                    title="Reset">
                                                    <RefreshCw class="w-5 h-5" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </transition>
                            </div>

                            <!-- Summary Cards -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Summary Overview</h3>
                                    <button @click="toggleSection('summary')" class="text-gray-500 hover:text-gray-700">
                                        <ChevronDown v-if="expandedSections.summary" class="w-5 h-5" />
                                        <ChevronUp v-else class="w-5 h-5" />
                                    </button>
                                </div>
                                <transition enter-active-class="transition-all duration-300"
                                    leave-active-class="transition-all duration-200"
                                    enter-from-class="opacity-0 -translate-y-2"
                                    enter-to-class="opacity-100 translate-y-0"
                                    leave-from-class="opacity-100 translate-y-0"
                                    leave-to-class="opacity-0 -translate-y-2">
                                    <div v-show="expandedSections.summary"
                                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                        <!-- Pending -->
                                        <div @click="viewSummaryDetails('pending')"
                                            class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 border border-orange-200 cursor-pointer hover:shadow-lg transition">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="bg-orange-500 rounded-lg p-2.5">
                                                    <Clock class="w-5 h-5 text-white" />
                                                </div>
                                                <TrendingDown class="w-5 h-5 text-orange-600" />
                                            </div>
                                            <p class="text-sm text-orange-700 font-medium mb-1">Pending Payments</p>
                                            <p class="text-2xl font-bold text-orange-900">₹{{
                                                formatCurrency(summaryData.pending.total_amount) }}</p>
                                            <p class="text-xs text-orange-600 mt-2">{{ summaryData.pending.count }}
                                                exceptions</p>
                                        </div>

                                        <!-- Advance -->
                                        <div @click="viewSummaryDetails('advance')"
                                            class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200 cursor-pointer hover:shadow-lg transition">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="bg-blue-500 rounded-lg p-2.5">
                                                    <TrendingUp class="w-5 h-5 text-white" />
                                                </div>
                                                <IndianRupee class="w-5 h-5 text-blue-600" />
                                            </div>
                                            <p class="text-sm text-blue-700 font-medium mb-1">Advance Payments</p>
                                            <p class="text-2xl font-bold text-blue-900">₹{{
                                                formatCurrency(summaryData.advance.total_amount) }}</p>
                                            <p class="text-xs text-blue-600 mt-2">{{ summaryData.advance.count }}
                                                exceptions</p>
                                        </div>

                                        <!-- Repayment -->
                                        <div @click="viewSummaryDetails('repayment')"
                                            class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200 cursor-pointer hover:shadow-lg transition">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="bg-purple-500 rounded-lg p-2.5">
                                                    <IndianRupee class="w-5 h-5 text-white" />
                                                </div>
                                                <Receipt class="w-5 h-5 text-purple-600" />
                                            </div>
                                            <p class="text-sm text-purple-700 font-medium mb-1">Repayments</p>
                                            <p class="text-2xl font-bold text-purple-900">₹{{
                                                formatCurrency(summaryData.repayment.total_amount) }}</p>
                                            <p class="text-xs text-purple-600 mt-2">{{ summaryData.repayment.count }}
                                                exceptions</p>
                                        </div>

                                        <!-- Net Amount -->
                                        <div
                                            class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="bg-gray-500 rounded-lg p-2.5">
                                                    <Minus class="w-5 h-5 text-white" />
                                                </div>
                                                <FileText class="w-5 h-5 text-gray-600" />
                                            </div>
                                            <p class="text-sm text-gray-700 font-medium mb-1">Net Amount</p>
                                            <p :class="netAmountColor" class="text-2xl font-bold">
                                                <!-- {{ parseFloat(summaryData.net_amount) >= 0 ? '+' : '-' }} -->
                                                ₹{{
                                                    formatCurrency(summaryData.net_amount) }}
                                            </p>
                                            <p class="text-xs text-gray-600 mt-2">Pending - Advance</p>
                                        </div>
                                    </div>
                                </transition>
                            </div>

                            <!-- Exceptions List -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Exception History</h3>
                                    <button @click="toggleSection('exceptions')"
                                        class="text-gray-500 hover:text-gray-700">
                                        <ChevronDown v-if="expandedSections.exceptions" class="w-5 h-5" />
                                        <ChevronUp v-else class="w-5 h-5" />
                                    </button>
                                </div>
                                <transition enter-active-class="transition-all duration-300"
                                    leave-active-class="transition-all duration-200"
                                    enter-from-class="opacity-0 -translate-y-2"
                                    enter-to-class="opacity-100 translate-y-0"
                                    leave-from-class="opacity-100 translate-y-0"
                                    leave-to-class="opacity-0 -translate-y-2">
                                    <div v-show="expandedSections.exceptions">
                                        <div v-if="exceptionsData.exceptions.length === 0"
                                            class="bg-white rounded-xl p-12 text-center border border-gray-200">
                                            <AlertCircle class="w-16 h-16 mx-auto mb-4 text-gray-300" />
                                            <p class="text-gray-500 text-lg">No payment exceptions found</p>
                                        </div>
                                        <div v-else
                                            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Exception Details</th>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Customer</th>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Invoice</th>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Amounts</th>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Type</th>
                                                            <!-- <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Status</th> -->
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        <tr v-for="exception in exceptionsData.exceptions"
                                                            :key="exception.id" class="hover:bg-gray-50 transition">
                                                            <td class="px-6 py-4">
                                                                <div class="text-sm font-medium text-gray-900">#{{
                                                                    exception.id }}</div>
                                                                <div class="text-xs text-gray-500">{{
                                                                    formatDate(exception.paid_at) }}</div>
                                                            </td>
                                                            <td class="px-6 py-4">
                                                                <div class="text-sm font-medium text-gray-900">{{
                                                                    exception.customer_name || 'N/A' }}</div>
                                                                <div class="text-xs text-gray-500">ID: {{
                                                                    exception.customer_id }}</div>
                                                            </td>
                                                            <td class="px-6 py-4">
                                                                <div class="text-sm font-medium text-gray-900">{{
                                                                    exception.proforma_no || 'N/A' }}</div>
                                                                <div class="text-xs text-gray-500">{{
                                                                    exception.quotation_no }}</div>
                                                            </td>
                                                            <td class="px-6 py-4">
                                                                <div class="text-sm">
                                                                    <div class="text-gray-600">Invoice: ₹{{
                                                                        formatCurrency(exception.invoice_amount) }}
                                                                    </div>
                                                                    <div class="text-green-600">Paid: ₹{{
                                                                        formatCurrency(exception.paid_amount) }}</div>
                                                                    <div :class="exception.amount_type === 'excess' ? 'text-green-600' : 'text-red-600'"
                                                                        class="font-semibold">
                                                                        Exception: {{ exception.amount_type === 'excess'
                                                                            ? '+' : '-' }}₹{{
                                                                            formatCurrency(exception.exception_amount) }}
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <div class="flex flex-col gap-1">
                                                                    <!-- <span
                                                                        :class="getExceptionTypeColor(exception.exception_type)"
                                                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border">
                                                                        {{ exception.exception_type }}
                                                                    </span> -->
                                                                    <span v-if="exception.amount_type"
                                                                        :class="getAmountTypeColor(exception.amount_type)"
                                                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border">
                                                                        {{ exception.amount_type }}
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <!-- <td class="px-6 py-4 whitespace-nowrap">
                                                                <span :class="getStatusColor(exception.status)"
                                                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border">
                                                                    {{ exception.status }}
                                                                </span>
                                                            </td> -->
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                                <button @click="viewExceptionDetails(exception)"
                                                                    class="text-[#ff5100] hover:text-[#e64900] transition flex items-center gap-1"
                                                                    title="View Details">
                                                                    <Eye class="w-5 h-5" />
                                                                    <span class="text-sm">View</span>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Pagination -->
                                            <div v-if="exceptionsData.pagination.totalPages > 1"
                                                class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                                                <div class="flex items-center justify-between">
                                                    <div class="text-sm text-gray-700">
                                                        Showing page {{ exceptionsData.pagination.currentPage }} of {{
                                                            exceptionsData.pagination.totalPages }}
                                                        ({{ exceptionsData.pagination.totalItems }} total)
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <button
                                                            @click="changePage(exceptionsData.pagination.currentPage - 1)"
                                                            :disabled="exceptionsData.pagination.currentPage === 1"
                                                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition">
                                                            Previous
                                                        </button>
                                                        <button
                                                            v-for="page in Math.min(exceptionsData.pagination.totalPages, 5)"
                                                            :key="page" @click="changePage(page)"
                                                            :class="page === exceptionsData.pagination.currentPage ? 'bg-[#ff5100] text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium transition">
                                                            {{ page }}
                                                        </button>
                                                        <button
                                                            @click="changePage(exceptionsData.pagination.currentPage + 1)"
                                                            :disabled="exceptionsData.pagination.currentPage === exceptionsData.pagination.totalPages"
                                                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition">
                                                            Next
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </transition>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </transition>

    <!-- Exception Detail Modal -->
    <transition enter-active-class="transition-opacity duration-300"
        leave-active-class="transition-opacity duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showExceptionDetail"
            class="fixed inset-0 bg-black bg-opacity-60 z-[60] flex items-center justify-center p-4"
            @click.self="closeExceptionDetail">
            <transition enter-active-class="transition-all duration-300 ease-out"
                leave-active-class="transition-all duration-200 ease-in" enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100" leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95">
                <div v-if="showExceptionDetail && selectedException"
                    class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[85vh] overflow-hidden flex flex-col">
                    <!-- Header -->
                    <div
                        class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-[#ff5100] to-[#ff7340]">
                        <div>
                            <h3 class="text-xl font-bold text-white">Exception Details</h3>
                            <p class="text-white/80 text-sm">Exception #{{ selectedException.id }}</p>
                        </div>
                        <button @click="closeExceptionDetail"
                            class="p-2 hover:bg-white/20 rounded-lg transition text-white">
                            <X class="w-6 h-6" />
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="flex-1 overflow-y-auto p-6">
                        <!-- Exception Summary -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                    <FileText class="w-4 h-4 text-[#ff5100]" />
                                    Exception Information
                                </h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Exception ID:</span>
                                        <span class="text-sm font-medium text-gray-900">#{{ selectedException.id
                                            }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Type:</span>
                                        <span :class="getExceptionTypeColor(selectedException.exception_type)"
                                            class="text-xs px-2 py-1 rounded-full border font-semibold">
                                            {{ selectedException.exception_type }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Amount Type:</span>
                                        <span :class="getAmountTypeColor(selectedException.amount_type)"
                                            class="text-xs px-2 py-1 rounded-full border font-semibold">
                                            {{ selectedException.amount_type || 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Status:</span>
                                        <span :class="getStatusColor(selectedException.status)"
                                            class="text-xs px-2 py-1 rounded-full border font-semibold">
                                            {{ selectedException.status }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Payment Date:</span>
                                        <span class="text-sm font-medium text-gray-900">{{
                                            formatDate(selectedException.paid_at) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                    <Building2 class="w-4 h-4 text-[#ff5100]" />
                                    Customer Information
                                </h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Customer ID:</span>
                                        <span class="text-sm font-medium text-gray-900">{{ selectedException.customer_id
                                            }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Name:</span>
                                        <span class="text-sm font-medium text-gray-900">{{
                                            selectedException.customer_name || 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Invoice:</span>
                                        <span class="text-sm font-medium text-gray-900">{{
                                            selectedException.invoice_number || 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Quotation:</span>
                                        <span class="text-sm font-medium text-gray-900">{{
                                            selectedException.quotation_no || 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Proforma:</span>
                                        <span class="text-sm font-medium text-gray-900">{{ selectedException.proforma_no
                                            || 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        <div
                            class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 mb-6 border border-blue-200">
                            <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <IndianRupee class="w-5 h-5 text-[#ff5100]" />
                                Payment Breakdown
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white rounded-lg p-4">
                                    <p class="text-xs text-gray-600 mb-1">Invoice Amount</p>
                                    <p class="text-xl font-bold text-gray-900">₹{{
                                        formatCurrency(selectedException.invoice_amount) }}</p>
                                </div>
                                <div class="bg-white rounded-lg p-4">
                                    <p class="text-xs text-gray-600 mb-1">Paid Amount</p>
                                    <p class="text-xl font-bold text-green-600">₹{{
                                        formatCurrency(selectedException.paid_amount) }}</p>
                                </div>
                                <div class="bg-white rounded-lg p-4">
                                    <p class="text-xs text-gray-600 mb-1">Exception Amount</p>
                                    <p :class="selectedException.amount_type === 'excess' ? 'text-green-600' : 'text-red-600'"
                                        class="text-xl font-bold">
                                        {{ selectedException.amount_type === 'excess' ? '+' : '-' }}₹{{
                                            formatCurrency(selectedException.exception_amount) }}
                                    </p>
                                </div>
                            </div>

                            <!-- TDS Details -->
                            <div v-if="selectedException.tds_amount > 0"
                                class="mt-4 pt-4 border-t border-blue-200 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-xs text-gray-600">TDS Rate</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ selectedException.tds_rate }}%</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">TDS Amount</p>
                                    <p class="text-sm font-semibold text-gray-900">₹{{
                                        formatCurrency(selectedException.tds_amount) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Net Received</p>
                                    <p class="text-sm font-semibold text-gray-900">₹{{
                                        formatCurrency(selectedException.net_received) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method Details -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <CreditCard class="w-4 h-4 text-[#ff5100]" />
                                Payment Method Details
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <p class="text-xs text-gray-600">Method</p>
                                    <p class="text-sm font-medium text-gray-900">{{ selectedException.payment_method ||
                                        'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Mode</p>
                                    <p class="text-sm font-medium text-gray-900">{{ selectedException.payment_mode ||
                                        'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Reference</p>
                                    <p class="text-sm font-medium text-gray-900">{{ selectedException.payment_reference
                                        || 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Service Type</p>
                                    <p class="text-sm font-medium text-gray-900">{{ selectedException.service_type ||
                                        'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Exception Reason -->
                        <div v-if="selectedException.exception_reason"
                            class="bg-orange-50 rounded-lg p-4 mb-6 border border-orange-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <AlertCircle class="w-4 h-4 text-orange-600" />
                                Exception Reason
                            </h4>
                            <p class="text-sm text-gray-700">{{ selectedException.exception_reason }}</p>
                        </div>

                        <!-- Resolution Details -->
                        <div v-if="selectedException.status !== 'pending'"
                            class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <Calendar class="w-4 h-4 text-green-600" />
                                Resolution Details
                            </h4>
                            <div class="space-y-2">
                                <div v-if="selectedException.resolved_at" class="flex justify-between">
                                    <span class="text-sm text-gray-600">Resolved At:</span>
                                    <span class="text-sm font-medium text-gray-900">{{
                                        formatDate(selectedException.resolved_at) }}</span>
                                </div>
                                <div v-if="selectedException.resolved_by" class="flex justify-between">
                                    <span class="text-sm text-gray-600">Resolved By:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ selectedException.resolved_by
                                        }}</span>
                                </div>
                                <div v-if="selectedException.resolution_notes">
                                    <p class="text-xs text-gray-600 mb-1">Notes:</p>
                                    <p class="text-sm text-gray-700">{{ selectedException.resolution_notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </transition>
</template>