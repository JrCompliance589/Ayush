<script setup>
    import 'vue3-toastify/dist/index.css';
    import { ref } from 'vue';
    import debounce from 'lodash/debounce';
    import { router } from '@inertiajs/vue3';
    import Modal from '@/Components/Modal.vue';
    import Table from '@/Components/Table.vue';
    import TableHeader from '@/Components/TableHeader.vue';
    import TableHeaderRow from '@/Components/TableHeaderRow.vue';
    import TableHeaderRowItem from '@/Components/TableHeaderRowItem.vue';
    import TableBody from '@/Components/TableBody.vue';
    import TableBodyRow from '@/Components/TableBodyRow.vue';
    import TableBodyRowItem from '@/Components/TableBodyRowItem.vue';

    const props = defineProps({
        rows: {
            type: Object,
            required: true,
        },
        filters: {
            type: Object
        },
        uuid: {
            type: String
        }
    });
    
    const params = ref({
        search: props.filters.search,
    });

    const logs = ref(null);
    const retryLogs = ref([]);
    const messageStatus = ref(null);
    const isOpenModal = ref(false);
    const isSearching = ref(false);
    const emit = defineEmits(['delete']);

    const clearSearch = () => {
        params.value.search = null;
        runSearch();
    }

    const search = debounce(() => {
        isSearching.value = true;
        runSearch();
    }, 1000);

    const runSearch = () => {
        router.visit('/campaigns/' + props.uuid, {
            method: 'get',
            data: params.value,
        })
    }

    const openModal = (status, value, retries = []) => {
        messageStatus.value = status;
        logs.value = value;
        retryLogs.value = retries || [];
        isOpenModal.value = true;
    }

    const getStatus = (metadata) => {
        return JSON.parse(metadata).status;
    }

    const getErrorDetails = (metadata) => {
        return JSON.parse(metadata);
    }

    const getRetryErrorMessage = (metadata) => {
        try {
            const parsed = JSON.parse(metadata);
            if (parsed?.data?.error?.message) {
                return parsed.data.error.message;
            }
            if (parsed?.message) {
                return parsed.message;
            }
            return 'Unknown error';
        } catch (e) {
            return 'Failed to parse error details';
        }
    }
</script>
<template>
    <div class="bg-white flex items-center shadow-sm h-10 w-80 rounded-[0.5rem] mb-6 text-sm">
        <span class="pl-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 15l6 6m-11-4a7 7 0 1 1 0-14a7 7 0 0 1 0 14Z"/></svg>
        </span>
        <input @input="search" v-model="params.search" type="text" class="outline-none px-4 w-full" :placeholder="$t('Search campaigns')">
        <button v-if="isSearching === false && params.search" @click="clearSearch" type="button" class="pr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm3.7 12.3c.4.4.4 1 0 1.4c-.4.4-1 .4-1.4 0L12 13.4l-2.3 2.3c-.4.4-1 .4-1.4 0c-.4-.4-.4-1 0-1.4l2.3-2.3l-2.3-2.3c-.4-.4-.4-1 0-1.4c.4-.4 1-.4 1.4 0l2.3 2.3l2.3-2.3c.4-.4 1-.4 1.4 0c.4.4.4 1 0 1.4L13.4 12l2.3 2.3z"/></svg>
        </button>
        <span v-if="isSearching" class="pr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0"><animateTransform attributeName="transform" calcMode="discrete" dur="2.4s" repeatCount="indefinite" type="rotate" values="0 12 12;90 12 12;180 12 12;270 12 12"/><animate attributeName="opacity" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite" values="1;1;0"/></circle><circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0"><animateTransform attributeName="transform" begin="0.2s" calcMode="discrete" dur="2.4s" repeatCount="indefinite" type="rotate" values="30 12 12;120 12 12;210 12 12;300 12 12"/><animate attributeName="opacity" begin="0.2s" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite" values="1;1;0"/></circle><circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0"><animateTransform attributeName="transform" begin="0.4s" calcMode="discrete" dur="2.4s" repeatCount="indefinite" type="rotate" values="60 12 12;150 12 12;240 12 12;330 12 12"/><animate attributeName="opacity" begin="0.4s" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite" values="1;1;0"/></circle></svg>
        </span>
    </div>
    <Table :rows="rows">
        <TableHeader>
            <TableHeaderRow>
                <TableHeaderRowItem :position="'first'" class="hidden sm:table-cell">{{ $t('Contact') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Phone') }}</TableHeaderRowItem>
                <TableHeaderRowItem class="hidden sm:table-cell">{{ $t('Last updated') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Retries') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Status') }}</TableHeaderRowItem>
                <TableHeaderRowItem :position="'last'"></TableHeaderRowItem>
            </TableHeaderRow>
        </TableHeader>
        <TableBody>
            <TableBodyRow v-for="(item, index) in rows.data" :key="index">
                <TableBodyRowItem :position="'first'" class="hidden sm:table-cell">{{ item.contact.full_name }}</TableBodyRowItem>
                <TableBodyRowItem>
                    {{ item.contact.phone }}
                    <!-- {{ JSON.stringify(item) }} -->
                </TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">
                    <span v-if="item.status === 'success'" class="border-b border-dashed border-black">{{ item.chat.created_at }}</span>
                    <span v-else class="border-b border-dashed border-black">{{ item.created_at }}</span>
                </TableBodyRowItem>
                <TableBodyRowItem>
                    <span>
                        {{ item.retry_count }}
                    </span>
                </TableBodyRowItem>
                <TableBodyRowItem>
                    <span class="px-2 py-1 text-xs rounded-md capitalize" :class="item.status === 'success' ? 'bg-green-700 text-white' : 'bg-red-400 text-white'">
                        {{ item.status === 'success' ? item.chat.status : item.status }}
                        <!-- {{ JSON.stringify(item) }} -->
                        
                    </span>
                </TableBodyRowItem>
                <TableBodyRowItem>
                    <div @click="openModal(item.status, item.status === 'success' ? item.chat?.logs : item.metadata, item.retries)" class="flex items-center underline cursor-pointer">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><g fill="currentColor"><path d="M11 10.98a1 1 0 1 1 2 0v6a1 1 0 1 1-2 0zm1-4.929a1 1 0 1 0 0 2a1 1 0 0 0 0-2"/><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10s10-4.477 10-10S17.523 2 12 2M4 12a8 8 0 1 0 16 0a8 8 0 0 0-16 0" clip-rule="evenodd"/></g></svg>
                        <span>{{ $t('More info') }}</span>
                    </div>
                </TableBodyRowItem>
            </TableBodyRow>
        </TableBody>
    </Table>
   <Modal :label="$t('Message info')" :isOpen="isOpenModal">
        <div class="max-w-md w-full space-y-8">
            <div class="mt-8 space-y-2">
                <!-- Original Message Status -->
                <div v-if="messageStatus === 'success'" v-for="(log, index) in logs" class="text-sm border-b pb-2">
                    <div class="flex items-center capitalize">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="1.5" d="m1.75 9.75l2.5 2.5m3.5-4l2.5-2.5m-4.5 4l2.5 2.5l6-6.5" />
                        </svg>

                        <!-- If failed, show Rejected by Meta -->
                        <span v-if="getStatus(log.metadata) === 'failed'">
                            {{ $t('Rejected by Meta') }}
                        </span>

                        <!-- Otherwise, show normal status -->
                        <span v-else>
                            {{ $t(getStatus(log.metadata)) }}
                        </span>
                    </div>

                    <!-- Add a note when failed -->
                    <div v-if="getStatus(log.metadata) === 'failed'" class="text-xs text-red-600 mt-2 space-y-1">
                        <p>
                            {{ $t('Note: This message was rejected by Meta and could not be delivered. In order to maintain a healthy ecosystem engagement.') }}
                        </p>
                        <p>
                            {{ $t('Try again after 48 hours. If you repeatedly send to this number, your Business WhatsApp could be banned.') }}
                        </p>
                    </div>

                    <div>{{ log.created_at }}</div>


                </div>
                <div v-else-if="messageStatus === 'failed'">
                    <div class="text-sm mb-3 bg-red-800 p-2 rounded text-white">Error: {{
                        getErrorDetails(logs).data.error.message }}</div>
                    <div v-if="getErrorDetails(logs).data?.error?.error_data?.details" class="text-sm">{{
                        getErrorDetails(logs).data?.error?.error_data?.details }}</div>
                    <div v-else>{{ getErrorDetails(logs).data.error.message }}</div>
                </div>

                <!-- Retry Logs Section -->
                <div v-if="retryLogs && retryLogs.length > 0" class="mt-6 border-t pt-4">
                    <h4 class="text-sm font-semibold mb-3 flex items-center">
                        <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M12 4V2A10 10 0 0 0 2 12h2a8 8 0 0 1 8-8m7.1 2.9L20.5 5.5A10 10 0 0 0 12 2v2a8 8 0 0 1 5.9 2.6zM22 12h-2a8 8 0 0 1-8 8v2a10 10 0 0 0 10-10M12 20a8 8 0 0 1-5.9-2.6l-1.4 1.4A10 10 0 0 0 12 22z"/>
                        </svg>
                        {{ $t('Retry Attempts') }} ({{ retryLogs.length }})
                    </h4>
                    <div class="space-y-3">
                        <div v-for="(retry, index) in retryLogs" :key="retry.id" class="text-sm border rounded-lg p-3 bg-gray-50">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded mr-2">
                                        {{ $t('Retry') }} #{{ retry.attempt }}
                                    </span>
                                    <span class="px-2 py-0.5 text-xs rounded capitalize" 
                                          :class="retry.status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                        {{ retry.status }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-500">
                                    {{ retry.created_at }}
                                </span>
                            </div>
                            <div class="flex items-center text-xs text-gray-600">
                                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2m4.2 14.2L11 13V7h1.5v5.2l4.5 2.7z"/>
                                </svg>
                                <span>{{ $t('Time since previous') }}: <strong>{{ retry.time_gap }}</strong></span>
                            </div>
                            <div v-if="retry.status === 'failed' && retry.metadata" class="mt-2 text-xs text-red-600 bg-red-50 p-2 rounded">
                                {{ getRetryErrorMessage(retry.metadata) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 flex justify-center">
            <button type="button" @click="isOpenModal = false"
                class="inline-flex justify-center rounded-md border border-transparent bg-slate-200 px-4 py-2 text-sm text-slate-500 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                {{ $t('Close') }}
            </button>
        </div>

    </Modal>
</template>
  