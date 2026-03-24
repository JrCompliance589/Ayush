<!-- <template>
    <AppLayout>
        <div class="bg-white md:bg-inherit pt-0 px-4 md:pt-8 md:p-8 rounded-[5px] text-[#000] overflow-y-scroll">
            <div class="flex justify-between mt-8 md:mt-0">
                <div>
                    <h1 class="text-xl mb-1">{{ $t('Ticket ref') }}: {{ props.ticket.reference }}</h1>
                </div>
                <div>
                    <Link href="/admin/support"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-300 flex items-center space-x-2">
                    <ArrowLeft class="w-4 h-4" />
                    <span>{{ $t('Back') }}</span>
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-2 md:flex gap-x-6 mt-4 md:mt-10">
                <div class="col-span-2 md:order-1 md:w-[70%]">
                    <div class="bg-white md:border py-5 md:px-5 rounded-[0.5rem] mb-4 text-sm">
                        <h1 class="text-xl">{{ $t('Subject') }}: {{ props.ticket.subject }}</h1>
                        <div class="border border-dashed py-2 px-2 mt-8 bg-slate-100">{{ props.ticket.message }}</div>
                    </div>

                    <div v-if="props.ticket.status === 'open' || props.ticket.status === 'pending'"
                        class="bg-white border py-5 px-5 rounded-[0.5rem] mb-4">
                        <form @submit.prevent="submitForm()">
                            <FormTextArea v-model="form.message" :name="$t('Comment')" :type="'text'" :showLabel="true"
                                :error="form.errors.message" :textAreaRows="3" :class="'sm:col-span-6 mb-5'" />
                            <button type="submit"
                                class="mb-2 rounded-md bg-black px-3 py-2 text-sm text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2">{{
                                    $t('Add comment') }}</button>
                        </form>
                    </div>

                    <div v-for="(item, index) in props.ticket.comments_with_user" :key="index"
                        class="bg-white border py-5 px-5 rounded-[0.5rem] mb-2">
                        <div class="flex space-x-4 text-sm">
                            <div>
                                <div class="bg-slate-100 rounded-full h-12 w-12 p-4 flex justify-center items-center">
                                    {{ getInitials(item.user.first_name, item.user.last_name) }}
                                </div>
                            </div>
                            <div>
                                <div class="space-x-4 mb-1">
                                    <span>{{ item.user.first_name + ' ' + item.user.last_name }}</span>
                                    <span>{{ formatDateTime(item.created_at) }}</span>
                                </div>
                                <div>{{ item.message }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-2 w-[100%] md:order-2 md:w-[30%]">
                    <div class="bg-white md:border md:p-4 text-sm rounded-[0.5rem]">
                        <div class="bg-slate-100 p-2 rounded mb-2 space-x-3">
                            <span>{{ $t('Category') }}:</span>
                            <span class="capitalize">{{ props.ticket.category.name }}</span>
                        </div>
                        <div class="bg-slate-100 p-2 rounded mb-2 space-x-3">
                            <span>{{ $t('Status') }}:</span>
                            <span class="capitalize">{{ props.ticket.status }}</span>
                        </div>
                        <div class="bg-slate-100 p-2 rounded mb-2 space-x-3">
                            <span>{{ $t('Priority') }}:</span>
                            <span v-if="props.ticket.status === 'open' || props.ticket.status === 'pending'"
                                class="capitalize">
                                <FormSelect v-model="priority" @update:modelValue="updatePriority"
                                    :options="priorityOptions" :class="'w-full'" :placeholder="'Not Set'" />
                            </span>
                            <span v-else class="capitalize">{{ props.ticket.priority }}</span>
                        </div>
                        <div class="bg-slate-100 p-2 rounded mb-2 space-x-3">
                            <span>{{ $t('Assigned to') }}:</span>
                            <span v-if="props.ticket.status === 'open' || props.ticket.status === 'pending'"
                                class="capitalize">
                                <FormSelect v-model="user" @update:modelValue="updateUser" :options="userOptions"
                                    :class="'w-full'" :placeholder="'Select User'" />
                            </span>
                            <span v-else class="capitalize">{{ props.ticket?.agent ? props.ticket?.agent?.first_name + `
                                ` + props.ticket?.agent?.last_name : $t('Not set') }}</span>
                        </div>
                        <div class="bg-slate-100 p-2 rounded mb-2 space-x-3">
                            <span>{{ $t('Date created') }}:</span>
                            <span>{{ formatDateTime(props.ticket.created_at) }}</span>
                        </div>
                        <div v-if="props.ticket.status === 'open' || props.ticket.status === 'pending'"
                            class="grid grid-cols-2 space-x-2 mt-4">
                            <button @click="changeTicketStatus('closed')"
                                class="mb-2 rounded-md bg-black px-3 py-2 text-sm text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2">{{
                                    $t('Close ticket') }}</button>
                            <button @click="changeTicketStatus('resolved')"
                                class="mb-2 rounded-md bg-black px-3 py-2 text-sm text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2">{{
                                    $t('Mark as resolved') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from './../Layout/App.vue';
import axios from 'axios';
import { Link, router, useForm } from "@inertiajs/vue3";
import { ref, onMounted } from 'vue';
import FormSelect from '@/Components/FormSelect.vue';
import FormTextArea from '@/Components/FormTextArea.vue';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps(['title', 'ticket', 'users']);
const priority = ref(props.ticket.priority);
const user = ref(props.ticket.assigned_to);
const userOptions = ref([]);
const form = useForm({
    'message': null,
});
const priorityOptions = ref([
    { value: 'critical', label: 'Critical' },
    { value: 'high', label: 'High' },
    { value: 'medium', label: 'Medium' },
    { value: 'low', label: 'Low' },
])

const formatDateTime = (value) => {
    const options = { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(value).toLocaleString('en-US', options);
};

const getInitials = (firstName, lastName) => {
    const firstInitial = firstName.charAt(0).toUpperCase();
    const lastInitial = lastName.charAt(0).toUpperCase();

    return `${firstInitial}${lastInitial}`;
}

const submitForm = () => {
    form.post('/admin/support/' + props.ticket.uuid + '/comment', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

const changeTicketStatus = (status) => {
    router.post('/admin/support/' + props.ticket.uuid + '/status', { status: status });
}

const transformUsers = (users) => {
    return users.map((user) => ({
        value: user.id,
        label: user.first_name + ' ' + user.last_name,
    }));
};

const updateUser = async (value) => {
    try {
        const response = await axios.post('/admin/support/' + props.ticket.uuid + '/assign', { user: value });
        //console.log('Response:', response.data);
    } catch (error) {
        //console.error('Error making POST request:', error);
    }
}

const updatePriority = async (value) => {
    try {
        const response = await axios.post('/admin/support/' + props.ticket.uuid + '/priority', { priority: value });
        //console.log('Response:', response.data);
    } catch (error) {
        //console.error('Error making POST request:', error);
    }
}

onMounted(() => {
    userOptions.value = transformUsers(props.users);
});
</script> -->


<!-- ============================================ NEW UI ============================================= -->


<template>
    <AppLayout>
        <div
            class="min-h-screen bg-gradient-to-br from-slate-50 via-orange-50/30 to-slate-50 pt-6 px-4 md:pt-8 md:px-8 pb-12">
            <div class="max-w-7xl mx-auto">
                <!-- Animated Background Elements -->
                <div class="absolute top-20 right-10 w-72 h-72 bg-[#ff5100]/5 rounded-full blur-3xl -z-10"></div>
                <div class="absolute bottom-20 left-10 w-96 h-96 bg-[#ff5100]/3 rounded-full blur-3xl -z-10"></div>

                <!-- Header Section -->
                <div class="mb-8">
                    <div
                        class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-orange-100/50 p-6 md:p-8 relative overflow-hidden">
                        <!-- Decorative Pattern -->
                        <div class="absolute top-0 right-0 w-48 h-48 opacity-5">
                            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                <path fill="#ff5100"
                                    d="M47.1,-57.1C59.9,-45.8,68.4,-28.9,71.1,-10.7C73.8,7.5,70.7,27,60.4,42.2C50.1,57.4,32.6,68.3,13.5,72.8C-5.6,77.3,-26.3,75.4,-42.8,66.1C-59.3,56.8,-71.6,40.1,-75.1,21.8C-78.6,3.5,-73.3,-16.4,-64.3,-32.5C-55.3,-48.6,-42.6,-60.9,-27.8,-71.3C-13,-81.7,3.9,-90.2,20.3,-87.8C36.7,-85.4,52.6,-72.1,47.1,-57.1Z"
                                    transform="translate(100 100)" />
                            </svg>
                        </div>

                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 relative z-10">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <div
                                        class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7a3d] rounded-2xl shadow-lg shadow-orange-500/25">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                                            viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">
                                                {{ $t('Ticket') }}
                                            </h1>
                                            <span
                                                class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-gradient-to-r from-[#ff5100] to-[#ff7a3d] text-white text-sm font-bold rounded-xl shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path
                                                        d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z">
                                                    </path>
                                                    <polyline points="14 2 14 8 20 8"></polyline>
                                                </svg>
                                                {{ props.ticket.reference }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-shrink-0">
                                <Link href="/admin/support"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-white hover:bg-slate-50 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-200 shadow-sm hover:shadow-md">
                                <ArrowLeft class="w-4 h-4" />
                                <span>{{ $t('Back') }}</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Main Content Column -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Customer Details Card -->
                        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-orange-100/50 p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="text-[#ff5100]">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">{{ $t(`Customer
                                    Details`) }}</h3>
                            </div>

                            <div class="space-y-4">
                                <!-- Customer Avatar & Name -->
                                <div class="flex items-center gap-3 pb-4 border-b border-slate-200">
                                    <div
                                        class="w-14 h-14 rounded-full bg-gradient-to-br from-[#ff5100] to-[#ff7a3d] flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-orange-500/25">
                                        SA
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-lg">SAHEB</h4>
                                        <p class="text-xs text-slate-500">Customer ID: #12345</p>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Email -->
                                    <div
                                        class="p-3 bg-gradient-to-br from-slate-50 to-orange-50/20 rounded-xl border border-slate-200">
                                        <div class="flex items-start gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="text-[#ff5100] mt-0.5 flex-shrink-0">
                                                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                                            </svg>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold text-slate-600 uppercase mb-1">Email</p>
                                                <p class="text-sm text-slate-800 break-all">saheb@jrcompliance.com</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Phone -->
                                    <div
                                        class="p-3 bg-gradient-to-br from-slate-50 to-orange-50/20 rounded-xl border border-slate-200">
                                        <div class="flex items-start gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="text-[#ff5100] mt-0.5 flex-shrink-0">
                                                <path
                                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                                </path>
                                            </svg>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold text-slate-600 uppercase mb-1">Phone</p>
                                                <p class="text-sm text-slate-800">+918800281734</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Member Since -->
                                    <div
                                        class="p-3 bg-gradient-to-br from-slate-50 to-orange-50/20 rounded-xl border border-slate-200">
                                        <div class="flex items-start gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="text-[#ff5100] mt-0.5 flex-shrink-0">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold text-slate-600 uppercase mb-1">Member
                                                    Since
                                                </p>
                                                <p class="text-sm text-slate-800">Sep 1, 2025</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Total Tickets -->
                                    <div
                                        class="p-3 bg-gradient-to-br from-slate-50 to-orange-50/20 rounded-xl border border-slate-200">
                                        <div class="flex items-start gap-2">

                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-building2-icon lucide-building-2 text-[#ff5100] mt-0.5 flex-shrink-0">
                                                <path d="M10 12h4" />
                                                <path d="M10 8h4" />
                                                <path d="M14 21v-3a2 2 0 0 0-4 0v3" />
                                                <path
                                                    d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2" />
                                                <path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16" />
                                            </svg>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold text-slate-600 uppercase mb-1">
                                                    Organization</p>
                                                <p class="text-sm text-slate-800 font-bold">Jr Compliance</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Details Card -->
                        <div
                            class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-orange-100/50 overflow-hidden">
                            <div class="p-6 md:p-8">
                                <!-- Subject Section -->
                                <div class="mb-6">
                                    <div class="flex items-center gap-2 mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="text-[#ff5100]">
                                            <line x1="12" y1="1" x2="12" y2="23"></line>
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                        </svg>
                                        <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider">{{
                                            $t('Subject') }}</h3>
                                    </div>
                                    <h2 class="text-2xl font-bold text-slate-800 mb-4">{{ props.ticket.subject }}</h2>
                                    <div
                                        class="p-4 bg-gradient-to-br from-slate-50 to-orange-50/30 rounded-2xl border-2 border-dashed border-slate-200">
                                        <p class="text-slate-700 leading-relaxed whitespace-pre-wrap">{{
                                            props.ticket.message }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add Comment Section (if ticket is open/pending) -->
                        <div v-if="props.ticket.status === 'open' || props.ticket.status === 'pending'"
                            class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-orange-100/50 p-6 md:p-8">
                            <div class="flex items-center gap-2 mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="text-[#ff5100]">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                    <line x1="9" y1="10" x2="15" y2="10"></line>
                                    <line x1="12" y1="7" x2="12" y2="13"></line>
                                </svg>
                                <h3 class="text-lg font-bold text-slate-800">{{ $t('Add Comment') }}</h3>
                            </div>

                            <form @submit.prevent="submitForm()" class="space-y-4">
                                <div class="relative">
                                    <FormTextArea v-model="form.message" :name="''" :type="'text'" :showLabel="false"
                                        :error="form.errors.message" :textAreaRows="3" :class="'custom-textarea'" />
                                </div>

                                <button type="submit"
                                    class="group relative inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff7a3d] hover:from-[#ff5100] hover:to-[#ff5100] text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-orange-500/30 transition-all duration-300 overflow-hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="relative z-10">
                                        <line x1="22" y1="2" x2="11" y2="13"></line>
                                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                    </svg>
                                    <span class="relative z-10">{{ $t('Add Comment') }}</span>
                                </button>
                            </form>
                        </div>


                        <!-- Comments Section -->
                        <div class="space-y-4">
                            <div v-for="(item, index) in props.ticket.comments_with_user" :key="index"
                                class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-lg border border-slate-200 hover:border-orange-200/50 p-6 transition-all duration-300 hover:shadow-xl">
                                <div class="flex gap-4">
                                    <!-- Avatar -->
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-12 h-12 rounded-full bg-gradient-to-br from-[#ff5100] to-[#ff7a3d] flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-orange-500/25">
                                            {{ getInitials(item.user.first_name, item.user.last_name) }}
                                        </div>
                                    </div>

                                    <!-- Comment Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3 mb-2 flex-wrap">
                                            <h4 class="font-bold text-slate-800">
                                                {{ item.user.first_name + ' ' + item.user.last_name }}
                                            </h4>
                                            <span class="flex items-center gap-1.5 text-xs text-slate-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                                {{ formatDateTime(item.created_at) }}
                                            </span>
                                        </div>
                                        <div class="text-slate-700 leading-relaxed whitespace-pre-wrap">
                                            {{ item.message }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Column -->

                    <div class="lg:col-span-1">
                        <div
                            class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-orange-100/50 p-6 sticky top-8">
                            <!-- Ticket Information -->
                            <div class="mb-6">
                                <div class="flex items-center gap-2 mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="text-[#ff5100]">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                    </svg>
                                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">{{ $t(`Ticket
                                        Information`) }}</h3>
                                </div>

                                <div class="space-y-3">
                                    <!-- Category -->
                                    <div
                                        class="p-3 bg-gradient-to-br from-slate-50 to-orange-50/20 rounded-xl border border-slate-200">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold text-slate-600 uppercase">{{
                                                $t('Category') }}</span>
                                            <span class="text-sm font-bold text-slate-800 capitalize">{{
                                                props.ticket.category.name }}</span>
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div
                                        class="p-3 bg-gradient-to-br from-slate-50 to-orange-50/20 rounded-xl border border-slate-200">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold text-slate-600 uppercase">{{ $t('Status')
                                            }}</span>
                                            <span
                                                :class="['px-3 py-1 rounded-lg text-xs font-bold border', getStatusColor(props.ticket.status)]">
                                                {{ $t(props.ticket.status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Priority -->
                                    <div
                                        class="p-3 bg-gradient-to-br from-slate-50 to-orange-50/20 rounded-xl border border-slate-200">
                                        <div class="flex flex-col gap-2">
                                            <span class="text-xs font-semibold text-slate-600 uppercase">{{
                                                $t('Priority') }}</span>
                                            <div
                                                v-if="props.ticket.status === 'open' || props.ticket.status === 'pending'">
                                                <FormSelect v-model="priority" @update:modelValue="updatePriority"
                                                    :options="priorityOptions" :class="'custom-select-mini'"
                                                    :placeholder="'Not Set'" />
                                            </div>
                                            <span v-else
                                                :class="['px-3 py-1 rounded-lg text-xs font-bold border inline-block', getPriorityColor(props.ticket.priority)]">
                                                {{ props.ticket.priority ?? $t('Not set') }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Assigned To -->
                                    <div
                                        class="p-3 bg-gradient-to-br from-slate-50 to-orange-50/20 rounded-xl border border-slate-200">
                                        <div class="flex flex-col gap-2">
                                            <span class="text-xs font-semibold text-slate-600 uppercase">{{
                                                $t('Assigned to') }}</span>
                                            <div
                                                v-if="props.ticket.status === 'open' || props.ticket.status === 'pending'">
                                                <FormSelect v-model="user" @update:modelValue="updateUser"
                                                    :options="userOptions" :class="'custom-select-mini'"
                                                    :placeholder="'Select User'" />
                                            </div>
                                            <span v-else class="text-sm font-medium text-slate-700">
                                                {{ props.ticket?.agent ? props.ticket?.agent?.first_name + ' ' +
                                                    props.ticket?.agent?.last_name : $t('Not set') }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Date Created -->
                                    <div
                                        class="p-3 bg-gradient-to-br from-slate-50 to-orange-50/20 rounded-xl border border-slate-200">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold text-slate-600 uppercase">{{ $t(`Date
                                                Created`) }}</span>
                                            <span class="text-xs font-medium text-slate-700">{{
                                                formatDateTime(props.ticket.created_at) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div v-if="props.ticket.status === 'open' || props.ticket.status === 'pending'"
                                class="space-y-3 pt-6 border-t border-slate-200">
                                <button @click="changeTicketStatus('closed')"
                                    class="w-full group relative inline-flex items-center justify-center gap-2 px-4 py-3 bg-slate-700 hover:bg-slate-800 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-r from-slate-800 to-slate-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="relative z-10">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="15" y1="9" x2="9" y2="15"></line>
                                        <line x1="9" y1="9" x2="15" y2="15"></line>
                                    </svg>
                                    <span class="relative z-10">{{ $t('Close Ticket') }}</span>
                                </button>

                                <button @click="changeTicketStatus('resolved')"
                                    class="w-full group relative inline-flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl shadow-md shadow-green-500/30 hover:shadow-lg hover:shadow-green-500/40 transition-all duration-300 overflow-hidden">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-r from-emerald-600 to-green-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round" class="relative z-10">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span class="relative z-10">{{ $t('Mark as Resolved') }}</span>
                                </button>
                            </div>

                            <!-- Status Badge (if closed/resolved) -->
                            <div v-else class="pt-6 border-t border-slate-200">
                                <div
                                    class="p-4 bg-gradient-to-br from-slate-100 to-slate-50 rounded-xl border-2 border-slate-200 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="mx-auto mb-2 text-slate-400">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                    <p class="text-sm font-bold text-slate-700 capitalize">{{ $t('Ticket') }} {{
                                        $t(props.ticket.status) }}</p>
                                    <p class="text-xs text-slate-500 mt-1">{{ $t('No further actions available') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from './../Layout/App.vue';
import axios from 'axios';
import { Link, router, useForm } from "@inertiajs/vue3";
import { ref, onMounted } from 'vue';
import FormSelect from '@/Components/FormSelect.vue';
import FormTextArea from '@/Components/FormTextArea.vue';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps(['title', 'ticket', 'users']);
// console.log('props', props?.users);
const priority = ref(props.ticket.priority);
const user = ref(props.ticket.assigned_to);
const userOptions = ref([]);
const form = useForm({
    'message': null,
});
const priorityOptions = ref([
    { value: 'critical', label: 'Critical' },
    { value: 'high', label: 'High' },
    { value: 'medium', label: 'Medium' },
    { value: 'low', label: 'Low' },
])

const formatDateTime = (value) => {
    const options = { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(value).toLocaleString('en-US', options);
};

const getInitials = (firstName, lastName) => {
    const firstInitial = firstName.charAt(0).toUpperCase();
    const lastInitial = lastName.charAt(0).toUpperCase();
    return `${firstInitial}${lastInitial}`;
}

const submitForm = () => {
    form.post('/admin/support/' + props.ticket.uuid + '/comment', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

const changeTicketStatus = (status) => {
    router.post('/admin/support/' + props.ticket.uuid + '/status', { status: status });
}

const transformUsers = (users) => {
    return users.map((user) => ({
        value: user.id,
        label: user.first_name + ' ' + user.last_name,
    }));
};

const updateUser = async (value) => {
    try {
        const response = await axios.post('/admin/support/' + props.ticket.uuid + '/assign', { user: value });
    } catch (error) {
        console.error('Error making POST request:', error);
    }
}

const updatePriority = async (value) => {
    try {
        const response = await axios.post('/admin/support/' + props.ticket.uuid + '/priority', { priority: value });
    } catch (error) {
        console.error('Error making POST request:', error);
    }
}

const getStatusColor = (status) => {
    const colors = {
        'open': 'bg-blue-100 text-blue-700 border-blue-200',
        'pending': 'bg-yellow-100 text-yellow-700 border-yellow-200',
        'resolved': 'bg-green-100 text-green-700 border-green-200',
        'closed': 'bg-slate-100 text-slate-700 border-slate-200',
    };
    return colors[status?.toLowerCase()] || 'bg-slate-100 text-slate-700 border-slate-200';
}

const getPriorityColor = (priority) => {
    const colors = {
        'critical': 'bg-purple-100 text-purple-700 border-purple-200',
        'high': 'bg-red-100 text-red-700 border-red-200',
        'medium': 'bg-orange-100 text-orange-700 border-orange-200',
        'low': 'bg-green-100 text-green-700 border-green-200',
    };
    return colors[priority?.toLowerCase()] || 'bg-slate-100 text-slate-700 border-slate-200';
}

onMounted(() => {
    userOptions.value = transformUsers(props.users);
});
</script>

<style scoped>
/* Smooth transitions */
* {
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Custom styles for FormTextArea */
:deep(.custom-textarea textarea) {
    width: 100%;
    padding: 0.875rem 1rem;
    background-color: rgb(248 250 252);
    border: 2px solid rgb(226 232 240);
    border-radius: 0.75rem;
    outline: none;
    color: rgb(51 65 85);
    transition: all 0.2s;
    resize: none;
}

:deep(.custom-textarea textarea:focus) {
    border-color: #ff5100;
    background-color: white;
    box-shadow: 0 0 0 4px rgba(255, 81, 0, 0.1);
    transform: translateY(-1px);
}

:deep(.custom-textarea .border-red-300) {
    border-color: rgb(252 165 165);
    background-color: rgb(254 242 242);
}

/* Custom styles for FormSelect (mini version in sidebar) */
:deep(.custom-select-mini select) {
    width: 100%;
    padding: 0.5rem 0.75rem;
    background-color: white;
    border: 1.5px solid rgb(226 232 240);
    border-radius: 0.5rem;
    outline: none;
    color: rgb(51 65 85);
    font-size: 0.875rem;
    transition: all 0.2s;
    appearance: none;
    cursor: pointer;
}

:deep(.custom-select-mini select:focus) {
    border-color: #ff5100;
    box-shadow: 0 0 0 3px rgba(255, 81, 0, 0.1);
}

/* Hide labels from form components */
:deep(.custom-textarea label),
:deep(.custom-select-mini label) {
    display: none;
}

/* Custom scrollbar for textarea */
:deep(textarea::-webkit-scrollbar) {
    width: 8px;
}

:deep(textarea::-webkit-scrollbar-track) {
    background: #f1f5f9;
    border-radius: 10px;
}

:deep(textarea::-webkit-scrollbar-thumb) {
    background: #cbd5e1;
    border-radius: 10px;
}

:deep(textarea::-webkit-scrollbar-thumb:hover) {
    background: #ff5100;
}

/* Sticky sidebar behavior */
@media (min-width: 1024px) {
    .sticky {
        position: sticky;
        top: 2rem;
    }
}

/* Smooth hover effect */
.group:hover svg {
    transform: scale(1.1);
    transition: transform 0.2s ease;
}
</style>
