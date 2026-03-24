<!-- <template>
    <AppLayout>
        <div>
            <h2 class="text-xl mb-1">{{ $t('Billing info') }}</h2>
            <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                </svg>
                <span class="ml-1 mt-1">{{ $t('Configure the information to be seen in your invoices') }}</span>
            </p>
        </div>
        <form @submit.prevent="submitForm()">
            <div class="space-y-12">
                <div class="pb-12">
                    <div class="grid gap-3 grid-cols-2 pb-6 border-b md:w-2/3">
                        <FormInput v-model="form.billing_name" :name="$t('Vendor name')"
                            :error="form.errors.billing_name" :type="'text'" :class="'col-span-2'" />
                        <FormInput v-model="form.invoice_prefix" :name="$t('Invoice prefix')"
                            :error="form.errors.invoice_prefix" :type="'text'" :class="'col-span-1'" />
                        <FormInput v-model="form.billing_tax_id" :name="$t('Tax number')"
                            :error="form.errors.billing_tax_id" :type="'text'" :class="'col-span-1'" />
                        <FormInput v-model="form.billing_phone_1" :name="$t('Phone')"
                            :error="form.errors.billing_phone_1" :type="'text'" :class="'col-span-1'" />
                        <FormInput v-model="form.billing_phone_2" :name="$t('Phone 2')"
                            :error="form.errors.billing_phone_2" :type="'text'" :class="'col-span-1'" />
                        <FormInput v-model="form.billing_address" :name="$t('Address')"
                            :error="form.errors.billing_address" :type="'text'" :class="'col-span-2'" />
                        <FormInput v-model="form.billing_city" :name="$t('City')" :error="form.errors.billing_city"
                            :type="'text'" :class="'col-span-1'" />
                        <FormInput v-model="form.billing_state" :name="$t('State')" :error="form.errors.billing_state"
                            :type="'text'" :class="'col-span-1'" />
                        <FormInput v-model="form.billing_postal_code" :name="$t('Zip code')"
                            :error="form.errors.billing_postal_code" :type="'text'" :class="'col-span-1'" />
                        <FormInput v-model="form.billing_country" :name="$t('Country')"
                            :error="form.errors.billing_country" :type="'text'" :class="'col-span-1'" />
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-x-6 md:w-2/3">
                        <button type="button" class="text-sm leading-6 text-gray-900">{{ $t('Cancel') }}</button>
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
                </div>
            </div>
        </form>
    </AppLayout>
</template>
<script setup>
import AppLayout from "./Layout/App.vue";
import { useForm } from "@inertiajs/vue3";
import { defineProps, ref } from 'vue';
import FormInput from '@/Components/FormInput.vue';

const props = defineProps({
    config: {
        type: Object,
        required: true
    }
});

const getValueByKey = (key) => {
    const found = props.config.find(item => item.key === key);
    return found ? found.value : '';
};

const isLoading = ref(false);
const form = useForm({
    billing_name: getValueByKey('billing_name'),
    invoice_prefix: getValueByKey('invoice_prefix'),
    billing_tax_id: getValueByKey('billing_tax_id'),
    billing_phone_1: getValueByKey('billing_phone_1'),
    billing_phone_2: getValueByKey('billing_phone_2'),
    billing_address: getValueByKey('billing_address'),
    billing_city: getValueByKey('billing_city'),
    billing_state: getValueByKey('billing_state'),
    billing_postal_code: getValueByKey('billing_postal_code'),
    billing_country: getValueByKey('billing_country'),
})

const submitForm = async () => {
    form.put('/admin/settings?type=billing', {
        preserveScroll: true,
    });
};
</script> -->

<!-- ============================================ NEW UI CODE ============================================= -->

<template>
  <AppLayout>
    <div class="p-6 md:p-8 space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
      <form @submit.prevent="submitForm()" class="w-full mx-auto space-y-6">
        <!-- Billing Information Card -->
        <div
          class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300"
        >
          <div
            class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200"
          >
            <div class="flex items-center gap-3">
              <div
                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  class="text-white"
                >
                  <path
                    fill="currentColor"
                    d="M20 8H4V6h16m0 12H4v-6h16m0-8H4c-1.11 0-2 .89-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Z"
                  />
                </svg>
              </div>
              <div>
                <h2 class="text-lg font-bold text-slate-900">
                  {{ $t("Billing Information") }}
                </h2>
                <div
                  class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="14"
                    height="14"
                    viewBox="0 0 24 24"
                    class="text-[#ff5100]"
                  >
                    <path
                      fill="none"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"
                    />
                  </svg>
                  <span>{{
                    $t("Configure the information to be seen in your invoices")
                  }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="p-6 space-y-6">
            <!-- Vendor Information -->
            <div class="grid md:grid-cols-5 gap-6 items-start">
              <div class="md:col-span-2 space-y-1">
                <label class="text-sm font-semibold text-slate-900">{{
                  $t("Vendor Details")
                }}</label>
                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="14"
                    height="14"
                    viewBox="0 0 24 24"
                    class="text-[#ff5100] shrink-0 mt-0.5"
                  >
                    <path
                      fill="none"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"
                    />
                  </svg>
                  <span>{{ $t("Business name and invoice settings") }}</span>
                </div>
              </div>
              <div class="md:col-span-3 grid grid-cols-1 gap-4">
                <FormInput
                  v-model="form.billing_name"
                  :name="$t('Vendor name')"
                  :error="form.errors.billing_name"
                  :type="'text'"
                />
                <div class="grid grid-cols-2 gap-4">
                  <FormInput
                    v-model="form.invoice_prefix"
                    :name="$t('Invoice prefix')"
                    :error="form.errors.invoice_prefix"
                    :type="'text'"
                  />
                  <FormInput
                    v-model="form.billing_tax_id"
                    :name="$t('Tax number')"
                    :error="form.errors.billing_tax_id"
                    :type="'text'"
                  />
                </div>
              </div>
            </div>

            <div class="border-t border-slate-100"></div>

            <!-- Contact Information -->
            <div class="grid md:grid-cols-5 gap-6 items-start">
              <div class="md:col-span-2 space-y-1">
                <label class="text-sm font-semibold text-slate-900">{{
                  $t("Contact Details")
                }}</label>
                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="14"
                    height="14"
                    viewBox="0 0 24 24"
                    class="text-[#ff5100] shrink-0 mt-0.5"
                  >
                    <path
                      fill="none"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"
                    />
                  </svg>
                  <span>{{ $t("Phone numbers for invoice contact") }}</span>
                </div>
              </div>
              <div class="md:col-span-3 grid grid-cols-2 gap-4">
                <FormInput
                  v-model="form.billing_phone_1"
                  :name="$t('Phone')"
                  :error="form.errors.billing_phone_1"
                  :type="'text'"
                />
                <FormInput
                  v-model="form.billing_phone_2"
                  :name="$t('Phone 2')"
                  :error="form.errors.billing_phone_2"
                  :type="'text'"
                />
              </div>
            </div>

            <div class="border-t border-slate-100"></div>

            <!-- Address Information -->
            <div class="grid md:grid-cols-5 gap-6 items-start">
              <div class="md:col-span-2 space-y-1">
                <label class="text-sm font-semibold text-slate-900">{{
                  $t("Billing Address")
                }}</label>
                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="14"
                    height="14"
                    viewBox="0 0 24 24"
                    class="text-[#ff5100] shrink-0 mt-0.5"
                  >
                    <path
                      fill="none"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"
                    />
                  </svg>
                  <span>{{ $t("Complete billing address for invoices") }}</span>
                </div>
              </div>
              <div class="md:col-span-3 grid grid-cols-1 gap-4">
                <FormInput
                  v-model="form.billing_address"
                  :name="$t('Address')"
                  :error="form.errors.billing_address"
                  :type="'text'"
                />
                <div class="grid grid-cols-2 gap-4">
                  <FormInput
                    v-model="form.billing_city"
                    :name="$t('City')"
                    :error="form.errors.billing_city"
                    :type="'text'"
                  />
                  <FormInput
                    v-model="form.billing_state"
                    :name="$t('State')"
                    :error="form.errors.billing_state"
                    :type="'text'"
                  />
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <FormInput
                    v-model="form.billing_postal_code"
                    :name="$t('Zip code')"
                    :error="form.errors.billing_postal_code"
                    :type="'text'"
                  />
                  <FormInput
                    v-model="form.billing_country"
                    :name="$t('Country')"
                    :error="form.errors.billing_country"
                    :type="'text'"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-4 pt-4">
          <button
            type="button"
            class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition-all duration-200"
          >
            {{ $t("Cancel") }}
          </button>
          <button
            type="submit"
            class="group px-8 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="isLoading"
          >
            <span v-if="isLoading" class="flex items-center gap-2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
              >
                <path
                  fill="currentColor"
                  d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                  opacity=".5"
                />
                <path
                  fill="currentColor"
                  d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z"
                >
                  <animateTransform
                    attributeName="transform"
                    dur="1s"
                    from="0 12 12"
                    repeatCount="indefinite"
                    to="360 12 12"
                    type="rotate"
                  />
                </path>
              </svg>
              {{ $t("Saving...") }}
            </span>
            <span v-else class="flex items-center gap-2">
              {{ $t("Save Settings") }}
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
              >
                <path
                  fill="currentColor"
                  d="m9.55 18l-5.7-5.7l1.425-1.425L9.55 15.15l9.175-9.175L20.15 7.4z"
                />
              </svg>
            </span>
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from "./Layout/App.vue";
import { useForm } from "@inertiajs/vue3";
import { defineProps, ref } from "vue";
import FormInput from "@/Components/FormInput.vue";

const props = defineProps({
  config: {
    type: Object,
    required: true,
  },
});

const getValueByKey = (key) => {
  const found = props.config.find((item) => item.key === key);
  return found ? found.value : "";
};

const isLoading = ref(false);
const form = useForm({
  billing_name: getValueByKey("billing_name"),
  invoice_prefix: getValueByKey("invoice_prefix"),
  billing_tax_id: getValueByKey("billing_tax_id"),
  billing_phone_1: getValueByKey("billing_phone_1"),
  billing_phone_2: getValueByKey("billing_phone_2"),
  billing_address: getValueByKey("billing_address"),
  billing_city: getValueByKey("billing_city"),
  billing_state: getValueByKey("billing_state"),
  billing_postal_code: getValueByKey("billing_postal_code"),
  billing_country: getValueByKey("billing_country"),
});

const submitForm = async () => {
  form.put("/admin/settings?type=billing", {
    preserveScroll: true,
  });
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
