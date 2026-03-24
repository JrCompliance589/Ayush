<template>
    <AppLayout>
        <div
            class="bg-white md:bg-inherit pt-10 px-4 md:pt-8 md:p-8 rounded-[5px] text-[#000] h-full md:overflow-y-auto">
            <div class="flex justify-between">
                <!-- OLD UI CODE -->
                <!-- <div>
                    <h1 class="text-xl mb-1">{{ $t('Users') }}</h1>
                    <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                        </svg>
                        <span class="ml-1 mt-1">{{ $t('Manage admin accounts') }}</span>
                    </p>
                </div>
                <div>
                    <Link href="/admin/team/users/create"
                        class="rounded-md bg-indigo-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    {{ $t('Add user') }}</Link>
                </div> -->
                <!-- NEW UI CODE -->
                <div class="flex-1 mb-8">
                    <div class="flex items-center gap-3 mb-3">
                        <div
                            class="w-10 h-10 text-white rounded-xl bg-gradient-to-br from-[#ff5100] to-[#ff7a3d] flex items-center justify-center shadow-lg shadow-[#ff5100]/25">
                            <UserCog />
                        </div>
                        <h2
                            class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-700 bg-clip-text text-transparent">
                            {{ $t('Users') }}
                        </h2>
                    </div>
                    <div class="flex items-start gap-2 text-gray-600">
                        <p class="text-sm md:text-base font-medium leading-relaxed">
                            {{ $t('Manage admin accounts') }}
                        </p>
                    </div>
                </div>
                <div>
                    <Link href="/admin/team/users/create"
                        class="bg-primary hover:bg-primary/90 text-white font-semibold px-4 md:px-6 py-2.5 rounded-xl transition-all duration-300 hover:scale-[1.02] shadow-lg flex items-center gap-2">
                    {{ $t('Add user') }}</Link>
                </div>
            </div>
            <UserTable :rows="props.rows" :filters="props.filters" :type="'admin'" />
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from "./../Layout/App.vue";
import { Link } from "@inertiajs/vue3";
import UserTable from '@/Components/Tables/UserTable.vue';
import { UserCog } from "lucide-vue-next";

const props = defineProps({ title: String, rows: Object, filters: Object });
</script>