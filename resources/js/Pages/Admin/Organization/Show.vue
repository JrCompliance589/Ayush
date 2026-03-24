<!-- <template>
    <AppLayout>
        <div class="bg-white md:bg-inherit pt-0 px-4 md:pt-8 md:p-8 rounded-[5px] text-[#000] h-full overflow-y-scroll">
            <div v-if="props.organization === null" class="md:flex justify-between hidden">
                <div>
                    <h1 class="text-xl mb-1">{{ $t('Create organization') }}</h1>
                    <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                        </svg>
                        <span class="ml-1 mt-1">{{ $t('Create organization') }}</span>
                    </p>
                </div>
                <div>
                    <Link href="/admin/organizations"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-300 flex items-center space-x-2">
                    <ArrowLeft class="w-4 h-4" />
                    <span>{{ $t('Back') }}</span></Link>
                </div>
            </div>
            <div v-if="props.organization" class="flex justify-between">
                <div class="flex items-center space-x-2 mb-8 mt-8 md:mt-0">
                    <div class="rounded-full p-1">
                        <div class="rounded-full w-32 h-32 bg-slate-200 flex justify-center items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24">
                                <g fill="none" fill-rule="evenodd">
                                    <path
                                        d="M24 0v24H0V0h24ZM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018Zm.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022Zm-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01l-.184-.092Z" />
                                    <path fill="currentColor"
                                        d="M17 3.722v5.497l2.864.716A1.5 1.5 0 0 1 21 11.39V19a1 1 0 1 1 0 2H3a1 1 0 1 1 0-2v-7.69a1.5 1.5 0 0 1 .83-1.343L7 8.382V6.347a1.5 1.5 0 0 1 .973-1.405l7-2.625A1.5 1.5 0 0 1 17 3.722Zm-2 .721l-6 2.25V19h6V4.443Zm2 6.838V19h2v-7.22l-2-.5Zm-10-.663l-2 1V19h2v-8.382Z" />
                                </g>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-lg">{{ props.organization.name }}</h1>
                        <h2 class="text-sm">{{ $t('Subscription plan') }}: {{
                            props.organization?.subscription?.plan?.name ?? 'Not set' }} <span
                                v-if="props.organization?.subscription?.status === 'trial'"
                                class="bg-[#000] text-white text-xs py-1 px-2 rounded-md">{{ $t('Trial period')
                                }}</span></h2>
                        <h2 class="text-sm capitalize">{{ $t('Valid until') }}: {{
                            props.organization?.subscription?.valid_until ?? 'Not set' }}</h2>
                    </div>
                </div>
                <div class="flex space-x-4">
                    <div>
                        <button type="button" @click="toggleFormModal()"
                            class="rounded-md bg-indigo-600 px-3 h-9 py-2 text-sm text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{
                                $t('Create transaction') }}</button>
                    </div>
                    <Link href="/admin/organizations"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-300 flex items-center space-x-2">
                    <ArrowLeft class="w-4 h-4" />
                    <span>{{ $t('Back') }}</span></Link>
                </div>
            </div>
            <div v-if="props.organization" class="flex border-b space-x-4 text-sm">
                <div @click="changeTab('organization')" class="cursor-pointer px-2 py-2"
                    :class="tab === 'organization' ? 'bg-white' : ''">{{ $t('User details') }}</div>
                <div @click="changeTab('team')" class="cursor-pointer px-2 py-2"
                    :class="tab === 'team' ? 'bg-white' : ''">{{ $t('Team') }}</div>
                <div @click="changeTab('billing')" class="cursor-pointer px-2 py-2"
                    :class="tab === 'billing' ? 'bg-white' : ''">{{ $t('Billing history') }}</div>
            </div>
            <div v-if="props.organization && tab === 'team'" class="pt-5">
                <UserTable :rows="props.users" :filters="props.filters" :type="'user'" :showRole="true"
                    :showDeleteBtn="false" />
            </div>
            <div v-if="props.organization && tab === 'billing'" class="pt-5">
                <BillingTable :rows="props.invoices" :filters="props.filters" :uuid="props.organization.uuid" />
            </div>
            <form v-if="tab === 'organization'" @submit.prevent="submitForm()"
                class="bg-white py-5 px-5 rounded-bl-[0.5rem] rounded-br-[0.5rem]">
                <div class="sm:flex border-b py-5">
                    <div class="hidden sm:block sm:w-[40%] mb-1">
                        <h3 class="text-sm tracking-[0px]">{{ $t('Organization details') }}</h3>
                    </div>
                    <div class="sm:w-[60%] sm:flex space-x-6">
                        <div class="sm:w-[80%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormInput v-model="form.name" :name="$t('Name')" :error="form.errors.name" :type="'text'"
                                :class="'sm:col-span-6'" />
                            <FormSelect v-model="form.plan" :name="$t('Subscription plan')" :error="form.errors.plan"
                                :options="roleOptions()" :type="'text'" :class="'sm:col-span-6'" />
                        </div>
                    </div>
                </div>
                <div v-if="props.organization === null" class="sm:flex border-b py-5">
                    <div class="hidden sm:block sm:w-[40%] mb-1">
                        <h3 class="text-sm tracking-[0px]">{{ $t('User details') }}</h3>
                        <p class="text-sm text-gray-500">{{ $t(`Enter the details of the main administrative user of
                            this organization`) }}</p>
                    </div>
                    <div class="sm:w-[60%]">
                        <div
                            class="sm:w-[80%] flex justify-between bg-primary rounded-[5px] p-1 space-x-2 mb-4 text-white">
                            <button type="button" class="w-[50%] rounded-[5px] px-1 py-2 text-sm"
                                :class="{ 'bg-white text-black': form.create_user === 1 }" @click="switchUserType(1)">{{
                                    $t('Add user') }}</button>
                            <button type="button" class="w-[50%] rounded-[5px] px-1 py-2 text-sm"
                                :class="{ 'bg-white text-black': form.create_user === 0 }" @click="switchUserType(0)">{{
                                    $t('Select existing user') }}</button>
                        </div>
                        <div v-if="form.create_user === 1" class="sm:w-[80%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormInput v-model="form.first_name" :name="$t('First name')"
                                :error="form.errors.first_name" :type="'text'" :class="'sm:col-span-3'" />
                            <FormInput v-model="form.last_name" :name="$t('Last name')" :error="form.errors.last_name"
                                :type="'text'" :class="'sm:col-span-3'" />
                            <FormInput v-model="form.email" :name="$t('Email')" :error="form.errors.email"
                                :type="'text'" :class="'sm:col-span-3'" />
                            <FormPhoneInput v-model="form.phone" :name="$t('Phone')" :error="form.errors.phone"
                                :type="'text'" :class="'sm:col-span-3'" />
                            <FormInput v-model="form.password" :name="$t('Password')" :error="form.errors.password"
                                :type="'password'" :class="'sm:col-span-3'" />
                            <FormInput v-model="form.password_confirmation" :name="$t('Confirm password')"
                                :error="form.errors.password_confirmation" :type="'password'"
                                :class="'sm:col-span-3'" />
                        </div>
                        <div v-else class="sm:w-[80%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormInput v-model="form.email" :name="$t('Email')" :error="form.errors.email"
                                :type="'text'" :class="'sm:col-span-6'" />
                        </div>
                    </div>
                </div>
                <div class="sm:flex py-5">
                    <div class="hidden sm:block w-[40%] mb-1">
                        <h3 class="text-sm tracking-[0px]">{{ $t('Address details') }}</h3>
                    </div>
                    <div class="sm:w-[60%] sm:flex space-x-6">
                        <div class="sm:w-[80%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormInput v-model="form.street" :name="$t('Street')" :error="form.errors.street"
                                :type="'text'" :class="'sm:col-span-6'" />
                            <FormInput v-model="form.city" :name="$t('City')" :error="form.errors.city" :type="'text'"
                                :class="'sm:col-span-3'" />
                            <FormInput v-model="form.state" :name="$t('State')" :error="form.errors.state"
                                :type="'text'" :class="'sm:col-span-3'" />
                            <FormInput v-model="form.zip" :name="$t('Zip code')" :error="form.errors.zip" :type="'text'"
                                :class="'sm:col-span-3'" />
                            <FormInput v-model="form.country" :name="$t('Country')" :error="form.errors.country"
                                :type="'text'" :class="'sm:col-span-3'" />
                        </div>
                    </div>
                </div>
                <div class="py-6">
                    <button type="submit"
                        class="float-right flex items-center space-x-4 rounded-md bg-black px-3 py-2 text-sm text-white shadow-sm hover:bg-slate-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        {{ $t('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
    <Modal :label="$t('Create transaction')" :isOpen=isOpenFormModal>
        <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-4">
            <form @submit.prevent="submitForm1()" class="">

                <div class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6 sm:col-span-4">
                    <FormSelect v-model="form1.type" :name="$t('Transaction type')" :error="form1.errors.type"
                        :options="typeOptions" :class="'sm:col-span-3'" />
                    <FormInput v-model="form1.amount" :name="$t('Amount')" :error="form1.errors.amount" :type="'number'"
                        :class="'sm:col-span-3'" />
                    <FormSelect v-if="form1.type === 'payment'" v-model="form1.method" :name="$t('Payment method')"
                        :error="form1.errors.method" :options="paymentOptions" :class="'sm:col-span-6'" />
                    <FormInput v-else v-model="form1.description" :name="$t('Description')"
                        :error="form1.errors.description" :type="'text'" :class="'sm:col-span-6'" />
                </div>

                <div class="bg-red-800 py-1 px-2 rounded-[5px] mt-6">
                    <p class="text-[12px] flex items-center space-x-2 text-white">
                        <svg class="text-white" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            viewBox="0 0 20 20">
                            <path fill="currentColor" fill-rule="evenodd"
                                d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625zM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5m0 9a1 1 0 1 0 0-2a1 1 0 0 0 0 2"
                                clip-rule="evenodd" />
                        </svg>
                        <span>{{ $t('You can\'t undo this transaction once you save it') }}</span>
                    </p>
                </div>

                <div class="mt-6 flex">
                    <button type="button" @click="toggleFormModal()"
                        class="inline-flex justify-center rounded-md border border-transparent bg-slate-50 px-4 py-2 text-sm text-slate-500 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 mr-4">{{
                            $t('Cancel') }}</button>
                    <button
                        :class="['inline-flex justify-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2', { 'opacity-50': isLoading }]"
                        :disabled="isLoading">
                        <svg v-if="isLoading" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
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
</template>
<script setup>
import AppLayout from "./../Layout/App.vue";
import { ref } from 'vue';
import { Link, useForm } from "@inertiajs/vue3";
import FormInput from '@/Components/FormInput.vue';
import FormPhoneInput from '@/Components/FormPhoneInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import BillingTable from '@/Components/Tables/BillingTable.vue';
import Modal from '@/Components/Modal.vue';
import UserTable from '@/Components/Tables/UserTable.vue';
import { trans } from 'laravel-vue-i18n';
import { ArrowLeft } from "lucide-vue-next";

const props = defineProps({
    showAddBtn: {
        type: Boolean,
        default: true
    },
    title: String,
    organization: Object,
    users: Object,
    invoices: Object,
    plans: Object,
    filters: Object,
    mode: String
});

const tab = ref('organization');

const getAddressDetail = (value, key) => {
    if (value) {
        const address = JSON.parse(value);
        return address?.[key] ?? null;
    } else {
        return null;
    }
};

const form = useForm({
    name: props.organization?.name,
    plan: props.organization?.subscription?.plan?.uuid,
    create_user: 1,
    first_name: null,
    last_name: null,
    email: null,
    phone: null,
    password: null,
    password_confirmation: null,
    street: getAddressDetail(props.organization?.address, 'street'),
    city: getAddressDetail(props.organization?.address, 'city'),
    state: getAddressDetail(props.organization?.address, 'state'),
    zip: getAddressDetail(props.organization?.address, 'zip'),
    country: getAddressDetail(props.organization?.address, 'country')
});

const typeOptions = ref([
    { value: 'credit', label: trans('Credit') },
    { value: 'debit', label: trans('Debit') },
    { value: 'payment', label: trans('Payment') },
])

const paymentOptions = ref([
    { value: 'manual', label: trans('Manual') },
    { value: 'bank', label: trans('Bank') },
])

const form1 = useForm({
    uuid: props.organization?.uuid,
    type: null,
    amount: null,
    method: null,
    description: null,
})

const roleOptions = () => {
    return props.plans.map((option) => ({
        value: option.uuid,
        label: option.name,
    }));
};

const isOpenFormModal = ref(false);

const isLoading = ref(false);

const changeTab = (value) => {
    tab.value = value;
}

const switchUserType = (value) => {
    form.create_user = value;
    if (value === 0) {
        form.first_name = null;
        form.last_name = null;
        form.email = null;
        form.phone = null;
        form.password = null;
        form.password_confirmation = null;
    } else {
        form.email = null;
    }
}

const submitForm = async () => {
    const url = props.organization ? window.location.pathname : '/admin/organizations';

    form[props.organization ? 'put' : 'post'](url, {
        preserveScroll: true,
    });
};

const toggleFormModal = () => {
    isOpenFormModal.value = !isOpenFormModal.value;
}

const submitForm1 = async () => {
    form1.post('/admin/billing', {
        preserveScroll: true,
        onSuccess: () => {
            toggleFormModal()
            changeTab('billing')
        },
    })
};
</script> -->

<!-- ========================================= NEW UI CODE ==================================== -->


<template>
    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50/30 p-4 md:p-8">
            <!-- Header Section for Create -->
            <div v-if="props.organization === null" class="mb-8">
                <div class="bg-white rounded-3xl shadow-md border-2 border-primary/10 p-6 md:p-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-14 h-14 bg-gradient-to-br from-[#ff5100] to-[#ff7733] rounded-2xl flex items-center justify-center shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $t('Create organization') }}</h1>
                                <p class="text-sm text-gray-500 mt-1 flex items-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="currentColor"
                                            d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8s8 3.59 8 8s-3.59 8-8 8z" />
                                    </svg>
                                    <span>{{ $t('Set up a new organization') }}</span>
                                </p>
                            </div>
                        </div>
                        <Link href="/admin/organizations"
                            class="px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 rounded-xl font-medium transition-all duration-200 border-2 border-gray-200 hover:border-gray-300 flex items-center space-x-2 shadow-sm w-fit">
                        <ArrowLeft class="w-4 h-4" />
                        <span>{{ $t('Back') }}</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Header Section for Edit -->
            <div v-if="props.organization" class="mb-8">
                <div class="bg-white rounded-3xl shadow-md border-2 border-primary/10 p-6 md:p-8">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                        <div class="flex items-start space-x-4">
                            <div
                                class="flex-shrink-0 w-20 h-20 bg-gradient-to-br from-[#ff5100] to-[#ff7733] rounded-2xl flex items-center justify-center shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3s-3-1.34-3-3s1.34-3 3-3zm0 14.2a7.2 7.2 0 0 1-6-3.22c.03-1.99 4-3.08 6-3.08c1.99 0 5.97 1.09 6 3.08a7.2 7.2 0 0 1-6 3.22z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ props.organization.name }}</h1>
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" class="mr-1.5">
                                                <path fill="currentColor"
                                                    d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12c5.16-1.26 9-6.45 9-12V7l-10-5zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V9h7V3.06l8 3.68v8.26h-8z" />
                                            </svg>
                                            {{ props.organization?.subscription?.plan?.name ?? 'Not set' }}
                                        </span>
                                        <span v-if="props.organization?.subscription?.status === 'trial'"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" class="mr-1.5">
                                                <path fill="currentColor"
                                                    d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm4.2 14.2L11 13V7h1.5v5.2l4.5 2.7l-.8 1.3z" />
                                            </svg>
                                            {{ $t('Trial period') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" class="text-gray-400">
                                            <path fill="currentColor"
                                                d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z" />
                                        </svg>
                                        <span class="capitalize">{{ $t('Valid until') }}: {{
                                            props.organization?.subscription?.valid_until ?? 'Not set' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <button type="button" @click="toggleFormModal()"
                                class="px-6 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff6622] hover:from-[#ff6622] hover:to-[#ff5100] text-white rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z" />
                                </svg>
                                <span>{{ $t('Create transaction') }}</span>
                            </button>
                            <Link href="/admin/organizations"
                                class="px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 rounded-xl font-medium transition-all duration-200 border-2 border-gray-200 hover:border-gray-300 flex items-center space-x-2 shadow-sm">
                            <ArrowLeft class="w-4 h-4" />
                            <span>{{ $t('Back') }}</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Section -->
            <div v-if="props.organization"
                class="bg-white rounded-t-3xl shadow-md border-2 border-b-0 border-primary/10 px-6 pt-4">
                <div class="flex space-x-1 overflow-x-auto">
                    <button @click="changeTab('organization')" type="button"
                        class="px-6 py-3 text-sm font-medium rounded-t-xl transition-all duration-200 whitespace-nowrap"
                        :class="tab === 'organization'
                            ? 'bg-gradient-to-br from-[#ff5100] to-[#ff6622] text-white shadow-md'
                            : 'text-gray-600 hover:bg-orange-50 hover:text-[#ff5100]'">
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4s-4 1.79-4 4s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                            </svg>
                            <span>{{ $t('User details') }}</span>
                        </div>
                    </button>
                    <button @click="changeTab('team')" type="button"
                        class="px-6 py-3 text-sm font-medium rounded-t-xl transition-all duration-200 whitespace-nowrap"
                        :class="tab === 'team'
                            ? 'bg-gradient-to-br from-[#ff5100] to-[#ff6622] text-white shadow-md'
                            : 'text-gray-600 hover:bg-orange-50 hover:text-[#ff5100]'">
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05c1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                            </svg>
                            <span>{{ $t('Team') }}</span>
                        </div>
                    </button>
                    <button @click="changeTab('billing')" type="button"
                        class="px-6 py-3 text-sm font-medium rounded-t-xl transition-all duration-200 whitespace-nowrap"
                        :class="tab === 'billing'
                            ? 'bg-gradient-to-br from-[#ff5100] to-[#ff6622] text-white shadow-md'
                            : 'text-gray-600 hover:bg-orange-50 hover:text-[#ff5100]'">
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" />
                            </svg>
                            <span>{{ $t('Billing history') }}</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Team Tab Content -->
            <div v-if="props.organization && tab === 'team'"
                class="bg-white rounded-b-3xl shadow-md border-2 border-t-0 border-primary/10 p-6">
                <UserTable :rows="props.users" :filters="props.filters" :type="'user'" :showRole="true"
                    :showDeleteBtn="false" />
            </div>

            <!-- Billing Tab Content -->
            <div v-if="props.organization && tab === 'billing'"
                class="bg-white rounded-b-3xl shadow-md border-2 border-t-0 border-primary/10 p-6">
                <BillingTable :rows="props.invoices" :filters="props.filters" :uuid="props.organization.uuid" />
            </div>

            <!-- Organization Form -->
            <form v-if="tab === 'organization'" @submit.prevent="submitForm()"
                :class="props.organization ? 'bg-white rounded-b-3xl shadow-md border-2 border-t-0 border-primary/10' : 'bg-white rounded-3xl shadow-md border-2 border-primary/10'">

                <!-- Organization Details Section -->
                <div class="p-6 md:p-8 border-b-2 border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:space-x-12">
                        <div class="lg:w-2/5 mb-6 lg:mb-0">
                            <div class="flex items-start space-x-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-[#ff5100] to-[#ff7733] rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-white">
                                        <path fill="currentColor"
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3s-3-1.34-3-3s1.34-3 3-3zm0 14.2a7.2 7.2 0 0 1-6-3.22c.03-1.99 4-3.08 6-3.08c1.99 0 5.97 1.09 6 3.08a7.2 7.2 0 0 1-6 3.22z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $t('Organization details') }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $t('Basic organization information') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="lg:w-3/5">
                            <div class="grid gap-6 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <FormInput v-model="form.name" :name="$t('Name')" :error="form.errors.name"
                                        :type="'text'" />
                                </div>
                                <div class="sm:col-span-2">
                                    <FormSelect v-model="form.plan" :name="$t('Subscription plan')"
                                        :error="form.errors.plan" :options="roleOptions()" :type="'text'" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Details Section (Only for Create) -->
                <div v-if="props.organization === null" class="p-6 md:p-8 border-b-2 border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:space-x-12">
                        <div class="lg:w-2/5 mb-6 lg:mb-0">
                            <div class="flex items-start space-x-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-[#ff5100] to-[#ff7733] rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-white">
                                        <path fill="currentColor"
                                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4s-4 1.79-4 4s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $t('User details') }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $t(`Enter the details of the main
                                        administrative user of this organization`) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="lg:w-3/5">
                            <!-- User Type Toggle -->
                            <div
                                class="bg-gradient-to-r from-gray-100 to-gray-50 rounded-2xl p-1.5 flex space-x-2 mb-6 border-2 border-gray-200">
                                <button type="button"
                                    class="flex-1 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200"
                                    :class="{ 'bg-white text-[#ff5100] shadow-md': form.create_user === 1, 'text-gray-600 hover:text-gray-900': form.create_user !== 1 }"
                                    @click="switchUserType(1)">
                                    <div class="flex items-center justify-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4s-4 1.79-4 4s1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                        </svg>
                                        <span>{{ $t('Add user') }}</span>
                                    </div>
                                </button>
                                <button type="button"
                                    class="flex-1 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200"
                                    :class="{ 'bg-white text-[#ff5100] shadow-md': form.create_user === 0, 'text-gray-600 hover:text-gray-900': form.create_user !== 0 }"
                                    @click="switchUserType(0)">
                                    <div class="flex items-center justify-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                        </svg>
                                        <span>{{ $t('Select existing user') }}</span>
                                    </div>
                                </button>
                            </div>

                            <!-- Add User Form -->
                            <div v-if="form.create_user === 1" class="grid gap-6 sm:grid-cols-2">
                                <FormInput v-model="form.first_name" :name="$t('First name')"
                                    :error="form.errors.first_name" :type="'text'" />
                                <FormInput v-model="form.last_name" :name="$t('Last name')"
                                    :error="form.errors.last_name" :type="'text'" />
                                <FormInput v-model="form.email" :name="$t('Email')" :error="form.errors.email"
                                    :type="'text'" />
                                <FormPhoneInput v-model="form.phone" :name="$t('Phone')" :error="form.errors.phone"
                                    :type="'text'" />
                                <FormInput v-model="form.password" :name="$t('Password')" :error="form.errors.password"
                                    :type="'password'" />
                                <FormInput v-model="form.password_confirmation" :name="$t('Confirm password')"
                                    :error="form.errors.password_confirmation" :type="'password'" />
                            </div>

                            <!-- Select Existing User Form -->
                            <div v-else class="grid gap-6">
                                <FormInput v-model="form.email" :name="$t('Email')" :error="form.errors.email"
                                    :type="'text'" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Details Section -->
                <div class="p-6 md:p-8">
                    <div class="flex flex-col lg:flex-row lg:space-x-12">
                        <div class="lg:w-2/5 mb-6 lg:mb-0">
                            <div class="flex items-start space-x-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-[#ff5100] to-[#ff7733] rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-white">
                                        <path fill="currentColor"
                                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5s2.5 1.12 2.5 2.5s-1.12 2.5-2.5 2.5z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $t('Address details') }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $t('Organization location information')
                                    }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="lg:w-3/5">
                            <div class="grid gap-6 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <FormInput v-model="form.street" :name="$t('Street')" :error="form.errors.street"
                                        :type="'text'" />
                                </div>
                                <FormInput v-model="form.city" :name="$t('City')" :error="form.errors.city"
                                    :type="'text'" />
                                <FormInput v-model="form.state" :name="$t('State')" :error="form.errors.state"
                                    :type="'text'" />
                                <FormInput v-model="form.zip" :name="$t('Zip code')" :error="form.errors.zip"
                                    :type="'text'" />
                                <FormInput v-model="form.country" :name="$t('Country')" :error="form.errors.country"
                                    :type="'text'" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="px-6 md:px-8 pb-6 md:pb-8 pt-4 border-t-2 border-gray-100">
                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff6622] hover:from-[#ff6622] hover:to-[#ff5100] text-white rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3s3 1.34 3 3s-1.34 3-3 3zm3-10H5V5h10v4z" />
                            </svg>
                            <span>{{ $t('Save') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>

    <!-- Transaction Modal -->
    <Modal :label="$t('Create transaction')" :isOpen=isOpenFormModal>
        <div class="mt-5">
            <form @submit.prevent="submitForm1()">
                <div class="grid gap-6 sm:grid-cols-2">
                    <FormSelect v-model="form1.type" :name="$t('Transaction type')" :error="form1.errors.type"
                        :options="typeOptions" />
                    <FormInput v-model="form1.amount" :name="$t('Amount')" :error="form1.errors.amount"
                        :type="'number'" />
                    <div class="sm:col-span-2">
                        <FormSelect v-if="form1.type === 'payment'" v-model="form1.method" :name="$t('Payment method')"
                            :error="form1.errors.method" :options="paymentOptions" />
                        <FormInput v-else v-model="form1.description" :name="$t('Description')"
                            :error="form1.errors.description" :type="'text'" />
                    </div>
                </div>

                <!-- Warning Message -->
                <div
                    class="mt-6 bg-gradient-to-r from-red-500 to-red-600 rounded-2xl p-4 border-2 border-red-400 shadow-lg">
                    <div class="flex items-start space-x-3">
                        <svg class="text-white flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="22"
                            height="22" viewBox="0 0 20 20">
                            <path fill="currentColor" fill-rule="evenodd"
                                d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625zM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5m0 9a1 1 0 1 0 0-2a1 1 0 0 0 0 2"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm text-white font-medium">
                            {{ $t('You can\'t undo this transaction once you save it') }}
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="toggleFormModal()"
                        class="px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 rounded-xl font-medium transition-all duration-200 border-2 border-gray-200 hover:border-gray-300 shadow-sm">
                        {{ $t('Cancel') }}
                    </button>
                    <button type="submit"
                        :class="['px-6 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff6622] hover:from-[#ff6622] hover:to-[#ff5100] text-white rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center space-x-2', { 'opacity-50 cursor-not-allowed': isLoading }]"
                        :disabled="isLoading">
                        <svg v-if="isLoading" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            viewBox="0 0 24 24" class="animate-spin">
                            <path fill="currentColor"
                                d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                opacity=".5" />
                            <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z" />
                        </svg>
                        <span v-if="!isLoading">{{ $t('Save') }}</span>
                        <span v-else>{{ $t('Saving...') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import { ref } from 'vue';
import { Link, useForm } from "@inertiajs/vue3";
import FormInput from '@/Components/FormInput.vue';
import FormPhoneInput from '@/Components/FormPhoneInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import BillingTable from '@/Components/Tables/BillingTable.vue';
import Modal from '@/Components/Modal.vue';
import UserTable from '@/Components/Tables/UserTable.vue';
import { trans } from 'laravel-vue-i18n';
import { ArrowLeft } from "lucide-vue-next";

const props = defineProps({
    showAddBtn: {
        type: Boolean,
        default: true
    },
    title: String,
    organization: Object,
    users: Object,
    invoices: Object,
    plans: Object,
    filters: Object,
    mode: String
});

const tab = ref('organization');

const getAddressDetail = (value, key) => {
    if (value) {
        const address = JSON.parse(value);
        return address?.[key] ?? null;
    } else {
        return null;
    }
};

const form = useForm({
    name: props.organization?.name,
    plan: props.organization?.subscription?.plan?.uuid,
    create_user: 1,
    first_name: null,
    last_name: null,
    email: null,
    phone: null,
    password: null,
    password_confirmation: null,
    street: getAddressDetail(props.organization?.address, 'street'),
    city: getAddressDetail(props.organization?.address, 'city'),
    state: getAddressDetail(props.organization?.address, 'state'),
    zip: getAddressDetail(props.organization?.address, 'zip'),
    country: getAddressDetail(props.organization?.address, 'country')
});

const typeOptions = ref([
    { value: 'credit', label: trans('Credit') },
    { value: 'debit', label: trans('Debit') },
    { value: 'payment', label: trans('Payment') },
])

const paymentOptions = ref([
    { value: 'manual', label: trans('Manual') },
    { value: 'bank', label: trans('Bank') },
])

const form1 = useForm({
    uuid: props.organization?.uuid,
    type: null,
    amount: null,
    method: null,
    description: null,
})

const roleOptions = () => {
    return props.plans.map((option) => ({
        value: option.uuid,
        label: option.name,
    }));
};

const isOpenFormModal = ref(false);

const isLoading = ref(false);

const changeTab = (value) => {
    tab.value = value;
}

const switchUserType = (value) => {
    form.create_user = value;
    if (value === 0) {
        form.first_name = null;
        form.last_name = null;
        form.email = null;
        form.phone = null;
        form.password = null;
        form.password_confirmation = null;
    } else {
        form.email = null;
    }
}

const submitForm = async () => {
    const url = props.organization ? window.location.pathname : '/admin/organizations';

    form[props.organization ? 'put' : 'post'](url, {
        preserveScroll: true,
    });
};

const toggleFormModal = () => {
    isOpenFormModal.value = !isOpenFormModal.value;
}

const submitForm1 = async () => {
    form1.post('/admin/billing', {
        preserveScroll: true,
        onSuccess: () => {
            toggleFormModal()
            changeTab('billing')
        },
    })
};
</script>