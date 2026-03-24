<!-- <template>
    <AppLayout>
        <div>
            <h2 class="text-xl mb-1">{{ $t('Broadcast drivers') }}</h2>
            <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                </svg>
                <span class="ml-1 mt-1">{{ $t('Configure your broadcast drivers for realtime chat notifications')
                }}</span>
            </p>
        </div>
        <form @submit.prevent="submitForm()">
            <div class="space-y-12">
                <div class="pb-12">
                    <div v-if="form.broadcast_driver === 'pusher'"
                        class="grid gap-6 grid-cols-2 pb-10 border-b md:w-2/3">
                        <FormSelect v-model="form.broadcast_driver" :name="$t('Broadcast driver')" :type="'text'"
                            :options="methods" :error="form.errors.broadcast_driver" :class="'col-span-2'" />
                        <FormInput v-model="form.pusher_app_id" :name="$t('Pusher app id')" :type="'text'"
                            :error="form.errors.pusher_app_id" :class="'col-span-1'" />
                        <FormInput v-model="form.pusher_app_key" :name="$t('Pusher app key')" :type="'text'"
                            :error="form.errors.pusher_app_key" :class="'col-span-1'" />
                        <FormInput v-model="form.pusher_app_secret" :name="$t('Pusher app secret')" :type="'password'"
                            :error="form.errors.pusher_app_secret" :class="'col-span-1'" />
                        <FormInput v-model="form.pusher_app_cluster" :name="$t('Pusher app cluster')" :type="'text'"
                            :error="form.errors.pusher_app_cluster" :class="'col-span-1'" />
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-x-6 md:w-2/3">
                        <button type="button" class="text-sm leading-6 text-gray-900">{{ $t('Cancel') }}</button>
                        <button
                            :class="['inline-flex justify-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2', { 'opacity-50': isLoading }]"
                            :disabled="isLoading">
                            <svg v-if="isLoading" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
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
                </div>
            </div>
        </form>
    </AppLayout>
</template>
<script setup>
import AppLayout from "./Layout/App.vue";
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';

const props = defineProps({
    config: {
        type: Object,
        required: true
    }
});

const getValueByKey = (key) => {
    const found = props.config.find(item => item.key === key);
    return found ? found.value : '';
};

const isLoading = ref(false);
const form = useForm({
    broadcast_driver: getValueByKey('broadcast_driver'),
    pusher_app_key: getValueByKey('pusher_app_key'),
    pusher_app_id: getValueByKey('pusher_app_id'),
    pusher_app_secret: getValueByKey('pusher_app_secret'),
    pusher_app_cluster: getValueByKey('pusher_app_cluster'),
})

const methods = [
    { label: 'Pusher', value: 'pusher' },
]

const submitForm = async () => {
    form.put('/admin/settings?type=broadcast', {
        preserveScroll: true,
    });
};
</script> -->


<!-- ============================================ NEW UI CODE ============================================= -->



<template>
    <AppLayout>
        <div class="space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <form @submit.prevent="submitForm()" class="w-full mx-auto space-y-6">

                <!-- Broadcast Drivers Card -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                    <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12S6.477 2 12 2zm0 2a8 8 0 1 0 0 16a8 8 0 0 0 0-16zm0 1a7 7 0 1 1 0 14a7 7 0 0 1 0-14zm0 2a5 5 0 1 0 0 10a5 5 0 0 0 0-10zm0 2a3 3 0 1 1 0 6a3 3 0 0 1 0-6z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Broadcast Drivers') }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Configure your broadcast drivers for realtime chat notifications')
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Broadcast Driver Selection -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('Broadcast Driver') }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Select your preferred broadcast service') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3">
                                <FormSelect v-model="form.broadcast_driver" :name="''" :type="'text'" :options="methods"
                                    :error="form.errors.broadcast_driver" />
                            </div>
                        </div>

                        <!-- Pusher Configuration -->
                        <transition name="slide-fade">
                            <div v-if="form.broadcast_driver === 'pusher'"
                                class="space-y-6 p-6 rounded-xl bg-slate-50 border-2 border-slate-100">

                                <div class="flex items-center gap-2 pb-4 border-b border-[#ff5100]/10">
                                    <div class="p-2 bg-white rounded-lg shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 24 24" class="text-[#ff5100]">
                                            <path fill="currentColor"
                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                        </svg>
                                    </div>

                                    <div>
                                        <h3 class="text-base font-bold text-slate-900">{{ $t('Pusher Configuration') }}
                                        </h3>
                                        <p class="text-xs text-slate-600">{{ $t('Enter your Pusher credentials') }}</p>
                                    </div>
                                </div>

                                <!-- Pusher App ID & Key -->
                                <div class="grid md:grid-cols-5 gap-6 items-start">
                                    <div class="md:col-span-2 space-y-1">
                                        <label class="text-sm font-semibold text-slate-900">{{ $t('App Credentials')
                                        }}</label>
                                        <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                                <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                            </svg>
                                            <span>{{ $t('Your Pusher application ID and key') }}</span>
                                        </div>
                                    </div>
                                    <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <FormInput v-model="form.pusher_app_id" :name="$t('Pusher app id')"
                                            :type="'text'" :error="form.errors.pusher_app_id" />
                                        <FormInput v-model="form.pusher_app_key" :name="$t('Pusher app key')"
                                            :type="'text'" :error="form.errors.pusher_app_key" />
                                    </div>
                                </div>

                                <div class="border-t border-[#ff5100]/10"></div>

                                <!-- Pusher Secret & Cluster -->
                                <div class="grid md:grid-cols-5 gap-6 items-start">
                                    <div class="md:col-span-2 space-y-1">
                                        <label class="text-sm font-semibold text-slate-900">{{ $t('Security & Region')
                                        }}</label>
                                        <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                                <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                            </svg>
                                            <span>{{ $t('App secret and cluster location') }}</span>
                                        </div>
                                    </div>
                                    <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <FormInput v-model="form.pusher_app_secret" :name="$t('Pusher app secret')"
                                            :type="'password'" :error="form.errors.pusher_app_secret" />
                                        <FormInput v-model="form.pusher_app_cluster" :name="$t('Pusher app cluster')"
                                            :type="'text'" :error="form.errors.pusher_app_cluster" />
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-4 pt-4">
                    <button type="button"
                        class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition-all duration-200">
                        {{ $t('Cancel') }}
                    </button>
                    <button type="submit"
                        class="group px-8 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="isLoading">
                        <span v-if="isLoading" class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
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
                        <span v-else class="flex items-center gap-2">
                            {{ $t('Save Settings') }}
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="m9.55 18l-5.7-5.7l1.425-1.425L9.55 15.15l9.175-9.175L20.15 7.4z" />
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./Layout/App.vue";
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';

const props = defineProps({
    config: {
        type: Object,
        required: true
    }
});

const getValueByKey = (key) => {
    const found = props.config.find(item => item.key === key);
    return found ? found.value : '';
};

const isLoading = ref(false);
const form = useForm({
    broadcast_driver: getValueByKey('broadcast_driver'),
    pusher_app_key: getValueByKey('pusher_app_key'),
    pusher_app_id: getValueByKey('pusher_app_id'),
    pusher_app_secret: getValueByKey('pusher_app_secret'),
    pusher_app_cluster: getValueByKey('pusher_app_cluster'),
})

const methods = [
    { label: 'Pusher', value: 'pusher' },
]

const submitForm = async () => {
    form.put('/admin/settings?type=broadcast', {
        preserveScroll: true,
    });
};
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