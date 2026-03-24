<!-- <script setup>
import { ref } from 'vue';
import debounce from 'lodash/debounce';
import { router } from '@inertiajs/vue3';
import { useForm } from "@inertiajs/vue3";
import AlertModal from '@/Components/AlertModal.vue';
import { useAlertModal } from '@/Composables/useAlertModal';
import 'vue3-toastify/dist/index.css';
import FormCheckbox from '@/Components/FormCheckbox.vue';
import FormInput from '@/Components/FormInput.vue';
import Modal from '@/Components/Modal.vue';
import Table from '@/Components/Table.vue';
import TableHeader from '@/Components/TableHeader.vue';
import TableHeaderRow from '@/Components/TableHeaderRow.vue';
import TableHeaderRowItem from '@/Components/TableHeaderRowItem.vue';
import TableBody from '@/Components/TableBody.vue';
import TableBodyRow from '@/Components/TableBodyRow.vue';
import TableBodyRowItem from '@/Components/TableBodyRowItem.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
import DropdownItem from '@/Components/DropdownItem.vue';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
    events: {
        type: Object
    }
});

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();

const form = useForm({ 'test': null });
const copiedRef = ref(null);
const unMaskedRef = ref(null);
const isOpenModal = ref(false);
const isOpenTestModal = ref(false);
const webhookUuid = ref(null);

const deleteAction = (key) => {
    form.delete('/developer-tools/webhooks/' + key);
}

const isLastRow = (index) => {
    return index === props.rows.data.length - 1;
}

const emit = defineEmits(['delete']);

function deleteItem(id) {
    emit('delete', id);
}

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

const form2 = useForm({
    url: null,
    events: [],
});

const editWebhook = (webhook) => {
    form2.url = webhook.url;
    form2.events = webhook.events.map(event => event.event);
    isOpenModal.value = true;
}

const openTestModal = (webhook) => {
    form2.url = webhook.url;
    form2.events = webhook.events.map(event => event.event);
    isOpenTestModal.value = true;
}

const form3 = useForm({
    url: null,
});

const loadingEvent = ref(null);

const triggerTest = (url, event) => {
    form3.url = url;
    loadingEvent.value = event;

    form3.post(`/webhooks/trigger/${event}/test`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            // Success handler logic
            loadingEvent.value = null;
        },
        onError: () => {
            // Reset the loading state even if there's an error
            loadingEvent.value = null;
        }
    });
}

const submitForm = async () => {
    form2.post('/developer-tools/webhooks/' + webhookUuid.value, {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            isOpenModal.value = false
        }
    })
};
</script>

<template>
    <Table :rows="rows">
        <TableHeader>
            <TableHeaderRow>
                <TableHeaderRowItem :position="'first'">{{ $t('Notification URL') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Events') }}</TableHeaderRowItem>
                <TableHeaderRowItem :position="'last'"></TableHeaderRowItem>
            </TableHeaderRow>
        </TableHeader>
        <TableBody>
            <TableBodyRow v-for="(item, index) in rows.data" :key="index" :class="!isLastRow(index) ? 'border-b' : ''">
                <TableBodyRowItem :position="'first'">
                    <div class="flex">
                        <div class="text-left mr-3 text-sm relative w-[10em] truncate">
                            <span>{{ item.url }}</span>
                        </div>
                    </div>
                </TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">
                    <div
                        class="py-1 px-2 bg-gray-50 rounded-[5px] border border-dashed w-[20em] truncate text-xs capitalize">
                        <span v-for="event in item.events">{{ event.event }}, </span>
                    </div>
                </TableBodyRowItem>
                <TableBodyRowItem :position="'last'">
                    <div class="flex items-center">
                        <button @click="openTestModal(item)"
                            class="bg-slate-100 border rounded-md py-1 px-4 h-[fit-content]">Test</button>
                        <Dropdown v-if="item.role != 'admin'" :align="'right'" class="mt-2">
                            <button
                                class="inline-flex w-full justify-center rounded-md text-sm font-medium text-black hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75">
                                <span class="hover:bg-[#F6F7F9] hover:rounded-full w-[fit-content] p-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M12 16a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2Z" />
                                    </svg>
                                </span>
                            </button>
                            <template #items>
                                <DropdownItemGroup>
                                    <DropdownItem as="button" @click="editWebhook(item); webhookUuid = item.uuid;">{{
                                        $t('View/edit') }}</DropdownItem>
                                    <DropdownItem as="button" @click="openAlert(item.uuid)">{{ $t('Delete') }}
                                    </DropdownItem>
                                </DropdownItemGroup>
                            </template>
                        </Dropdown>
                    </div>
                </TableBodyRowItem>
            </TableBodyRow>
        </TableBody>
    </Table>

    <Modal :label="$t('Edit/View Webhook')" :isOpen=isOpenModal>
        <div class="mt-5 grid grid-cols-1 gap-x-6">
            <form @submit.prevent="submitForm()" class="grid gap-x-6 sm:grid-cols-6">
                <FormInput v-model="form2.url" :error="form2.errors.url" :name="$t('URL')" :type="'text'"
                    :class="'sm:col-span-6'" />
                <h4 class="mt-4 text-sm">{{ $t('Events') }}</h4>
                <FormCheckbox v-for="event in props.events" :key="event" v-model="form2.events"
                    :error="form2.errors.events" :name="$t(event)" :label="$t(event)" :value="event" :type="'text'"
                    :class="'sm:col-span-6'" />

                <div class="mt-4 flex">
                    <button type="button" @click.self="isOpenModal = false"
                        class="inline-flex justify-center rounded-md border border-transparent bg-slate-50 px-4 py-2 text-sm text-slate-500 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 mr-4">{{
                            $t('Cancel') }}</button>
                    <button
                        :class="['inline-flex justify-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2', { 'opacity-50': form.processing }]"
                        :disabled="form2.processing">
                        <svg v-if="form2.processing" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
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

    <Modal :label="$t('Test Webhook Notifications')" :isOpen=isOpenTestModal>
        <div class="mt-5 grid grid-cols-1 gap-x-6">
            <div>
                <h4 class="mb-2">Webhook URL</h4>
                <h5 class="text-sm bg-slate-100 py-1 rounded-md px-2">{{ form2.url }}</h5>
            </div>
            <div class="mt-4">
                <h4 class="mb-2">Events</h4>
                <div v-for="event in form2.events">
                    <div class="flex items-center justify-between border-b py-1 text-sm">
                        <div>
                            <span class="capitalize">{{ event }}</span>
                        </div>
                        <div>
                            <button @click="triggerTest(form2.url, event)"
                                class="bg-slate-100 border rounded-md py-1 px-4 h-[fit-content]">
                                {{ loadingEvent === event ? 'Sending...' : 'Test' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid gap-x-6 sm:grid-cols-6">
                <div class="mt-4 flex">
                    <button type="button" @click.self="isOpenTestModal = false"
                        class="inline-flex justify-center rounded-md border border-transparent bg-slate-50 px-4 py-2 text-sm text-slate-500 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 mr-4">{{
                            $t('Cancel') }}</button>
                </div>
            </div>
        </div>
    </Modal>

    <AlertModal v-model="isOpenAlert" @confirm="() => confirmAlert(deleteAction)" :label="$t('Delete row')"
        :description="$t('Are you sure you want to delete this row? This action can not be undone')" />
</template> -->

<!-- ============================================ NEW UI CODE ============================================= -->

<script setup>
import { ref } from 'vue';
import debounce from 'lodash/debounce';
import { router } from '@inertiajs/vue3';
import { useForm } from "@inertiajs/vue3";
import AlertModal from '@/Components/AlertModal.vue';
import { useAlertModal } from '@/Composables/useAlertModal';
import 'vue3-toastify/dist/index.css';
import FormCheckbox from '@/Components/FormCheckbox.vue';
import FormInput from '@/Components/FormInput.vue';
import Modal from '@/Components/Modal.vue';
import Table from '@/Components/Table.vue';
import TableHeader from '@/Components/TableHeader.vue';
import TableHeaderRow from '@/Components/TableHeaderRow.vue';
import TableHeaderRowItem from '@/Components/TableHeaderRowItem.vue';
import TableBody from '@/Components/TableBody.vue';
import TableBodyRow from '@/Components/TableBodyRow.vue';
import TableBodyRowItem from '@/Components/TableBodyRowItem.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
import DropdownItem from '@/Components/DropdownItem.vue';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
    events: {
        type: Object
    }
});

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();

const form = useForm({ 'test': null });
const copiedRef = ref(null);
const unMaskedRef = ref(null);
const isOpenModal = ref(false);
const isOpenTestModal = ref(false);
const webhookUuid = ref(null);

const deleteAction = (key) => {
    form.delete('/developer-tools/webhooks/' + key);
}

const isLastRow = (index) => {
    return index === props.rows.data.length - 1;
}

const emit = defineEmits(['delete']);

function deleteItem(id) {
    emit('delete', id);
}

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
    unMaskedRef.value = token;
};

const form2 = useForm({
    url: null,
    events: [],
});

const editWebhook = (webhook) => {
    form2.url = webhook.url;
    form2.events = webhook.events.map(event => event.event);
    isOpenModal.value = true;
}

const openTestModal = (webhook) => {
    form2.url = webhook.url;
    form2.events = webhook.events.map(event => event.event);
    isOpenTestModal.value = true;
}

const form3 = useForm({
    url: null,
});

const loadingEvent = ref(null);

const triggerTest = (url, event) => {
    form3.url = url;
    loadingEvent.value = event;

    form3.post(`/webhooks/trigger/${event}/test`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            loadingEvent.value = null;
        },
        onError: () => {
            loadingEvent.value = null;
        }
    });
}

const submitForm = async () => {
    form2.post('/developer-tools/webhooks/' + webhookUuid.value, {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            isOpenModal.value = false
        }
    })
};
</script>

<template>
    <div class="overflow-hidden">
        <!-- Custom Table Design -->
        <div class="overflow-x-auto">

            <!-- Table Header -->
            <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b-2 border-[#ff5100]/20">
                <div class="grid grid-cols-12 gap-4 items-center">
                    <div class="col-span-12 md:col-span-5">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-600">
                            {{ $t('Notification URL') }}
                        </span>
                    </div>
                    <div class="hidden md:block md:col-span-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-600">
                            {{ $t('Events') }}
                        </span>
                    </div>
                    <div class="col-span-12 md:col-span-3 text-right">
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

                        <!-- URL Column -->
                        <div class="col-span-12 md:col-span-5">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="flex items-center gap-2 text-sm text-slate-700 bg-slate-50 px-4 py-3 rounded-xl border border-slate-200 group-hover:border-[#ff5100]/30 transition-colors duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" class="text-[#ff5100] flex-shrink-0">
                                            <path fill="currentColor"
                                                d="m7.05 11.293l-2.12 2.121a4 4 0 0 0 5.657 5.657l2.828-2.828a4 4 0 0 0 0-5.657l-1.06 1.06a2.5 2.5 0 0 1 0 3.536l-2.83 2.828a2.5 2.5 0 0 1-3.535-3.535l2.12-2.121z" />
                                            <path fill="currentColor"
                                                d="m15.889 11.646l2.121-2.12a2.5 2.5 0 0 0-3.535-3.536l-2.829 2.828a2.5 2.5 0 0 0 0 3.536l-1.06 1.06a4 4 0 0 1 0-5.657l2.828-2.828a4 4 0 0 1 5.657 5.657l-2.121 2.121z" />
                                        </svg>
                                        <span class="truncate">{{ item.url }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Events Column (Hidden on mobile) -->
                        <div class="hidden md:block md:col-span-4">
                            <div class="flex flex-wrap gap-2">
                                <span v-for="(event, idx) in item.events" :key="idx"
                                    class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-gradient-to-r from-[#ff5100]/10 to-orange-100/50 text-[#ff5100] border border-[#ff5100]/20 capitalize">
                                    {{ event.event }}
                                </span>
                            </div>
                        </div>

                        <!-- Actions Column -->
                        <div class="col-span-12 md:col-span-3 flex justify-end gap-2">
                            <!-- Test Button -->
                            <button @click="openTestModal(item)"
                                class="flex items-center gap-2 px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-all duration-200 hover:shadow-md group/test">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    class="group-hover/test:scale-110 transition-transform">
                                    <path fill="currentColor" d="M8 5v14l11-7z" />
                                </svg>
                                <span class="text-sm font-medium">Test</span>
                            </button>

                            <!-- Dropdown Menu -->
                            <Dropdown v-if="item.role != 'admin'" :align="'right'">
                                <button class="p-2 hover:bg-slate-100 rounded-lg transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-slate-600">
                                        <path fill="currentColor"
                                            d="M12 16a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2Z" />
                                    </svg>
                                </button>
                                <template #items>
                                    <DropdownItemGroup>
                                        <DropdownItem as="button" @click="editWebhook(item); webhookUuid = item.uuid;">
                                            {{ $t('View/edit') }}
                                        </DropdownItem>
                                        <DropdownItem as="button" @click="openAlert(item.uuid)">
                                            {{ $t('Delete') }}
                                        </DropdownItem>
                                    </DropdownItemGroup>
                                </template>
                            </Dropdown>
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
                                    d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                                <polyline points="15 3 21 3 21 9" fill="none" stroke="currentColor" stroke-width="2" />
                                <line x1="10" y1="14" x2="21" y2="3" stroke="currentColor" stroke-width="2" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-700 mb-2">No Webhooks Yet</h3>
                        <p class="text-sm text-slate-500">Create your first webhook to get started</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Edit/View Webhook Modal -->
    <Modal :label="$t('Edit/View Webhook')" :isOpen="isOpenModal">
        <div class="mt-5 grid grid-cols-1 gap-x-6">
            <form @submit.prevent="submitForm()" class="grid gap-x-6 sm:grid-cols-6">
                <FormInput v-model="form2.url" :error="form2.errors.url" :name="$t('URL')" :type="'text'"
                    :class="'sm:col-span-6'" />

                <div class="sm:col-span-6 mt-4">
                    <h4 class="text-sm font-semibold text-slate-700 mb-3">{{ $t('Events') }}</h4>
                    <div class="space-y-2">
                        <FormCheckbox v-for="event in props.events" :key="event" v-model="form2.events"
                            :error="form2.errors.events" :name="$t(event)" :label="$t(event)" :value="event"
                            :type="'text'" :class="'sm:col-span-6'" />
                    </div>
                </div>

                <div class="sm:col-span-6 mt-6 flex gap-3">
                    <button type="button" @click.self="isOpenModal = false"
                        class="flex-1 justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none transition-colors duration-200">
                        {{ $t('Cancel') }}
                    </button>
                    <button type="submit"
                        :class="['flex-1 justify-center rounded-xl border border-transparent bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary/90 focus:outline-none transition-all duration-200', { 'opacity-50 cursor-not-allowed': form2.processing }]"
                        :disabled="form2.processing">
                        <span v-if="form2.processing" class="flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                class="animate-spin">
                                <path fill="currentColor"
                                    d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                    opacity=".5" />
                                <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                    <animateTransform attributeName="transform" dur="1s" from="0 12 12"
                                        repeatCount="indefinite" to="360 12 12" type="rotate" />
                                </path>
                            </svg>
                            {{ $t('Saving...') }}
                        </span>
                        <span v-else>{{ $t('Save') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </Modal>

    <!-- Test Webhook Modal -->
    <Modal :label="$t('Test Webhook Notifications')" :isOpen="isOpenTestModal">
        <div class="mt-5 space-y-6">
            <!-- Webhook URL Display -->
            <div>
                <h4 class="text-sm font-semibold text-slate-700 mb-2">Webhook URL</h4>
                <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        class="text-[#ff5100] flex-shrink-0">
                        <path fill="currentColor"
                            d="m7.05 11.293l-2.12 2.121a4 4 0 0 0 5.657 5.657l2.828-2.828a4 4 0 0 0 0-5.657l-1.06 1.06a2.5 2.5 0 0 1 0 3.536l-2.83 2.828a2.5 2.5 0 0 1-3.535-3.535l2.12-2.121z" />
                        <path fill="currentColor"
                            d="m15.889 11.646l2.121-2.12a2.5 2.5 0 0 0-3.535-3.536l-2.829 2.828a2.5 2.5 0 0 0 0 3.536l-1.06 1.06a4 4 0 0 1 0-5.657l2.828-2.828a4 4 0 0 1 5.657 5.657l-2.121 2.121z" />
                    </svg>
                    <span class="text-sm text-slate-700 break-all">{{ form2.url }}</span>
                </div>
            </div>

            <!-- Events List -->
            <div>
                <h4 class="text-sm font-semibold text-slate-700 mb-3">Events</h4>
                <div class="space-y-2">
                    <div v-for="event in form2.events" :key="event"
                        class="flex items-center justify-between p-3 bg-white rounded-xl border border-slate-200 hover:border-[#ff5100]/30 transition-colors duration-200">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-[#ff5100]"></div>
                            <span class="text-sm font-medium text-slate-700 capitalize">{{ event }}</span>
                        </div>
                        <button @click="triggerTest(form2.url, event)" :disabled="loadingEvent === event"
                            class="px-4 py-1.5 bg-gradient-to-r from-[#ff5100] to-orange-600 text-white text-sm font-medium rounded-lg hover:shadow-lg hover:shadow-[#ff5100]/30 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span v-if="loadingEvent === event" class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    class="animate-spin">
                                    <path fill="currentColor"
                                        d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                        opacity=".5" />
                                    <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                        <animateTransform attributeName="transform" dur="1s" from="0 12 12"
                                            repeatCount="indefinite" to="360 12 12" type="rotate" />
                                    </path>
                                </svg>
                                Sending...
                            </span>
                            <span v-else>Test</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Close Button -->
            <div class="flex justify-end pt-4">
                <button type="button" @click.self="isOpenTestModal = false"
                    class="px-6 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none transition-colors duration-200">
                    {{ $t('Close') }}
                </button>
            </div>
        </div>
    </Modal>

    <!-- Alert Modal Component-->
    <AlertModal v-model="isOpenAlert" @confirm="() => confirmAlert(deleteAction)" :label="$t('Delete row')"
        :description="$t('Are you sure you want to delete this row? This action can not be undone')" />
</template>

<style scoped>
/* Custom animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }

    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.group:hover {
    animation: slideIn 0.3s ease-out;
}
</style>
