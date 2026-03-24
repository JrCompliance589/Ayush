
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
                                <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $t('Delete Terms & Conditions') }}
                                </h3>
                                <p class="text-sm text-slate-600 mb-4">
                                    {{ $t('Are you sure you want to delete these terms & conditions?') }}
                                </p>
                                <div class="bg-slate-50 rounded-xl p-4 text-left">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="flex-shrink-0 p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" class="text-white">
                                                <path fill="currentColor"
                                                    d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-slate-900 mb-1">{{
                                                termsConditions.name }}</p>
                                            <p class="text-xs text-slate-600">{{ formatCategory(termsConditions.category)
                                                }}</p>
                                            <p class="text-xs text-slate-500 mt-1">{{ termsConditions.terms.length }} {{
                                                $t('terms') }}</p>
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
    termsConditions: {
        type: Object,
        required: true
    }
});

defineEmits(['close', 'confirm']);

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