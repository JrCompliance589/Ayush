<!-- <template>
    <AppLayout>
        <div>
            <h2 class="text-xl mb-1">{{ $t('Email templates') }}</h2>
            <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                </svg>
                <span class="ml-1 mt-1">{{ $t('Configure email templates') }}</span>
            </p>
        </div>
        <EmailTemplateTable :rows="props.rows" :filters="props.filters" />
    </AppLayout>
</template>
<script setup>
import AppLayout from "../Layout/App.vue";
import EmailTemplateTable from '@/Components/Tables/EmailTemplateTable.vue';

const props = defineProps({ rows: Object, filters: Object });
</script> -->


<!-- ============================================ NEW UI CODE ============================================= -->

<template>
    <AppLayout>
        <div class="space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <!-- Email Templates Card -->
            <div class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                    <div class="flex items-center gap-3">
                        <div
                            class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                class="text-white">
                                <path fill="currentColor"
                                    d="M22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6zm-2 0l-8 5l-8-5h16zm0 12H4V8l8 5l8-5v10z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">{{ $t('Email Templates') }}</h2>
                            <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    class="text-[#ff5100]">
                                    <path fill="none" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2"
                                        d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                </svg>
                                <span>{{ $t('Configure email templates') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Table Component -->
                    <EmailTemplateTable :rows="props.rows" :filters="props.filters" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "../Layout/App.vue";
import EmailTemplateTable from '@/Components/Tables/EmailTemplateTable.vue';

const props = defineProps({
    rows: Object,
    filters: Object
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