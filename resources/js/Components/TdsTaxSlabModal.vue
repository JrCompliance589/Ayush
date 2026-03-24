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
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-y-auto"
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
                        d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11zM8 15.01l1.41 1.41L11 14.84V19h2v-4.16l1.59 1.59L16 15.01L12.01 11z"
                      />
                    </svg>
                  </div>
                  <div>
                    <h2 class="text-lg font-bold text-slate-900">
                      {{
                        isEdit
                          ? $t("Edit TDS Tax Slab")
                          : $t("Add TDS Tax Slab")
                      }}
                    </h2>
                    <p class="text-sm text-slate-600">
                      {{ $t("Configure TDS tax rates and thresholds") }}
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
              <!-- Section Information -->
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
                      d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"
                    />
                  </svg>
                  <h3 class="text-sm font-semibold text-slate-900">
                    {{ $t("Section Information") }}
                  </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <FormInput
                    v-model="form.section_code"
                    :name="$t('Section Code')"
                    :error="form.errors.section_code"
                    :placeholder="$t('e.g., 194J, 194C')"
                    :required="true"
                    style="text-transform: uppercase"
                  />

                  <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-900">
                      {{ $t("Service Type") }}
                      <span class="text-red-500">*</span>
                    </label>
                    <select
                      v-model="form.service_type"
                      class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200"
                    >
                      <option value="">{{ $t("Select Service Type") }}</option>
                      <option value="professional_services">
                        {{ $t("Professional Services") }}
                      </option>
                      <option value="technical_services">
                        {{ $t("Technical Services") }}
                      </option>
                      <option value="contractor_services">
                        {{ $t("Contractor Services") }}
                      </option>
                      <option value="rent">{{ $t("Rent") }}</option>
                      <option value="commission">{{ $t("Commission") }}</option>
                      <option value="interest">{{ $t("Interest") }}</option>
                      <option value="salary">{{ $t("Salary") }}</option>
                      <option value="other">{{ $t("Other") }}</option>
                    </select>
                    <p
                      v-if="form.errors.service_type"
                      class="text-xs text-red-600 mt-1"
                    >
                      {{ form.errors.service_type }}
                    </p>
                  </div>
                </div>

                <FormInput
                  v-model="form.section_name"
                  :name="$t('Section Name')"
                  :error="form.errors.section_name"
                  :placeholder="
                    $t('e.g., TDS on Professional or Technical Services')
                  "
                  :required="true"
                />

                <div class="space-y-2">
                  <label class="text-sm font-semibold text-slate-900">
                    {{ $t("Description") }}
                  </label>
                  <textarea
                    v-model="form.description"
                    rows="3"
                    :placeholder="
                      $t(
                        'Enter detailed description of what this section covers...'
                      )
                    "
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200 resize-none"
                  ></textarea>
                  <p
                    v-if="form.errors.description"
                    class="text-xs text-red-600"
                  >
                    {{ form.errors.description }}
                  </p>
                </div>
              </div>

              <!-- Tax Rates -->
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
                      d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15c0-1.09 1.01-1.85 2.7-1.85c1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61c0 2.31 1.91 3.46 4.7 4.13c2.5.6 3 1.48 3 2.41c0 .69-.49 1.79-2.7 1.79c-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55c0-2.84-2.43-3.81-4.7-4.4z"
                    />
                  </svg>
                  <h3 class="text-sm font-semibold text-slate-900">
                    {{ $t("Tax Rates & Threshold") }}
                  </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-900">
                      {{ $t("Individual Rate (%)") }}
                      <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <input
                        v-model="form.rate_individual"
                        type="number"
                        step="0.01"
                        min="0"
                        max="100"
                        :placeholder="$t('e.g., 10.00')"
                        class="w-full pl-4 pr-10 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200"
                      />
                      <div
                        class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
                      >
                        <span class="text-slate-500 text-sm font-medium"
                          >%</span
                        >
                      </div>
                    </div>
                    <p
                      v-if="form.errors.rate_individual"
                      class="text-xs text-red-600"
                    >
                      {{ form.errors.rate_individual }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-900">
                      {{ $t("Company Rate (%)") }}
                      <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <input
                        v-model="form.rate_company"
                        type="number"
                        step="0.01"
                        min="0"
                        max="100"
                        :placeholder="$t('e.g., 2.00')"
                        class="w-full pl-4 pr-10 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200"
                      />
                      <div
                        class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
                      >
                        <span class="text-slate-500 text-sm font-medium"
                          >%</span
                        >
                      </div>
                    </div>
                    <p
                      v-if="form.errors.rate_company"
                      class="text-xs text-red-600"
                    >
                      {{ form.errors.rate_company }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-900">
                      {{ $t("Threshold Limit (₹)") }}
                      <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <div
                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                      >
                        <span class="text-slate-500 text-sm font-medium"
                          >₹</span
                        >
                      </div>
                      <input
                        v-model="form.threshold_limit"
                        type="number"
                        step="0.01"
                        min="0"
                        :placeholder="$t('e.g., 50000')"
                        class="w-full pl-8 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200"
                      />
                    </div>
                    <p
                      v-if="form.errors.threshold_limit"
                      class="text-xs text-red-600"
                    >
                      {{ form.errors.threshold_limit }}
                    </p>
                  </div>
                </div>

                <!-- Tax Calculation Preview -->
                <div
                  class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4"
                >
                  <div class="flex items-start gap-3">
                    <div class="p-2 bg-blue-100 rounded-lg shrink-0">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        class="text-blue-600"
                      >
                        <path
                          fill="currentColor"
                          d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"
                        />
                      </svg>
                    </div>
                    <div class="flex-1">
                      <h4 class="text-sm font-semibold text-blue-900 mb-2">
                        {{ $t(`Tax Calculation Preview`) }}
                      </h4>
                      <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="bg-white rounded-lg p-2">
                          <p class="text-blue-600 mb-1">
                            {{ $t("Individual") }}
                          </p>
                          <p class="text-blue-900 font-semibold">
                            {{
                              form.rate_individual
                                ? `${form.rate_individual}%`
                                : "-"
                            }}
                          </p>
                          <p
                            v-if="form.threshold_limit && form.rate_individual"
                            class="text-blue-700 mt-1"
                          >
                            {{ $t("On") }} ₹1,00,000: ₹{{
                              calculateTax(
                                100000,
                                form.rate_individual,
                                form.threshold_limit
                              )
                            }}
                          </p>
                        </div>
                        <div class="bg-white rounded-lg p-2">
                          <p class="text-blue-600 mb-1">{{ $t("Company") }}</p>
                          <p class="text-blue-900 font-semibold">
                            {{
                              form.rate_company ? `${form.rate_company}%` : "-"
                            }}
                          </p>
                          <p
                            v-if="form.threshold_limit && form.rate_company"
                            class="text-blue-700 mt-1"
                          >
                            {{ $t("On") }} ₹1,00,000: ₹{{
                              calculateTax(
                                100000,
                                form.rate_company,
                                form.threshold_limit
                              )
                            }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Effective Dates -->
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
                      d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"
                    />
                  </svg>
                  <h3 class="text-sm font-semibold text-slate-900">
                    {{ $t("Effective Period") }}
                  </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-900">
                      {{ $t("Effective From") }}
                      <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model="form.effective_from"
                      type="date"
                      class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200"
                    />
                    <p
                      v-if="form.errors.effective_from"
                      class="text-xs text-red-600"
                    >
                      {{ form.errors.effective_from }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-900">
                      {{ $t("Effective To") }}
                    </label>
                    <input
                      v-model="form.effective_to"
                      type="date"
                      class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200"
                    />
                    <p class="text-xs text-slate-500">
                      {{ $t("Leave empty for indefinite period") }}
                    </p>
                    <p
                      v-if="form.errors.effective_to"
                      class="text-xs text-red-600"
                    >
                      {{ form.errors.effective_to }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Settings & Notes -->
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
                    {{ $t("Settings & Notes") }}
                  </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-900">
                      {{ $t("Status") }}
                    </label>
                    <div class="flex items-center h-[50px]">
                      <label
                        class="relative inline-flex items-center cursor-pointer"
                      >
                        <input
                          type="checkbox"
                          v-model="form.is_active"
                          class="sr-only peer"
                        />
                        <div
                          class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#ff5100]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-[#ff5100] peer-checked:to-[#ff7340]"
                        ></div>
                        <span class="ml-3 text-sm text-slate-600">
                          {{ form.is_active ? $t("Active") : $t("Inactive") }}
                        </span>
                      </label>
                    </div>
                  </div>

                  <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-900">
                      {{ $t("Default for Service Type") }}
                    </label>
                    <div class="flex items-center h-[50px]">
                      <label
                        class="relative inline-flex items-center cursor-pointer"
                      >
                        <input
                          type="checkbox"
                          v-model="form.is_default"
                          class="sr-only peer"
                        />
                        <div
                          class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#ff5100]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-[#ff5100] peer-checked:to-[#ff7340]"
                        ></div>
                        <span class="ml-3 text-sm text-slate-600">
                          {{ form.is_default ? $t("Yes") : $t("No") }}
                        </span>
                      </label>
                    </div>
                    <p class="text-xs text-slate-500">
                      {{ $t("Set as default slab for this service type") }}
                    </p>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-semibold text-slate-900">
                    {{ $t("Notes") }}
                  </label>
                  <textarea
                    v-model="form.notes"
                    rows="3"
                    :placeholder="$t('Add any additional notes or remarks...')"
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200 resize-none"
                  ></textarea>
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
                    {{ isEdit ? $t("Update") : $t("Create") }}
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
import FormInput from "@/Components/FormInput.vue";
import axios from "axios";

const base_url = import.meta.env.VITE_BACKEND_API_URL;

const props = defineProps({
  tdsSlab: Object,
  isEdit: Boolean,
});

const emit = defineEmits(["close", "success"]);

const isSubmitting = ref(false);

const form = reactive({
  section_code: "",
  section_name: "",
  description: "",
  service_type: "",
  rate_individual: "",
  rate_company: "",
  threshold_limit: "",
  effective_from: "",
  effective_to: "",
  is_active: true,
  is_default: false,
  notes: "",
  errors: {},
});

// Initialize form with existing data if editing
if (props.isEdit && props.tdsSlab) {
  form.section_code = props.tdsSlab.section_code;
  form.section_name = props.tdsSlab.section_name;
  form.description = props.tdsSlab.description || "";
  form.service_type = props.tdsSlab.service_type;
  form.rate_individual = props.tdsSlab.rate_individual;
  form.rate_company = props.tdsSlab.rate_company;
  form.threshold_limit = props.tdsSlab.threshold_limit;
  form.effective_from = props.tdsSlab.effective_from
    ? new Date(props.tdsSlab.effective_from).toISOString().split("T")[0]
    : "";
  form.effective_to = props.tdsSlab.effective_to
    ? new Date(props.tdsSlab.effective_to).toISOString().split("T")[0]
    : "";
  form.is_active = props.tdsSlab.is_active;
  form.is_default = props.tdsSlab.is_default;
  form.notes = props.tdsSlab.notes || "";
}

const calculateTax = (amount, rate, threshold) => {
  const amt = Number(amount);
  const rt = Number(rate);
  const thr = Number(threshold);

  if (isNaN(amt) || isNaN(rt) || isNaN(thr) || amt < thr) {
    return "0";
  }

  const tax = (amt * rt) / 100;
  return new Intl.NumberFormat("en-IN", {
    maximumFractionDigits: 2,
    minimumFractionDigits: 2,
  }).format(tax);
};

const submitForm = async () => {
  isSubmitting.value = true;
  form.errors = {};

  try {
    const data = {
      section_code: form.section_code.toUpperCase().trim(),
      section_name: form.section_name,
      description: form.description,
      service_type: form.service_type,
      rate_individual: Number(form.rate_individual),
      rate_company: Number(form.rate_company),
      threshold_limit: Number(form.threshold_limit),
      effective_from: form.effective_from,
      effective_to: form.effective_to || null,
      is_active: form.is_active,
      is_default: form.is_default,
      notes: form.notes,
    };
    if (props.isEdit) {
      await axios.put(`${base_url}/tds-tax-slabs/${props.tdsSlab.id}`, data);
    } else {
      await axios.post(`${base_url}/tds-tax-slabs`, data);
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
