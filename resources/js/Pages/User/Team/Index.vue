<!-- <template>
    <AppLayout>
        <div
            class="bg-white md:bg-inherit pt-10 px-4 md:pt-8 md:p-8 rounded-[5px] text-[#000] h-full md:overflow-y-auto">
            <div class="flex justify-between">
                <div>
                    <h2 class="text-xl mb-1">{{ $t('Team') }}</h2>
                    <p class="mb-6 flex items-center text-sm leading-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                        </svg>
                        <span class="ml-1 mt-1">{{ $t('Add edit and delete accounts in your team') }}</span>
                    </p>
                </div>
                <div>
                    <button @click="openModal()"
                        class="rounded-md bg-primary px-3 py-2 text-sm text-white shadow-sm hover:bg-primary/90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{
                            $t('Invite user') }}</button>
                </div>
            </div>
            <TeamTable :rows="props.rows" @edit="openModal" />
        </div>

        <Modal :label="label" :isOpen=isOpenFormModal>
            <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-4">
                <form @submit.prevent="submitForm()" class="grid gap-x-6 gap-y-4 sm:grid-cols-6">
                    <FormInput v-model="form.email" :error="form.errors.email" :name="$t('Email')" :type="'email'"
                        :class="'sm:col-span-6'" :disabled="formMethod === 'put' ? true : false" />
                    <FormSelect v-model="form.role" :error="form.errors.role" :options="roleOptions" :name="$t('Role')"
                        :class="'sm:col-span-6'" :placeholder="$t('Select Role')" />
                    <div class="mt-4 flex">
                        <button type="button" @click.self="isOpenFormModal = false"
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
import AppLayout from "./../Layout/App.vue";
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import TeamTable from '@/Components/Tables/TeamTable.vue';
import Modal from '@/Components/Modal.vue';
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({ rows: Object, filters: Object });
const isOpenFormModal = ref(false);
const formUrl = ref('/team/invite');
const formMethod = ref('post');
const label = ref('Invite user');

const form = useForm({
    email: null,
    role: null,
});

const roleOptions = [
    { value: 'manager', label: trans('Manager') },
    { value: 'agent', label: trans('Agent') },
]

const openModal = (key, formData = {}) => {
    label.value = trans('Invite user');
    formUrl.value = '/team/invite';
    formMethod.value = 'post';

    if (key) {
        label.value = trans('Update user');
        formUrl.value = '/team/' + key.id;
        formMethod.value = 'put';
        form.email = key.email;
        form.role = key.role
        isOpenFormModal.value = true;
    } else {
        form.email = null;
        form.role = null;
        isOpenFormModal.value = true;
    }
}

const submitForm = () => {
    if (formMethod.value === 'post') {
        form.post(formUrl.value, {
            onFinish: () => {
                isOpenFormModal.value = false;
            }
        });
    } else {
        form.put(formUrl.value, {
            onFinish: () => {
                isOpenFormModal.value = false;
            }
        });
    }
}
</script> -->



<!-- ========================================= NEW UI CODE ====================================== -->


<template>
    <AppLayout>
        <div class="min-h-screen bg-white pt-10 px-4 md:pt-8 md:p-8">
            <!-- Hero Header Section -->
            <div class="mb-8">
                <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <!-- Left Section -->
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#ff5100] to-[#ff7a3d] flex items-center justify-center shadow-lg shadow-[#ff5100]/25">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="text-white">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                            </div>
                            <h2
                                class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-700 bg-clip-text text-transparent">
                                {{ $t('Team') }}
                            </h2>
                        </div>
                        <div class="flex items-start gap-2 text-gray-600">
                            <p class="text-sm md:text-base font-medium leading-relaxed">
                                {{ $t('Add edit and delete accounts in your team') }}
                            </p>
                        </div>
                    </div>

                    <!-- Right Section - Action Button -->
                    <div>
                        <button @click="openModal()"
                            class="group relative inline-flex items-center gap-3 px-6 py-3.5 bg-gradient-to-r from-primary via-orange-600 to-red-600 text-white text-sm font-bold rounded-2xl shadow-lg transform transition-all duration-300 overflow-hidden">
                            <div
                                class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300">
                            </div>
                            <div class="relative w-6 h-6 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <line x1="19" y1="8" x2="19" y2="14" />
                                    <line x1="22" y1="11" x2="16" y2="11" />
                                </svg>
                            </div>
                            <span class="relative">{{ $t('Invite user') }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="mb-6">
                <div class="flex items-center gap-2 p-1.5 bg-gray-100 rounded-2xl w-fit">
                    <button @click="activeTab = 'active'"
                        :class="[
                            'flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300',
                            activeTab === 'active' 
                                ? 'bg-white text-primary shadow-md' 
                                : 'text-gray-600 hover:text-gray-900 hover:bg-white/50'
                        ]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        {{ $t('Active Members') }}
                        <span class="px-2 py-0.5 text-xs font-bold bg-primary/10 text-primary rounded-full">
                            {{ props.rows.data?.length || 0 }}
                        </span>
                    </button>
                    <button v-if="isOwner" @click="activeTab = 'pending'"
                        :class="[
                            'flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300',
                            activeTab === 'pending' 
                                ? 'bg-white text-amber-600 shadow-md' 
                                : 'text-gray-600 hover:text-gray-900 hover:bg-white/50'
                        ]">
                        <Clock class="w-4 h-4" />
                        {{ $t('Pending Invitations') }}
                        <span v-if="props.pendingInvites?.length > 0" class="px-2 py-0.5 text-xs font-bold bg-amber-100 text-amber-700 rounded-full">
                            {{ props.pendingInvites.length }}
                        </span>
                    </button>
                </div>
            </div>

            <!-- Active Members Tab -->
            <div v-show="activeTab === 'active'">
                <TeamTable :rows="props.rows" @edit="openModal" @permissions="openPermissionsModal" />
            </div>

            <!-- Pending Invitations Tab -->
            <div v-show="activeTab === 'pending' && isOwner">
                <div v-if="props.pendingInvites && props.pendingInvites.length > 0">
                    <div class="bg-white rounded-3xl shadow-md border-2 border-primary/10 p-6">
                        <div class="space-y-3">
                            <div v-for="invite in props.pendingInvites" :key="invite.id"
                                class="bg-gradient-to-r from-gray-50 to-white rounded-2xl p-4 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <!-- Invite Info -->
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center">
                                            <Mail class="w-5 h-5 text-amber-600" />
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ invite.email }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">
                                                    <Shield class="w-3 h-3 mr-1" />
                                                    {{ $t(invite.role) }}
                                                </span>
                                                <span v-if="invite.is_expired" class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700 rounded-full">
                                                    {{ $t('Expired') }}
                                                </span>
                                                <span v-else class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">
                                                    {{ $t('Active') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Invite Link -->
                                    <div class="flex-1 md:max-w-md">
                                        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-xl border border-gray-200">
                                            <input type="text" :value="invite.invite_link" readonly
                                                class="flex-1 bg-transparent text-sm text-gray-600 border-none focus:ring-0 truncate" />
                                            <button @click="copyInviteLink(invite)"
                                                class="p-2 hover:bg-primary/10 rounded-lg transition-colors group"
                                                :title="$t('Copy link')">
                                                <Copy v-if="copiedLink !== invite.id" class="w-4 h-4 text-gray-500 group-hover:text-primary" />
                                                <svg v-else class="w-4 h-4 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="flex items-center gap-2">
                                        <button @click="resendInvite(invite)"
                                            class="p-2 hover:bg-blue-50 rounded-xl transition-colors group"
                                            :title="$t('Resend invitation')">
                                            <RefreshCw class="w-5 h-5 text-gray-500 group-hover:text-blue-600" />
                                        </button>
                                        <button @click="deleteInvite(invite)"
                                            class="p-2 hover:bg-red-50 rounded-xl transition-colors group"
                                            :title="$t('Delete invitation')">
                                            <Trash2 class="w-5 h-5 text-gray-500 group-hover:text-red-600" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State for Pending Invites -->
                <div v-else class="bg-white rounded-3xl shadow-md border-2 border-primary/10 p-16">
                    <div class="max-w-lg mx-auto text-center">
                        <div class="mb-6 relative inline-block">
                            <div class="bg-gradient-to-br from-amber-100 to-orange-100 p-8 rounded-[2rem] inline-block">
                                <Clock class="w-12 h-12 text-amber-500" />
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $t('No Pending Invitations') }}</h3>
                        <p class="text-gray-600 mb-6">{{ $t('All invitations have been accepted or there are no pending invites.') }}</p>
                        <button @click="openModal()"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary to-orange-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <line x1="19" y1="8" x2="19" y2="14" />
                                <line x1="22" y1="11" x2="16" y2="11" />
                            </svg>
                            {{ $t('Invite a team member') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Component -->
        <Modal :label="label" :isOpen="isOpenFormModal">
            <div class="mt-5">
                <div class="space-y-5">
                    <!-- Email Input -->
                    <FormInput v-model="form.email" :error="form.errors.email" :name="$t('Email')" :type="'email'"
                        :class="'w-full'" :disabled="formMethod === 'put' ? true : false" />

                    <!-- Role Select -->
                    <FormSelect v-model="form.role" :error="form.errors.role" :options="roleOptions" :name="$t('Role')"
                        :class="'w-full'" :placeholder="$t('Select Role')" />

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-3 pt-4">
                        <button type="button" @click.self="isOpenFormModal = false"
                            class="flex-1 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all duration-300 transform hover:scale-105">
                            {{ $t('Cancel') }}
                        </button>
                        <button type="button" @click="submitForm" :class="[
                            'flex-1 px-6 py-3 bg-primary text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-2',
                            { 'opacity-50 cursor-not-allowed': form.processing }
                        ]" :disabled="form.processing">
                            <svg v-if="form.processing" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" class="animate-spin">
                                <path fill="currentColor"
                                    d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                    opacity=".5" />
                                <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                    <animateTransform attributeName="transform" dur="1s" from="0 12 12"
                                        repeatCount="indefinite" to="360 12 12" type="rotate" />
                                </path>
                            </svg>
                            <span>{{ form.processing ? $t('Saving...') : $t('Save') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Permissions Modal -->
        <Modal :label="$t('Manage Permissions')" :isOpen="isOpenPermissionsModal">
            <div class="mt-5">
                <div v-if="selectedMember" class="mb-4">
                    <p class="text-sm text-gray-600">
                        {{ $t('Select which features') }} <span class="font-semibold">{{ selectedMember.name }}</span> {{ $t('can access') }}:
                    </p>
                </div>
                
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    <div v-for="perm in availablePermissions" :key="perm.key" 
                        class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors cursor-pointer"
                        @click="togglePermission(perm.key)">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-white shadow-sm flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-600">
                                    <rect width="7" height="9" x="3" y="3" rx="1" />
                                    <rect width="7" height="5" x="14" y="3" rx="1" />
                                    <rect width="7" height="9" x="14" y="12" rx="1" />
                                    <rect width="7" height="5" x="3" y="16" rx="1" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $t(perm.label) }}</p>
                                <p class="text-xs text-gray-500">{{ $t(perm.description) }}</p>
                            </div>
                        </div>
                        <div class="relative">
                            <input type="checkbox" 
                                :checked="permissionsForm.permissions.includes(perm.key)"
                                class="sr-only" />
                            <div :class="[
                                'w-14 h-7 rounded-full transition-colors duration-300 cursor-pointer',
                                permissionsForm.permissions.includes(perm.key) ? 'bg-primary' : 'bg-gray-300'
                            ]">
                                <div :class="[
                                    'absolute w-5 h-5 bg-white rounded-full shadow-md transform transition-transform duration-300 top-1',
                                    permissionsForm.permissions.includes(perm.key) ? 'translate-x-8' : 'translate-x-1'
                                ]"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-3 pt-6">
                    <button type="button" @click="isOpenPermissionsModal = false"
                        class="flex-1 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all duration-300">
                        {{ $t('Cancel') }}
                    </button>
                    <button type="button" @click="submitPermissions" :class="[
                        'flex-1 px-6 py-3 bg-primary text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2',
                        { 'opacity-50 cursor-not-allowed': permissionsForm.processing }
                    ]" :disabled="permissionsForm.processing">
                        <svg v-if="permissionsForm.processing" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            viewBox="0 0 24 24" class="animate-spin">
                            <path fill="currentColor"
                                d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                opacity=".5" />
                            <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                <animateTransform attributeName="transform" dur="1s" from="0 12 12"
                                    repeatCount="indefinite" to="360 12 12" type="rotate" />
                            </path>
                        </svg>
                        <span>{{ permissionsForm.processing ? $t('Saving...') : $t('Save Permissions') }}</span>
                    </button>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>


<script setup>
import AppLayout from "./../Layout/App.vue";
import { ref, computed } from 'vue';
import { useForm, usePage, router } from "@inertiajs/vue3";
import TeamTable from '@/Components/Tables/TeamTable.vue';
import Modal from '@/Components/Modal.vue';
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import { trans } from 'laravel-vue-i18n';
import { Copy, RefreshCw, Trash2, Clock, Mail, Shield } from 'lucide-vue-next';

const props = defineProps({ rows: Object, filters: Object, availablePermissions: Array, pendingInvites: Array });
const isOpenFormModal = ref(false);
const isOpenPermissionsModal = ref(false);
const formUrl = ref('/team/invite');
const formMethod = ref('post');
const label = ref('Invite user');
const selectedMember = ref(null);
const teamRole = computed(() => usePage().props.teamRole);
const isOwner = computed(() => teamRole.value === 'owner');
const copiedLink = ref(null);
const activeTab = ref('active');

const form = useForm({
    email: null,
    role: null,
});

const permissionsForm = useForm({
    permissions: [],
});

const roleOptions = [
    { value: 'manager', label: trans('Manager') },
    { value: 'agent', label: trans('Agent') },
]

const openModal = (key, formData = {}) => {
    label.value = trans('Invite user');
    formUrl.value = '/team/invite';
    formMethod.value = 'post';

    if (key) {
        label.value = trans('Update user');
        formUrl.value = '/team/' + key.id;
        formMethod.value = 'put';
        form.email = key.email;
        form.role = key.role
        isOpenFormModal.value = true;
    } else {
        form.email = null;
        form.role = null;
        isOpenFormModal.value = true;
    }
}

const openPermissionsModal = (member) => {
    if (!isOwner.value || member.role === 'owner') return;
    
    selectedMember.value = member;
    // Set current permissions
    permissionsForm.permissions = member.permissions || getDefaultPermissions(member.role);
    isOpenPermissionsModal.value = true;
}

const getDefaultPermissions = (role) => {
    const defaults = {
        'manager': ['dashboard', 'chats', 'contacts', 'campaigns', 'templates', 'support'],
        'agent': ['dashboard', 'chats', 'support'],
    };
    return defaults[role] || ['dashboard', 'chats'];
}

const togglePermission = (permKey) => {
    const index = permissionsForm.permissions.indexOf(permKey);
    if (index > -1) {
        permissionsForm.permissions.splice(index, 1);
    } else {
        permissionsForm.permissions.push(permKey);
    }
}

const submitForm = () => {
    if (formMethod.value === 'post') {
        form.post(formUrl.value, {
            onFinish: () => {
                isOpenFormModal.value = false;
            }
        });
    } else {
        form.put(formUrl.value, {
            onFinish: () => {
                isOpenFormModal.value = false;
            }
        });
    }
}

const submitPermissions = () => {
    permissionsForm.put('/team/' + selectedMember.value.uuid + '/permissions', {
        onSuccess: () => {
            isOpenPermissionsModal.value = false;
            selectedMember.value = null;
        }
    });
}

const copyInviteLink = (invite) => {
    navigator.clipboard.writeText(invite.invite_link);
    copiedLink.value = invite.id;
    setTimeout(() => {
        copiedLink.value = null;
    }, 2000);
}

const resendInvite = (invite) => {
    router.post('/team/invite/' + invite.id + '/resend');
}

const deleteInvite = (invite) => {
    if (confirm(trans('Are you sure you want to delete this invitation?'))) {
        router.delete('/team/invite/' + invite.id);
    }
}
</script>