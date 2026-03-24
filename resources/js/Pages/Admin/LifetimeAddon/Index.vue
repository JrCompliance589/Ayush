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
                                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                                    {{ $t('Lifetime Addons') }}
                                </h1>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $t('Manage purchasable addons for lifetime subscription users') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Create Button -->
                    <Link href="/admin/lifetime-addons/create"
                        class="inline-flex items-center justify-center space-x-2 px-5 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-2xl font-medium transition-all duration-200 shadow-lg shadow-green-200 hover:shadow-xl group">
                        <Plus class="w-5 h-5" />
                        <span>{{ $t('Create Addon') }}</span>
                    </Link>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="mb-6">
                <div class="relative">
                    <input
                        v-model="searchQuery"
                        @input="debounceSearch"
                        type="text"
                        :placeholder="$t('Search addons...')"
                        class="w-full px-4 py-3 pl-12 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-all duration-200"
                    />
                    <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                </div>
            </div>

            <!-- Addons Grid -->
            <div v-if="rows?.data?.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="addon in rows.data" :key="addon.id"
                    class="bg-white rounded-3xl shadow-md border-2 border-green-100 overflow-hidden transition-all duration-300 hover:shadow-lg hover:border-green-200">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div :class="[
                                    'w-12 h-12 rounded-xl flex items-center justify-center',
                                    addon.type === 'campaign_limit' 
                                        ? 'bg-blue-100 text-blue-600' 
                                        : 'bg-purple-100 text-purple-600'
                                ]">
                                    <svg v-if="addon.type === 'campaign_limit'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05c1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900">{{ addon.name }}</h3>
                                    <span :class="[
                                        'text-xs font-medium px-2 py-0.5 rounded-full',
                                        addon.status === 'active' 
                                            ? 'bg-green-100 text-green-700' 
                                            : 'bg-gray-100 text-gray-600'
                                    ]">
                                        {{ addon.status === 'active' ? $t('Active') : $t('Inactive') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <p v-if="addon.description" class="text-sm text-gray-600 mb-4">{{ addon.description }}</p>

                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">{{ $t('Type') }}:</span>
                                <span class="font-medium text-gray-900">
                                    {{ addon.type === 'campaign_limit' ? $t('Daily Campaigns') : $t('Daily Contacts') }}
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">{{ $t('Quantity') }}:</span>
                                <span class="font-medium text-gray-900">+{{ addon.quantity }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">{{ $t('Price') }}:</span>
                                <span class="font-bold text-green-600">₹{{ addon.price }}</span>
                            </div>
                        </div>

                        <div class="flex space-x-2">
                            <Link :href="`/admin/lifetime-addons/${addon.uuid}`"
                                class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-center rounded-xl font-medium transition-all duration-200">
                                {{ $t('Edit') }}
                            </Link>
                            <button @click="confirmDelete(addon)"
                                class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl font-medium transition-all duration-200">
                                <Trash2 class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-16">
                <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <Package class="w-10 h-10 text-gray-400" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $t('No addons found') }}</h3>
                <p class="text-gray-500 mb-6">{{ $t('Create your first addon to get started') }}</p>
                <Link href="/admin/lifetime-addons/create"
                    class="inline-flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-2xl font-medium">
                    <Plus class="w-5 h-5" />
                    <span>{{ $t('Create Addon') }}</span>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="rows?.data?.length > 0" class="mt-8 flex justify-center">
                <nav class="flex space-x-2">
                    <Link v-for="link in rows.links" :key="link.label"
                        :href="link.url || '#'"
                        :class="[
                            'px-4 py-2 rounded-xl font-medium transition-all duration-200',
                            link.active 
                                ? 'bg-green-500 text-white' 
                                : 'bg-gray-100 hover:bg-gray-200 text-gray-700',
                            !link.url && 'opacity-50 cursor-not-allowed'
                        ]"
                        v-html="link.label"
                    />
                </nav>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-3xl p-8 max-w-md w-full mx-4">
                <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $t('Delete Addon') }}</h3>
                <p class="text-gray-600 mb-6">
                    {{ $t('Are you sure you want to delete') }} <strong>{{ addonToDelete?.name }}</strong>? {{ $t('This action cannot be undone.') }}
                </p>
                <div class="flex space-x-3">
                    <button @click="showDeleteModal = false"
                        class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-all duration-200">
                        {{ $t('Cancel') }}
                    </button>
                    <button @click="deleteAddon"
                        class="flex-1 px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-medium transition-all duration-200">
                        {{ $t('Delete') }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import { ref } from 'vue';
import { Link, router } from "@inertiajs/vue3";
import { Plus, Search, Trash2, Package } from "lucide-vue-next";

const props = defineProps(['title', 'rows', 'filters', 'allowCreate']);

const searchQuery = ref(props.filters?.search || '');
const showDeleteModal = ref(false);
const addonToDelete = ref(null);

let searchTimeout = null;

const debounceSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/admin/lifetime-addons', { search: searchQuery.value }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
};

const confirmDelete = (addon) => {
    addonToDelete.value = addon;
    showDeleteModal.value = true;
};

const deleteAddon = () => {
    if (addonToDelete.value) {
        router.delete(`/admin/lifetime-addons/${addonToDelete.value.uuid}`, {
            onSuccess: () => {
                showDeleteModal.value = false;
                addonToDelete.value = null;
            }
        });
    }
};
</script>
