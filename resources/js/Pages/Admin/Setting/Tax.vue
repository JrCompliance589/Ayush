<!-- <template>
    <AppLayout>
        <div class="flex justify-between">
            <div>
                <h2 class="text-xl mb-1">{{ $t('Tax rates') }}</h2>
                <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                    </svg>
                    <span class="ml-1 mt-1">{{ $t('Configure tax rates') }}</span>
                </p>
            </div>
            <div>
                <button @click="openModal()" type="button"
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{
                        $t('Add tax rate') }}</button>
            </div>
        </div>

        <div class="bg-white rounded-md mb-4 py-4 px-4 grid grid-cols-2">
            <div>
                <h3>{{ $t('Tax calculation method') }}</h3>
                <span class="text-xs">{{ $t('Choose whether tax should be calculated inclusively or exclusively')
                    }}</span>
            </div>
            <div>
                <FormSelect v-model="form1.is_tax_inclusive" :name="''" :type="'text'" :options="taxCalculationOptions"
                    :error="form1.errors.is_tax_inclusive" :class="'sm:col-span-3'" />
                <button @click="submitForm1()" type="button"
                    class="rounded-md bg-gray-600 mt-4 px-3 py-2 text-sm text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{
                        $t('Update') }}</button>
            </div>
        </div>

        <TaxTable :rows="props.rows" @edit="openModal" @delete="openAlert" />

        <FormModal v-model="isOpenFormModal" :label="label" :url="formUrl" :form="form" :formInputs="formInputs"
            :formMethod="formMethod" @closeModal="isOpenFormModal = false" />
    </AppLayout>
</template>
<script setup>
import AppLayout from "./Layout/App.vue";
import axios from "axios";
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import TaxTable from '@/Components/Tables/TaxTable.vue';
import FormModal from '@/Components/FormModalModified.vue';
import FormSelect from '@/Components/FormSelect.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({ rows: Object, config: Object });
const isOpenFormModal = ref(false);
const label = ref('Add Tax Rate');
const formUrl = ref('/admin/tax-rates');
const formMethod = ref('post');

const form = {
    name: null,
    type: null,
    percentage: null,
    status: null
};

const getValueByKey = (key) => {
    const found = props.config.find(item => item.key === key);
    return found ? found.value : '';
};

const form1 = useForm({
    is_tax_inclusive: getValueByKey('is_tax_inclusive'),
});

const openModal = (key, formData = {}) => {
    label.value = trans('Add tax rate');
    formUrl.value = '/admin/tax-rates';
    formMethod.value = 'post';

    if (key != null) {
        label.value = trans('Edit tax rate');
        formUrl.value = '/admin/tax-rates/' + key;
        formMethod.value = 'put';
        getRow();
    } else {
        form.name = null;
        form.percentage = null;
        form.status = null;
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

const taxCalculationOptions = ref([
    { value: '1', label: trans('Inclusive') },
    { value: '0', label: trans('Exclusive') }
]);

const formInputs = [
    {
        inputType: 'FormInput',
        name: 'name',
        label: trans('name'),
        type: 'text',
        className: 'sm:col-span-6',
    },
    {
        inputType: 'FormInput',
        name: 'percentage',
        label: trans('Percentage'),
        type: 'number',
        className: 'sm:col-span-3',
    },
    {
        inputType: 'FormSelect',
        name: 'status',
        label: trans('status'),
        options: [
            { value: 'active', label: trans('Active') },
            { value: 'inactive', label: trans('Inactive') },
        ],
        className: 'sm:col-span-3',
    },
];

const submitForm1 = async () => {
    form1.put('/admin/settings', {
        preserveScroll: true,
    })
};
</script> -->


<!-- ============================================ NEW UI CODE ============================================= -->

<template>
    <AppLayout>
        <div class="p-6 md:p-8 space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">

            <!-- Tax Calculation Method Card -->
            <div class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                    <div class="flex items-center gap-3">
                        <div
                            class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                class="text-white">
                                <path fill="currentColor" d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">{{ $t('Tax Calculation Method') }}</h2>
                            <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    class="text-[#ff5100]">
                                    <path fill="none" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2"
                                        d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                </svg>
                                <span>{{ $t('Choose whether tax should be calculated inclusively or exclusively')
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid md:grid-cols-5 gap-6 items-start">
                        <div class="md:col-span-2 space-y-1">
                            <label class="text-sm font-semibold text-slate-900">{{ $t('Calculation Type') }}</label>
                            <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    class="text-[#ff5100] shrink-0 mt-0.5">
                                    <path fill="none" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2"
                                        d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                </svg>
                                <span>{{ $t('Select tax calculation method for all rates') }}</span>
                            </div>
                        </div>
                        <div class="md:col-span-3 flex items-center gap-4">
                            <div class="flex-1">
                                <FormSelect v-model="form1.is_tax_inclusive" :name="''" :type="'text'"
                                    :options="taxCalculationOptions" :error="form1.errors.is_tax_inclusive" />
                            </div>
                            <button @click="submitForm1()" type="button"
                                class="px-6 py-2.5 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 transition-all duration-200">
                                <span class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="m9.55 18l-5.7-5.7l1.425-1.425L9.55 15.15l9.175-9.175L20.15 7.4z" />
                                    </svg>
                                    {{ $t('Update') }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tax Rates Card -->
            <div class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Tax Rates') }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Configure tax rates') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Add Tax Rate Button -->
                        <button @click="openModal()" type="button"
                            class="group px-6 py-2.5 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 transition-all duration-200">
                            <span class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                                </svg>
                                {{ $t('Add tax rate') }}
                            </span>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Table Component -->
                    <TaxTable :rows="props.rows" @edit="openModal" @delete="openAlert" />
                </div>
            </div>
        </div>

        <!-- Form Modal Component-->
        <FormModal v-model="isOpenFormModal" :label="label" :url="formUrl" :form="form" :formInputs="formInputs"
            :formMethod="formMethod" @closeModal="isOpenFormModal = false" />
    </AppLayout>
</template>

<script setup>
import AppLayout from "./Layout/App.vue";
import axios from "axios";
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import TaxTable from '@/Components/Tables/TaxTable.vue';
import FormModal from '@/Components/FormModalModified.vue';
import FormSelect from '@/Components/FormSelect.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    rows: Object,
    config: Object
});

const isOpenFormModal = ref(false);
const label = ref('Add Tax Rate');
const formUrl = ref('/admin/tax-rates');
const formMethod = ref('post');

const form = {
    name: null,
    type: null,
    percentage: null,
    status: null
};

const getValueByKey = (key) => {
    const found = props.config.find(item => item.key === key);
    return found ? found.value : '';
};

const form1 = useForm({
    is_tax_inclusive: getValueByKey('is_tax_inclusive'),
});

const openModal = (key, formData = {}) => {
    label.value = trans('Add tax rate');
    formUrl.value = '/admin/tax-rates';
    formMethod.value = 'post';

    if (key != null) {
        label.value = trans('Edit tax rate');
        formUrl.value = '/admin/tax-rates/' + key;
        formMethod.value = 'put';
        getRow();
    } else {
        form.name = null;
        form.percentage = null;
        form.status = null;
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

const taxCalculationOptions = ref([
    { value: '1', label: trans('Inclusive') },
    { value: '0', label: trans('Exclusive') }
]);

const formInputs = [
    {
        inputType: 'FormInput',
        name: 'name',
        label: trans('name'),
        type: 'text',
        className: 'sm:col-span-6',
    },
    {
        inputType: 'FormInput',
        name: 'percentage',
        label: trans('Percentage'),
        type: 'number',
        className: 'sm:col-span-3',
    },
    {
        inputType: 'FormSelect',
        name: 'status',
        label: trans('status'),
        options: [
            { value: 'active', label: trans('Active') },
            { value: 'inactive', label: trans('Inactive') },
        ],
        className: 'sm:col-span-3',
    },
];

const submitForm1 = async () => {
    form1.put('/admin/settings', {
        preserveScroll: true,
    })
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