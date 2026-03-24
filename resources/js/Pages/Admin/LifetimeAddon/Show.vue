<template>
    <AppLayout>
        <div class="min-h-screen bg-white p-4 sm:p-6 lg:p-8">
            <!-- Header Section -->
            <div class="w-full mx-auto mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <!-- Title Section -->
                    <div class="space-y-2">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-12 h-12 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg shadow-green-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                                </svg>
                            </div>
                            <div>
                                <h1 v-if="props.addon === null" class="text-2xl sm:text-3xl font-bold text-gray-900">
                                    {{ $t('Create Addon') }}
                                </h1>
                                <h1 v-else class="text-2xl sm:text-3xl font-bold text-gray-900">
                                    {{ $t('Update Addon') }}
                                </h1>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $t('Configure addon for lifetime subscription users') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Back Button -->
                    <Link href="/admin/lifetime-addons"
                        class="inline-flex items-center justify-center space-x-2 px-5 py-3 bg-white hover:bg-gray-50 text-gray-700 rounded-2xl font-medium transition-all duration-200 border-2 border-gray-200 shadow-sm hover:shadow-md group">
                        <ArrowLeft class="w-4 h-4 transition-transform group-hover:-translate-x-1" />
                        <span>{{ $t('Back') }}</span>
                    </Link>
                </div>
            </div>

            <!-- Main Form -->
            <div class="w-full max-w-3xl mx-auto">
                <form @submit.prevent="submitForm()" class="space-y-6">
                    <!-- Basic Information Card -->
                    <div
                        class="bg-white rounded-3xl shadow-md border-2 border-green-100 transition-all duration-300 hover:shadow-lg">
                        <div
                            class="bg-gradient-to-r from-green-50 to-green-100/50 px-6 py-4 border-b-2 border-green-100">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="mr-2 text-green-600">
                                    <path fill="currentColor"
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3s-3-1.34-3-3s1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22c.03-1.99 4-3.08 6-3.08c1.99 0 5.97 1.09 6 3.08c-1.29 1.94-3.5 3.22-6 3.22z" />
                                </svg>
                                {{ $t('Addon Information') }}
                            </h2>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- Name -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">{{ $t('Name') }} *</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    :placeholder="$t('e.g., Extra 50 Daily Campaigns')"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-all duration-200"
                                    :class="{'border-red-500': form.errors.name}"
                                />
                                <p v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</p>
                            </div>

                            <!-- Description -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">{{ $t('Description') }}</label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    :placeholder="$t('Describe what this addon offers...')"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-all duration-200"
                                ></textarea>
                            </div>

                            <!-- Type -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">{{ $t('Addon Type') }} *</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <button
                                        type="button"
                                        @click="form.type = 'campaign_limit'"
                                        :class="[
                                            'p-4 border-2 rounded-xl text-left transition-all duration-200',
                                            form.type === 'campaign_limit'
                                                ? 'border-blue-500 bg-blue-50'
                                                : 'border-gray-200 hover:border-gray-300'
                                        ]"
                                    >
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="text-blue-600">
                                                    <path fill="currentColor" d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $t('Daily Campaigns') }}</p>
                                                <p class="text-xs text-gray-500">{{ $t('Increase campaign limit per day') }}</p>
                                            </div>
                                        </div>
                                    </button>
                                    <button
                                        type="button"
                                        @click="form.type = 'contacts_limit'"
                                        :class="[
                                            'p-4 border-2 rounded-xl text-left transition-all duration-200',
                                            form.type === 'contacts_limit'
                                                ? 'border-purple-500 bg-purple-50'
                                                : 'border-gray-200 hover:border-gray-300'
                                        ]"
                                    >
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="text-purple-600">
                                                    <path fill="currentColor" d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $t('Daily Contacts') }}</p>
                                                <p class="text-xs text-gray-500">{{ $t('Increase contacts limit per day') }}</p>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                                <p v-if="form.errors.type" class="text-red-500 text-sm">{{ form.errors.type }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Card -->
                    <div
                        class="bg-white rounded-3xl shadow-md border-2 border-green-100 transition-all duration-300 hover:shadow-lg">
                        <div
                            class="bg-gradient-to-r from-green-50 to-green-100/50 px-6 py-4 border-b-2 border-green-100">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="mr-2 text-green-600">
                                    <path fill="currentColor"
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87c1.96 0 2.4-.98 2.4-1.59c0-.83-.44-1.61-2.67-2.14c-2.48-.6-4.18-1.62-4.18-3.67c0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87c-1.5 0-2.4.68-2.4 1.64c0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z" />
                                </svg>
                                {{ $t('Pricing & Quantity') }}
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Quantity -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">{{ $t('Quantity') }} *</label>
                                    <input
                                        v-model="form.quantity"
                                        type="number"
                                        min="1"
                                        :placeholder="$t('e.g., 50')"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-all duration-200"
                                        :class="{'border-red-500': form.errors.quantity}"
                                    />
                                    <p class="text-xs text-gray-500">
                                        {{ form.type === 'campaign_limit' 
                                            ? $t('Number of extra daily campaigns') 
                                            : $t('Number of extra daily contacts') 
                                        }}
                                    </p>
                                    <p v-if="form.errors.quantity" class="text-red-500 text-sm">{{ form.errors.quantity }}</p>
                                </div>

                                <!-- Price -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">{{ $t('Price (₹)') }} *</label>
                                    <input
                                        v-model="form.price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        :placeholder="$t('e.g., 499')"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-all duration-200"
                                        :class="{'border-red-500': form.errors.price}"
                                    />
                                    <p class="text-xs text-gray-500">{{ $t('Price for this addon package') }}</p>
                                    <p v-if="form.errors.price" class="text-red-500 text-sm">{{ form.errors.price }}</p>
                                </div>
                            </div>

                            <!-- Preview -->
                            <div v-if="form.quantity && form.price" class="mt-6 p-4 bg-green-50 border-2 border-green-200 rounded-2xl">
                                <p class="text-sm text-gray-700">
                                    <strong>{{ $t('Preview') }}:</strong> 
                                    {{ $t('Users will pay') }} <span class="font-bold text-green-600">₹{{ form.price }}</span> 
                                    {{ $t('to get') }} <span class="font-bold">+{{ form.quantity }}</span> 
                                    {{ form.type === 'campaign_limit' ? $t('daily campaigns') : $t('daily contacts') }}.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Status Card -->
                    <div
                        class="bg-white rounded-3xl shadow-md border-2 border-green-100 transition-all duration-300 hover:shadow-lg">
                        <div
                            class="bg-gradient-to-r from-green-50 to-green-100/50 px-6 py-4 border-b-2 border-green-100">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="mr-2 text-green-600">
                                    <path fill="currentColor"
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                </svg>
                                {{ $t('Status') }}
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $t('Addon Status') }}</p>
                                    <p class="text-sm text-gray-500">{{ $t('Active addons are visible to users for purchase') }}</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span :class="form.status === 'active' ? 'text-green-600' : 'text-gray-500'">
                                        {{ form.status === 'active' ? $t('Active') : $t('Inactive') }}
                                    </span>
                                    <button
                                        type="button"
                                        @click="form.status = form.status === 'active' ? 'inactive' : 'active'"
                                        :class="[
                                            'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2',
                                            form.status === 'active' ? 'bg-green-500' : 'bg-gray-200'
                                        ]"
                                    >
                                        <span
                                            :class="[
                                                'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                                form.status === 'active' ? 'translate-x-5' : 'translate-x-0'
                                            ]"
                                        />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-4">
                        <Link href="/admin/lifetime-addons"
                            class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl font-medium transition-all duration-200">
                            {{ $t('Cancel') }}
                        </Link>
                        <button type="submit"
                            :disabled="form.processing"
                            class="px-8 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-2xl transition-all duration-200 shadow-lg shadow-green-200 hover:shadow-xl hover:shadow-green-300 flex items-center space-x-2 disabled:opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3s3 1.34 3 3s-1.34 3-3 3zm3-10H5V5h10v4z" />
                            </svg>
                            <span>{{ props.addon ? $t('Update Addon') : $t('Create Addon') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { ArrowLeft } from "lucide-vue-next";

const props = defineProps(['title', 'addon']);

const form = useForm({
    name: props.addon?.name || '',
    description: props.addon?.description || '',
    type: props.addon?.type || 'campaign_limit',
    quantity: props.addon?.quantity || '',
    price: props.addon?.price || '',
    status: props.addon?.status || 'active',
});

const submitForm = () => {
    if (props.addon) {
        form.put(`/admin/lifetime-addons/${props.addon.uuid}`, {
            preserveScroll: true,
        });
    } else {
        form.post('/admin/lifetime-addons', {
            preserveScroll: true,
        });
    }
};
</script>
