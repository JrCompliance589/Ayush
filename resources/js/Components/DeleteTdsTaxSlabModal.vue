<template>
    <Teleport to="body">
        <Transition name="modal">
            <div class="fixed inset-0 z-50 overflow-y-auto" @click.self="$emit('close')">
                <div class="flex min-h-screen items-center justify-center p-4">
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

                    <!-- Modal -->
                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
                        <div class="p-6">
                            <!-- Icon -->
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    class="text-red-600">
                                    <path fill="currentColor"
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                                </svg>
                            </div>

                            <!-- Content -->
                            <div class="text-center mb-6">
                                <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $t('Delete TDS Tax Slab') }}</h3>
                                <p class="text-sm text-slate-600 mb-4">
                                    {{ $t('Are you sure you want to delete this TDS tax slab?') }}
                                </p>
                                <div class="bg-slate-50 rounded-xl p-4 text-left">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="flex-shrink-0 p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" class="text-white">
                                                <path fill="currentColor"
                                                    d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-slate-900 mb-1">{{
                                                tdsSlab.section_code }}</p>
                                            <p class="text-xs text-slate-600 mb-2">{{ tdsSlab.section_name }}</p>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-xs text-slate-500">{{ formatServiceType(tdsSlab.service_type) }}</span>
                                                <span class="text-xs text-slate-400">•</span>
                                                <span class="text-xs text-slate-500">{{ $t('Individual') }}: {{ tdsSlab.rate_individual }}%</span>
                                                <span class="text-xs text-slate-400">•</span>
                                                <span class="text-xs text-slate-500">{{ $t('Company') }}: {{ tdsSlab.rate_company }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-xs text-red-600 mt-3 font-medium">
                                    {{ $t('This action cannot be undone') }}
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-3">
                                <button type="button" @click="$emit('close')"
                                    class="flex-1 px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition-all duration-200">
                                    {{ $t('Cancel') }}
                                </button>
                                <button type="button" @click="$emit('confirm')"
                                    class="flex-1 px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-xl shadow-lg shadow-red-500/20 hover:shadow-xl hover:shadow-red-500/30 transition-all duration-200">
                                    {{ $t('Delete') }}
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

defineEmits(['close', 'confirm']);

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