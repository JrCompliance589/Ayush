<template>
    <AppLayout>
        <div class="p-4 md:p-8 max-w-6xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between flex-wrap gap-4 mb-8">
                <div>
                    <div class="flex items-center space-x-3 mb-1">
                        <div class="bg-gradient-to-br from-[#96bf48] to-[#5c8a17] rounded-xl p-2.5 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="text-white">
                                <path fill="currentColor" d="M15.34 3.27c-.07 0-.14.01-.2.04l-.81.32c-.48-1.38-1.33-2.65-2.81-2.65h-.13C11 .35 10.49.03 10.06.03c-3.28.04-4.86 4.1-5.35 6.19l-1.84.57c-.57.18-.59.2-.66.74l-1.29 9.94L12.63 20l6.72-1.47c-.01-.01-4-15.21-4.01-15.26zM11.33 4.5l-1.6.5c.31-1.17.9-2.35 1.95-2.78-.05.8-.2 1.54-.35 2.28zm-1.74.54l-3.46 1.07C6.6 4.17 7.82 2.07 9.13 1.58a3.61 3.61 0 0 0 .46 3.46zM9.76.93c.24 0 .45.09.62.26-1.56.73-2.24 2.57-2.52 4.1L5.87 6c.51-2.06 1.69-5.07 3.89-5.07z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">{{ $t('Shopify Integration') }}</h1>
                            <p class="text-sm text-gray-500">{{ $t('Connect your Shopify store and automate WhatsApp notifications') }}</p>
                        </div>
                    </div>
                </div>
                <button v-if="stores.length > 0" @click="showConnectModal = true"
                    class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-[#96bf48] to-[#5c8a17] text-white font-medium rounded-xl hover:scale-105 transition-transform duration-300 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" class="mr-2">
                        <path fill="currentColor" d="M11 13H5v-2h6V5h2v6h6v2h-6v6h-2v-6Z" />
                    </svg>
                    {{ $t('Add Store') }}
                </button>
            </div>

            <!-- Tab Navigation -->
            <div class="flex border-b border-gray-200 mb-6 overflow-x-auto" v-if="stores.length > 0">
                <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
                    :class="[
                        'px-4 py-3 text-sm font-medium whitespace-nowrap border-b-2 transition-colors',
                        activeTab === tab.id
                            ? 'border-[#96bf48] text-[#5c8a17]'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                    ]">
                    {{ $t(tab.label) }}
                </button>
            </div>

            <!-- No Store Connected - Setup Guide -->
            <div v-if="stores.length === 0" class="space-y-6">
                <!-- Setup Steps -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-2">{{ $t('Setup Guide') }}</h2>
                    <p class="text-sm text-gray-500 mb-6">{{ $t('Follow these steps to connect your Shopify store with WhatsApp notifications') }}</p>

                    <div class="space-y-4">
                        <div v-for="(step, index) in setupSteps" :key="index"
                            class="flex gap-4 p-4 rounded-xl border border-gray-100 hover:border-[#96bf48]/30 hover:bg-[#96bf48]/5 transition-all">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-[#96bf48] to-[#5c8a17] flex items-center justify-center text-white font-bold text-sm">
                                {{ index + 1 }}
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-sm">{{ step.title }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ step.description }}</p>
                                <div v-if="step.substeps" class="mt-2 space-y-1">
                                    <div v-for="(sub, si) in step.substeps" :key="si" class="flex items-start gap-2 text-sm text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" class="text-[#96bf48] flex-shrink-0 mt-0.5">
                                            <path fill="currentColor" d="m9 20.42l-6.21-6.21l2.83-2.83L9 14.77l9.88-9.89l2.83 2.83z" />
                                        </svg>
                                        <span>{{ sub }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-center">
                        <button @click="showConnectModal = true"
                            class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-[#96bf48] to-[#5c8a17] text-white font-semibold rounded-xl hover:scale-105 transition-transform duration-300 shadow-lg shadow-[#96bf48]/20">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="mr-2">
                                <path fill="currentColor" d="M11 13H5v-2h6V5h2v6h6v2h-6v6h-2v-6Z" />
                            </svg>
                            {{ $t('Connect Shopify Store') }}
                        </button>
                    </div>
                </div>

                <!-- Feature Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
                        <div class="bg-blue-50 rounded-xl p-3 w-fit mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="text-blue-600">
                                <path fill="currentColor" d="M21 8V7l-3 2l-3-2v1l3 2m1-7H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">{{ $t('Automated Notifications') }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $t('Order confirmations, shipping updates, delivery status & COD verification — all automatic via WhatsApp.') }}</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
                        <div class="bg-orange-50 rounded-xl p-3 w-fit mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="text-orange-600">
                                <path fill="currentColor" d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2s-.9-2-2-2M1 2v2h2l3.6 7.59l-1.35 2.45c-.16.28-.25.61-.25.96c0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12l.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0 0 20 4H5.21l-.94-2zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2s2-.9 2-2s-.9-2-2-2" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">{{ $t('Abandoned Cart Recovery') }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $t('Recover lost sales with a 3-step reminder sequence: 30 min, 6 hours, and 24 hours with discount offers.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Stores Tab -->
            <div v-if="stores.length > 0 && activeTab === 'stores'">
                <div class="space-y-4">
                    <div v-for="store in stores" :key="store.uuid"
                        class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[#96bf48]/10 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="text-[#5c8a17]">
                                        <path fill="currentColor" d="M15.34 3.27c-.07 0-.14.01-.2.04l-.81.32c-.48-1.38-1.33-2.65-2.81-2.65h-.13C11 .35 10.49.03 10.06.03c-3.28.04-4.86 4.1-5.35 6.19l-1.84.57c-.57.18-.59.2-.66.74l-1.29 9.94L12.63 20l6.72-1.47c-.01-.01-4-15.21-4.01-15.26z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ store.shop_domain }}</h3>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span :class="[
                                            'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                                            store.is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'
                                        ]">
                                            {{ store.is_active ? $t('Active') : $t('Inactive') }}
                                        </span>
                                        <span class="text-xs text-gray-400">{{ $t('Logs') }}: {{ store.notification_logs_count || 0 }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="editStore(store)" class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    {{ $t('Edit') }}
                                </button>
                                <button @click="configureStore(store)" class="px-3 py-1.5 text-sm bg-[#96bf48] text-white rounded-lg hover:bg-[#7da63a] transition-colors">
                                    {{ $t('Configure') }}
                                </button>
                                <button @click="confirmDeleteStore(store)" class="px-3 py-1.5 text-sm text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                                    {{ $t('Delete') }}
                                </button>
                            </div>
                        </div>

                        <!-- Webhook URL display -->
                        <div class="mt-4 bg-gray-50 rounded-lg p-3">
                            <label class="text-xs font-medium text-gray-500 mb-1 block">{{ $t('Webhook URL (paste this in Shopify)') }}</label>
                            <div class="flex items-center gap-2">
                                <code class="text-sm text-gray-700 bg-white px-3 py-1.5 rounded border border-gray-200 flex-1 overflow-x-auto">{{ getWebhookUrl(store.uuid) }}</code>
                                <button @click="copyToClipboard(getWebhookUrl(store.uuid))" class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex-shrink-0">
                                    {{ $t('Copy') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Tab -->
            <div v-if="stores.length > 0 && activeTab === 'notifications' && selectedStore">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">{{ $t('Notification Templates') }}</h2>
                            <p class="text-sm text-gray-500">{{ $t('Map WhatsApp templates to Shopify events for') }} {{ selectedStore.shop_domain }}</p>
                        </div>
                    </div>

                    <!-- Store Selector if multiple stores -->
                    <div v-if="stores.length > 1" class="mb-4">
                        <select v-model="selectedStoreUuid" @change="onStoreChange" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full max-w-xs">
                            <option v-for="s in stores" :key="s.uuid" :value="s.uuid">{{ s.shop_domain }}</option>
                        </select>
                    </div>

                    <div class="space-y-4">
                        <div v-for="eventType in eventTypes" :key="eventType"
                            class="border border-gray-100 rounded-xl p-4 hover:border-[#96bf48]/30 transition-all">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div :class="['w-8 h-8 rounded-lg flex items-center justify-center', eventIconClass(eventType)]">
                                        <component :is="eventIcon(eventType)" class="w-4 h-4" />
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900 text-sm">{{ formatEventType(eventType) }}</h3>
                                        <p class="text-xs text-gray-500">{{ eventDescription(eventType) }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <!-- Template Status Badge -->
                                    <span v-if="prebuiltTemplates[eventType] && getTemplateByName(prebuiltTemplates[eventType]?.name)"
                                        :class="['px-2 py-0.5 text-[10px] font-medium rounded-full border', getTemplateStatusClass(getTemplateByName(prebuiltTemplates[eventType]?.name)?.status)]">
                                        {{ getTemplateStatusIcon(getTemplateByName(prebuiltTemplates[eventType]?.name)?.status) }}
                                        {{ getTemplateByName(prebuiltTemplates[eventType]?.name)?.status }}
                                    </span>
                                    <!-- Quick Create from prebuilt -->
                                    <button v-if="prebuiltTemplates[eventType]"
                                        @click="openTemplateEditor(eventType)"
                                        class="px-3 py-1.5 text-xs rounded-lg transition-colors"
                                        :class="getTemplateByName(prebuiltTemplates[eventType]?.name)
                                            ? 'bg-gray-50 text-gray-600 border border-gray-200 hover:bg-gray-100'
                                            : 'bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100'">
                                        {{ getTemplateByName(prebuiltTemplates[eventType]?.name) ? '👁 View' : '⚡ Create Template' }}
                                    </button>
                                    <button @click="toggleNotification(eventType)"
                                        :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', notificationForm.templates[eventType]?.is_active ? 'bg-[#96bf48]' : 'bg-gray-300']">
                                        <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', notificationForm.templates[eventType]?.is_active ? 'translate-x-6' : 'translate-x-1']"></span>
                                    </button>
                                </div>
                            </div>

                            <!-- Attached template info -->
                            <div v-if="notificationForm.templates[eventType]?.template_id" class="flex items-center gap-2 mt-1 px-11">
                                <span class="text-[11px] text-gray-400">{{ $t('Attached:') }}</span>
                                <span class="text-[11px] font-mono text-gray-600 bg-gray-50 px-1.5 py-0.5 rounded">
                                    {{ templates.find(t => t.id === notificationForm.templates[eventType].template_id)?.name || 'Unknown' }}
                                </span>
                                <span v-if="templates.find(t => t.id === notificationForm.templates[eventType].template_id)"
                                    class="text-[10px] px-1.5 py-0.5 rounded-full bg-green-50 text-green-600 border border-green-200">
                                    APPROVED
                                </span>
                            </div>
                            <div v-else-if="!notificationForm.templates[eventType]?.is_active && prebuiltTemplates[eventType] && !getTemplateByName(prebuiltTemplates[eventType]?.name)" class="mt-1 px-11">
                                <span class="text-[11px] text-gray-400">{{ $t('No template attached. Create one using the button above.') }}</span>
                            </div>

                            <div v-if="notificationForm.templates[eventType]?.is_active" class="mt-3 space-y-3">
                                <div>
                                    <label class="text-xs font-medium text-gray-600 mb-1 block">{{ $t('WhatsApp Template') }}</label>
                                    <select v-model="notificationForm.templates[eventType].template_id"
                                        class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full">
                                        <option :value="null">{{ $t('Select a template...') }}</option>
                                        <option v-for="t in templates" :key="t.id" :value="t.id">
                                            {{ t.name }} ({{ t.language }})
                                        </option>
                                    </select>
                                </div>
                                <div v-if="templateVariables[eventType]" class="bg-gray-50 rounded-lg p-3">
                                    <label class="text-xs font-medium text-gray-500 mb-1 block">{{ $t('Available Variables') }}</label>
                                    <div class="flex flex-wrap gap-1.5">
                                        <span v-for="v in templateVariables[eventType]" :key="v"
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono bg-white border border-gray-200 text-gray-600 cursor-pointer hover:border-[#96bf48] hover:text-[#5c8a17] transition-colors"
                                            @click="copyToClipboard(v)">
                                            {{ v }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button @click="saveNotificationTemplates" :disabled="savingNotifications"
                            class="inline-flex items-center px-6 py-2.5 bg-[#96bf48] text-white font-medium rounded-xl hover:bg-[#7da63a] transition-colors text-sm disabled:opacity-50">
                            <svg v-if="savingNotifications" class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            {{ $t('Save Templates') }}
                        </button>
                    </div>
                </div>

                <!-- Prebuilt Cart Recovery Templates Section -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm mt-6">
                    <h3 class="text-base font-bold text-gray-900 mb-1">{{ $t('Cart Recovery Templates') }}</h3>
                    <p class="text-sm text-gray-500 mb-4">{{ $t('Create ready-made templates for abandoned cart reminders') }}</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div v-for="key in ['cart_reminder_1', 'cart_reminder_2', 'cart_reminder_3']" :key="key"
                            class="border border-gray-100 rounded-xl p-4 hover:border-blue-200 transition-all">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-sm text-gray-900">{{ prebuiltTemplates[key]?.name?.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}</h4>
                                <span v-if="getTemplateByName(prebuiltTemplates[key]?.name)"
                                    :class="['text-[10px] px-1.5 py-0.5 rounded-full border font-medium', getTemplateStatusClass(getTemplateByName(prebuiltTemplates[key]?.name)?.status)]">
                                    {{ getTemplateStatusIcon(getTemplateByName(prebuiltTemplates[key]?.name)?.status) }}
                                    {{ getTemplateByName(prebuiltTemplates[key]?.name)?.status }}
                                </span>
                                <span v-else class="text-[10px] px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-500">
                                    {{ $t('Not created') }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ prebuiltTemplates[key]?.body?.text?.substring(0, 80) }}...</p>
                            <button @click="openTemplateEditor(key)"
                                class="w-full px-3 py-1.5 text-xs rounded-lg transition-colors"
                                :class="getTemplateByName(prebuiltTemplates[key]?.name)
                                    ? 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                    : 'bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100'">
                                {{ getTemplateByName(prebuiltTemplates[key]?.name) ? '👁 View' : '⚡ Create & Submit' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Recovery Tab -->
            <div v-if="stores.length > 0 && activeTab === 'cart_recovery' && selectedStore">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">{{ $t('Abandoned Cart Recovery') }}</h2>
                            <p class="text-sm text-gray-500">{{ $t('Automatically recover abandoned carts with WhatsApp reminder sequences') }}</p>
                        </div>
                        <button @click="cartForm.is_active = !cartForm.is_active"
                            :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', cartForm.is_active ? 'bg-[#96bf48]' : 'bg-gray-300']">
                            <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', cartForm.is_active ? 'translate-x-6' : 'translate-x-1']"></span>
                        </button>
                    </div>

                    <!-- Store Selector -->
                    <div v-if="stores.length > 1" class="mb-4">
                        <select v-model="selectedStoreUuid" @change="onStoreChange" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full max-w-xs">
                            <option v-for="s in stores" :key="s.uuid" :value="s.uuid">{{ s.shop_domain }}</option>
                        </select>
                    </div>

                    <!-- Reminder Sequence Visual -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div v-for="(reminder, idx) in reminders" :key="idx"
                            :class="['border rounded-xl p-4 transition-all', cartForm['reminder_' + (idx+1) + '_enabled'] ? 'border-[#96bf48]/40 bg-[#96bf48]/5' : 'border-gray-200']">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-gradient-to-br text-white text-xs font-bold flex items-center justify-center"
                                        :class="reminder.gradient">{{ idx + 1 }}</span>
                                    <h4 class="font-medium text-sm text-gray-900">{{ $t(reminder.title) }}</h4>
                                </div>
                                <button @click="cartForm['reminder_' + (idx+1) + '_enabled'] = !cartForm['reminder_' + (idx+1) + '_enabled']"
                                    :class="['relative inline-flex h-5 w-9 items-center rounded-full transition-colors', cartForm['reminder_' + (idx+1) + '_enabled'] ? 'bg-[#96bf48]' : 'bg-gray-300']">
                                    <span :class="['inline-block h-3 w-3 transform rounded-full bg-white transition-transform', cartForm['reminder_' + (idx+1) + '_enabled'] ? 'translate-x-5' : 'translate-x-1']"></span>
                                </button>
                            </div>

                            <p class="text-xs text-gray-500 mb-3">{{ $t(reminder.description) }}</p>

                            <div class="space-y-2" v-if="cartForm['reminder_' + (idx+1) + '_enabled']">
                                <div>
                                    <label class="text-xs font-medium text-gray-600">{{ $t('Delay (minutes)') }}</label>
                                    <input type="number" v-model.number="cartForm['reminder_' + (idx+1) + '_delay_minutes']"
                                        class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm w-full mt-1" min="1" />
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-600">{{ $t('WhatsApp Template') }}</label>
                                    <select v-model="cartForm['reminder_' + (idx+1) + '_template_id']"
                                        class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm w-full mt-1">
                                        <option :value="null">{{ $t('Select template...') }}</option>
                                        <option v-for="t in templates" :key="t.id" :value="t.id">{{ t.name }} ({{ t.language }})</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Discount Section for Reminder 3 -->
                    <div v-if="cartForm.reminder_3_enabled" class="border border-orange-200 bg-orange-50 rounded-xl p-4 mb-6">
                        <h4 class="font-medium text-sm text-gray-900 mb-3 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" class="text-orange-600">
                                <path fill="currentColor" d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58s1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41s-.23-1.06-.59-1.42M5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4S7 4.67 7 5.5S6.33 7 5.5 7" />
                            </svg>
                            {{ $t('Discount Offer (Reminder 3)') }}
                        </h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-medium text-gray-600">{{ $t('Discount Code') }}</label>
                                <input type="text" v-model="cartForm.discount_code" placeholder="e.g. COMEBACK10"
                                    class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm w-full mt-1" />
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">{{ $t('Discount %') }}</label>
                                <input type="number" v-model.number="cartForm.discount_percentage" placeholder="10"
                                    class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm w-full mt-1" min="0" max="100" step="0.01" />
                            </div>
                        </div>
                    </div>

                    <!-- Available Variables -->
                    <div class="bg-gray-50 rounded-xl p-4 mb-6">
                        <label class="text-xs font-medium text-gray-500 mb-2 block">{{ $t('Available Cart Variables for Templates') }}</label>
                        <div class="flex flex-wrap gap-1.5">
                            <span v-for="v in templateVariables.abandoned_cart" :key="v"
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono bg-white border border-gray-200 text-gray-600 cursor-pointer hover:border-[#96bf48] hover:text-[#5c8a17] transition-colors"
                                @click="copyToClipboard(v)">
                                {{ v }}
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button @click="saveCartRecovery" :disabled="savingCart"
                            class="inline-flex items-center px-6 py-2.5 bg-[#96bf48] text-white font-medium rounded-xl hover:bg-[#7da63a] transition-colors text-sm disabled:opacity-50">
                            <svg v-if="savingCart" class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            {{ $t('Save Cart Recovery Settings') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Logs Tab -->
            <div v-if="stores.length > 0 && activeTab === 'logs' && selectedStore">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900">{{ $t('Notification Logs') }}</h2>
                        <button @click="loadLogs" class="text-sm text-[#5c8a17] hover:underline">{{ $t('Refresh') }}</button>
                    </div>

                    <div v-if="logs.length === 0" class="text-center py-8 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" class="mx-auto mb-3 opacity-30">
                            <path fill="currentColor" d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2m-2 12H6v-2h12zm0-3H6V9h12zm0-3H6V6h12z" />
                        </svg>
                        <p class="text-sm">{{ $t('No notification logs yet') }}</p>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-500">{{ $t('Event') }}</th>
                                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-500">{{ $t('Contact') }}</th>
                                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-500">{{ $t('Status') }}</th>
                                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-500">{{ $t('Time') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="log in logs" :key="log.id" class="border-b border-gray-50 hover:bg-gray-50">
                                    <td class="py-2 px-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                            {{ formatEventType(log.event_type) }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3 text-gray-700">
                                        {{ log.contact ? (log.contact.first_name + ' ' + log.contact.last_name) : '-' }}
                                    </td>
                                    <td class="py-2 px-3">
                                        <span :class="[
                                            'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium',
                                            log.status === 'sent' ? 'bg-green-50 text-green-700' :
                                            log.status === 'failed' ? 'bg-red-50 text-red-700' :
                                            log.status === 'skipped' ? 'bg-yellow-50 text-yellow-700' :
                                            'bg-gray-50 text-gray-700'
                                        ]">
                                            {{ log.status }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3 text-gray-500 text-xs">{{ formatDate(log.created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Abandoned Carts Tab -->
            <div v-if="stores.length > 0 && activeTab === 'abandoned_carts' && selectedStore">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900">{{ $t('Abandoned Carts') }}</h2>
                        <button @click="loadAbandonedCarts" class="text-sm text-[#5c8a17] hover:underline">{{ $t('Refresh') }}</button>
                    </div>

                    <div v-if="abandonedCarts.length === 0" class="text-center py-8 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" class="mx-auto mb-3 opacity-30">
                            <path fill="currentColor" d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2s-.9-2-2-2M1 2v2h2l3.6 7.59l-1.35 2.45c-.16.28-.25.61-.25.96c0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12l.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0 0 20 4H5.21l-.94-2zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2s2-.9 2-2s-.9-2-2-2" />
                        </svg>
                        <p class="text-sm">{{ $t('No abandoned carts tracked yet') }}</p>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-500">{{ $t('Customer') }}</th>
                                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-500">{{ $t('Total') }}</th>
                                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-500">{{ $t('Items') }}</th>
                                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-500">{{ $t('Status') }}</th>
                                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-500">{{ $t('Abandoned At') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="cart in abandonedCarts" :key="cart.id" class="border-b border-gray-50 hover:bg-gray-50">
                                    <td class="py-2 px-3">
                                        <div class="text-gray-900">{{ cart.customer_name || '-' }}</div>
                                        <div class="text-xs text-gray-400">{{ cart.customer_phone }}</div>
                                    </td>
                                    <td class="py-2 px-3 font-medium text-gray-900">{{ cart.currency }} {{ cart.total_price }}</td>
                                    <td class="py-2 px-3 text-gray-500">{{ (cart.line_items || []).length }} {{ $t('items') }}</td>
                                    <td class="py-2 px-3">
                                        <span :class="[
                                            'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium',
                                            cart.status === 'recovered' ? 'bg-green-50 text-green-700' :
                                            cart.status === 'expired' ? 'bg-gray-100 text-gray-500' :
                                            'bg-orange-50 text-orange-700'
                                        ]">
                                            {{ formatCartStatus(cart.status) }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3 text-gray-500 text-xs">{{ formatDate(cart.abandoned_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Setup Guide Tab -->
            <div v-if="stores.length > 0 && activeTab === 'setup_guide'">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">{{ $t('Complete Setup Guide') }}</h2>

                    <div class="space-y-6">
                        <!-- Step 1 -->
                        <div class="border border-gray-100 rounded-xl p-5">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="w-8 h-8 rounded-full bg-gradient-to-br from-[#96bf48] to-[#5c8a17] flex items-center justify-center text-white font-bold text-sm">1</span>
                                <h3 class="font-semibold text-gray-900">{{ $t('Create a Shopify Custom App') }}</h3>
                            </div>
                            <div class="ml-11 space-y-2 text-sm text-gray-600">
                                <p>1. {{ $t('Go to your Shopify Admin → Settings → Apps and sales channels') }}</p>
                                <p>2. {{ $t('Click "Develop apps" → "Create an app"') }}</p>
                                <p>3. {{ $t('Name it something like "WhatsApp Notifications"') }}</p>
                                <p>4. {{ $t('Under "Configuration" → "Admin API access scopes", select:') }}</p>
                                <div class="bg-gray-50 rounded-lg p-3 mt-1 font-mono text-xs space-y-1">
                                    <p>✅ read_orders, write_orders</p>
                                    <p>✅ read_checkouts, write_checkouts</p>
                                    <p>✅ read_fulfillments</p>
                                    <p>✅ read_customers</p>
                                </div>
                                <p>5. {{ $t('Click "Install app" and copy the Admin API access token') }}</p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="border border-gray-100 rounded-xl p-5">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="w-8 h-8 rounded-full bg-gradient-to-br from-[#96bf48] to-[#5c8a17] flex items-center justify-center text-white font-bold text-sm">2</span>
                                <h3 class="font-semibold text-gray-900">{{ $t('Connect Store Here') }}</h3>
                            </div>
                            <div class="ml-11 space-y-2 text-sm text-gray-600">
                                <p>1. {{ $t('Click "Add Store" button above') }}</p>
                                <p>2. {{ $t('Enter your Shopify store domain (e.g., mystore.myshopify.com)') }}</p>
                                <p>3. {{ $t('Paste the Admin API access token from Step 1') }}</p>
                                <p>4. {{ $t('Optionally add API Key and Webhook Secret for extra security') }}</p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="border border-gray-100 rounded-xl p-5">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="w-8 h-8 rounded-full bg-gradient-to-br from-[#96bf48] to-[#5c8a17] flex items-center justify-center text-white font-bold text-sm">3</span>
                                <h3 class="font-semibold text-gray-900">{{ $t('Configure Shopify Webhooks') }}</h3>
                            </div>
                            <div class="ml-11 space-y-2 text-sm text-gray-600">
                                <p>1. {{ $t('In Shopify Admin → Settings → Notifications → Webhooks') }}</p>
                                <p>2. {{ $t('Click "Create webhook" for each event:') }}</p>
                                <div class="bg-gray-50 rounded-lg p-3 mt-1 space-y-2 text-xs">
                                    <div v-for="event in webhookEvents" :key="event" class="flex items-center gap-2">
                                        <span class="inline-block w-2 h-2 rounded-full bg-[#96bf48]"></span>
                                        <span class="font-mono">{{ event }}</span>
                                    </div>
                                </div>
                                <p>3. {{ $t('Set the URL to your webhook URL (shown on the Stores tab)') }}</p>
                                <p>4. {{ $t('Format: JSON, API version: Latest') }}</p>
                                <div v-if="selectedStore" class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-2">
                                    <label class="text-xs font-medium text-blue-700 mb-1 block">{{ $t('Your Webhook URL') }}</label>
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs text-blue-800 bg-white px-2 py-1 rounded border border-blue-100 flex-1 overflow-x-auto">{{ getWebhookUrl(selectedStore.uuid) }}</code>
                                        <button @click="copyToClipboard(getWebhookUrl(selectedStore.uuid))" class="px-2 py-1 text-xs bg-blue-100 hover:bg-blue-200 rounded transition-colors">{{ $t('Copy') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="border border-gray-100 rounded-xl p-5">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="w-8 h-8 rounded-full bg-gradient-to-br from-[#96bf48] to-[#5c8a17] flex items-center justify-center text-white font-bold text-sm">4</span>
                                <h3 class="font-semibold text-gray-900">{{ $t('Create WhatsApp Message Templates') }}</h3>
                            </div>
                            <div class="ml-11 space-y-2 text-sm text-gray-600">
                                <p>{{ $t('Go to Message Templates section and create templates for:') }}</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <p class="font-medium text-gray-800 text-xs mb-1">📦 {{ $t('Order Confirmation') }}</p>
                                        <p class="text-xs text-gray-500" v-text="'Use variables: {{order_number}}, {{customer_name}}, {{total_price}}'"></p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <p class="font-medium text-gray-800 text-xs mb-1">🚚 {{ $t('Shipping Update') }}</p>
                                        <p class="text-xs text-gray-500" v-text="'Use variables: {{tracking_number}}, {{tracking_url}}, {{carrier}}'"></p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <p class="font-medium text-gray-800 text-xs mb-1">✅ {{ $t('Delivery Status') }}</p>
                                        <p class="text-xs text-gray-500" v-text="'Use variables: {{order_number}}, {{delivery_status}}'"></p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <p class="font-medium text-gray-800 text-xs mb-1">💰 {{ $t('COD Verification') }}</p>
                                        <p class="text-xs text-gray-500" v-text="'Use variables: {{total_price}}, {{delivery_address}}'"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 5 -->
                        <div class="border border-gray-100 rounded-xl p-5">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="w-8 h-8 rounded-full bg-gradient-to-br from-[#96bf48] to-[#5c8a17] flex items-center justify-center text-white font-bold text-sm">5</span>
                                <h3 class="font-semibold text-gray-900">{{ $t('Map Templates to Events') }}</h3>
                            </div>
                            <div class="ml-11 space-y-2 text-sm text-gray-600">
                                <p>1. {{ $t('Go to the "Notifications" tab above') }}</p>
                                <p>2. {{ $t('Enable each event type you want') }}</p>
                                <p>3. {{ $t('Select the WhatsApp template for each event') }}</p>
                                <p>4. {{ $t('Save your configuration') }}</p>
                            </div>
                        </div>

                        <!-- Step 6 -->
                        <div class="border border-gray-100 rounded-xl p-5">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="w-8 h-8 rounded-full bg-gradient-to-br from-[#96bf48] to-[#5c8a17] flex items-center justify-center text-white font-bold text-sm">6</span>
                                <h3 class="font-semibold text-gray-900">{{ $t('Setup Cart Recovery (Optional)') }}</h3>
                            </div>
                            <div class="ml-11 space-y-2 text-sm text-gray-600">
                                <p>1. {{ $t('Go to the "Cart Recovery" tab') }}</p>
                                <p>2. {{ $t('Enable cart recovery and configure timing:') }}</p>
                                <div class="bg-gray-50 rounded-lg p-3 mt-1 space-y-1 text-xs">
                                    <p>⏱ {{ $t('Reminder 1: After 30 minutes — gentle reminder') }}</p>
                                    <p>⏱ {{ $t('Reminder 2: After 6 hours — urgency message') }}</p>
                                    <p>⏱ {{ $t('Reminder 3: After 24 hours — with discount offer') }}</p>
                                </div>
                                <p>3. {{ $t('Assign WhatsApp templates to each reminder step') }}</p>
                                <p>4. {{ $t('Add a discount code for Reminder 3 to incentivize purchase') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Connect Store Modal -->
        <Teleport to="body">
            <div v-if="showConnectModal" class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/50" @click="showConnectModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 p-6 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-bold text-gray-900">{{ editingStore ? $t('Edit Store') : $t('Connect Shopify Store') }}</h3>
                        <button @click="showConnectModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Connection Method Tabs (only when not editing) -->
                    <div v-if="!editingStore" class="flex border-b border-gray-200 mb-5">
                        <button @click="connectMethod = 'oauth'"
                            :class="['px-4 py-2 text-sm font-medium border-b-2 transition-colors', connectMethod === 'oauth' ? 'border-[#96bf48] text-[#5c8a17]' : 'border-transparent text-gray-500']">
                            {{ $t('OAuth Connect') }}
                            <span class="ml-1 text-[10px] px-1.5 py-0.5 bg-green-100 text-green-700 rounded-full">{{ $t('Recommended') }}</span>
                        </button>
                        <button @click="connectMethod = 'manual'"
                            :class="['px-4 py-2 text-sm font-medium border-b-2 transition-colors', connectMethod === 'manual' ? 'border-[#96bf48] text-[#5c8a17]' : 'border-transparent text-gray-500']">
                            {{ $t('Manual Token') }}
                        </button>
                    </div>

                    <!-- OAuth Connect Form -->
                    <form v-if="connectMethod === 'oauth' && !editingStore" @submit.prevent="submitOAuth" class="space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-700">
                            {{ $t('This will redirect you to Shopify to authorize the app. The access token will be generated automatically.') }}
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 block mb-1">{{ $t('Shop Domain') }} *</label>
                            <input type="text" v-model="oauthForm.shop_domain" placeholder="mystore.myshopify.com"
                                class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full focus:ring-2 focus:ring-[#96bf48] focus:border-transparent" required />
                            <p v-if="oauthForm.errors.shop_domain" class="text-xs text-red-500 mt-1">{{ oauthForm.errors.shop_domain }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 block mb-1">{{ $t('Client ID') }} *</label>
                            <input type="text" v-model="oauthForm.client_id" placeholder="From Dev Dashboard → Settings"
                                class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full focus:ring-2 focus:ring-[#96bf48] focus:border-transparent" required />
                            <p class="text-xs text-gray-400 mt-1">{{ $t('Found in Shopify Dev Dashboard → Your App → Settings → Client credentials') }}</p>
                            <p v-if="oauthForm.errors.client_id" class="text-xs text-red-500 mt-1">{{ oauthForm.errors.client_id }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 block mb-1">{{ $t('Client Secret') }} *</label>
                            <input type="password" v-model="oauthForm.client_secret" placeholder="shpss_..."
                                class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full focus:ring-2 focus:ring-[#96bf48] focus:border-transparent" required />
                            <p v-if="oauthForm.errors.client_secret" class="text-xs text-red-500 mt-1">{{ oauthForm.errors.client_secret }}</p>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="showConnectModal = false"
                                class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">
                                {{ $t('Cancel') }}
                            </button>
                            <button type="submit" :disabled="oauthForm.processing"
                                class="px-6 py-2 text-sm bg-gradient-to-r from-[#96bf48] to-[#5c8a17] text-white font-medium rounded-lg hover:opacity-90 disabled:opacity-50">
                                <svg v-if="oauthForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                {{ $t('Connect via Shopify') }}
                            </button>
                        </div>
                    </form>

                    <!-- Manual Token Form -->
                    <form v-if="connectMethod === 'manual' || editingStore" @submit.prevent="submitStoreForm" class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700 block mb-1">{{ $t('Shop Domain') }} *</label>
                            <input type="text" v-model="storeForm.shop_domain" placeholder="mystore.myshopify.com"
                                class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full focus:ring-2 focus:ring-[#96bf48] focus:border-transparent" required />
                            <p v-if="storeForm.errors.shop_domain" class="text-xs text-red-500 mt-1">{{ storeForm.errors.shop_domain }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 block mb-1">{{ $t('Admin API Access Token') }} *</label>
                            <input type="password" v-model="storeForm.access_token" placeholder="shpat_..."
                                class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full focus:ring-2 focus:ring-[#96bf48] focus:border-transparent" required />
                            <p v-if="storeForm.errors.access_token" class="text-xs text-red-500 mt-1">{{ storeForm.errors.access_token }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 block mb-1">{{ $t('Webhook Secret') }} <span class="text-gray-400">({{ $t('optional, for HMAC verification') }})</span></label>
                            <input type="password" v-model="storeForm.webhook_secret" placeholder=""
                                class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full" />
                            <p class="text-xs text-gray-400 mt-1">{{ $t('Found in Shopify → Settings → Notifications → Webhooks → Signing secret') }}</p>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="showConnectModal = false"
                                class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">
                                {{ $t('Cancel') }}
                            </button>
                            <button type="submit" :disabled="storeForm.processing"
                                class="px-6 py-2 text-sm bg-gradient-to-r from-[#96bf48] to-[#5c8a17] text-white font-medium rounded-lg hover:opacity-90 disabled:opacity-50">
                                <svg v-if="storeForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                {{ editingStore ? $t('Update') : $t('Connect') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <!-- Delete Confirmation Modal -->

        <!-- Template Editor Modal -->
        <Teleport to="body">
            <div v-if="showTemplateEditor" class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/50" @click="showTemplateEditor = false"></div>
                <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 p-6 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $t('WhatsApp Template') }}</h3>
                            <p class="text-xs text-gray-500">{{ $t('Review and submit to Meta for approval') }}</p>
                        </div>
                        <button @click="showTemplateEditor = false" class="text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Already exists notice -->
                    <div v-if="getTemplateByName(templateEditor.name)" class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
                        <p class="text-sm text-green-700">{{ $t('This template already exists.') }}
                            <span class="font-medium">{{ $t('Status') }}: {{ getTemplateByName(templateEditor.name)?.status }}</span>
                        </p>
                    </div>

                    <!-- Template Form -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-medium text-gray-600 mb-1 block">{{ $t('Template Name') }}</label>
                                <input type="text" v-model="templateEditor.name"
                                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full font-mono"
                                    :disabled="!!getTemplateByName(templateEditor.name)"
                                    placeholder="shopify_order_confirmation" />
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $t('Lowercase, underscores only') }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-xs font-medium text-gray-600 mb-1 block">{{ $t('Category') }}</label>
                                    <select v-model="templateEditor.category" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full"
                                        :disabled="!!getTemplateByName(templateEditor.name)">
                                        <option value="UTILITY">UTILITY</option>
                                        <option value="MARKETING">MARKETING</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-600 mb-1 block">{{ $t('Language') }}</label>
                                    <select v-model="templateEditor.language" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full"
                                        :disabled="!!getTemplateByName(templateEditor.name)">
                                        <option value="en_US">English (US)</option>
                                        <option value="en">English</option>
                                        <option value="hi">Hindi</option>
                                        <option value="es">Spanish</option>
                                        <option value="fr">French</option>
                                        <option value="ar">Arabic</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Header -->
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">{{ $t('Header') }}</label>
                            <input type="text" v-model="templateEditor.header_text"
                                class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full"
                                :disabled="!!getTemplateByName(templateEditor.name)"
                                maxlength="60" />
                        </div>

                        <!-- Body -->
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">{{ $t('Body Message') }}</label>
                            <textarea v-model="templateEditor.body_text" rows="6"
                                class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full font-mono leading-relaxed"
                                :disabled="!!getTemplateByName(templateEditor.name)"
                                maxlength="1024"></textarea>
                            <p class="text-[10px] text-gray-400 mt-0.5">{{ templateEditor.body_text?.length || 0 }}/1024</p>
                        </div>

                        <!-- Footer -->
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">{{ $t('Footer') }}</label>
                            <input type="text" v-model="templateEditor.footer_text"
                                class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full"
                                :disabled="!!getTemplateByName(templateEditor.name)"
                                maxlength="60" />
                        </div>

                        <!-- Buttons Preview -->
                        <div v-if="templateEditor.buttons?.length > 0">
                            <label class="text-xs font-medium text-gray-600 mb-1 block">{{ $t('Buttons') }}</label>
                            <div class="space-y-1">
                                <div v-for="(btn, bi) in templateEditor.buttons" :key="bi"
                                    class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2 text-sm">
                                    <span class="text-[10px] px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded">{{ btn.type }}</span>
                                    <span class="text-gray-700">{{ btn.text }}</span>
                                    <span v-if="btn.url" class="text-gray-400 text-xs truncate">{{ btn.url }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Variable Map Info -->
                        <div v-if="templateEditor.variable_map?.length" class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <label class="text-xs font-medium text-blue-700 mb-1 block">{{ $t('Variable Mapping (auto-configured)') }}</label>
                            <div class="flex flex-wrap gap-1">
                                <span v-for="(v, i) in templateEditor.variable_map" :key="i"
                                    class="text-xs bg-white border border-blue-100 rounded px-2 py-0.5 font-mono text-blue-600">
                                    {{ '{' + '{' + (i+1) + '}' + '}' }} → {{ v }}
                                </span>
                            </div>
                        </div>

                        <!-- WhatsApp Preview -->
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-2 block">{{ $t('Preview') }}</label>
                            <div class="bg-[#e5ddd5] rounded-xl p-4 max-w-sm mx-auto">
                                <div class="bg-white rounded-xl p-3 shadow-sm">
                                    <p v-if="templateEditor.header_text" class="font-bold text-sm text-gray-900 mb-1">{{ templateEditor.header_text }}</p>
                                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ templateEditor.body_text }}</p>
                                    <p v-if="templateEditor.footer_text" class="text-xs text-gray-400 mt-2">{{ templateEditor.footer_text }}</p>
                                    <div v-if="templateEditor.buttons?.length" class="mt-2 border-t border-gray-100 pt-2 space-y-1">
                                        <div v-for="(btn, bi) in templateEditor.buttons" :key="bi"
                                            class="text-center text-sm text-blue-600 py-1 hover:bg-blue-50 rounded cursor-default">
                                            {{ btn.text }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                        <button @click="showTemplateEditor = false"
                            class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">
                            {{ $t('Close') }}
                        </button>
                        <button v-if="!getTemplateByName(templateEditor.name)"
                            @click="submitPrebuiltTemplate" :disabled="submittingTemplate"
                            class="px-6 py-2 text-sm bg-gradient-to-r from-[#96bf48] to-[#5c8a17] text-white font-medium rounded-lg hover:opacity-90 disabled:opacity-50">
                            <svg v-if="submittingTemplate" class="animate-spin -ml-1 mr-2 h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            {{ $t('Submit to Meta for Approval') }}
                        </button>
                    </div>

                    <!-- Result Message -->
                    <div v-if="templateSubmitResult" class="mt-3 rounded-lg p-3 text-sm"
                        :class="templateSubmitResult.success ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'">
                        {{ templateSubmitResult.message }}
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Delete Confirmation Modal -->
        <Teleport to="body">
            <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/50" @click="showDeleteModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $t('Delete Store') }}</h3>
                    <p class="text-sm text-gray-500 mb-5">{{ $t('Are you sure you want to disconnect this store? All notification templates and settings will be removed.') }}</p>
                    <div class="flex justify-end gap-3">
                        <button @click="showDeleteModal = false" class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">
                            {{ $t('Cancel') }}
                        </button>
                        <button @click="deleteStore" class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700">
                            {{ $t('Delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>

<script setup>
import AppLayout from './../../../../resources/js/Pages/User/Layout/App.vue';
import { ref, reactive, computed, onMounted } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    stores: { type: Array, default: () => [] },
    templates: { type: Array, default: () => [] },
    allTemplates: { type: Array, default: () => [] },
    eventTypes: { type: Array, default: () => [] },
    templateVariables: { type: Object, default: () => ({}) },
    webhookEvents: { type: Array, default: () => [] },
    prebuiltTemplates: { type: Object, default: () => ({}) },
});

// State
const activeTab = ref(props.stores.length > 0 ? 'stores' : 'setup_guide');
const showConnectModal = ref(false);
const showDeleteModal = ref(false);
const editingStore = ref(null);
const deletingStore = ref(null);
const selectedStoreUuid = ref(props.stores.length > 0 ? props.stores[0].uuid : null);
const savingNotifications = ref(false);
const savingCart = ref(false);
const logs = ref([]);
const abandonedCarts = ref([]);
const connectMethod = ref('oauth');

// Template Editor State
const showTemplateEditor = ref(false);
const editingTemplateKey = ref(null);
const submittingTemplate = ref(false);
const templateSubmitResult = ref(null);
const templateEditor = reactive({
    name: '',
    category: 'UTILITY',
    language: 'en_US',
    header_text: '',
    body_text: '',
    footer_text: '',
    buttons: [],
    variable_map: [],
    button_variable_map: {},
    body_example: [],
});

const tabs = [
    { id: 'stores', label: 'Stores' },
    { id: 'notifications', label: 'Notifications' },
    { id: 'cart_recovery', label: 'Cart Recovery' },
    { id: 'logs', label: 'Logs' },
    { id: 'abandoned_carts', label: 'Abandoned Carts' },
    { id: 'setup_guide', label: 'Setup Guide' },
];

const selectedStore = computed(() => props.stores.find(s => s.uuid === selectedStoreUuid.value));

// Store Form
const storeForm = useForm({
    shop_domain: '',
    access_token: '',
    api_key: '',
    api_secret: '',
    webhook_secret: '',
});

// OAuth Form
const oauthForm = useForm({
    shop_domain: '',
    client_id: '',
    client_secret: '',
});

// Notification Templates Form
const notificationForm = reactive({
    templates: {},
});

// Initialize notification form for each event type
const initNotificationForm = () => {
    props.eventTypes.forEach(et => {
        notificationForm.templates[et] = {
            event_type: et,
            template_id: null,
            template_params: {},
            is_active: false,
        };
    });
};
initNotificationForm();

// Cart Recovery Form
const cartForm = reactive({
    is_active: false,
    reminder_1_enabled: true,
    reminder_1_delay_minutes: 30,
    reminder_1_template_id: null,
    reminder_1_params: null,
    reminder_2_enabled: true,
    reminder_2_delay_minutes: 360,
    reminder_2_template_id: null,
    reminder_2_params: null,
    reminder_3_enabled: true,
    reminder_3_delay_minutes: 1440,
    reminder_3_template_id: null,
    reminder_3_params: null,
    discount_code: '',
    discount_percentage: null,
});

const reminders = [
    { title: 'Reminder 1 — Gentle Nudge', description: 'Send a friendly reminder soon after cart abandonment', gradient: 'from-blue-500 to-cyan-500' },
    { title: 'Reminder 2 — Urgency', description: 'Create urgency with a follow-up message', gradient: 'from-orange-500 to-amber-500' },
    { title: 'Reminder 3 — Discount Offer', description: 'Send a discount offer as the final push', gradient: 'from-red-500 to-pink-500' },
];

const setupSteps = [
    {
        title: 'Create a Shopify Custom App',
        description: 'Go to Shopify Admin → Settings → Apps and sales channels → Develop apps',
        substeps: [
            'Create a new custom app for WhatsApp notifications',
            'Enable Admin API scopes: read_orders, read_checkouts, read_fulfillments, read_customers',
            'Install the app and copy the Admin API access token',
        ],
    },
    {
        title: 'Connect Your Store',
        description: 'Click "Connect Shopify Store" below and enter your store details',
        substeps: [
            'Enter your store domain (e.g., mystore.myshopify.com)',
            'Paste your Admin API access token',
            'Optionally add webhook secret for HMAC verification',
        ],
    },
    {
        title: 'Setup Webhooks in Shopify',
        description: 'Configure Shopify to send events to your webhook URL',
        substeps: [
            'Go to Shopify Admin → Settings → Notifications → Webhooks',
            'Add webhooks for: orders/create, fulfillments/create, fulfillments/update, checkouts/create',
            'Set the webhook URL to the one shown after connecting your store',
            'Select JSON format and latest API version',
        ],
    },
    {
        title: 'Create WhatsApp Templates',
        description: 'Create approved WhatsApp message templates for each notification type',
        substeps: [
            'Order Confirmation — include {{order_number}}, {{total_price}}',
            'Shipping Updates — include {{tracking_number}}, {{tracking_url}}',
            'Abandoned Cart — include {{items_summary}}, {{recovery_url}}',
        ],
    },
    {
        title: 'Map Templates & Enable',
        description: 'Map your WhatsApp templates to Shopify events in the Notifications tab',
        substeps: [
            'Select a template for each event type',
            'Enable/disable individual notifications',
            'Configure cart recovery sequence with timing and discount offers',
        ],
    },
];

// Methods
const getWebhookUrl = (uuid) => {
    return `${window.location.origin}/shopify/webhook/${uuid}`;
};

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
};

const formatEventType = (type) => {
    return type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
};

const formatCartStatus = (status) => {
    const map = {
        'abandoned': 'Abandoned',
        'reminder_1_sent': 'Reminder 1 Sent',
        'reminder_2_sent': 'Reminder 2 Sent',
        'reminder_3_sent': 'Reminder 3 Sent',
        'recovered': 'Recovered ✅',
        'expired': 'Expired',
    };
    return map[status] || status;
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleString();
};

const eventIconClass = (type) => {
    const map = {
        'order_confirmation': 'bg-blue-50 text-blue-600',
        'shipping_update': 'bg-purple-50 text-purple-600',
        'delivery_status': 'bg-green-50 text-green-600',
        'cod_verification': 'bg-orange-50 text-orange-600',
    };
    return map[type] || 'bg-gray-50 text-gray-600';
};

const eventIcon = (type) => {
    // Return a simple span with emoji as icon fallback
    return {
        template: {
            'order_confirmation': '<span>📦</span>',
            'shipping_update': '<span>🚚</span>',
            'delivery_status': '<span>✅</span>',
            'cod_verification': '<span>💰</span>',
        }[type] || '<span>📋</span>',
    };
};

const eventDescription = (type) => {
    const map = {
        'order_confirmation': 'Sent when a new order is created or paid',
        'shipping_update': 'Sent when an order is shipped with tracking info',
        'delivery_status': 'Sent when an order is delivered',
        'cod_verification': 'Sent for Cash on Delivery orders for verification',
    };
    return map[type] || '';
};

// Template Editor Methods
const getTemplateByName = (name) => {
    return props.allTemplates.find(t => t.name === name);
};

const getTemplateStatusClass = (status) => {
    const map = {
        'APPROVED': 'bg-green-50 text-green-700 border-green-200',
        'PENDING': 'bg-yellow-50 text-yellow-700 border-yellow-200',
        'REJECTED': 'bg-red-50 text-red-700 border-red-200',
    };
    return map[status] || 'bg-gray-50 text-gray-500 border-gray-200';
};

const getTemplateStatusIcon = (status) => {
    const map = { 'APPROVED': '✅', 'PENDING': '⏳', 'REJECTED': '❌' };
    return map[status] || '—';
};

const openTemplateEditor = (templateKey) => {
    const tpl = props.prebuiltTemplates?.[templateKey];
    if (!tpl) return;
    editingTemplateKey.value = templateKey;
    templateSubmitResult.value = null;
    templateEditor.name = tpl.name || '';
    templateEditor.category = tpl.category || 'UTILITY';
    templateEditor.language = tpl.language || 'en_US';
    templateEditor.header_text = tpl.header?.text || '';
    templateEditor.body_text = tpl.body?.text || '';
    templateEditor.footer_text = tpl.footer?.text || '';
    templateEditor.buttons = tpl.buttons ? [...tpl.buttons] : [];
    templateEditor.variable_map = tpl.body?.variable_map || [];
    templateEditor.button_variable_map = tpl.button_variable_map || {};
    templateEditor.body_example = tpl.body?.example || [];
    showTemplateEditor.value = true;
};

const submitPrebuiltTemplate = async () => {
    submittingTemplate.value = true;
    templateSubmitResult.value = null;
    try {
        const payload = {
            template_key: editingTemplateKey.value,
            name: templateEditor.name,
            category: templateEditor.category,
            language: templateEditor.language,
            header_text: templateEditor.header_text,
            body_text: templateEditor.body_text,
            footer_text: templateEditor.footer_text,
            buttons: templateEditor.buttons,
            body_example: templateEditor.body_example,
            store_uuid: selectedStoreUuid.value,
        };
        const { data } = await axios.post('/integrations/shopify/submit-template', payload);
        templateSubmitResult.value = { success: true, message: data.message || 'Template submitted successfully!' };
        router.reload({ preserveScroll: true });
    } catch (e) {
        const msg = e.response?.data?.message || e.response?.data?.error || 'Failed to submit template';
        templateSubmitResult.value = { success: false, message: msg };
    } finally {
        submittingTemplate.value = false;
    }
};

const toggleNotification = (eventType) => {
    notificationForm.templates[eventType].is_active = !notificationForm.templates[eventType].is_active;
};

const editStore = (store) => {
    editingStore.value = store;
    storeForm.shop_domain = store.shop_domain;
    storeForm.access_token = '';
    storeForm.api_key = '';
    storeForm.api_secret = '';
    storeForm.webhook_secret = '';
    showConnectModal.value = true;
};

const configureStore = (store) => {
    selectedStoreUuid.value = store.uuid;
    activeTab.value = 'notifications';
    loadNotificationTemplates();
    loadCartRecoverySettings();
};

const confirmDeleteStore = (store) => {
    deletingStore.value = store;
    showDeleteModal.value = true;
};

const submitStoreForm = () => {
    if (editingStore.value) {
        storeForm.put(`/integrations/shopify/stores/${editingStore.value.uuid}`, {
            preserveScroll: true,
            onSuccess: () => {
                showConnectModal.value = false;
                editingStore.value = null;
                storeForm.reset();
            },
        });
    } else {
        storeForm.post('/integrations/shopify/stores', {
            preserveScroll: true,
            onSuccess: () => {
                showConnectModal.value = false;
                storeForm.reset();
            },
        });
    }
};

const submitOAuth = () => {
    oauthForm.post('/integrations/shopify/oauth/redirect');
};

const deleteStore = () => {
    if (!deletingStore.value) return;
    router.delete(`/integrations/shopify/stores/${deletingStore.value.uuid}`, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            deletingStore.value = null;
        },
    });
};

const onStoreChange = () => {
    if (activeTab.value === 'notifications') loadNotificationTemplates();
    if (activeTab.value === 'cart_recovery') loadCartRecoverySettings();
    if (activeTab.value === 'logs') loadLogs();
    if (activeTab.value === 'abandoned_carts') loadAbandonedCarts();
};

const loadNotificationTemplates = async () => {
    if (!selectedStoreUuid.value) return;
    try {
        const { data } = await axios.get(`/integrations/shopify/stores/${selectedStoreUuid.value}/notification-templates`);
        initNotificationForm();
        if (data.templates) {
            data.templates.forEach(t => {
                if (notificationForm.templates[t.event_type]) {
                    notificationForm.templates[t.event_type] = {
                        event_type: t.event_type,
                        template_id: t.template_id,
                        template_params: t.template_params || {},
                        is_active: t.is_active,
                    };
                }
            });
        }
    } catch (e) {
        console.error('Failed to load notification templates', e);
    }
};

const saveNotificationTemplates = async () => {
    if (!selectedStoreUuid.value) return;
    savingNotifications.value = true;
    try {
        const templates = Object.values(notificationForm.templates);
        await axios.post(`/integrations/shopify/stores/${selectedStoreUuid.value}/notification-templates`, {
            templates,
        });
        router.reload({ preserveScroll: true });
    } catch (e) {
        console.error('Failed to save notification templates', e);
    } finally {
        savingNotifications.value = false;
    }
};

const loadCartRecoverySettings = async () => {
    if (!selectedStoreUuid.value) return;
    try {
        const { data } = await axios.get(`/integrations/shopify/stores/${selectedStoreUuid.value}/cart-recovery`);
        if (data.settings) {
            Object.keys(cartForm).forEach(key => {
                if (data.settings[key] !== undefined) {
                    cartForm[key] = data.settings[key];
                }
            });
        }
    } catch (e) {
        console.error('Failed to load cart recovery settings', e);
    }
};

const saveCartRecovery = async () => {
    if (!selectedStoreUuid.value) return;
    savingCart.value = true;
    try {
        await axios.post(`/integrations/shopify/stores/${selectedStoreUuid.value}/cart-recovery`, cartForm);
        router.reload({ preserveScroll: true });
    } catch (e) {
        console.error('Failed to save cart recovery settings', e);
    } finally {
        savingCart.value = false;
    }
};

const loadLogs = async () => {
    if (!selectedStoreUuid.value) return;
    try {
        const { data } = await axios.get(`/integrations/shopify/stores/${selectedStoreUuid.value}/logs`);
        logs.value = data.logs?.data || [];
    } catch (e) {
        console.error('Failed to load logs', e);
    }
};

const loadAbandonedCarts = async () => {
    if (!selectedStoreUuid.value) return;
    try {
        const { data } = await axios.get(`/integrations/shopify/stores/${selectedStoreUuid.value}/abandoned-carts`);
        abandonedCarts.value = data.carts?.data || [];
    } catch (e) {
        console.error('Failed to load abandoned carts', e);
    }
};

// Load data on mount
onMounted(() => {
    if (selectedStoreUuid.value) {
        loadNotificationTemplates();
        loadCartRecoverySettings();
    }
});
</script>
