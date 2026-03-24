<!-- <template>
    <AppLayout>
        <div
            class="bg-white md:bg-inherit pt-10 px-4 md:pt-8 md:p-8 rounded-[5px] text-[#000] h-full md:overflow-y-auto">
            <div class="flex justify-between">
                <div>
                    <h2 class="text-xl mb-1">{{ $t('Support tickets') }}</h2>
                    <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                        </svg>
                        <span class="ml-1 mt-1">{{ $t(`Have an issue? Create a ticket and one of our reps will be in
                            touch`) }}</span>
                    </p>
                </div>
                <div>
                    <Link href="/support/create"
                        class="rounded-md bg-indigo-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    {{ $t('Create') }}</Link>
                </div>
            </div>
            <TicketTable :rows="props.rows" />
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from "./../Layout/App.vue";
import { Link } from "@inertiajs/vue3";
import TicketTable from '@/Components/Tables/TicketTable.vue';

const props = defineProps({ rows: Object });
</script> -->


<!-- ============================================ NEW UI ============================================= -->

<template>
    <AppLayout>
        <div class="min-h-screen bg-white pt-10 px-4 md:pt-8 md:p-8">
            <!-- Header Card -->
            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 relative z-10">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7a3d] rounded-2xl shadow-lg shadow-orange-500/25">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                                    fill="none" stroke="white" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                    <path d="M8 10h.01M12 10h.01M16 10h.01"></path>
                                </svg>
                            </div>
                            <div>
                                <h1
                                    class="text-3xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                                    {{ $t('Support Tickets') }}
                                </h1>
                            </div>
                        </div>
                        <div class="flex items-start gap-2 text-slate-600 ml-1">
                            <p class="text-sm md:text-base leading-relaxed">
                                {{ $t('Have an issue? Create a ticket and one of our reps will be in touch') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex-shrink-0">
                        <Link href="/support/create"
                            class="relative inline-flex items-center gap-3 px-6 py-3 bg-primary text-white font-semibold rounded-2xl hover:shadow-lg transition-all duration-300 overflow-hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                            class="relative z-10">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span class="relative z-10">{{ $t('Create Ticket') }}</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-white rounded-3xl shadow-md border-2 border-primary/10 p-4 overflow-hidden">
                <TicketTable :rows="props.rows" />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import { Link } from "@inertiajs/vue3";
import TicketTable from '@/Components/Tables/TicketTable.vue';

const props = defineProps({ rows: Object });
</script>

<style scoped>
/* Custom animations */
@keyframes float {

    0%,
    100% {
        transform: translateY(0px);
    }

    50% {
        transform: translateY(-10px);
    }
}

.group:hover svg {
    animation: float 2s ease-in-out infinite;
}

/* Smooth transitions */
* {
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
