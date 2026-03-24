<script setup>
import AppLayout from '@/Pages/User/Layout/App.vue';
import { ShieldX, ArrowLeft, Home } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    title: String,
    message: String,
});
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center p-4">
            <div class="max-w-lg w-full">
                <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 p-10 text-center">
                    <!-- Icon -->
                    <div class="mb-8 relative inline-block">
                        <div class="absolute inset-0 bg-red-500/20 rounded-full blur-2xl animate-pulse"></div>
                        <div class="relative bg-gradient-to-br from-red-100 to-red-50 p-6 rounded-[2rem] inline-block">
                            <ShieldX class="w-16 h-16 text-red-500" />
                        </div>
                    </div>

                    <!-- Title -->
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">
                        {{ $t('Access Denied') }}
                    </h1>

                    <!-- Message -->
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        {{ message || $t('You do not have permission to access this feature. Please contact your administrator to request access.') }}
                    </p>

                    <!-- Error Code -->
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 rounded-full text-sm font-medium mb-8">
                        <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                        {{ $t('Error Code') }}: 403
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <button @click="$inertia.visit(document.referrer || '/dashboard')" 
                            class="flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all duration-300">
                            <ArrowLeft class="w-5 h-5" />
                            {{ $t('Go Back') }}
                        </button>
                        <Link href="/dashboard" 
                            class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary to-orange-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                            <Home class="w-5 h-5" />
                            {{ $t('Go to Dashboard') }}
                        </Link>
                    </div>
                </div>

                <!-- Help Text -->
                <p class="text-center text-gray-500 text-sm mt-6">
                    {{ $t('If you believe this is an error, please contact your team administrator.') }}
                </p>
            </div>
        </div>
    </AppLayout>
</template>
