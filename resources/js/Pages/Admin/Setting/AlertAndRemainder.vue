<template>
    <AppLayout>
        <div class="p-6 md:p-8 space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <!-- Alerts & Reminders Card -->
            <div class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] text-white rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <Siren />
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Alerts & Reminders') }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Manage automated notifications for subscription renewals') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Add Alert/Reminder Button -->
                        <button @click="openModal()" type="button"
                            class="group px-6 py-2.5 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 transition-all duration-200">
                            <span class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                                </svg>
                                {{ $t('Create Alert') }}
                            </span>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
                        <!-- Total Alerts -->
                        <div class="group relative">
                            <div
                                class="absolute -inset-0.5 bg-gradient-to-r from-blue-400 to-blue-500 rounded-2xl blur opacity-20 hover:opacity-40 transition">
                            </div>
                            <div
                                class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-blue-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/10">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-2 shadow-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" class="text-white">
                                            <path fill="currentColor"
                                                d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
                                        </svg>
                                    </div>
                                    <div
                                        class="flex items-center gap-1 bg-emerald-50 rounded-lg px-2 py-1 border border-emerald-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 24 24" class="text-emerald-600">
                                            <path fill="currentColor"
                                                d="M16 6l2.29 2.29l-4.88 4.88l-4-4L2 16.59L3.41 18l6-6l4 4l6.3-6.29L22 12V6h-6z" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-gray-600 text-sm font-medium mb-1">{{ $t('Total Alerts') }}</p>
                                <p class="text-gray-900 text-2xl font-bold mb-1">{{ stats.total || 0 }}</p>
                                <button
                                    class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 text-sm font-medium group-hover:gap-2 transition-all">
                                    <span>{{ $t("View details") }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Active -->
                        <div class="group relative">
                            <div
                                class="absolute -inset-0.5 bg-gradient-to-r from-green-400 to-emerald-400 rounded-2xl blur opacity-20 hover:opacity-40 transition">
                            </div>
                            <div
                                class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-emerald-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-emerald-500/10">
                                <div class="flex justify-between items-start mb-4">
                                    <div
                                        class="bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl p-2 shadow-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" class="text-white">
                                            <path fill="currentColor"
                                                d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2m-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9Z" />
                                        </svg>
                                    </div>
                                    <div
                                        class="flex items-center gap-1 bg-emerald-50 rounded-lg px-2 py-1 border border-emerald-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 24 24" class="text-emerald-600">
                                            <path fill="currentColor"
                                                d="M16 6l2.29 2.29l-4.88 4.88l-4-4L2 16.59L3.41 18l6-6l4 4l6.3-6.29L22 12V6h-6z" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-gray-600 text-sm font-medium mb-1">{{ $t('Active') }}</p>
                                <p class="text-gray-900 text-2xl font-bold mb-1">{{ stats.active || 0 }}</p>
                                <button
                                    class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-700 text-sm font-medium group-hover:gap-2 transition-all">
                                    <span>{{ $t("View details") }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Sent Today -->
                        <div class="group relative">
                            <div
                                class="absolute -inset-0.5 bg-gradient-to-r from-amber-400 to-orange-400 rounded-2xl blur opacity-20 hover:opacity-40 transition">
                            </div>
                            <div
                                class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-amber-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-amber-500/10">
                                <div class="flex justify-between items-start mb-4">
                                    <div
                                        class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl p-2 shadow-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" class="text-white">
                                            <path fill="currentColor"
                                                d="M2.01 21L23 12L2.01 3L2 10l15 2l-15 2l.01 7z" />
                                        </svg>
                                    </div>
                                    <div
                                        class="flex items-center gap-1 bg-emerald-50 rounded-lg px-2 py-1 border border-emerald-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 24 24" class="text-emerald-600">
                                            <path fill="currentColor"
                                                d="M16 6l2.29 2.29l-4.88 4.88l-4-4L2 16.59L3.41 18l6-6l4 4l6.3-6.29L22 12V6h-6z" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-gray-600 text-sm font-medium mb-1">{{ $t('Sent Today') }}</p>
                                <p class="text-gray-900 text-2xl font-bold mb-1">{{ stats.sent_today || 0 }}</p>
                                <button
                                    class="inline-flex items-center gap-1 text-amber-600 hover:text-amber-700 text-sm font-medium group-hover:gap-2 transition-all">
                                    <span>{{ $t("View details") }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Success Rate -->
                        <div class="group relative col-span-2 lg:col-span-1">
                            <div
                                class="absolute -inset-0.5 bg-gradient-to-r from-purple-400 to-pink-400 rounded-2xl blur opacity-20 hover:opacity-40 transition">
                            </div>
                            <div
                                class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-purple-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-purple-500/10">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl p-2 shadow-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" class="text-white">
                                            <path fill="currentColor"
                                                d="M16 6l2.29 2.29l-4.88 4.88l-4-4L2 16.59L3.41 18l6-6l4 4l6.3-6.29L22 12V6h-6z" />
                                        </svg>
                                    </div>
                                    <div
                                        class="flex items-center gap-1 bg-emerald-50 rounded-lg px-2 py-1 border border-emerald-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 24 24" class="text-emerald-600">
                                            <path fill="currentColor"
                                                d="M16 6l2.29 2.29l-4.88 4.88l-4-4L2 16.59L3.41 18l6-6l4 4l6.3-6.29L22 12V6h-6z" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-gray-600 text-sm font-medium mb-1">{{ $t('Success Rate') }}</p>
                                <p class="text-gray-900 text-2xl font-bold mb-1">{{ stats.success_rate || 0 }}%</p>
                                <button
                                    class="inline-flex items-center gap-1 text-purple-600 hover:text-purple-700 text-sm font-medium group-hover:gap-2 transition-all">
                                    <span>{{ $t("View details") }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="mb-6 border-b border-slate-200">
                        <nav class="flex space-x-8">
                            <button @click="activeTab = 'alerts'" :class="[
                                'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                                activeTab === 'alerts'
                                    ? 'border-[#ff5100] text-[#ff5100]'
                                    : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'
                            ]">
                                {{ $t('All Alerts') }}
                            </button>
                            <button @click="activeTab = 'logs'" :class="[
                                'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                                activeTab === 'logs'
                                    ? 'border-[#ff5100] text-[#ff5100]'
                                    : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'
                            ]">
                                {{ $t('Delivery Logs') }}
                            </button>
                        </nav>
                    </div>

                    <!-- Table Components -->
                    <!-- <AlertsTable v-if="activeTab === 'alerts'" :rows="props.rows" @edit="openModal"
                        @delete="openDeleteAlert" @sendNow="handleSendNow" /> -->

                    <!-- <AlertLogsTable v-if="activeTab === 'logs'" :logs="props.logs" /> -->
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <!-- <AlertModal v-model="isOpenModal" :alert="selectedAlert" :templates="props.templates" :settings="props.settings"
            @close="closeModal" /> -->

        <!-- Delete Confirmation -->
        <!-- <DeleteModal v-model="isOpenDeleteAlert" @confirm="confirmDelete" /> -->
    </AppLayout>
</template>

<script setup>
import { Siren } from "lucide-vue-next";
import AppLayout from "./Layout/App.vue";
import { ref } from 'vue';
// import AlertsTable from '@/Components/Tables/AlertsTable.vue';
// import AlertLogsTable from '@/Components/Tables/AlertLogsTable.vue';
// import AlertModal from '@/Components/Modals/AlertModal.vue';
// import DeleteModal from '@/Components/Modals/DeleteModal.vue';



const props = defineProps({
    rows: Object,
    logs: Object,
    templates: Object,
    settings: Object,
    stats: {
        type: Object,
        default: () => ({
            total: 0,
            active: 0,
            sent_today: 0,
            success_rate: 0
        })
    }
});

const activeTab = ref('alerts');
const isOpenModal = ref(false);
// const isOpenDeleteAlert = ref(false);
const selectedAlert = ref(null);

const openModal = (alert = null) => {
    selectedAlert.value = alert;
    isOpenModal.value = true;
};

// const closeModal = () => {
//     selectedAlert.value = null;
//     isOpenModal.value = false;
// };

// const openDeleteAlert = (id) => {
//     selectedAlert.value = id;
//     isOpenDeleteAlert.value = true;
// };

// const confirmDelete = () => {
//     // Handle delete logic
//     isOpenDeleteAlert.value = false;
//     selectedAlert.value = null;
// };

// const handleSendNow = (alertId) => {
//     // Handle instant send
//     console.log('Sending alert:', alertId);
// };

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