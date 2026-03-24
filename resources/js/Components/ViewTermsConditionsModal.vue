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
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto"
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
                        d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"
                      />
                    </svg>
                  </div>
                  <div>
                    <h2 class="text-lg font-bold text-slate-900">
                      {{ termsConditions.name }}
                    </h2>
                    <div class="flex items-center gap-2 mt-1">
                      <span
                        class="px-2 py-0.5 bg-slate-100 text-slate-700 text-xs font-medium rounded"
                      >
                        {{ formatCategory(termsConditions.category) }}
                      </span>
                      <span
                        v-if="termsConditions.is_primary"
                        class="px-2 py-0.5 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white text-xs font-semibold rounded"
                      >
                        {{ $t("Primary") }}
                      </span>
                      <span
                        :class="getStatusClass(termsConditions.status)"
                        class="px-2 py-0.5 text-xs font-semibold rounded"
                      >
                        {{
                          $t(
                            termsConditions.status.charAt(0).toUpperCase() +
                              termsConditions.status.slice(1)
                          )
                        }}
                      </span>
                    </div>
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

            <!-- Content -->
            <div class="p-6">
              <div class="space-y-4">
                <div
                  v-for="(term, index) in termsConditions.terms"
                  :key="index"
                  class="bg-gradient-to-br from-slate-50 to-white rounded-xl p-5 border border-slate-200 hover:shadow-md transition-all duration-200"
                >
                  <div class="flex items-start gap-4">
                    <div
                      class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-[#ff5100] to-[#ff7340] text-white text-base font-bold rounded-full flex items-center justify-center shadow-lg shadow-[#ff5100]/20"
                    >
                      {{ index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                      <h3 class="text-base font-bold text-slate-900 mb-2">
                        {{ term.title }}
                      </h3>
                      <p
                        class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap"
                      >
                        {{ term.description }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div
              class="sticky bottom-0 bg-slate-50 px-6 py-4 border-t border-slate-200"
            >
              <div class="flex items-center justify-between">
                <div class="text-sm text-slate-600">
                  {{ $t("Total Terms") }}:
                  <span class="font-semibold text-slate-900">{{
                    termsConditions.terms.length
                  }}</span>
                </div>
                <button
                  @click="$emit('close')"
                  type="button"
                  class="px-6 py-2.5 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 transition-all duration-200"
                >
                  {{ $t("Close") }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
defineProps({
  termsConditions: {
    type: Object,
    required: true,
  },
});
defineEmits(["close"]);
const formatCategory = (category) => {
  const categories = {
    quotation: "Quotation",
    proforma_invoice: "Proforma Invoice",
    invoice: "Invoice",
    general: "General",
    other: "Other",
  };
  return categories[category] || category;
};
const getStatusClass = (status) => {
  const classes = {
    active: "bg-green-100 text-green-700",
    inactive: "bg-yellow-100 text-yellow-700",
  };
  return classes[status] || "bg-slate-100 text-slate-700";
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
</style>
