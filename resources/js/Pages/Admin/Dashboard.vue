<!-- <template>
    <AppLayout>
        <div class="bg-white md:bg-inherit p-4 md:p-8 rounded-[5px] text-[#000] h-full overflow-y-auto">
            <div class="flex justify-between mt-3 md:mt-0">
                <div>
                    <h2 class="md:block hidden text-xl mb-1">{{ $t('Dashboard') }}</h2>
                    <p class="mb-6 flex items-center leading-6">
                        <span class="mt-1 font-semibold md:font-normal text-xl">{{ $t('Welcome back') }}, {{
                            user?.first_name }} 👋</span>
                    </p>
                </div>
            </div>
            <div class="md:flex space-x-2 md:space-x-2 mt-4 md:mt-0 mb-8 text-xl md:text-sm hidden">
                <Link href="/admin/organizations/create" class="bg-primary py-2 px-3 rounded-lg text-white text-center">
                {{ $t('Add organization') }}</Link>
                <Link href="/admin/users/create" class="bg-primary py-2 px-3 rounded-lg text-white text-center">{{
                    $t('Add user') }}</Link>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-2 md:space-y-0">
                <div class="bg-slate-100 md:bg-white col-span-2 md:col-span-1 rounded-lg p-3">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-slate-600">{{ $t('Total revenue') }}</h2>
                            <h1 class="text-xl text-gray-600">{{ props.totalRevenue }}</h1>
                        </div>
                        <div class="flex">
                            <span class="bg-secondary/10 p-3 rounded-full self-start">
                                <svg class="text-secondary" xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                    viewBox="0 0 24 24">
                                    <g fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path
                                            d="M2 12c0-4.714 0-7.071 1.464-8.536C4.93 2 7.286 2 12 2c4.714 0 7.071 0 8.535 1.464C22 4.93 22 7.286 22 12c0 4.714 0 7.071-1.465 8.535C19.072 22 16.714 22 12 22s-7.071 0-8.536-1.465C2 19.072 2 16.714 2 12Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m7 14l2.293-2.293a1 1 0 0 1 1.414 0l1.586 1.586a1 1 0 0 0 1.414 0L17 10m0 0v2.5m0-2.5h-2.5" />
                                    </g>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="text-sm space-x-1 md:block hidden">
                        <Link href="/admin/payment-logs" class="flex items-center space-x-1 underline">
                        <span>{{ $t('View all') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m8 16l8-8m0 0h-6m6 0v6"></path>
                        </svg>
                        </Link>
                    </div>
                </div>
                <div class="bg-slate-100 md:bg-white col-span-1 rounded-lg p-3">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-slate-600">{{ $t('Active users') }}</h2>
                            <h1 class="text-xl text-gray-600">{{ props.userCount }}</h1>
                        </div>
                        <div class="flex">
                            <span class="bg-secondary/10 p-3 rounded-full self-start">
                                <svg class="text-secondary" xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                    viewBox="0 0 24 24">
                                    <g fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path
                                            d="M2 12c0-4.714 0-7.071 1.464-8.536C4.93 2 7.286 2 12 2c4.714 0 7.071 0 8.535 1.464C22 4.93 22 7.286 22 12c0 4.714 0 7.071-1.465 8.535C19.072 22 16.714 22 12 22s-7.071 0-8.536-1.465C2 19.072 2 16.714 2 12Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m7 14l2.293-2.293a1 1 0 0 1 1.414 0l1.586 1.586a1 1 0 0 0 1.414 0L17 10m0 0v2.5m0-2.5h-2.5" />
                                    </g>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="text-sm space-x-1 md:block hidden">
                        <Link href="/admin/users" class="flex items-center space-x-1 underline">
                        <span>{{ $t('View all') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m8 16l8-8m0 0h-6m6 0v6"></path>
                        </svg>
                        </Link>
                    </div>
                </div>
                <div class="bg-slate-100 md:bg-white rounded-lg p-3">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-slate-600">{{ $t('Open tickets') }}</h2>
                            <h1 class="text-xl text-gray-600">{{ props.openTickets }}</h1>
                        </div>
                        <div class="flex">
                            <span class="bg-secondary/10 p-3 rounded-full self-start">
                                <svg class="text-secondary" xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                    viewBox="0 0 24 24">
                                    <g fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path
                                            d="M2 12c0-4.714 0-7.071 1.464-8.536C4.93 2 7.286 2 12 2c4.714 0 7.071 0 8.535 1.464C22 4.93 22 7.286 22 12c0 4.714 0 7.071-1.465 8.535C19.072 22 16.714 22 12 22s-7.071 0-8.536-1.465C2 19.072 2 16.714 2 12Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m7 14l2.293-2.293a1 1 0 0 1 1.414 0l1.586 1.586a1 1 0 0 0 1.414 0L17 10m0 0v2.5m0-2.5h-2.5" />
                                    </g>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="text-sm space-x-1 md:block hidden">
                        <Link href="/admin/support" class="flex items-center space-x-1 underline">
                        <span>{{ $t('View all') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m8 16l8-8m0 0h-6m6 0v6"></path>
                        </svg>
                        </Link>
                    </div>
                </div>
                <div class="bg-slate-100 col-span-2 md:col-span-1 md:bg-white rounded-lg p-3">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-slate-600">{{ $t('Total messages') }}</h2>
                            <h1 class="text-xl text-gray-600">{{ props.totalMessages }}</h1>
                        </div>
                        <div class="flex">
                            <span class="bg-secondary/10 p-3 rounded-full self-start">
                                <svg class="text-secondary" xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                    viewBox="0 0 24 24">
                                    <g fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path
                                            d="M2 12c0-4.714 0-7.071 1.464-8.536C4.93 2 7.286 2 12 2c4.714 0 7.071 0 8.535 1.464C22 4.93 22 7.286 22 12c0 4.714 0 7.071-1.465 8.535C19.072 22 16.714 22 12 22s-7.071 0-8.536-1.465C2 19.072 2 16.714 2 12Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m7 14l2.293-2.293a1 1 0 0 1 1.414 0l1.586 1.586a1 1 0 0 0 1.414 0L17 10m0 0v2.5m0-2.5h-2.5" />
                                    </g>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="md:grid md:grid-cols-2 gap-x-4 mt-8">
                <div>
                    <div id="chart" class="md:block hidden">
                        <apexchart type="area" height="350" :options="chartOptions" :series="series"></apexchart>
                    </div>
                </div>
                <div class="bg-white rounded-lg py-4 md:px-4 h-[fit-content]">
                    <h1>{{ $t('Recent transactions') }}</h1>
                    <p class="text-sm">{{ $t('Below are your most recent transactions') }}</p>
                    <div v-if="props.payments.length === 0" class="md:p-5 border-2 border-dashed mt-4">
                        <p class="text-sm text-center">{{ $t('You do not have any transactions') }}</p>
                    </div>
                    <div v-else class="space-y-1">
                        <div v-for="(item, index) in props.payments.data" :key="index"
                            class="mt-4 border-l-4 bg-gray-50 flex justify-between p-2 text-sm">
                            <div class="capitalize">{{ item.organization.name }}</div>
                            <div class="self-end">
                                <span class="bg-[#ffe5b4] p-1 rounded-md text-xs px-2 capitalize">{{ item.amount
                                }}</span>
                            </div>
                        </div>
                        <Link href="/admin/payment-logs" class="flex items-center space-x-1 underline pt-3 text-sm">
                        <span>{{ $t('View all') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m8 16l8-8m0 0h-6m6 0v6"></path>
                        </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from "./Layout/App.vue";
import { computed, defineProps, ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const user = computed(() => usePage().props.auth.user);

const props = defineProps({
    title: { type: String },
    payments: { type: Object },
    totalRevenue: { type: String },
    userCount: { type: Number },
    openTickets: { type: Number },
    totalMessages: { type: Number },
    period: { type: Object },
    newUsers: { type: Object },
    revenue: { type: Object },
});

const chartOptions = {
    chart: {
        height: 350,
        type: 'area',
        toolbar: {
            show: false
        },
        zoom: {
            enabled: false
        }
    },
    colors: ['#ff5100'],
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.7,
            opacityTo: 0.9,
            stops: [0, 90, 100]
        }
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        width: 1,
        curve: 'smooth'
    },
    xaxis: {
        type: 'datetime',
        categories: props.period
    },
    tooltip: {
        x: {
            format: 'dd/MM/yy HH:mm'
        },
    },
};

const series = [
    {
        name: 'New Customers',
        data: props.newUsers
    }, {
        name: 'Revenue',
        data: props.revenue
    }
];
</script> -->

<!-- ========================================= NEW UI CODE ==================================== -->

<template>
    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50 to-gray-50 p-4 md:p-8">

            <!-- Top Header Bar -->
            <div class="mb-8 border-2 border-primary/10 rounded-2xl p-6 bg-white shadow-sm">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between flex-wrap gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div
                                class="w-12 h-12 bg-primary rounded-2xl flex items-center justify-center shadow-lg shadow-primary/30">
                                <BarChart3 class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h1 class="text-3xl md:text-4xl font-bold text-gray-900">
                                    {{ $t("Welcome back") }}, {{ user?.first_name }} 👋
                                </h1>
                                <p class="text-gray-600 text-sm">{{ $t("Dashboard") }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Action Buttons -->
                    <div class="flex items-center flex-wrap ml-auto gap-2">
                        <Link href="/admin/organizations/create"
                            class="bg-primary hover:bg-primary/90 text-white font-semibold px-4 md:px-6 py-2.5 rounded-xl transition-all duration-300 hover:scale-[1.02] shadow-lg flex items-center gap-2">
                        <Plus class="w-4 h-4" />
                        <span class="inline">{{ $t("Add organization") }}</span>
                        </Link>
                        <Link href="/admin/users/create"
                            class="bg-primary hover:bg-primary/90 text-white font-semibold px-4 md:px-6 py-2.5 rounded-xl transition-all duration-300 hover:scale-[1.02] shadow-lg flex items-center gap-2">
                        <Plus class="w-4 h-4" />
                        <span class="inline">{{ $t("Add user") }}</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Key Metrics Header -->
            <div class="flex items-center gap-2 mb-4">
                <TrendingUp class="w-5 h-5 text-primary" />
                <h3 class="text-gray-900 font-semibold text-lg">{{ $t("Key Metrics") }}</h3>
            </div>

            <!-- Stats Dashboard -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
                <!-- Total Revenue -->
                <div class="group relative">
                    <div
                        class="absolute -inset-0.5 bg-gradient-to-r from-primary to-orange-400 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                    </div>
                    <div
                        class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-primary/30 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-primary/10">
                        <div class="flex justify-between items-start mb-4">
                            <div
                                class="bg-gradient-to-br from-primary to-orange-500 rounded-xl p-3 shadow-md shadow-primary/30">
                                <IndianRupee class="w-6 h-6 text-white" />
                            </div>
                            <div
                                class="flex items-center gap-1 bg-emerald-50 rounded-lg px-2 py-1 border border-emerald-200">
                                <TrendingUp class="w-3 h-3 text-emerald-600" />
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm font-medium mb-1">{{ $t("Total revenue") }}</p>
                        <p class="text-gray-900 text-3xl font-bold mb-3">{{ props.totalRevenue }}</p>
                        <Link href="/admin/payment-logs"
                            class="inline-flex items-center gap-1 text-primary hover:text-primary/80 text-sm font-medium group-hover:gap-2 transition-all">
                        <span>{{ $t("View all") }}</span>
                        <ArrowRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>

                <!-- Active Users -->
                <div class="group relative">
                    <div
                        class="absolute -inset-0.5 bg-gradient-to-r from-blue-400 to-cyan-400 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                    </div>
                    <div
                        class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-blue-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/10">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl p-3 shadow-md">
                                <Users class="w-6 h-6 text-white" />
                            </div>
                            <div
                                class="flex items-center gap-1 bg-emerald-50 rounded-lg px-2 py-1 border border-emerald-200">
                                <TrendingUp class="w-3 h-3 text-emerald-600" />
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm font-medium mb-1">{{ $t("Active users") }}</p>
                        <p class="text-gray-900 text-3xl font-bold mb-3">{{ props.userCount }}</p>
                        <Link href="/admin/users"
                            class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 text-sm font-medium group-hover:gap-2 transition-all">
                        <span>{{ $t("View all") }}</span>
                        <ArrowRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>

                <!-- Open Tickets -->
                <div class="group relative">
                    <div
                        class="absolute -inset-0.5 bg-gradient-to-r from-purple-400 to-pink-400 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                    </div>
                    <div
                        class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-purple-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-purple-500/10">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl p-3 shadow-md">
                                <Ticket class="w-6 h-6 text-white" />
                            </div>
                            <div class="flex items-center gap-1 bg-red-50 rounded-lg px-2 py-1 border border-red-200">
                                <AlertCircle class="w-3 h-3 text-red-600" />
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm font-medium mb-1">{{ $t("Open tickets") }}</p>
                        <p class="text-gray-900 text-3xl font-bold mb-3">{{ props.openTickets }}</p>
                        <Link href="/admin/support"
                            class="inline-flex items-center gap-1 text-purple-600 hover:text-purple-700 text-sm font-medium group-hover:gap-2 transition-all">
                        <span>{{ $t("View all") }}</span>
                        <ArrowRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>

                <!-- Total Messages -->
                <div class="group relative col-span-2 lg:col-span-1">
                    <div
                        class="absolute -inset-0.5 bg-gradient-to-r from-emerald-400 to-green-400 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                    </div>
                    <div
                        class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-emerald-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-emerald-500/10">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-gradient-to-br from-emerald-500 to-green-500 rounded-xl p-3 shadow-md">
                                <MessageSquare class="w-6 h-6 text-white" />
                            </div>
                            <div
                                class="flex items-center gap-1 bg-emerald-50 rounded-lg px-2 py-1 border border-emerald-200">
                                <TrendingUp class="w-3 h-3 text-emerald-600" />
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm font-medium mb-1">{{ $t("Total messages") }}</p>
                        <p class="text-gray-900 text-3xl font-bold mb-3">{{ props.totalMessages }}</p>
                        <button
                            class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-700 text-sm font-medium group-hover:gap-2 transition-all">
                            <span>{{ $t("View all") }}</span>
                            <ArrowRight class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="w-full space-y-6">
                <!-- Analytics Chart -->
                <div class="relative group">
                    <div
                        class="absolute -inset-0.5 bg-gradient-to-r from-primary/30 to-blue-400/30 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                    </div>
                    <div class="relative bg-white border border-gray-200 rounded-2xl p-6 shadow-lg">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-gray-900 text-xl font-bold mb-1">{{ $t("Revenue & Growth Analytics")
                                }}</h3>
                                <p class="text-gray-600 text-sm">{{ $t("Track revenue and user growth trends") }}
                                </p>
                            </div>
                            <div class="bg-primary rounded-xl p-3 shadow-md shadow-primary/30">
                                <Activity class="w-6 h-6 text-white" />
                            </div>
                        </div>
                        <div id="chart">
                            <apexchart type="area" height="350" :options="chartOptions" :series="series">
                            </apexchart>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="relative group">
                    <div
                        class="absolute -inset-0.5 bg-gradient-to-r from-pink-300 to-rose-300 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                    </div>
                    <div class="relative bg-white border border-gray-200 rounded-2xl p-6 shadow-lg">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-gray-900 text-xl font-bold mb-1">{{ $t("Recent transactions") }}
                                </h3>
                                <p class="text-gray-600 text-sm">{{ $t("Below are your most recent transactions") }}
                                </p>
                            </div>
                            <div class="bg-gradient-to-br from-pink-500 to-rose-500 rounded-xl p-3 shadow-md">
                                <CreditCard class="w-6 h-6 text-white" />
                            </div>
                        </div>

                        <div v-if="props.payments.length === 0"
                            class="border-2 border-dashed border-gray-300 rounded-2xl p-10 text-center bg-gray-50">
                            <div
                                class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                                <Inbox class="w-10 h-10 text-gray-400" />
                            </div>
                            <p class="text-gray-600 text-sm">{{ $t("You do not have any transactions") }}</p>
                        </div>

                        <div v-else class="space-y-3">
                            <div v-for="(item, index) in props.payments.data" :key="index"
                                class="group/item relative overflow-hidden bg-gradient-to-r from-orange-50 to-gray-50 hover:from-orange-100 hover:to-gray-100 border border-gray-200 hover:border-primary/30 rounded-xl p-4 transition-all duration-300">
                                <div
                                    class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-primary to-orange-400">
                                </div>
                                <div class="flex items-center justify-between pl-3">
                                    <div class="flex items-center gap-4 flex-1">
                                        <div class="bg-primary/10 border border-primary/20 rounded-lg p-2.5">
                                            <CreditCard class="w-4 h-4 text-primary" />
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-gray-900 font-medium capitalize">{{
                                                item.organization.name }}</p>
                                            <p class="text-gray-500 text-xs">{{ new
                                                Date(item.created_at).toLocaleString() }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-gray-900 font-bold">{{ item.amount }}</span>
                                        <span
                                            class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 text-xs font-semibold px-3 py-1.5 rounded-full capitalize border border-amber-200">
                                            <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></div>
                                            {{ item.status }}
                                        </span>
                                        <ChevronRight
                                            class="w-4 h-4 text-gray-400 opacity-0 group-hover/item:opacity-100 transition-opacity" />
                                    </div>
                                </div>
                            </div>

                            <Link href="/admin/payment-logs"
                                class="inline-flex items-center gap-2 text-sm text-primary hover:text-primary/80 font-medium mt-4 group/link">
                            <span>{{ $t("View all transactions") }}</span>
                            <ArrowRight class="w-4 h-4 group-hover/link:translate-x-1 transition-transform" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./Layout/App.vue";
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import { trans } from "laravel-vue-i18n";
import {
    BarChart3, Users, TrendingUp, MessageSquare,
    Ticket, ArrowRight, Activity, CreditCard, AlertCircle,
    ChevronRight, Plus, Inbox,
    IndianRupee
} from 'lucide-vue-next';

const user = computed(() => usePage().props.auth.user);

const props = defineProps({
    title: { type: String },
    payments: { type: Object },
    totalRevenue: { type: String },
    userCount: { type: Number },
    openTickets: { type: Number },
    totalMessages: { type: Number },
    period: { type: Object },
    newUsers: { type: Object },
    revenue: { type: Object },
});

const chartOptions = {
    chart: {
        height: 350,
        type: "area",
        toolbar: {
            show: false,
        },
        zoom: {
            enabled: false,
        },
        background: 'transparent',
    },
    colors: ["#ff5100", "#3b82f6"],
    fill: {
        type: "gradient",
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.5,
            opacityTo: 0.1,
            stops: [0, 90, 100],
        },
    },
    dataLabels: {
        enabled: false,
    },
    stroke: {
        width: 3,
        curve: "smooth",
    },
    xaxis: {
        type: "datetime",
        categories: props.period,
        labels: {
            style: {
                colors: '#ff5100',
                fontSize: '12px',
            }
        },
        axisBorder: {
            color: '#fed7aa',
        },
        axisTicks: {
            color: '#fed7aa',
        }
    },
    yaxis: {
        labels: {
            style: {
                colors: '#ff5100',
                fontSize: '12px',
            }
        }
    },
    tooltip: {
        x: {
            format: "dd/MM/yy HH:mm",
        },
        theme: 'light',
        style: {
            fontSize: '12px',
        }
    },
    grid: {
        borderColor: '#fed7aa',
        strokeDashArray: 4,
        opacity: 0.5,
    },
    legend: {
        position: 'top',
        horizontalAlign: 'left',
        fontSize: '14px',
        fontWeight: 600,
        labels: {
            colors: '#ff5100',
        },
        markers: {
            width: 12,
            height: 12,
            radius: 6,
        }
    }
};

const series = [
    {
        name: trans("Revenue"),
        data: props.revenue,
    },
    {
        name: trans("New Customers"),
        data: props.newUsers,
    },
];
</script>

<style scoped>
@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Custom scrollbar for light theme */
:deep(*::-webkit-scrollbar) {
    width: 8px;
    height: 8px;
}

:deep(*::-webkit-scrollbar-track) {
    background: rgba(255, 81, 0, 0.05);
    border-radius: 10px;
}

:deep(*::-webkit-scrollbar-thumb) {
    background: rgba(255, 81, 0, 0.3);
    border-radius: 10px;
}

:deep(*::-webkit-scrollbar-thumb:hover) {
    background: rgba(255, 81, 0, 0.5);
}
</style>