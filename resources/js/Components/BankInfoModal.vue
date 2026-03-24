<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        class="fixed inset-0 z-50 overflow-y-auto"
        @click.self="$emit('close')"
      >
        <div class="flex min-h-screen items-center justify-center p-4">
          <!-- Backdrop -->
          <div
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
          ></div>

          <!-- Modal -->
          <div
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto"
          >
            <!-- Header -->
            <div
              class="sticky top-0 bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200 z-10"
            >
              <div class="flex items-center justify-between">
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
                      {{
                        isEdit
                          ? $t("Edit Bank Account")
                          : $t("Add Bank Account")
                      }}
                    </h2>
                    <p class="text-sm text-slate-600">
                      {{ $t("Enter the bank account details") }}
                    </p>
                  </div>
                </div>
                <button
                  @click="$emit('close')"
                  type="button"
                  class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-all duration-200"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                  >
                    <path
                      fill="currentColor"
                      d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12z"
                    />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submitForm" class="p-6 space-y-6">
              <!-- Account Holder Information -->
              <div class="space-y-4">
                <div
                  class="flex items-center gap-2 pb-2 border-b border-slate-200"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    class="text-[#ff5100]"
                  >
                    <path
                      fill="currentColor"
                      d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4s-4 1.79-4 4s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"
                    />
                  </svg>
                  <h3 class="text-sm font-semibold text-slate-900">
                    {{ $t("Account Holder Information") }}
                  </h3>
                </div>

                <FormInput
                  v-model="form.account_name"
                  :name="$t('Account Holder Name')"
                  :error="form.errors.account_name"
                  :placeholder="$t('Enter account holder name')"
                  :required="true"
                />
              </div>

              <!-- Bank Details -->
              <div class="space-y-4">
                <div
                  class="flex items-center gap-2 pb-2 border-b border-slate-200"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    class="text-[#ff5100]"
                  >
                    <path
                      fill="currentColor"
                      d="M20 8H4V6h16m0 12H4v-6h16m0-8H4c-1.11 0-2 .89-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Z"
                    />
                  </svg>
                  <h3 class="text-sm font-semibold text-slate-900">
                    {{ $t("Bank Details") }}
                  </h3>
                </div>

                <FormInput
                  v-model="form.bank_name"
                  :name="$t('Bank Name')"
                  :error="form.errors.bank_name"
                  :placeholder="$t('Enter bank name')"
                  :required="true"
                />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <FormInput
                    v-model="form.account_number"
                    :name="$t('Account Number')"
                    :error="form.errors.account_number"
                    :placeholder="$t('Enter account number')"
                    :required="true"
                  />

                  <FormInput
                    v-model="form.ifsc_code"
                    :name="$t('IFSC Code')"
                    :error="form.errors.ifsc_code"
                    :placeholder="$t('e.g., SBIN0001234')"
                    :required="true"
                    style="text-transform: uppercase"
                  />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <FormInput
                    v-model="form.branch"
                    :name="$t('Branch')"
                    :error="form.errors.branch"
                    :placeholder="$t('Enter branch name')"
                    :required="true"
                  />

                  <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-900">
                      {{ $t("Account Type") }}
                      <span class="text-red-500">*</span>
                    </label>
                    <select
                      v-model="form.account_type"
                      class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200"
                    >
                      <option value="savings">{{ $t("Savings") }}</option>
                      <option value="current">{{ $t("Current") }}</option>
                      <option value="salary">{{ $t("Salary") }}</option>
                      <option value="other">{{ $t("Other") }}</option>
                    </select>
                    <p
                      v-if="form.errors.account_type"
                      class="text-xs text-red-600 mt-1"
                    >
                      {{ form.errors.account_type }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Account Settings -->
              <div class="space-y-4">
                <div
                  class="flex items-center gap-2 pb-2 border-b border-slate-200"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    class="text-[#ff5100]"
                  >
                    <path
                      fill="currentColor"
                      d="M12 15.5A3.5 3.5 0 0 1 8.5 12A3.5 3.5 0 0 1 12 8.5a3.5 3.5 0 0 1 3.5 3.5a3.5 3.5 0 0 1-3.5 3.5m7.43-2.53c.04-.32.07-.64.07-.97c0-.33-.03-.66-.07-1l2.11-1.63c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.31-.61-.22l-2.49 1c-.52-.39-1.06-.73-1.69-.98l-.37-2.65A.506.506 0 0 0 14 2h-4c-.25 0-.46.18-.5.42l-.37 2.65c-.63.25-1.17.59-1.69.98l-2.49-1c-.22-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64L4.57 11c-.04.34-.07.67-.07 1c0 .33.03.65.07.97l-2.11 1.66c-.19.15-.25.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1.01c.52.4 1.06.74 1.69.99l.37 2.65c.04.24.25.42.5.42h4c.25 0 .46-.18.5-.42l.37-2.65c.63-.26 1.17-.59 1.69-.99l2.49 1.01c.22.08.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.66Z"
                    />
                  </svg>
                  <h3 class="text-sm font-semibold text-slate-900">
                    {{ $t("Account Settings") }}
                  </h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-900">
                      {{ $t("Status") }}
                      <span class="text-red-500">*</span>
                    </label>
                    <select
                      v-model="form.status"
                      class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200"
                    >
                      <option value="active">{{ $t("Active") }}</option>
                      <option value="inactive">{{ $t("Inactive") }}</option>
                      <option value="closed">{{ $t("Closed") }}</option>
                    </select>
                    <p
                      v-if="form.errors.status"
                      class="text-xs text-red-600 mt-1"
                    >
                      {{ form.errors.status }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-900">
                      {{ $t("Primary Account") }}
                    </label>
                    <div class="flex items-center h-[50px]">
                      <label
                        class="relative inline-flex items-center cursor-pointer"
                      >
                        <input
                          type="checkbox"
                          v-model="form.is_primary"
                          class="sr-only peer"
                        />
                        <div
                          class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#ff5100]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-[#ff5100] peer-checked:to-[#ff7340]"
                        ></div>
                        <span class="ml-3 text-sm text-slate-600">
                          {{ form.is_primary ? $t("Yes") : $t("No") }}
                        </span>
                      </label>
                    </div>
                    <p class="text-xs text-slate-500">
                      {{ $t("Set this as your primary bank account") }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div
                class="flex items-center justify-end gap-4 pt-4 border-t border-slate-200"
              >
                <button
                  type="button"
                  @click="$emit('close')"
                  class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition-all duration-200"
                >
                  {{ $t("Cancel") }}
                </button>
                <button
                  type="submit"
                  class="group px-8 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                  :disabled="isSubmitting"
                >
                  <span v-if="isSubmitting" class="flex items-center gap-2">
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
                    {{ isEdit ? $t("Update Account") : $t("Add Account") }}
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
        </div>
      </div>
    </Transition>
  </Teleport>
</template>
<script setup>
import { ref, reactive } from "vue";
import FormInput from "./FormInput.vue";
import axios from "axios";

const base_url = import.meta.env.VITE_BACKEND_API_URL;

const props = defineProps({
  bank: Object,
  isEdit: Boolean,
});

const emit = defineEmits(["close", "success"]);

const isSubmitting = ref(false);

const form = reactive({
  account_name: "",
  bank_name: "",
  account_number: "",
  branch: "",
  ifsc_code: "",
  account_type: "savings",
  is_primary: false,
  status: "active",
  errors: {},
});

// Initialize form with existing data if editing
if (props.isEdit && props.bank) {
  form.account_name = props.bank.account_name;
  form.bank_name = props.bank.bank_name;
  form.account_number = props.bank.account_number;
  form.branch = props.bank.branch;
  form.ifsc_code = props.bank.ifsc_code;
  form.account_type = props.bank.account_type;
  form.is_primary = props.bank.is_primary;
  form.status = props.bank.status;
}

const submitForm = async () => {
  isSubmitting.value = true;
  form.errors = {};

  try {
    const data = {
      account_name: form.account_name,
      bank_name: form.bank_name,
      account_number: form.account_number,
      branch: form.branch,
      ifsc_code: form.ifsc_code.toUpperCase(),
      account_type: form.account_type,
      is_primary: form.is_primary,
      status: form.status,
    };

    if (props.isEdit) {
      await axios.put(`${base_url}/bank-info/${props.bank.id}`, data);
    } else {
      await axios.post(`${base_url}/bank-info`, data);
    }

    emit("success");
  } catch (error) {
    if (error.response?.data?.errors) {
      form.errors = error.response.data.errors;
    }
    console.error("Error submitting form:", error);
  } finally {
    isSubmitting.value = false;
  }
};
</script>
<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
  transition: transform 0.3s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
  transform: scale(0.95);
}
</style>
