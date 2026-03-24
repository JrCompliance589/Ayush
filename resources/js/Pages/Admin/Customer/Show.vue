<!-- <template>
    <AppLayout>
        <div
            class="bg-white md:bg-inherit pt-0 px-4 md:pt-8 md:p-8 rounded-[5px] text-[#000] h-full md:overflow-y-scroll">
            <div v-if="props.user === null" class="md:flex justify-between hidden">
                <div>
                    <h1 class="text-xl mb-1">{{ $t('Create user') }}</h1>
                    <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                        </svg>
                        <span class="ml-1 mt-1">{{ $t('Create user') }}</span>
                    </p>
                </div>
                <div>
                    <Link href="/admin/users"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-300 flex items-center space-x-2">
                    <ArrowLeft class="w-4 h-4" />
                    <span>{{ $t('Back') }}</span></Link>
                </div>
            </div>
            <div v-if="props.user" class="flex justify-between">
                <div class="flex items-center space-x-2 mb-8 mt-8 md:mt-0">
                    <div class="rounded-full p-1">
                        <img v-if="user.avatar" class="rounded-full w-40 h-40" :src="user.avatar">
                        <div v-else class="rounded-full w-32 h-32 bg-slate-200 flex justify-center items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24">
                                <g fill="none">
                                    <path
                                        d="M24 0v24H0V0h24ZM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018Zm.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022Zm-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01l-.184-.092Z" />
                                    <path fill="currentColor"
                                        d="M12 13c2.396 0 4.575.694 6.178 1.672c.8.488 1.484 1.064 1.978 1.69c.486.615.844 1.351.844 2.138c0 .845-.411 1.511-1.003 1.986c-.56.45-1.299.748-2.084.956c-1.578.417-3.684.558-5.913.558s-4.335-.14-5.913-.558c-.785-.208-1.524-.506-2.084-.956C3.41 20.01 3 19.345 3 18.5c0-.787.358-1.523.844-2.139c.494-.625 1.177-1.2 1.978-1.69C7.425 13.695 9.605 13 12 13Zm0-11a5 5 0 1 1 0 10a5 5 0 0 1 0-10Z" />
                                </g>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-lg">{{ props.user.full_name }}</h1>
                        <h2 class="text-normal">{{ props.user.email }}</h2>
                    </div>
                </div>
                <div>
                    <Link href="/admin/users"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-300 flex items-center space-x-2">
                    <ArrowLeft class="w-4 h-4" />
                    <span>{{ $t('Back') }}</span></Link>
                </div>
            </div>
            <div v-if="props.user" class="flex border-b-2 space-x-4 text-sm text-slate-700">
                <div @click="changeTab('user')" class="cursor-pointer px-2 py-2 rounded-tl-lg rounded-tr-lg"
                    :class="tab === 'user' ? 'bg-white border' : ''">{{ $t('User details') }}</div>
                <div @click="changeTab('organization')" class="cursor-pointer px-2 py-2 rounded-tl-lg rounded-tr-lg"
                    :class="tab === 'organization' ? 'bg-white border' : ''">{{ $t('Organization details') }}</div>
            </div>
            <div v-if="props.user && tab === 'organization'" class="pt-5">
                <OrganizationTable :rows="props.organizations" :filters="props.filters" />
            </div>
            <form v-if="tab === 'user'" @submit.prevent="submitForm()"
                class="bg-white py-5 px-5 rounded-bl-[0.5rem] rounded-br-[0.5rem]">
                <div class="sm:flex border-b py-5">
                    <div class="hidden sm:block sm:w-[40%] mb-1">
                        <h3 class="text-sm tracking-[0px]">{{ $t('User details') }}</h3>
                    </div>
                    <div class="sm:w-[60%] sm:flex space-x-6">
                        <div class="sm:w-[80%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormImage v-model="form.avatar" :name="$t('Avatar')" :error="form.errors.avatar"
                                :label="$t('Upload image')"
                                :imageUrl="props.user?.avatar ? '/media/' + props.user?.avatar : null"
                                :class="'sm:col-span-6'" />
                            <FormInput v-model="form.first_name" :name="$t('First name')"
                                :error="form.errors.first_name" :type="'text'" :class="'sm:col-span-3'" />
                            <FormInput v-model="form.last_name" :name="$t('Last name')" :error="form.errors.last_name"
                                :type="'text'" :class="'sm:col-span-3'" />
                            <FormInput v-model="form.email" :name="$t('Email')" :error="form.errors.email"
                                :type="'text'" :class="'sm:col-span-3'" />
                            <FormPhoneInput v-model="form.phone" :name="$t('Phone')" :error="form.errors.phone"
                                :type="'text'" :class="'sm:col-span-3'" />
                            <FormInput v-if="!props.user" v-model="form.password" :name="$t('Password')"
                                :error="form.errors.password" :type="'password'" :class="'sm:col-span-3'" />
                            <FormInput v-if="!props.user" v-model="form.password_confirmation"
                                :name="$t('Confirm password')" :error="form.errors.password_confirmation"
                                :type="'password'" :class="'sm:col-span-3'" />
                        </div>
                    </div>
                </div>
                <div v-if="!props.user" class="sm:flex border-b py-5">
                    <div class="hidden sm:block sm:w-[40%] mb-1">
                        <h3 class="text-sm tracking-[0px]">{{ $t('Organization') }}</h3>
                    </div>
                    <div class="sm:w-[60%] sm:flex space-x-6">
                        <div class="sm:w-[80%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormCheckbox @input="toggleCreateOrganization" v-model="create_organization"
                                :name="$t('Create organization')" :label="$t('Create organization')"
                                :value="'organization'" :type="'checkbox'" :class="'sm:col-span-3'" />
                            <FormInput v-if="create_organization" v-model="form.organization_name"
                                :name="$t('Organization name')" :error="form.errors.organization_name" :type="'text'"
                                :class="'sm:col-span-6'" />
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
                    <button type="submit" :disabled="form.processing"
                        class="float-right flex items-center space-x-4 rounded-md bg-black px-3 py-2 text-sm text-white shadow-sm hover:bg-slate-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        {{ $t('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import { ref } from 'vue';
import { Link, useForm } from "@inertiajs/vue3";
import FormCheckbox from '@/Components/FormCheckbox.vue';
import FormImage from '@/Components/FormImage.vue';
import FormInput from '@/Components/FormInput.vue';
import FormPhoneInput from '@/Components/FormPhoneInput.vue';
import OrganizationTable from '@/Components/Tables/OrganizationTable.vue';
import { ArrowLeft } from "lucide-vue-next";

const props = defineProps({ title: String, user: Object, roles: Object, organizations: Object, filters: Object });
const create_organization = ref(false);
const tab = ref('user');

const getAddressDetail = (value, key) => {
    if (value) {
        const address = JSON.parse(value);
        return address?.[key] ?? null;
    } else {
        return null;
    }
};

const toggleCreateOrganization = () => {
    if (!create_organization.value) {
        form.organization_name = null;
    } else {
        form.organization_name = undefined;
    }
}

const form = useForm({
    first_name: props.user?.first_name,
    last_name: props.user?.last_name,
    email: props.user?.email,
    phone: props.user?.phone,
    role: props.user?.role?.uuid,
    avatar: undefined,
    street: getAddressDetail(props.user?.address, 'street'),
    city: getAddressDetail(props.user?.address, 'city'),
    state: getAddressDetail(props.user?.address, 'state'),
    zip: getAddressDetail(props.user?.address, 'zip'),
    country: getAddressDetail(props.user?.address, 'country'),
    ...(props.user ? {} : { password: null, password_confirmation: null }),
    organization_name: undefined,
});

const changeTab = (value) => {
    tab.value = value;
}

const submitForm = async () => {
    const url = props.user ? window.location.pathname : '/admin/users';

    form[props.user ? 'put' : 'post'](url, {
        preserveScroll: true,
    });
};
</script> -->

<!-- ========================================= NEW UI CODE ==================================== -->

<template>
    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50/30 p-4 md:p-8">
            <!-- Header Section for Create -->
            <div v-if="props.user === null" class="mb-8">
                <div class="bg-white rounded-3xl shadow-md border-2 border-primary/10 p-6 md:p-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-14 h-14 bg-gradient-to-br from-[#ff5100] to-[#ff7733] rounded-2xl flex items-center justify-center shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4s-4 1.79-4 4s1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $t('Create user') }}</h1>
                                <p class="text-sm text-gray-500 mt-1 flex items-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="currentColor"
                                            d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8s8 3.59 8 8s-3.59 8-8 8z" />
                                    </svg>
                                    <span>{{ $t('Add a new user to the system') }}</span>
                                </p>
                            </div>
                        </div>
                        <Link href="/admin/users"
                            class="px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 rounded-xl font-medium transition-all duration-200 border-2 border-gray-200 hover:border-gray-300 flex items-center space-x-2 shadow-sm w-fit">
                        <ArrowLeft class="w-4 h-4" />
                        <span>{{ $t('Back') }}</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Header Section for Edit -->
            <div v-if="props.user" class="mb-8">
                <div class="bg-white rounded-3xl shadow-md border-2 border-primary/10 p-6 md:p-8">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                        <div class="flex items-start space-x-4">
                            <!-- Avatar -->
                            <div class="flex-shrink-0 relative group">
                                <div v-if="user.avatar"
                                    class="w-20 h-20 rounded-2xl overflow-hidden shadow-lg ring-4 ring-orange-100">
                                    <img class="w-full h-full object-cover" :src="user.avatar" :alt="user.full_name">
                                </div>
                                <div v-else
                                    class="w-20 h-20 bg-gradient-to-br from-[#ff5100] to-[#ff7733] rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-orange-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                                        class="text-white">
                                        <path fill="currentColor"
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3s-3-1.34-3-3s1.34-3 3-3zm0 14.2a7.2 7.2 0 0 1-6-3.22c.03-1.99 4-3.08 6-3.08c1.99 0 5.97 1.09 6 3.08a7.2 7.2 0 0 1-6 3.22z" />
                                    </svg>
                                </div>
                                <div
                                    class="absolute -bottom-1 -right-1 w-7 h-7 bg-green-500 rounded-full border-4 border-white shadow-sm">
                                </div>
                            </div>

                            <!-- User Info -->
                            <div class="flex-1">
                                <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ props.user.full_name }}</h1>
                                <div class="flex items-center space-x-2 text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        class="text-gray-400">
                                        <path fill="currentColor"
                                            d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5l-8-5V6l8 5l8-5v2z" />
                                    </svg>
                                    <span class="text-sm">{{ props.user.email }}</span>
                                </div>
                            </div>
                        </div>

                        <Link href="/admin/users"
                            class="px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 rounded-xl font-medium transition-all duration-200 border-2 border-gray-200 hover:border-gray-300 flex items-center space-x-2 shadow-sm w-fit">
                        <ArrowLeft class="w-4 h-4" />
                        <span>{{ $t('Back') }}</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Tabs Section -->
            <div v-if="props.user"
                class="bg-white rounded-t-3xl shadow-md border-2 border-b-0 border-primary/10 px-6 pt-4">
                <div class="flex space-x-1 overflow-x-auto">
                    <button @click="changeTab('user')" type="button"
                        class="px-6 py-3 text-sm font-medium rounded-t-xl transition-all duration-200 whitespace-nowrap"
                        :class="tab === 'user'
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
                    <button @click="changeTab('organization')" type="button"
                        class="px-6 py-3 text-sm font-medium rounded-t-xl transition-all duration-200 whitespace-nowrap"
                        :class="tab === 'organization'
                            ? 'bg-gradient-to-br from-[#ff5100] to-[#ff6622] text-white shadow-md'
                            : 'text-gray-600 hover:bg-orange-50 hover:text-[#ff5100]'">
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z" />
                            </svg>
                            <span>{{ $t('Organization details') }}</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Organization Tab Content -->
            <div v-if="props.user && tab === 'organization'"
                class="bg-white rounded-b-3xl shadow-md border-2 border-t-0 border-primary/10 p-6">
                <OrganizationTable :rows="props.organizations" :filters="props.filters" />
            </div>

            <!-- User Form -->
            <form v-if="tab === 'user'" @submit.prevent="submitForm()"
                :class="props.user ? 'bg-white rounded-b-3xl shadow-md border-2 border-t-0 border-primary/10' : 'bg-white rounded-3xl shadow-md border-2 border-primary/10'">

                <!-- User Details Section -->
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
                                    <h3 class="text-lg font-bold text-gray-900">{{ $t('User details') }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $t('Personal information and credentials')
                                        }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="lg:w-3/5">
                            <div class="grid gap-6 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <FormImage v-model="form.avatar" :name="$t('Avatar')" :error="form.errors.avatar"
                                        :label="$t('Upload image')"
                                        :imageUrl="props.user?.avatar ? '/media/' + props.user?.avatar : null" />
                                </div>
                                <FormInput v-model="form.first_name" :name="$t('First name')"
                                    :error="form.errors.first_name" :type="'text'" />
                                <FormInput v-model="form.last_name" :name="$t('Last name')"
                                    :error="form.errors.last_name" :type="'text'" />
                                <FormInput v-model="form.email" :name="$t('Email')" :error="form.errors.email"
                                    :type="'text'" />
                                <FormPhoneInput v-model="form.phone" :name="$t('Phone')" :error="form.errors.phone"
                                    :type="'text'" />
                                <FormInput v-if="!props.user" v-model="form.password" :name="$t('Password')"
                                    :error="form.errors.password" :type="'password'" />
                                <FormInput v-if="!props.user" v-model="form.password_confirmation"
                                    :name="$t('Confirm password')" :error="form.errors.password_confirmation"
                                    :type="'password'" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Organization Section (Only for Create) -->
                <div v-if="!props.user" class="p-6 md:p-8 border-b-2 border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:space-x-12">
                        <div class="lg:w-2/5 mb-6 lg:mb-0">
                            <div class="flex items-start space-x-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-[#ff5100] to-[#ff7733] rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-white">
                                        <path fill="currentColor"
                                            d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $t('Organization') }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $t('Associate user with an organization')
                                        }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="lg:w-3/5">
                            <div class="space-y-6">
                                <!-- Create Organization Toggle -->
                                <div
                                    class="bg-gradient-to-br from-orange-50 to-orange-100/50 rounded-2xl p-5 border-2 border-orange-200">
                                    <FormCheckbox @input="toggleCreateOrganization" v-model="create_organization"
                                        :name="$t('Create organization')" :label="$t('Create organization')"
                                        :value="'organization'" :type="'checkbox'" />
                                </div>

                                <!-- Organization Name Input -->
                                <div v-if="create_organization" class="transform transition-all duration-300">
                                    <FormInput v-model="form.organization_name" :name="$t('Organization name')"
                                        :error="form.errors.organization_name" :type="'text'" />
                                </div>
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
                                    <p class="text-sm text-gray-500 mt-1">{{ $t('User location information') }}</p>
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
                        <button type="submit" :disabled="form.processing"
                            :class="['px-8 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff6622] hover:from-[#ff6622] hover:to-[#ff5100] text-white rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center space-x-2', { 'opacity-50 cursor-not-allowed': form.processing }]">
                            <svg v-if="form.processing" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" class="animate-spin">
                                <path fill="currentColor"
                                    d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                    opacity=".5" />
                                <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3s3 1.34 3 3s-1.34 3-3 3zm3-10H5V5h10v4z" />
                            </svg>
                            <span v-if="form.processing">{{ $t('Saving...') }}</span>
                            <span v-else>{{ $t('Save') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import { ref } from 'vue';
import { Link, useForm } from "@inertiajs/vue3";
import FormCheckbox from '@/Components/FormCheckbox.vue';
import FormImage from '@/Components/FormImage.vue';
import FormInput from '@/Components/FormInput.vue';
import FormPhoneInput from '@/Components/FormPhoneInput.vue';
import OrganizationTable from '@/Components/Tables/OrganizationTable.vue';
import { ArrowLeft } from "lucide-vue-next";

const props = defineProps({
    title: String,
    user: Object,
    roles: Object,
    organizations: Object,
    filters: Object
});

const create_organization = ref(false);
const tab = ref('user');

const getAddressDetail = (value, key) => {
    if (value) {
        const address = JSON.parse(value);
        return address?.[key] ?? null;
    } else {
        return null;
    }
};

const toggleCreateOrganization = () => {
    if (!create_organization.value) {
        form.organization_name = null;
    } else {
        form.organization_name = undefined;
    }
}

const form = useForm({
    first_name: props.user?.first_name,
    last_name: props.user?.last_name,
    email: props.user?.email,
    phone: props.user?.phone,
    role: props.user?.role?.uuid,
    avatar: undefined,
    street: getAddressDetail(props.user?.address, 'street'),
    city: getAddressDetail(props.user?.address, 'city'),
    state: getAddressDetail(props.user?.address, 'state'),
    zip: getAddressDetail(props.user?.address, 'zip'),
    country: getAddressDetail(props.user?.address, 'country'),
    ...(props.user ? {} : { password: null, password_confirmation: null }),
    organization_name: undefined,
});

const changeTab = (value) => {
    tab.value = value;
}

const submitForm = async () => {
    const url = props.user ? window.location.pathname : '/admin/users';

    form[props.user ? 'put' : 'post'](url, {
        preserveScroll: true,
    });
};
</script>
