<!-- <template>
    <AppLayout>
        <div class="flex justify-between">
            <div>
                <h2 class="text-xl mb-1">{{ $t('Languages & translations') }}</h2>
                <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                    </svg>
                    <span class="ml-1 mt-1">{{ $t('Add, edit & translate languages') }}</span>
                </p>
            </div>
            <div>
                <button @click="openModal()" type="button"
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{
                        $t('Add language') }}</button>
            </div>
        </div>

        <LangTable :rows="props.rows" :defaultLanguage="props.default_language" @edit="openModal" @delete="openAlert" />

        <FormModal v-model="isOpenFormModal" :label="label" :url="formUrl" :form="form" :formInputs="formInputs"
            :formMethod="formMethod" @closeModal="isOpenFormModal = false" />
    </AppLayout>
</template>
<script setup>
import AppLayout from "./../Layout/App.vue";
import axios from "axios";
import { ref } from 'vue';
import LangTable from '@/Components/Tables/LangTable.vue';
import FormModal from '@/Components/FormModalModified.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps(['rows', 'config', 'default_language']);
const isOpenFormModal = ref(false);
const label = ref('Add Tax Rate');
const formUrl = ref('/admin/tax-rates');
const formMethod = ref('post');

const form = {
    name: null,
    code: null,
    status: null,
    is_rtl: false,
};

const openModal = (key, formData = {}) => {
    label.value = trans('Add language');
    formUrl.value = '/admin/languages';
    formMethod.value = 'post';

    if (key != null) {
        label.value = trans('Edit language');
        formUrl.value = '/admin/languages/' + key;
        formMethod.value = 'put';
        getRow();
    } else {
        form.name = null;
        form.code = null;
        form.status = null;
        form.is_rtl = false;
        isOpenFormModal.value = true;
    }
}

function getRow() {
    axios.get(formUrl.value).then((response) => {
        const { data } = response;
        for (const key in data.item) {
            if (form.hasOwnProperty(key)) {
                form[key] = data.item[key];
            }
        }
        isOpenFormModal.value = true;
    })
        .catch((error) => {
            // console.error('Error:', error);
        });
}

const formInputs = [
    {
        inputType: 'FormInput',
        name: 'name',
        label: trans('Name'),
        type: 'text',
        className: 'sm:col-span-6',
    },
    {
        inputType: 'FormInput',
        name: 'code',
        label: trans('Code'),
        type: 'text',
        className: 'sm:col-span-3',
    },
    {
        inputType: 'FormSelect',
        name: 'status',
        label: trans('Status'),
        options: [
            { value: 'active', label: trans('Active') },
            { value: 'inactive', label: trans('Inactive') },
        ],
        className: 'sm:col-span-3',
    },
    {
        inputType: 'FormSelect',
        name: 'is_rtl',
        label: trans('Right to Left'),
        options: [
            { value: false, label: trans('False') },
            { value: true, label: trans('True') },
        ],
        className: 'sm:col-span-6',
    }
];
</script> -->

<!-- ============================================ NEW UI CODE ============================================= -->


<template>
    <AppLayout>
        <div class="p-6 md:p-8 space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <!-- Languages Card -->
            <div class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="m12.87 15.07l-2.54-2.51l.03-.03A17.52 17.52 0 0 0 14.07 6H17V4h-7V2H8v2H1v2h11.17C11.5 7.92 10.44 9.75 9 11.35C8.07 10.32 7.3 9.19 6.69 8h-2c.73 1.63 1.73 3.17 2.98 4.56l-5.09 5.02L4 19l5-5l3.11 3.11l.76-2.04M18.5 10h-2L12 22h2l1.12-3h4.75L21 22h2l-4.5-12m-2.62 7l1.62-4.33L19.12 17h-3.24Z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Languages & translations') }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Add, edit & translate languages') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Add Language Button -->
                        <button @click="openModal()" type="button"
                            class="group px-6 py-2.5 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 transition-all duration-200">
                            <span class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                                </svg>
                                {{ $t('Add language') }}
                            </span>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Table Component -->
                    <LangTable :rows="props.rows" :defaultLanguage="props.default_language" @edit="openModal"
                        @delete="openAlert" />
                </div>
            </div>
        </div>

        <!-- Form Modal Component-->
        <FormModal v-model="isOpenFormModal" :label="label" :url="formUrl" :form="form" :formInputs="formInputs"
            :formMethod="formMethod" @closeModal="isOpenFormModal = false" />
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import axios from "axios";
import { ref } from 'vue';
import LangTable from '@/Components/Tables/LangTable.vue';
import FormModal from '@/Components/FormModalModified.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps(['rows', 'config', 'default_language']);
const isOpenFormModal = ref(false);
const label = ref('Add Tax Rate');
const formUrl = ref('/admin/tax-rates');
const formMethod = ref('post');

const form = {
    name: null,
    code: null,
    status: null,
    is_rtl: false,
};

const openModal = (key, formData = {}) => {
    label.value = trans('Add language');
    formUrl.value = '/admin/languages';
    formMethod.value = 'post';

    if (key != null) {
        label.value = trans('Edit language');
        formUrl.value = '/admin/languages/' + key;
        formMethod.value = 'put';
        getRow();
    } else {
        form.name = null;
        form.code = null;
        form.status = null;
        form.is_rtl = false;
        isOpenFormModal.value = true;
    }
}

function getRow() {
    axios.get(formUrl.value).then((response) => {
        const { data } = response;
        for (const key in data.item) {
            if (form.hasOwnProperty(key)) {
                form[key] = data.item[key];
            }
        }
        isOpenFormModal.value = true;
    })
        .catch((error) => {
            // console.error('Error:', error);
        });
}

const formInputs = [
    {
        inputType: 'FormInput',
        name: 'name',
        label: trans('Name'),
        type: 'text',
        className: 'sm:col-span-6',
    },
    {
        inputType: 'FormInput',
        name: 'code',
        label: trans('Code'),
        type: 'text',
        className: 'sm:col-span-3',
    },
    {
        inputType: 'FormSelect',
        name: 'status',
        label: trans('Status'),
        options: [
            { value: 'active', label: trans('Active') },
            { value: 'inactive', label: trans('Inactive') },
        ],
        className: 'sm:col-span-3',
    },
    {
        inputType: 'FormSelect',
        name: 'is_rtl',
        label: trans('Right to Left'),
        options: [
            { value: false, label: trans('False') },
            { value: true, label: trans('True') },
        ],
        className: 'sm:col-span-6',
    }
];
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
