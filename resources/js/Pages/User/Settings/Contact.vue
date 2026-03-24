<!-- <template>
    <SettingLayout :modules="props.modules">
        <div class="md:h-[90vh]">
            <div class="flex justify-center items-center">
                <div class="md:w-[60em]">
                    <div class="bg-white border border-slate-200 rounded-lg pt-2 text-sm px-4 mb-20">
                        <div class="w-full py-2 mb-4 mt-2">
                            <div class="flex w-full">
                                <div class="text-md">
                                    <h4 class="text-[16px]">{{ $t('Contact fields location') }}</h4>
                                    <div class="mb-1 text-slate-500">{{ $t(`Place custom fields before or after the
                                        address section`) }}</div>
                                </div>
                                <div class="ml-auto">
                                    <FormSelect v-model="location" :options="locationOptions" :name="''"
                                        :error="form2.errors.location" :class="'col-span-6'"
                                        :placeholder="'Select Here'" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-lg py-2 text-sm pb-4 px-4 mb-20">
                        <div class="w-full py-2 mb-2 mt-2">
                            <div class="flex w-full mb-4">
                                <div class="text-md">
                                    <h4 class="text-[16px]">{{ $t('Custom contact fields') }}</h4>
                                    <span class="flex items-center mt-1 text-slate-500">
                                        {{ $t('Create custom input fields for the contacts section') }}
                                    </span>
                                </div>
                                <div class="ml-auto">
                                    <button @click="openModal()"
                                        class="float-right rounded-md bg-primary px-3 py-2 text-sm text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{
                                            $t('Add fields') }}</button>
                                </div>
                            </div>
                            <div class="w-5/5">
                                <ContactFieldTable :rows="props.rows" @edit="openModal" @delete="openAlert" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :label="label" :isOpen=isOpenFormModal>
            <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-4">
                <form @submit.prevent="submitForm()" class="gap-y-4">
                    <div class="grid grid-cols gap-y-4">
                        <FormInput v-model="form.name" :name="$t('Label')" :error="form.errors.name" :type="'text'"
                            :class="'col-span-6'" />
                        <FormSelect v-model="form.component" :options="componentOptions" :name="$t('Form component')"
                            :error="form.errors.component" :class="'col-span-6'" :optionClassName="'h-[8em]'"
                            :placeholder="'Select Here'" />
                        <FormSelect v-if="form.component === 'input'" v-model="form.type" :options="inputTypeOptions"
                            :name="$t('Input type')" :error="form.errors.type" :class="'col-span-6'"
                            :optionClassName="'h-[8em]'" :placeholder="'Select Here'" />
                        <div v-if="form.component === 'select'" :class="'col-span-6 mt-2'">
                            <div class="flex pb-2">
                                <span class="text-sm">{{ $t('Select options') }}</span>
                                <div class="col-1 ml-auto">
                                    <button class="btn bg-primary text-xs text-white px-2 rounded-md py-1"
                                        @click="add">{{ $t('Add option') }}</button>
                                </div>
                            </div>
                            <div class="bg-slate-100 rounded-lg p-2">
                                <draggable tag="div" :list="form.options" class="mt-2 w-full" handle=".handle"
                                    item-key="id">
                                    <template #item="{ element, index }">
                                        <div class="flex items-center w-full space-x-4">
                                            <span class="handle">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24">
                                                    <path fill="currentColor"
                                                        d="M9 19.23q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36" />
                                                </svg>
                                            </span>

                                            <FormInput v-model="element.value" :name="''" :type="'text'"
                                                :class="'w-full py-2'" :required="true" />

                                            <span v-if="index != 0" @click="removeAt(index)"
                                                class="cursor-pointer hover:bg-red-600 hover:text-white p-1 rounded-full">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24">
                                                    <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="1.5"
                                                        d="m20 9l-1.995 11.346A2 2 0 0 1 16.035 22h-8.07a2 2 0 0 1-1.97-1.654L4 9m17-3h-5.625M3 6h5.625m0 0V4a2 2 0 0 1 2-2h2.75a2 2 0 0 1 2 2v2m-6.75 0h6.75" />
                                                </svg>
                                            </span>
                                            <span v-else @click="add"
                                                class="cursor-pointer hover:bg-primary hover:text-white p-1 rounded-full">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24">
                                                    <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M3 17h7m5-1h3m0 0h3m-3 0v3m0-3v-3M3 12h11M3 7h11" />
                                                </svg>
                                            </span>
                                        </div>
                                    </template>
                                </draggable>
                            </div>
                        </div>
                        <div class="flex space-x-4 py-3" :class="'col-span-6'">
                            <span class="text-sm">{{ $t('Is required') }}</span>
                            <div class="w-12 h-6 flex items-center bg-gray-300 rounded-full p-1"
                                :class="{ 'bg-primary': form.required != 0 }" @click="toggleRequiredInput()">
                                <div class="bg-white w-4 h-4 rounded-full shadow-md transform duration-300 ease-in-out"
                                    :class="{ 'translate-x-6': form.required != 0 }"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex">
                        <button type="button" @click.self="isOpenFormModal = false"
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
    </SettingLayout>
</template>
<script setup>
import SettingLayout from "./Layout.vue";
import axios from "axios";
import { ref, watch } from 'vue';
import { useForm } from "@inertiajs/vue3";
import { trans } from 'laravel-vue-i18n';
import draggable from 'vuedraggable';
import Modal from '@/Components/Modal.vue';
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import ContactFieldTable from '@/Components/Tables/ContactFieldTable.vue';

const props = defineProps(['rows', 'filters', 'settings', 'modules']);
const isOpenFormModal = ref(false);
const label = ref('Add Contact Field');
const formUrl = ref('/contact-fields');
const formMethod = ref('post');
const config = ref(props.settings.metadata);
const settings = ref(config.value ? JSON.parse(config.value) : null);
const isLoading = ref(false);
const location = ref(settings?.value?.contacts?.location ? settings?.value?.contacts?.location : null);
let id = 0;

const form = useForm({
    name: null,
    component: null,
    type: null,
    required: 0,
    options: [
        { value: "", id: 0 },
    ],
});

const form2 = useForm({
    location: null,
});

const openModal = (key, formData = {}) => {
    label.value = trans('Add contact field');
    formUrl.value = '/contact-fields';
    formMethod.value = 'post';

    if (key != null) {
        label.value = trans('Edit contact field');
        formUrl.value = '/contact-fields/' + key;
        formMethod.value = 'put';
        getRow();
    } else {
        id = 0;
        form.name = null;
        form.type = null;
        form.options = [
            { value: "", id: 0 },
        ];
        isOpenFormModal.value = true;
    }
}

function getRow() {
    axios.get(formUrl.value).then((response) => {
        const { data } = response;
        console.log(data.item);

        if (data.item.type === 'select') {
            form['name'] = data.item.name,
                form['component'] = data.item.type;
            form['required'] = data.item.required;

            const inputString = data.item.value;
            const transformedArray = inputString.split(', ').map((value, index) => ({
                id: index,
                value: value
            }));
            id = transformedArray.length - 1;
            form['options'] = transformedArray;
        } else if (data.item.type === 'input') {
            form['name'] = data.item.name,
                form['component'] = data.item.type;
            form['type'] = data.item.value;
            form['required'] = data.item.required;
        } else {
            form['name'] = data.item.name,
                form['component'] = data.item.type;
            form['required'] = data.item.required;
        }

        isOpenFormModal.value = true;
    })
        .catch((error) => {
            // console.error('Error:', error);
        });
}


const inputTypeOptions = [
    { label: trans('Text'), value: 'text' },
    { label: trans('Number'), value: 'number' },
    { label: trans('Email'), value: 'email' },
    { label: trans('URL'), value: 'url' },
    { label: trans('Date'), value: 'date' },
    { label: trans('Time'), value: 'time' },
    { label: trans('Date & time'), value: 'datetime-local' },
];

const componentOptions = [
    { label: trans('Input'), value: 'input' },
    { label: trans('Select box'), value: 'select' },
    { label: trans('Text area'), value: 'textarea' },
    { label: trans('Checkbox'), value: 'checkbox' },
];

const locationOptions = [
    { label: trans('Before address'), value: 'before' },
    { label: trans('After address'), value: 'after' },
];

const toggleRequiredInput = () => {
    form.required = form.required === 0 ? 1 : 0;
};

const removeAt = (idx) => {
    form.options.splice(idx, 1);
};

const add = () => {
    id++;
    form.options.push({ id, value: "" });
};

const submitForm = async () => {
    isLoading.value = true;

    if (formMethod.value == 'post') {
        form.post(formUrl.value, {
            preserveScroll: true,
            onFinish: () => {
                isLoading.value = false;
            },
            onSuccess: () => {
                isOpenFormModal.value = false;
            }
        })
    } else {
        form.put(formUrl.value, {
            preserveScroll: true,
            onFinish: () => {
                isLoading.value = false;
            },
            onSuccess: () => {
                isOpenFormModal.value = false;
            }
        })
    }
};

watch(location, (newValue, oldValue) => {
    if (newValue !== oldValue) {
        form2.location = location;

        submitForm2();
    }
});

const submitForm2 = async () => {
    form2.post('/settings/contacts', {
        preserveScroll: true,
    })
};
</script> -->


<!-- ============================================ NEW UI CODE ============================================= -->

<template>
    <SettingLayout :modules="props.modules">
        <div class="space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <div class="w-full mx-auto space-y-6">

                <!-- Contact Fields Location Card -->
                <div
                    class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-lg transition-all duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between gap-6">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="p-3 bg-gradient-to-br from-[#ff5100]/10 to-[#ff7340]/10 rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="currentColor"
                                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7m0 9.5a2.5 2.5 0 0 1 0-5a2.5 2.5 0 0 1 0 5" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-base font-bold text-slate-900 mb-1">{{ $t('Contact fields location')
                                    }}</h4>
                                    <p class="text-sm text-slate-600">{{ $t(`Place custom fields before or after the
                                        address section`) }}</p>
                                </div>
                            </div>
                            <div class="w-48">
                                <FormSelect v-model="location" :options="locationOptions" :name="''"
                                    :error="form2.errors.location" :class="''" :placeholder="'Select Here'" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Contact Fields Card -->
                <div
                    class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-lg transition-all duration-300">
                    <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-white">
                                        <path fill="currentColor"
                                            d="M19 3h-4.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2m-7 0c.55 0 1 .45 1 1s-.45 1-1 1s-1-.45-1-1s.45-1 1-1m2 14H7v-2h7zm3-4H7v-2h10zm0-4H7V7h10z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-slate-900">{{ $t('Custom contact fields') }}</h2>
                                    <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                        {{ $t('Create custom input fields for the contacts section') }}
                                    </div>
                                </div>
                            </div>
                            <button @click="openModal()" type="button"
                                class="group flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:shadow-lg transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M11 13H5v-2h6V5h2v6h6v2h-6v6h-2z" />
                                </svg>
                                {{ $t('Add fields') }}
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="p-6">
                        <ContactFieldTable :rows="props.rows" @edit="openModal" @delete="openAlert" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Field Modal -->
        <Modal :label="label" :isOpen="isOpenFormModal">
            <div class="mt-5 space-y-5">
                <div class="space-y-5">
                    <!-- Label -->
                    <FormInput v-model="form.name" :name="$t('Label')" :error="form.errors.name" :type="'text'"
                        :class="''" />

                    <!-- Form Component -->
                    <FormSelect v-model="form.component" :options="componentOptions" :name="$t('Form component')"
                        :error="form.errors.component" :class="''" :optionClassName="'h-[8em]'"
                        :placeholder="'Select Here'" />

                    <!-- Input Type (conditional) -->
                    <FormSelect v-if="form.component === 'input'" v-model="form.type" :options="inputTypeOptions"
                        :name="$t('Input type')" :error="form.errors.type" :class="''" :optionClassName="'h-[8em]'"
                        :placeholder="'Select Here'" />

                    <!-- Select Options (conditional) -->
                    <div v-if="form.component === 'select'" class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-slate-900">{{ $t('Select options') }}</span>
                            <button type="button" @click="add"
                                class="flex items-center gap-1.5 px-3 py-1.5 bg-[#ff5100] hover:bg-[#ff7340] text-white text-xs font-medium rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M11 13H5v-2h6V5h2v6h6v2h-6v6h-2z" />
                                </svg>
                                {{ $t('Add option') }}
                            </button>
                        </div>
                        <div
                            class="bg-gradient-to-br from-slate-50 to-slate-100/50 rounded-xl p-4 border border-slate-200 space-y-3">
                            <draggable tag="div" :list="form.options" class="space-y-3" handle=".handle" item-key="id">
                                <template #item="{ element, index }">
                                    <div
                                        class="flex items-center gap-3 p-3 bg-white rounded-lg border border-slate-200 hover:border-[#ff5100]/30 transition-colors group">
                                        <span
                                            class="handle cursor-move p-1 hover:bg-slate-100 rounded transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                viewBox="0 0 24 24" class="text-slate-400 group-hover:text-[#ff5100]">
                                                <path fill="currentColor"
                                                    d="M9 19.23q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36" />
                                            </svg>
                                        </span>

                                        <div class="flex-1">
                                            <FormInput v-model="element.value" :name="''" :type="'text'" :class="''"
                                                :required="true" />
                                        </div>

                                        <button v-if="index != 0" type="button" @click="removeAt(index)"
                                            class="p-2 hover:bg-red-50 rounded-lg transition-colors group/delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24"
                                                class="text-slate-400 group-hover/delete:text-red-600">
                                                <path fill="currentColor"
                                                    d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6zM8 9h8v10H8zm7.5-5l-1-1h-5l-1 1H5v2h14V4z" />
                                            </svg>
                                        </button>
                                        <button v-else type="button" @click="add"
                                            class="p-2 hover:bg-[#ff5100]/10 rounded-lg transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" class="text-[#ff5100]">
                                                <path fill="currentColor" d="M11 13H5v-2h6V5h2v6h6v2h-6v6h-2z" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </draggable>
                        </div>
                    </div>

                    <!-- Is Required Toggle -->
                    <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-sm font-semibold text-slate-900">{{ $t('Is required') }}</span>
                        <div @click="toggleRequiredInput()"
                            class="relative w-14 h-7 flex items-center rounded-full p-1 cursor-pointer transition-colors duration-300"
                            :class="form.required != 0 ? 'bg-gradient-to-r from-[#ff5100] to-[#ff7340]' : 'bg-gray-300'">
                            <div class="bg-white w-5 h-5 rounded-full shadow-md transform transition-transform duration-300"
                                :class="{ 'translate-x-7': form.required != 0 }"></div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click.self="isOpenFormModal = false"
                            class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition-colors">
                            {{ $t('Cancel') }}
                        </button>
                        <button type="button" @click="submitForm"
                            :class="['flex-1 px-4 py-2.5 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl hover:shadow-lg transition-all', { 'opacity-50': isLoading }]"
                            :disabled="isLoading">
                            <span v-if="isLoading" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                    viewBox="0 0 24 24">
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
                </div>
            </div>
        </Modal>
    </SettingLayout>
</template>

<script setup>
import SettingLayout from "./Layout.vue";
import { ref, watch } from 'vue';
import { useForm } from "@inertiajs/vue3";
import { trans } from 'laravel-vue-i18n';
import draggable from 'vuedraggable';
import Modal from '@/Components/Modal.vue';
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import ContactFieldTable from '@/Components/Tables/ContactFieldTable.vue';

const props = defineProps(['rows', 'filters', 'settings', 'modules']);
const isOpenFormModal = ref(false);
const label = ref('Add Contact Field');
const formUrl = ref('/contact-fields');
const formMethod = ref('post');
const config = ref(props.settings.metadata);
const settings = ref(config.value ? JSON.parse(config.value) : null);
const isLoading = ref(false);
const location = ref(settings?.value?.contacts?.location ? settings?.value?.contacts?.location : null);
let id = 0;

const form = useForm({
    name: null,
    component: null,
    type: null,
    required: 0,
    options: [
        { value: "", id: 0 },
    ],
});

const form2 = useForm({
    location: null,
});

const openModal = (key, formData = {}) => {
    label.value = trans('Add contact field');
    formUrl.value = '/contact-fields';
    formMethod.value = 'post';

    if (key != null) {
        label.value = trans('Edit contact field');
        formUrl.value = '/contact-fields/' + key;
        formMethod.value = 'put';
        getRow();
    } else {
        id = 0;
        form.name = null;
        form.component = null;
        form.type = null;
        form.required = 0;
        form.options = [
            { value: "", id: 0 },
        ];
        isOpenFormModal.value = true;
    }
}

function getRow() {
    fetch(formUrl.value)
        .then(response => response.json())
        .then(response => {
            const { data } = response;

            if (data.item.type === 'select') {
                form.name = data.item.name;
                form.component = data.item.type;
                form.required = data.item.required;

                const inputString = data.item.value;
                const transformedArray = inputString.split(', ').map((value, index) => ({
                    id: index,
                    value: value
                }));
                id = transformedArray.length - 1;
                form.options = transformedArray;
            } else if (data.item.type === 'input') {
                form.name = data.item.name;
                form.component = data.item.type;
                form.type = data.item.value;
                form.required = data.item.required;
            } else {
                form.name = data.item.name;
                form.component = data.item.type;
                form.required = data.item.required;
            }

            isOpenFormModal.value = true;
        })
        .catch((error) => {
            console.error('Error:', error);
        });
}

const inputTypeOptions = [
    { label: trans('Text'), value: 'text' },
    { label: trans('Number'), value: 'number' },
    { label: trans('Email'), value: 'email' },
    { label: trans('URL'), value: 'url' },
    { label: trans('Date'), value: 'date' },
    { label: trans('Time'), value: 'time' },
    { label: trans('Date & time'), value: 'datetime-local' },
];

const componentOptions = [
    { label: trans('Input'), value: 'input' },
    { label: trans('Select box'), value: 'select' },
    { label: trans('Text area'), value: 'textarea' },
    { label: trans('Checkbox'), value: 'checkbox' },
];

const locationOptions = [
    { label: trans('Before address'), value: 'before' },
    { label: trans('After address'), value: 'after' },
];

const toggleRequiredInput = () => {
    form.required = form.required === 0 ? 1 : 0;
};

const removeAt = (idx) => {
    form.options.splice(idx, 1);
};

const add = () => {
    id++;
    form.options.push({ id, value: "" });
};

const submitForm = async () => {
    isLoading.value = true;

    if (formMethod.value == 'post') {
        form.post(formUrl.value, {
            preserveScroll: true,
            onFinish: () => {
                isLoading.value = false;
            },
            onSuccess: () => {
                isOpenFormModal.value = false;
            }
        })
    } else {
        form.put(formUrl.value, {
            preserveScroll: true,
            onFinish: () => {
                isLoading.value = false;
            },
            onSuccess: () => {
                isOpenFormModal.value = false;
            }
        })
    }
};

watch(location, (newValue, oldValue) => {
    if (newValue !== oldValue) {
        form2.location = location;
        submitForm2();
    }
});

const submitForm2 = async () => {
    form2.post('/settings/contacts', {
        preserveScroll: true,
    })
};

const openAlert = (key) => {
    // Handle delete confirmation
    console.log('Delete:', key);
};
</script>

<style scoped>
.slide-fade-enter-active {
    transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
    transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    transform: translateY(-10px);
    opacity: 0;
}
</style>