<!-- <template>
    <AppLayout>
        <div class="bg-white md:bg-inherit pt-0 px-4 md:pt-8 md:p-8 rounded-[5px] text-[#000] overflow-y-scroll">
            <div class="md:flex justify-between hidden">
                <div>
                    <h1 v-if="props.user === null" class="text-xl mb-1">{{ $t('Create user') }}</h1>
                    <h1 v-else class="text-xl mb-1">{{ $t('Update user') }}</h1>
                    <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                        </svg>
                        <span v-if="props.user === null" class="ml-1 mt-1">{{ $t(`Create administrative user and assign
                            role`) }}</span>
                        <span v-else class="ml-1 mt-1">{{ $t('Update administrative user and assign role') }}</span>
                    </p>
                </div>
                <div>
                    <Link href="/admin/team/users"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-300 flex items-center space-x-2">
                    <ArrowLeft class="w-4 h-4" />
                    <span>{{ $t('Back') }}</span></Link>
                </div>
            </div>
            <form @submit.prevent="submitForm()" class="bg-white md:border py-5 px-5 rounded-[0.5rem]">
                <div class="sm:flex border-b py-5">
                    <div class="hidden sm:block sm:w-[40%] mb-1">
                        <h1 class="text-sm text-gray-500 tracking-[0px]">{{ $t('Personally identifiable information') }}
                        </h1>
                    </div>
                    <div class="sm:w-[60%] sm:flex space-x-6">
                        <div class="sm:w-[80%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormImage v-model="form.avatar" :name="'Avatar'" :error="form.errors.avatar"
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
                            <FormSelect v-model="form.role" :name="$t('Role')" :error="form.errors.role"
                                :options="roleOptions()" :type="'text'" :class="'sm:col-span-6'" />
                            <FormInput v-if="!props.user" v-model="form.password" :name="$t('Password')"
                                :error="form.errors.password" :type="'password'" :class="'sm:col-span-3'" />
                            <FormInput v-if="!props.user" v-model="form.password_confirmation"
                                :name="$t('Confirm password')" :error="form.errors.password_confirmation"
                                :type="'password'" :class="'sm:col-span-3'" />
                        </div>
                    </div>
                </div>
                <div class="sm:flex py-5">
                    <div class="hidden sm:block w-[40%] mb-1">
                        <h1 class="text-sm text-gray-500 tracking-[0px]">{{ $t('Address details') }}</h1>
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
</template>
<script setup>
import AppLayout from "./../Layout/App.vue";
import { Link, useForm } from "@inertiajs/vue3";
import FormImage from '@/Components/FormImage.vue';
import FormInput from '@/Components/FormInput.vue';
import FormPhoneInput from '@/Components/FormPhoneInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import { ArrowLeft } from "lucide-vue-next";

const props = defineProps({ title: String, user: Object, roles: Object });

const getAddressDetail = (value, key) => {
    if (value) {
        const address = JSON.parse(value);
        return address?.[key] ?? null;
    } else {
        return null;
    }
}

const form = useForm({
    first_name: props.user?.first_name,
    last_name: props.user?.last_name,
    email: props.user?.email,
    phone: props.user?.phone,
    role: props.user?.role?.uuid,
    password: null,
    password_confirmation: null,
    avatar: null,
    street: getAddressDetail(props.user?.address, 'street'),
    city: getAddressDetail(props.user?.address, 'city'),
    state: getAddressDetail(props.user?.address, 'state'),
    zip: getAddressDetail(props.user?.address, 'zip'),
    country: getAddressDetail(props.user?.address, 'country')
})

const roleOptions = () => {
    return props.roles.map((option) => ({
        value: option.uuid,
        label: option.name,
    }));
};

const submitForm = async () => {
    const url = props.user ? window.location.pathname : '/admin/team/users';

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
                                        d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5S5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05c1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
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
                                    <span>{{ $t('Create administrative user and assign role') }}</span>
                                </p>
                            </div>
                        </div>
                        <Link href="/admin/team/users"
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
                                <div v-if="props.user.avatar"
                                    class="w-20 h-20 rounded-2xl overflow-hidden shadow-lg ring-4 ring-orange-100">
                                    <img class="w-full h-full object-cover" :src="props.user.avatar"
                                        :alt="props.user.first_name + ' ' + props.user.last_name">
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
                                <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ props.user.first_name + ' ' +
                                    props.user.last_name }}</h1>
                                <div class="flex items-center space-x-2 text-gray-600 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        class="text-gray-400">
                                        <path fill="currentColor"
                                            d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5l-8-5V6l8 5l8-5v2z" />
                                    </svg>
                                    <span class="text-sm">{{ props.user.email }}</span>
                                </div>
                                <div v-if="props.user.role"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 text-xs font-semibold rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12c5.16-1.26 9-6.45 9-12V5l-9-4z" />
                                    </svg>
                                    {{ props.user.role.name }}
                                </div>
                            </div>
                        </div>

                        <Link href="/admin/team/users"
                            class="px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 rounded-xl font-medium transition-all duration-200 border-2 border-gray-200 hover:border-gray-300 flex items-center space-x-2 shadow-sm w-fit">
                        <ArrowLeft class="w-4 h-4" />
                        <span>{{ $t('Back') }}</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- User Form -->
            <form @submit.prevent="submitForm()" class="bg-white rounded-3xl shadow-md border-2 border-primary/10">

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
                                    <p class="text-sm text-gray-500 mt-1">{{ $t('Personally identifiable information')
                                    }}
                                    </p>
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
                                <div class="sm:col-span-2">
                                    <FormSelect v-model="form.role" :name="$t('Role')" :error="form.errors.role"
                                        :options="roleOptions()" :type="'text'" />
                                </div>
                                <FormInput v-if="!props.user" v-model="form.password" :name="$t('Password')"
                                    :error="form.errors.password" :type="'password'" />
                                <FormInput v-if="!props.user" v-model="form.password_confirmation"
                                    :name="$t('Confirm password')" :error="form.errors.password_confirmation"
                                    :type="'password'" />
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
import { Link, useForm } from "@inertiajs/vue3";
import FormImage from '@/Components/FormImage.vue';
import FormInput from '@/Components/FormInput.vue';
import FormPhoneInput from '@/Components/FormPhoneInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import { ArrowLeft } from "lucide-vue-next";

const props = defineProps({ title: String, user: Object, roles: Object });

const getAddressDetail = (value, key) => {
    if (value) {
        const address = JSON.parse(value);
        return address?.[key] ?? null;
    } else {
        return null;
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
})

const roleOptions = () => {
    return props.roles.map((option) => ({
        value: option.uuid,
        label: option.name,
    }));
};

const submitForm = async () => {
    const url = props.user ? window.location.pathname : '/admin/team/users';

    form[props.user ? 'put' : 'post'](url, {
        preserveScroll: true,
    });
};
</script>

<style scoped>
/* Smooth transitions */
* {
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Animation for loading spinner */
@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>