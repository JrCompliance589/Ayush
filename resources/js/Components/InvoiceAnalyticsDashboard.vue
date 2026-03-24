<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, watch } from 'vue';
import {
    FileText,
    CheckCircle,
    AlertCircle,
    CreditCard,
    RefreshCw,
    Loader2,
    Filter,
    IndianRupeeIcon,
    X,
    BarChart3
} from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';
import Chart from 'chart.js/auto';
import PaymentExceptionCard from './PaymentExceptionCard.vue';
import PaymentBalanceDashboard from './PaymentBalanceDashboard.vue';
import axios from 'axios';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false
    }
});

const apiBaseUrl = import.meta.env.VITE_BACKEND_API_URL;
const phpApiBaseUrl = import.meta.env.VITE_PHP_API_URL;

const page = usePage();
const isAdmin = page?.props?.auth?.user?.role === 'admin';

const emit = defineEmits(['dataLoaded', 'error', 'close']);

// State
const loading = ref(false);
const showGlobalFilters = ref(true);
const showStatsFilters = ref(false);

// Global filters (applied to all sections)
const globalFilters = ref({
    startDate: '',
    endDate: '',
    createdBy: ''
});

// Stats card specific filters
const statsFilters = ref({
    paymentStatus: '',
    platformChargeType: ''
});

// Revenue trend specific filter
const revenueTrendGroupBy = ref('day');

// Data - UPDATED FOR NEW API
const dashboardStats = ref({
    totalInvoiced: 0,
    totalCollected: 0,
    totalOutstanding: 0,
    totalGST: 0,
    totalDiscount: 0,
    paymentStatus: {
        paid: { count: 0, amount: 0 },
        partial: { count: 0, amount: 0 },
        unpaid: { count: 0, amount: 0 },
        expired: { count: 0, amount: 0 }
    }
});

const teamMembers = ref([]);

const revenueTrend = ref([]);
const paymentMethods = ref([]);
const topCustomers = ref([]);
const discountAnalysis = ref({
    discountedInvoices: 0,
    totalDiscount: 0,
    avgDiscountPercent: 0
});

// Chart refs
const revenueTrendChart = ref(null);
const paymentMethodChart = ref(null);

const charts = ref({
    revenueTrend: null,
    paymentMethod: null
});

// Methods
const closeModal = () => {
    // Destroy charts BEFORE emitting close
    destroyCharts();
    emit('close');
    document.body.style.overflow = '';
};

const formatCurrency = (value) => {
    if (!value && value !== 0) return '0.00';
    return new Intl.NumberFormat('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value);
};

const buildQueryParams = (additionalParams = {}) => {
    const params = { ...globalFilters.value, ...additionalParams, ...(!isAdmin ? { createdBy: page?.props?.auth?.user?.id } : {}) };
    const filtered = Object.entries(params).filter(([_, v]) => v !== '');
    return new URLSearchParams(filtered).toString();
};

// UPDATED: Fetch Dashboard Stats for new API
const fetchDashboardStats = async (flag) => {
    try {
        loading.value = true;
        const params = buildQueryParams(statsFilters.value);
        const response = await axios.get(`${apiBaseUrl}/analytics/dashboard/stats?${params}`);
        const result =  response.data;

        if (result.success || result.data) {
            dashboardStats.value = result.data || result;

            if (flag) {
                emit('dataLoaded', {
                    stats: dashboardStats.value,
                    revenueTrend: revenueTrend.value,
                    paymentMethods: paymentMethods.value,
                    topCustomers: topCustomers.value,
                    discountAnalysis: discountAnalysis.value
                });

                await nextTick();

                setTimeout(() => {
                    if (revenueTrend.value?.length) renderRevenueTrendChart();
                    if (paymentMethods.value?.length) renderPaymentMethodChart();
                }, 100);
            }
        }
    } catch (error) {
        console.error('Error fetching stats:', error);
        emit('error', { type: 'stats', error });
    } finally {
        loading.value = false;
    }
};

const fetchTeam = async () => {
    try {
        const response = await axios.get(`${phpApiBaseUrl}/api/admin/team`);
        const result =  response.data;
        if (response.ok) {
            teamMembers.value = result.data || [];
            return;
        }
        throw new Error(result.message || 'Failed to fetch team');
    } catch (err) {
        console.error(err.message || 'Error fetching team');
    }
}

// UPDATED: Fetch Revenue Trend
const fetchRevenueTrend = async () => {
    try {
        const params = buildQueryParams({ groupBy: revenueTrendGroupBy.value });
        const response = await axios.get(`${apiBaseUrl}/analytics/revenue/trend?${params}`);
        const result =  response.data;

        if (result.success || result.data) {
            revenueTrend.value = result.data || result;
        }
    } catch (error) {
        console.error('Error fetching revenue trend:', error);
        emit('error', { type: 'revenueTrend', error });
    }
};

// UPDATED: Fetch Payment Methods (now from payment_ledger)
const fetchPaymentMethods = async () => {
    try {
        const params = buildQueryParams();
        const response = await axios.get(`${apiBaseUrl}/analytics/payment-methods?${params}`);
        const result =  response.data;

        if (result.success || result.data) {
            paymentMethods.value = result.data || result;
        }
    } catch (error) {
        console.error('Error fetching payment methods:', error);
        emit('error', { type: 'paymentMethods', error });
    }
};

// UPDATED: Fetch Top Customers (now by paid_amount and created_for_id)
const fetchTopCustomers = async () => {
    try {
        const params = buildQueryParams({ limit: 10 });
        const response = await axios.get(`${apiBaseUrl}/analytics/top-customers?${params}`);
        const result =  response.data;

        if (result.success || result.data) {
            topCustomers.value = result.data || result;
        }
    } catch (error) {
        console.error('Error fetching top customers:', error);
        emit('error', { type: 'topCustomers', error });
    }
};

// UPDATED: Fetch Discount Analysis
const fetchDiscountAnalysis = async () => {
    try {
        const params = buildQueryParams();
        const response = await  axios.get(`${apiBaseUrl}/analytics/discounts?${params}`);
        const result =  response.data;

        if (result.success || result.data) {
            discountAnalysis.value = result.data || result;
        }
    } catch (error) {
        console.error('Error fetching discount analysis:', error);
        emit('error', { type: 'discountAnalysis', error });
    }
};

const fetchAllData = async () => {
    loading.value = true;

    try {
        await Promise.all([
            ...(isAdmin ? [fetchTeam()] : [Promise.resolve()]),
            fetchDashboardStats(false),
            fetchRevenueTrend(),
            fetchPaymentMethods(),
            fetchTopCustomers(),
            fetchDiscountAnalysis()
        ]);

        emit('dataLoaded', {
            stats: dashboardStats.value,
            revenueTrend: revenueTrend.value,
            paymentMethods: paymentMethods.value,
            topCustomers: topCustomers.value,
            discountAnalysis: discountAnalysis.value
        });

        loading.value = false;

        // Render charts after data is loaded and DOM is updated
        await nextTick();
        renderCharts();

    } catch (error) {
        loading.value = false;
        emit('error', { type: 'fetchAll', error });
    }
};

// Single function to render all charts
const renderCharts = async () => {
    // Wait for next tick to ensure DOM is ready
    await nextTick();

    // Small delay to ensure canvas elements are fully mounted
    setTimeout(() => {
        if (revenueTrend.value?.length) {
            renderRevenueTrendChart();
        }
        if (paymentMethods.value?.length) {
            renderPaymentMethodChart();
        }
    }, 50);
};

// UPDATED: Render Revenue Trend Chart (collected vs invoiced)
const renderRevenueTrendChart = () => {
    // Destroy existing chart first
    if (charts.value.revenueTrend) {
        try {
            charts.value.revenueTrend.destroy();
        } catch (e) {
            console.warn('Error destroying revenue trend chart:', e);
        }
        charts.value.revenueTrend = null;
    }

    // Validate canvas ref exists and has getContext
    if (!revenueTrendChart.value) {
        console.warn('Revenue trend canvas ref not available');
        return;
    }

    if (!revenueTrend.value?.length) {
        console.warn('No revenue trend data to render');
        return;
    }

    try {
        const ctx = revenueTrendChart.value.getContext('2d');
        if (!ctx) {
            console.error('Could not get 2d context for revenue trend chart');
            return;
        }

        charts.value.revenueTrend = new Chart(ctx, {
            type: 'line',
            data: {
                labels: revenueTrend.value.map(item => item.period || ''),
                datasets: [
                    {
                        label: 'Collected Revenue',
                        data: revenueTrend.value.map(item => item.revenue || 0),
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Total Invoiced',
                        data: revenueTrend.value.map(item => item.invoiced || 0),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += '₹' + formatCurrency(context.parsed.y);
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return '₹' + formatCurrency(value);
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error rendering revenue trend chart:', error);
        emit('error', { type: 'chartRender', error });
    }
};

// UPDATED: Render Payment Method Chart (now from payment_ledger)
const renderPaymentMethodChart = () => {
    // Destroy existing chart first
    if (charts.value.paymentMethod) {
        try {
            charts.value.paymentMethod.destroy();
        } catch (e) {
            console.warn('Error destroying payment method chart:', e);
        }
        charts.value.paymentMethod = null;
    }

    // Validate canvas ref exists and has getContext
    if (!paymentMethodChart.value) {
        console.warn('Payment method canvas ref not available');
        return;
    }

    if (!paymentMethods.value?.length) {
        console.warn('No payment method data to render');
        return;
    }

    try {
        const ctx = paymentMethodChart.value.getContext('2d');
        if (!ctx) {
            console.error('Could not get 2d context for payment method chart');
            return;
        }

        const colors = [
            'rgba(59, 130, 246, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(245, 158, 11, 0.8)',
            'rgba(139, 92, 246, 0.8)',
            'rgba(239, 68, 68, 0.8)'
        ];

        charts.value.paymentMethod = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: paymentMethods.value.map(item => item.method || 'Unknown'),
                datasets: [{
                    label: 'Payment Amount',
                    data: paymentMethods.value.map(item => item.totalAmount || 0),
                    backgroundColor: colors,
                    borderColor: colors.map(c => c.replace('0.8', '1')),
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const method = context.label || '';
                                const value = context.parsed || 0;
                                const count = paymentMethods.value[context.dataIndex]?.count || 0;
                                return `${method}: ₹${formatCurrency(value)} (${count} transactions)`;
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error rendering payment method chart:', error);
        emit('error', { type: 'chartRender', error });
    }
};

const resetGlobalFilters = () => {
    globalFilters.value = {
        startDate: '',
        endDate: '',
        createdBy: ''
    };
    applyGlobalFilters();
};

const resetStatsFilters = () => {
    statsFilters.value = {
        paymentStatus: '',
        platformChargeType: ''
    };
    applyStatsFilters();
};

const applyGlobalFilters = () => {
    fetchAllData();
};

const applyStatsFilters = () => {
    fetchDashboardStats(true);
};

const destroyCharts = () => {
    if (charts.value.revenueTrend) {
        try {
            charts.value.revenueTrend.destroy();
        } catch (e) {
            console.warn('Error destroying revenue trend chart:', e);
        }
        charts.value.revenueTrend = null;
    }
    if (charts.value.paymentMethod) {
        try {
            charts.value.paymentMethod.destroy();
        } catch (e) {
            console.warn('Error destroying payment method chart:', e);
        }
        charts.value.paymentMethod = null;
    }
};

// UPDATED: Computed for collection rate
const collectionRate = () => {
    if (!dashboardStats.value.totalInvoiced) return '0.00';
    const rate = (dashboardStats.value.totalCollected / dashboardStats.value.totalInvoiced) * 100;
    return rate.toFixed(2);
};

// Lifecycle - Fetch data when modal opens
onMounted(() => {
    if (props.isOpen) {
        document.body.style.overflow = 'hidden';
        fetchAllData();
    }
});

onBeforeUnmount(() => {
    destroyCharts();
    document.body.style.overflow = '';
});

// Watch for revenue trend group change
watch(revenueTrendGroupBy, async () => {
    await fetchRevenueTrend();
    await nextTick();
    renderRevenueTrendChart();
});

// Watch for modal open/close - THIS IS THE KEY FIX
watch(() => props.isOpen, async (newVal) => {
    if (newVal) {
        document.body.style.overflow = 'hidden';

        // Always destroy old charts when opening
        destroyCharts();

        // Fetch data if not already loaded
        if (!dashboardStats.value.totalInvoiced) {
            await fetchAllData();
        } else {
            // If data exists, just re-render charts after DOM update
            await nextTick();
            renderCharts();
        }

    } else {
        // Destroy charts when closing
        destroyCharts();
        document.body.style.overflow = '';
    }
});

// Expose methods
defineExpose({
    fetchAllData,
    resetGlobalFilters,
    resetStatsFilters
});
</script>

<template>
    <!-- Modal Overlay -->
    <transition enter-active-class="transition-opacity duration-300"
        leave-active-class="transition-opacity duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            @click.self="closeModal">
            <!-- Modal Content -->
            <transition enter-active-class="transition-all duration-300 ease-out"
                leave-active-class="transition-all duration-200 ease-in"
                enter-from-class="opacity-0 scale-95 translate-y-4" enter-to-class="opacity-100 scale-100 translate-y-0"
                leave-from-class="opacity-100 scale-100 translate-y-0"
                leave-to-class="opacity-0 scale-95 translate-y-4">
                <div v-if="isOpen"
                    class="bg-white rounded-xl shadow-2xl w-full max-w-7xl max-h-[90vh] overflow-hidden flex flex-col">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                            <BarChart3 class="w-6 h-6 text-primary" />
                            Invoice Analytics Dashboard
                        </h2>
                        <button @click="closeModal"
                            class="p-2 hover:bg-gray-200 rounded-lg transition text-gray-600 hover:text-gray-900">
                            <X class="w-6 h-6" />
                        </button>
                    </div>

                    <!-- Modal Body - Scrollable -->
                    <div class="flex-1 overflow-y-auto p-6 bg-gray-50">
                        <!-- Global Filters Section -->
                        <div class="bg-white rounded-lg shadow-sm mb-6">
                            <div class="p-4 border-b border-gray-200 flex items-center justify-between flex-wrap gap-4">
                                <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
                                <button @click="showGlobalFilters = !showGlobalFilters"
                                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                    <Filter class="w-4 h-4" />
                                    {{ showGlobalFilters ? 'Hide' : 'Show' }} Filters
                                </button>
                            </div>

                            <!-- Global Filters -->
                            <transition enter-active-class="transition-all duration-300 ease-out"
                                leave-active-class="transition-all duration-200 ease-in"
                                enter-from-class="opacity-0 max-h-0" enter-to-class="opacity-100 max-h-96"
                                leave-from-class="opacity-100 max-h-96" leave-to-class="opacity-0 max-h-0">
                                <div v-show="showGlobalFilters" class="p-6 bg-gray-50 border-b border-gray-200">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Start
                                                Date</label>
                                            <input type="date" v-model="globalFilters.startDate"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                                            <input type="date" v-model="globalFilters.endDate"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                        </div>
                                        <div v-if="isAdmin">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Team
                                                Member</label>
                                            <select v-model="globalFilters.createdBy"
                                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">All</option>
                                                <option v-for="member in teamMembers" :key="member.id"
                                                    :value="member.id">
                                                    {{ member.full_name || member.first_name }} - {{ member.role }}
                                                </option>
                                            </select>
                                        </div>
                                        <div :class="isAdmin ? '' : 'col-span-2'">
                                            <div class="w-fit flex items-end justify-end gap-2 ml-auto">
                                                <button @click="applyGlobalFilters"
                                                    class="flex-1 px-4 py-2 bg-primary/90 text-white rounded-lg hover:bg-primary transition font-medium">
                                                    Apply Filters
                                                </button>
                                                <button @click="resetGlobalFilters"
                                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                                                    title="Reset Filters">
                                                    <RefreshCw class="w-4 h-6" />
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </transition>
                        </div>

                        <!-- Loading State -->
                        <div v-if="loading" class="flex justify-center items-center py-12">
                            <Loader2 class="w-8 h-8 animate-spin text-blue-500" />
                            <span class="ml-3 text-gray-600 font-medium">Loading dashboard data...</span>
                        </div>

                        <!-- Dashboard Content -->
                        <div v-else>
                            <!-- Stats Cards Section -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                                    <h3 class="text-lg font-semibold text-gray-900">Revenue Statistics</h3>
                                    <button @click="showStatsFilters = !showStatsFilters"
                                        class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-600 bg-white rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                                        <Filter class="w-4 h-4" />
                                        Filters
                                    </button>
                                </div>

                                <!-- Stats Filters -->
                                <transition enter-active-class="transition-all duration-300 ease-out"
                                    leave-active-class="transition-all duration-200 ease-in"
                                    enter-from-class="opacity-0 -translate-y-2"
                                    enter-to-class="opacity-100 translate-y-0"
                                    leave-from-class="opacity-100 translate-y-0"
                                    leave-to-class="opacity-0 -translate-y-2">
                                    <div v-show="showStatsFilters"
                                        class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-blue-200">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Payment
                                                    Status</label>
                                                <select v-model="statsFilters.paymentStatus"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                    <option value="">All</option>
                                                    <option value="paid">Paid</option>
                                                    <option value="partial">Partial</option>
                                                    <option value="unpaid">Unpaid</option>
                                                    <option value="expired">Expired</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Platform
                                                    Charge Type</label>
                                                <select v-model="statsFilters.platformChargeType"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                    <option value="">All</option>
                                                    <option value="Monthly">Monthly</option>
                                                    <option value="Yearly">Yearly</option>
                                                </select>
                                            </div>
                                            <div class="col-span-2 pt-6">
                                                <div class="w-fit flex items-center justify-end gap-2 ml-auto">
                                                    <button @click="applyStatsFilters"
                                                        class="flex-1 px-4 py-2 bg-primary/90 text-white rounded-lg hover:bg-primary transition font-medium">
                                                        Apply Filters
                                                    </button>
                                                    <button @click="resetStatsFilters"
                                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                                                        title="Clear Filters">
                                                        <RefreshCw class="w-4 h-5" />
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </transition>

                                <!-- Stats Cards Grid - UPDATED -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4">
                                    <!-- Total Invoiced -->
                                    <div class="group relative">
                                        <div
                                            class="absolute -inset-0.5 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                                        </div>
                                        <div
                                            class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-blue-300 transition-all duration-300 shadow-md hover:shadow-lg hover:shadow-blue-500/10">
                                            <div class="flex justify-between items-start mb-4">
                                                <div
                                                    class="bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl p-3 shadow-md">
                                                    <FileText class="w-6 h-6 text-white" />
                                                </div>
                                            </div>
                                            <p class="text-gray-600 text-sm font-medium mb-1">Total Invoiced</p>
                                            <p class="text-gray-900 text-3xl font-bold mb-3">
                                                ₹{{ formatCurrency(dashboardStats.totalInvoiced) }}
                                            </p>
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="text-green-600">GST: ₹{{
                                                    formatCurrency(dashboardStats.totalGST) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Total Collected -->
                                    <div class="group relative">
                                        <div
                                            class="absolute -inset-0.5 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                                        </div>
                                        <div
                                            class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-green-300 transition-all duration-300 shadow-md hover:shadow-lg hover:shadow-green-500/10">
                                            <div class="flex justify-between items-start mb-4">
                                                <div
                                                    class="bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl p-3 shadow-md">
                                                    <IndianRupeeIcon class="w-6 h-6 text-white" />
                                                </div>
                                            </div>
                                            <p class="text-gray-600 text-sm font-medium mb-1">Total Collected</p>
                                            <p class="text-3xl font-bold mb-3 text-green-600">
                                                ₹{{ formatCurrency(dashboardStats.totalCollected) }}
                                            </p>
                                            <p class="text-xs text-gray-500">Rate: {{ collectionRate() }}%</p>
                                        </div>
                                    </div>

                                    <!-- Outstanding -->
                                    <div class="group relative">
                                        <div
                                            class="absolute -inset-0.5 bg-gradient-to-r from-red-500 to-rose-500 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                                        </div>
                                        <div
                                            class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-red-300 transition-all duration-300 shadow-md hover:shadow-lg hover:shadow-red-500/10">
                                            <div class="flex justify-between items-start mb-4">
                                                <div
                                                    class="bg-gradient-to-br from-red-500 to-rose-500 rounded-xl p-3 shadow-md">
                                                    <AlertCircle class="w-6 h-6 text-white" />
                                                </div>
                                            </div>
                                            <p class="text-gray-600 text-sm font-medium mb-1">Outstanding</p>
                                            <p class="text-3xl font-bold mb-3 text-red-600">
                                                ₹{{ formatCurrency(dashboardStats.totalOutstanding) }}
                                            </p>
                                            <p class="text-xs text-gray-500">Pending Collection</p>
                                        </div>
                                    </div>

                                    <!-- Paid Invoices -->
                                    <div class="group relative">
                                        <div
                                            class="absolute -inset-0.5 bg-gradient-to-r from-emerald-500 to-green-500 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                                        </div>
                                        <div
                                            class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-emerald-300 transition-all duration-300 shadow-md hover:shadow-lg hover:shadow-emerald-500/10">
                                            <div class="flex justify-between items-start mb-4">
                                                <div
                                                    class="bg-gradient-to-br from-emerald-500 to-green-500 rounded-xl p-3 shadow-md">
                                                    <CheckCircle class="w-6 h-6 text-white" />
                                                </div>
                                            </div>
                                            <p class="text-gray-600 text-sm font-medium mb-1">Paid</p>
                                            <p class="text-gray-900 text-3xl font-bold mb-3">
                                                {{ dashboardStats.paymentStatus.paid.count }}
                                            </p>
                                            <p class="text-xs text-emerald-600">
                                                ₹{{ formatCurrency(dashboardStats.paymentStatus.paid.amount) }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Partial Payments -->
                                    <div class="group relative">
                                        <div
                                            class="absolute -inset-0.5 bg-gradient-to-r from-orange-500 to-amber-500 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                                        </div>
                                        <div
                                            class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-orange-300 transition-all duration-300 shadow-md hover:shadow-lg hover:shadow-orange-500/10">
                                            <div class="flex justify-between items-start mb-4">
                                                <div
                                                    class="bg-gradient-to-br from-orange-500 to-amber-500 rounded-xl p-3 shadow-md">
                                                    <AlertCircle class="w-6 h-6 text-white" />
                                                </div>
                                            </div>
                                            <p class="text-gray-600 text-sm font-medium mb-1">Partial</p>
                                            <p class="text-gray-900 text-3xl font-bold mb-3">
                                                {{ dashboardStats.paymentStatus.partial.count }}
                                            </p>
                                            <p class="text-xs text-orange-600">
                                                ₹{{ formatCurrency(dashboardStats.paymentStatus.partial.amount) }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Unpaid Invoices -->
                                    <div class="group relative">
                                        <div
                                            class="absolute -inset-0.5 bg-gradient-to-r from-red-500 to-rose-500 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                                        </div>
                                        <div
                                            class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-red-300 transition-all duration-300 shadow-md hover:shadow-lg hover:shadow-red-500/10">
                                            <div class="flex justify-between items-start mb-4">
                                                <div
                                                    class="bg-gradient-to-br from-red-500 to-rose-500 rounded-xl p-3 shadow-md">
                                                    <AlertCircle class="w-6 h-6 text-white" />
                                                </div>
                                            </div>
                                            <p class="text-gray-600 text-sm font-medium mb-1">Unpaid</p>
                                            <p class="text-gray-900 text-3xl font-bold mb-3">
                                                {{ dashboardStats.paymentStatus.unpaid.count }}
                                            </p>
                                            <p class="text-xs text-red-600">
                                                ₹{{ formatCurrency(dashboardStats.paymentStatus.unpaid.amount) }}
                                            </p>
                                        </div>
                                    </div>
                                    <PaymentBalanceDashboard @error="handleError" />
                                    <!-- <PaymentExceptionCard :customerId="null" @error="handleError" /> -->
                                </div>
                            </div>

                            <!-- Charts Section -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                                <!-- Revenue Trend Chart -->
                                <div class="group relative">
                                    <div
                                        class="h-full relative bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 border border-gray-200">
                                        <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                                            <h3 class="text-lg font-semibold text-gray-900">Revenue Trend</h3>
                                            <select v-model="revenueTrendGroupBy"
                                                class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="day">Daily</option>
                                                <option value="week">Weekly</option>
                                                <option value="month">Monthly</option>
                                            </select>
                                        </div>
                                        <div v-if="revenueTrend.length === 0" class="text-center text-gray-500 py-12">
                                            <FileText class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                                            <p>No revenue trend data available</p>
                                        </div>
                                        <div v-else style="position: relative; height: 300px; width: 100%;">
                                            <canvas ref="revenueTrendChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Methods -->
                                <div class="group relative">
                                    <div
                                        class="relative h-full bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 border border-gray-200">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Methods</h3>
                                        <div class="space-y-3 max-h-[300px] overflow-y-auto">
                                            <div v-if="paymentMethods.length === 0"
                                                class="text-center text-gray-500 py-8">
                                                <CreditCard class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                                                <p>No payment method data available</p>
                                            </div>
                                            <div v-for="(method, idx) in paymentMethods" :key="idx"
                                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                                <div class="flex items-center">
                                                    <CreditCard class="w-5 h-5 text-gray-600 mr-3" />
                                                    <span class="font-medium text-gray-900">{{ method.method }}</span>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-sm font-semibold text-gray-900">
                                                        ₹{{ formatCurrency(method.totalAmount) }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">{{ method.count }} transactions</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Method Distribution Chart -->
                                <div class="group relative">
                                    <div
                                        class="relative h-full bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 border border-gray-200">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Method Distribution
                                        </h3>
                                        <div v-if="paymentMethods.length === 0" class="text-center text-gray-500 py-12">
                                            <CreditCard class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                                            <p>No payment method data available</p>
                                        </div>
                                        <div v-else style="position: relative; height: 300px; width: 100%;">
                                            <canvas ref="paymentMethodChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Top Customers -->
                                <div class="group relative">
                                    <div
                                        class="h-full relative bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 border border-gray-200">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Customers by Revenue
                                        </h3>
                                        <div class="overflow-x-auto max-h-[300px]">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50 sticky top-0">
                                                    <tr>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Company
                                                        </th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Revenue
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    <tr v-if="topCustomers.length === 0">
                                                        <td colspan="2" class="px-4 py-8 text-center text-gray-500">
                                                            No customer data available
                                                        </td>
                                                    </tr>
                                                    <tr v-for="(customer, idx) in topCustomers.slice(0, 5)" :key="idx"
                                                        class="hover:bg-gray-50 transition">
                                                        <td class="px-4 py-3">
                                                            <div class="text-sm font-medium text-gray-900">{{
                                                                customer.companyName }}</div>
                                                            <div class="text-xs text-gray-500 truncate max-w-[200px]">{{
                                                                customer.email }}</div>
                                                        </td>
                                                        <td
                                                            class="px-4 py-3 text-sm font-semibold text-gray-900 whitespace-nowrap">
                                                            ₹{{ formatCurrency(customer.revenue) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Discount Analysis -->
                            <div class="group relative">
                                <div class="h-full relative bg-white rounded-2xl p-4">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Discount Analysis</h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                                        <div class="group/card relative">
                                            <div
                                                class="absolute -inset-0.5 bg-gradient-to-r from-purple-400 to-pink-400 rounded-xl blur opacity-20 group-hover/card:opacity-40 transition">
                                            </div>
                                            <div
                                                class="relative bg-white border border-gray-200 rounded-xl p-4 hover:border-purple-300 transition-all duration-300 shadow-sm hover:shadow-md">
                                                <p class="text-sm text-purple-700 font-medium">Discounted Invoices</p>
                                                <p class="text-2xl font-bold text-purple-900 mt-1">
                                                    {{ discountAnalysis.discountedInvoices }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="group/card relative">
                                            <div
                                                class="absolute -inset-0.5 bg-gradient-to-r from-purple-400 to-pink-400 rounded-xl blur opacity-20 group-hover/card:opacity-40 transition">
                                            </div>
                                            <div
                                                class="relative bg-white border border-gray-200 rounded-xl p-4 hover:border-purple-300 transition-all duration-300 shadow-sm hover:shadow-md">
                                                <p class="text-sm text-purple-700 font-medium">Total Discount Given</p>
                                                <p class="text-2xl font-bold text-purple-900 mt-1">
                                                    ₹{{ formatCurrency(discountAnalysis.totalDiscount) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="group/card relative">
                                            <div
                                                class="absolute -inset-0.5 bg-gradient-to-r from-purple-400 to-pink-400 rounded-xl blur opacity-20 group-hover/card:opacity-40 transition">
                                            </div>
                                            <div
                                                class="relative bg-white border border-gray-200 rounded-xl p-4 hover:border-purple-300 transition-all duration-300 shadow-sm hover:shadow-md">
                                                <p class="text-sm text-purple-700 font-medium">Avg Discount %</p>
                                                <p class="text-2xl font-bold text-purple-900 mt-1">
                                                    {{ discountAnalysis.avgDiscountPercent.toFixed(2) }}%
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </transition>
</template>