<script setup>
import { computed } from 'vue';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle } from '@headlessui/vue';

const props = defineProps({
    isOpen: Boolean,
    invoiceData: { type: Object, default: () => ({}) },
    closeBtn: { type: Boolean, default: true },
    showHeader: { type: Boolean, default: true }
});

const emit = defineEmits(['close']);

function closeModal() {
    emit('close', true);
}

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatCurrency = (amount) => {
    if (!amount) return '₹0.00';
    return `₹${parseFloat(amount).toFixed(2)}`;
};

const getPaymentStatusClass = computed(() => {
    const status = props.invoiceData?.payment_status?.toLowerCase();
    if (status === 'paid') return 'bg-green-100 text-green-800';
    if (status === 'unpaid') return 'bg-yellow-100 text-yellow-800';
    if (status === 'partial') return 'bg-yellow-100 text-yellow-800';
    if (status === 'expired') return 'bg-red-100 text-red-800';
    return 'bg-gray-100 text-gray-800';
});

const getPaymentStatusText = computed(() => {
    return props.invoiceData?.payment_status?.toUpperCase() || 'N/A';
});

</script>

<template>
    <TransitionRoot appear :show="props.isOpen" as="template">
        <Dialog as="div" class="relative z-50">
            <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100"
                leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-black bg-opacity-40" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95">
                        <DialogPanel
                            class="w-full max-w-4xl transform overflow-hidden rounded-xl bg-white text-left align-middle shadow-xl transition-all">

                            <!-- Header -->
                            <div v-if="props.showHeader" class="flex justify-between items-center px-6 py-4"
                                style="background: linear-gradient(135deg, #ff5100 0%, #ff7a3d 100%);">
                                <DialogTitle as="h3" class="text-xl font-bold text-white">
                                    Invoice Details
                                </DialogTitle>

                                <span @click="closeModal" v-if="closeBtn"
                                    class="bg-white bg-opacity-20 rounded-full p-1.5 hover:bg-opacity-30 cursor-pointer transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                        <path fill="none" stroke="white" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M17 7L7 17M7 7l10 10" />
                                    </svg>
                                </span>
                            </div>

                            <!-- Body -->
                            <div class="px-6 py-6 max-h-[70vh] overflow-y-auto">
                                <!-- Company Information -->
                                <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <h4 class="text-lg font-semibold mb-3" style="color: #ff5100;">Company Information
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-xs font-medium text-gray-500 uppercase">Company
                                                Name</label>
                                            <p class="text-sm font-semibold text-gray-900 mt-1">{{
                                                invoiceData.company_name || 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium text-gray-500 uppercase">Contact
                                                Person</label>
                                            <p class="text-sm font-semibold text-gray-900 mt-1">{{
                                                invoiceData.contact_person || 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium text-gray-500 uppercase">Phone</label>
                                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ invoiceData.phone ||
                                                'N/A' }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium text-gray-500 uppercase">Email</label>
                                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ invoiceData.email ||
                                                'N/A' }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium text-gray-500 uppercase">GST Number</label>
                                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ invoiceData.gst_number ||
                                                'N/A' }}</p>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-xs font-medium text-gray-500 uppercase">Address</label>
                                            <p class="text-sm font-semibold text-gray-900 mt-1 whitespace-pre-line">{{
                                                invoiceData.address || 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Invoice Numbers & Dates -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                    <!-- Quotation Details -->
                                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                                        <h5 class="font-semibold text-blue-900 mb-3 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Quotation
                                        </h5>
                                        <div class="space-y-2">
                                            <div>
                                                <label class="text-xs font-medium text-blue-700">Number</label>
                                                <p class="text-sm font-mono font-semibold text-blue-900">{{
                                                    invoiceData.quotation_number || 'N/A' }}</p>
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-blue-700">Date</label>
                                                <p class="text-sm font-semibold text-blue-900">{{
                                                    formatDate(invoiceData.quotation_date) }}</p>
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-blue-700">Valid Until</label>
                                                <p class="text-sm font-semibold text-blue-900">{{
                                                    formatDate(invoiceData.quotation_valid_until_date) }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Proforma Details -->
                                    <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                                        <h5 class="font-semibold text-purple-900 mb-3 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Proforma Invoice
                                        </h5>
                                        <div class="space-y-2">
                                            <div>
                                                <label class="text-xs font-medium text-purple-700">Number</label>
                                                <p class="text-sm font-mono font-semibold text-purple-900">{{
                                                    invoiceData.proforma_number || 'N/A' }}</p>
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-purple-700">Date</label>
                                                <p class="text-sm font-semibold text-purple-900">{{
                                                    formatDate(invoiceData.proforma_date) }}</p>
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-purple-700">Valid Until</label>
                                                <p class="text-sm font-semibold text-purple-900">{{
                                                    formatDate(invoiceData.proforma_valid_until_date) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Financial Details -->
                                <div class="mb-6 p-4 rounded-lg border-2"
                                    style="background: linear-gradient(to bottom, #fff5f0, #ffffff); border-color: #ff5100;">
                                    <h4 class="text-lg font-semibold mb-4" style="color: #ff5100;">Financial Breakdown
                                    </h4>

                                    <!-- Charges -->
                                    <div class="space-y-3 mb-4">
                                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                                            <span class="text-sm text-gray-600">Platform Charge</span>
                                            <span class="text-sm font-semibold text-gray-900">{{
                                                formatCurrency(invoiceData.platform_charge) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                                            <span class="text-sm text-gray-600">Wallet Recharge</span>
                                            <span class="text-sm font-semibold text-gray-900">{{
                                                formatCurrency(invoiceData.wallet_recharge) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                                            <span class="text-sm text-gray-600">Setup Fee</span>
                                            <span class="text-sm font-semibold text-gray-900">{{
                                                formatCurrency(invoiceData.setup_fee) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                                            <span class="text-sm text-gray-600">Customization Fee</span>
                                            <span class="text-sm font-semibold text-gray-900">{{
                                                formatCurrency(invoiceData.customization_fee) }}</span>
                                        </div>

                                        <!-- Additional Fees -->
                                        <div v-if="invoiceData.additional_fee && invoiceData.additional_fee.length > 0"
                                            class="mt-4">
                                            <h5 class="text-sm font-semibold text-gray-700 mb-2">Additional Fees</h5>
                                            <div v-for="(fee, index) in invoiceData.additional_fee" :key="index"
                                                class="flex justify-between items-center pb-2 pl-4 border-b border-gray-200">
                                                <span class="text-sm text-gray-600">{{ fee.description }}</span>
                                                <span class="text-sm font-semibold text-gray-900">{{
                                                    formatCurrency(fee.amount) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Totals -->
                                    <div class="space-y-2 pt-3 border-t-2 border-gray-300">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-gray-700">Sub Total</span>
                                            <span class="text-sm font-bold text-gray-900">{{
                                                formatCurrency(invoiceData.sub_total) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-gray-700">Discount ({{
                                                invoiceData.discount }}%)</span>
                                            <span class="text-sm font-bold text-red-600">- {{
                                                formatCurrency(invoiceData.discount_amount) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-gray-700">After Discount</span>
                                            <span class="text-sm font-bold text-gray-900">{{
                                                formatCurrency(invoiceData.amount_after_discount) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-gray-700">GST ({{ invoiceData.GST
                                                }}%)</span>
                                            <span class="text-sm font-bold text-gray-900">{{
                                                formatCurrency(invoiceData.GST_amount) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center pt-3 border-t-2 border-gray-300">
                                            <span class="text-base font-bold" style="color: #ff5100;">Total
                                                Amount</span>
                                            <span class="text-xl font-bold" style="color: #ff5100;">{{
                                                formatCurrency(invoiceData.total) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Information -->
                                <div class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
                                    <h4 class="text-lg font-semibold text-green-900 mb-3 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        Payment Status
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-xs font-medium text-green-700 uppercase">Status</label>
                                            <div class="mt-1">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold"
                                                    :class="getPaymentStatusClass">
                                                    {{ getPaymentStatusText }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Download Links -->
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Download Documents
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <a v-if="invoiceData.quotation_invoice_pdf_url"
                                            :href="invoiceData.quotation_invoice_pdf_url" target="_blank"
                                            class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all border-2"
                                            style="background-color: #fff5f0; border-color: #ff5100; color: #ff5100;"
                                            onmouseover="this.style.backgroundColor='#ff5100'; this.style.color='white';"
                                            onmouseout="this.style.backgroundColor='#fff5f0'; this.style.color='#ff5100';">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                            </svg>
                                            Quotation
                                        </a>
                                        <a v-if="invoiceData.proforma_invoice_pdf_url"
                                            :href="invoiceData.proforma_invoice_pdf_url" target="_blank"
                                            class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all border-2"
                                            style="background-color: #fff5f0; border-color: #ff5100; color: #ff5100;"
                                            onmouseover="this.style.backgroundColor='#ff5100'; this.style.color='white';"
                                            onmouseout="this.style.backgroundColor='#fff5f0'; this.style.color='#ff5100';">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                            </svg>
                                            Proforma
                                        </a>
                                        <a v-for="(ledger, index) in invoiceData?.payment_ledger?.filter(
                                            ledger => !!ledger?.payment_invoice_pdf_url
                                        )" :key="ledger.id || index" :href="ledger.payment_invoice_pdf_url" target="_blank"
                                            class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all border-2"
                                            style="background-color: #fff5f0; border-color: #ff5100; color: #ff5100;"
                                            onmouseover="this.style.backgroundColor='#ff5100'; this.style.color='white';"
                                            onmouseout="this.style.backgroundColor='#fff5f0'; this.style.color='#ff5100';">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                            </svg>
                                            Receipt {{ index + 1 }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="flex justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">
                                <button type="button"
                                    class="px-6 py-2.5 rounded-lg text-sm font-semibold transition-all border-2"
                                    style="background-color: white; border-color: #ff5100; color: #ff5100;"
                                    onmouseover="this.style.backgroundColor='#ff5100'; this.style.color='white';"
                                    onmouseout="this.style.backgroundColor='white'; this.style.color='#ff5100';"
                                    @click="closeModal">
                                    Close
                                </button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>