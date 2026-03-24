<template>
    <Teleport to="body">
        <Transition name="modal">
            <div class="fixed inset-0 z-50 overflow-y-auto" @click.self="$emit('close')">
                <div class="flex min-h-screen items-center justify-center p-4">
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

                    <!-- Modal -->
                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                        <!-- Header -->
                        <div
                            class="sticky top-0 bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200 z-10">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" class="text-white">
                                            <path fill="currentColor"
                                                d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold text-slate-900">
                                            {{ isEdit ? $t('Edit Product') : $t('Add Product') }}
                                        </h2>
                                        <p class="text-sm text-slate-600">
                                            {{ $t('Enter product details') }}
                                        </p>
                                    </div>
                                </div>
                                <button @click="$emit('close')" type="button"
                                    class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Form -->
                        <form @submit.prevent="submitForm" class="p-6 space-y-6">
                            <!-- Product Information -->
                            <div class="space-y-4">
                                <div class="flex items-center gap-2 pb-2 border-b border-slate-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="currentColor"
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                    </svg>
                                    <h3 class="text-sm font-semibold text-slate-900">{{ $t('Product Information') }}
                                    </h3>
                                </div>

                                <FormInput v-model="form.name" :name="$t('Product Name')" :error="form.errors.name"
                                    :placeholder="$t('e.g., Web Development Service')" :required="true" />

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-slate-900">
                                            {{ $t('Category') }}
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <select v-model="form.category"
                                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200">
                                            <option value="good">{{ $t('Good') }}</option>
                                            <option value="service">{{ $t('Service') }}</option>
                                        </select>
                                        <p v-if="form.errors.category" class="text-xs text-red-600">
                                            {{ form.errors.category }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-slate-900">
                                            {{ $t('Amount') }}
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-slate-500 text-sm font-medium">₹</span>
                                            </div>
                                            <input v-model="form.amount" type="number" step="0.01" min="0"
                                                :placeholder="$t('e.g., 5000.00')"
                                                class="w-full pl-8 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200">
                                        </div>
                                        <p v-if="form.errors.amount" class="text-xs text-red-600">
                                            {{ form.errors.amount }}
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <FormInput v-model="form.hsn_sac" :name="$t('HSN/SAC Code')"
                                        :error="form.errors.hsn_sac" :placeholder="$t('e.g., 998314')"
                                        style="text-transform: uppercase;" />

                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-slate-900">
                                            {{ $t('GST Rate (%)') }}
                                        </label>
                                        <div class="relative">
                                            <input v-model="form.gst_rate" type="number" step="0.01" min="0" max="100"
                                                :placeholder="$t('e.g., 18.00')"
                                                class="w-full pl-4 pr-10 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200">
                                            <div
                                                class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-slate-500 text-sm font-medium">%</span>
                                            </div>
                                        </div>
                                        <p v-if="form.errors.gst_rate" class="text-xs text-red-600">
                                            {{ form.errors.gst_rate }}
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-slate-900">
                                        {{ $t('Unit') }}
                                    </label>
                                    <select v-model="form.unit"
                                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200">
                                        <option value="pcs">{{ $t('Pieces (pcs)') }}</option>
                                        <option value="hrs">{{ $t('Hours (hrs)') }}</option>
                                        <option value="days">{{ $t('Days') }}</option>
                                        <option value="months">{{ $t('Months') }}</option>
                                        <option value="kg">{{ $t('Kilograms (kg)') }}</option>
                                        <option value="ltr">{{ $t('Liters (ltr)') }}</option>
                                        <option value="mtr">{{ $t('Meters (mtr)') }}</option>
                                        <option value="sqft">{{ $t('Square Feet (sqft)') }}</option>
                                        <option value="box">{{ $t('Box') }}</option>
                                        <option value="set">{{ $t('Set') }}</option>
                                    </select>
                                    <p v-if="form.errors.unit" class="text-xs text-red-600">
                                        {{ form.errors.unit }}
                                    </p>
                                </div>
                            </div>

                            <!-- Settings -->
                            <div class="space-y-4">
                                <div class="flex items-center gap-2 pb-2 border-b border-slate-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="currentColor"
                                            d="M12 15.5A3.5 3.5 0 0 1 8.5 12A3.5 3.5 0 0 1 12 8.5a3.5 3.5 0 0 1 3.5 3.5a3.5 3.5 0 0 1-3.5 3.5m7.43-2.53c.04-.32.07-.64.07-.97c0-.33-.03-.66-.07-1l2.11-1.63c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.31-.61-.22l-2.49 1c-.52-.39-1.06-.73-1.69-.98l-.37-2.65A.506.506 0 0 0 14 2h-4c-.25 0-.46.18-.5.42l-.37 2.65c-.63.25-1.17.59-1.69.98l-2.49-1c-.22-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64L4.57 11c-.04.34-.07.67-.07 1c0 .33.03.65.07.97l-2.11 1.66c-.19.15-.25.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1.01c.52.4 1.06.74 1.69.99l.37 2.65c.04.24.25.42.5.42h4c.25 0 .46-.18.5-.42l.37-2.65c.63-.26 1.17-.59 1.69-.99l2.49 1.01c.22.08.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.66Z" />
                                    </svg>
                                    <h3 class="text-sm font-semibold text-slate-900">{{ $t('Settings') }}</h3>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-slate-900">
                                        {{ $t('Status') }}
                                    </label>
                                    <div class="flex items-center h-[50px]">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" v-model="form.is_active" class="sr-only peer">
                                            <div
                                                class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#ff5100]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-[#ff5100] peer-checked:to-[#ff7340]">
                                            </div>
                                            <span class="ml-3 text-sm text-slate-600">
                                                {{ form.is_active ? $t('Active') : $t('Inactive') }}
                                            </span>
                                        </label>
                                    </div>
                                    <p class="text-xs text-slate-500">
                                        {{ $t('Active products will appear in invoice dropdown') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-200">
                                <button type="button" @click="$emit('close')"
                                    class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition-all duration-200">
                                    {{ $t('Cancel') }}
                                </button>
                                <button type="submit"
                                    class="group px-8 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                    :disabled="isSubmitting">
                                    <span v-if="isSubmitting" class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                                opacity=".5" />
                                            <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                                <animateTransform attributeName="transform" dur="1s" from="0 12 12"
                                                    repeatCount="indefinite" to="360 12 12" type="rotate" />
                                            </path>
                                        </svg>
                                        {{ $t('Saving...') }}
                                    </span>
                                    <span v-else class="flex items-center gap-2">
                                        {{ isEdit ? $t('Update Product') : $t('Add Product') }}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="m9.55 18l-5.7-5.7l1.425-1.425L9.55 15.15l9.175-9.175L20.15 7.4z" />
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
import { ref, reactive } from 'vue';
import FormInput from '@/Components/FormInput.vue';
import axios from 'axios';

const base_url = import.meta.env.VITE_BACKEND_API_URL;

const props = defineProps({
    product: Object,
    isEdit: Boolean
});

const emit = defineEmits(['close', 'success']);

const isSubmitting = ref(false);

const form = reactive({
    name: '',
    amount: '',
    hsn_sac: '',
    gst_rate: 18,
    unit: 'pcs',
    category: 'service',
    is_active: true,
    errors: {}
});

// Initialize form with existing data if editing
if (props.isEdit && props.product) {
    form.name = props.product.name;
    form.amount = props.product.amount;
    form.hsn_sac = props.product.hsn_sac || '';
    form.gst_rate = props.product.gst_rate || 18;
    form.unit = props.product.unit || 'pcs';
    form.category = props.product.category || 'service';
    form.is_active = props.product.is_active;
}

const formatAmountInWords = (amount) => {
    if (!amount) return '';
    const num = parseFloat(amount);
    if (isNaN(num)) return '';

    const formatter = new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        maximumFractionDigits: 2
    });

    return formatter.format(num);
};

const submitForm = async () => {
    isSubmitting.value = true;
    form.errors = {};

    try {
        const data = {
            name: form.name,
            amount: Number(form.amount),
            hsn_sac: form.hsn_sac || null,
            gst_rate: Number(form.gst_rate),
            unit: form.unit || 'pcs',
            category: form.category,
            is_active: form.is_active
        };

        if (props.isEdit) {
            await axios.put(`${base_url}/products/${props.product.id}`, data);
        } else {
            await axios.post(`${base_url}/products`, data);
        }

        emit('success');
    } catch (error) {
        if (error.response?.data?.errors) {
            form.errors = error.response.data.errors;
        }
        console.error('Error submitting form:', error);
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