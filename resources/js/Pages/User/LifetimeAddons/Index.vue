<template>
    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-4 sm:p-6 lg:p-8">
            <!-- Header Section -->
            <div class="w-full mx-auto mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="space-y-2">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-12 h-12 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg shadow-green-200">
                                <Zap class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                                    {{ $t('Lifetime Addons') }}
                                </h1>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ hasLifetime ? $t('Increase your daily limits by purchasing addons') : $t('Addons for lifetime subscribers') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Non-Lifetime User Message -->
            <div v-if="!hasLifetime" class="mb-8">
                <div class="bg-white rounded-3xl shadow-lg border-2 border-amber-100 overflow-hidden">
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 mx-auto mb-6 bg-amber-100 rounded-full flex items-center justify-center">
                            <AlertCircle class="w-10 h-10 text-amber-600" />
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $t('Lifetime Subscription Required') }}</h2>
                        <p class="text-gray-600 max-w-md mx-auto mb-6">
                            {{ $t('Addons are only available for users with a lifetime subscription plan. Upgrade your subscription to access addon purchases.') }}
                        </p>
                        <a href="/billing" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                            <CreditCard class="w-5 h-5 mr-2" />
                            {{ $t('View Subscription Plans') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Lifetime User Content -->
            <template v-else>
                <!-- Usage Summary Card -->
                <div v-if="usageSummary" class="mb-8">
                <div class="bg-white rounded-3xl shadow-lg border-2 border-green-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-50 to-green-100/50 px-6 py-4 border-b-2 border-green-100">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center">
                            <BarChart3 class="w-5 h-5 mr-2 text-green-600" />
                            {{ $t("Today's Usage") }}
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Campaigns Used -->
                            <div class="bg-blue-50 rounded-2xl p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-blue-600">{{ $t('Campaigns Today') }}</span>
                                    <Send class="w-5 h-5 text-blue-500" />
                                </div>
                                <div class="text-2xl font-bold text-gray-900">
                                    {{ usageSummary.campaigns_created_today }}
                                    <span v-if="usageSummary.daily_campaign_limit !== -1" class="text-sm font-normal text-gray-500">
                                        / {{ usageSummary.daily_campaign_limit }}
                                    </span>
                                    <span v-else class="text-sm font-normal text-green-500">{{ $t('Unlimited') }}</span>
                                </div>
                                <div v-if="usageSummary.daily_campaign_limit !== -1" class="mt-2">
                                    <div class="w-full bg-blue-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" 
                                            :style="{ width: `${Math.min(100, (usageSummary.campaigns_created_today / usageSummary.daily_campaign_limit) * 100)}%` }"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contacts Used -->
                            <div class="bg-purple-50 rounded-2xl p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-purple-600">{{ $t('Contacts Today') }}</span>
                                    <Users class="w-5 h-5 text-purple-500" />
                                </div>
                                <div class="text-2xl font-bold text-gray-900">
                                    {{ usageSummary.contacts_used_today }}
                                    <span v-if="usageSummary.daily_contacts_limit !== -1" class="text-sm font-normal text-gray-500">
                                        / {{ usageSummary.daily_contacts_limit }}
                                    </span>
                                    <span v-else class="text-sm font-normal text-green-500">{{ $t('Unlimited') }}</span>
                                </div>
                                <div v-if="usageSummary.daily_contacts_limit !== -1" class="mt-2">
                                    <div class="w-full bg-purple-200 rounded-full h-2">
                                        <div class="bg-purple-600 h-2 rounded-full" 
                                            :style="{ width: `${Math.min(100, (usageSummary.contacts_used_today / usageSummary.daily_contacts_limit) * 100)}%` }"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Campaigns from Addons -->
                            <div class="bg-green-50 rounded-2xl p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-green-600">{{ $t('Bonus Campaigns') }}</span>
                                    <Plus class="w-5 h-5 text-green-500" />
                                </div>
                                <div class="text-2xl font-bold text-green-600">
                                    +{{ usageSummary.additional_campaigns || 0 }}
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $t('From purchased addons') }}</p>
                            </div>

                            <!-- Additional Contacts from Addons -->
                            <div class="bg-amber-50 rounded-2xl p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-amber-600">{{ $t('Bonus Contacts') }}</span>
                                    <Plus class="w-5 h-5 text-amber-500" />
                                </div>
                                <div class="text-2xl font-bold text-amber-600">
                                    +{{ usageSummary.additional_contacts || 0 }}
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $t('From purchased addons') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Addons -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <ShoppingBag class="w-6 h-6 mr-2 text-green-600" />
                    {{ $t('Available Addons') }}
                </h2>
                
                <div v-if="addons?.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="addon in addons" :key="addon.id"
                        class="bg-white rounded-3xl shadow-lg border-2 border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-xl hover:border-green-200">
                        <div :class="[
                            'p-6',
                            addon.type === 'campaign_limit' ? 'bg-gradient-to-br from-blue-50 to-white' : 'bg-gradient-to-br from-purple-50 to-white'
                        ]">
                            <div class="flex items-start justify-between mb-4">
                                <div :class="[
                                    'w-14 h-14 rounded-2xl flex items-center justify-center',
                                    addon.type === 'campaign_limit' ? 'bg-blue-100' : 'bg-purple-100'
                                ]">
                                    <Send v-if="addon.type === 'campaign_limit'" class="w-7 h-7 text-blue-600" />
                                    <Users v-else class="w-7 h-7 text-purple-600" />
                                </div>
                                <span :class="[
                                    'px-3 py-1 rounded-full text-xs font-medium',
                                    addon.type === 'campaign_limit' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700'
                                ]">
                                    {{ addon.type_label }}
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ addon.name }}</h3>
                            <p v-if="addon.description" class="text-sm text-gray-600 mb-4">{{ addon.description }}</p>

                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <span class="text-3xl font-bold text-gray-900">₹{{ addon.price }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-2xl font-bold text-green-600">+{{ addon.quantity }}</span>
                                    <p class="text-xs text-gray-500">{{ addon.type === 'campaign_limit' ? $t('campaigns/day') : $t('contacts/day') }}</p>
                                </div>
                            </div>

                            <!-- Quantity Selector -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('Quantity') }}</label>
                                <div class="flex items-center space-x-3">
                                    <button 
                                        @click="decrementQuantity(addon.id)"
                                        class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-all"
                                        :disabled="getQuantity(addon.id) <= 1"
                                    >
                                        <Minus class="w-5 h-5 text-gray-600" />
                                    </button>
                                    <span class="text-xl font-bold text-gray-900 w-12 text-center">{{ getQuantity(addon.id) }}</span>
                                    <button 
                                        @click="incrementQuantity(addon.id)"
                                        class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-all"
                                    >
                                        <Plus class="w-5 h-5 text-gray-600" />
                                    </button>
                                </div>
                                <p class="text-sm text-gray-500 mt-2">
                                    {{ $t('Total') }}: <span class="font-bold text-green-600">₹{{ addon.price * getQuantity(addon.id) }}</span>
                                    {{ $t('for') }} <span class="font-bold">+{{ addon.quantity * getQuantity(addon.id) }}</span> {{ addon.type === 'campaign_limit' ? $t('daily campaigns') : $t('daily contacts') }}
                                </p>
                            </div>

                            <button 
                                @click="purchaseAddon(addon)"
                                :disabled="purchasing"
                                class="w-full py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 flex items-center justify-center space-x-2"
                            >
                                <CreditCard class="w-5 h-5" />
                                <span>{{ $t('Purchase Now') }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-16 bg-white rounded-3xl shadow-lg">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                        <Package class="w-10 h-10 text-gray-400" />
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $t('No addons available') }}</h3>
                    <p class="text-gray-500">{{ $t('Check back later for new addon packages') }}</p>
                </div>
            </div>

            <!-- Purchased Addons -->
            <div v-if="purchasedAddons?.length > 0" class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <CheckCircle class="w-6 h-6 mr-2 text-green-600" />
                    {{ $t('Your Purchased Addons') }}
                </h2>
                
                <div class="bg-white rounded-3xl shadow-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Addon') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Type') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Quantity') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Price Paid') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="purchase in purchasedAddons" :key="purchase.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-medium text-gray-900">{{ purchase.addon?.name }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[
                                        'px-2 py-1 rounded-full text-xs font-medium',
                                        purchase.addon?.type === 'campaign_limit' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700'
                                    ]">
                                        {{ purchase.addon?.type === 'campaign_limit' ? $t('Campaigns') : $t('Contacts') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold text-green-600">+{{ purchase.quantity }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-medium text-gray-900">₹{{ purchase.price_paid }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ purchase.created_at }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            </template>
        </div>

        <!-- Payment Processing Modal -->
        <div v-if="purchasing" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-3xl p-8 max-w-md w-full mx-4 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center animate-pulse">
                    <CreditCard class="w-8 h-8 text-green-600" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $t('Processing Payment') }}</h3>
                <p class="text-gray-600">{{ $t('Please wait while we redirect you to payment...') }}</p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import { ref, reactive } from 'vue';
import { 
    Zap, BarChart3, Send, Users, Plus, Minus, ShoppingBag, 
    CreditCard, Package, CheckCircle, AlertCircle 
} from "lucide-vue-next";
import axios from 'axios';

const props = defineProps([
    'title', 
    'addons', 
    'purchasedAddons', 
    'usageSummary', 
    'razorpayKeyId', 
    'currency',
    'hasLifetime'
]);

const quantities = reactive({});
const purchasing = ref(false);

// Initialize quantities
props.addons?.forEach(addon => {
    quantities[addon.id] = 1;
});

const getQuantity = (addonId) => quantities[addonId] || 1;

const incrementQuantity = (addonId) => {
    quantities[addonId] = (quantities[addonId] || 1) + 1;
};

const decrementQuantity = (addonId) => {
    if (quantities[addonId] > 1) {
        quantities[addonId]--;
    }
};

const purchaseAddon = async (addon) => {
    purchasing.value = true;
    
    try {
        const response = await axios.post('/lifetime-addons/purchase', {
            addon_id: addon.id,
            quantity: getQuantity(addon.id),
        });

        if (response.data.success && response.data.payment_url) {
            window.location.href = response.data.payment_url;
        } else {
            alert(response.data.message || 'Failed to create payment');
            purchasing.value = false;
        }
    } catch (error) {
        console.error('Purchase error:', error);
        alert(error.response?.data?.message || 'An error occurred. Please try again.');
        purchasing.value = false;
    }
};
</script>
