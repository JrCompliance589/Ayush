<!-- <template>
    <AppLayout>
        <div class="bg-white md:bg-inherit md:flex md:flex-grow md:overflow-y-hidden">
            <div class="md:w-[60%] m-8">
                <Menu />
                <div class="flex justify-between mt-8">
                    <div>
                        <h3 class="text-md mb-1">{{ $t('Webhooks') }}</h3>
                        <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                            </svg>
                            <span class="ml-1 mt-1">{{ $t('Manage webhook urls and events') }}</span>
                        </p>
                    </div>
                    <div>
                        <button @click="isOpenModal = true" type="button"
                            class="rounded-md bg-primary px-3 py-2 text-sm text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <span>{{ $t('Add Webhook') }}</span>
                        </button>
                    </div>
                </div>
                <WebhookTable :rows="props.rows" :events="props.events" />
            </div>
            <div class="md:w-[40%] border-l bg-black h-screen hidden md:block">
                <Documentation :apirequests="apirequests" />
            </div>
        </div>

        <Modal :label="$t('Add Webhook')" :isOpen=isOpenModal>
            <div class="mt-5 grid grid-cols-1 gap-x-6">
                <form @submit.prevent="submitForm()" class="grid gap-x-6 sm:grid-cols-6">
                    <FormInput v-model="form.url" :error="form.errors.url" :name="$t('URL')" :type="'text'"
                        :class="'sm:col-span-6'" />
                    <h4 class="mt-4 text-sm">{{ $t('Events') }}</h4>
                    <FormCheckbox v-for="event in props.events" :key="event" v-model="form.events"
                        :error="form.errors.events" :name="$t(event)" :label="$t(event)" :value="event" :type="'text'"
                        :class="'sm:col-span-6'" />


                    <div class="mt-4 flex">
                        <button type="button" @click.self="isOpenModal = false"
                            class="inline-flex justify-center rounded-md border border-transparent bg-slate-50 px-4 py-2 text-sm text-slate-500 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 mr-4">{{
                            $t('Cancel') }}</button>
                        <button
                            :class="['inline-flex justify-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2', { 'opacity-50': form.processing }]"
                            :disabled="form.processing">
                            <svg v-if="form.processing" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                    opacity=".5" />
                                <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                    <animateTransform attributeName="transform" dur="1s" from="0 12 12"
                                        repeatCount="indefinite" to="360 12 12" type="rotate" />
                                </path>
                            </svg>
                            <span v-else>{{ $t('Save') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>
<script setup>
import AppLayout from "../../../../resources/js/Pages/User/Layout/App.vue";
import Documentation from "../../../../resources/js/Pages/User/Developer/Documentation.vue";
import FormCheckbox from '@/Components/FormCheckbox.vue';
import FormInput from '@/Components/FormInput.vue';
import Menu from "../../../../resources/js/Pages/User/Developer/Menu.vue";
import Modal from '@/Components/Modal.vue';
import WebhookTable from './WebhookTable.vue';
import { Link, useForm } from "@inertiajs/vue3";
import { ref } from 'vue';

const props = defineProps({ rows: Object, url: String, apirequests: Object, events: Object });

const isOpenModal = ref(false);
const selectedEvents = ref([]);

const form = useForm({
    url: null,
    events: [],
});

const submitForm = async () => {
    form.post('/developer-tools/webhooks', {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            isOpenModal.value = false
        }
    })
};
</script> -->



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
                        <Menu />

                        <!-- Header Section with Glass Effect -->
                        <div class="mt-8 mb-8">
                            <div
                                class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg border border-white/20 p-8 hover:shadow-xl transition-all duration-300">
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">

                                    <!-- Title & Description -->
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-3">
                                            <div
                                                class="p-2 text-white bg-gradient-to-br from-[#ff5100] to-orange-600 rounded-xl shadow-lg">
                                                <Globe />
                                            </div>
                                            <h3
                                                class="text-2xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                                                {{ $t('Webhooks') }}
                                            </h3>
                                        </div>

                                        <div class="flex items-start gap-2 text-slate-600">
                                            <span class="text-sm leading-relaxed">
                                                {{ $t('Manage webhook urls and events') }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Add Webhook Button -->
                                    <div class="flex-shrink-0">
                                        <button @click="isOpenModal = true" type="button"
                                            class="group relative px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:shadow-lg hover:bg-primary/90 transform transition-all duration-300 overflow-hidden">
                                            <!-- Button Content -->
                                            <span class="relative flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <line x1="12" y1="8" x2="12" y2="16" />
                                                    <line x1="8" y1="12" x2="16" y2="12" />
                                                </svg>
                                                {{ $t('Add Webhook') }}
                                            </span>
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Webhook Table Component with Enhanced Styling -->
                        <div
                            class="bg-white/60 backdrop-blur-sm rounded-2xl shadow-lg border border-primary/10 overflow-hidden">
                            <WebhookTable :rows="props.rows" :events="props.events" />
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

        <!-- Add Webhook Modal -->
        <Modal :label="$t('Add Webhook')" :isOpen="isOpenModal">
            <div class="mt-5 grid grid-cols-1 gap-x-6">
                <form @submit.prevent="submitForm()" class="grid gap-x-6 sm:grid-cols-6">
                    <FormInput v-model="form.url" :error="form.errors.url" :name="$t('URL')" :type="'text'"
                        :class="'sm:col-span-6'" />

                    <div class="sm:col-span-6 mt-4">
                        <h4 class="text-sm font-semibold text-slate-700 mb-3">{{ $t('Events') }}</h4>
                        <div class="space-y-2">
                            <FormCheckbox v-for="event in props.events" :key="event" v-model="form.events"
                                :error="form.errors.events" :name="$t(event)" :label="$t(event)" :value="event"
                                :type="'text'" :class="'sm:col-span-6'" />
                        </div>
                    </div>

                    <div class="sm:col-span-6 mt-6 flex gap-3">
                        <button type="button" @click.self="isOpenModal = false"
                            class="flex-1 justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none transition-colors duration-200">
                            {{ $t('Cancel') }}
                        </button>
                        <button type="submit"
                            :class="['flex-1 justify-center rounded-xl border border-transparent bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary/90 focus:outline-none transition-all duration-200', { 'opacity-50 cursor-not-allowed': form.processing }]"
                            :disabled="form.processing">
                            <span v-if="form.processing" class="flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    class="animate-spin">
                                    <path fill="currentColor"
                                        d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                        opacity=".5" />
                                    <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                        <animateTransform attributeName="transform" dur="1s" from="0 12 12"
                                            repeatCount="indefinite" to="360 12 12" type="rotate" />
                                    </path>
                                </svg>
                                {{ $t('Saving...') }}
                            </span>
                            <span v-else>{{ $t('Save') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup>
import AppLayout from "../../../../resources/js/Pages/User/Layout/App.vue";
import Documentation from "../../../../resources/js/Pages/User/Developer/Documentation.vue";
import FormCheckbox from '@/Components/FormCheckbox.vue';
import FormInput from '@/Components/FormInput.vue';
import Menu from "../../../../resources/js/Pages/User/Developer/Menu.vue";
import Modal from '@/Components/Modal.vue';
import WebhookTable from './WebhookTable.vue';
import { Link, useForm } from "@inertiajs/vue3";
import { ref } from 'vue';
import { Globe } from "lucide-vue-next";

const props = defineProps({ rows: Object, url: String, apirequests: Object, events: Object });

const isOpenModal = ref(false);
const selectedEvents = ref([]);

const form = useForm({
    url: null,
    events: [],
});

const submitForm = async () => {
    form.post('/developer-tools/webhooks', {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            isOpenModal.value = false;
            form.reset();
        }
    })
};
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