<template>
    <Teleport to="body">
        <Transition name="modal">
            <div class="fixed inset-0 z-50 overflow-y-auto" @click.self="$emit('close')">
                <div class="flex min-h-screen items-center justify-center p-4">
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

                    <!-- Modal -->
                    <div
                        class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                        <!-- Header -->
                        <div
                            class="sticky top-0 bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200 z-10">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" class="text-white">
                                            <path fill="currentColor"
                                                d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11zM8 15.01l1.41 1.41L11 14.84V19h2v-4.16l1.59 1.59L16 15.01L12.01 11z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold text-slate-900">{{ tdsSlab.section_code }}</h2>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span
                                                class="px-2 py-0.5 bg-slate-100 text-slate-700 text-xs font-medium rounded">
                                                {{ formatServiceType(tdsSlab.service_type) }}
                                            </span>
                                            <span v-if="tdsSlab.is_default"
                                                class="px-2 py-0.5 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white text-xs font-semibold rounded">
                                                {{ $t('Default') }}
                                            </span>
                                            <span :class="getStatusClass(tdsSlab.is_active)"
                                                class="px-2 py-0.5 text-xs font-semibold rounded">
                                                {{ tdsSlab.is_active ? $t('Active') : $t('Inactive') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <button @click="$emit('close')" type="button"
                                    class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6 space-y-6">
                            <!-- Section Details -->
                            <div class="bg-gradient-to-br from-slate-50 to-white rounded-xl p-5 border border-slate-200">
                                <h3 class="text-base font-bold text-slate-900 mb-3">{{ tdsSlab.section_name }}</h3>
                                <p v-if="tdsSlab.description" class="text-sm text-slate-700 leading-relaxed">
                                    {{ tdsSlab.description }}
                                </p>
                                <p v-else class="text-sm text-slate-500 italic">{{ $t('No description provided') }}</p>
                            </div>

                            <!-- Tax Rates -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div
                                    class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border border-green-200">
                                    <div class="flex items-start gap-3">
                                        <div class="p-2 bg-green-100 rounded-lg shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" class="text-green-600">
                                                <path fill="currentColor"
                                                    d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4s-4 1.79-4 4s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs text-green-600 font-medium mb-1">{{ $t('Individual Rate')
                                                }}</p>
                                            <p class="text-2xl font-bold text-green-900 mb-2">
                                                {{ tdsSlab.rate_individual }}%
                                            </p>
                                            <div class="text-xs text-green-700 space-y-1">
                                                <p>{{ $t('On') }} ₹50,000: <span class="font-semibold">₹{{
                                                    calculateTax(50000, tdsSlab.rate_individual,
                                                        tdsSlab.threshold_limit) }}</span></p>
                                                <p>{{ $t('On') }} ₹1,00,000: <span class="font-semibold">₹{{
                                                    calculateTax(100000, tdsSlab.rate_individual,
                                                        tdsSlab.threshold_limit) }}</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-5 border border-purple-200">
                                    <div class="flex items-start gap-3">
                                        <div class="p-2 bg-purple-100 rounded-lg shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" class="text-purple-600">
                                                <path fill="currentColor"
                                                    d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs text-purple-600 font-medium mb-1">{{ $t('Company Rate') }}
                                            </p>
                                            <p class="text-2xl font-bold text-purple-900 mb-2">
                                                {{ tdsSlab.rate_company }}%
                                            </p>
                                            <div class="text-xs text-purple-700 space-y-1">
                                                <p>{{ $t('On') }} ₹50,000: <span class="font-semibold">₹{{
                                                    calculateTax(50000, tdsSlab.rate_company, tdsSlab.threshold_limit)
                                                        }}</span></p>
                                                <p>{{ $t('On') }} ₹1,00,000: <span class="font-semibold">₹{{
                                                    calculateTax(100000, tdsSlab.rate_company, tdsSlab.threshold_limit)
                                                        }}</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Threshold -->
                            <div
                                class="bg-gradient-to-br from-orange-50 to-yellow-50 rounded-xl p-5 border border-orange-200">
                                <div class="flex items-start gap-3">
                                    <div class="p-2 bg-orange-100 rounded-lg shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" class="text-orange-600">
                                            <path fill="currentColor"
                                                d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15c0-1.09 1.01-1.85 2.7-1.85c1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61c0 2.31 1.91 3.46 4.7 4.13c2.5.6 3 1.48 3 2.41c0 .69-.49 1.79-2.7 1.79c-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55c0-2.84-2.43-3.81-4.7-4.4z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-orange-600 font-medium mb-1">{{ $t('Threshold Limit') }}
                                        </p>
                                        <p class="text-2xl font-bold text-orange-900 mb-2">
                                            {{ formatCurrency(tdsSlab.threshold_limit) }}
                                        </p>
                                        <p class="text-xs text-orange-700">
                                            {{ $t('TDS will be deducted only if payment exceeds this amount') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Effective Period -->
                            <div
                                class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-200">
                                <div class="flex items-start gap-3">
                                    <div class="p-2 bg-blue-100 rounded-lg shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" class="text-blue-600">
                                            <path fill="currentColor"
                                                d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-blue-600 font-medium mb-2">{{ $t('Effective Period') }}</p>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-xs text-blue-700 mb-1">{{ $t('From') }}</p>
                                                <p class="text-sm font-semibold text-blue-900">
                                                    {{ formatDate(tdsSlab.effective_from) }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-blue-700 mb-1">{{ $t('To') }}</p>
                                                <p class="text-sm font-semibold text-blue-900">
                                                    {{ tdsSlab.effective_to ? formatDate(tdsSlab.effective_to) :
                                                        $t('Indefinite') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div v-if="tdsSlab.notes"
                                class="bg-gradient-to-br from-slate-50 to-white rounded-xl p-5 border border-slate-200">
                                <div class="flex items-start gap-3">
                                    <div class="p-2 bg-slate-100 rounded-lg shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" class="text-slate-600">
                                            <path fill="currentColor"
                                                d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-slate-600 font-medium mb-2">{{ $t('Additional Notes') }}
                                        </p>
                                        <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap">{{
                                            tdsSlab.notes }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="sticky bottom-0 bg-slate-50 px-6 py-4 border-t border-slate-200">
                            <div class="flex items-center justify-end">
                                <button @click="$emit('close')" type="button"
                                    class="px-6 py-2.5 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 transition-all duration-200">
                                    {{ $t('Close') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
defineProps({
    tdsSlab: {
        type: Object,
        required: true
    }
});

defineEmits(['close']);

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
        month: 'long',
        year: 'numeric'
    });
};

const getStatusClass = (isActive) => {
    return isActive ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700';
};

const calculateTax = (amount, rate, threshold) => {
    const amt = Number(amount);
    const rt = Number(rate);
    const thr = Number(threshold);

    if (isNaN(amt) || isNaN(rt) || isNaN(thr) || amt < thr) {
        return '0';
    }

    const tax = (amt * rt) / 100;
    return new Intl.NumberFormat('en-IN', {
        maximumFractionDigits: 2,
        minimumFractionDigits: 2
    }).format(tax);
};
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
</style>