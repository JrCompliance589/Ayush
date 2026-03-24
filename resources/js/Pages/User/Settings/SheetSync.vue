<template>
    <SettingLayout :modules="props.modules">
        <div class="lg:h-[calc(100vh-220px)] lg:overflow-y-auto pr-2">
            <form @submit.prevent="submitForm()">
                <!-- Google Sheets Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 mb-6 overflow-hidden">
                    <div class="p-5 bg-gradient-to-r from-green-50 to-emerald-50/30 border-b border-slate-200/60">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg shadow-green-500/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="text-white">
                                    <path fill="currentColor" d="M19 11V9h-6V5h-2v4H5v2h6v4h2v-4h6Zm2-7a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h18Zm0 14V6H3v12h18Z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-slate-900">{{ $t('Google Sheets Integration') }}</h3>
                                <p class="text-sm text-slate-500">{{ $t('Automatically sync new contacts to Google Sheets') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 space-y-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-medium text-slate-700">{{ $t('Enable Google Sheets Sync') }}</span>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $t('New contacts will be automatically added to your Google Sheet') }}</p>
                            </div>
                            <FormToggleSwitch v-model="form.google_sheets.enabled" />
                        </div>

                        <div v-if="form.google_sheets.enabled" class="space-y-4 pt-2 border-t border-slate-100">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ $t('Spreadsheet ID') }} <span class="text-red-500">*</span></label>
                                <p class="text-xs text-slate-500 mb-2">{{ $t('Found in the Google Sheets URL: docs.google.com/spreadsheets/d/{SPREADSHEET_ID}/edit') }}</p>
                                <FormInput v-model="form.google_sheets.spreadsheet_id" :error="form.errors['google_sheets.spreadsheet_id']" :name="''" :type="'text'" placeholder="1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74Og..." />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ $t('Sheet Name') }}</label>
                                <p class="text-xs text-slate-500 mb-2">{{ $t('Name of the tab/sheet inside the spreadsheet (default: Sheet1)') }}</p>
                                <FormInput v-model="form.google_sheets.sheet_name" :error="form.errors['google_sheets.sheet_name']" :name="''" :type="'text'" placeholder="Sheet1" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ $t('Service Account Access Token') }} <span class="text-red-500">*</span></label>
                                <p class="text-xs text-slate-500 mb-2">{{ $t('OAuth2 access token from Google Cloud Console with Sheets API enabled') }}</p>
                                <FormInput v-model="form.google_sheets.access_token" :error="form.errors['google_sheets.access_token']" :name="''" :type="'password'" placeholder="ya29.a0AfH6SM..." />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ $t('API Key (Optional)') }}</label>
                                <p class="text-xs text-slate-500 mb-2">{{ $t('Alternative: use API key if not using OAuth access token') }}</p>
                                <FormInput v-model="form.google_sheets.api_key" :error="form.errors['google_sheets.api_key']" :name="''" :type="'password'" placeholder="AIzaSy..." />
                            </div>

                            <div class="pt-2">
                                <button type="button" @click="testConnection('google_sheets')"
                                    :disabled="testingGoogle"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50">
                                    <svg v-if="testingGoogle" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2m-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9Z"/></svg>
                                    {{ testingGoogle ? $t('Testing...') : $t('Test Connection') }}
                                </button>
                                <span v-if="googleTestResult !== null" class="ml-3 text-sm" :class="googleTestResult ? 'text-green-600' : 'text-red-600'">
                                    {{ googleTestMessage }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Zoho Sheet Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 mb-6 overflow-hidden">
                    <div class="p-5 bg-gradient-to-r from-blue-50 to-indigo-50/30 border-b border-slate-200/60">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg shadow-blue-500/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="text-white">
                                    <path fill="currentColor" d="M3 3h18v18H3V3m2 2v14h14V5H5m2 2h10v2H7V7m0 4h10v2H7v-2m0 4h7v2H7v-2Z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-slate-900">{{ $t('Zoho Sheet Integration') }}</h3>
                                <p class="text-sm text-slate-500">{{ $t('Automatically sync new contacts to Zoho Sheet') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 space-y-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-medium text-slate-700">{{ $t('Enable Zoho Sheet Sync') }}</span>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $t('New contacts will be automatically added to your Zoho Sheet') }}</p>
                            </div>
                            <FormToggleSwitch v-model="form.zoho_sheet.enabled" />
                        </div>

                        <div v-if="form.zoho_sheet.enabled" class="space-y-5 pt-2 border-t border-slate-100">

                            <!-- Step-by-step Setup Guide (collapsible) -->
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl overflow-hidden">
                                <button type="button" @click="showZohoGuide = !showZohoGuide"
                                    class="w-full flex items-center justify-between p-4 text-left hover:bg-blue-100/50 transition-colors">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="text-blue-600">
                                            <path fill="currentColor" d="M11 18h2v-2h-2v2m1-16C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8s8 3.59 8 8s-3.59 8-8 8m0-14c-2.21 0-4 1.79-4 4h2c0-1.1.9-2 2-2s2 .9 2 2c0 2-3 1.75-3 5h2c0-2.25 3-2.5 3-5c0-2.21-1.79-4-4-4"/>
                                        </svg>
                                        <span class="font-semibold text-blue-800 text-sm">{{ $t('Setup Guide: How to connect Zoho Sheet (click to expand)') }}</span>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-blue-600 transition-transform duration-200"
                                        :class="showZohoGuide ? 'rotate-180' : ''">
                                        <path fill="currentColor" d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6l-6-6l1.41-1.41Z"/>
                                    </svg>
                                </button>
                                <div v-if="showZohoGuide" class="px-4 pb-4 space-y-4">
                                    <!-- Step 1 -->
                                    <div class="flex gap-3">
                                        <div class="shrink-0 w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">1</div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">{{ $t('Create a Zoho Sheet') }}</p>
                                            <p class="text-xs text-slate-600 mt-0.5">{{ $t('Go to') }} <a href="https://sheet.zoho.in" target="_blank" class="text-blue-600 underline">sheet.zoho.in</a> {{ $t('and create a new spreadsheet. Add these column headers in the first row:') }}</p>
                                            <div class="mt-2 flex flex-wrap gap-1.5">
                                                <span class="px-2 py-0.5 bg-white border border-slate-200 rounded text-xs font-mono">First Name</span>
                                                <span class="px-2 py-0.5 bg-white border border-slate-200 rounded text-xs font-mono">Last Name</span>
                                                <span class="px-2 py-0.5 bg-white border border-slate-200 rounded text-xs font-mono">Phone</span>
                                                <span class="px-2 py-0.5 bg-white border border-slate-200 rounded text-xs font-mono">Email</span>
                                                <span class="px-2 py-0.5 bg-white border border-slate-200 rounded text-xs font-mono">Created At</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Step 2 -->
                                    <div class="flex gap-3">
                                        <div class="shrink-0 w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">2</div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">{{ $t('Copy Workbook ID from Sheet URL') }}</p>
                                            <p class="text-xs text-slate-600 mt-0.5">{{ $t('Open your Zoho Sheet. The URL will look like:') }}</p>
                                            <div class="mt-1.5 bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs font-mono text-slate-700 break-all">
                                                https://sheet.zoho.in/sheet/open/<span class="bg-yellow-200 text-yellow-800 font-bold px-0.5 rounded">xxxxxxxxxxxxxxx</span>/sheets/Sheet1
                                            </div>
                                            <p class="text-xs text-slate-600 mt-1">{{ $t('The highlighted part after /open/ is your Workbook ID. Copy and paste it below.') }}</p>
                                        </div>
                                    </div>
                                    <!-- Step 3 -->
                                    <div class="flex gap-3">
                                        <div class="shrink-0 w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">3</div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">{{ $t('Create App in Zoho API Console') }}</p>
                                            <p class="text-xs text-slate-600 mt-0.5">{{ $t('Go to') }} <a href="https://api-console.zoho.in" target="_blank" class="text-blue-600 underline">api-console.zoho.in</a> {{ $t('→ Click "ADD CLIENT" → Select "Server-based Applications"') }}</p>
                                            <ul class="mt-1.5 space-y-1 text-xs text-slate-600">
                                                <li class="flex items-start gap-1.5">
                                                    <span class="text-blue-500 mt-0.5">•</span>
                                                    <span><strong>{{ $t('Client Name') }}:</strong> {{ $t('Any name (e.g. "Nyife Sheet Sync")') }}</span>
                                                </li>
                                                <li class="flex items-start gap-1.5">
                                                    <span class="text-blue-500 mt-0.5">•</span>
                                                    <span><strong>{{ $t('Homepage URL') }}:</strong> {{ currentDomain }}</span>
                                                </li>
                                                <li class="flex items-start gap-1.5">
                                                    <span class="text-blue-500 mt-0.5">•</span>
                                                    <div>
                                                        <strong>{{ $t('Authorized Redirect URI') }}:</strong>
                                                        <div class="mt-1 flex items-center gap-2">
                                                            <code class="bg-white border border-slate-200 rounded px-2 py-1 text-xs break-all select-all">{{ redirectUri }}</code>
                                                            <button type="button" @click="copyToClipboard(redirectUri)" class="shrink-0 p-1 hover:bg-blue-100 rounded transition-colors" :title="$t('Copy')">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"><path fill="currentColor" d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1m3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2m0 16H8V7h11v14"/></svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                            <p class="text-xs text-slate-600 mt-1.5">{{ $t('Click "CREATE" → Then go to "Client Secret" tab to get your Client ID & Secret.') }}</p>
                                        </div>
                                    </div>
                                    <!-- Step 4 -->
                                    <div class="flex gap-3">
                                        <div class="shrink-0 w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">4</div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">{{ $t('Enter Client ID & Secret below') }}</p>
                                            <p class="text-xs text-slate-600 mt-0.5">{{ $t('Copy the Client ID and Client Secret from Zoho API Console and paste them in the fields below.') }}</p>
                                        </div>
                                    </div>
                                    <!-- Step 5 -->
                                    <div class="flex gap-3">
                                        <div class="shrink-0 w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">5</div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">{{ $t('Click "Connect with Zoho" button') }}</p>
                                            <p class="text-xs text-slate-600 mt-0.5">{{ $t('This will open Zoho login page. Log in and click "Accept" to authorize. You\'ll be redirected back and the Refresh Token will be generated automatically.') }}</p>
                                        </div>
                                    </div>
                                    <!-- Step 6 -->
                                    <div class="flex gap-3">
                                        <div class="shrink-0 w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">6</div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">{{ $t('Save & Test') }}</p>
                                            <p class="text-xs text-slate-600 mt-0.5">{{ $t('Click "Save Settings" and then "Test Connection" to verify everything works. A test row will be added to your sheet.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Zoho Domain -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ $t('Zoho Domain') }}</label>
                                <p class="text-xs text-slate-500 mb-2">{{ $t('Select your Zoho region (.in for India, .com for US/Global, .eu for Europe)') }}</p>
                                <select v-model="form.zoho_sheet.zoho_domain" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="zoho.in">zoho.in (India)</option>
                                    <option value="zoho.com">zoho.com (US/Global)</option>
                                    <option value="zoho.eu">zoho.eu (Europe)</option>
                                    <option value="zoho.com.au">zoho.com.au (Australia)</option>
                                    <option value="zoho.jp">zoho.jp (Japan)</option>
                                </select>
                            </div>
                            <!-- Client ID -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ $t('Client ID') }} <span class="text-red-500">*</span></label>
                                <p class="text-xs text-slate-500 mb-2">{{ $t('From Zoho API Console → Your App → Client Secret tab') }}</p>
                                <FormInput v-model="form.zoho_sheet.client_id" :error="form.errors['zoho_sheet.client_id']" :name="''" :type="'text'" placeholder="1000.Z6RPBRMNS7M56OV1VLG..." />
                            </div>
                            <!-- Client Secret -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ $t('Client Secret') }} <span class="text-red-500">*</span></label>
                                <p class="text-xs text-slate-500 mb-2">{{ $t('From Zoho API Console → Your App → Client Secret tab') }}</p>
                                <FormInput v-model="form.zoho_sheet.client_secret" :error="form.errors['zoho_sheet.client_secret']" :name="''" :type="'password'" placeholder="ecd577f97c2409cfcd..." />
                            </div>
                            <!-- Connect with Zoho Button + Refresh Token -->
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ $t('Refresh Token') }} <span class="text-red-500">*</span></label>
                                <p class="text-xs text-slate-500 mb-3">{{ $t('Click "Connect with Zoho" to authorize and get a refresh token automatically, or paste one manually.') }}</p>
                                <div class="flex gap-3 items-start">
                                    <div class="flex-1">
                                        <FormInput v-model="form.zoho_sheet.refresh_token" :error="form.errors['zoho_sheet.refresh_token']" :name="''" :type="'password'" placeholder="1000.abc123def456..." />
                                    </div>
                                    <button type="button" @click="connectZoho()"
                                        :disabled="!form.zoho_sheet.client_id || !form.zoho_sheet.client_secret"
                                        class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="currentColor" d="M10 17V7l5 5-5 5Z"/></svg>
                                        {{ $t('Connect with Zoho') }}
                                    </button>
                                </div>
                                <p v-if="zohoTokenMessage" class="mt-2 text-sm" :class="zohoTokenSuccess ? 'text-green-600' : 'text-red-600'">
                                    {{ zohoTokenMessage }}
                                </p>
                                <p v-if="form.zoho_sheet.refresh_token" class="mt-2 text-xs text-green-600 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2m-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9Z"/></svg>
                                    {{ $t('Refresh token is set. Access token will be auto-generated every time.') }}
                                </p>
                            </div>
                            <!-- Workbook ID -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ $t('Workbook ID') }} <span class="text-red-500">*</span></label>
                                <p class="text-xs text-slate-500 mb-2">{{ $t('Open your Zoho Sheet → copy the ID from URL: sheet.zoho.in/sheet/open/{WORKBOOK_ID}/sheets/...') }}</p>
                                <FormInput v-model="form.zoho_sheet.workbook_id" :error="form.errors['zoho_sheet.workbook_id']" :name="''" :type="'text'" placeholder="abc123def456..." />
                            </div>
                            <!-- Sheet Name -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ $t('Sheet Name') }}</label>
                                <p class="text-xs text-slate-500 mb-2">{{ $t('Name of the worksheet tab (default: Sheet1)') }}</p>
                                <FormInput v-model="form.zoho_sheet.sheet_name" :error="form.errors['zoho_sheet.sheet_name']" :name="''" :type="'text'" placeholder="Sheet1" />
                            </div>

                            <div class="pt-2">
                                <button type="button" @click="testConnection('zoho_sheet')"
                                    :disabled="testingZoho"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50">
                                    <svg v-if="testingZoho" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2m-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9Z"/></svg>
                                    {{ testingZoho ? $t('Testing...') : $t('Test Connection') }}
                                </button>
                                <span v-if="zohoTestResult !== null" class="ml-3 text-sm" :class="zohoTestResult ? 'text-green-600' : 'text-red-600'">
                                    {{ zohoTestMessage }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6">
                    <div class="flex gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="text-amber-600 shrink-0 mt-0.5">
                            <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2m-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9Z"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-amber-800 mb-1">{{ $t('How it works') }}</h4>
                            <ul class="text-sm text-amber-700 space-y-1 list-disc pl-4">
                                <li>{{ $t('When a new contact sends you a WhatsApp message for the first time, their details are automatically added to your configured sheet(s).') }}</li>
                                <li>{{ $t('Columns: First Name, Last Name, Phone, Email, Created At') }}</li>
                                <li>{{ $t('Make sure your sheet has these column headers in the first row.') }}</li>
                                <li>{{ $t('For Google Sheets: Enable Google Sheets API in Google Cloud Console and create credentials.') }}</li>
                                <li>{{ $t('For Zoho Sheet: Create an app in Zoho API Console (api-console.zoho.in), enter Client ID & Secret, then click "Connect with Zoho" to authorize.') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end">
                    <button type="submit" :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff7340] hover:from-[#e64900] hover:to-[#ff6330] text-white text-sm font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 transition-all duration-200 disabled:opacity-50">
                        <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        {{ form.processing ? $t('Saving...') : $t('Save Settings') }}
                    </button>
                </div>
            </form>
        </div>
    </SettingLayout>
</template>

<script setup>
import SettingLayout from "./Layout.vue";
import { ref } from 'vue';
import FormInput from '@/Components/FormInput.vue';
import FormToggleSwitch from '@/Components/FormToggleSwitch.vue';
import { useForm } from "@inertiajs/vue3";
import axios from 'axios';

const props = defineProps(['settings', 'sheetSyncConfig', 'modules', 'zohoAuthCode']);

const testingGoogle = ref(false);
const testingZoho = ref(false);
const googleTestResult = ref(null);
const googleTestMessage = ref('');
const zohoTestResult = ref(null);
const zohoTestMessage = ref('');
const zohoTokenMessage = ref('');
const zohoTokenSuccess = ref(false);
const showZohoGuide = ref(false);

const currentDomain = window.location.origin;
const redirectUri = window.location.origin + '/settings/sheet-sync/zoho/callback';

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
};

const form = useForm({
    google_sheets: {
        enabled: props.sheetSyncConfig?.google_sheets?.enabled ?? false,
        spreadsheet_id: props.sheetSyncConfig?.google_sheets?.spreadsheet_id ?? '',
        sheet_name: props.sheetSyncConfig?.google_sheets?.sheet_name ?? 'Sheet1',
        api_key: props.sheetSyncConfig?.google_sheets?.api_key ?? '',
        access_token: props.sheetSyncConfig?.google_sheets?.access_token ?? '',
    },
    zoho_sheet: {
        enabled: props.sheetSyncConfig?.zoho_sheet?.enabled ?? false,
        client_id: props.sheetSyncConfig?.zoho_sheet?.client_id ?? '',
        client_secret: props.sheetSyncConfig?.zoho_sheet?.client_secret ?? '',
        refresh_token: props.sheetSyncConfig?.zoho_sheet?.refresh_token ?? '',
        workbook_id: props.sheetSyncConfig?.zoho_sheet?.workbook_id ?? '',
        sheet_name: props.sheetSyncConfig?.zoho_sheet?.sheet_name ?? 'Sheet1',
        zoho_domain: props.sheetSyncConfig?.zoho_sheet?.zoho_domain ?? 'zoho.in',
    },
});

const submitForm = () => {
    form.post('/settings/sheet-sync', {
        preserveScroll: true,
    });
};

const testConnection = async (provider) => {
    if (provider === 'google_sheets') {
        testingGoogle.value = true;
        googleTestResult.value = null;
    } else {
        testingZoho.value = true;
        zohoTestResult.value = null;
    }

    try {
        const response = await axios.post('/settings/sheet-sync/test', { provider });
        if (provider === 'google_sheets') {
            googleTestResult.value = response.data.success;
            googleTestMessage.value = response.data.message;
        } else {
            zohoTestResult.value = response.data.success;
            zohoTestMessage.value = response.data.message;
        }
    } catch (error) {
        const msg = error.response?.data?.message || 'Connection test failed';
        if (provider === 'google_sheets') {
            googleTestResult.value = false;
            googleTestMessage.value = msg;
        } else {
            zohoTestResult.value = false;
            zohoTestMessage.value = msg;
        }
    } finally {
        if (provider === 'google_sheets') {
            testingGoogle.value = false;
        } else {
            testingZoho.value = false;
        }
    }
};

const connectZoho = async () => {
    // Save settings to DB first so they persist after OAuth redirect
    try {
        zohoTokenMessage.value = 'Saving settings before redirect...';
        zohoTokenSuccess.value = false;
        await axios.post('/settings/sheet-sync', {
            google_sheets: form.google_sheets,
            zoho_sheet: form.zoho_sheet,
        });
    } catch (e) {
        zohoTokenMessage.value = 'Failed to save settings before redirect. Please try again.';
        return;
    }

    const domain = form.zoho_sheet.zoho_domain || 'zoho.in';
    const clientId = form.zoho_sheet.client_id;
    const callbackUri = encodeURIComponent(window.location.origin + '/settings/sheet-sync/zoho/callback');
    const scope = encodeURIComponent('ZohoSheet.dataAPI.UPDATE,ZohoSheet.dataAPI.READ');
    const authUrl = `https://accounts.${domain}/oauth/v2/auth?response_type=code&client_id=${clientId}&scope=${scope}&redirect_uri=${callbackUri}&access_type=offline&prompt=consent`;
    window.location.href = authUrl;
};

// Auto-exchange auth code if returned from Zoho OAuth callback
if (props.zohoAuthCode) {
    console.log('Zoho OAuth callback detected. Auth code:', props.zohoAuthCode);
    console.log('Form client_id:', form.zoho_sheet.client_id);
    console.log('Form client_secret:', form.zoho_sheet.client_secret ? '***set***' : '***EMPTY***');
    console.log('Form zoho_domain:', form.zoho_sheet.zoho_domain);

    (async () => {
        try {
            zohoTokenMessage.value = 'Generating refresh token...';
            const response = await axios.post('/settings/sheet-sync/zoho/generate-token', {
                code: props.zohoAuthCode,
                client_id: form.zoho_sheet.client_id,
                client_secret: form.zoho_sheet.client_secret,
                zoho_domain: form.zoho_sheet.zoho_domain,
            });
            if (response.data.success) {
                form.zoho_sheet.refresh_token = response.data.refresh_token;
                zohoTokenSuccess.value = true;
                zohoTokenMessage.value = response.data.message;
            } else {
                zohoTokenSuccess.value = false;
                zohoTokenMessage.value = response.data.message;
            }
        } catch (error) {
            console.error('Zoho token exchange error:', error.response?.data);
            zohoTokenSuccess.value = false;
            zohoTokenMessage.value = error.response?.data?.message || 'Failed to generate token';
        }
    })();
}
</script>
