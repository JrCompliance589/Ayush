<script setup>
import { ref, computed, watch } from "vue";
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle,
} from "@headlessui/vue";
import axios from "axios";
import { IndianRupee } from "lucide-vue-next";

const props = defineProps({
  isOpen: Boolean,
  invoice: { type: Object, default: () => ({}) },
  closeBtn: { type: Boolean, default: true },
});

const emit = defineEmits(["close", "submit"]);

const base_url = import.meta.env.VITE_BACKEND_API_URL;

// Form state
const paymentType = ref("full");
const selectedSlabIds = ref([]);
const paymentMethod = ref("");
const paymentReference = ref("");
const selectedServiceType = ref("technical_services");
const applyTDS = ref(false);
const isLoadingTDS = ref(false);

// Payment exception handling
const hasPaymentException = ref(false);
const exceptionAmount = ref(0);
const exceptionReason = ref("");

const tdsInputAmount = ref(0);
const canEditTDS = ref(false);

// Balance state
const isLoadingBalance = ref(false);
const balanceInfo = ref({
  balance_type: null, // 'advance' | 'pending' | null
  balance_amount: 0,
  display_message: "",
  accounting_type: "", // 'credit' | 'debit'
  raw_balance: 0
});

const tdsInfo = ref({
  enabled: false,
  rate: 0,
  threshold_limit: 0,
  total_taxable_paid: 0,
  threshold_reached: false,
  effective_from: null,
  effective_to: null,
  section_code: "",
});

const paymentMethods = [
  "UPI",
  "Card",
  "Net Banking",
  "Wallet",
  "Cash",
  "Cheque",
  "Bank Transfer",
];

const serviceTypes = [
  { value: "technical_services", label: "Technical Services" },
  { value: "professional_services", label: "Professional Services" },
  { value: "contractor_services", label: "Contractor Services" },
  { value: "rent", label: "Rent" },
  { value: "commission", label: "Commission" },
  { value: "interest", label: "Interest" },
  { value: "salary", label: "Salary" },
  { value: "other", label: "Other" },
];

// Computed properties
const slabs = computed(() => props.invoice?.payment_terms?.slabs || []);

const unpaidSlabs = computed(() => slabs.value.filter((s) => !s.paymentDone));

const hasUnpaidSlabs = computed(() => unpaidSlabs.value.length > 0);

const selectedSlabs = computed(() => {
  if (paymentType.value === "full") {
    return unpaidSlabs.value;
  }
  return slabs.value.filter((s) => selectedSlabIds.value.includes(s.id));
});

// Total amount of selected slabs
const totalSlabAmount = computed(() =>
  selectedSlabs.value.reduce((sum, slab) => sum + Number(slab.amount), 0)
);

// TDS calculation - ONE-TIME on total invoice amount (amount_after_discount)
const totalTdsAmount = computed(() => {
  if (!applyTDS.value || !tdsInfo.value.enabled) return 0;

  // If user is editing TDS manually
  if (canEditTDS.value && tdsInputAmount.value > 0) {
    return Number(tdsInputAmount.value);
  }

  // ONE-TIME TDS: Calculate on total invoice amount (excluding GST)
  const baseAmount = Number(props.invoice?.amount_after_discount || 0);
  const calculatedTDS = Number((baseAmount * tdsInfo.value.rate / 100).toFixed(2));

  // Update the input field with calculated value
  if (!canEditTDS.value) {
    tdsInputAmount.value = calculatedTDS;
  }

  return calculatedTDS;
});

// Amount after TDS deduction
const amountAfterTDS = computed(() =>
  Number((totalSlabAmount.value - totalTdsAmount.value).toFixed(2))
);

// Balance adjustment amount (positive for advance, negative for pending)
// UPDATED: Only apply balance adjustment when payment exception is enabled
const balanceAdjustment = computed(() => {
  // Only apply balance adjustment if payment exception checkbox is checked
  if (!hasPaymentException.value || !balanceInfo.value.balance_type) return 0;

  // If advance, it's a NEGATIVE adjustment (subtract from amount)
  // If pending, it's a POSITIVE adjustment (add to amount)
  if (balanceInfo.value.balance_type === 'advance') {
    return -Number(balanceInfo.value.balance_amount); // SUBTRACT advance
  } else if (balanceInfo.value.balance_type === 'pending') {
    return Number(balanceInfo.value.balance_amount); // ADD pending
  }

  return 0;
});

// Final amount payable (amount after TDS + balance adjustment)
const totalPaidAmount = computed(() => {
  const baseAmount = amountAfterTDS.value;
  // If advance: baseAmount - balance (user pays less)
  // If pending: baseAmount + balance (user pays more)
  const finalAmount = baseAmount + balanceAdjustment.value;
  return Number(finalAmount.toFixed(2));
});;


// UPDATED: Calculate actual balance used based on exception amount
const actualBalanceUsed = computed(() => {
  if (!hasPaymentException.value || !balanceInfo.value.balance_type) return 0;

  const exceptionAmt = Number(exceptionAmount.value) || 0;
  const amountBeforeBalance = amountAfterTDS.value; // Invoice amount after TDS
  const fullBalanceAmount = balanceInfo.value.balance_amount;

  if (balanceInfo.value.balance_type === 'advance') {
    // Advance: User should pay LESS (balance is subtracted)
    // Expected with full balance: amountBeforeBalance - fullBalanceAmount

    if (exceptionAmt < amountBeforeBalance) {
      // User is paying less than full invoice, so they're using advance
      const balanceUsed = amountBeforeBalance - exceptionAmt;
      // Return the actual balance used (can't exceed available advance)
      return Math.min(balanceUsed, fullBalanceAmount);
    }
    return 0; // User paying full or more, not using advance

  } else if (balanceInfo.value.balance_type === 'pending') {
    // Pending: User should pay MORE (balance is added)

    if (exceptionAmt > amountBeforeBalance) {
      // User is paying more than invoice, so they're clearing pending
      const balanceCleared = exceptionAmt - amountBeforeBalance;
      // Return the actual pending cleared (can't exceed total pending)
      return Math.min(balanceCleared, fullBalanceAmount);
    }
    return 0; // User not clearing pending
  }

  return 0;
});

// UPDATED: Determine if balance adjustment should be shown in UI
const shouldShowBalanceAdjustment = computed(() => {
  // Simply check if there's any balance being used
  return actualBalanceUsed.value > 0;
});

// Fetch balance information from API
const fetchBalanceInfo = async () => {
  if (!props.invoice?.created_for_id) return;

  isLoadingBalance.value = true;

  try {
    const response = await axios.get(
      `${base_url}/payment-balances/user/${props.invoice.created_for_id}/summary`
    );

    if (response.data.success) {
      const data = response.data.data;
      balanceInfo.value = {
        balance_type: data.balance_type, // 'advance' | 'pending' | null
        balance_amount: parseFloat(data.balance_amount) || 0,
        display_message: data.display_message || "",
        accounting_type: data.accounting_type || "",
        raw_balance: parseFloat(data.raw_balance) || 0
      };
    }
  } catch (error) {
    console.error("Error fetching balance info:", error);
    balanceInfo.value = {
      balance_type: null,
      balance_amount: 0,
      display_message: "",
      accounting_type: "",
      raw_balance: 0
    };
  } finally {
    isLoadingBalance.value = false;
  }
};

// Fetch TDS information from API
const fetchTDSInfo = async () => {
  if (!props.invoice?.id || !props.invoice?.created_for_id) return;

  isLoadingTDS.value = true;

  try {
    const response = await axios.post(
      `${base_url}/tds-tax-slabs/eligibility-and-current-rate/${props.invoice.created_for_id}`,
      {
        service_type: selectedServiceType.value,
      }
    );

    if (response.data.success) {
      const data = response.data.data;
      tdsInfo.value = {
        enabled: data.enabled,
        rate: parseFloat(data.rate) || 0,
        threshold_limit: parseFloat(data.threshold_limit) || 0,
        total_taxable_paid: parseFloat(data.total_taxable_paid) || 0,
        threshold_reached: data.threshold_reached,
        effective_from: data.effective_from,
        effective_to: data.effective_to,
        section_code: data.section_code || "",
      };

      applyTDS.value = tdsInfo.value.enabled && tdsInfo.value.threshold_reached;
    }
  } catch (error) {
    console.error("Error fetching TDS info:", error);
    tdsInfo.value = {
      enabled: false,
      rate: 0,
      threshold_limit: 0,
      total_taxable_paid: 0,
      threshold_reached: false,
      effective_from: null,
      effective_to: null,
      section_code: "",
    };
  } finally {
    isLoadingTDS.value = false;
  }
};

// Reset form
const resetForm = () => {
  paymentType.value = "full";
  selectedSlabIds.value = [];
  paymentMethod.value = "";
  paymentReference.value = "";
  selectedServiceType.value = "technical_services";
  applyTDS.value = false;

  // Reset exception fields
  hasPaymentException.value = false;
  exceptionAmount.value = 0;
  exceptionReason.value = "";

  // Reset TDS input fields
  tdsInputAmount.value = 0;
  canEditTDS.value = false;

  tdsInfo.value = {
    enabled: false,
    rate: 0,
    threshold_limit: 0,
    total_taxable_paid: 0,
    threshold_reached: false,
    effective_from: null,
    effective_to: null,
    section_code: "",
  };

  // Reset balance info
  balanceInfo.value = {
    balance_type: null,
    balance_amount: 0,
    display_message: "",
    accounting_type: "",
    raw_balance: 0
  };
};

// Watch for modal open/close
watch(
  () => props.isOpen,
  (isOpen) => {
    if (isOpen) {
      resetForm();
      fetchTDSInfo();
      fetchBalanceInfo();
    }
  }
);

// Watch payment type changes
watch(paymentType, (newType) => {
  if (newType === "full") {
    selectedSlabIds.value = [];
  }
});

// Watch service type changes to refetch TDS info
watch(selectedServiceType, () => {
  if (props.isOpen) {
    fetchTDSInfo();
  }
});

// Watch for manual TDS input changes
watch(tdsInputAmount, (newVal) => {
  if (canEditTDS.value && newVal > 0) {
    const maxTDS = Number(props.invoice?.amount_after_discount || 0);
    if (newVal > maxTDS) {
      tdsInputAmount.value = maxTDS;
    }
  }
});

// Watch hasPaymentException to auto-fill amount with balance adjustment
// UPDATED: Now pre-fills with balance-adjusted amount when checked
watch(hasPaymentException, (newValue) => {
  if (newValue) {
    // Pre-fill with the calculated total (NOW includes balance because hasPaymentException is true)
    exceptionAmount.value = totalPaidAmount.value;
  } else {
    exceptionAmount.value = 0;
    exceptionReason.value = "";
  }
});

// Toggle slab selection
const toggleSlabSelection = (slabId) => {
  const index = selectedSlabIds.value.indexOf(slabId);
  if (index > -1) {
    selectedSlabIds.value.splice(index, 1);
  } else {
    selectedSlabIds.value.push(slabId);
  }
};// Submit payment

const submitPayment = () => {
  // Validation
  if (paymentType.value === "partial" && selectedSlabIds.value.length === 0) {
    alert("Please select at least one payment slab");
    return;
  }

  if (!paymentMethod.value) {
    alert("Please select a payment method");
    return;
  }

  if (!paymentReference.value.trim()) {
    alert("Please enter a payment reference/ID");
    return;
  }

  // Validate exception amount if enabled
  if (hasPaymentException.value) {
    if (!exceptionAmount.value || exceptionAmount.value <= 0) {
      alert("Please enter a valid exception amount");
      return;
    }
    if (!exceptionReason.value.trim()) {
      alert("Please provide a reason for the payment exception");
      return;
    }
  }

  // UPDATED: Calculate actual balance used for backend
  const actualBalanceAdjustment = actualBalanceUsed.value;

  const payload = {
    id: props.invoice.id,
    payment_mode: "Bank Transfer",
    payment_type: paymentType.value,
    service_type: selectedServiceType.value,
    slabIds:
      paymentType.value === "full"
        ? unpaidSlabs.value.map((s) => s.id)
        : selectedSlabIds.value,

    // Amount (only slab amount, no GST)
    amount: totalSlabAmount.value.toFixed(2),

    // TDS details (ONE-TIME on total invoice)
    tds_rate: applyTDS.value ? tdsInfo.value.rate : 0,
    tds_amount: totalTdsAmount.value.toFixed(2),
    tds_base_amount: Number(props.invoice?.amount_after_discount || 0).toFixed(2),

    // Final paid amount (with exception if applicable)
    paid_amount: hasPaymentException.value
      ? Number(exceptionAmount.value).toFixed(2)
      : totalPaidAmount.value.toFixed(2),

    // Payment exception details
    has_payment_exception: hasPaymentException.value,
    exception_amount: hasPaymentException.value ? Number(exceptionAmount.value).toFixed(2) : null,
    exception_reason: hasPaymentException.value ? exceptionReason.value.trim() : null,

    // UPDATED: Include actual balance adjustment used
    balance_adjustment: hasPaymentException.value && actualBalanceAdjustment > 0
      ? Number(actualBalanceAdjustment).toFixed(2)
      : null,
    balance_type: hasPaymentException.value && actualBalanceAdjustment > 0
      ? balanceInfo.value.balance_type
      : null,

    // Payment details
    method: paymentMethod.value,
    reference: paymentReference.value.trim(),
  };

  emit("submit", payload);
  closeModal();
};

const closeModal = () => {
  emit("close");
};

// Format currency
const formatCurrency = (amount) => {
  return new Intl.NumberFormat("en-IN", {
    style: "currency",
    currency: "INR",
    minimumFractionDigits: 2,
  }).format(amount);
};

// Format date
const formatDate = (dateString) => {
  if (!dateString) return "N/A";
  const date = new Date(dateString);
  return date.toLocaleDateString("en-IN", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
};
</script>

<template>
  <TransitionRoot appear :show="isOpen" as="template">
    <Dialog as="div" class="relative z-50" @close="closeModal">
      <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100"
        leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
          <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95">
            <DialogPanel
              class="w-full max-w-3xl transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
              <!-- Header -->
              <div class="relative px-6 py-4" style="background: linear-gradient(135deg, #ff5100 0%, #ff7a3d 100%);">
                <DialogTitle as="h3" class="text-xl font-bold text-white flex items-center gap-2">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                  Update Payment
                </DialogTitle>

                <p class="text-white text-opacity-90 text-xs mt-1">
                  Invoice: {{ invoice.proforma_number || "N/A" }}
                </p>

                <button v-if="closeBtn" type="button" @click="closeModal"
                  class="absolute top-3 right-4 bg-white bg-opacity-20 rounded-full p-1.5 hover:bg-opacity-30 transition-all hover:rotate-90 duration-300">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <!-- Loading State -->
              <div v-if="isLoadingTDS || isLoadingBalance" class="px-6 py-8 text-center">
                <div
                  class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-orange-500 border-t-transparent">
                </div>
                <p class="mt-3 text-gray-600">Loading payment information...</p>
              </div>

              <!-- Content -->
              <div v-else class="px-6 py-5 max-h-[65vh] overflow-y-auto">
                <!-- No Unpaid Slabs -->
                <div v-if="!hasUnpaidSlabs" class="text-center py-8">
                  <svg class="w-16 h-16 mx-auto text-green-500 mb-3" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <h4 class="text-lg font-semibold text-gray-800 mb-2">All Payments Completed</h4>
                  <p class="text-gray-600">All payment slabs have been paid for this invoice.</p>
                </div>

                <div v-else>
                  <!-- Balance Information Banner -->
                  <div v-if="balanceInfo.balance_type && Number(balanceInfo.balance_amount) !== 0" class="mb-5">
                    <div class="rounded-xl p-4 border-2" :class="[
                      balanceInfo.balance_type === 'advance'
                        ? 'bg-gradient-to-br from-green-50 to-emerald-50 border-green-300'
                        : 'bg-gradient-to-br from-red-50 to-rose-50 border-red-300'
                    ]">
                      <div class="flex items-start gap-3">
                        <div class="rounded-lg p-2" :class="[
                          balanceInfo.balance_type === 'advance' ? 'bg-green-500' : 'bg-red-500'
                        ]">
                          <IndianRupee class="text-white h-4 w-4" />
                        </div>
                        <div class="flex-1">
                          <div class="flex items-center justify-between mb-1">
                            <h4 class="text-sm font-bold text-gray-800">
                              {{ balanceInfo.balance_type === 'advance' ? 'Advance Balance' : 'Pending Balance' }}
                            </h4>
                            <span class="text-lg font-bold" :class="[
                              balanceInfo.balance_type === 'advance' ? 'text-green-600' : 'text-red-600'
                            ]">
                              {{ balanceInfo.balance_type === 'advance' ? '+' : '-' }}
                              {{ formatCurrency(balanceInfo.balance_amount) }}
                            </span>
                          </div>
                          <p class="text-xs text-gray-600">
                            {{ balanceInfo.display_message }}
                          </p>
                          <p class="text-xs mt-1.5" :class="[
                            balanceInfo.balance_type === 'advance' ? 'text-green-700' : 'text-red-700'
                          ]">
                            <span class="font-semibold">
                              {{ balanceInfo.balance_type === 'advance'
                                ? '✓ This amount will be adjusted (deducted) from the final payment'
                                : '⚠ This amount will be added to the final payment'
                              }}
                            </span>
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Payment Type Selection -->
                  <div v-if="slabs?.length > 1" class="mb-5">
                    <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Payment Type</h4>
                    <div class="grid grid-cols-2 gap-3">
                      <label class="relative cursor-pointer">
                        <input type="radio" value="full" v-model="paymentType" class="peer sr-only" />
                        <div
                          class="border-2 rounded-lg p-3 text-center transition-all peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:border-orange-300">
                          <div class="font-semibold text-gray-800">Full Payment</div>
                          <div class="text-xs text-gray-500 mt-1">Pay all pending slabs</div>
                        </div>
                      </label>
                      <label class="relative cursor-pointer">
                        <input type="radio" value="partial" v-model="paymentType" class="peer sr-only" />
                        <div
                          class="border-2 rounded-lg p-3 text-center transition-all peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:border-orange-300">
                          <div class="font-semibold text-gray-800">Partial Payment</div>
                          <div class="text-xs text-gray-500 mt-1">Select specific slabs</div>
                        </div>
                      </label>
                    </div>
                  </div>

                  <!-- Payment Slabs Section -->
                  <div class="mb-5">
                    <h4 v-if="slabs?.length > 1"
                      class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide flex items-center justify-between">
                      <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                          <path fill-rule="evenodd"
                            d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                            clip-rule="evenodd" />
                        </svg>
                        Payment Slabs
                      </span>
                      <span v-if="paymentType === 'partial'" class="text-xs font-normal text-orange-600">
                        {{ selectedSlabIds.length }} selected
                      </span>
                    </h4>

                    <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                      <label v-for="slab in slabs" :key="slab.id" class="block relative">
                        <input v-if="paymentType === 'partial'" type="checkbox" :disabled="slab.paymentDone"
                          :checked="selectedSlabIds.includes(slab.id)" @change="toggleSlabSelection(slab.id)"
                          class="peer sr-only" />

                        <div class="border-2 rounded-lg p-3 transition-all duration-200" :class="[
                          slab.paymentDone
                            ? 'bg-gray-50 border-gray-200 opacity-60'
                            : paymentType === 'full'
                              ? 'border-orange-300 bg-orange-50'
                              : 'border-gray-200 hover:border-orange-300 peer-checked:border-orange-500 peer-checked:bg-orange-50 cursor-pointer',
                        ]">
                          <div class="flex items-center gap-3">
                            <!-- Checkbox/Indicator -->
                            <div v-if="paymentType === 'partial'" class="flex-shrink-0">
                              <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-all"
                                :class="[
                                  slab.paymentDone
                                    ? 'border-gray-300 bg-gray-100'
                                    : selectedSlabIds.includes(slab.id)
                                      ? 'border-orange-500 bg-orange-500'
                                      : 'border-gray-300',
                                ]">
                                <svg v-if="selectedSlabIds.includes(slab.id) && !slab.paymentDone"
                                  class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7" />
                                </svg>
                              </div>
                            </div>

                            <!-- Slab Details -->
                            <div class="flex-1 min-w-0">
                              <div class="flex items-center justify-between gap-2 mb-1">
                                <div class="flex items-center gap-2">
                                  <div class="text-base font-bold text-gray-800">
                                    {{ formatCurrency(slab.amount) }}
                                  </div>
                                  <div v-if="slabs?.length > 1" class="text-xs text-gray-500">
                                    Slab #{{ slab.id }}
                                  </div>
                                </div>

                                <span v-if="slab.paymentDone"
                                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                  <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                      clip-rule="evenodd" />
                                  </svg>
                                  PAID
                                </span>
                              </div>

                              <div class="flex items-center gap-4 text-xs text-gray-600">
                                <span v-if="slab.what_to_show === 'validity'" class="text-gray-500">
                                  Valid until: {{ formatDate(slab.validity) }}
                                </span>
                                <span v-if="slab.what_to_show === 'remarks' && slab.remarks"
                                  class="text-gray-500 italic">
                                  {{ slab.remarks }}
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </label>
                    </div>
                  </div>

                  <!-- TDS Section -->
                  <div v-if="selectedSlabs.length > 0 || paymentType === 'full'" class="mb-5">
                    <!-- Service Type Selection -->
                    <div class="mb-4">
                      <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd"
                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                            clip-rule="evenodd" />
                        </svg>
                        Select Service Type for TDS
                      </h4>
                      <div class="relative">
                        <select v-model="selectedServiceType"
                          class="w-full px-4 py-3 text-sm border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all appearance-none bg-white cursor-pointer font-medium">
                          <option v-for="service in serviceTypes" :key="service.value" :value="service.value">
                            {{ service.label }}
                          </option>
                        </select>
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"
                          fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                      </div>
                      <p class="text-xs text-gray-500 mt-2">
                        Select the type of service to fetch applicable TDS rate and threshold information
                      </p>
                    </div>

                    <!-- TDS Information Card -->
                    <div v-if="tdsInfo.enabled"
                      class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-4">
                      <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-2">
                          <div class="bg-blue-500 rounded-lg p-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                          </div>
                          <div>
                            <h4 class="text-sm font-bold text-gray-800">TDS Information</h4>
                            <p class="text-xs text-gray-600">Section {{ tdsInfo.section_code }}</p>
                          </div>
                        </div>
                        <span v-if="tdsInfo.threshold_reached"
                          class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-300">
                          <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd" />
                          </svg>
                          Threshold Reached
                        </span>
                        <span v-else
                          class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 border border-yellow-300">
                          <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                              d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                              clip-rule="evenodd" />
                          </svg>
                          Below Threshold
                        </span>
                      </div>

                      <!-- TDS Details Grid -->
                      <div class="grid grid-cols-2 gap-3 mb-3">
                        <div class="bg-white rounded-lg p-2.5 border border-blue-100">
                          <div class="text-xs text-gray-500 mb-0.5">TDS Rate</div>
                          <div class="text-lg font-bold text-blue-600">{{ tdsInfo.rate }}%</div>
                        </div>
                        <div class="bg-white rounded-lg p-2.5 border border-blue-100">
                          <div class="text-xs text-gray-500 mb-0.5">Threshold Limit</div>
                          <div class="text-lg font-bold text-gray-800">{{ formatCurrency(tdsInfo.threshold_limit) }}
                          </div>
                        </div>
                        <div class="bg-white rounded-lg p-2.5 border border-blue-100">
                          <div class="text-xs text-gray-500 mb-0.5">Total Paid</div>
                          <div class="text-lg font-bold text-gray-800">{{ formatCurrency(tdsInfo.total_taxable_paid) }}
                          </div>
                        </div>
                        <div class="bg-white rounded-lg p-2.5 border border-blue-100">
                          <div class="text-xs text-gray-500 mb-0.5">Invoice Amount</div>
                          <div class="text-lg font-bold text-gray-800">{{ formatCurrency(invoice?.amount_after_discount
                            || 0) }}</div>
                        </div>
                      </div>

                      <!-- Validity Period -->
                      <div class="bg-white rounded-lg p-2.5 border border-blue-100 mb-3">
                        <div class="text-xs text-gray-500 mb-1.5 font-semibold">Validity Period</div>
                        <div class="flex items-center justify-between text-sm">
                          <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-gray-700 font-medium">{{ formatDate(tdsInfo.effective_from) }}</span>
                          </div>
                          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M14 5l7 7m0 0l-7 7m7-7H3" />
                          </svg>
                          <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-gray-700 font-medium">{{ formatDate(tdsInfo.effective_to) }}</span>
                          </div>
                        </div>
                      </div>

                      <!-- TDS Configuration (ONE-TIME ONLY) -->
                      <div class="bg-white rounded-lg p-3 border border-blue-100 mb-3">
                        <div class="text-xs text-gray-500 mb-2 font-semibold">TDS Configuration</div>

                        <!-- TDS Info Display -->
                        <div class="bg-blue-50 rounded-lg p-2.5 mb-3">
                          <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor"
                              viewBox="0 0 20 20">
                              <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                            </svg>
                            <div class="flex-1">
                              <div class="text-xs text-gray-600">
                                TDS will be calculated on total invoice amount:
                                <span class="font-medium">{{ formatCurrency(invoice?.amount_after_discount || 0)
                                }}</span>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- TDS Amount Input -->
                        <div>
                          <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold text-gray-700">TDS Amount</label>
                            <label class="flex items-center gap-1.5 cursor-pointer">
                              <input type="checkbox" v-model="canEditTDS"
                                class="w-3.5 h-3.5 text-blue-500 border-gray-300 rounded focus:ring-blue-500 focus:ring-1" />
                              <span class="text-xs text-gray-600">Edit manually</span>
                            </label>
                          </div>

                          <div class="relative">
                            <span
                              class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium text-sm">₹</span>
                            <input v-model.number="tdsInputAmount" type="number" step="0.01" min="0"
                              :disabled="!canEditTDS" :readonly="!canEditTDS"
                              placeholder="TDS will be calculated automatically"
                              class="w-full pl-8 pr-3 py-2 text-sm border-2 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                              :class="canEditTDS ? 'border-blue-300 bg-white' : 'border-gray-200 bg-gray-50 cursor-not-allowed'" />
                          </div>
                          <div class="mt-1.5 text-xs text-gray-500">
                            <span v-if="!canEditTDS">
                              Auto-calculated: {{ tdsInfo.rate }}% of invoice amount
                            </span>
                            <span v-else class="text-amber-600 font-medium">
                              ⚠️ Manual mode: Verify the amount before submitting
                            </span>
                          </div>
                        </div>
                      </div>

                      <!-- Apply TDS Checkbox -->
                      <label
                        class="flex items-start gap-3 cursor-pointer bg-white rounded-lg p-3 border-2 border-blue-200 hover:border-blue-300 transition-all">
                        <input type="checkbox" v-model="applyTDS"
                          class="mt-0.5 w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-orange-500 focus:ring-2" />
                        <div class="flex-1">
                          <div class="font-semibold text-sm text-gray-800 mb-1">
                            Apply TDS (Tax Deducted at Source)
                          </div>
                          <p class="text-xs text-gray-600 leading-relaxed">
                            <template v-if="totalTdsAmount > 0">
                              <span class="font-semibold text-red-600">{{ formatCurrency(totalTdsAmount) }}</span>
                              will be deducted as TDS.
                            </template>
                          </p>
                        </div>
                      </label>
                    </div>

                    <!-- No TDS Available Message -->
                    <div v-else class="bg-gray-50 border-2 border-gray-200 rounded-xl p-4">
                      <div class="flex items-start gap-3">
                        <div class="bg-gray-300 rounded-lg p-2">
                          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                        </div>
                        <div class="flex-1">
                          <h4 class="text-sm font-bold text-gray-700 mb-1">TDS Not Applicable</h4>
                          <p class="text-xs text-gray-600">
                            TDS is not applicable for the selected service type or the threshold has not been reached
                            for this client.
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Payment Exception Section -->
                  <div v-if="selectedSlabs.length > 0 || paymentType === 'full'" class="mb-5">
                    <div class="bg-amber-50 border-2 border-amber-200 rounded-xl p-4">
                      <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" v-model="hasPaymentException"
                          class="mt-0.5 w-5 h-5 text-amber-500 border-gray-300 rounded focus:ring-amber-500 focus:ring-2" />
                        <div class="flex-1">
                          <div class="font-semibold text-sm text-gray-800 mb-1 flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                            </svg>
                            Payment Exception (Partial/Excess Payment)
                          </div>
                          <p class="text-xs text-gray-600 mb-3">
                            Enable this if the client paid a different amount than expected. The amount field below is
                            pre-filled with the
                            calculated total (including balance adjustment if applicable).
                          </p>

                          <!-- Exception Fields -->
                          <div v-if="hasPaymentException" class="space-y-3 mt-3">
                            <div>
                              <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Actual Amount Received <span class="text-red-500">*</span>
                              </label>
                              <div class="relative">
                                <span
                                  class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">₹</span>
                                <input v-model.number="exceptionAmount" type="number" step="0.01" min="0"
                                  placeholder="Enter actual amount received"
                                  class="w-full pl-8 pr-3 py-2.5 text-sm border-2 border-amber-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all" />
                              </div>
                              <div class="flex items-center justify-between mt-1">
                                <p class="text-xs text-gray-500">
                                  Expected (with balance): {{ formatCurrency(totalPaidAmount) }}
                                </p>
                                <p v-if="exceptionAmount > 0" class="text-xs font-semibold" :class="exceptionAmount
                                  < totalPaidAmount ? 'text-red-600' : exceptionAmount > totalPaidAmount ?
                                  'text-green-600' : 'text-gray-600'">
                                  {{ exceptionAmount < totalPaidAmount ? 'Short by' : exceptionAmount > totalPaidAmount
                                    ? 'Excess by' : 'Exact' }}:
                                    {{ exceptionAmount !== totalPaidAmount ? formatCurrency(Math.abs(exceptionAmount -
                                      totalPaidAmount)) : '' }}
                                </p>
                              </div>
                            </div>

                            <div>
                              <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Reason for Exception <span class="text-red-500">*</span>
                              </label>
                              <textarea v-model="exceptionReason" rows="2"
                                placeholder="E.g., Client agreed to pay remaining ₹1000 in next payment" maxlength="255"
                                class="w-full px-3 py-2 text-sm border-2 border-amber-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all resize-none"></textarea>
                            </div>
                          </div>
                        </div>
                      </label>
                    </div>
                  </div>

                  <!-- Payment Details Section -->
                  <div v-if="selectedSlabs.length > 0 || paymentType === 'full'" class="mb-5">
                    <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Payment Details</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                      <!-- Payment Method -->
                      <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                          Payment Method <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                          <select v-model="paymentMethod" required
                            class="w-full px-3 py-2 text-sm border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all appearance-none bg-white cursor-pointer">
                            <option value="" disabled>Select method</option>
                            <option v-for="method in paymentMethods" :key="method" :value="method">{{ method }}</option>
                          </select>
                          <svg
                            class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                          </svg>
                        </div>
                      </div>

                      <!-- Payment Reference -->
                      <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                          Payment Reference/ID <span class="text-red-500">*</span>
                        </label>
                        <input v-model="paymentReference" type="text" required placeholder="e.g., pay_LQx13l"
                          class="w-full px-3 py-2 text-sm border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all" />
                      </div>
                    </div>
                  </div>

                  <!-- Payment Summary -->
                  <div v-if="selectedSlabs.length > 0 || paymentType === 'full'"
                    class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4 border border-gray-200">
                    <h4 class="text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Payment Summary</h4>

                    <div class="space-y-1.5 text-sm">
                      <div class="flex justify-between">
                        <span class="text-gray-600">Slab Amount:</span>
                        <span class="font-semibold text-gray-800">{{ formatCurrency(totalSlabAmount) }}</span>
                      </div>

                      <div v-if="applyTDS && totalTdsAmount > 0"
                        class="flex justify-between text-red-600 pt-1.5 border-t border-gray-300">
                        <span>TDS Deduction:</span>
                        <span class="font-semibold">- {{ formatCurrency(totalTdsAmount) }}</span>
                      </div>

                      <!-- Show amount after TDS (before balance adjustment) -->
                      <div v-if="applyTDS && totalTdsAmount > 0" class="flex justify-between pt-1">
                        <span class="text-gray-600">Amount after TDS:</span>
                        <span class="font-semibold text-gray-800">{{ formatCurrency(amountAfterTDS) }}</span>
                      </div>

                      <!-- Balance Adjustment -->
                      <!-- UPDATED: Only show when payment exception is checked AND balance is actually being used -->
                      <div v-if="hasPaymentException && balanceInfo.balance_type && shouldShowBalanceAdjustment"
                        class="flex justify-between pt-1.5 border-t border-gray-300" :class="[
                          balanceInfo.balance_type === 'advance' ? 'text-green-600' : 'text-red-600'
                        ]">
                        <span class="flex items-center gap-1">
                          {{ balanceInfo.balance_type === 'advance' ? 'Advance Adjustment:' : 'Pending Adjustment:' }}
                          <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                              clip-rule="evenodd" />
                          </svg>
                        </span>
                        <span class="font-semibold">
                          {{ balanceInfo.balance_type === 'advance' ? '-' : '+' }}
                          {{ formatCurrency(actualBalanceUsed) }}
                        </span>
                      </div>

                      <div class="flex justify-between text-base font-bold pt-2 border-t-2 border-gray-400 mt-1.5"
                        :class="hasPaymentException ? 'text-gray-500' : 'text-gray-800'">
                        <span>Expected Amount:</span>
                        <span :class="hasPaymentException ? 'line-through' : 'text-orange-600'">{{
                          formatCurrency(totalPaidAmount) }}</span>
                      </div>

                      <!-- Show exception amount if enabled -->
                      <div v-if="hasPaymentException && exceptionAmount > 0"
                        class="flex justify-between text-lg font-bold pt-2 border-t-2 border-amber-400">
                        <span class="text-gray-800">Actual Amount Received:</span>
                        <span class="text-amber-600">{{ formatCurrency(exceptionAmount) }}</span>
                      </div>

                      <!-- Show final amount if no exception -->
                      <div v-else-if="!hasPaymentException" class="flex justify-between text-base font-semibold pt-1">
                        <span class="text-gray-600">Final Payable:</span>
                        <span class="text-orange-600">{{ formatCurrency(totalPaidAmount) }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Footer Actions -->
              <div v-if="hasUnpaidSlabs && !isLoadingTDS && !isLoadingBalance"
                class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" @click="closeModal"
                  class="px-5 py-2 rounded-lg text-sm font-semibold transition-all border-2 border-gray-300 text-gray-700 hover:bg-gray-100 hover:border-gray-400">
                  Cancel
                </button>
                <button type="button" @click="submitPayment" :disabled="(paymentType === 'partial' && selectedSlabIds.length === 0) ||
                  !paymentMethod ||
                  !paymentReference ||
                  (hasPaymentException && (!exceptionAmount || !exceptionReason.trim()))
                  "
                  class="px-5 py-2 rounded-lg text-sm font-semibold text-white transition-all shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-md flex items-center gap-2"
                  :style="{
                    backgroundColor:
                      (paymentType === 'partial' && selectedSlabIds.length === 0) ||
                        !paymentMethod ||
                        !paymentReference ||
                        (hasPaymentException && (!exceptionAmount || !exceptionReason.trim()))
                        ? '#d1d5db'
                        : '#ff5100',
                  }" @mouseover="(e) => {
                    if (!((paymentType === 'partial' && selectedSlabIds.length === 0) || !paymentMethod ||
                      !paymentReference || (hasPaymentException && (!exceptionAmount || !exceptionReason.trim()))))
                      e.target.style.backgroundColor = '#e64900';
                  }" @mouseout="(e) => {
                    if (!((paymentType === 'partial' && selectedSlabIds.length === 0) || !paymentMethod ||
                      !paymentReference || (hasPaymentException && (!exceptionAmount || !exceptionReason.trim()))))
                      e.target.style.backgroundColor = '#ff5100';
                  }">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  Submit Payment
                </button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
<style scoped>
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #ff5100;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #e64900;
}

input:focus,
select:focus {
  outline: none;
}
</style>