<!-- <template>
    <AppLayout>
        <div class="flex justify-between">
            <div>
                <h2 class="text-xl mb-1">{{ $t('Coupons') }}</h2>
                <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                    </svg>
                    <span class="ml-1 mt-1">{{ $t('Configure coupons') }}</span>
                </p>
            </div>
            <div>
                <button @click="openModal()" type="button"
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{
                        $t('Add coupon') }}</button>
            </div>
        </div>

        <CouponTable :rows="props.rows" @edit="openModal" @delete="openAlert" />

        <FormModal v-model="isOpenFormModal" :label="label" :url="formUrl" :form="form" :formInputs="formInputs"
            :formMethod="formMethod" @closeModal="isOpenFormModal = false" />
    </AppLayout>
</template>
<script setup>
import AppLayout from "./Layout/App.vue";
import axios from "axios";
import { ref } from 'vue';
import CouponTable from '@/Components/Tables/CouponTable.vue';
import FormModal from '@/Components/FormModalModified.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({ rows: Object });
const isOpenFormModal = ref(false);
const label = ref('Add Coupon');
const formUrl = ref('/admin/coupons');
const formMethod = ref('post');

const form = {
    name: null,
    code: null,
    percentage: null,
    quantity: null,
};

const openModal = (key, formData = {}) => {
    label.value = trans('Add coupon');
    formUrl.value = '/admin/coupons';
    formMethod.value = 'post';

    if (key != null) {
        label.value = trans('Edit coupon');
        formUrl.value = '/admin/coupons/' + key;
        formMethod.value = 'put';
        getRow();
    } else {
        form.name = null;
        form.code = null;
        form.percentage = null;
        form.quantity = null;
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
        label: trans('name'),
        type: 'text',
        className: 'sm:col-span-6',
    },
    {
        inputType: 'FormInput',
        name: 'code',
        label: trans('Coupon code'),
        type: 'text',
        className: 'sm:col-span-3',
    },
    {
        inputType: 'FormInput',
        name: 'percentage',
        label: trans('Percentage'),
        type: 'number',
        className: 'sm:col-span-3',
    },
    {
        inputType: 'FormInput',
        name: 'quantity',
        label: trans('Quantity'),
        type: 'number',
        className: 'sm:col-span-6',
    },
];
</script> -->


<!-- ============================================ NEW UI CODE ============================================= -->


<template>
    <AppLayout>
        <div class="p-6 md:p-8 space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <!-- Coupons Card -->
            <div class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58s1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41s-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4S7 4.67 7 5.5S6.33 7 5.5 7z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Coupons') }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Configure coupons') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Add Coupon Button -->
                        <button @click="openModal()" type="button"
                            class="group px-6 py-2.5 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 transition-all duration-200">
                            <span class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                                </svg>
                                {{ $t('Add coupon') }}
                            </span>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Table Component -->
                    <CouponTable :rows="props.rows" @edit="openModal" @delete="openAlert" />
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
import CouponTable from '@/Components/Tables/CouponTable.vue';
import FormModal from '@/Components/FormModalModified.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    rows: Object
});

const isOpenFormModal = ref(false);
const label = ref('Add Coupon');
const formUrl = ref('/admin/coupons');
const formMethod = ref('post');

const form = {
    name: null,
    code: null,
    percentage: null,
    quantity: null,
};

const openModal = (key, formData = {}) => {
    label.value = trans('Add coupon');
    formUrl.value = '/admin/coupons';
    formMethod.value = 'post';

    if (key != null) {
        label.value = trans('Edit coupon');
        formUrl.value = '/admin/coupons/' + key;
        formMethod.value = 'put';
        getRow();
    } else {
        form.name = null;
        form.code = null;
        form.percentage = null;
        form.quantity = null;
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
        label: trans('name'),
        type: 'text',
        className: 'sm:col-span-6',
    },
    {
        inputType: 'FormInput',
        name: 'code',
        label: trans('Coupon code'),
        type: 'text',
        className: 'sm:col-span-3',
    },
    {
        inputType: 'FormInput',
        name: 'percentage',
        label: trans('Percentage'),
        type: 'number',
        className: 'sm:col-span-3',
    },
    {
        inputType: 'FormInput',
        name: 'quantity',
        label: trans('Quantity'),
        type: 'number',
        className: 'sm:col-span-6',
    },
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