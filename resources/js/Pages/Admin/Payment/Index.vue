<template>
    <AppLayout>
        <div
            class="bg-white md:bg-inherit pt-10 px-4 md:pt-8 md:p-8 rounded-[5px] text-[#000] h-full md:overflow-y-auto">
            <div class="flex-1 mb-8">
                <div class="flex items-center gap-3 mb-3">
                    <div
                        class="w-10 h-10 text-white rounded-xl bg-gradient-to-br from-[#ff5100] to-[#ff7a3d] flex items-center justify-center shadow-lg shadow-[#ff5100]/25">
                        <CreditCard />
                    </div>
                    <h2
                        class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-700 bg-clip-text text-transparent">
                        {{ $t('Invoice & Payment receipts') }}
                    </h2>
                </div>
                <div class="flex items-start gap-2 text-gray-600">
                    <p class="text-sm md:text-base font-medium leading-relaxed">
                        {{ $t('Quotations, proformas, and payment receipts.') }}
                    </p>
                </div>
            </div>

            <InvoiceAndPaymentTable />
        </div>
    </AppLayout>
</template>

<script setup>
import { CreditCard } from "lucide-vue-next";
import AppLayout from "./../Layout/App.vue";
import InvoiceAndPaymentTable from '@/Components/Tables/InvoiceAndPaymentTable.vue';

</script>