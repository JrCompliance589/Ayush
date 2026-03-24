<template>
  <TransitionRoot appear :show="props.isOpen" as="template">
    <Dialog as="div" class="relative z-10">
      <TransitionChild
        as="template"
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black bg-opacity-25" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel
              class="w-full max-w-3xl transform rounded-xl bg-white text-left align-middle shadow-xl transition-all"
            >
              <!-- Header -->
              <div
                class="flex justify-between items-center bg-gray-50 px-6 py-4 border-b"
              >
                <DialogTitle as="h3" class="text-lg font-semibold text-gray-900">
                  {{ modalTitle }}
                </DialogTitle>
                <button
                  @click="closeModal"
                  type="button"
                  class="bg-slate-100 rounded-full p-1.5 hover:bg-slate-200 transition-colors"
                  aria-label="Close modal"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                  >
                    <path
                      fill="none"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M17 7L7 17M7 7l10 10"
                    />
                  </svg>
                </button>
              </div>

              <div class="px-6 py-5 max-h-[calc(100vh-200px)] overflow-y-auto">
                <!-- Total Amount Display -->
                <div
                  class="bg-gradient-to-br from-[#ff5100] from-10% to-[#ff7640] border border-[#ff5100] rounded-lg p-4 mb-6"
                >
                  <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-white"
                      >Total Invoice Amount</span
                    >
                    <span class="text-2xl font-bold text-white">{{
                      formatCurrency(totalAmountNum)
                    }}</span>
                  </div>
                </div>

                <!-- Slabs Section -->
                <div class="space-y-4 mb-6">
                  <div class="flex justify-between items-center">
                    <h4 class="text-base font-semibold text-gray-900">Payment Slabs</h4>
                    <button
                      @click="addSlab"
                      :disabled="!canAddSlab"
                      :class="[
                        'flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-all',
                        canAddSlab
                          ? 'bg-[#ff5100] text-white hover:bg-[#e64900]'
                          : 'bg-gray-200 text-gray-400 cursor-not-allowed',
                      ]"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="18"
                        height="18"
                        viewBox="0 0 24 24"
                      >
                        <path
                          fill="currentColor"
                          d="M11 13H5v-2h6V5h2v6h6v2h-6v6h-2v-6Z"
                        />
                      </svg>
                      Add Slab ({{ slabs.length }}/{{ MAX_SLABS }})
                    </button>
                  </div>

                  <!-- Slab Cards -->
                  <div class="space-y-4">
                    <div
                      v-for="(slab, index) in slabs"
                      :key="slab.id"
                      class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm"
                    >
                      <div class="flex justify-between items-start mb-4">
                        <span
                          class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-[#ff5100] bg-opacity-10 text-[#ff5100]"
                        >
                          Slab {{ index + 1 }}
                        </span>
                        <button
                          v-if="slabs.length > MIN_SLABS"
                          @click="removeSlab(slab.id)"
                          type="button"
                          class="text-red-600 hover:text-red-800 hover:bg-red-50 p-1.5 rounded-lg transition-colors"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                          >
                            <path
                              fill="currentColor"
                              d="M7 21q-.825 0-1.413-.588T5 19V6H4V4h5V3h6v1h5v2h-1v13q0 .825-.588 1.413T17 21H7ZM17 6H7v13h10V6ZM9 17h2V8H9v9Zm4 0h2V8h-2v9ZM7 6v13V6Z"
                            />
                          </svg>
                        </button>
                      </div>

                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Type Selector -->
                        <div>
                          <label class="block text-xs font-medium text-gray-700 mb-2"
                            >Payment Type</label
                          >
                          <div class="grid grid-cols-2 gap-2">
                            <button
                              type="button"
                              @click="handleSlabTypeChange(slab, 'percentage')"
                              :class="[
                                'px-3 py-2 text-sm font-medium rounded-md border transition-all',
                                slab.type === 'percentage'
                                  ? 'bg-[#ff5100] text-white border-[#ff5100]'
                                  : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                              ]"
                            >
                              Percentage (%)
                            </button>
                            <button
                              type="button"
                              @click="handleSlabTypeChange(slab, 'amount')"
                              :class="[
                                'px-3 py-2 text-sm font-medium rounded-md border transition-all',
                                slab.type === 'amount'
                                  ? 'bg-[#ff5100] text-white border-[#ff5100]'
                                  : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                              ]"
                            >
                              Amount (₹)
                            </button>
                          </div>
                        </div>

                        <!-- Value Input -->
                        <div>
                          <label class="block text-xs font-medium text-gray-700 mb-2">
                            {{ slab.type === "percentage" ? "Percentage" : "Amount" }}
                            <span class="text-red-500">*</span>
                          </label>
                          <div class="relative">
                            <div
                              class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                            >
                              <span class="text-gray-500 text-sm font-medium">
                                {{ slab.type === "percentage" ? "%" : "₹" }}
                              </span>
                            </div>
                            <input
                              v-model="slab.value"
                              @input="handleSlabValueChange(slab)"
                              type="number"
                              min="0"
                              :max="slab.type === 'percentage' ? 100 : totalAmountNum"
                              step="0.01"
                              :class="[
                                'w-full pl-8 pr-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 transition-colors',
                                slab.errors.value
                                  ? 'border-red-300 focus:ring-red-500 bg-red-50'
                                  : 'border-gray-300 focus:ring-[#ff5100]',
                              ]"
                              :placeholder="
                                slab.type === 'percentage' ? 'Enter %' : 'Enter amount'
                              "
                            />
                          </div>
                          <p v-if="slab.errors.value" class="text-xs text-red-600 mt-1">
                            {{ slab.errors.value }}
                          </p>
                        </div>

                        <!-- Validity Input -->
                        <div>
                          <label class="block text-xs font-medium text-gray-700 mb-2">
                            Validity Date
                            <span class="text-red-500">*</span>
                          </label>
                          <FormDateInput
                            v-model="slab.validity"
                            @update:modelValue="validateSlab(slab)"
                            :name="`slab_validity_${slab.id}`"
                            :required="true"
                            placeholder="Select validity date"
                            :error-message="slab.errors.validity"
                            :default-date="
                              getDefaultDate(DEFAULT_VALIDITY_DAYS * slab.id)
                            "
                          />
                          <p
                            v-if="slab.errors.validity"
                            class="text-xs text-red-600 mt-1"
                          >
                            {{ slab.errors.validity }}
                          </p>
                        </div>

                        <!-- Remarks Input - NEW SECTION -->
                        <div>
                          <label class="block text-xs font-medium text-gray-700 mb-2">
                            Remarks (Optional)
                          </label>
                          <textarea
                            v-model="slab.remarks"
                            rows="2"
                            :class="[
                              'w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 transition-colors resize-none',
                              'border-gray-300 focus:ring-[#ff5100]',
                            ]"
                            placeholder="Enter remarks for this payment slab..."
                          ></textarea>
                          <p class="text-xs text-gray-500 mt-1">
                            Optional notes about this payment term
                          </p>
                        </div>

                        <!-- What to Show on Invoice - NEW SECTION -->
                        <div>
                          <label class="block text-xs font-medium text-gray-700 mb-2">
                            Show on Invoice PDF
                            <span class="text-red-500">*</span>
                          </label>
                          <div class="space-y-2">
                            <label class="flex items-center cursor-pointer">
                              <input
                                type="radio"
                                v-model="slab.showOnInvoice"
                                value="validity"
                                class="w-4 h-4 text-[#ff5100] border-gray-300 focus:ring-[#ff5100] cursor-pointer"
                              />
                              <span class="ml-2 text-sm text-gray-700"
                                >Validity Date</span
                              >
                            </label>
                            <label class="flex items-center cursor-pointer">
                              <input
                                type="radio"
                                v-model="slab.showOnInvoice"
                                value="remarks"
                                class="w-4 h-4 text-[#ff5100] border-gray-300 focus:ring-[#ff5100] cursor-pointer"
                              />
                              <span class="ml-2 text-sm text-gray-700">Remarks</span>
                            </label>
                          </div>
                          <p class="text-xs text-gray-500 mt-1">
                            Choose what to display on the invoice
                          </p>
                        </div>

                        <!-- Calculated Values -->
                        <div class="bg-gray-50 border border-gray-200 rounded-md p-3">
                          <p class="text-xs text-gray-600 mb-1">
                            #{{ index + 1 }} Payment Term Amount (₹)
                          </p>
                          <div class="space-y-1">
                            <div class="flex justify-between text-xs">
                              <span class="text-gray-700">Amount:</span>
                              <span class="font-semibold text-gray-900">
                                {{ formatCurrency(getSlabCalculatedAmount(slab)) }}
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Summary -->
                  <div
                    class="bg-gradient-to-r from-gray-50 to-slate-50 border border-gray-300 rounded-lg p-4"
                  >
                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <p class="text-xs text-gray-600 mb-1">Total Allocated</p>
                        <p class="text-lg font-bold text-gray-900">
                          {{ formatCurrency(totalAllocatedAmount) }}
                        </p>
                        <p class="text-xs text-[#ff5100] font-medium">
                          {{ totalAllocatedPercentage.toFixed(2) }}%
                        </p>
                      </div>
                      <div>
                        <p class="text-xs text-gray-600 mb-1">Remaining</p>
                        <p
                          :class="[
                            'text-lg font-bold',
                            Math.abs(remainingPercentage) < 0.01
                              ? 'text-green-600'
                              : 'text-orange-600',
                          ]"
                        >
                          {{ formatCurrency(remainingAmount) }}
                        </p>
                        <p
                          :class="[
                            'text-xs font-medium',
                            Math.abs(remainingPercentage) < 0.01
                              ? 'text-green-600'
                              : 'text-orange-600',
                          ]"
                        >
                          {{ remainingPercentage.toFixed(2) }}%
                        </p>
                      </div>
                    </div>
                    <div
                      v-if="Math.abs(remainingPercentage) > 0.01"
                      class="mt-3 flex items-start gap-2 text-xs text-orange-700 bg-orange-50 border border-orange-200 rounded p-2"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 flex-shrink-0 mt-0.5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <span
                        >Total allocation must equal 100%. Please adjust slab
                        values.</span
                      >
                    </div>
                  </div>
                </div>
                <!-- Bank Account Selection -->
                <div class="mb-6">
                  <label class="block text-sm font-semibold text-gray-900 mb-3">
                    Bank Account
                    <span class="text-red-500">*</span>
                  </label>

                  <div v-if="isBankLoading" class="flex items-center justify-center py-8">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="32"
                      height="32"
                      viewBox="0 0 24 24"
                      class="animate-spin text-[#ff5100]"
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
                  </div>

                  <div
                    v-else-if="bankError"
                    class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-700"
                  >
                    <div class="flex items-center gap-2">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 flex-shrink-0"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <div class="flex-1">
                        <p class="font-medium">{{ bankError }}</p>
                        <button
                          @click="fetchBankInfo"
                          type="button"
                          class="text-xs underline hover:no-underline mt-1"
                        >
                          Try again
                        </button>
                      </div>
                    </div>
                  </div>

                  <div
                    v-else-if="activeBankAccounts.length === 0"
                    class="text-center py-8 bg-gray-50 border border-gray-200 rounded-lg"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-12 w-12 mx-auto text-gray-400 mb-2"
                      viewBox="0 0 24 24"
                    >
                      <path
                        fill="currentColor"
                        d="M20 8H4V6h16m0 12H4v-6h16m0-8H4c-1.11 0-2 .89-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Z"
                      />
                    </svg>
                    <p class="text-sm text-gray-600 font-medium">
                      No active bank accounts found
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                      Please add a bank account first
                    </p>
                  </div>

                  <div v-else>
                    <select
                      v-model="selectedBankAccount"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#ff5100] text-sm"
                    >
                      <option :value="null" disabled>Select a bank account</option>
                      <option
                        v-for="bank in activeBankAccounts"
                        :key="bank.id"
                        :value="bank.id"
                      >
                        {{ bank.account_name }} - {{ bank.bank_name }} ({{
                          maskAccountNumber(bank.account_number)
                        }})
                        {{ bank.is_primary ? "⭐" : "" }}
                      </option>
                    </select>

                    <transition
                      enter-active-class="transition-opacity duration-200"
                      leave-active-class="transition-opacity duration-200"
                      enter-from-class="opacity-0"
                      leave-to-class="opacity-0"
                    >
                      <div
                        v-if="bankError && !selectedBankAccount"
                        class="flex items-start gap-1.5 text-xs text-red-600 bg-red-50 border border-red-200 rounded-md p-2 mt-2"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4 flex-shrink-0 mt-0.5"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"
                          />
                        </svg>
                        <span>Please select a bank account</span>
                      </div>
                    </transition>

                    <transition
                      enter-active-class="transition-all duration-300"
                      leave-active-class="transition-all duration-200"
                      enter-from-class="opacity-0 -translate-y-2"
                      leave-to-class="opacity-0 -translate-y-2"
                    >
                      <div
                        v-if="selectedBankDetails"
                        class="mt-3 bg-gradient-to-br from-orange-50 to-red-50 border border-[#ff5100] rounded-lg p-4"
                      >
                        <div class="flex items-start gap-3">
                          <div class="p-2 bg-[#ff5100] bg-opacity-10 rounded-lg">
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
                          </div>
                          <div class="flex-1 text-sm">
                            <div class="flex items-center gap-2 mb-1">
                              <p class="font-semibold text-gray-900">
                                {{ selectedBankDetails.account_name }}
                              </p>
                              <span
                                v-if="selectedBankDetails.is_primary"
                                class="px-2 py-0.5 bg-[#ff5100] text-white text-xs font-medium rounded"
                              >
                                Primary
                              </span>
                            </div>
                            <p class="text-gray-700">
                              {{ selectedBankDetails.bank_name }}
                            </p>
                            <div class="grid grid-cols-2 gap-2 mt-2 text-xs">
                              <div>
                                <span class="text-gray-600">A/C:</span>
                                <span class="font-mono font-medium text-gray-900 ml-1">
                                  {{
                                    maskAccountNumber(selectedBankDetails.account_number)
                                  }}
                                </span>
                              </div>
                              <div>
                                <span class="text-gray-600">IFSC:</span>
                                <span class="font-mono font-medium text-gray-900 ml-1">{{
                                  selectedBankDetails.ifsc_code
                                }}</span>
                              </div>
                            </div>
                            <div class="mt-1.5 text-xs">
                              <span class="text-gray-600">Branch:</span>
                              <span class="text-gray-900 ml-1">{{
                                selectedBankDetails.branch
                              }}</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </transition>
                  </div>
                </div>

                <!-- Terms & Conditions Selection -->
                <div class="mb-6">
                  <label class="block text-sm font-semibold text-gray-900 mb-3">
                    Terms & Conditions
                    <span class="text-red-500">*</span>
                  </label>

                  <div
                    v-if="isTermsLoading"
                    class="flex items-center justify-center py-8"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="32"
                      height="32"
                      viewBox="0 0 24 24"
                      class="animate-spin text-[#ff5100]"
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
                  </div>

                  <div
                    v-else-if="termsError"
                    class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-700"
                  >
                    <div class="flex items-center gap-2">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 flex-shrink-0"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <div class="flex-1">
                        <p class="font-medium">{{ termsError }}</p>
                        <button
                          @click="fetchTermsConditions"
                          type="button"
                          class="text-xs underline hover:no-underline mt-1"
                        >
                          Try again
                        </button>
                      </div>
                    </div>
                  </div>

                  <div
                    v-else-if="activeTermsConditions.length === 0"
                    class="text-center py-8 bg-gray-50 border border-gray-200 rounded-lg"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-12 w-12 mx-auto text-gray-400 mb-2"
                      viewBox="0 0 24 24"
                    >
                      <path
                        fill="currentColor"
                        d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6m4 18H6V4h7v5h5v11Z"
                      />
                    </svg>
                    <p class="text-sm text-gray-600 font-medium">
                      No active terms & conditions found
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                      Please add terms & conditions first
                    </p>
                  </div>

                  <div v-else>
                    <select
                      v-model="selectedTermsCondition"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#ff5100] text-sm"
                    >
                      <option :value="null" disabled>Select terms & conditions</option>
                      <option
                        v-for="term in activeTermsConditions"
                        :key="term.id"
                        :value="term.id"
                      >
                        {{ term.name }} {{ term.is_primary ? "⭐" : "" }}
                      </option>
                    </select>

                    <transition
                      enter-active-class="transition-opacity duration-200"
                      leave-active-class="transition-opacity duration-200"
                      enter-from-class="opacity-0"
                      leave-to-class="opacity-0"
                    >
                      <div
                        v-if="termsError && !selectedTermsCondition"
                        class="flex items-start gap-1.5 text-xs text-red-600 bg-red-50 border border-red-200 rounded-md p-2 mt-2"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4 flex-shrink-0 mt-0.5"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"
                          />
                        </svg>
                        <span>Please select terms & conditions</span>
                      </div>
                    </transition>

                    <transition
                      enter-active-class="transition-all duration-300"
                      leave-active-class="transition-all duration-200"
                      enter-from-class="opacity-0 -translate-y-2"
                      leave-to-class="opacity-0 -translate-y-2"
                    >
                      <div
                        v-if="selectedTermsDetails"
                        class="mt-3 bg-gradient-to-br from-orange-50 to-red-50 border border-[#ff5100] rounded-lg p-4"
                      >
                        <div class="flex items-center gap-3">
                          <div class="p-2 bg-[#ff5100] bg-opacity-10 rounded-lg">
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              width="20"
                              height="20"
                              viewBox="0 0 24 24"
                              class="text-[#ff5100]"
                            >
                              <path
                                fill="currentColor"
                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6m4 18H6V4h7v5h5v11Z"
                              />
                            </svg>
                          </div>
                          <div class="flex-1 text-sm">
                            <div class="flex items-center gap-2">
                              <p class="font-semibold text-gray-900">
                                {{ selectedTermsDetails.name }}
                              </p>
                              <span
                                v-if="selectedTermsDetails.is_primary"
                                class="px-2 py-0.5 bg-[#ff5100] text-white text-xs font-medium rounded"
                              >
                                Primary
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </transition>
                  </div>
                </div>
              </div>

              <!-- Footer -->
              <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50">
                <button
                  type="button"
                  @click="closeModal"
                  class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  Cancel
                </button>
                <button
                  type="button"
                  @click="handleSubmit"
                  :disabled="!isFormValid"
                  :class="[
                    'px-6 py-2.5 text-sm font-medium text-white rounded-lg transition-all',
                    isFormValid
                      ? 'bg-[#ff5100] hover:bg-[#e64900]'
                      : 'bg-gray-300 cursor-not-allowed opacity-60',
                  ]"
                >
                  {{ submitButtonLabel }}
                </button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
<script setup>
import { ref, watch, computed, onMounted } from "vue";
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle,
} from "@headlessui/vue";
import FormDateInput from "./FormDateInput.vue";
import axios from "axios";

const base_url = import.meta.env.VITE_BACKEND_API_URL;

// Props
const props = defineProps({
  isOpen: Boolean,
  totalAmount: { type: [Number, String], required: true },
  mode: {
    type: String,
    default: "generate",
  },
  initialData: {
    type: Object,
    default: null,
  },
});

// Emits
const emit = defineEmits(["close", "submit"]);

// State - Slabs
const slabs = ref([]);
const slabIdCounter = ref(0);

// State - Bank Account
const bankAccounts = ref([]);
const selectedBankAccount = ref(null);
const isBankLoading = ref(false);
const bankError = ref("");

// State - Terms & Conditions
const termsConditions = ref([]);
const selectedTermsCondition = ref(null);
const isTermsLoading = ref(false);
const termsError = ref("");

// Constants
const MAX_SLABS = 5;
const MIN_SLABS = 1;
const DEFAULT_VALIDITY_DAYS = 7;

const getDefaultDate = (days) => {
  const date = new Date();
  date.setDate(date.getDate() + days);

  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const day = String(date.getDate()).padStart(2, "0");

  return `${year}-${month}-${day}`;
};

// Computed Properties
const totalAmountNum = computed(() => {
  const num = Number(props.totalAmount);
  return isNaN(num) ? 0 : num;
});

const isUpdateMode = computed(() => props.mode === "update");

const modalTitle = computed(() =>
  isUpdateMode.value ? "Update Proforma Configuration" : "Payment Slab Configuration"
);

const submitButtonLabel = computed(() =>
  isUpdateMode.value ? "Update Proforma" : "Confirm & Save"
);

const totalAllocatedPercentage = computed(() => {
  return slabs.value.reduce((sum, slab) => {
    const value = Number(slab.value) || 0;
    if (slab.type === "percentage") {
      return sum + value;
    } else {
      if (totalAmountNum.value === 0) return sum;
      return sum + (value / totalAmountNum.value) * 100;
    }
  }, 0);
});

const remainingPercentage = computed(() => {
  return Math.max(0, 100 - totalAllocatedPercentage.value);
});

const totalAllocatedAmount = computed(() => {
  return slabs.value.reduce((sum, slab) => {
    const value = Number(slab.value) || 0;
    if (slab.type === "amount") {
      return sum + value;
    } else {
      return sum + (totalAmountNum.value * value) / 100;
    }
  }, 0);
});

const remainingAmount = computed(() => {
  return Math.max(0, totalAmountNum.value - totalAllocatedAmount.value);
});

const canAddSlab = computed(() => {
  return slabs.value.length < MAX_SLABS && remainingPercentage.value > 0;
});

const isFormValid = computed(() => {
  // Validate minimum slabs
  if (slabs.value.length < MIN_SLABS) return false;

  // Check all slabs are valid
  const slabsValid = slabs.value.every((slab) => {
    const hasValue = slab.value && Number(slab.value) > 0;
    const hasValidity =
      slab.validity !== null && slab.validity !== undefined && slab.validity !== "";
    const noErrors = !slab.errors || Object.keys(slab.errors).length === 0;
    return hasValue && hasValidity && noErrors;
  });

  // Check total is exactly 100%
  const totalValid = Math.abs(totalAllocatedPercentage.value - 100) < 0.01;

  // Check bank and terms selection
  const bankValid =
    selectedBankAccount.value !== null && selectedBankAccount.value !== "";
  const termsValid =
    selectedTermsCondition.value !== null && selectedTermsCondition.value !== "";

  return slabsValid && totalValid && bankValid && termsValid;
});

const activeBankAccounts = computed(() => {
  return bankAccounts.value.filter((bank) => bank.status === "active");
});

const activeTermsConditions = computed(() => {
  return termsConditions.value.filter((term) => term.status === "active");
});

const selectedBankDetails = computed(() => {
  if (!selectedBankAccount.value) return null;
  return bankAccounts.value.find((bank) => bank.id === selectedBankAccount.value);
});

const selectedTermsDetails = computed(() => {
  if (!selectedTermsCondition.value) return null;
  return termsConditions.value.find((term) => term.id === selectedTermsCondition.value);
});

// Helper Functions
function formatCurrency(amount) {
  const num = Number(amount);
  if (isNaN(num)) return "₹0.00";
  return new Intl.NumberFormat("en-IN", {
    style: "currency",
    currency: "INR",
    maximumFractionDigits: 2,
  }).format(num);
}

function maskAccountNumber(accountNumber) {
  if (!accountNumber) return "";
  const accountStr = String(accountNumber);
  if (accountStr.length <= 4) return accountStr;
  const visible = accountStr.slice(-4);
  return "••••" + visible;
}

function createDefaultSlab() {
  slabIdCounter.value++;
  return {
    id: slabIdCounter.value,
    type: "percentage",
    value: 100,
    validity: "",
    remarks: "",
    showOnInvoice: "validity",
    errors: {},
  };
}

function autoSelectPrimaryBankAccount() {
  if (selectedBankAccount.value || bankAccounts.value.length === 0) return;

  const primaryBank = bankAccounts.value.find(
    (bank) => bank.is_primary && bank.status === "active"
  );

  if (primaryBank) {
    selectedBankAccount.value = primaryBank.id;
    return;
  }

  const firstActiveBank = activeBankAccounts.value[0];
  if (firstActiveBank) {
    selectedBankAccount.value = firstActiveBank.id;
  }
}

function autoSelectPrimaryTermsCondition() {
  if (selectedTermsCondition.value || termsConditions.value.length === 0) return;

  const primaryTerms = termsConditions.value.find(
    (term) => term.is_primary && term.status === "active"
  );

  if (primaryTerms) {
    selectedTermsCondition.value = primaryTerms.id;
    return;
  }

  const firstActiveTerm = activeTermsConditions.value[0];
  if (firstActiveTerm) {
    selectedTermsCondition.value = firstActiveTerm.id;
  }
}

function normalizeInitialSlab(slab) {
  const parsedAmount = Number(slab?.amount);
  const parsedPercentage = Number(
    slab?.calculatedPercentage ?? slab?.calculated_percentage
  );
  const parsedValue = Number(slab?.value);

  let type = slab?.type;
  if (type !== "percentage" && type !== "amount") {
    type = !Number.isNaN(parsedValue)
      ? !Number.isNaN(parsedPercentage) && Math.abs(parsedValue - parsedPercentage) < 0.01
        ? "percentage"
        : "amount"
      : !Number.isNaN(parsedPercentage)
      ? "percentage"
      : "amount";
  }

  let value = !Number.isNaN(parsedValue)
    ? parsedValue
    : type === "percentage"
    ? parsedPercentage
    : parsedAmount;

  if (Number.isNaN(value) || value < 0) {
    value = 0;
  }

  slabIdCounter.value++;

  return {
    id: slabIdCounter.value,
    type,
    value,
    validity: slab?.validity || "",
    remarks: slab?.remarks || "",
    showOnInvoice: slab?.what_to_show === "remarks" ? "remarks" : "validity",
    errors: {},
  };
}

function getInitialSlabs() {
  const existingSlabs = props.initialData?.payment_terms?.slabs;

  if (!Array.isArray(existingSlabs) || existingSlabs.length === 0) {
    return [createDefaultSlab()];
  }

  return existingSlabs.slice(0, MAX_SLABS).map((slab) => normalizeInitialSlab(slab));
}

function getInitialTermsConditionId() {
  const id =
    props.initialData?.terms_conditions_id ??
    props.initialData?.termsConditionsId ??
    props.initialData?.terms_conditions?.id ??
    null;

  if (id === null || id === undefined || id === "") return null;
  return Number(id);
}

function applyInitialBankSelection() {
  if (selectedBankAccount.value) return;

  const initialBankInfo = props.initialData?.bank_info;
  if (!initialBankInfo) {
    autoSelectPrimaryBankAccount();
    return;
  }

  const matchedBank = bankAccounts.value.find((bank) => {
    if (bank.status !== "active") return false;

    if (initialBankInfo.id && Number(bank.id) === Number(initialBankInfo.id)) {
      return true;
    }

    const accountMatch =
      initialBankInfo.account_number &&
      String(bank.account_number) === String(initialBankInfo.account_number);

    const ifscMatch =
      initialBankInfo.ifsc_code &&
      String(bank.ifsc_code || "").toUpperCase() ===
        String(initialBankInfo.ifsc_code || "").toUpperCase();

    const accountNameMatch =
      initialBankInfo.account_name &&
      String(bank.account_name || "").trim().toLowerCase() ===
        String(initialBankInfo.account_name || "").trim().toLowerCase();

    return accountMatch || (ifscMatch && accountNameMatch);
  });

  if (matchedBank) {
    selectedBankAccount.value = matchedBank.id;
    return;
  }

  autoSelectPrimaryBankAccount();
}

function applyInitialTermsSelection() {
  if (selectedTermsCondition.value) return;

  const initialTermsId = getInitialTermsConditionId();
  if (!initialTermsId) {
    autoSelectPrimaryTermsCondition();
    return;
  }

  const matchedTerm = termsConditions.value.find(
    (term) => term.status === "active" && Number(term.id) === Number(initialTermsId)
  );

  if (matchedTerm) {
    selectedTermsCondition.value = matchedTerm.id;
    return;
  }

  autoSelectPrimaryTermsCondition();
}

// Slab Management Functions
function addSlab() {
  if (!canAddSlab.value) return;

  slabIdCounter.value++;
  const remainingPerc = remainingPercentage.value;

  slabs.value.push({
    id: slabIdCounter.value,
    type: "percentage",
    value: Math.min(remainingPerc, 20),
    validity: "",
    remarks: "",
    showOnInvoice: "validity",
    errors: {},
  });
}

function removeSlab(slabId) {
  if (slabs.value.length <= MIN_SLABS) return;
  slabs.value = slabs.value.filter((s) => s.id !== slabId);
}

function validateSlab(slab) {
  const errors = {};
  const value = Number(slab.value);

  // Value validation
  if (!slab.value || slab.value === "") {
    errors.value = "Value is required";
  } else if (isNaN(value)) {
    errors.value = "Value must be a valid number";
  } else if (value <= 0) {
    errors.value = "Value must be greater than 0";
  } else if (slab.type === "percentage") {
    if (value > 100) {
      errors.value = "Percentage cannot exceed 100%";
    } else if (value < 0.01) {
      errors.value = "Percentage must be at least 0.01%";
    }
  } else {
    if (value > totalAmountNum.value) {
      errors.value = `Amount cannot exceed ${formatCurrency(totalAmountNum.value)}`;
    } else if (value < 0.01) {
      errors.value = "Amount must be at least ₹0.01";
    }
  }

  // Validity validation
  if (!slab.validity || slab.validity === "") {
    errors.validity = "Validity date is required";
  }

  slab.errors = errors;
  return Object.keys(errors).length === 0;
}

function handleSlabValueChange(slab) {
  validateSlab(slab);
}

function handleSlabTypeChange(slab, newType) {
  if (slab.type === newType) return;

  const oldValue = Number(slab.value) || 0;

  if (newType === "percentage" && slab.type === "amount") {
    if (totalAmountNum.value > 0) {
      slab.value = Math.min(
        100,
        Math.round((oldValue / totalAmountNum.value) * 100 * 100) / 100
      );
    } else {
      slab.value = 0;
    }
  } else if (newType === "amount" && slab.type === "percentage") {
    slab.value = Math.round(((totalAmountNum.value * oldValue) / 100) * 100) / 100;
  }

  slab.type = newType;
  validateSlab(slab);
}

function getSlabCalculatedAmount(slab) {
  const value = Number(slab.value) || 0;
  if (slab.type === "percentage") {
    return (totalAmountNum.value * value) / 100;
  }
  return value;
}

function getSlabCalculatedPercentage(slab) {
  const value = Number(slab.value) || 0;
  if (slab.type === "amount") {
    if (totalAmountNum.value === 0) return 0;
    return (value / totalAmountNum.value) * 100;
  }
  return value;
} // API Functions
async function fetchBankInfo() {
  isBankLoading.value = true;
  bankError.value = "";

  try {
    const response = await axios.get(`${base_url}/bank-info`, {
      params: { status: "active" },
    });

    if (response.data.success) {
      bankAccounts.value = response.data.data.data || [];
      applyInitialBankSelection();
    } else {
      bankError.value = "Failed to load bank accounts";
    }
  } catch (error) {
    console.error("Error fetching bank info:", error);
    bankError.value = error.response?.data?.message || "Failed to load bank accounts";
  } finally {
    isBankLoading.value = false;
  }
}

async function fetchTermsConditions() {
  isTermsLoading.value = true;
  termsError.value = "";

  try {
    const response = await axios.get(`${base_url}/terms-conditions/proforma_invoice`);

    if (response.data.success) {
      termsConditions.value = response.data.data.data || [];
      applyInitialTermsSelection();
    } else {
      termsError.value = "Failed to load terms & conditions";
    }
  } catch (error) {
    console.error("Error fetching terms & conditions:", error);
    termsError.value =
      error.response?.data?.message || "Failed to load terms & conditions";
  } finally {
    isTermsLoading.value = false;
  }
}

// Form Management Functions
function initializeForm() {
  // Reset errors
  bankError.value = "";
  termsError.value = "";
  selectedBankAccount.value = null;
  selectedTermsCondition.value = null;
  slabIdCounter.value = 0;

  // Use existing proforma slabs in update mode; fallback to default slab.
  slabs.value = getInitialSlabs();

  // Fetch data if not already loaded
  if (bankAccounts.value.length === 0) {
    fetchBankInfo();
  } else {
    applyInitialBankSelection();
  }
  if (termsConditions.value.length === 0) {
    fetchTermsConditions();
  } else {
    applyInitialTermsSelection();
  }
}

function resetForm() {
  slabs.value = [];
  slabIdCounter.value = 0;
  selectedBankAccount.value = null;
  selectedTermsCondition.value = null;
  bankError.value = "";
  termsError.value = "";
}

function closeModal() {
  resetForm();
  emit("close");
}
function handleSubmit() {
  // Final validation before submit
  if (!isFormValid.value) {
    // Show validation errors
    if (Math.abs(totalAllocatedPercentage.value - 100) >= 0.01) {
      console.error("Total allocation must equal 100%");
    }
    if (!selectedBankAccount.value) {
      bankError.value = "Please select a bank account";
    }
    if (!selectedTermsCondition.value) {
      termsError.value = "Please select terms & conditions";
    }

    // Validate all slabs
    slabs.value.forEach((slab) => {
      validateSlab(slab);
    });

    return;
  }

  // Prepare submit data
  const submitData = {
    paymentTerms: {
      slabs: slabs.value.map((slab) => ({
        id: slab.id,
        type: slab.type,
        value: Number(slab.value),
        amount: getSlabCalculatedAmount(slab),
        calculatedPercentage: getSlabCalculatedPercentage(slab),
        paymentDone: false,
        what_to_show: slab.showOnInvoice, // validity or remarks
        remarks: slab.remarks || "", // empty string if not provided
        validity: slab.validity,
      })),
      totalAmount: totalAmountNum.value,
      totalAllocatedAmount: totalAllocatedAmount.value,
      totalAllocatedPercentage: totalAllocatedPercentage.value,
    },
    bankInfo: selectedBankDetails.value,
    termsConditions: selectedTermsDetails.value,
  };

  // Emit submit event with data
  emit("submit", submitData);

  // Close modal after successful submit
  closeModal();
}

// function handleSubmit() {
//   // Final validation before submit
//   if (!isFormValid.value) {
//     // Show validation errors
//     if (Math.abs(totalAllocatedPercentage.value - 100) >= 0.01) {
//       console.error("Total allocation must equal 100%");
//     }
//     if (!selectedBankAccount.value) {
//       bankError.value = "Please select a bank account";
//     }
//     if (!selectedTermsCondition.value) {
//       termsError.value = "Please select terms & conditions";
//     }

//     // Validate all slabs
//     slabs.value.forEach((slab) => {
//       validateSlab(slab);
//     });

//     return;
//   }

//   // Prepare submit data
//   const submitData = {
//     paymentTerms: {
//       slabs: slabs.value.map((slab) => ({
//         id: slab.id,
//         type: slab.type,
//         value: Number(slab.value),
//         amount: getSlabCalculatedAmount(slab),
//         calculatedPercentage: getSlabCalculatedPercentage(slab),
//         paymentDone: false,
//         // PENDING TAKS PLEASE WORK ON IT
//         what_to_show: "validity",// either remarks or validity
//         remarks: "50% of the amount will be paid on the first phase of development",
//         validity: slab.validity,
//       })),
//       totalAmount: totalAmountNum.value,
//       totalAllocatedAmount: totalAllocatedAmount.value,
//       totalAllocatedPercentage: totalAllocatedPercentage.value,
//     },
//     bankInfo: selectedBankDetails.value,
//     termsConditions: selectedTermsDetails.value,
//   };

//   // Emit submit event with data
//   emit("submit", submitData);

//   // Close modal after successful submit
//   closeModal();
// }

// Watchers
watch(
  () => props.isOpen,
  (newVal) => {
    if (newVal) {
      initializeForm();
    } else {
      // Optional: Reset form when modal closes
      resetForm();
    }
  }
);

watch(
  () => props.totalAmount,
  () => {
    // Re-validate slabs if total amount changes
    if (slabs.value.length > 0) {
      slabs.value.forEach((slab) => {
        if (slab.type === "amount") {
          validateSlab(slab);
        }
      });
    }
  }
);

watch(
  () => props.initialData,
  () => {
    if (props.isOpen) {
      initializeForm();
    }
  }
);

// Lifecycle Hooks
onMounted(() => {
  if (props.isOpen) {
    initializeForm();
  }
});
</script>

<style scoped>
/* Custom scrollbar styling for modal content */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: #ff5100;
  border-radius: 4px;
  transition: background 0.3s ease;
}

::-webkit-scrollbar-thumb:hover {
  background: #e64900;
}

/* Firefox scrollbar styling */
* {
  scrollbar-width: thin;
  scrollbar-color: #ff5100 #f1f1f1;
}

/* Smooth transitions for all interactive elements */
button,
select,
input {
  transition: all 0.2s ease-in-out;
}

/* Focus visible outline for accessibility */
button:focus-visible,
select:focus-visible,
input:focus-visible {
  outline: 2px solid #ff5100;
  outline-offset: 2px;
}

/* Prevent number input spinner arrows in some browsers */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
}

/* Smooth animation for modal content */
.max-h-\[calc\(100vh-200px\)\] {
  scroll-behavior: smooth;
}

/* Ensure proper spacing for error messages */
.text-red-600 {
  line-height: 1.4;
}

/* Loading spinner animation enhancement */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }

  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Hover effects for interactive cards */
.border-gray-200:hover {
  border-color: #d1d5db;
}

/* Disabled state styling */
button:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

/* Custom focus ring color */
*:focus {
  outline-color: #ff5100;
}

/* Ensure proper text truncation */
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Gradient background animation */
@keyframes gradient-shift {
  0% {
    background-position: 0% 50%;
  }

  50% {
    background-position: 100% 50%;
  }

  100% {
    background-position: 0% 50%;
  }
}

/* Enhance gradient backgrounds */
.bg-gradient-to-br {
  background-size: 200% 200%;
}

/* Toast/Alert styling enhancement */
.bg-red-50 {
  backdrop-filter: blur(10px);
}

.bg-orange-50 {
  backdrop-filter: blur(10px);
}

/* Responsive design adjustments */
@media (max-width: 768px) {
  .grid-cols-2 {
    grid-template-columns: 1fr;
  }

  .max-w-3xl {
    max-width: 95vw;
  }
}

/* Print styles - hide modal backdrop */
@media print {
  .fixed.inset-0 {
    display: none;
  }
}

/* Enhanced focus states for better accessibility */
select:focus,
input:focus {
  box-shadow: 0 0 0 3px rgba(255, 81, 0, 0.1);
}

/* Smooth height transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

/* Error state pulsing animation */
@keyframes pulse-error {
  0%,
  100% {
    opacity: 1;
  }

  50% {
    opacity: 0.8;
  }
}

.border-red-300 {
  animation: pulse-error 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Success state styling */
.text-green-600 {
  font-weight: 600;
}

/* Loading state overlay */
.animate-spin {
  will-change: transform;
}

/* Optimize rendering performance */
.transform {
  transform: translateZ(0);
  backface-visibility: hidden;
}

/* Custom radio button styling */
input[type="radio"] {
  accent-color: #ff5100;
}

input[type="radio"]:focus {
  outline: 2px solid #ff5100;
  outline-offset: 2px;
}

/* Textarea styling */
textarea {
  font-family: inherit;
}

textarea:focus {
  box-shadow: 0 0 0 3px rgba(255, 81, 0, 0.1);
}

textarea::placeholder {
  color: #9ca3af;
}
</style>
