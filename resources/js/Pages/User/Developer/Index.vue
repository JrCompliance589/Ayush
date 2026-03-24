<!-- <template>
    <AppLayout>
        <div class="bg-white md:bg-inherit md:flex md:flex-grow md:overflow-y-hidden">
            <div class="md:w-[60%] m-8">
                <Menu v-if="webhookModule" />
                <div class="flex justify-between mt-8">
                    <div>
                        <h3 class="text-md mb-1">{{ $t('Access Tokens') }}</h3>
                        <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                            </svg>
                            <span class="ml-1 mt-1">{{ $t('Create and manage your API keys') }}</span>
                        </p>
                    </div>
                    <div>
                        <button @click="generateToken()" type="button" :disabled="loadIcon"
                            class="rounded-md bg-primary px-3 py-2 text-sm text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <span v-if="loadIcon == false">{{ $t('Generate API key') }}</span>
                            <span v-else>
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                    <path fill="none" stroke="currentColor" stroke-dasharray="15" stroke-dashoffset="15"
                                        stroke-linecap="round" stroke-width="2" d="M12 3C16.9706 3 21 7.02944 21 12">
                                        <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.3s"
                                            values="15;0" />
                                        <animateTransform attributeName="transform" dur="1.5s" repeatCount="indefinite"
                                            type="rotate" values="0 12 12;360 12 12" />
                                    </path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
                <TokenTable :rows="props.rows" />
            </div>
            <div class="md:w-[40%] border-l bg-black h-screen hidden md:block">
                <Documentation :apirequests="apirequests" />
            </div>
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from "./../Layout/App.vue";
import Documentation from "./Documentation.vue";
import Menu from "./Menu.vue";
import { useForm } from "@inertiajs/vue3";
import TokenTable from '@/Components/Tables/TokenTable.vue';
import { ref } from 'vue';

const props = defineProps(['rows', 'url', 'apirequests', 'webhookModule']);

const loadIcon = ref(false);

const form = useForm({
    'name': null,
});


const generateToken = () => {
    loadIcon.value = true;

    form.post('/developer-tools/access-tokens', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onFinish: () => {
            loadIcon.value = false;
        }
    })
}
</script>

 -->

<!-- ============================================ NEW UI CODE ============================================= -->


<template>
    <AppLayout>
        <div
            class="lg:h-[calc(100vh-65px)] overflow-hidden bg-gradient-to-br from-slate-50 via-orange-50/30 to-slate-100">
            <!-- Main Container -->
            <div class="grid grid-cols-5 h-full overflow-hidden">

                <!-- Left Panel - Main Content -->
                <div class="col-span-5 lg:col-span-3 overflow-y-auto">
                    <div class="w-full mx-auto pt-10 px-4 md:pt-8 md:p-8">

                        <!-- Menu Component -->
                        <Menu v-if="webhookModule" />

                        <!-- Header Section with Glass Effect -->
                        <div class="mt-8 mb-8">
                            <div
                                class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg border border-white/20 p-8 hover:shadow-xl transition-all duration-300">
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">

                                    <!-- Title & Description -->
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-3">
                                            <div
                                                class="p-2 bg-gradient-to-br from-[#ff5100] to-orange-600 rounded-xl shadow-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="2" y="6" width="20" height="12" rx="2" />
                                                    <path d="M12 12h.01" />
                                                    <path d="M17 12h.01" />
                                                    <path d="M7 12h.01" />
                                                </svg>
                                            </div>
                                            <h3
                                                class="text-2xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                                                {{ $t('Access Tokens') }}
                                            </h3>
                                        </div>

                                        <div class="flex items-start gap-2 text-slate-600">

                                            <span class="text-sm leading-relaxed">
                                                {{ $t('Create and manage your API keys') }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Generate Button -->
                                    <div class="flex-shrink-0">
                                        <button @click="generateToken()" type="button" :disabled="loadIcon"
                                            class="group relative px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:shadow-lg hover:bg-primary/90 transform transition-all duration-300 overflow-hidden">
                                            <!-- Button Content -->
                                            <span v-if="!loadIcon" class="relative flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <line x1="12" y1="8" x2="12" y2="16" />
                                                    <line x1="8" y1="12" x2="16" y2="12" />
                                                </svg>
                                                {{ $t('Generate API key') }}
                                            </span>
                                            <span v-else class="relative flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24" class="animate-spin">
                                                    <path fill="none" stroke="currentColor" stroke-dasharray="15"
                                                        stroke-dashoffset="15" stroke-linecap="round" stroke-width="2"
                                                        d="M12 3C16.9706 3 21 7.02944 21 12">
                                                        <animate fill="freeze" attributeName="stroke-dashoffset"
                                                            dur="0.3s" values="15;0" />
                                                        <animateTransform attributeName="transform" dur="1.5s"
                                                            repeatCount="indefinite" type="rotate"
                                                            values="0 12 12;360 12 12" />
                                                    </path>
                                                </svg>
                                            </span>
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Token Table Component with Enhanced Styling -->
                        <div
                            class="bg-white/60 backdrop-blur-sm rounded-2xl shadow-lg border border-primary/10 overflow-hidden">
                            <TokenTable :rows="props.rows" />
                        </div>

                    </div>
                </div>

                <!-- Right Panel - Documentation (Desktop Only) -->
                <div
                    class="col-span-5 lg:col-span-2 bg-white lg:rounded-l-2xl border-l border-primary/10 overflow-hidden relative">
                    <!-- Documentation Content -->
                    <div class="relative z-10 h-full">
                        <Documentation :apirequests="apirequests" />
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import Documentation from "./Documentation.vue";
import Menu from "./Menu.vue";
import { useForm } from "@inertiajs/vue3";
import TokenTable from '@/Components/Tables/TokenTable.vue';
import { ref } from 'vue';

const props = defineProps(['rows', 'url', 'apirequests', 'webhookModule']);

const loadIcon = ref(false);

const form = useForm({
    'name': null,
});

const generateToken = () => {
    loadIcon.value = true;

    form.post('/developer-tools/access-tokens', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onFinish: () => {
            loadIcon.value = false;
        }
    })
}
</script>

<style scoped>
/* Smooth animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeInUp {
    animation: fadeInUp 0.6s ease-out;
}
</style>





