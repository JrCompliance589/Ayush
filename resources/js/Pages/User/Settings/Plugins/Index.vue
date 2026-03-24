<!-- <template>
    <SettingLayout :modules="props.modules">
        <div class="md:h-[90vh]">
            <div class="flex justify-center items-center mb-8">
                <div class="md:w-[60em]">
                    <div class="bg-white border border-slate-200 rounded-lg py-2 text-sm mb-4 pb-2">
                        <div class="flex px-4 pt-2 pb-4">
                            <div>
                                <h2 class="text-[17px]">{{ $t('Plugins Section') }}</h2>
                                <span class="flex items-center mt-1">
                                    <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                        viewBox="0 0 24 24">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    {{ $t('Enable or disable plugins to enhance your business operations.') }}
                                </span>
                            </div>
                        </div>
                        <div class="max-w-7xl mx-auto">
                            <div class="overflow-hidden sm:rounded-lg">
                                <div class="p-6">
                                    <div v-if="plugins.length === 0" class="text-center py-12">
                                        <div class="mx-auto w-24 h-24 text-gray-300 mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $t('No Plugins Available')
                                            }}</h3>
                                        <p class="text-gray-500 max-w-sm mx-auto">
                                            {{ $t(`There are currently no plugins available for your account. Check back
                                            later for new integrations.`) }}
                                        </p>
                                    </div>

                                    <div v-else class="space-y-6">
                                        <div v-for="plugin in plugins" :key="plugin.uuid"
                                            class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                            <component :is="getPluginComponent(plugin.name)" :plugin="plugin" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SettingLayout>
</template>

<script setup>
import { defineAsyncComponent } from 'vue';
import SettingLayout from "./../Layout.vue";

const props = defineProps(['modules', 'plugins']);

const getPluginComponent = (pluginName) => {
    try {
        return defineAsyncComponent(() =>
            import(`../../../../../../modules/${pluginName}/Pages/User/Index.vue`)
        );
    } catch (error) {
        console.warn(`Plugin component not found for ${pluginName}`);
        return null;
    }
};
</script> -->


<!-- ============================================ NEW UI CODE ============================================= -->


<template>
    <SettingLayout :modules="props.modules">
        <div class="space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <div class="w-full mx-auto space-y-6">

                <!-- Page Header -->
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M22 13.5c0 1.76-1.3 3.22-3 3.46V20a2 2 0 0 1-2 2h-3.8v-.3a2.7 2.7 0 0 0-2.7-2.7c-1.5 0-2.7 1.21-2.7 2.7v.3H4a2 2 0 0 1-2-2v-3.8h.3C3.79 16.2 5 15 5 13.5s-1.21-2.7-2.7-2.7H2V7a2 2 0 0 1 2-2h3.04c.24-1.7 1.7-3 3.46-3s3.22 1.3 3.46 3H17a2 2 0 0 1 2 2v3.04c1.7.24 3 1.7 3 3.46M17 15h1.5a1.5 1.5 0 0 0 1.5-1.5a1.5 1.5 0 0 0-1.5-1.5H17V7h-5V5.5A1.5 1.5 0 0 0 10.5 4A1.5 1.5 0 0 0 9 5.5V7H4v2.12c1.76.68 3 2.38 3 4.38s-1.25 3.7-3 4.38V20h2.12a4.7 4.7 0 0 1 4.38-3c2 0 3.7 1.25 4.38 3H17z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-slate-900">{{ $t('Plugins Section') }}</h1>
                                <p class="text-sm text-slate-600 mt-0.5 flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    {{ $t('Enable or disable plugins to enhance your business operations.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="plugins.length === 0"
                    class="bg-white rounded-2xl border-2 border-dashed border-slate-300 overflow-hidden">
                    <div class="py-16 px-6 text-center">
                        <div class="flex justify-center mb-6">
                            <div class="p-6 bg-gradient-to-br from-slate-100 to-slate-200 rounded-2xl">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24"
                                    class="text-slate-400">
                                    <path fill="currentColor"
                                        d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">{{ $t('No Plugins Available') }}</h3>
                        <p class="text-slate-600 max-w-sm mx-auto mb-6">{{ $t(`There are currently no plugins available
                            for your account.Check back later for new integrations.`) }}</p>
                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                class="text-blue-600">
                                <path fill="currentColor"
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2m1 15h-2v-2h2v2m0-4h-2V7h2v6z" />
                            </svg>
                            <span class="text-sm text-blue-700 font-medium">{{ $t(`Tip: Check back regularly for new
                                plugin releases`) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Plugins Grid (when available) -->
                <div v-else class="space-y-4">
                    <div v-for="plugin in plugins" :key="plugin.uuid" class="group">
                        <component :is="getPluginComponent(plugin.name)" :plugin="plugin"
                            class="rounded-2xl border p-4 border-slate-200 overflow-hidden bg-white shadow-sm hover:shadow-lg transition-all duration-300" />
                    </div>
                </div>
            </div>
        </div>
    </SettingLayout>
</template>

<script setup>
import { ref, defineAsyncComponent, h } from 'vue';
import SettingLayout from "./../Layout.vue";

const props = defineProps(['modules', 'plugins']);

const plugins = ref(props.plugins || []);

const getPluginComponent = (pluginName) => {
    try {
        return defineAsyncComponent(() =>
            import(`../../../../../../modules/${pluginName}/Pages/User/Index.vue`)
        );
    } catch (error) {
        console.warn(`Plugin component not found for ${pluginName}`);

        // Return a fallback component
        return {
            template: `
                <div class="p-6 bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl border border-slate-200">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-yellow-100 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="text-yellow-600">
                                <path fill="currentColor" d="M1 21h22L12 2L1 21m12-3h-2v-2h2v2m0-4h-2v-4h2v4z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Plugin Component Not Found</h4>
                            <p class="text-sm text-slate-600 mt-1">The plugin component for "${pluginName}" could not be loaded. Please ensure the plugin module is properly installed.</p>
                        </div>
                    </div>
                </div>
            `
        };
    }
};
</script>

<style scoped>
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.group {
    animation: slideIn 0.3s ease-out forwards;
}

.group:nth-child(1) {
    animation-delay: 0.05s;
}

.group:nth-child(2) {
    animation-delay: 0.1s;
}

.group:nth-child(3) {
    animation-delay: 0.15s;
}

.group:nth-child(4) {
    animation-delay: 0.2s;
}

.group:nth-child(5) {
    animation-delay: 0.25s;
}
</style>