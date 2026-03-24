<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, watch, computed } from 'vue';
import {
    FileText,
    CheckCircle,
    AlertCircle,
    CreditCard,
    RefreshCw,
    Loader2,
    Filter,
    IndianRupee,
    X,
    BarChart3,
    Download,
    TrendingUp,
    TrendingDown,
    Clock,
    User,
    Mail,
    Phone,
    Building2,
    Receipt,
    Eye,
    ChevronDown,
    ChevronUp
} from 'lucide-vue-next';
import Chart from 'chart.js/auto';
import PaymentExceptionCard from './PaymentExceptionCard.vue';
import PaymentBalanceDashboard from './PaymentBalanceDashboard.vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false
    },
    customerId: {
        type: [String, Number],
        required: true
    }
});

const apiBaseUrl = import.meta.env.VITE_BACKEND_API_URL;

const emit = defineEmits(['close', 'error']);

// State
const loading = ref(false);
const showFilters = ref(false);
const selectedInvoice = ref(null);
const showInvoiceDetail = ref(false);
const expandedSections = ref({
    overview: true,
    trends: true,
    invoices: true,
    payments: true
});

// Filters
const filters = ref({
    startDate: '',
    endDate: '',
    paymentStatus: '',
    platformChargeType: ''
});

// Data
const customerData = ref({
    profile: {
        companyName: '',
        contactPerson: '',
        email: '',
        phone: '',
        gstNumber: ''
    },
    stats: {
        totals: {
            totalRevenue: 0,
            totalCollected: 0,
            totalOutstanding: 0,
            totalGST: 0,
            totalDiscount: 0,
            totalInvoices: 0
        },
        byStatus: {
            paid: { count: 0, invoiceTotal: 0, paidAmount: 0, balanceAmount: 0 },
            partial: { count: 0, invoiceTotal: 0, paidAmount: 0, balanceAmount: 0 },
            unpaid: { count: 0, invoiceTotal: 0, paidAmount: 0, balanceAmount: 0 },
            expired: { count: 0, invoiceTotal: 0, paidAmount: 0, balanceAmount: 0 }
        }
    },
    invoices: [],
    pagination: {
        totalItems: 0,
        totalPages: 0,
        currentPage: 1,
        limit: 10
    }
});

// Charts
const revenueTrendChart = ref(null);
const paymentMethodChart = ref(null);
const paymentStatusChart = ref(null);

const charts = ref({
    revenueTrend: null,
    paymentMethod: null,
    paymentStatus: null
});

// Error handler
const handleError = (error) => {
    console.error('Dashboard Error:', error);
    // You can add toast notification or alert here
    alert(`Error: ${error.type} - ${error.error.message || 'Something went wrong'}`);
};

// Computed
const collectionRate = computed(() => {
    if (!customerData.value.stats.totals.totalRevenue) return '0.00';
    const rate = (customerData.value.stats.totals.totalCollected / customerData.value.stats.totals.totalRevenue) * 100;
    return rate.toFixed(2);
});

const averageInvoiceValue = computed(() => {
    if (!customerData.value.stats.totals.totalInvoices) return '0.00';
    return (customerData.value.stats.totals.totalRevenue / customerData.value.stats.totals.totalInvoices).toFixed(2);
});

const paymentMethodsData = computed(() => {
    const methods = {};
    customerData.value.invoices.forEach(invoice => {
        if (invoice.payment_ledger && Array.isArray(invoice.payment_ledger)) {
            invoice.payment_ledger.forEach(payment => {
                const method = payment.method || 'Unknown';
                if (!methods[method]) {
                    methods[method] = { count: 0, amount: 0 };
                }
                methods[method].count++;
                methods[method].amount += Number(payment.paid_amount || 0);
            });
        }
    });
    return Object.entries(methods).map(([method, data]) => ({
        method,
        count: data.count,
        totalAmount: data.amount
    }));
});

const revenueTrendData = computed(() => {
    const trendMap = {};
    customerData.value.invoices.forEach(invoice => {
        const date = new Date(invoice.createdAt);
        const monthYear = `${date.toLocaleString('default', { month: 'short' })} ${date.getFullYear()}`;

        if (!trendMap[monthYear]) {
            trendMap[monthYear] = { invoiced: 0, collected: 0 };
        }

        trendMap[monthYear].invoiced += Number(invoice.total || 0);
        trendMap[monthYear].collected += Number(invoice.total_net_received || 0);
    });

    return Object.entries(trendMap)
        .sort((a, b) => new Date(a[0]) - new Date(b[0]))
        .map(([period, data]) => ({
            period,
            invoiced: data.invoiced,
            revenue: data.collected
        }));
});

// Methods
const formatCurrency = (value) => {
    if (!value && value !== 0) return '0.00';
    return new Intl.NumberFormat('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value);
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-IN', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};

const getStatusColor = (status) => {
    const colors = {
        paid: 'bg-green-100 text-green-700 border-green-300',
        partial: 'bg-orange-100 text-orange-700 border-orange-300',
        unpaid: 'bg-red-100 text-red-700 border-red-300',
        expired: 'bg-gray-100 text-gray-700 border-gray-300'
    };
    return colors[status] || colors.unpaid;
};

const buildQueryParams = () => {
    const params = {
        createdForId: props.customerId,
        page: customerData.value.pagination.currentPage,
        limit: customerData.value.pagination.limit,
        ...filters.value
    };
    const filtered = Object.entries(params).filter(([_, v]) => v !== '');
    return new URLSearchParams(filtered).toString();
};

const fetchCustomerData = async () => {
    try {
        loading.value = true;
        const params = buildQueryParams();
        const response = await axios.get(`${apiBaseUrl}/invoices?${params}`);
        const result = response.data;

        if (result.success && result.data) {
            customerData.value.invoices = result.data.invoices || [];
            customerData.value.stats = result.data.stats || customerData.value.stats;
            customerData.value.pagination = result.data.pagination || customerData.value.pagination;

            // Extract customer profile from first invoice
            if (customerData.value.invoices.length > 0) {
                const firstInvoice = customerData.value.invoices[0];
                customerData.value.profile = {
                    companyName: firstInvoice.company_name || '',
                    contactPerson: firstInvoice.contact_person || '',
                    email: firstInvoice.email || '',
                    phone: firstInvoice.phone || '',
                    gstNumber: firstInvoice.gst_number || 'N/A'
                };
            }

            await nextTick();
            setTimeout(() => {
                renderCharts();
            }, 100);
        }
    } catch (error) {
        console.error('Error fetching customer data:', error);
        emit('error', { type: 'fetchData', error });
    } finally {
        loading.value = false;
    }
};

const renderCharts = () => {
    if (revenueTrendData.value.length > 0) {
        renderRevenueTrendChart();
    }
    if (paymentMethodsData.value.length > 0) {
        renderPaymentMethodChart();
    }
    renderPaymentStatusChart();
};

const renderRevenueTrendChart = () => {
    if (charts.value.revenueTrend) {
        try {
            charts.value.revenueTrend.destroy();
        } catch (e) {
            console.warn('Error destroying revenue trend chart:', e);
        }
        charts.value.revenueTrend = null;
    }

    if (!revenueTrendChart.value) return;

    try {
        const ctx = revenueTrendChart.value.getContext('2d');
        if (!ctx) return;

        charts.value.revenueTrend = new Chart(ctx, {
            type: 'line',
            data: {
                labels: revenueTrendData.value.map(item => item.period),
                datasets: [
                    {
                        label: 'Collected',
                        data: revenueTrendData.value.map(item => item.revenue),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Invoiced',
                        data: revenueTrendData.value.map(item => item.invoiced),
                        borderColor: '#ff5100',
                        backgroundColor: 'rgba(255, 81, 0, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.dataset.label + ': ₹' + formatCurrency(context.parsed.y);
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
    }
};

const renderPaymentMethodChart = () => {
    if (charts.value.paymentMethod) {
        try {
            charts.value.paymentMethod.destroy();
        } catch (e) {
            console.warn('Error destroying payment method chart:', e);
        }
        charts.value.paymentMethod = null;
    }

    if (!paymentMethodChart.value) return;

    try {
        const ctx = paymentMethodChart.value.getContext('2d');
        if (!ctx) return;

        const colors = ['#ff5100', '#10b981', '#3b82f6', '#f59e0b', '#8b5cf6'];

        charts.value.paymentMethod = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: paymentMethodsData.value.map(item => item.method),
                datasets: [{
                    data: paymentMethodsData.value.map(item => item.totalAmount),
                    backgroundColor: colors,
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const method = context.label;
                                const value = context.parsed;
                                const count = paymentMethodsData.value[context.dataIndex]?.count || 0;
                                return `${method}: ₹${formatCurrency(value)} (${count} txns)`;
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error rendering payment method chart:', error);
    }
};

const renderPaymentStatusChart = () => {
    if (charts.value.paymentStatus) {
        try {
            charts.value.paymentStatus.destroy();
        } catch (e) {
            console.warn('Error destroying payment status chart:', e);
        }
        charts.value.paymentStatus = null;
    }

    if (!paymentStatusChart.value) return;

    try {
        const ctx = paymentStatusChart.value.getContext('2d');
        if (!ctx) return;

        const statusData = customerData.value.stats.byStatus;

        charts.value.paymentStatus = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Paid', 'Partial', 'Unpaid', 'Expired'],
                datasets: [{
                    label: 'Invoice Amount',
                    data: [
                        statusData.paid.paidAmount,
                        statusData.partial.paidAmount,
                        statusData.unpaid.invoiceTotal,
                        statusData.expired.invoiceTotal
                    ],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#6b7280']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return '₹' + formatCurrency(context.parsed.y);
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
        console.error('Error rendering payment status chart:', error);
    }
};

const applyFilters = () => {
    customerData.value.pagination.currentPage = 1;
    fetchCustomerData();
};

const resetFilters = () => {
    filters.value = {
        startDate: '',
        endDate: '',
        paymentStatus: '',
        platformChargeType: ''
    };
    applyFilters();
};

const changePage = (page) => {
    customerData.value.pagination.currentPage = page;
    fetchCustomerData();
};

const viewInvoiceDetails = (invoice) => {
    selectedInvoice.value = invoice;
    showInvoiceDetail.value = true;
};

const closeInvoiceDetail = () => {
    showInvoiceDetail.value = false;
    selectedInvoice.value = null;
};

// const downloadPDF = (url, filename) => {
//     if (!url) return;
//     const link = document.createElement('a');
//     link.href = url;
//     link.download = filename || 'invoice.pdf';
//     link.target = '_blank';
//     document.body.appendChild(link);
//     link.click();
//     document.body.removeChild(link);
// };

const downloadPDF = async (url, filename) => {

    if (!url) {
        toast.error('No PDF available to download');
        return;
    }


    try {
        // Fetch the PDF as a blob
        const response = await axios.get(
            url,
            {
                responseType: "blob", // important
            }
        );

        const blob = response.data;


        // Create a blob URL
        const blobUrl = window.URL.createObjectURL(blob);

        // Create and trigger download
        const link = document.createElement('a');
        link.href = blobUrl;
        link.download = filename || "invoice.pdf";
        document.body.appendChild(link);
        link.click();

        // Cleanup
        document.body.removeChild(link);
        window.URL.revokeObjectURL(blobUrl);

        toast.success('PDF downloaded successfully!');
    } catch (error) {
        console.error('Download error:', error);
        toast.error('Error downloading PDF. Please try again.');
    }};

const toggleSection = (section) => {
    expandedSections.value[section] = !expandedSections.value[section];
};

const closeModal = () => {
    destroyCharts();
    emit('close');
    document.body.style.overflow = '';
};

const destroyCharts = () => {
    Object.keys(charts.value).forEach(key => {
        if (charts.value[key]) {
            try {
                charts.value[key].destroy();
            } catch (e) {
                console.warn(`Error destroying ${key} chart:`, e);
            }
            charts.value[key] = null;
        }
    });
};

const getPaymentCompletionText = (item) => {
    const slabs = item?.payment_terms?.slabs;
    if (!Array.isArray(slabs) || slabs.length === 0) return `Unpaid`;

    const totalSlabs = slabs.length;
    const paidSlabs = slabs.filter(s => s?.paymentDone).length;

    // Normalize numbers
    const totalAmount = Number(item?.total) || 0;
    const totalPaid = Number(item?.total_net_received) || 0;
    const totalTds = Number(item?.total_tds_deducted) || 0;
    const exceptionAmount = Number(item?.total_excess_or_short_amount) || 0;

    // ❌ No payment at all
    if (paidSlabs === 0) return `Unpaid`;

    // ✅ Some slabs paid, some pending
    if (paidSlabs < totalSlabs) {
        return `Partially Paid (${paidSlabs}/${totalSlabs})`;
    }

    // ✅ All slabs paid → evaluate financial correctness
    if (totalPaid === totalAmount) {
        return 'Fully Paid';
    }

    if (totalPaid < totalAmount) {
        if (totalTds > 0 && exceptionAmount === 0) {
            return 'Fully Paid (TDS Applied)';
        }

        return 'Short Paid (Exception Applied)';


    }

    // totalPaid > totalAmount
    return 'Excess Paid (Exception Applied)';
};

// Lifecycle
onMounted(() => {
    if (props.isOpen && props.customerId) {
        document.body.style.overflow = 'hidden';
        fetchCustomerData();
    }
});

onBeforeUnmount(() => {
    destroyCharts();
    document.body.style.overflow = '';
});

watch(() => props.isOpen, async (newVal) => {
    if (newVal && props.customerId) {
        document.body.style.overflow = 'hidden';
        destroyCharts();
        await fetchCustomerData();
    } else {
        destroyCharts();
        document.body.style.overflow = '';
    }
});
</script>

<template>
    <!-- Main Modal -->
    <transition enter-active-class="transition-opacity duration-300"
        leave-active-class="transition-opacity duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            @click.self="closeModal">
            <transition enter-active-class="transition-all duration-300 ease-out"
                leave-active-class="transition-all duration-200 ease-in" enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100" leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95">
                <div v-if="isOpen"
                    class="bg-white rounded-xl shadow-2xl w-full max-w-7xl max-h-[90vh] overflow-hidden flex flex-col">
                    <!-- Header -->
                    <div
                        class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-[#ff5100] to-[#ff7340]">
                        <div class="flex items-center gap-3">
                            <div class="bg-white/20 backdrop-blur-sm rounded-lg p-2">
                                <User class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">Customer Analytics</h2>
                                <p class="text-white/80 text-sm mt-0.5">{{ customerData.profile.companyName ||
                                    'Loading...' }}</p>
                            </div>
                        </div>
                        <button @click="closeModal" class="p-2 hover:bg-white/20 rounded-lg transition text-white">
                            <X class="w-6 h-6" />
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="flex-1 overflow-y-auto p-6 bg-gray-50">
                        <!-- Loading State -->
                        <div v-if="loading" class="flex justify-center items-center py-12">
                            <Loader2 class="w-8 h-8 animate-spin text-[#ff5100]" />
                            <span class="ml-3 text-gray-600 font-medium">Loading customer data...</span>
                        </div>

                        <!-- Content -->
                        <div v-else>
                            <!-- Customer Profile -->
                            <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                    <Building2 class="w-5 h-5 text-[#ff5100]" />
                                    Customer Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div class="flex items-start gap-3">
                                        <User class="w-5 h-5 text-gray-400 mt-0.5" />
                                        <div>
                                            <p class="text-xs text-gray-500">Contact Person</p>
                                            <p class="text-sm font-medium text-gray-900">{{
                                                customerData.profile.contactPerson || 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <Mail class="w-5 h-5 text-gray-400 mt-0.5" />
                                        <div>
                                            <p class="text-xs text-gray-500">Email</p>
                                            <p class="text-sm font-medium text-gray-900 truncate">{{
                                                customerData.profile.email || 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <Phone class="w-5 h-5 text-gray-400 mt-0.5" />
                                        <div>
                                            <p class="text-xs text-gray-500">Phone</p>
                                            <p class="text-sm font-medium text-gray-900">{{ customerData.profile.phone
                                                || 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <Receipt class="w-5 h-5 text-gray-400 mt-0.5" />
                                        <div>
                                            <p class="text-xs text-gray-500">GST Number</p>
                                            <p class="text-sm font-medium text-gray-900">{{
                                                customerData.profile.gstNumber }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

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
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Start
                                                    Date</label>
                                                <input type="date" v-model="filters.startDate"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5100] focus:border-[#ff5100]" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">End
                                                    Date</label>
                                                <input type="date" v-model="filters.endDate"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5100] focus:border-[#ff5100]" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Payment
                                                    Status</label>
                                                <select v-model="filters.paymentStatus"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5100] focus:border-[#ff5100]">
                                                    <option value="">All</option>
                                                    <option value="paid">Paid</option>
                                                    <option value="partial">Partial</option>
                                                    <option value="unpaid">Unpaid</option>
                                                    <option value="expired">Expired</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Charge
                                                    Type</label>
                                                <select v-model="filters.platformChargeType"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5100] focus:border-[#ff5100]">
                                                    <option value="">All</option>
                                                    <option value="Monthly">Monthly</option>
                                                    <option value="Yearly">Yearly</option>
                                                </select>
                                            </div>
                                            <div class="flex items-end gap-2">
                                                <button @click="applyFilters"
                                                    class="flex-1 px-4 py-2 bg-[#ff5100] text-white rounded-lg hover:bg-[#e64900] transition font-medium">
                                                    Apply
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

                            <!-- Stats Overview -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Financial Overview</h3>
                                    <button @click="toggleSection('overview')"
                                        class="text-gray-500 hover:text-gray-700">
                                        <ChevronDown v-if="expandedSections.overview" class="w-5 h-5" />
                                        <ChevronUp v-else class="w-5 h-5" />
                                    </button>
                                </div>
                                <transition enter-active-class="transition-all duration-300"
                                    leave-active-class="transition-all duration-200"
                                    enter-from-class="opacity-0 -translate-y-2"
                                    enter-to-class="opacity-100 translate-y-0"
                                    leave-from-class="opacity-100 translate-y-0"
                                    leave-to-class="opacity-0 -translate-y-2">
                                    <div v-show="expandedSections.overview"
                                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                        <!-- Total Invoiced -->
                                        <div
                                            class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="bg-blue-500 rounded-lg p-2.5">
                                                    <FileText class="w-5 h-5 text-white" />
                                                </div>
                                                <TrendingUp class="w-5 h-5 text-blue-600" />
                                            </div>
                                            <p class="text-sm text-blue-700 font-medium mb-1">Total Invoiced</p>
                                            <p class="text-2xl font-bold text-blue-900">₹{{
                                                formatCurrency(customerData.stats.totals.totalRevenue) }}</p>
                                            <p class="text-xs text-blue-600 mt-2">{{
                                                customerData.stats.totals.totalInvoices }} invoices</p>
                                        </div>

                                        <!-- Total Collected -->
                                        <div
                                            class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="bg-green-500 rounded-lg p-2.5">
                                                    <CheckCircle class="w-5 h-5 text-white" />
                                                </div>
                                                <IndianRupee class="w-5 h-5 text-green-600" />
                                            </div>
                                            <p class="text-sm text-green-700 font-medium mb-1">Total Collected</p>
                                            <p class="text-2xl font-bold text-green-900">₹{{
                                                formatCurrency(customerData.stats.totals.totalCollected) }}</p>
                                            <p class="text-xs text-green-600 mt-2">{{ collectionRate }}% collection rate
                                            </p>
                                        </div>

                                        <!-- Outstanding -->
                                        <div
                                            class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 border border-red-200">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="bg-red-500 rounded-lg p-2.5">
                                                    <AlertCircle class="w-5 h-5 text-white" />
                                                </div>
                                                <TrendingDown class="w-5 h-5 text-red-600" />
                                            </div>
                                            <p class="text-sm text-red-700 font-medium mb-1">Outstanding</p>
                                            <p class="text-2xl font-bold text-red-900">₹{{
                                                formatCurrency(customerData.stats.totals.totalOutstanding) }}</p>
                                            <p class="text-xs text-red-600 mt-2">Pending amount</p>
                                        </div>
                                        <PaymentBalanceDashboard :userId="props.customerId" @error="handleError" />
                                        <!-- Average Invoice Value -->
                                        <!-- <div
                                            class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 border border-orange-200">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="bg-orange-500 rounded-lg p-2.5">
                                                    <Receipt class="w-5 h-5 text-white" />
                                                </div>
                                                <BarChart3 class="w-5 h-5 text-orange-600" />
                                            </div>
                                            <p class="text-sm text-orange-700 font-medium mb-1">Avg Invoice Value</p>
                                            <p class="text-2xl font-bold text-orange-900">₹{{
                                                formatCurrency(averageInvoiceValue) }}</p>
                                            <p class="text-xs text-orange-600 mt-2">Per invoice</p>
                                        </div> -->
                                    </div>
                                </transition>
                            </div>

                            <!-- Payment Status Breakdown -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Payment Status</h3>
                                    <button @click="toggleSection('payments')"
                                        class="text-gray-500 hover:text-gray-700">
                                        <ChevronDown v-if="expandedSections.payments" class="w-5 h-5" />
                                        <ChevronUp v-else class="w-5 h-5" />
                                    </button>
                                </div>
                                <transition enter-active-class="transition-all duration-300"
                                    leave-active-class="transition-all duration-200"
                                    enter-from-class="opacity-0 -translate-y-2"
                                    enter-to-class="opacity-100 translate-y-0"
                                    leave-from-class="opacity-100 translate-y-0"
                                    leave-to-class="opacity-0 -translate-y-2">
                                    <div v-show="expandedSections.payments"
                                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                        <!-- Paid -->
                                        <div
                                            class="bg-white rounded-xl p-5 border-2 border-green-200 hover:shadow-lg transition">
                                            <div class="flex items-center justify-between mb-3">
                                                <CheckCircle class="w-8 h-8 text-green-500" />
                                                <span class="text-3xl font-bold text-green-600">{{
                                                    customerData.stats.byStatus.paid.count }}</span>
                                            </div>
                                            <p class="text-sm font-medium text-gray-700 mb-1">Paid Invoices</p>
                                            <p class="text-lg font-bold text-gray-900">₹{{
                                                formatCurrency(customerData.stats.byStatus.paid.paidAmount) }}</p>
                                        </div>

                                        <!-- Partial -->
                                        <div
                                            class="bg-white rounded-xl p-5 border-2 border-orange-200 hover:shadow-lg transition">
                                            <div class="flex items-center justify-between mb-3">
                                                <Clock class="w-8 h-8 text-orange-500" />
                                                <span class="text-3xl font-bold text-orange-600">{{
                                                    customerData.stats.byStatus.partial.count }}</span>
                                            </div>
                                            <p class="text-sm font-medium text-gray-700 mb-1">Partial Payments</p>
                                            <p class="text-lg font-bold text-gray-900">₹{{
                                                formatCurrency(customerData.stats.byStatus.partial.paidAmount) }}</p>
                                        </div>

                                        <!-- Unpaid -->
                                        <div
                                            class="bg-white rounded-xl p-5 border-2 border-red-200 hover:shadow-lg transition">
                                            <div class="flex items-center justify-between mb-3">
                                                <AlertCircle class="w-8 h-8 text-red-500" />
                                                <span class="text-3xl font-bold text-red-600">{{
                                                    customerData.stats.byStatus.unpaid.count }}</span>
                                            </div>
                                            <p class="text-sm font-medium text-gray-700 mb-1">Unpaid Invoices</p>
                                            <p class="text-lg font-bold text-gray-900">₹{{
                                                formatCurrency(customerData.stats.byStatus.unpaid.invoiceTotal) }}</p>
                                        </div>

                                        <!-- Expired -->
                                        <div
                                            class="bg-white rounded-xl p-5 border-2 border-gray-200 hover:shadow-lg transition">
                                            <div class="flex items-center justify-between mb-3">
                                                <X class="w-8 h-8 text-gray-500" />
                                                <span class="text-3xl font-bold text-gray-600">{{
                                                    customerData.stats.byStatus.expired.count }}</span>
                                            </div>
                                            <p class="text-sm font-medium text-gray-700 mb-1">Expired Invoices</p>
                                            <p class="text-lg font-bold text-gray-900">₹{{
                                                formatCurrency(customerData.stats.byStatus.expired.invoiceTotal) }}</p>
                                        </div>
                                    </div>
                                </transition>
                            </div>

                            <!-- Charts Section -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Trends & Analytics</h3>
                                    <button @click="toggleSection('trends')" class="text-gray-500 hover:text-gray-700">
                                        <ChevronDown v-if="expandedSections.trends" class="w-5 h-5" />
                                        <ChevronUp v-else class="w-5 h-5" />
                                    </button>
                                </div>
                                <transition enter-active-class="transition-all duration-300"
                                    leave-active-class="transition-all duration-200"
                                    enter-from-class="opacity-0 -translate-y-2"
                                    enter-to-class="opacity-100 translate-y-0"
                                    leave-from-class="opacity-100 translate-y-0"
                                    leave-to-class="opacity-0 -translate-y-2">
                                    <div v-show="expandedSections.trends" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                        <!-- Revenue Trend Chart -->
                                        <div
                                            class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                                            <h4
                                                class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                                <TrendingUp class="w-5 h-5 text-[#ff5100]" />
                                                Revenue Trend
                                            </h4>
                                            <div v-if="revenueTrendData.length === 0"
                                                class="text-center text-gray-500 py-12">
                                                <BarChart3 class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                                                <p>No trend data available</p>
                                            </div>
                                            <div v-else style="position: relative; height: 300px; width: 100%;">
                                                <canvas ref="revenueTrendChart"></canvas>
                                            </div>
                                        </div>

                                        <!-- Payment Method Distribution -->
                                        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                                            <h4
                                                class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                                <CreditCard class="w-5 h-5 text-[#ff5100]" />
                                                Payment Methods
                                            </h4>
                                            <div v-if="paymentMethodsData.length === 0"
                                                class="text-center text-gray-500 py-12">
                                                <CreditCard class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                                                <p>No payment data</p>
                                            </div>
                                            <div v-else style="position: relative; height: 300px; width: 100%;">
                                                <canvas ref="paymentMethodChart"></canvas>
                                            </div>
                                        </div>

                                        <!-- Payment Status Chart -->
                                        <div
                                            class="lg:col-span-3 bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                                            <h4
                                                class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                                <BarChart3 class="w-5 h-5 text-[#ff5100]" />
                                                Payment Status Distribution
                                            </h4>
                                            <div style="position: relative; height: 300px; width: 100%;">
                                                <canvas ref="paymentStatusChart"></canvas>
                                            </div>
                                        </div>

                                        <!-- Payment Methods List -->
                                        <div
                                            class="lg:col-span-3 bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                                            <h4 class="text-md font-semibold text-gray-900 mb-4">Payment Method
                                                Breakdown</h4>
                                            <div v-if="paymentMethodsData.length === 0"
                                                class="text-center text-gray-500 py-8">
                                                <CreditCard class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                                                <p>No payment methods found</p>
                                            </div>
                                            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                                <div v-for="(method, idx) in paymentMethodsData" :key="idx"
                                                    class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition border border-gray-200">
                                                    <div class="flex items-center gap-3">
                                                        <div class="bg-[#ff5100] rounded-lg p-2">
                                                            <CreditCard class="w-5 h-5 text-white" />
                                                        </div>
                                                        <div>
                                                            <p class="font-semibold text-gray-900">{{ method.method }}
                                                            </p>
                                                            <p class="text-xs text-gray-500">{{ method.count }}
                                                                transactions</p>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-sm font-bold text-gray-900">₹{{
                                                            formatCurrency(method.totalAmount) }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </transition>
                            </div>



                            <!-- Invoices List -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Invoice History</h3>
                                    <button @click="toggleSection('invoices')"
                                        class="text-gray-500 hover:text-gray-700">
                                        <ChevronDown v-if="expandedSections.invoices" class="w-5 h-5" />
                                        <ChevronUp v-else class="w-5 h-5" />
                                    </button>
                                </div>
                                <transition enter-active-class="transition-all duration-300"
                                    leave-active-class="transition-all duration-200"
                                    enter-from-class="opacity-0 -translate-y-2"
                                    enter-to-class="opacity-100 translate-y-0"
                                    leave-from-class="opacity-100 translate-y-0"
                                    leave-to-class="opacity-0 -translate-y-2">
                                    <div v-show="expandedSections.invoices">
                                        <div v-if="customerData.invoices.length === 0"
                                            class="bg-white rounded-xl p-12 text-center border border-gray-200">
                                            <FileText class="w-16 h-16 mx-auto mb-4 text-gray-300" />
                                            <p class="text-gray-500 text-lg">No invoices found</p>
                                        </div>
                                        <div v-else
                                            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Invoice</th>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Date</th>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Amount</th>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Paid</th>
                                                            <!-- <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Balance</th> -->
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Status</th>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        <tr v-for="invoice in customerData.invoices" :key="invoice.id"
                                                            class="hover:bg-gray-50 transition">
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <div class="text-sm font-medium text-gray-900">{{
                                                                    invoice.proforma_number || invoice.quotation_number
                                                                }}</div>
                                                                <div class="text-xs text-gray-500">{{
                                                                    invoice.platform_charge_type }}</div>
                                                            </td>
                                                            <td
                                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                {{ formatDate(invoice.proforma_date ||
                                                                    invoice.quotation_date) }}
                                                            </td>
                                                            <td
                                                                class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                                ₹{{ formatCurrency(invoice.total) }}
                                                            </td>
                                                            <td
                                                                class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                                                                <!-- ₹{{ formatCurrency(invoice.total_net_received) }} -->

                                                                <div class="text-sm font-medium text-green-600">₹{{
                                                                    formatCurrency(invoice.total_net_received) }}</div>
                                                                <div class="text-xs text-gray-500">{{
                                                                    getPaymentCompletionText(invoice) }}</div>
                                                            </td>
                                                            <!-- <td
                                                                class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600">
                                                                ₹{{ formatCurrency(invoice.balance_amount) }}
                                                            </td> -->
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <span :class="getStatusColor(invoice.payment_status)"
                                                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border">
                                                                    {{ invoice.payment_status }}
                                                                </span>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                                <div class="flex items-center gap-2">
                                                                    <button @click="viewInvoiceDetails(invoice)"
                                                                        class="text-[#ff5100] hover:text-[#e64900] transition"
                                                                        title="View Details">
                                                                        <Eye class="w-5 h-5" />
                                                                    </button>
                                                                    <button v-if="invoice.quotation_invoice_pdf_url"
                                                                        @click="downloadPDF(invoice.quotation_invoice_pdf_url, `Quotation_${invoice.quotation_number}.pdf`)"
                                                                        class="text-blue-600 hover:text-blue-800 transition"
                                                                        title="Download Quotation">
                                                                        <Download class="w-5 h-5" />
                                                                    </button>
                                                                    <button v-if="invoice.proforma_invoice_pdf_url"
                                                                        @click="downloadPDF(invoice.proforma_invoice_pdf_url, `Proforma_${invoice.proforma_number}.pdf`)"
                                                                        class="text-green-600 hover:text-green-800 transition"
                                                                        title="Download Proforma">
                                                                        <Download class="w-5 h-5" />
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Pagination -->
                                            <div v-if="customerData.pagination.totalPages > 1"
                                                class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                                                <div class="flex items-center justify-between">
                                                    <div class="text-sm text-gray-700">
                                                        Showing page {{ customerData.pagination.currentPage }} of {{
                                                            customerData.pagination.totalPages }}
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <button
                                                            @click="changePage(customerData.pagination.currentPage - 1)"
                                                            :disabled="customerData.pagination.currentPage === 1"
                                                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition">
                                                            Previous
                                                        </button>
                                                        <button v-for="page in customerData.pagination.totalPages"
                                                            :key="page" @click="changePage(page)"
                                                            :class="page === customerData.pagination.currentPage ? 'bg-[#ff5100] text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium transition">
                                                            {{ page }}
                                                        </button>
                                                        <button
                                                            @click="changePage(customerData.pagination.currentPage + 1)"
                                                            :disabled="customerData.pagination.currentPage === customerData.pagination.totalPages"
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

    <!-- Invoice Detail Modal -->
    <transition enter-active-class="transition-opacity duration-300"
        leave-active-class="transition-opacity duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showInvoiceDetail"
            class="fixed inset-0 bg-black bg-opacity-60 z-[60] flex items-center justify-center p-4"
            @click.self="closeInvoiceDetail">
            <transition enter-active-class="transition-all duration-300 ease-out"
                leave-active-class="transition-all duration-200 ease-in" enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100" leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95">
                <div v-if="showInvoiceDetail && selectedInvoice"
                    class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[85vh] overflow-hidden flex flex-col">
                    <!-- Header -->
                    <div
                        class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-[#ff5100] to-[#ff7340]">
                        <div>
                            <h3 class="text-xl font-bold text-white">Invoice Details</h3>
                            <p class="text-white/80 text-sm">{{ selectedInvoice.proforma_number ||
                                selectedInvoice.quotation_number }}</p>
                        </div>
                        <button @click="closeInvoiceDetail"
                            class="p-2 hover:bg-white/20 rounded-lg transition text-white">
                            <X class="w-6 h-6" />
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="flex-1 overflow-y-auto p-6">
                        <!-- Invoice Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Invoice Information</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Quotation No:</span>
                                        <span class="text-sm font-medium text-gray-900">{{
                                            selectedInvoice.quotation_number }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Proforma No:</span>
                                        <span class="text-sm font-medium text-gray-900">{{
                                            selectedInvoice.proforma_number || 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Date:</span>
                                        <span class="text-sm font-medium text-gray-900">{{
                                            formatDate(selectedInvoice.quotation_date) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Quotation Valid Until:</span>
                                        <span class="text-sm font-medium text-gray-900">{{
                                            formatDate(selectedInvoice.quotation_valid_until_date) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Proforma Valid Until:</span>
                                        <span class="text-sm font-medium text-gray-900">{{
                                            formatDate(selectedInvoice.proforma_valid_until_date) || 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Payment Summary</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Sub Total:</span>
                                        <span class="text-sm font-medium text-gray-900">₹{{
                                            formatCurrency(selectedInvoice.sub_total) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Discount:</span>
                                        <span class="text-sm font-medium text-red-600">-₹{{
                                            formatCurrency(selectedInvoice.discount_amount) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">GST:</span>
                                        <span class="text-sm font-medium text-gray-900">₹{{
                                            formatCurrency(selectedInvoice.GST_amount) }}</span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t border-gray-300">
                                        <span class="text-sm font-semibold text-gray-900">Total:</span>
                                        <span class="text-sm font-bold text-[#ff5100]">₹{{
                                            formatCurrency(selectedInvoice.total) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Terms -->
                        <div v-if="selectedInvoice.payment_terms && selectedInvoice.payment_terms.slabs" class="mb-6">
                            <h4 class="text-md font-semibold text-gray-900 mb-3">Payment Terms</h4>
                            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                    Slab</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                    Amount</th>
                                                <!-- <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                    GST</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                    Total</th> -->
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                    remarks/due date</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                    Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="slab in selectedInvoice.payment_terms.slabs" :key="slab.id"
                                                class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm text-gray-900">#{{ slab.id }}</td>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">₹{{
                                                    formatCurrency(slab.amount) }}</td>
                                                <!-- <td class="px-4 py-3 text-sm text-gray-600">₹{{
                                                    formatCurrency(slab.gstAmount) }}</td>
                                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">₹{{
                                                    formatCurrency(slab.totalAmount) }}</td> -->
                                                <td v-if="slab?.what_to_show === 'validity'"
                                                    class="px-4 py-3 text-sm text-gray-600">{{ formatDate(slab.validity)
                                                    }}</td>
                                                <td v-if="slab?.what_to_show === 'remarks'"
                                                    class="px-4 py-3 text-sm text-gray-600">{{ slab.remarks
                                                    }}</td>
                                                <td class="px-4 py-3">
                                                    <span v-if="slab.paymentDone"
                                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 border border-green-300">
                                                        Paid
                                                    </span>
                                                    <span v-else
                                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700 border border-red-300">
                                                        Pending
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Ledger -->
                        <div v-if="selectedInvoice.payment_ledger && selectedInvoice.payment_ledger.length > 0"
                            class="mb-6">
                            <h4 class="text-md font-semibold text-gray-900 mb-3">Payment History</h4>
                            <div class="space-y-3">
                                <div v-for="(payment, idx) in selectedInvoice.payment_ledger" :key="payment.id"
                                    class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="bg-green-500 rounded-full p-1.5">
                                                <CheckCircle class="w-4 h-4 text-white" />
                                            </div>
                                            <span class="text-sm font-semibold text-gray-900">Payment #{{ idx + 1
                                            }}</span>
                                        </div>
                                        <span class="text-sm font-bold text-green-700">₹{{
                                            formatCurrency(payment.paid_amount) }}</span>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                                        <div>
                                            <span class="text-gray-600">Method:</span>
                                            <span class="ml-1 font-medium text-gray-900">{{ payment.method }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Reference:</span>
                                            <span class="ml-1 font-medium text-gray-900">{{ payment.reference }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Date:</span>
                                            <span class="ml-1 font-medium text-gray-900">{{ formatDate(payment.paid_at)
                                            }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Mode:</span>
                                            <span class="ml-1 font-medium text-gray-900">{{ payment.payment_mode
                                            }}</span>
                                        </div>
                                    </div>
                                    <div
                                        class="mt-3 pt-3 border-t border-green-200 grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                                        <div>
                                            <span class="text-gray-600">Amount:</span>
                                            <span class="ml-1 font-medium text-gray-900">₹{{
                                                formatCurrency(payment.amount) }}</span>
                                        </div>
                                        <!-- <div>
                                            <span class="text-gray-600">GST:</span>
                                            <span class="ml-1 font-medium text-gray-900">₹{{
                                                formatCurrency(payment.gst_amount) }}</span>
                                        </div> -->
                                        <div v-if="Number(payment.tds_amount) > 0">
                                            <span class="text-gray-600">TDS:</span>
                                            <span class="ml-1 font-medium text-gray-900">-₹{{
                                                formatCurrency(payment.tds_amount) }}</span>
                                        </div>
                                        <div v-if="payment.has_payment_exception">
                                            <span class="text-gray-600">Exception Amount:</span>
                                            <span class="ml-1 font-medium">{{ payment?.exception_type === "excess" ? "+"
                                                : "" }} {{ payment?.exception_type === "short" ? "-" : "" }}₹{{
                                                    formatCurrency(payment.excess_or_short_amount) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Total Paid:</span>
                                            <span class="ml-1 font-medium text-gray-900">₹{{
                                                formatCurrency(payment.paid_amount) }}</span>
                                        </div>
                                    </div>
                                    <div v-if="payment.payment_invoice_pdf_url"
                                        class="mt-3 pt-3 border-t border-green-200">
                                        <button
                                            @click="downloadPDF(payment.payment_invoice_pdf_url, `Receipt_${payment.reference}.pdf`)"
                                            class="flex items-center gap-2 px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition">
                                            <Download class="w-4 h-4" />
                                            Download Receipt
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Download PDFs -->
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Download Documents</h4>
                            <div class="flex flex-wrap gap-3">
                                <button v-if="selectedInvoice.quotation_invoice_pdf_url"
                                    @click="downloadPDF(selectedInvoice.quotation_invoice_pdf_url, `Quotation_${selectedInvoice.quotation_number}.pdf`)"
                                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                    <Download class="w-4 h-4" />
                                    Quotation PDF
                                </button>
                                <button v-if="selectedInvoice.proforma_invoice_pdf_url"
                                    @click="downloadPDF(selectedInvoice.proforma_invoice_pdf_url, `Proforma_${selectedInvoice.proforma_number}.pdf`)"
                                    class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                    <Download class="w-4 h-4" />
                                    Proforma PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </transition>
</template>