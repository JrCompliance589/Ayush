<!-- <template>
    <AppLayout>
        <div class="bg-white md:bg-inherit pt-0 px-4 md:pt-8 md:p-8 rounded-[5px] text-[#000] overflow-y-scroll">
            <div class="md:flex justify-between hidden">
                <div>
                    <h1 class="text-xl mb-1">{{ $t('Create role') }}</h1>
                    <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                        </svg>
                        <span class="ml-1 mt-1">{{ $t('Create roles for administrative users') }}</span>
                    </p>
                </div>
                <div>
                    <Link href="/admin/team/roles"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-300 flex items-center space-x-2">
                    <ArrowLeft class="w-4 h-4" />
                    <span>{{ $t('Back') }}</span></Link>
                </div>
            </div>
            <form @submit.prevent="submitForm()" class="bg-white md:border py-5 px-5 rounded-[0.5rem]">
                <div class="sm:flex border-b py-5">
                    <div class="sm:w-[40%] mb-1">
                        <h1 class="text-sm text-gray-500 tracking-[0px]">{{ $t('Name') }}</h1>
                    </div>
                    <div class="sm:w-[60%] sm:flex space-x-6">
                        <div class="sm:w-[80%]">
                            <FormInput v-model="form.name" :type="'text'" :error="form.errors.name" :class="'w-full'"
                                :labelClass="'mb-0'" />
                        </div>
                    </div>
                </div>
                <div class="flex py-5">
                    <div class="hidden sm:block w-[40%] mb-1">
                        <h1 class="text-sm text-gray-500 tracking-[0px]">{{ $t('Permissions') }}</h1>
                        <span class="flex text-xs mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                            </svg>
                            {{ $t('Choose the appropriate permissions for this role') }}
                        </span>
                    </div>
                    <div class="sm:w-[60%] flex space-x-6">
                        <div class="sm:w-[80%]">
                            <div class="mb-4" v-for="(item, index) in props.modules" :key="index">
                                <label class="text-sm">{{ $t(removeUnderscoreAndCapitalize(item.name)) }}</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 mt-2">
                                    <div v-for="value in separateValues(item.actions)" :key="value"
                                        class="relative flex mb-2">
                                        <div class="flex h-6 items-center mr-2">
                                            <input v-model="form.permissions[item.name][value]"
                                                :id="'permission[' + item.name + '|' + value + ']'"
                                                :name="'permission[' + item.name + '|' + value + ']'" type="checkbox"
                                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                        </div>
                                        <div class="text-sm leading-6">
                                            <label :for="'permission[' + item.name + '|' + value + ']'"
                                                class="font-medium text-gray-900">{{
                                                    $t(removeUnderscoreAndCapitalize(value)) }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
import { onMounted } from 'vue';
import { Link, useForm, router } from "@inertiajs/vue3";
import FormInput from '@/Components/FormInput.vue';
import { ArrowLeft } from "lucide-vue-next";

const props = defineProps({ title: String, role: Object, modules: Object, permissions: Object });

const form = useForm({
    name: props.role?.name,
    permissions: Object.fromEntries(props.modules.map(item => [item.name, {}]))
})

const separateValues = (value) => {
    return value.split(',');
};

const removeUnderscoreAndCapitalize = (inputString) => {
    const words = inputString.split('_');
    const capitalizedWords = words.map(word => word.charAt(0).toUpperCase() + word.slice(1));

    return capitalizedWords.join(' ');
};

const isPermissionChecked = (moduleName, actionName) => {
    const value = props.permissions.some(
        (permission) =>
            permission.module === moduleName &&
            permission.action === actionName &&
            permission.role_id === props.role?.id
    );

    form.permissions[moduleName][actionName] = value;
};

const initializeCheckboxValues = () => {
    props.modules.forEach(item => {
        const moduleName = item.name;
        const modulePermissions = props.permissions.filter(permission => permission.module === moduleName);

        modulePermissions.forEach(permission => {
            const actionName = permission.action;
            form.permissions[moduleName][actionName] = true;
        });
    });
};

const submitForm = async () => {
    const url = props.role ? window.location.pathname : '/admin/team/roles';

    form[props.role ? 'put' : 'post'](url, {
        preserveScroll: true,
    });
};

onMounted(() => {
    initializeCheckboxValues();
});
</script> -->


<!-- ============================================ NEW UI CODE ============================================= -->


<template>
    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50/30 p-4 md:p-8">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="bg-white rounded-3xl shadow-md border-2 border-primary/10 p-6 md:p-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-14 h-14 text-white bg-gradient-to-br from-[#ff5100] to-[#ff7733] rounded-2xl flex items-center justify-center shadow-lg">
                                <ShieldCheck />
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">
                                    {{ props.role ? $t('Edit role') : $t('Create role') }}
                                </h1>
                                <p class="text-sm text-gray-500 mt-1 flex items-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="currentColor"
                                            d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8s8 3.59 8 8s-3.59 8-8 8z" />
                                    </svg>
                                    <span>{{ $t('Create roles for administrative users') }}</span>
                                </p>
                            </div>
                        </div>
                        <Link href="/admin/team/roles"
                            class="px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 rounded-xl font-medium transition-all duration-200 border-2 border-gray-200 hover:border-gray-300 flex items-center space-x-2 shadow-sm w-fit">
                        <ArrowLeft class="w-4 h-4" />
                        <span>{{ $t('Back') }}</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Form Container -->
            <form @submit.prevent="submitForm()" class="bg-white rounded-3xl shadow-md border-2 border-primary/10">
                <!-- Role Name Section -->
                <div class="p-6 md:p-8 border-b-2 border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:space-x-12">
                        <div class="lg:w-2/5 mb-6 lg:mb-0">
                            <div class="flex items-start space-x-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-[#ff5100] to-[#ff7733] rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-white">
                                        <path fill="currentColor"
                                            d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $t('Role name') }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $t('Give this role a descriptive name') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="lg:w-3/5">
                            <FormInput v-model="form.name" :type="'text'" :error="form.errors.name" :class="'w-full'"
                                :labelClass="'mb-0'" :name="$t('Name')" />
                        </div>
                    </div>
                </div>

                <!-- Permissions Section -->
                <div class="p-6 md:p-8">
                    <div class="flex flex-col lg:flex-row lg:space-x-12">
                        <div class="lg:w-2/5 mb-6 lg:mb-0">
                            <div class="flex items-start space-x-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-[#ff5100] to-[#ff7733] rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-white">
                                        <path fill="currentColor"
                                            d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2s2 .9 2 2s-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $t('Permissions') }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" class="inline mr-1 text-[#ff5100]">
                                            <path fill="currentColor"
                                                d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8s8 3.59 8 8s-3.59 8-8 8z" />
                                        </svg>
                                        {{ $t('Choose the appropriate permissions for this role') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="lg:w-3/5">
                            <div class="space-y-6">
                                <!-- Permission Module Card -->
                                <div v-for="(item, index) in props.modules" :key="index"
                                    class="bg-gradient-to-br from-gray-50 to-white rounded-2xl border-2 border-gray-200 p-5 hover:border-orange-200 transition-all duration-200">
                                    <div class="flex items-center space-x-3 mb-4">
                                        <!-- <div
                                            class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" class="text-white">
                                                <path fill="currentColor"
                                                    d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z" />
                                            </svg>
                                        </div> -->
                                        <h4 class="text-base font-bold text-gray-900">
                                            {{ $t(removeUnderscoreAndCapitalize(item.name)) }}
                                        </h4>
                                    </div>

                                    <!-- Permission Checkboxes -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                        <label v-for="value in separateValues(item.actions)" :key="value"
                                            :for="'permission[' + item.name + '|' + value + ']'"
                                            class="relative flex items-center p-3 rounded-xl cursor-pointer transition-all duration-200 group"
                                            :class="form.permissions[item.name][value]
                                                ? 'bg-gradient-to-br from-[#ff5100] to-[#ff6622] shadow-md'
                                                : 'bg-white border-2 border-gray-200 hover:border-orange-200 hover:bg-orange-50/30'">
                                            <div class="flex items-center h-5">
                                                <input v-model="form.permissions[item.name][value]"
                                                    :id="'permission[' + item.name + '|' + value + ']'"
                                                    :name="'permission[' + item.name + '|' + value + ']'"
                                                    type="checkbox"
                                                    class="h-4 w-4 hidden rounded border-gray-300 text-[#ff5100] focus:ring-[#ff5100] focus:ring-offset-0 cursor-pointer">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <span
                                                    :class="form.permissions[item.name][value] ? 'text-white font-semibold' : 'text-gray-700 font-medium group-hover:text-gray-900'">
                                                    {{ $t(removeUnderscoreAndCapitalize(value)) }}
                                                </span>
                                            </div>
                                            <!-- Checkmark Icon for Selected State -->
                                            <svg v-if="form.permissions[item.name][value]"
                                                xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                viewBox="0 0 24 24" class="ml-auto text-white">
                                                <path fill="currentColor"
                                                    d="M9 16.17L4.83 12l-1.42 1.41L9 19L21 7l-1.41-1.41L9 16.17z" />
                                            </svg>
                                        </label>
                                    </div>
                                </div>
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
import { onMounted } from 'vue';
import { Link, useForm } from "@inertiajs/vue3";
import FormInput from '@/Components/FormInput.vue';
import { ArrowLeft, ShieldCheck } from "lucide-vue-next";

const props = defineProps({
    title: String,
    role: Object,
    modules: Object,
    permissions: Object
});

const form = useForm({
    name: props.role?.name,
    permissions: Object.fromEntries(props.modules.map(item => [item.name, {}]))
})

const separateValues = (value) => {
    return value.split(',');
};

const removeUnderscoreAndCapitalize = (inputString) => {
    const words = inputString.split('_');
    const capitalizedWords = words.map(word => word.charAt(0).toUpperCase() + word.slice(1));

    return capitalizedWords.join(' ');
};

const isPermissionChecked = (moduleName, actionName) => {
    const value = props.permissions.some(
        (permission) =>
            permission.module === moduleName &&
            permission.action === actionName &&
            permission.role_id === props.role?.id
    );

    form.permissions[moduleName][actionName] = value;
};

const initializeCheckboxValues = () => {
    props.modules.forEach(item => {
        const moduleName = item.name;
        const modulePermissions = props.permissions.filter(permission => permission.module === moduleName);

        modulePermissions.forEach(permission => {
            const actionName = permission.action;
            form.permissions[moduleName][actionName] = true;
        });
    });
};

const submitForm = async () => {
    const url = props.role ? window.location.pathname : '/admin/team/roles';

    form[props.role ? 'put' : 'post'](url, {
        preserveScroll: true,
    });
};

onMounted(() => {
    initializeCheckboxValues();
});
</script>