<template>
    <AppLayout>
        <div class="bg-white md:bg-inherit pt-10 px-4 md:pt-8 md:p-8 rounded-[5px] text-[#000] h-full md:overflow-y-auto">
            <!-- Header -->
            <div class="flex justify-between items-start mb-8">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 text-white rounded-xl bg-gradient-to-br from-[#ff5100] to-[#ff7a3d] flex items-center justify-center shadow-lg shadow-[#ff5100]/25">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="2" y1="12" x2="22" y2="12"/>
                                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-700 bg-clip-text text-transparent">
                                {{ props.user_id ? $t('User Pricing') : $t('Country Pricing') }}
                            </h2>
                            <p v-if="props.user_name" class="text-sm font-semibold text-[#ff5100] mt-1">
                                {{ props.user_name }}
                            </p>
                        </div>
                    </div>
                    <p class="text-sm md:text-base font-medium leading-relaxed text-gray-600">
                        {{ props.user_id
                            ? $t('Manage custom message pricing for this user. Custom prices override global defaults.')
                            : $t('Manage default message pricing per country code. These are used when no user-specific pricing exists.') }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a v-if="props.user_id" href="/admin/country-pricing"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-4 md:px-6 py-2.5 rounded-xl transition-all duration-300 flex items-center gap-2">
                        {{ $t('Global Pricing') }}
                    </a>
                    <button @click="openAddModal"
                        class="bg-primary hover:bg-primary/90 text-white font-semibold px-4 md:px-6 py-2.5 rounded-xl transition-all duration-300 hover:scale-[1.02] shadow-lg flex items-center gap-2">
                        {{ $t('Add Country') }}
                    </button>
                </div>
            </div>

            <!-- Search -->
            <div class="relative group max-w-screen-sm mb-6">
                <div class="relative bg-white flex items-center shadow-lg h-14 rounded-2xl transition-all duration-300 border-2 border-orange-100">
                    <span class="pl-5 text-[#ff5100]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 15l6 6m-11-4a7 7 0 1 1 0-14a7 7 0 0 1 0 14Z" />
                        </svg>
                    </span>
                    <input v-model="searchQuery" @input="fetchPricing" type="text"
                        class="outline-none px-4 w-full bg-transparent text-gray-700 placeholder-gray-400 font-medium"
                        :placeholder="$t('Search by country name or code...')">
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl shadow-lg border border-orange-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-orange-50 to-orange-100/50 border-b-2 border-orange-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ $t('Country') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ $t('Code') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ $t('Marketing') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ $t('Utility') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ $t('Authentication') }}</th>
                                <th v-if="props.user_id" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ $t('Type') }}</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">{{ $t('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-orange-100">
                            <tr v-for="item in pricingList" :key="item.country_code" class="hover:bg-orange-50/30 transition-all duration-200">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900">{{ item.country_name }}</span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                        +{{ item.country_code }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-orange-50 text-[#ff5100] border border-orange-100">
                                        Rs.{{ item.marketing_price }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        Rs.{{ item.utility_price }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100">
                                        Rs.{{ item.auth_price }}
                                    </span>
                                </td>
                                <td v-if="props.user_id" class="px-6 py-5 whitespace-nowrap">
                                    <span v-if="item.is_custom"
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Custom
                                    </span>
                                    <span v-else
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-50 text-gray-500 border border-gray-200">
                                        Global
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openEditModal(item)"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-100 transition-colors">
                                            {{ $t('Edit') }}
                                        </button>
                                        <button v-if="props.user_id && item.is_custom" @click="confirmReset(item)"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100 hover:bg-amber-100 transition-colors">
                                            {{ $t('Reset') }}
                                        </button>
                                        <button v-if="!props.user_id" @click="confirmDelete(item)"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-100 hover:bg-red-100 transition-colors">
                                            {{ $t('Delete') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="pricingList.length === 0">
                                <td :colspan="props.user_id ? 7 : 6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" class="text-gray-400">
                                                <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
                                                <line x1="2" y1="12" x2="22" y2="12" stroke="currentColor" stroke-width="2"/>
                                                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" fill="none" stroke="currentColor" stroke-width="2"/>
                                            </svg>
                                        </div>
                                        <div class="text-center">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $t('No country pricing found') }}</h3>
                                            <p class="text-sm text-gray-500">{{ $t('Add country pricing to manage per-country message rates') }}</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div v-if="isModalOpen" @click.self="closeModal"
            class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all animate-in fade-in zoom-in duration-300">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900">{{ isEditing ? $t('Edit Country Pricing') : $t('Add Country Pricing') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $t('Set message pricing for this country code') }}</p>
                </div>
                <div class="p-6 space-y-4">
                    <div v-if="!isEditing">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('Country Code') }}</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">+</span>
                            <input v-model="form.country_code" type="text" :placeholder="$t('91')"
                                class="w-full pl-9 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#ff5100] focus:ring-4 focus:ring-orange-50 transition-all outline-none" />
                        </div>
                    </div>
                    <div v-else>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('Country Code') }}</label>
                        <div class="px-4 py-3 bg-gray-50 rounded-xl text-gray-600 font-semibold">+{{ form.country_code }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('Country Name') }}</label>
                        <input v-model="form.country_name" type="text" :placeholder="$t('India')"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#ff5100] focus:ring-4 focus:ring-orange-50 transition-all outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('Marketing Price') }}</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">Rs.</span>
                            <input v-model="form.marketing_price" type="number" step="0.0001" :placeholder="$t('0.00')"
                                class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#ff5100] focus:ring-4 focus:ring-orange-50 transition-all outline-none" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('Utility Price') }}</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">Rs.</span>
                            <input v-model="form.utility_price" type="number" step="0.0001" :placeholder="$t('0.00')"
                                class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#ff5100] focus:ring-4 focus:ring-orange-50 transition-all outline-none" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('Auth Price') }}</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">Rs.</span>
                            <input v-model="form.auth_price" type="number" step="0.0001" :placeholder="$t('0.00')"
                                class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#ff5100] focus:ring-4 focus:ring-orange-50 transition-all outline-none" />
                        </div>
                    </div>
                    <p v-if="errorMessage" class="text-red-600 text-sm">{{ errorMessage }}</p>
                </div>
                <div class="p-6 border-t border-gray-100 flex justify-end space-x-3">
                    <button @click="closeModal"
                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                        {{ $t('Cancel') }}
                    </button>
                    <button @click="submitForm"
                        class="px-5 py-2.5 bg-primary text-white font-medium rounded-xl transition-all hover:shadow-lg">
                        {{ isEditing ? $t('Save Changes') : $t('Add Country') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="isDeleteModalOpen" @click.self="isDeleteModalOpen = false"
            class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm transform transition-all animate-in fade-in zoom-in duration-300">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $t('Delete Country Pricing') }}</h2>
                    <p class="text-sm text-gray-500">{{ $t('Are you sure you want to delete pricing for') }} <strong>{{ deleteTarget?.country_name }}</strong> (+{{ deleteTarget?.country_code }})?</p>
                </div>
                <div class="p-6 border-t border-gray-100 flex justify-end space-x-3">
                    <button @click="isDeleteModalOpen = false"
                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                        {{ $t('Cancel') }}
                    </button>
                    <button @click="deletePricing"
                        class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-all hover:shadow-lg">
                        {{ $t('Delete') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Reset Confirmation Modal -->
        <div v-if="isResetModalOpen" @click.self="isResetModalOpen = false"
            class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm transform transition-all animate-in fade-in zoom-in duration-300">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $t('Reset to Global Price') }}</h2>
                    <p class="text-sm text-gray-500">{{ $t('Reset custom pricing for') }} <strong>{{ resetTarget?.country_name }}</strong> (+{{ resetTarget?.country_code }}) {{ $t('back to global default?') }}</p>
                </div>
                <div class="p-6 border-t border-gray-100 flex justify-end space-x-3">
                    <button @click="isResetModalOpen = false"
                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                        {{ $t('Cancel') }}
                    </button>
                    <button @click="resetPricing"
                        class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-xl transition-all hover:shadow-lg">
                        {{ $t('Reset') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div v-if="isSuccessModalOpen" @click.self="isSuccessModalOpen = false"
            class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm transform transition-all animate-in fade-in zoom-in duration-300 p-8 text-center">
                <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" class="text-emerald-600">
                        <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $t('Success') }}</h3>
                <p class="text-sm text-gray-500 mb-6">{{ successMessage }}</p>
                <button @click="isSuccessModalOpen = false"
                    class="px-6 py-2.5 bg-primary text-white font-medium rounded-xl transition-all hover:shadow-lg">
                    {{ $t('OK') }}
                </button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from "./../Layout/App.vue";

const props = defineProps({
    title: String,
    user_id: [Number, null],
    user_name: [String, null],
});

const pricingList = ref([]);
const searchQuery = ref('');
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const isDeleteModalOpen = ref(false);
const deleteTarget = ref(null);
const isResetModalOpen = ref(false);
const resetTarget = ref(null);
const isSuccessModalOpen = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const form = ref({
    country_code: '',
    country_name: '',
    marketing_price: 0,
    utility_price: 0,
    auth_price: 0,
});

const fetchPricing = async () => {
    try {
        const params = { search: searchQuery.value || undefined };
        if (props.user_id) params.user_id = props.user_id;
        const response = await axios.get('/api/country-pricing', { params });
        pricingList.value = response.data.data;
    } catch (error) {
        console.error('Failed to fetch country pricing:', error);
    }
};

const openAddModal = () => {
    isEditing.value = false;
    editingId.value = null;
    form.value = { country_code: '', country_name: '', marketing_price: 0, utility_price: 0, auth_price: 0 };
    errorMessage.value = '';
    isModalOpen.value = true;
};

const openEditModal = (item) => {
    isEditing.value = true;
    editingId.value = item.id;
    form.value = {
        country_code: item.country_code,
        country_name: item.country_name,
        marketing_price: item.marketing_price,
        utility_price: item.utility_price,
        auth_price: item.auth_price,
    };
    errorMessage.value = '';
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    errorMessage.value = '';
};

const submitForm = async () => {
    errorMessage.value = '';
    try {
        if (props.user_id) {
            // Per-user pricing: always use saveUserPricing endpoint
            await axios.post('/api/country-pricing/user-pricing', {
                user_id: props.user_id,
                country_code: form.value.country_code,
                country_name: form.value.country_name,
                marketing_price: form.value.marketing_price,
                utility_price: form.value.utility_price,
                auth_price: form.value.auth_price,
            });
            successMessage.value = 'User pricing saved successfully';
        } else if (isEditing.value) {
            await axios.put(`/api/country-pricing/${editingId.value}`, {
                country_name: form.value.country_name,
                marketing_price: form.value.marketing_price,
                utility_price: form.value.utility_price,
                auth_price: form.value.auth_price,
            });
            successMessage.value = 'Country pricing updated successfully';
        } else {
            await axios.post('/api/country-pricing', form.value);
            successMessage.value = 'Country pricing added successfully';
        }
        closeModal();
        isSuccessModalOpen.value = true;
        fetchPricing();
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Failed to save country pricing';
    }
};

const confirmDelete = (item) => {
    deleteTarget.value = item;
    isDeleteModalOpen.value = true;
};

const deletePricing = async () => {
    try {
        await axios.delete(`/api/country-pricing/${deleteTarget.value.id}`);
        isDeleteModalOpen.value = false;
        successMessage.value = 'Country pricing deleted successfully';
        isSuccessModalOpen.value = true;
        fetchPricing();
    } catch (error) {
        console.error('Failed to delete:', error);
    }
};

const confirmReset = (item) => {
    resetTarget.value = item;
    isResetModalOpen.value = true;
};

const resetPricing = async () => {
    try {
        await axios.post('/api/country-pricing/reset-user-pricing', {
            user_id: props.user_id,
            country_code: resetTarget.value.country_code,
        });
        isResetModalOpen.value = false;
        successMessage.value = 'Pricing reset to global default';
        isSuccessModalOpen.value = true;
        fetchPricing();
    } catch (error) {
        console.error('Failed to reset:', error);
    }
};

onMounted(() => {
    fetchPricing();
});
</script>
