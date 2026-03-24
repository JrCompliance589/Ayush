<template>
    <div class="">
        <div class="space-y-6 mb-12">
            <div class="relative flex justify-between gap-4">
                <!-- Search Bar -->
                <div class="relative group w-full max-w-screen-sm">
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
                        <input @input="search" v-model="statsFilters.search" type="text"
                            class="outline-none px-4 w-full bg-transparent text-gray-700 placeholder-gray-400 font-medium"
                            :placeholder="$t('Search quotations, proformas, and payment receipts')">
                        <button v-if="isSearching === false && statsFilters.search" @click="clearSearch" type="button"
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
                                    <animate attributeName="opacity" dur="0.6s" keyTimes="0;0.5;1"
                                        repeatCount="indefinite" values="1;1;0" />
                                </circle>
                                <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                                    <animateTransform attributeName="transform" begin="0.2s" calcMode="discrete"
                                        dur="2.4s" repeatCount="indefinite" type="rotate"
                                        values="30 12 12;120 12 12;210 12 12;300 12 12" />
                                    <animate attributeName="opacity" begin="0.2s" dur="0.6s" keyTimes="0;0.5;1"
                                        repeatCount="indefinite" values="1;1;0" />
                                </circle>
                                <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                                    <animateTransform attributeName="transform" begin="0.4s" calcMode="discrete"
                                        dur="2.4s" repeatCount="indefinite" type="rotate"
                                        values="60 12 12;150 12 12;240 12 12;330 12 12" />
                                    <animate attributeName="opacity" begin="0.4s" dur="0.6s" keyTimes="0;0.5;1"
                                        repeatCount="indefinite" values="1;1;0" />
                                </circle>
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="ml-auto flex items-center gap-4 flex-wrap">
                    <button @click="fetchInvoices"
                        class="bg-primary text-white font-semibold px-4 md:px-6 py-2.5 rounded-xl transition-all duration-300 hover:scale-[1.02] shadow-lg flex items-center gap-2">
                        <RefreshCcw :class="loading ? 'animate-spin' : ''" />
                    </button>
                    <QuotationInvoiceGenerator :refresh="refresh" @update:refresh="refresh = $event" />
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center items-center py-12">
                <Loader2 class="w-8 h-8 animate-spin text-blue-500" />
                <span class="ml-3 text-gray-600 font-medium">Loading...</span>
            </div>

            <!-- Stats Filters -->

            <div v-else class="mb-6">
                <div class="p-4 pt-0 flex items-center justify-end flex-wrap gap-4">
                    <button @click="openAnalytics = true"
                        class="flex justify-center text-sm items-center gap-2 text-green-600 bg-slate-50 py-2 px-4 rounded-lg transition-colors">
                        <ExternalLink class="w-4 h-4" /> Open Detailed Analytics
                    </button>
                    <InvoiceAnalyticsDashboard :isOpen="openAnalytics" @close="openAnalytics = false" />
                    <button @click="showStatsFilters = !showStatsFilters"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        <Filter class="w-4 h-4" />
                        {{ showStatsFilters ? 'Hide' : 'Show' }} Filters
                    </button>
                </div>

                <!-- Stats Filters -->
                <transition enter-active-class="transition-all duration-300 ease-out"
                    leave-active-class="transition-all duration-200 ease-in" enter-from-class="opacity-0 max-h-0"
                    enter-to-class="opacity-100 max-h-96" leave-from-class="opacity-100 max-h-96"
                    leave-to-class="opacity-0 max-h-0">
                    <div v-show="showStatsFilters" class="p-4 mb-4 bg-slate-50 border border-slate-200 rounded-md">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                                <input type="date" v-model="statsFilters.startDate"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                                <input type="date" v-model="statsFilters.endDate"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                                <select v-model="statsFilters.paymentStatus"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All</option>
                                    <option value="paid">Paid</option>
                                    <option value="unpaid">Unpaid</option>
                                    <option value="partial">Partial</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Platform Charge Type</label>
                                <select v-model="statsFilters.platformChargeType"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All</option>
                                    <option value="Monthly">Monthly</option>
                                    <option value="Yearly">Yearly</option>
                                </select>
                            </div>
                            <div v-if="isAdmin">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Team Member</label>
                                <select v-model="statsFilters.createdById"
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All</option>
                                    <option v-for="member in teamMembers" :key="member.id" :value="member.id">
                                        {{ member.full_name || member.first_name }} - {{ member.role }}
                                    </option>
                                </select>
                            </div>
                            <div :class="isAdmin ? 'col-span-3' : 'col-span-full'">
                                <div class="w-fit flex items-end ml-auto gap-2">
                                    <button @click="applyStatsFilters"
                                        class="flex-1 px-4 py-2 bg-primary/90 text-white rounded-lg hover:bg-primary transition font-medium">
                                        Apply Filters
                                    </button>
                                    <button @click="resetStatsFilters"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                                        title="Reset Filters">
                                        <RefreshCw class="w-4 h-6" />
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </transition>

                <!-- Stats Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 my-4">

                    <!-- Total Revenue Card -->
                    <div class="group relative h-full">
                        <div
                            class="absolute -inset-0.5 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                        </div>
                        <div
                            class="h-full relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-green-300 transition-all duration-300 shadow-md hover:shadow-lg hover:shadow-green-500/10">
                            <div class="flex justify-between items-start mb-4">
                                <div class="bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl p-3 shadow-md">
                                    <IndianRupeeIcon class="w-6 h-6 text-white" />
                                </div>
                            </div>

                            <p class="text-gray-600 text-sm font-medium mb-1">Total Revenue</p>
                            <p class="text-gray-900 text-3xl font-bold mb-2">
                                ₹{{ formatCurrency(invoiceData?.stats?.totals?.totalRevenue) }}
                            </p>

                            <div class="space-y-1 text-xs">
                                <p class="text-green-600">
                                    GST: ₹{{ formatCurrency(invoiceData?.stats?.totals?.totalGST) }}
                                </p>
                                <p class="text-emerald-600">
                                    Collected: ₹{{ formatCurrency(invoiceData?.stats?.totals?.totalCollected) }}
                                </p>
                                <p class="text-gray-500">
                                    Discount: ₹{{ formatCurrency(invoiceData?.stats?.totals?.totalDiscount) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Invoices Card -->
                    <div class="group relative h-full">
                        <div
                            class="absolute -inset-0.5 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                        </div>
                        <div
                            class="h-full relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-blue-300 transition-all duration-300 shadow-md hover:shadow-lg hover:shadow-blue-500/10">
                            <div class="flex justify-between items-start mb-4">
                                <div class="bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl p-3 shadow-md">
                                    <FileText class="w-6 h-6 text-white" />
                                </div>
                            </div>

                            <p class="text-gray-600 text-sm font-medium mb-1">Total Invoices</p>
                            <p class="text-gray-900 text-3xl font-bold mb-2">
                                {{ invoiceData?.stats?.totals?.totalInvoices }}
                            </p>

                            <p class="text-xs text-blue-600">
                                Amount: ₹{{ formatCurrency(invoiceData?.stats?.totals?.totalRevenue) }}
                            </p>

                            <p class="text-xs text-indigo-600 mt-1">
                                Collection Rate:
                                {{
                                    invoiceData?.stats?.totals?.totalRevenue
                                        ? ((invoiceData.stats.totals.totalCollected / invoiceData.stats.totals.totalRevenue) *
                                            100).toFixed(1)
                                        : 0
                                }}%
                            </p>
                        </div>
                    </div>

                    <!-- Paid Invoices Card -->
                    <div class="group relative h-full">
                        <div
                            class="absolute -inset-0.5 bg-gradient-to-r from-emerald-500 to-green-500 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                        </div>
                        <div
                            class="h-full relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-emerald-300 transition-all duration-300 shadow-md hover:shadow-lg hover:shadow-emerald-500/10">
                            <div class="flex justify-between items-start mb-4">
                                <div class="bg-gradient-to-br from-emerald-500 to-green-500 rounded-xl p-3 shadow-md">
                                    <CheckCircle class="w-6 h-6 text-white" />
                                </div>
                            </div>

                            <p class="text-gray-600 text-sm font-medium mb-1">Paid Invoices</p>
                            <p class="text-gray-900 text-3xl font-bold mb-2">
                                {{ invoiceData?.stats?.byStatus?.paid?.count }}
                            </p>

                            <p class="text-xs text-emerald-600">
                                Collected: ₹{{ formatCurrency(invoiceData?.stats?.byStatus?.paid?.paidAmount) }}
                            </p>
                        </div>
                    </div>

                    <!-- Unpaid + Partial Invoices Card -->
                    <div class="group relative h-full">
                        <div
                            class="absolute -inset-0.5 bg-gradient-to-r from-red-500 to-rose-500 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                        </div>

                        <div
                            class="h-full relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-red-300 transition-all duration-300 shadow-md hover:shadow-lg hover:shadow-red-500/10">
                            <div class="flex justify-between items-start mb-4">
                                <div class="bg-gradient-to-br from-red-500 to-rose-500 rounded-xl p-3 shadow-md">
                                    <AlertCircle class="w-6 h-6 text-white" />
                                </div>
                            </div>

                            <p class="text-gray-600 text-sm font-medium mb-1">Unpaid / Partial</p>
                            <p class="text-gray-900 text-3xl font-bold mb-2">
                                {{
                                    (invoiceData?.stats?.byStatus?.unpaid?.count || 0) +
                                    (invoiceData?.stats?.byStatus?.partial?.count || 0)
                                }}
                            </p>

                            <div class="space-y-1 text-xs">
                                <p class="text-red-600">
                                    Outstanding: ₹{{
                                        formatCurrency(
                                            (invoiceData?.stats?.byStatus?.unpaid?.balanceAmount || 0) +
                                            (invoiceData?.stats?.byStatus?.partial?.balanceAmount || 0)
                                        )
                                    }}
                                </p>

                                <p class="text-orange-600">
                                    Partial: {{ invoiceData?.stats?.byStatus?.partial?.count || 0 }}
                                </p>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
            <!-- Table Container -->
            <div class="bg-white rounded-3xl shadow-md border-2 border-primary/10 p-4">
                <!-- Table -->
                <div class="">
                    <div class="">
                        <table class="w-full">

                            <!-- Header -->
                            <thead class="border-b border-primary/10">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Q / PI No.
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Company
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Contact
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Amount
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Status
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase">Actions
                                    </th>
                                </tr>
                            </thead>

                            <!-- Body -->
                            <tbody class="divide-y divide-primary/10">

                                <tr v-for="item in invoiceData?.invoices" :key="item.id"
                                    class="hover:bg-orange-50/30 transition-all duration-200 group">
                                    <!-- Q / PI -->
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ item.proforma_number || item.quotation_number }}
                                        </div>
                                        <div v-if="item.payment_id" class="text-xs text-gray-500 mt-1">
                                            Ref: {{ item.payment_id }}
                                        </div>
                                    </td>

                                    <!-- Company -->
                                    <td class="px-6 py-5 max-w-[200px]">
                                        <div class="text-sm font-medium text-gray-900 line-clamp-2">
                                            {{ item.company_name }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ item.email }}
                                        </div>
                                    </td>

                                    <!-- Contact -->
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ item.contact_person }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ item.phone }}
                                        </div>
                                    </td>

                                    <!-- Date -->
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ item.proforma_date || item.quotation_date }}
                                        </div>

                                        <div v-if="item.payment_status === 'paid' || item.payment_status === 'partial'"
                                            class="text-xs text-gray-500 mt-1">
                                            Last paid: {{ item.updatedAt?.split('T')[0] }}
                                        </div>

                                        <div v-else-if="item.payment_status === 'expired'"
                                            class="text-xs text-red-500 mt-1">
                                            Expired on {{ item.proforma_valid_until_date }}
                                        </div>
                                    </td>

                                    <!-- Amount -->
                                    <td class="px-6 py-5">
                                        <div class="text-base font-bold text-gray-900">
                                            ₹{{ formatAmount(item.total) }}
                                        </div>

                                        <div v-if="item.payment_status !== 'unpaid'"
                                            class="text-xs text-green-600 mt-1">
                                            Paid: ₹{{ formatAmount(item.total_net_received || 0) }}
                                        </div>

                                        <!-- <div v-if="item.payment_status === 'partial'" class="text-xs text-red-500">
                                            Balance: ₹{{ formatAmount(item.balance_amount || 0) }}
                                        </div> -->

                                        <div v-if="getPaymentCompletionText(item)" class="text-xs text-gray-500 mt-1">
                                            {{ getPaymentCompletionText(item) }}
                                        </div>

                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-5">
                                        <span :class="getStatusBadgeClass(item)"
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium">
                                            {{ getStatusText(item) }}
                                        </span>

                                        <div v-if="item.payment_status === 'paid'" class="text-xs text-gray-500 mt-1">
                                            via {{ item.payment_ledger?.[0]?.method || '—' }}
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-5 text-right">
                                        <div class="relative inline-block">
                                            <button @click="toggleDropdown(item.id)"
                                                class="inline-flex justify-center rounded-md text-sm font-medium text-black hover:bg-[#F6F7F9] hover:rounded-full p-2 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24">
                                                    <path fill="currentColor"
                                                        d="M12 16a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2Z" />
                                                </svg>
                                            </button>

                                            <!-- Dropdown Menu -->
                                            <Transition enter-active-class="transition ease-out duration-100"
                                                enter-from-class="transform opacity-0 scale-95"
                                                enter-to-class="transform opacity-100 scale-100"
                                                leave-active-class="transition ease-in duration-75"
                                                leave-from-class="transform opacity-100 scale-100"
                                                leave-to-class="transform opacity-0 scale-95">
                                                <div v-if="openDropdownId === item.id"
                                                    class="absolute right-0 mt-2 w-64 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50 overflow-hidden">
                                                    <div class="py-1">
                                                        <button
                                                            @click="openCustomerDashboard(item?.created_for_id); closeDropdown()"
                                                            class="text-teal-600 w-full text-left px-4 py-3 text-sm transition-colors flex items-center gap-3 hover:bg-gray-50">
                                                            <Eye size="18" class="flex-shrink-0" />
                                                            <span class="font-medium truncate">{{ $t("View details")
                                                                }}</span>
                                                        </button>

                                                        <button v-for="action in getAvailableActions(item)"
                                                            :key="action.key"
                                                            @click="handleAction(action.key, item); closeDropdown()"
                                                            class="w-full text-left px-4 py-3 text-sm transition-colors flex items-center gap-3 hover:bg-gray-50"
                                                            :class="action.colorClass">
                                                            <component :is="action.icon" :size="18"
                                                                class="flex-shrink-0" />
                                                            <span class="font-medium truncate">{{ $t(action.label)
                                                                }}</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </Transition>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Empty -->
                                <tr v-if="invoiceData?.invoices?.length === 0 && !loading">
                                    <td colspan="7" class="px-6 py-16 text-center text-gray-500">
                                        No invoices found
                                    </td>
                                </tr>

                                <!-- Loading -->
                                <tr v-if="loading">
                                    <td colspan="7" class="px-6 py-16 text-center text-gray-500">
                                        Loading invoices...
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                </div>

                <!-- Pagination -->
                <div v-if="invoiceData?.pagination?.totalItems > invoiceData?.pagination?.limit"
                    class="flex items-center justify-start gap-2">
                    <p>
                        {{ invoiceData?.pagination?.totalItems }} Items
                    </p>

                    <div class="flex items-center gap-3">
                        <!-- First Page Button -->
                        <button @click="goToFirstPage" :disabled="invoiceData?.pagination?.currentPage === 1"
                            class="flex items-center justify-center w-12 h-12 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                            aria-label="First page">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="text-gray-600">
                                <polyline points="11 17 6 12 11 7"></polyline>
                                <polyline points="18 17 13 12 18 7"></polyline>
                            </svg>
                        </button>

                        <!-- Previous Page Button -->
                        <button @click="goToPreviousPage" :disabled="invoiceData?.pagination?.currentPage === 1"
                            class="flex items-center justify-center w-12 h-12 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                            aria-label="Previous page">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="text-gray-600">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                        </button>

                        <!-- Page Indicator -->
                        <div
                            class="flex items-center justify-center min-w-[120px] h-12 px-6 bg-white border border-gray-200 rounded-lg">
                            <span class="text-gray-700 font-medium text-base">
                                {{ invoiceData?.pagination?.currentPage }}
                                of
                                {{ invoiceData?.pagination?.totalPages }}
                            </span>
                        </div>

                        <!-- Next Page Button -->
                        <button @click="goToNextPage"
                            :disabled="invoiceData?.pagination?.currentPage === invoiceData?.pagination?.totalPages"
                            class="flex items-center justify-center w-12 h-12 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                            aria-label="Next page">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="text-gray-600">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </button>

                        <!-- Last Page Button -->
                        <button @click="goToLastPage"
                            :disabled="invoiceData?.pagination?.currentPage === invoiceData?.pagination?.totalPages"
                            class="flex items-center justify-center w-12 h-12 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                            aria-label="Last page">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="text-gray-600">
                                <polyline points="13 17 18 12 13 7"></polyline>
                                <polyline points="6 17 11 12 6 7"></polyline>
                            </svg>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Share Options Modal -->
        <div v-if="showShareOptions" class="modal-overlay" @click.self="closeShareModal">
            <div class="share-modal">
                <div class="share-header">
                    <h2 class="share-title">Share PDF</h2>
                    <button @click="closeShareModal" class="close-btn">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>

                <div class="share-body">
                    <p class="share-subtitle">Choose how you want to share this pdf</p>

                    <div class="share-options">
                        <!-- Share on Free WhatsApp -->
                        <button @click="shareOnFreeWhatsApp" :disabled="isSharing.freeWhatsapp"
                            class="share-option whatsapp">
                            <div class="share-icon-wrapper whatsapp-bg">
                                <svg v-if="!isSharing.freeWhatsapp" width="28" height="28" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                                    </path>
                                </svg>
                                <svg v-else class="animate-spin" width="28" height="28" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="2" x2="12" y2="6"></line>
                                    <line x1="12" y1="18" x2="12" y2="22"></line>
                                    <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
                                    <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
                                    <line x1="2" y1="12" x2="6" y2="12"></line>
                                    <line x1="18" y1="12" x2="22" y2="12"></line>
                                    <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
                                    <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
                                </svg>
                            </div>

                            <div class="share-content">
                                <h3>Share on WhatsApp (Direct)</h3>
                                <p>{{ isSharing.freeWhatsapp ? 'Sharing...' : 'Send pdf when chat is open' }}</p>
                            </div>
                        </button>

                        <!-- WhatsApp Share -->
                        <button @click="shareOnWhatsApp" :disabled="isSharing.whatsapp" class="share-option whatsapp">
                            <div class="share-icon-wrapper whatsapp-bg">
                                <svg v-if="!isSharing.whatsapp" width="28" height="28" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                                    </path>
                                </svg>
                                <svg v-else class="animate-spin" width="28" height="28" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="2" x2="12" y2="6"></line>
                                    <line x1="12" y1="18" x2="12" y2="22"></line>
                                    <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
                                    <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
                                    <line x1="2" y1="12" x2="6" y2="12"></line>
                                    <line x1="18" y1="12" x2="22" y2="12"></line>
                                    <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
                                    <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
                                </svg>
                            </div>
                            <div class="share-content">
                                <h3>Share on WhatsApp (Template)</h3>
                                <p>{{ isSharing.whatsapp ? 'Sharing...' : 'Send pdf when chat is not open yet' }}
                                </p>
                            </div>
                        </button>



                        <!-- Email Share -->
                        <button @click="shareViaEmail" :disabled="isSharing.email || !currentPDF.email"
                            class="share-option email">
                            <div class="share-icon-wrapper email-bg">
                                <svg v-if="!isSharing.email" width="28" height="28" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                    </path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                <svg v-else class="animate-spin" width="28" height="28" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="2" x2="12" y2="6"></line>
                                    <line x1="12" y1="18" x2="12" y2="22"></line>
                                    <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
                                    <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
                                    <line x1="2" y1="12" x2="6" y2="12"></line>
                                    <line x1="18" y1="12" x2="22" y2="12"></line>
                                    <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
                                    <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
                                </svg>
                            </div>
                            <div class="share-content">
                                <h3>Share via Email</h3>
                                <p v-if="!currentPDF.email" class="text-red-500">Email not provided</p>
                                <p v-else-if="isSharing.email">Sending...</p>
                                <p v-else>Send to {{ currentPDF.email }}</p>
                            </div>
                        </button>

                        <!-- Download Again -->
                        <button @click="downloadPDF" :disabled="isSharing.downloading" class="share-option download">
                            <div class="share-icon-wrapper download-bg">
                                <svg v-if="!isSharing.downloading" width="28" height="28" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                <svg v-else class="animate-spin" width="28" height="28" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="2" x2="12" y2="6"></line>
                                    <line x1="12" y1="18" x2="12" y2="22"></line>
                                    <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
                                    <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
                                    <line x1="2" y1="12" x2="6" y2="12"></line>
                                    <line x1="18" y1="12" x2="22" y2="12"></line>
                                    <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
                                    <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
                                </svg>
                            </div>
                            <div class="share-content">
                                <h3 v-if="isSharing.downloading">Downloading...</h3>
                                <h3 v-else>Download</h3>
                                <p v-if="isSharing.downloading">Please wait...</p>
                                <p v-else>Save copy of the PDF</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- PDF Proforma Invoice Template (Hidden) -->
        <div v-if="generateCurrentProformaPDF" ref="pdfTemplate" id="pdf-template"
            class="pdf-content relative pb-8 flex flex-col justify-between">
            <!-- first page -->
            <div class="pdf-page">

                <div class="pdf-header">
                    <div class="company-info">
                        <img src="../../../images/nyifeBrand.png" alt="nyife-logo" width='220' height="73"
                            style="width: 220px !important; height: 73px !important;"></img>
                        <h2>Complia Services Ltd</h2>
                        <p>nyife.chat | info@nyife.chat | +91 11 430 22 315 </p>
                        <p>Plot no.9, Third Floor, Paschim Vihar Extn. Delhi-110063, India</p>
                        <p>GSTIN: 07AALCC1963C1ZT | CIN No: U70200DL2023PLC417528</p>
                    </div>
                    <div class="invoice-info">
                        <h3>PROFORMA INVOICE</h3>
                        <p><strong>Invoice #:</strong> {{ generateCurrentProformaPDF?.proforma_number }}</p>
                        <p><strong>Date:</strong> {{ generateCurrentProformaPDF?.proforma_date }}</p>
                        <p><strong>Due Date:</strong> {{ generateCurrentProformaPDF?.proforma_valid_until_date }}
                        </p>
                    </div>
                </div>

                <div class="client-info">
                    <h4>Bill To:</h4>
                    <p><strong>Mr/Ms: {{ generateCurrentProformaPDF?.contact_person || 'N/A' }}</strong></p>
                    <p><strong>Company:</strong> {{ generateCurrentProformaPDF?.company_name || 'N/A' }}</p>
                    <p><strong>Phone:</strong> {{ generateCurrentProformaPDF?.phone || 'N/A' }}</p>
                    <p v-if="generateCurrentProformaPDF?.email?.trim()"><strong>Email:</strong> {{
                        generateCurrentProformaPDF?.email || 'N/A' }}
                    </p>
                    <p class="w-[70%]" v-if="generateCurrentProformaPDF?.address?.trim()"><strong>Address:</strong> {{
                        generateCurrentProformaPDF?.address ||
                        'N/A' }}</p>
                    <p v-if="generateCurrentProformaPDF?.gst_number?.trim()"><strong>GSTIN:</strong> {{
                        generateCurrentProformaPDF?.gst_number
                        ||
                        'N/A' }}</p>
                </div>

                <!-- <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Description</th>
                            <th style="width: 150px; text-align: right;">Amount (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in getVisibleItems(generateCurrentProformaPDF, generateCurrentProformaPDF?.additional_fee)"
                            :key="index">
                            <td>{{ index + 1 }}</td>
                            <td>{{ item?.description }}</td>
                            <td style="text-align: right;">{{ formatCurrency(item?.amount) }}</td>
                        </tr>
                    </tbody>
                </table> -->

                <!-- UPDATE TABLE -->

                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Description</th>
                            <th style="width: 90px;">HSN/SAC</th>
                            <th style="width: 60px; text-align: center;">Qty</th>
                            <th style="width: 90px; text-align: right;">Rate</th>
                            <th style="width: 70px; text-align: center;">Discount</th>
                            <th style="width: 70px; text-align: center;">GST</th>
                            <th style="width: 110px; text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in getVisibleItems(generateCurrentProformaPDF, generateCurrentProformaPDF?.additional_fee)"
                            :key="index">
                            <td>{{ index + 1 }}</td>
                            <td>{{ item?.description }}</td>
                            <td style="text-align: center;">{{ item?.hsn_sac || 'N/A' }}</td>
                            <td style="text-align: center;">{{ item?.quantity || 1 }}</td>
                            <td style="text-align: right;">{{ formatCurrency(item?.unit_price) }}</td>
                            <td style="text-align: right;">({{ item?.discount ?? 0 }}%)
                                {{ formatCurrency(item?.discount_amount) }}</td>
                            <td style="text-align: right;">({{ item?.gst_rate ?? 0 }}%)
                                {{ formatCurrency(item?.gst_amount) }}</td>
                            <td style="text-align: right;">{{ formatCurrency(item?.amount) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="summary-section">
                    <div class="payment-qr">
                        <div class="qr-container">
                            <div class="qr-header">
                                <h4>Scan to Pay</h4>
                                <p>Quick & Secure Payment</p>
                            </div>
                            <div class="qr-code-wrapper">
                                <img :src="generateCurrentProformaPDF?.qrCode" alt="Payment QR Code" class="qr-code">
                            </div>
                        </div>
                    </div>

                    <table class="summary-table-wrapper">
                        <thead class="summary-table">
                            <tr>
                                <td>SUBTOTAL:</td>
                                <td>₹{{ formatCurrency(generateCurrentProformaPDF?.sub_total) }}</td>
                            </tr>
                            <tr v-if="Number(generateCurrentProformaPDF?.discount) > 0">
                                <td>DISCOUNT:</td>
                                <td class="discount-amount">-₹{{
                                    formatCurrency(generateCurrentProformaPDF?.discount_amount) }}</td>
                            </tr>
                            <tr v-if="Number(generateCurrentProformaPDF?.discount) > 0">
                                <td>AMOUNT AFTER DISCOUNT:</td>
                                <td>₹{{ formatCurrency(generateCurrentProformaPDF?.amount_after_discount) }}</td>
                            </tr>
                            <tr>
                                <td>GST:</td>
                                <td>₹{{ formatCurrency(generateCurrentProformaPDF?.GST_amount) }}</td>
                            </tr>
                            <tr class="total-row">
                                <td><strong>TOTAL AMOUNT DUE:</strong></td>
                                <td><strong>₹{{ formatCurrency(generateCurrentProformaPDF?.total) }}</strong></td>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div v-if="generateCurrentProformaPDF?.bank_info" class="bank-info">
                    <h4>Bank Account Details for Payment</h4>
                    <div class="bank-details-grid">
                        <div class="bank-detail-item">
                            <span class="label">Account Name:</span>
                            <span class="value">{{ generateCurrentProformaPDF?.bank_info?.account_name }}</span>
                        </div>
                        <div class="bank-detail-item">
                            <span class="label">Bank Name:</span>
                            <span class="value">{{ generateCurrentProformaPDF?.bank_info?.bank_name }} Bank</span>
                        </div>
                        <div class="bank-detail-item">
                            <span class="label">Account Number:</span>
                            <span class="value">{{ generateCurrentProformaPDF?.bank_info?.account_number }}</span>
                        </div>
                        <!-- <div class="bank-detail-item">
                                  <span class="label">Account Type:</span>
                                  <span class="value">{{ generateCurrentProformaPDF?.bank_info?.account_type?.charAt(0).toUpperCase() + generateCurrentProformaPDF?.bank_info?.account_type?.slice(1) }} Account</span>
                              </div> -->
                        <div class="bank-detail-item">
                            <span class="label">IFSC Code:</span>
                            <span class="value">{{ generateCurrentProformaPDF?.bank_info?.ifsc_code }}</span>
                        </div>
                        <!-- <div class="bank-detail-item full-width">
                                  <span class="label">Branch:</span>
                                  <span class="value">{{ generateCurrentProformaPDF?.bank_info?.branch }}</span>
                              </div> -->
                    </div>
                    <div class="bank-note">
                        <p>Please use the invoice number as payment reference. Share payment confirmation at
                            info@nyife.chat for faster processing.</p>
                    </div>
                </div>
            </div>

            <!-- second page -->
            <div class="pdf-page">

                <!-- Payment Terms Schedule Section -->
                <div v-if="generateCurrentProformaPDF?.payment_terms?.slabs?.length > 1" class="payment-schedule">
                    <h4>Payment Terms</h4>

                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Payment #</th>
                                <th>Remarks/Due Date</th>
                                <th>Percentage</th>
                                <th style="text-align: right;">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(slab, index) in (generateCurrentProformaPDF)?.payment_terms?.slabs"
                                :key="index">
                                <td>
                                    <span class="installment-number">Payment {{ index + 1 }}</span>
                                </td>
                                <td v-if="slab?.what_to_show === 'validity'">
                                    <span class="due-date">{{ (slab?.validity)?.split('T')[0] }}</span>
                                </td>
                                <td v-if="slab?.what_to_show === 'remarks'">
                                    <span class="due-date">{{ slab?.remarks || "N/A" }}</span>
                                </td>
                                <td style="text-wrap: nowrap;">
                                    <span class="percentage-badge">{{ slab?.calculatedPercentage }}%</span>
                                </td>
                                <td style="text-wrap: nowrap;">
                                    <span class="amount-value">₹{{ formatCurrency(slab?.amount) }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="payment-summary">
                        <div class="payment-summary-item">
                            <span class="label">Total Amount:</span>
                            <span class="value">₹{{
                                formatCurrency(generateCurrentProformaPDF?.payment_terms?.totalAmount)
                                }}</span>
                        </div>
                    </div>

                    <div class="payment-note">
                        <p><strong>Important:</strong> Please ensure timely payment as per the schedule above. Each
                            payment must be made by the due date mentioned. Use the invoice number as payment reference
                            for all transactions.</p>
                    </div>
                </div>


                <div class="terms" v-if="generateCurrentProformaPDF?.termsConditions?.length > 0">
                    <h4>Terms & Conditions</h4>
                    <div class="terms-grid">

                        <div v-for="(term, index) in generateCurrentProformaPDF.termsConditions" :key="index"
                            class="term-section">
                            <h5>{{ term.title }}</h5>
                            <p>{{ term.description }}</p>
                        </div>
                    </div>
                </div>

                <div class="signature">
                    <p>For any queries regarding this invoice, please contact us at info@nyife.chat</p>
                    <p class="signature-line">____________________</p>
                    <p class="name">{{ generateCurrentProformaPDF?.signature }}</p>
                    <p class="title">{{ generateCurrentProformaPDF?.designation }}</p>
                </div>

                <p class="absolute text-nowrap bottom-4 left-[50%] -translate-x-[50%] text-xs text-black/50">
                    This is an auto-generated proforma invoice. Please make payment to receive official tax invoice.
                </p>

            </div>
        </div>

        <!--  -->
        <!-- PDF Payment Receipt Template (Hidden) -->
        <div v-if="generateCurrentPaymentReceiptPDF" ref="pdfTemplatePaymentReceipt" id="pdf-template-payment-receipt"
            class="payment-receipt-content">
            <div class="pdf-page">
                <!-- Header matching Proforma Invoice -->
                <div class="pdf-header">
                    <div class="company-info">
                        <img src="../../../images/nyifeBrand.png" alt="nyife-logo" width="220" height="73"
                            style="width: 220px !important; height: 73px !important;">
                        <h2>Complia Services Ltd</h2>
                        <p>nyife.chat | info@nyife.chat | +91 11 430 22 315</p>
                        <p>Plot no.9, Third Floor, Paschim Vihar Extn. Delhi-110063, India</p>
                        <p>GSTIN: 07AALCC1963C1ZT | CIN No: U70200DL2023PLC417528</p>
                    </div>


                    <div class="invoice-info">
                        <h3>PAYMENT RECEIPT</h3>
                        <p><strong>Payment ID:</strong> {{ generateCurrentPaymentReceiptPDF?.payment_id }}</p>
                        <p><strong>Payment Date:</strong> {{
                            formatDateTimeIST(generateCurrentPaymentReceiptPDF?.paid_at) }}</p>
                        <p><strong>Status:</strong> <span class="font-bold">PAID</span></p>
                    </div>
                </div>

                <!-- Proforma Reference -->
                <div class="proforma-reference">
                    <strong>Payment Against Proforma Invoice:</strong> {{
                        generateCurrentPaymentReceiptPDF?.proforma_number }}
                </div>

                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Description</th>
                            <th style="width: 90px;">HSN/SAC</th>
                            <th style="width: 60px; text-align: center;">Qty</th>
                            <th style="width: 90px; text-align: right;">Rate</th>
                            <th style="width: 70px; text-align: center;">Discount</th>
                            <th style="width: 70px; text-align: center;">GST</th>
                            <th style="width: 110px; text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in getVisibleItems(generateCurrentPaymentReceiptPDF, generateCurrentPaymentReceiptPDF?.additional_fee)"
                            :key="index">
                            <td>{{ index + 1 }}</td>
                            <td>{{ item?.description }}</td>
                            <td style="text-align: center;">{{ item?.hsn_sac || 'N/A' }}</td>
                            <td style="text-align: center;">{{ item?.quantity || 1 }}</td>
                            <td style="text-align: right;">{{ formatCurrency(item?.unit_price) }}</td>
                            <td style="text-align: right;">({{ item?.discount ?? 0 }}%)
                                {{ formatCurrency(item?.discount_amount) }}</td>
                            <td style="text-align: right;">({{ item?.gst_rate ?? 0 }}%)
                                {{ formatCurrency(item?.gst_amount) }}</td>
                            <td style="text-align: right;">{{ formatCurrency(item?.amount) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="summary-section">
                    <table class="summary-table-wrapper">
                        <thead class="summary-table">
                            <tr>
                                <td>SUBTOTAL:</td>
                                <td>₹{{ formatCurrency(generateCurrentPaymentReceiptPDF?.sub_total) }}</td>
                            </tr>
                            <tr v-if="Number(generateCurrentPaymentReceiptPDF?.discount) > 0">
                                <td>DISCOUNT:</td>
                                <td class="discount-amount">-₹{{
                                    formatCurrency(generateCurrentPaymentReceiptPDF?.discount_amount) }}</td>
                            </tr>
                            <tr v-if="Number(generateCurrentPaymentReceiptPDF?.discount) > 0">
                                <td>AMOUNT AFTER DISCOUNT:</td>
                                <td>₹{{ formatCurrency(generateCurrentPaymentReceiptPDF?.amount_after_discount) }}</td>
                            </tr>
                            <tr>
                                <td>GST:</td>
                                <td>₹{{ formatCurrency(generateCurrentPaymentReceiptPDF?.GST_amount) }}</td>
                            </tr>
                            <tr class="total-row">
                                <td><strong>TOTAL AMOUNT:</strong></td>
                                <td><strong>₹{{ formatCurrency(generateCurrentPaymentReceiptPDF?.total) }}</strong></td>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="pdf-page">
                <!-- Two Column Layout -->
                <div class="content-grid">
                    <!-- Left Column: Payment & Amount Details -->
                    <div class="info-section">
                        <h3 class="section-title">Payment Information</h3>
                        <div class="info-list">
                            <div class="info-row">
                                <span class="label">Payment Method</span>
                                <span class="value">{{ generateCurrentPaymentReceiptPDF?.payment_method }}</span>
                            </div>
                            <div v-if="generateCurrentPaymentReceiptPDF.payment_terms_remarks?.trim()" class="info-row">
                                <span class="label">Payment Terms</span>
                                <span class="value">{{ generateCurrentPaymentReceiptPDF?.payment_terms_remarks }}</span>
                            </div>
                            <div v-if="generateCurrentPaymentReceiptPDF?.slab_ids?.length > 0" class="info-row">
                                <span class="label">Payment Against Slabs</span>
                                <span class="value">{{ generateCurrentPaymentReceiptPDF?.slab_ids?.join(", ") }}</span>
                            </div>
                            <div v-if="generateCurrentPaymentReceiptPDF?.has_payment_exception && Number(generateCurrentPaymentReceiptPDF?.exception_amount) > 0"
                                class="info-row">
                                <span class="label">Payment Exception Reason</span>
                                <span class="value">{{ generateCurrentPaymentReceiptPDF?.exception_reason }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Transaction Date</span>
                                <span class="value">{{ formatDateTimeIST(generateCurrentPaymentReceiptPDF?.paid_at)
                                    }}</span>
                            </div>
                        </div>

                        <h3 class="section-title">Amount Details</h3>
                        <div class="info-list">
                            <div class="info-row">
                                <span class="label">Base Amount</span>
                                <span class="value">₹{{ formatCurrency(generateCurrentPaymentReceiptPDF?.amount)
                                    }}</span>
                            </div>

                            <div v-if="Number(generateCurrentPaymentReceiptPDF?.tds_rate) > 0 && Number(generateCurrentPaymentReceiptPDF?.tds_amount) > 0"
                                class="info-row">
                                <span class="label">TDS</span>
                                <span class="value">-₹{{ formatCurrency(generateCurrentPaymentReceiptPDF?.tds_amount)
                                    }}</span>
                            </div>
                            <div v-if="generateCurrentPaymentReceiptPDF?.has_payment_exception && Number(generateCurrentPaymentReceiptPDF?.exception_amount) > 0"
                                class="info-row">
                                <span class="label">Exception Amount</span>
                                <span class="value">{{ Number(generateCurrentPaymentReceiptPDF?.excess_or_short_amount)
                                    > 0 ?
                                    "+" : "-" }}₹{{
                                        formatCurrency(generateCurrentPaymentReceiptPDF?.excess_or_short_amount) }}</span>
                            </div>
                            <div class="info-row total-row">
                                <span class="label">Total Amount Paid</span>
                                <span class="value total">₹{{
                                    formatCurrency(generateCurrentPaymentReceiptPDF?.total_amount)
                                    }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Customer Information -->
                    <div class="info-section">
                        <h3 class="section-title">Customer Details</h3>
                        <div class="info-list">
                            <div class="info-row">
                                <span class="label">Company Name</span>
                                <span class="value">{{ generateCurrentPaymentReceiptPDF?.company_name || `Not
                                    provided` }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Contact Person</span>
                                <span class="value">{{ generateCurrentPaymentReceiptPDF?.contact_person || `Not
                                    provided` }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Email Address</span>
                                <span class="value">{{ generateCurrentPaymentReceiptPDF?.email || `Not provided`
                                    }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Phone Number</span>
                                <span class="value">{{ generateCurrentPaymentReceiptPDF?.phone || `Not provided`
                                    }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">GST Number</span>
                                <span class="value">{{ generateCurrentPaymentReceiptPDF?.gst_number || `Not
                                    provided` }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Billing Address</span>
                                <span class="value">{{ generateCurrentPaymentReceiptPDF?.address || `Not provided`
                                    }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="receipt-footer absolute text-nowrap bottom-4 left-[50%] -translate-x-[50%] ">
                    <p>This is a computer-generated receipt and does not require a physical signature.</p>
                    <p>For any queries, please contact us at <strong>info@nyife.chat</strong></p>
                </div>
            </div>
        </div>

        <PaymentSlabModal :isOpen="isModalOpen" :totalAmount="parseFloat(tempProformaData?.total) || 0"
            :mode="slabModalMode" :initialData="slabModalMode === 'update' ? tempProformaData : null"
            @close="closeSlabModal" @submit="handleSlabSubmit" />

        <PaymentReceiptModal :isOpen="isPaymentReceiptModalOpen" :itemData="paymentItemData" @close="closeReceiptModal"
            @generate-receipt="generateReceipt" @share-receipt="shareReceipt" />

        <CustomerAnalyticsDashboard :is-open="showCustomerDashboard" :customer-id="selectedCustomerId"
            @close="closeCustomerDashboard" @error="handleError" />

        <!-- Update Payment Modal -->
        <UpdatePaymentModal :isOpen="isUpdatePaymentModalOpen" :invoice="updatePaymentData"
            @submit="updatePaymentConfirm" @close="closeUpdatePaymentModal" :closeBtn="true" />

        <!-- Update Quotation Modal -->
        <UpdateQuotationModal :isOpen="isUpdateQuotationModalOpen" :item="updateQuotationItem"
            @close="isUpdateQuotationModalOpen = false; updateQuotationItem = null" @success="refresh = !refresh" />

        <DeleteInvoiceModal :isOpen="isDeleteInvoiceModalOpen" :invoice="deleteInvoiceItem"
            :isDeleting="isDeletingInvoice" @close="closeDeleteInvoiceModal" @confirm="confirmDeleteInvoice" />

    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import debounce from 'lodash/debounce';
import { FileText, FileCheck, Receipt, Plus, RefreshCcw, Eye, IndianRupee, Filter, RefreshCw, IndianRupeeIcon, Loader2, AlertCircle, CheckCircle, ExternalLink, PenLine, FilePlus2, Trash2 } from 'lucide-vue-next';
import QuotationInvoiceGenerator from '../QuotationInvoiceGenerator.vue';
import { toast } from 'vue3-toastify';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
import axios from '../../../js/axios';
import QRCode from "qrcode";
import UpdatePaymentModal from '../UpdatePaymentModal.vue';
import InvoiceAnalyticsDashboard from '../InvoiceAnalyticsDashboard.vue';
import { usePage } from '@inertiajs/vue3';
import PaymentSlabModal from '../PaymentSlabModal.vue';
import PaymentReceiptModal from '../PaymentReceiptModal.vue';
import CustomerAnalyticsDashboard from '../CustomerAnalyticsDashboard.vue';
import UpdateQuotationModal from '../UpdateQuotationModal.vue';
import DeleteInvoiceModal from '../DeleteInvoiceModal.vue';

const base_url = import.meta.env.VITE_BACKEND_API_URL;
const whatsapp_token = import.meta.env.VITE_WA_TOKEN;
const phpApiBaseUrl = import.meta.env.VITE_PHP_API_URL;

const page = usePage();
const isAdmin = page?.props?.auth?.user?.role === 'admin';
const showStatsFilters = ref(false);
const openAnalytics = ref(false);


const statsFilters = ref({
    startDate: '',
    endDate: '',
    paymentStatus: '',
    platformChargeType: '',
    page: 1,
    limit: 10,
    search: '',
    createdById: ''
});

const teamMembers = ref([]);

const buildQueryParams = (additionalParams = {}) => {
    const params = { ...statsFilters.value, ...additionalParams, ...(!isAdmin ? { createdById: page?.props?.auth?.user?.id } : {}) };
    const filtered = Object.entries(params).filter(([_, v]) => v !== '');
    return new URLSearchParams(filtered).toString();
};


const invoiceData = ref([]);
const loading = ref(false);

const isSharing = ref({
    whatsapp: false,
    freeWhatsapp: false,
    email: false,
    downloading: false
});

const refresh = ref(false);

const pdfTemplate = ref(null);
const pdfTemplatePaymentReceipt = ref(null);
const isSearching = ref(false);
const showShareOptions = ref(false);
const openDropdownId = ref(null);
const currentPDF = ref(null);
const generateCurrentProformaPDF = ref(null);
const generateCurrentPaymentReceiptPDF = ref(null);

// payment terms modal
const isModalOpen = ref(false);
const tempProformaData = ref(null);
const slabModalMode = ref('generate');

// payment receipt modal
const isPaymentReceiptModalOpen = ref(false);
const paymentItemData = ref(null);


// update payment modal
const isUpdatePaymentModalOpen = ref(false);
const updatePaymentData = ref(null);

// update quotation modal
const isUpdateQuotationModalOpen = ref(false);
const updateQuotationItem = ref(null);

// delete invoice modal
const isDeleteInvoiceModalOpen = ref(false);
const deleteInvoiceItem = ref(null);
const isDeletingInvoice = ref(false);

function openSlabModal(item, mode = 'generate') {
    isModalOpen.value = true;
    slabModalMode.value = mode;
    tempProformaData.value = item;
}

function closeSlabModal() {
    isModalOpen.value = false;
    tempProformaData.value = null;
    slabModalMode.value = 'generate';
}

function openReceiptModal(item) {
    isPaymentReceiptModalOpen.value = true;
    paymentItemData.value = item;
}

function closeReceiptModal() {
    isPaymentReceiptModalOpen.value = false;
    paymentItemData.value = null;
}

async function handleSlabSubmit(data) {
    if (!tempProformaData?.value) return;

    if (slabModalMode.value === 'update') {
        await updateProforma(tempProformaData.value, data);
        return;
    }

    await generateProforma(tempProformaData.value, data);
}

// State variables
const showCustomerDashboard = ref(false);
const selectedCustomerId = ref(null);

// Method to open dashboard for a specific customer
const openCustomerDashboard = (customerId) => {
    selectedCustomerId.value = customerId;
    showCustomerDashboard.value = true;
};

// Method to close dashboard
const closeCustomerDashboard = () => {
    showCustomerDashboard.value = false;
    selectedCustomerId.value = null;
};

// Error handler
const handleError = (error) => {
    console.error('Dashboard Error:', error);
    // You can add toast notification or alert here
    alert(`Error: ${error.type} - ${error.error.message || 'Something went wrong'}`);
};

const actionConfig = {
    shareQuotation: {
        key: 'shareQuotation',
        label: 'Share Quotation',
        icon: FileText,
        colorClass: 'text-blue-600',
        condition: () => true,
    },
    updateQuotation: {
        key: 'updateQuotation',
        label: 'Update Quotation',
        icon: PenLine,
        colorClass: 'text-indigo-600',
        condition: (item) => !item.payment_receipt && item.payment_status === "unpaid",
    },
    generateProforma: {
        key: 'generateProforma',
        label: 'Generate & Share Proforma',
        icon: Plus,
        colorClass: 'text-green-600',
        condition: (item) => !item.proforma_invoice,
    },
    shareProforma: {
        key: 'shareProforma',
        label: 'Share Proforma',
        icon: FileCheck,
        colorClass: 'text-purple-600',
        condition: (item) => item.proforma_invoice,
    },
    updateProforma: {
        key: 'updateProforma',
        label: 'Update Proforma',
        icon: FilePlus2,
        colorClass: 'text-teal-600',
        condition: (item) => item.proforma_invoice && !item.payment_receipt && item.payment_status === "unpaid",
    },
    updatePayment: {
        key: 'updatePayment',
        label: 'Update Payment manually',
        icon: IndianRupee,
        colorClass: 'text-green-600',
        condition: (item) => item.proforma_invoice && !item.payment_receipt,
    },
    generateReceipt: {
        key: 'generateReceipt',
        label: 'Share Payment Receipts',
        icon: Receipt,
        colorClass: 'text-orange-600',
        condition: (item) => item.payment_status === "paid" || item.payment_status === "partial",
    },
    deleteInvoice: {
        key: 'deleteInvoice',
        label: 'Delete Invoice',
        icon: Trash2,
        colorClass: 'text-red-600',
        condition: () => true,
    },
};

const openShareModal = () => {
    showShareOptions.value = true;
};

const closeShareModal = () => {
    showShareOptions.value = false;
    currentPDF.value = null;
    generateCurrentProformaPDF.value = null;
    generateCurrentPaymentReceiptPDF.value = null;
};

// Dropdown methods
const toggleDropdown = (id) => {
    openDropdownId.value = openDropdownId.value === id ? null : id;
};

const closeDropdown = () => {
    openDropdownId.value = null;
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    const dropdowns = document.querySelectorAll('.relative.inline-block');
    let clickedInside = false;

    dropdowns.forEach(dropdown => {
        if (dropdown.contains(event.target)) {
            clickedInside = true;
        }
    });

    if (!clickedInside) {
        closeDropdown();
    }
};

async function fetchInvoices() {
    const params = buildQueryParams();
    loading.value = true;
    try {
        const res = await axios.get(`${base_url}/invoices?${params}`);

        invoiceData.value = res.data?.data || {};
    } catch (err) {
        toast.error(err.message || 'Error fetching invoices');
    } finally {
        loading.value = false;
        isSearching.value = false;
    }
}

const fetchTeam = async () => {
    try {

        const response = await fetch(`${phpApiBaseUrl}/api/admin/team`);
        const result = await response.json();
        if (response.ok) {
            teamMembers.value = result.data || [];
            return;
        }


        throw new Error(result.message || 'Failed to fetch team');

    } catch (err) {
        console.error(err.message || 'Error fetching team');
    }
}

// initial load
onMounted(() => {
    fetchInvoices();
    fetchTeam();
});

watch(refresh, () => {
    fetchInvoices();
});

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});

// Get available actions based on item state
const getAvailableActions = (item) => {
    return Object.values(actionConfig).filter(action => action.condition(item));
};

// Handle action clicks
const handleAction = (actionKey, item) => {
    switch (actionKey) {
        case 'shareQuotation':
            shareQuotation(item);
            break;
        case 'updateQuotation':
            updateQuotationItem.value = item;
            isUpdateQuotationModalOpen.value = true;
            break;
        case 'generateProforma':
            openSlabModal(item, 'generate');
            break;
        case 'shareProforma':
            shareProforma(item);
            break;
        case 'updateProforma':
            openSlabModal(item, 'update');
            break;
        case 'updatePayment':
            updatePayment(item);
            break;
        case 'generateReceipt':
            openReceiptModal(item);
            break;
        case 'deleteInvoice':
            openDeleteInvoiceModal(item);
            break;
    }
};

const formatCurrency = (value) => {
    const num = parseFloat(value) || 0;
    return num.toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

const getVisibleItems = (Data, additionalData) => {
    const items = [];

    if (parseFloat(Data?.platform_charge) > 0) {

        // input values
        const unit_price = Number(Data?.platform_charge ?? 0);
        const discount = Number(Data?.discount ?? 0); // %
        const gst_rate = Number(Data?.GST ?? 0);       // %

        // calculations
        const discount_amount = Math.round(unit_price * discount) / 100;

        const discounted_price = unit_price - discount_amount;

        const gst_amount = Math.round(discounted_price * gst_rate) / 100;

        const total_amount = Math.round((discounted_price + gst_amount) * 100) / 100;


        items.push({
            description: `Platform Charge (${Data?.platform_charge_type})`,
            quantity: 1,
            unit_price: unit_price,
            discount: discount,
            amount: total_amount,
            hsn_sac: Data?.service_charge_hsn_sac || '998314',
            gst_rate: gst_rate,
            discount_amount: discount_amount,
            gst_amount: gst_amount,
        });
    }

    if (parseFloat(Data?.wallet_recharge) > 0) {

        // input values
        const unit_price = Number(Data?.wallet_recharge ?? 0);
        const discount = Number(Data?.discount ?? 0); // %
        const gst_rate = Number(Data?.GST ?? 0);       // %

        // calculations
        const discount_amount = Math.round(unit_price * discount) / 100;

        const discounted_price = unit_price - discount_amount;

        const gst_amount = Math.round(discounted_price * gst_rate) / 100;

        const total_amount = Math.round((discounted_price + gst_amount) * 100) / 100;

        items.push({
            description: 'Wallet Recharge',
            quantity: 1,
            unit_price: unit_price,
            discount: discount,
            amount: total_amount,
            hsn_sac: Data?.service_charge_hsn_sac || '998314',
            gst_rate: gst_rate,
            discount_amount: discount_amount,
            gst_amount: gst_amount,
        });
    }

    if (parseFloat(Data?.setup_fee) > 0) {

        // input values
        const unit_price = Number(Data?.setup_fee ?? 0);
        const discount = Number(Data?.discount ?? 0); // %
        const gst_rate = Number(Data?.GST ?? 0);       // %

        // calculations
        const discount_amount = Math.round(unit_price * discount) / 100;

        const discounted_price = unit_price - discount_amount;

        const gst_amount = Math.round(discounted_price * gst_rate) / 100;

        const total_amount = Math.round((discounted_price + gst_amount) * 100) / 100;

        items.push({
            description: 'Setup Fee',
            quantity: 1,
            unit_price: unit_price,
            discount: discount,
            amount: total_amount,
            hsn_sac: Data?.service_charge_hsn_sac || '998314',
            gst_rate: gst_rate,
            discount_amount: discount_amount,
            gst_amount: gst_amount,
        });
    }

    if (parseFloat(Data?.customization_fee) > 0) {

        // input values
        const unit_price = Number(Data?.customization_fee ?? 0);
        const discount = Number(Data?.discount ?? 0); // %
        const gst_rate = Number(Data?.GST ?? 0);       // %

        // calculations
        const discount_amount = Math.round(unit_price * discount) / 100;

        const discounted_price = unit_price - discount_amount;

        const gst_amount = Math.round(discounted_price * gst_rate) / 100;

        const total_amount = Math.round((discounted_price + gst_amount) * 100) / 100;

        items.push({
            description: 'Customization Fee',
            quantity: 1,
            unit_price: unit_price,
            discount: discount,
            amount: total_amount,
            hsn_sac: Data?.service_charge_hsn_sac || '998314',
            gst_rate: gst_rate,
            discount_amount: discount_amount,
            gst_amount: gst_amount,
        });
    }

    additionalData?.forEach(item => {
        if (item?.description && parseFloat(item?.amount) > 0) {
            items.push({
                description: item?.description,
                quantity: item?.quantity || 1,
                unit_price: parseFloat(item?.unit_price) || 0,
                discount: parseFloat(item?.discount) || 0,
                hsn_sac: item?.hsn_sac || '',
                gst_rate: item?.gst_rate || 0,
                base_amount: parseFloat(item?.base_amount),
                discount_amount: parseFloat(item?.discount_amount),
                gst_amount: parseFloat(item?.gst_amount),
                amount: parseFloat(item?.amount),
            });
        }
    });

    return items;
};

// const getVisibleItems = (Data, additionalData) => {
//     const items = [];

//     if (parseFloat(Data?.platform_charge) > 0) {
//         items.push({
//             description: `Platform Charge (${Data?.platform_charge_type})`,
//             amount: parseFloat(Data?.platform_charge)
//         });
//     }

//     if (parseFloat(Data?.wallet_recharge) > 0) {
//         items.push({
//             description: 'Wallet Recharge',
//             amount: parseFloat(Data?.wallet_recharge)
//         });
//     }

//     if (parseFloat(Data?.setup_fee) > 0) {
//         items.push({
//             description: 'Setup Fee',
//             amount: parseFloat(Data?.setup_fee)
//         });
//     }

//     if (parseFloat(Data?.customization_fee) > 0) {
//         items.push({
//             description: 'Customization Fee',
//             amount: parseFloat(Data?.customization_fee)
//         });
//     }

//     additionalData?.forEach(item => {
//         if (item?.description && parseFloat(item?.amount) > 0) {
//             items?.push({
//                 description: item?.description,
//                 amount: parseFloat(item?.amount)
//             });
//         }
//     });

//     return items;
// };


const generatePDFBlob = async () => {
    // Constants for PDF generation
    const PDF_CONFIG = {
        A4_WIDTH_MM: 210,
        A4_HEIGHT_MM: 297,
        A4_WIDTH_PX: 793.7, // 210mm at 96 DPI
        PADDING_MM: 10,
        CANVAS_SCALE: 4, // Reduced from 5 for better performance, still high quality
        IMAGE_QUALITY: 0.95, // Slightly reduced for smaller file size
        RENDER_DELAY_MS: 200
    };

    try {
        // Wait for any pending DOM updates
        await new Promise(resolve => setTimeout(resolve, PDF_CONFIG.RENDER_DELAY_MS));

        const element = pdfTemplate.value;
        if (!element) {
            throw new Error('PDF template element not found');
        }

        // Get the two content sections with validation
        const firstPageContent = element.querySelector('.pdf-content > div:first-child');
        const secondPageContent = element.querySelector('.pdf-content > div:last-child');

        if (!firstPageContent || !secondPageContent) {
            throw new Error('Required content sections not found in PDF template');
        }

        // Validate proforma data
        if (!generateCurrentProformaPDF?.value) {
            throw new Error('Proforma data is not available');
        }

        // Create optimized page element factory
        const createPageElement = (content) => {
            const pageDiv = document.createElement('div');

            // Apply styles in batch for performance
            Object.assign(pageDiv.style, {
                width: `${PDF_CONFIG.A4_WIDTH_MM}mm`,
                minHeight: `${PDF_CONFIG.A4_HEIGHT_MM}mm`,
                padding: `${PDF_CONFIG.PADDING_MM}mm`,
                backgroundColor: '#ffffff',
                boxSizing: 'border-box',
                position: 'relative',
                fontSmoothing: 'antialiased',
                WebkitFontSmoothing: 'antialiased'
            });

            // Deep clone to avoid reference issues
            pageDiv.appendChild(content.cloneNode(true));
            return pageDiv;
        };

        // Create temporary container with optimized positioning
        const tempContainer = document.createElement('div');
        Object.assign(tempContainer.style, {
            position: 'fixed',
            left: '0',
            top: '0',
            zIndex: '-1000',
            opacity: '0',
            pointerEvents: 'none'
        });
        document.body.appendChild(tempContainer);

        // Shared html2canvas configuration
        const canvasConfig = {
            scale: PDF_CONFIG.CANVAS_SCALE,
            useCORS: true,
            allowTaint: false,
            logging: false,
            backgroundColor: '#ffffff',
            width: PDF_CONFIG.A4_WIDTH_PX,
            windowWidth: PDF_CONFIG.A4_WIDTH_PX,
            imageTimeout: 15000,
            removeContainer: false
        };

        // Render first page
        const firstPage = createPageElement(firstPageContent);
        tempContainer.appendChild(firstPage);

        const canvas1 = await html2canvas(firstPage, canvasConfig);

        // Render second page (reuse container for memory efficiency)
        tempContainer.innerHTML = '';
        const secondPage = createPageElement(secondPageContent);
        tempContainer.appendChild(secondPage);

        const canvas2 = await html2canvas(secondPage, canvasConfig);

        // Clean up DOM
        document.body.removeChild(tempContainer);

        // Initialize PDF with compression
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4',
            compress: true,
            hotfixes: ['px_scaling']
        });

        // Helper function to add image to PDF with proper scaling
        const addImageToPDF = (canvas, pageIndex = 0) => {
            const imgData = canvas.toDataURL('image/jpeg', PDF_CONFIG.IMAGE_QUALITY);
            const imgWidth = PDF_CONFIG.A4_WIDTH_MM;
            const imgHeight = (canvas.height * PDF_CONFIG.A4_WIDTH_MM) / canvas.width;

            // Ensure image doesn't exceed page height
            const finalHeight = Math.min(imgHeight, PDF_CONFIG.A4_HEIGHT_MM);

            if (pageIndex > 0) {
                pdf.addPage();
            }

            pdf.addImage(imgData, 'JPEG', 0, 0, imgWidth, finalHeight, undefined, 'FAST');
        };

        // Add pages to PDF
        addImageToPDF(canvas1, 0);
        addImageToPDF(canvas2, 1);

        // Generate sanitized filename
        const sanitizeFilename = (str) => {
            return str ? str.replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 100) : 'Unknown';
        };

        const proformaNumber = sanitizeFilename(generateCurrentProformaPDF?.value?.proforma_number || 'N/A');
        const companyName = sanitizeFilename(generateCurrentProformaPDF?.value?.company_name || 'Company');

        const fileName = `Proforma_${proformaNumber}_${companyName}.pdf`;

        // Generate output
        const pdfBlob = pdf.output('blob');

        // Save PDF with the sanitized filename
        // pdf.save(fileName);

        // Return blob and metadata
        return {
            blob: pdfBlob,
            pdf,
            fileName,
            size: pdfBlob.size
        };

    } catch (error) {
        throw new Error(`Failed to generate PDF: ${error.message}`);
    }
};
const generatePaymentReceiptPDFBlob = async () => {
    // Constants for PDF generation
    const PDF_CONFIG = {
        A4_WIDTH_MM: 210,
        A4_HEIGHT_MM: 297,
        A4_WIDTH_PX: 793.7, // 210mm at 96 DPI
        PADDING_MM: 10,
        CANVAS_SCALE: 4, // Reduced from 5 for better performance, still high quality
        IMAGE_QUALITY: 0.95, // Slightly reduced for smaller file size
        RENDER_DELAY_MS: 200
    };

    try {
        // Wait for any pending DOM updates
        await new Promise(resolve => setTimeout(resolve, PDF_CONFIG.RENDER_DELAY_MS));

        const element = pdfTemplatePaymentReceipt.value;
        if (!element) {
            throw new Error('PDF template element not found');
        }

        // Get the two content sections with validation
        const firstPageContent = element.querySelector('.payment-receipt-content > div:first-child');
        const secondPageContent = element.querySelector('.payment-receipt-content > div:last-child');

        if (!firstPageContent || !secondPageContent) {
            throw new Error('Required content sections not found in PDF template');
        }

        // Validate proforma data
        if (!generateCurrentPaymentReceiptPDF?.value) {
            throw new Error('Proforma data is not available');
        }

        // Create optimized page element factory
        const createPageElement = (content) => {
            const pageDiv = document.createElement('div');

            // Apply styles in batch for performance
            Object.assign(pageDiv.style, {
                width: `${PDF_CONFIG.A4_WIDTH_MM}mm`,
                minHeight: `${PDF_CONFIG.A4_HEIGHT_MM}mm`,
                padding: `${PDF_CONFIG.PADDING_MM}mm`,
                backgroundColor: '#ffffff',
                boxSizing: 'border-box',
                position: 'relative',
                fontSmoothing: 'antialiased',
                WebkitFontSmoothing: 'antialiased'
            });

            // Deep clone to avoid reference issues
            pageDiv.appendChild(content.cloneNode(true));
            return pageDiv;
        };

        // Create temporary container with optimized positioning
        const tempContainer = document.createElement('div');
        Object.assign(tempContainer.style, {
            position: 'fixed',
            left: '0',
            top: '0',
            zIndex: '-1000',
            opacity: '0',
            pointerEvents: 'none'
        });
        document.body.appendChild(tempContainer);

        // Shared html2canvas configuration
        const canvasConfig = {
            scale: PDF_CONFIG.CANVAS_SCALE,
            useCORS: true,
            allowTaint: false,
            logging: false,
            backgroundColor: '#ffffff',
            width: PDF_CONFIG.A4_WIDTH_PX,
            windowWidth: PDF_CONFIG.A4_WIDTH_PX,
            imageTimeout: 15000,
            removeContainer: false
        };

        // Render first page
        const firstPage = createPageElement(firstPageContent);
        tempContainer.appendChild(firstPage);

        const canvas1 = await html2canvas(firstPage, canvasConfig);

        // Render second page (reuse container for memory efficiency)
        tempContainer.innerHTML = '';
        const secondPage = createPageElement(secondPageContent);
        tempContainer.appendChild(secondPage);

        const canvas2 = await html2canvas(secondPage, canvasConfig);

        // Clean up DOM
        document.body.removeChild(tempContainer);

        // Initialize PDF with compression
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4',
            compress: true,
            hotfixes: ['px_scaling']
        });

        // Helper function to add image to PDF with proper scaling
        const addImageToPDF = (canvas, pageIndex = 0) => {
            const imgData = canvas.toDataURL('image/jpeg', PDF_CONFIG.IMAGE_QUALITY);
            const imgWidth = PDF_CONFIG.A4_WIDTH_MM;
            const imgHeight = (canvas.height * PDF_CONFIG.A4_WIDTH_MM) / canvas.width;

            // Ensure image doesn't exceed page height
            const finalHeight = Math.min(imgHeight, PDF_CONFIG.A4_HEIGHT_MM);

            if (pageIndex > 0) {
                pdf.addPage();
            }

            pdf.addImage(imgData, 'JPEG', 0, 0, imgWidth, finalHeight, undefined, 'FAST');
        };

        // Add pages to PDF
        addImageToPDF(canvas1, 0);
        addImageToPDF(canvas2, 1);

        // Generate sanitized filename
        const sanitizeFilename = (str) => {
            return str ? str.replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 100) : 'Unknown';
        };

        const payment_id = sanitizeFilename(generateCurrentPaymentReceiptPDF?.value?.payment_id || 'N/A');
        const companyName = sanitizeFilename(generateCurrentPaymentReceiptPDF?.value?.company_name || 'Company');

        const fileName = `PaymentReceipt_${payment_id}_${companyName}.pdf`;

        // Generate output
        const pdfBlob = pdf.output('blob');

        // Save PDF with the sanitized filename
        // pdf.save(fileName);

        // Return blob and metadata
        return {
            blob: pdfBlob,
            pdf,
            fileName,
            size: pdfBlob.size
        };

    } catch (error) {
        throw new Error(`Failed to generate PDF: ${error.message}`);
    }
};

// const generatePaymentReceiptPDFBlob = async () => {


//     await new Promise(resolve => setTimeout(resolve, 200));

//     const element = pdfTemplatePaymentReceipt.value;

//     // Show element temporarily for rendering
//     element.style.position = 'fixed';
//     element.style.left = '0';
//     element.style.top = '0';
//     element.style.zIndex = '-1';
//     element.style.display = 'block';
//     element.style.width = '210mm';
//     element.style.minHeight = '297mm';

//     // High-quality canvas rendering
//     const canvas = await html2canvas(element, {
//         scale: 4,
//         useCORS: true,
//         logging: false,
//         backgroundColor: '#ffffff',
//         width: 793.7,
//         windowWidth: 793.7,
//         onclone: (clonedDoc) => {
//             const clonedElement = clonedDoc.querySelector('.pdf-template-payment-receipt');
//             if (clonedElement) {
//                 clonedElement.style.display = 'block';
//                 clonedElement.style.width = '210mm';
//             }
//         }
//     });

//     // Hide element after rendering
//     element.style.display = 'none';
//     element.style.position = 'absolute';
//     element.style.left = '-9999px';
//     element.style.zIndex = '';
//     element.style.width = '';
//     element.style.minHeight = '';

//     // Create PDF with high quality
//     const imgData = canvas.toDataURL('image/png', 1.0);

//     const pdf = new jsPDF({
//         orientation: 'portrait',
//         unit: 'mm',
//         format: 'a4',
//         compress: true
//     });

//     const pdfWidth = 210;
//     const pdfHeight = 297;
//     const imgWidth = pdfWidth;
//     const imgHeight = (canvas.height * pdfWidth) / canvas.width;

//     let heightLeft = imgHeight;
//     let position = 0;

//     // Add first page
//     pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight, undefined, 'FAST');
//     heightLeft -= pdfHeight;

//     // Add additional pages if content overflows
//     // while (heightLeft > 0) {
//     //     position = heightLeft - imgHeight;
//     //     pdf.addPage();
//     //     pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight, undefined, 'FAST');
//     //     heightLeft -= pdfHeight;
//     // }

//     // Generate filename
//     const fileName = `PaymentReceipt_${generateCurrentPaymentReceiptPDF?.value?.payment_id?.replace('/', '_')}_${generateCurrentPaymentReceiptPDF?.value?.company_name?.replace(/[^a-zA-Z0-9]/g, '_')}.pdf`;

//     // Convert PDF to Blob
//     const pdfBlob = pdf.output('blob');
//     return { blob: pdfBlob, pdf, fileName };
// };

const downloadPDF = async () => {
    if (!currentPDF.value?.pdfDownloadUrl) {
        toast.error('No PDF available to download');
        return;
    }

    isSharing.value.downloading = true;

    try {
        // Fetch the PDF as a blob
        const response = await axios.get(currentPDF.value.pdfDownloadUrl, {
            responseType: "blob", // important
        });

        const blob = response.data;

        // Create a blob URL
        const blobUrl = window.URL.createObjectURL(blob);

        // Create and trigger download
        const link = document.createElement('a');
        link.href = blobUrl;
        link.download = currentPDF.value.pdfName || "invoice.pdf";
        document.body.appendChild(link);
        link.click();

        // Cleanup
        document.body.removeChild(link);
        window.URL.revokeObjectURL(blobUrl);

        toast.success('PDF downloaded successfully!');

    } catch (error) {
        console.error('Download error:', error);
        toast.error('Error downloading PDF. Please try again.');
    } finally {
        isSharing.value.downloading = false;
    }
};

const shareOnFreeWhatsApp = async () => {


    if (!currentPDF.value.pdf) {
        toast.error('No PDF available to share');
        return;
    }

    if (!currentPDF.value.phone) {
        toast.error('Phone number is required');
        return;
    }

    isSharing.value.freeWhatsapp = true;

    try {
        const response = await fetch("https://wa.nyife.chat/api/send/media", {
            method: "POST",
            headers: {
                "Authorization": `Bearer ${whatsapp_token}`,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                phone: currentPDF.value.phone,
                media_type: "document",
                media_url: currentPDF.value.pdf,
                file_name: currentPDF.value.pdfName,
                caption: `Dear ${currentPDF?.value?.contactPerson || 'Sir'},
                
Thank you for your interest! Please download your ${currentPDF.value.templateType?.toLowerCase()} invoice.
                
If you have any questions, feel free to reply here.
                
Looking forward to assisting you.`,

            })
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => null);
            console.error("WhatsApp API Error:", errorData);
            throw new Error("Failed to share on WhatsApp");
        }

        toast.success("Invoice shared on WhatsApp!");


    } catch (error) {
        console.error("Error sharing on WhatsApp:", error);
        toast.error("Error sharing on WhatsApp. Please try again.");
    } finally {
        isSharing.value.freeWhatsapp = false;
    }

    // ========================== SECOND METHOD ========================


    //     const message = `
    // Hi Sir,

    // Thank you for your interest! Please download your ${currentPDF.value.templateType?.toLowerCase()} invoice by clicking on the link below:

    // If you have any questions or need any changes, feel free to reply here.

    // Looking forward to assisting you.

    // Url : ${currentPDF.value.pdfDownloadUrl}
    //     `.trim();

    //     const encodedMessage = encodeURIComponent(message);

    //     // remove spaces & non-digits, keep country code
    //     const phone = currentPDF.value.phone.replace(/\D/g, '');

    //     const whatsappUrl = `https://wa.me/${phone}?text=${encodedMessage}`;

    //     window.open(whatsappUrl, '_blank');

};

const shareOnWhatsApp = async () => {
    if (!currentPDF.value.pdf) {
        toast.error('No PDF available to share');
        return;
    }

    if (!currentPDF.value.phone) {
        toast.error('Phone number is required');
        return;
    }

    isSharing.value.whatsapp = true;

    try {
        const response = await fetch("https://wa.nyife.chat/api/send/template", {
            method: "POST",
            headers: {
                "Authorization": `Bearer ${whatsapp_token}`,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                phone: currentPDF.value.phone,
                template: {
                    name: currentPDF.value.templateName,
                    language: { code: "en" },
                    components: [
                        {
                            type: "header",
                            parameters: [
                                {
                                    type: "document",
                                    document: {
                                        link: currentPDF.value.pdf,
                                        filename: currentPDF.value.pdfName || "invoice.pdf"
                                    }
                                }
                            ]
                        }
                    ]
                }
            })
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => null);
            console.error("WhatsApp API Error:", errorData);
            throw new Error("Failed to share on WhatsApp");
        }

        toast.success("Invoice shared on WhatsApp!");



    } catch (error) {
        console.error("Error sharing on WhatsApp:", error);
        toast.error("Error sharing on WhatsApp. Please try again.");
    } finally {
        isSharing.value.whatsapp = false;
    }
};


const shareViaEmail = async () => {
    if (!currentPDF.value.pdf) {
        toast.error('No PDF available to share');
        return;
    }

    if (!currentPDF.value.email) {
        toast.error('Email address is required');
        return;
    }

    isSharing.value.email = true;

    try {

        const payload = {
            customer_name: currentPDF.value.contactPerson,
            invoice_type: currentPDF.value.templateType,
            invoice_number: currentPDF.value.invoiceNumber,
            invoice_url: currentPDF.value.pdfDownloadUrl,
            email: currentPDF.value.email,
            payment_url: currentPDF.value.payment_url
        }

        const response = await axios.post(`${base_url}/email/share-invoice`, payload);

        if (!response?.data?.success) {
            throw new Error('Failed to send email');
        }

        toast.success(`Quotation sent to ${currentPDF.value.email} successfully!`);

    } catch (error) {
        toast.error('Error sending email. Please try again.');
    } finally {
        isSharing.value.email = false;
    }
};

// Action handlers
const shareQuotation = (item) => {
    currentPDF.value = {
        contactPerson: item.contact_person,
        companyName: item.company_name,
        phone: item.phone,
        email: item.email,
        pdf: item.quotation_invoice_pdf_url,
        invoiceNumber: item.quotation_number,
        pdfDownloadUrl: `${item.quotation_invoice_pdf_url}/download`,
        templateName: "quotation_invoice",
        templateType: "Quotation",
        pdfName: `Quotation_${item.quotation_number.replace('/', '_')}_${item.company_name.replace(/[^a-zA-Z0-9]/g, '_')}.pdf`
    };
    openShareModal();
};

const submitProforma = async (item, data, options = {}) => {
    const {
        endpoint,
        loadingText = 'Processing proforma invoice...',
        successText = 'Proforma invoice processed successfully!',
    } = options;

    const tId = toast.loading(loadingText);
    try {

        const termsConditions = data?.termsConditions?.terms || [];

        const payload = {

            proforma_valid_until_date: data.paymentTerms.slabs.at(-1).validity,
            payment_terms: data.paymentTerms,
            bank_info: data.bankInfo
        }

        const res = await axios.put(endpoint, payload);

        if (!res?.data?.success) {
            throw new Error(res?.data?.message || 'Failed to generate proforma invoice');
        }

        const qrCode = ref('');

        const paymentUrl = res?.data?.data.payment_url?.payment_link_short_url;

        qrCode.value = await QRCode.toDataURL(paymentUrl, { width: 180 });

        generateCurrentProformaPDF.value = {
            ...res?.data?.data,
            qrCode: qrCode.value,
            termsConditions
        };

        const { blob, fileName } = await generatePDFBlob();

        const formData = new FormData();
        formData.append("pdf_data", blob, fileName);
        formData.append("pdf_type", "proforma");
        formData.append("id", res?.data?.data.id);

        const uploadRes = await axios.post(`${base_url}/uploads`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        });

        if (!uploadRes?.data?.success) {
            throw new Error(uploadRes?.data?.message || "Failed to upload proforma PDF");
        }

        closeSlabModal();
        toast.success(successText);

        currentPDF.value = {
            contactPerson: generateCurrentProformaPDF?.value?.contact_person,
            companyName: generateCurrentProformaPDF?.value?.company_name,
            phone: generateCurrentProformaPDF?.value?.phone,
            email: generateCurrentProformaPDF?.value?.email,
            invoiceNumber: generateCurrentProformaPDF?.value?.proforma_number,
            templateType: "Proforma",
            templateName: "proforma_invoice",
            payment_url: generateCurrentProformaPDF?.value?.payment_url?.payment_link_short_url,
            pdf: uploadRes?.data?.data?.file?.url,
            pdfDownloadUrl: uploadRes?.data?.data?.file?.downloadUrl,
            pdfName: uploadRes?.data?.data?.file?.filename
        };


        // Show share options modal after a short delay
        setTimeout(() => {
            openShareModal();
        }, 300);

    } catch (error) {
        toast.error(error.message || 'Error processing proforma invoice. Please try again.');

    } finally {
        refresh.value = !refresh.value;
        toast.remove(tId);
    }
};

const generateProforma = async (item, data) => {
    await submitProforma(item, data, {
        endpoint: `${base_url}/invoices/generate-proforma/${item.id}`,
        loadingText: 'Generating proforma invoice...',
        successText: 'Proforma invoice generated successfully!',
    });
};

const updateProforma = async (item, data) => {
    await submitProforma(item, data, {
        endpoint: `${base_url}/invoices/update-proforma/${item.id}`,
        loadingText: 'Updating proforma invoice...',
        successText: 'Proforma invoice updated successfully!',
    });
};

const shareProforma = (item) => {
    currentPDF.value = {
        contactPerson: item.contact_person,
        companyName: item.company_name,
        phone: item.phone,
        email: item.email,
        templateName: "proforma_invoice",
        templateType: "Proforma",
        invoiceNumber: item.proforma_number,
        pdf: item.proforma_invoice_pdf_url,
        payment_url: item.payment_url?.payment_link_short_url,
        pdfDownloadUrl: `${item.proforma_invoice_pdf_url}/download`,
        pdfName: `Proforma_${item.proforma_number.replace('/', '_')}_${item.company_name.replace(/[^a-zA-Z0-9]/g, '_')}.pdf`
    };
    openShareModal();
};

const updatePayment = async (item) => {
    updatePaymentData.value = item;
    isUpdatePaymentModalOpen.value = true;
};

const openDeleteInvoiceModal = (item) => {
    deleteInvoiceItem.value = item;
    isDeleteInvoiceModalOpen.value = true;
};

const closeDeleteInvoiceModal = () => {
    isDeleteInvoiceModalOpen.value = false;
    deleteInvoiceItem.value = null;
};

const confirmDeleteInvoice = async () => {
    if (!deleteInvoiceItem.value?.id || isDeletingInvoice.value) return;

    isDeletingInvoice.value = true;

    try {
        const res = await axios.delete(`${base_url}/invoices/${deleteInvoiceItem.value.id}`);

        if (!res?.data?.success) {
            throw new Error(res?.data?.message || 'Failed to delete invoice');
        }

        toast.success('Invoice deleted successfully!');
        closeDeleteInvoiceModal();
        refresh.value = !refresh.value;
    } catch (error) {
        toast.error(error?.message || 'Error deleting invoice. Please try again.');
    } finally {
        isDeletingInvoice.value = false;
    }
};


const closeUpdatePaymentModal = () => {
    isUpdatePaymentModalOpen.value = false;

    setTimeout(() => {
        updatePaymentData.value = null;
    }, 1000)
};


const updatePaymentConfirm = async (paymentData) => {
    const tId = toast.loading('Updating payment...');

    try {

        const { id, ...payload } = paymentData;

        const res = await axios.put(`${base_url}/invoices/update-payment/${id}`, payload);

        if (!res?.data?.success) {
            throw new Error(res?.data?.message || 'Failed to update payment');
        }

        toast.success('Payment updated successfully!');


    } catch (error) {
        toast.error(error.message || 'Error updating payment. Please try again.');

    } finally {
        refresh.value = !refresh.value;
        toast.remove(tId);
    }
}

const generateReceipt = async (item) => {

    const tId = toast.loading('Generating  payment receipt...');
    try {

        generateCurrentPaymentReceiptPDF.value = item;

        const { blob, fileName } = await generatePaymentReceiptPDFBlob();

        const formData = new FormData();
        formData.append("pdf_data", blob, fileName);
        formData.append("pdf_type", "payment");
        formData.append("id", item.id);
        formData.append("ledger_id", item.ledger_id);

        const uploadRes = await axios.post(`${base_url}/uploads`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        });

        if (!uploadRes?.data?.success) {
            throw new Error(uploadRes?.data?.message || "Failed to upload payment receipt PDF");
        }

        toast.success('Payment receipt generated successfully!');

        currentPDF.value = {
            contactPerson: generateCurrentPaymentReceiptPDF?.value?.contact_person,
            companyName: generateCurrentPaymentReceiptPDF?.value?.company_name,
            phone: generateCurrentPaymentReceiptPDF?.value?.phone,
            email: generateCurrentPaymentReceiptPDF?.value?.email,
            invoiceNumber: generateCurrentPaymentReceiptPDF?.value?.payment_id,
            templateType: "Payment Receipt",
            templateName: "payment_receipt",
            pdf: uploadRes?.data?.data?.file?.url,
            pdfDownloadUrl: uploadRes?.data?.data?.file?.downloadUrl,
            pdfName: uploadRes?.data?.data?.file?.filename
        };

        closeReceiptModal();

        // Show share options modal after a short delay
        setTimeout(() => {
            openShareModal();
        }, 300);

    } catch (error) {
        toast.error(error.message || 'Error generating payment receipt. Please try again.');

    } finally {
        refresh.value = !refresh.value;
        toast.remove(tId);
    }
};

const shareReceipt = (item) => {

    currentPDF.value = {
        contactPerson: item.contact_person,
        companyName: item.company_name,
        phone: item.phone,
        email: item.email,
        invoiceNumber: item.invoiceNumber,
        templateType: item.templateType,
        templateName: item.templateName,
        pdf: item.pdf,
        pdfDownloadUrl: item.pdfDownloadUrl,
        pdfName: item.pdfName
    };

    closeReceiptModal();
    openShareModal();
};

const getStatusText = (item) => {
    switch (item?.payment_status) {
        case 'paid':
            return 'Paid';
        case 'partial':
            return 'Partially Paid';
        case 'expired':
            return 'Expired';
        default:
            return item.proforma_invoice ? 'Proforma Issued' : 'Quotation Issued';
    }
};

const getStatusBadgeClass = (item) => {
    switch (item?.payment_status) {
        case 'paid':
            return 'bg-green-100 text-green-700';
        case 'partial':
            return 'bg-yellow-100 text-yellow-700';
        case 'expired':
            return 'bg-red-100 text-red-700';
        default:
            return item.proforma_invoice ? 'bg-sky-100 text-sky-700' : 'bg-purple-100 text-purple-700';
    }
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




// Utility functions
const clearSearch = () => {
    statsFilters.value.search = '';
    fetchInvoices();
};

const search = debounce(() => {
    isSearching.value = true;
    fetchInvoices();
}, 1000);


const goToFirstPage = () => {
    statsFilters.value.page = 1;
    fetchInvoices();
}

const goToPreviousPage = () => {
    if (invoiceData.value.pagination.currentPage > 1) {
        statsFilters.value.page--;
        fetchInvoices();
    }
}

const goToNextPage = () => {
    if (invoiceData.value.pagination.currentPage < invoiceData.value.pagination.totalPages) {
        statsFilters.value.page++;
        fetchInvoices();
    }
}

const goToLastPage = () => {
    statsFilters.value.page = invoiceData.value.pagination.totalPages;
    fetchInvoices();
}

const resetStatsFilters = () => {

    statsFilters.value = {
        startDate: '',
        endDate: '',
        paymentStatus: '',
        platformChargeType: '',
        page: 1,
        limit: 10,
        search: '',
        createdById: ''
    };

    applyStatsFilters();
};


const applyStatsFilters = () => {
    fetchInvoices();
};

const formatAmount = (amount) => {
    return new Intl.NumberFormat('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
};

function formatDateTimeIST(isoString) {
    if (!isoString) return '';

    return new Intl.DateTimeFormat('en-IN', {
        timeZone: 'Asia/Kolkata',
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    }).format(new Date(isoString));
}


const emit = defineEmits(['update:modelValue', 'callback']);
</script>

<style scoped>
/* Modal Overlay */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 1rem;
}

/* Proforma Invoice Generator Styles */

#pdf-template {
    position: absolute;
    left: -9999px;
    display: none;
    width: 793.7px;
}

.pdf-page {
    background-image: url("../../../images/nyife-bg.png");
    background-size: 70%;
    background-position: center;
    background-repeat: no-repeat;
    height: 297mm;
}

.pdf-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    padding-bottom: 20px;
    border-bottom: 2px solid #333;
}

.company-info h2 {
    color: #ff5100;
    font-size: 24px;
    margin-bottom: 5px;
}

.company-info p {
    font-size: 11px;
    color: #666;
    margin: 2px 0;
}

.invoice-info {
    text-align: right;
    background: linear-gradient(135deg, #ff5100 0%, #ff7d47 100%);
    padding: 16px 20px;
    border-radius: 8px;
    color: white;
    height: fit-content;
    margin-top: auto;
}

.invoice-info h3 {
    font-size: 20px;
    color: white;
    margin-bottom: 8px;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.invoice-info p {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.95);
    margin: 4px 0;
}

/* Payment Terms Schedule Section Styles */
.payment-schedule {
    background: linear-gradient(135deg, #fff5f0 0%, #ffffff 100%);
    border: 2px solid #ff5100;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 25px;
}

.payment-schedule h4 {
    font-size: 14px;
    color: #ff5100;
    margin-bottom: 15px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.payment-schedule-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 15px;
}

.payment-schedule-table thead th {
    background: linear-gradient(135deg, #ff5100 0%, #ff7d47 100%);
    color: white;
    padding: 10px 12px;
    text-align: left;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.payment-schedule-table thead th:last-child,
.payment-schedule-table tbody td:last-child {
    text-align: right;
}

.payment-schedule-table tbody td {
    padding: 10px 12px;
    border-bottom: 1px solid #e0e0e0;
    font-size: 12px;
    color: #333;
}

.payment-schedule-table tbody tr:last-child td {
    border-bottom: none;
}

.payment-schedule-table tbody tr:hover {
    background: #f8f9fa;
}

.installment-number {
    font-weight: 600;
    color: #ff5100;
}

.due-date {
    color: #666;
}

.percentage-badge {
    display: inline-block;
    color: #2c2c2c;
    font-size: 10px;
    font-weight: 600;
}

.amount-value {
    font-weight: 600;
    color: #333;
}

.payment-summary {
    display: flex;
    justify-content: flex-end;
    padding-top: 12px;
    border-top: 2px solid #ff5100;
}

.payment-summary-item {
    display: flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #ff5100 0%, #ff7d47 100%);
    padding: 12px 20px;
    border-radius: 6px;
}

.payment-summary-item .label {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.95);
    font-weight: 600;
}

.payment-summary-item .value {
    font-size: 16px;
    color: white;
    font-weight: 700;
}

.payment-note {
    margin-top: 12px;
    padding: 10px 12px;
    background: #f8f9fa;
    border-left: 3px solid #ff5100;
    border-radius: 4px;
}

.payment-note p {
    font-size: 10px;
    color: #666;
    line-height: 1.5;
    margin: 0;
}

.payment-note strong {
    color: #ff5100;
    font-weight: 600;
}

.client-info {
    background: #f8f9fa;
    padding: 16px;
    margin-bottom: 15px;
    border-radius: 5px;
    border-left: 4px solid #ff5100;
}

.client-info h4 {
    font-size: 13px;
    color: #ff5100;
    margin-bottom: 8px;
    font-weight: 700;
}

.client-info p {
    font-size: 12px;
    color: #555;
    line-height: 1.6;
    margin: 2px 0;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.items-table th {
    background: #ff5100;
    color: white;
    padding: 12px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
}

.items-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #e0e0e0;
    font-size: 12px;
    color: #333;
}

.items-table tr:last-child td {
    border-bottom: none;
}

.summary-section {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
    align-items: flex-start;
}

.payment-qr {
    flex: 0 0 200px;
}

.qr-container {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: 2px solid #ff5100cb;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(255, 81, 0, 0.15);
}

.qr-header h4 {
    font-size: 14px;
    color: #ff5100;
    margin-bottom: 4px;
    font-weight: 700;
}

.qr-header p {
    font-size: 10px;
    color: #666;
    margin-bottom: 12px;
}

.qr-code-wrapper {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.qr-code {
    width: 180px;
    height: 180px;
    display: block;
    margin: 0 auto;
    overflow: hidden;
}

.summary-table-wrapper {
    flex: 1;
}

.summary-table {
    width: 100%;
    border-collapse: collapse;
}

.summary-table tr {
    border-bottom: 1px solid #e0e0e0;
}

.summary-table td {
    padding: 10px 12px;
    font-size: 13px;
}

.summary-table td:first-child {
    color: #666;
}

.summary-table td:last-child {
    text-align: right;
    font-weight: 600;
    color: #333;
}

.summary-table .discount-amount {
    color: #28a745;
}

.summary-table .total-row {
    background: linear-gradient(135deg, #ff5100 0%, #ff7d47 100%);
}

.summary-table .total-row td {
    padding: 14px 12px;
    font-size: 16px;
    border: none;
    color: white;
}

.bank-info {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border: 2px solid #ff5100;
    border-radius: 8px;
    padding: 20px;
    /* margin-bottom: 25px; */
}

.bank-info h4 {
    font-size: 14px;
    color: #ff5100;
    margin-bottom: 15px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.bank-details-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px 20px;
}

.bank-detail-item {
    display: flex;
    align-items: baseline;
}

.bank-detail-item .label {
    font-size: 11px;
    color: #666;
    font-weight: 600;
    min-width: 120px;
    margin-right: 8px;
}

.bank-detail-item .value {
    font-size: 12px;
    color: #333;
    font-weight: 500;
}

.bank-detail-item.full-width {
    grid-column: 1 / -1;
}

.bank-note {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #e0e0e0;
}

.bank-note p {
    font-size: 10px;
    color: #666;
    font-style: italic;
    line-height: 1.5;
}

.terms {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid #333;
}

.terms h4 {
    font-size: 14px;
    color: #333;
    margin-bottom: 12px;
}

.terms-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.term-section h5 {
    font-size: 11px;
    color: #ff5100;
    margin-bottom: 5px;
    font-weight: 700;
}

.term-section p {
    font-size: 10px;
    color: #555;
    line-height: 1.5;
    margin-bottom: 8px;
}

.signature {
    margin: 100px 0 60px 0;
    text-align: right;
}

.signature p {
    font-size: 12px;
    color: #333;
    margin: 5px 0;
}

.signature .signature-line {
    margin-top: 30px;
    margin-bottom: 5px;
}

.signature .name {
    font-weight: 700;
    font-size: 14px;
    color: #ff5100;
}

.signature .title {
    font-size: 11px;
    color: #666;
}

/* Payment Receipt Generator Styles */

#pdf-template-payment-receipt {
    position: absolute;
    left: -9999px;
    display: none;
    width: 793.7px;
}


/* Proforma Reference */
.proforma-reference {
    background: #fff5f0;
    border-left: 4px solid #ff5100;
    padding: 12px 16px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.proforma-reference strong {
    color: #ff5100;
    font-weight: 600;
    font-size: 11px;
}

.proforma-reference {
    font-size: 11px;
    color: #212529;
}

/* Content Grid */
.content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.info-section {
    background: white;
    padding: 10px;
    border-radius: 4px;
    height: fit-content;
}

.section-title {
    font-size: 13px;
    font-weight: 700;
    color: #212529;
    margin: 0 0 12px 0;
    padding-bottom: 8px;
    border-bottom: 2px solid #ff5100;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    /* margin-bottom: 20px; */
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
    gap: 12px;
}

.info-row:last-child {
    border-bottom: none;
}

.label {
    font-size: 11px;
    color: #6c757d;
    font-weight: 500;
    flex-shrink: 0;
}

.value {
    font-size: 11px;
    color: #212529;
    font-weight: 600;
    text-align: right;
    word-break: break-word;
}

.total-row {
    border-top: 2px solid #ff5100;
    border-bottom: 2px solid #ff5100;
    padding: 10px 0;
    margin-top: 8px;
    background: #fff5f0;
    padding-left: 8px;
    padding-right: 8px;
}

.total-row .label,
.total-row .value {
    font-size: 13px;
    font-weight: 700;
    color: #ff5100;
}

/* Footer */
.receipt-footer {
    background: #f8f9fa;
    padding: 16px 30px;
    text-align: center;
    border-top: 2px solid #e9ecef;
    margin-top: auto;
}

.receipt-footer p {
    font-size: 10px;
    color: #6c757d;
    margin: 4px 0;
    line-height: 1.5;
}

.receipt-footer strong {
    color: #ff5100;
    font-weight: 600;
}

/* Share Modal Styles */
.share-modal {
    background: white;
    border-radius: 1rem;
    max-width: 500px;
    width: 100%;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.share-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.share-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
}

.share-body {
    padding: 1.5rem;
}

.share-subtitle {
    color: #6b7280;
    margin-bottom: 1.5rem;
    font-size: 0.95rem;
}

.share-options {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.share-option {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.75rem;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: left;
}

.share-option:hover:not(:disabled) {
    border-color: #ff5100;
    background: #fff5f0;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 81, 0, 0.1);
}

.share-option:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.share-icon-wrapper {
    width: 56px;
    height: 56px;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.whatsapp-bg {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    color: white;
}

.email-bg {
    background: linear-gradient(135deg, #EA4335 0%, #C5221F 100%);
    color: white;
}

.download-bg {
    background: linear-gradient(135deg, #4285F4 0%, #1967D2 100%);
    color: white;
}

.share-content {
    flex: 1;
}

.share-content h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
}

.share-content p {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
}

.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.btn-outline {
    background: white;
    color: #374151;
    border: 2px solid #e5e7eb;
}

.btn-outline:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}

.w-full {
    width: 100%;
}

.mt-6 {
    margin-top: 1.5rem;
}

.safe-text {
    max-width: 100%;
    word-break: break-word;
    overflow-wrap: anywhere;
    white-space: normal;
}
</style>
