<!-- <script setup>
import { ref } from 'vue';
import FlutterwaveFormModal from '@/Components/PaymentConfigModals/FlutterwaveModal.vue';
import PayPalFormModal from '@/Components/PaymentConfigModals/PayPalModal.vue';
import PaystackFormModal from '@/Components/PaymentConfigModals/PaystackModal.vue';
import StripeFormModal from '@/Components/PaymentConfigModals/StripeModal.vue';
import Table from '@/Components/Table.vue';
import TableBody from '@/Components/TableBody.vue';
import TableBodyRow from '@/Components/TableBodyRow.vue';
import TableBodyRowItem from '@/Components/TableBodyRowItem.vue';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['edit']);

const isOpenFormModal = ref(false);
const paymentMethod = ref();

const view = (type) => {
    paymentMethod.value = type;
    isOpenFormModal.value = true;
}

const isLastRow = (index) => {
    return index === props.rows.data.length - 1;
}
</script>
<template>
    <Table :rows="rows">
        <TableBody>
            <TableBodyRow v-for="(item, index) in rows.data" :key="index"
                :class="[index === 0 ? 'border-t-[0px]' : '', !isLastRow(index) ? 'border-b' : '']">
                <TableBodyRowItem :position="'first'" class="py-2">
                    {{ item.name }}
                    <span class="bg-gray-200 text-[11px] p-1 rounded-md">{{ item.is_active == '1' ? $t('Active') :
                        $t('Inactive') }}</span>
                </TableBodyRowItem>
                <TableBodyRowItem :position="'last'">
                    <div class="flex items-center h-100 py-2">
                        <button type="button"
                            class="inline-flex justify-center rounded-md border border-transparent bg-primary px-2 py-1 text-[12px] text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                            @click="view(item.name)">{{ $t('Edit') }}</button>
                    </div>
                </TableBodyRowItem>
            </TableBodyRow>
        </TableBody>
    </Table>

    <FlutterwaveFormModal v-if="paymentMethod === 'Flutterwave'" v-model="isOpenFormModal"
        @closeModal="isOpenFormModal = false" />
    <PayPalFormModal v-if="paymentMethod === 'Paypal'" v-model="isOpenFormModal"
        @closeModal="isOpenFormModal = false" />
    <PaystackFormModal v-if="paymentMethod === 'Paystack'" v-model="isOpenFormModal"
        @closeModal="isOpenFormModal = false" />
    <StripeFormModal v-if="paymentMethod === 'Stripe'" v-model="isOpenFormModal"
        @closeModal="isOpenFormModal = false" />
</template> -->


<!-- ============================================ NEW UI CODE ============================================= -->


<template>
    <div class="overflow-hidden">
        <!-- Modern Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-50 to-orange-50/20">
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">
                            {{ $t('Gateway Name') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">
                            {{ $t('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    <tr v-for="(item, index) in rows.data" :key="index"
                        class="group hover:bg-slate-50/50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div
                                    class="p-2 bg-gradient-to-br from-slate-100 to-slate-50 rounded-lg group-hover:from-[#ff5100]/10 group-hover:to-[#ff7340]/5 transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        class="text-[#ff5100] transition-colors">
                                        <path fill="currentColor"
                                            d="M20 8H4V6h16m0 12H4v-6h16m0-8H4c-1.11 0-2 .89-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Z" />
                                    </svg>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-slate-900">{{ item.name }}</span>
                                    <span v-if="item.is_active == '1'"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 border border-green-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2m-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9Z" />
                                        </svg>
                                        {{ $t('Active') }}
                                    </span>
                                    <span v-else
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-slate-50 to-slate-100 text-slate-600 border border-slate-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8s8 3.58 8 8s-3.58 8-8 8z" />
                                        </svg>
                                        {{ $t('Inactive') }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <button type="button" @click="view(item.name)"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white text-sm font-medium rounded-lg shadow-md shadow-[#ff5100]/20 hover:shadow-lg hover:shadow-[#ff5100]/30 transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M5 19h1.4l8.625-8.625l-1.4-1.4L5 17.6V19ZM19.3 8.925l-4.25-4.2l1.4-1.4q.575-.575 1.413-.575t1.412.575l1.4 1.4q.575.575.6 1.388t-.55 1.387L19.3 8.925ZM17.85 10.4L7.25 21H3v-4.25l10.6-10.6l4.25 4.25Zm-3.525-.725l-.7-.7l1.4 1.4l-.7-.7Z" />
                                </svg>
                                {{ $t('Edit') }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <Pagination class="mt-3" :pagination="rows.meta" />
        </div>
    </div>

    <!-- Modals -->
    <FlutterwaveFormModal v-if="paymentMethod === 'Flutterwave'" v-model="isOpenFormModal"
        @closeModal="isOpenFormModal = false" />
    <PayPalFormModal v-if="paymentMethod === 'Paypal'" v-model="isOpenFormModal"
        @closeModal="isOpenFormModal = false" />
    <PaystackFormModal v-if="paymentMethod === 'Paystack'" v-model="isOpenFormModal"
        @closeModal="isOpenFormModal = false" />
    <StripeFormModal v-if="paymentMethod === 'Stripe'" v-model="isOpenFormModal"
        @closeModal="isOpenFormModal = false" />
</template>

<script setup>
import { ref } from 'vue';
import FlutterwaveFormModal from '@/Components/PaymentConfigModals/FlutterwaveModal.vue';
import PayPalFormModal from '@/Components/PaymentConfigModals/PayPalModal.vue';
import PaystackFormModal from '@/Components/PaymentConfigModals/PaystackModal.vue';
import StripeFormModal from '@/Components/PaymentConfigModals/StripeModal.vue';
import Pagination from '../Pagination.vue';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
});

const isOpenFormModal = ref(false);
const paymentMethod = ref();

const view = (type) => {
    paymentMethod.value = type;
    isOpenFormModal.value = true;
}
</script>

<style scoped>
/* Custom scrollbar for table */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>