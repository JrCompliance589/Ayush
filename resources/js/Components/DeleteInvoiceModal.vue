<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto" @click.self="$emit('close')">
        <div class="flex min-h-screen items-center justify-center p-4">
          <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

          <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl">
            <div class="p-6">
              <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="text-red-600">
                  <path
                    fill="currentColor"
                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"
                  />
                </svg>
              </div>

              <div class="mb-6 text-center">
                <h3 class="mb-2 text-lg font-bold text-slate-900">Delete Invoice</h3>
                <p class="mb-4 text-sm text-slate-600">
                  Are you sure you want to delete this invoice? This action cannot be undone.
                </p>

                <div class="rounded-xl bg-slate-50 p-4 text-left">
                  <p class="mb-2 text-sm font-semibold text-slate-900">
                    {{ invoice?.company_name || "N/A" }}
                  </p>
                  <p class="text-xs text-slate-600">
                    {{ invoice?.proforma_number || invoice?.quotation_number || "Invoice" }}
                  </p>
                  <p class="text-xs text-slate-600">
                    {{ invoice?.contact_person || "N/A" }}
                  </p>
                </div>
              </div>

              <div class="flex items-center gap-3">
                <button
                  type="button"
                  :disabled="isDeleting"
                  @click="$emit('close')"
                  class="flex-1 rounded-xl bg-slate-100 px-4 py-3 font-medium text-slate-700 transition-all duration-200 hover:bg-slate-200 disabled:cursor-not-allowed disabled:opacity-60"
                >
                  Cancel
                </button>
                <button
                  type="button"
                  :disabled="isDeleting"
                  @click="$emit('confirm')"
                  class="flex-1 rounded-xl bg-gradient-to-r from-red-500 to-red-600 px-4 py-3 font-semibold text-white shadow-lg shadow-red-500/20 transition-all duration-200 hover:shadow-xl hover:shadow-red-500/30 disabled:cursor-not-allowed disabled:opacity-60"
                >
                  <span v-if="isDeleting">Deleting...</span>
                  <span v-else>Delete</span>
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
  isOpen: {
    type: Boolean,
    required: true,
  },
  invoice: {
    type: Object,
    default: null,
  },
  isDeleting: {
    type: Boolean,
    default: false,
  },
});

defineEmits(["close", "confirm"]);
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
