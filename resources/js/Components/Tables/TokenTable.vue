<!-- <script setup>
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import AlertModal from '@/Components/AlertModal.vue';
import { useAlertModal } from '@/Composables/useAlertModal';
import 'vue3-toastify/dist/index.css';
import Table from '@/Components/Table.vue';
import TableHeader from '@/Components/TableHeader.vue';
import TableHeaderRow from '@/Components/TableHeaderRow.vue';
import TableHeaderRowItem from '@/Components/TableHeaderRowItem.vue';
import TableBody from '@/Components/TableBody.vue';
import TableBodyRow from '@/Components/TableBodyRow.vue';
import TableBodyRowItem from '@/Components/TableBodyRowItem.vue';
import Dropdown from '@/Components/Dropdown.vue';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    }
});

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();

const form = useForm({ 'test': null });
const copiedRef = ref(null);
const unMaskedRef = ref(null);

const deleteAction = (key) => {
    form.delete('/developer-tools/access-tokens/' + key);
}

const isLastRow = (index) => {
    return index === props.rows.data.length - 1;
}

const emit = defineEmits(['delete']);

const copyRow = async (token) => {
    copiedRef.value = token;

    const tempInput = document.createElement("textarea");
    tempInput.value = token;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);

    setTimeout(() => {
        copiedRef.value = null;
    }, 2000);
};

const maskToken = (token) => {
    if (unMaskedRef.value === token) {
        return token;
    } else {
        return token.replace(/./g, '*'); // Replace each character with '*'
    }
};

const unMask = (token) => {
    unMaskedRef.value = token; // Toggle the 'masked' property of the item
};
</script>
<template>
    <Table :rows="rows">
        <TableHeader>
            <TableHeaderRow>
                <TableHeaderRowItem :position="'first'">{{ $t('Token') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Created on') }}</TableHeaderRowItem>
                <TableHeaderRowItem :position="'last'"></TableHeaderRowItem>
            </TableHeaderRow>
        </TableHeader>
        <TableBody>
            <TableBodyRow v-for="(item, index) in rows.data" :key="index" :class="!isLastRow(index) ? 'border-b' : ''">
                <TableBodyRowItem :position="'first'" class="capitalize">
                    <div class="flex">
                        <div class="text-left mr-3 text-sm relative">
                            <span>{{ maskToken(item.token) }}</span>
                            <span
                                class="bg-gray-50 text-xs p-1 border border-dashed rounded-[5px] absolute top-1/2 transform -translate-y-1/2"
                                :class="copiedRef === item.token ? '' : 'hidden'">{{ $t('Copied') }}</span>
                        </div>
                        <span class="cursor-pointer mr-1" @click="unMask(item.token)">
                            <svg v-if="unMaskedRef != item.token" xmlns="http://www.w3.org/2000/svg" width="20"
                                height="20" viewBox="0 0 24 24">
                                <g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd">
                                    <path
                                        d="M12 8.25a3.75 3.75 0 1 0 0 7.5a3.75 3.75 0 0 0 0-7.5M9.75 12a2.25 2.25 0 1 1 4.5 0a2.25 2.25 0 0 1-4.5 0" />
                                    <path
                                        d="M12 3.25c-4.514 0-7.555 2.704-9.32 4.997l-.031.041c-.4.519-.767.996-1.016 1.56c-.267.605-.383 1.264-.383 2.152c0 .888.116 1.547.383 2.152c.25.564.617 1.042 1.016 1.56l.032.041C4.445 18.046 7.486 20.75 12 20.75c4.514 0 7.555-2.704 9.32-4.997l.031-.041c.4-.518.767-.996 1.016-1.56c.267-.605.383-1.264.383-2.152c0-.888-.116-1.547-.383-2.152c-.25-.564-.617-1.041-1.016-1.56l-.032-.041C19.555 5.954 16.514 3.25 12 3.25M3.87 9.162C5.498 7.045 8.15 4.75 12 4.75c3.85 0 6.501 2.295 8.13 4.412c.44.57.696.91.865 1.292c.158.358.255.795.255 1.546s-.097 1.188-.255 1.546c-.169.382-.426.722-.864 1.292C18.5 16.955 15.85 19.25 12 19.25c-3.85 0-6.501-2.295-8.13-4.412c-.44-.57-.696-.91-.865-1.292c-.158-.358-.255-.795-.255-1.546s.097-1.188.255-1.546c.169-.382.426-.722.864-1.292" />
                                </g>
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2">
                                    <path
                                        d="M9.88 9.88a3 3 0 1 0 4.24 4.24m-3.39-9.04A10 10 0 0 1 12 5c7 0 10 7 10 7a13.2 13.2 0 0 1-1.67 2.68" />
                                    <path
                                        d="M6.61 6.61A13.5 13.5 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61M2 2l20 20" />
                                </g>
                            </svg>
                        </span>
                        <span class="cursor-pointer" @click="copyRow(item.token)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <g fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path
                                        d="M6 11c0-2.828 0-4.243.879-5.121C7.757 5 9.172 5 12 5h3c2.828 0 4.243 0 5.121.879C21 6.757 21 8.172 21 11v5c0 2.828 0 4.243-.879 5.121C19.243 22 17.828 22 15 22h-3c-2.828 0-4.243 0-5.121-.879C6 20.243 6 18.828 6 16z" />
                                    <path
                                        d="M6 19a3 3 0 0 1-3-3v-6c0-3.771 0-5.657 1.172-6.828C5.343 2 7.229 2 11 2h4a3 3 0 0 1 3 3" />
                                </g>
                            </svg>
                        </span>
                    </div>
                </TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">
                    <div
                        class="py-1 px-2 bg-gray-50 rounded-[5px] border border-dashed w-[10em] truncate text-xs capitalize">
                        {{ item.created_at }}
                    </div>
                </TableBodyRowItem>
                <TableBodyRowItem :position="'last'">
                    <Dropdown :align="'right'" class="mt-2">
                        <button @click="openAlert(item.uuid)"
                            class="inline-flex w-full justify-center rounded-md text-sm font-medium text-black hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75">
                            <span class="hover:bg-[#F6F7F9] hover:rounded-full w-[fit-content] p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M16 9v10H8V9zm-1.5-6h-5l-1 1H5v2h14V4h-3.5zM18 7H6v12c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2z" />
                                </svg>
                            </span>
                        </button>
                    </Dropdown>
                </TableBodyRowItem>
            </TableBodyRow>
        </TableBody>
    </Table>

    <AlertModal v-model="isOpenAlert" @confirm="() => confirmAlert(deleteAction)" :label="$t('Delete row')"
        :description="$t('Are you sure you want to delete this row? This action can not be undone')" />
</template> -->




<!-- ============================================ NEW UI CODE ============================================= -->

<script setup>
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import AlertModal from '@/Components/AlertModal.vue';
import { useAlertModal } from '@/Composables/useAlertModal';
import 'vue3-toastify/dist/index.css';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    }
});

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();

const form = useForm({ 'test': null });
const copiedRef = ref(null);
const unMaskedRef = ref(null);

const deleteAction = (key) => {
    form.delete('/developer-tools/access-tokens/' + key);
}


const emit = defineEmits(['delete']);

const copyRow = async (token) => {
    copiedRef.value = token;

    const tempInput = document.createElement("textarea");
    tempInput.value = token;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);

    setTimeout(() => {
        copiedRef.value = null;
    }, 2000);
};

const maskToken = (token) => {
    if (unMaskedRef.value === token) {
        return token;
    } else {
        return token.replace(/./g, '•');
    }
};

const unMask = (token) => {
    if (unMaskedRef.value === token) {
        unMaskedRef.value = token.replace(/./g, '•');
    } else {
        unMaskedRef.value = token;
    }
};
</script>

<template>
    <div class="overflow-hidden">
        <!-- Custom Table Design -->
        <div class="overflow-x-auto">

            <!-- Table Header -->
            <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b-2 border-[#ff5100]/20">
                <div class="grid grid-cols-12 gap-4 items-center">
                    <div class="col-span-6 md:col-span-5">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-600">
                            {{ $t('Token') }}
                        </span>
                    </div>
                    <div class="hidden md:block md:col-span-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-600">
                            {{ $t('Created on') }}
                        </span>
                    </div>
                    <div class="col-span-6 md:col-span-3 text-right">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-600">
                            {{ $t('Actions') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Table Body -->
            <div class="divide-y divide-slate-100">
                <div v-for="(item, index) in rows.data" :key="index"
                    class="px-6 py-5 hover:bg-gradient-to-r hover:from-orange-50/50 hover:to-transparent transition-all duration-300 group">
                    <div class="grid grid-cols-12 gap-4 items-center">

                        <!-- Token Column -->
                        <div class="col-span-12 md:col-span-5">
                            <div class="flex items-center gap-3">
                                <!-- Token Display -->
                                <div class="relative flex-1 min-w-0">
                                    <div
                                        class="font-mono text-center !text-wrap text-sm text-slate-700 break-all bg-slate-50 px-4 py-3 rounded-xl border border-slate-200 group-hover:border-[#ff5100]/30 transition-colors duration-300">
                                        {{ unMaskedRef === item.token ? item.token :
                                            maskToken(item.token)?.slice(0, 6) }}
                                    </div>

                                    <!-- Copied Notification -->
                                    <transition enter-active-class="transition ease-out duration-200"
                                        enter-from-class="opacity-0 transform scale-90"
                                        enter-to-class="opacity-100 transform scale-100"
                                        leave-active-class="transition ease-in duration-150"
                                        leave-from-class="opacity-100 transform scale-100"
                                        leave-to-class="opacity-0 transform scale-90">
                                        <div v-if="copiedRef === item.token"
                                            class="absolute -top-10 left-1/2 transform -translate-x-1/2 bg-green-500 text-white text-xs px-3 py-1.5 rounded-lg shadow-lg whitespace-nowrap">
                                            <div class="flex items-center gap-1.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                {{ $t('Copied') }}
                                            </div>
                                            <div
                                                class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 rotate-45 w-2 h-2 bg-green-500">
                                            </div>
                                        </div>
                                    </transition>
                                </div>

                                <!-- Action Icons -->
                                <div class="flex items-center gap-1">
                                    <!-- Toggle Mask/Unmask -->
                                    <button @click="unMask(item.token)"
                                        class="p-2 hover:bg-[#ff5100]/10 rounded-lg transition-colors duration-200 group/btn"
                                        :title="unMaskedRef === item.token ? 'Hide token' : 'Show token'">
                                        <svg v-if="unMaskedRef !== item.token" xmlns="http://www.w3.org/2000/svg"
                                            width="20" height="20" viewBox="0 0 24 24"
                                            class="text-slate-400 group-hover/btn:text-[#ff5100] transition-colors">
                                            <g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd">
                                                <path
                                                    d="M12 8.25a3.75 3.75 0 1 0 0 7.5a3.75 3.75 0 0 0 0-7.5M9.75 12a2.25 2.25 0 1 1 4.5 0a2.25 2.25 0 0 1-4.5 0" />
                                                <path
                                                    d="M12 3.25c-4.514 0-7.555 2.704-9.32 4.997l-.031.041c-.4.519-.767.996-1.016 1.56c-.267.605-.383 1.264-.383 2.152c0 .888.116 1.547.383 2.152c.25.564.617 1.042 1.016 1.56l.032.041C4.445 18.046 7.486 20.75 12 20.75c4.514 0 7.555-2.704 9.32-4.997l.031-.041c.4-.518.767-.996 1.016-1.56c.267-.605.383-1.264.383-2.152c0-.888-.116-1.547-.383-2.152c-.25-.564-.617-1.041-1.016-1.56l-.032-.041C19.555 5.954 16.514 3.25 12 3.25M3.87 9.162C5.498 7.045 8.15 4.75 12 4.75c3.85 0 6.501 2.295 8.13 4.412c.44.57.696.91.865 1.292c.158.358.255.795.255 1.546s-.097 1.188-.255 1.546c-.169.382-.426.722-.864 1.292C18.5 16.955 15.85 19.25 12 19.25c-3.85 0-6.501-2.295-8.13-4.412c-.44-.57-.696-.91-.865-1.292c-.158-.358-.255-.795-.255-1.546s.097-1.188.255-1.546c.169-.382.426-.722.864-1.292" />
                                            </g>
                                        </svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" class="text-[#ff5100]">
                                            <g fill="none" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2">
                                                <path
                                                    d="M9.88 9.88a3 3 0 1 0 4.24 4.24m-3.39-9.04A10 10 0 0 1 12 5c7 0 10 7 10 7a13.2 13.2 0 0 1-1.67 2.68" />
                                                <path
                                                    d="M6.61 6.61A13.5 13.5 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61M2 2l20 20" />
                                            </g>
                                        </svg>
                                    </button>

                                    <!-- Copy Button -->
                                    <button @click="copyRow(item.token)"
                                        class="p-2 hover:bg-[#ff5100]/10 rounded-lg transition-colors duration-200 group/btn"
                                        title="Copy token">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24"
                                            class="text-slate-400 group-hover/btn:text-[#ff5100] transition-colors">
                                            <g fill="none" stroke="currentColor" stroke-width="2">
                                                <path
                                                    d="M6 11c0-2.828 0-4.243.879-5.121C7.757 5 9.172 5 12 5h3c2.828 0 4.243 0 5.121.879C21 6.757 21 8.172 21 11v5c0 2.828 0 4.243-.879 5.121C19.243 22 17.828 22 15 22h-3c-2.828 0-4.243 0-5.121-.879C6 20.243 6 18.828 6 16z" />
                                                <path
                                                    d="M6 19a3 3 0 0 1-3-3v-6c0-3.771 0-5.657 1.172-6.828C5.343 2 7.229 2 11 2h4a3 3 0 0 1 3 3" />
                                            </g>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Created Date Column (Hidden on mobile) -->
                        <div class="hidden md:block md:col-span-4">
                            <div
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-slate-100 to-slate-50 rounded-lg border border-slate-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    class="text-[#ff5100]">
                                    <path fill="none" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2"
                                        d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2" />
                                </svg>
                                <span class="text-sm text-slate-600 capitalize">
                                    {{ item.created_at }}
                                </span>
                            </div>
                        </div>

                        <!-- Actions Column -->
                        <div class="col-span-12 md:col-span-3 flex justify-end">
                            <button @click="openAlert(item.uuid)"
                                class="flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-all duration-200 hover:shadow-md group/delete">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    class="group-hover/delete:scale-110 transition-transform">
                                    <path fill="currentColor"
                                        d="M16 9v10H8V9zm-1.5-6h-5l-1 1H5v2h14V4h-3.5zM18 7H6v12c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2z" />
                                </svg>
                                <span class="text-sm font-medium hidden sm:inline">Delete</span>
                            </button>
                        </div>

                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="!rows.data || rows.data.length === 0" class="px-6 py-16 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div
                            class="w-20 h-20 bg-gradient-to-br from-slate-100 to-slate-200 rounded-full flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                class="text-slate-400">
                                <path fill="currentColor"
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-700 mb-2">No API Tokens Yet</h3>
                        <p class="text-sm text-slate-500">Generate your first API token to get started</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Alert Modal Component-->
    <AlertModal v-model="isOpenAlert" @confirm="() => confirmAlert(deleteAction)" :label="$t('Delete row')"
        :description="$t('Are you sure you want to delete this row? This action can not be undone')" />
</template>
