<!-- <template>
    <AppLayout>
        <div>
            <h2 class="text-xl mb-1">{{ $t('Payment gateways') }}</h2>
            <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                </svg>
                <span class="ml-1 mt-1">{{ $t('Configure payment gateways for accepting subscription payments')
                    }}</span>
            </p>
        </div>
        <PaymentGatewayTable :rows="props.rows" />
    </AppLayout>
</template>
<script setup>
import AppLayout from "./Layout/App.vue";
import PaymentGatewayTable from '@/Components/Tables/PaymentGatewayTable.vue';
const props = defineProps({ rows: Object });
</script> -->


<!-- ============================================ NEW UI CODE ============================================= -->

<template>
    <AppLayout>
        <div class="space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <!-- Payment Gateways Card -->
            <div class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                    <div class="flex items-center gap-3">
                        <div
                            class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                class="text-white">
                                <path fill="currentColor"
                                    d="M20 8H4V6h16m0 12H4v-6h16m0-8H4c-1.11 0-2 .89-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">{{ $t('Payment Gateways') }}</h2>
                            <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    class="text-[#ff5100]">
                                    <path fill="none" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2"
                                        d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                </svg>
                                <span>{{ $t('Configure payment gateways for accepting subscription payments') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Table Component -->
                    <PaymentGatewayTable :rows="props.rows" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./Layout/App.vue";
import PaymentGatewayTable from '@/Components/Tables/PaymentGatewayTable.vue';

const props = defineProps({
    rows: Object
});
</script>

<style scoped>
.slide-fade-enter-active {
    transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
    transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    transform: translateY(-10px);
    opacity: 0;
}
</style>