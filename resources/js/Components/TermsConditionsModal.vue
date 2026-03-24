<template>
    <Teleport to="body">
        <Transition name="modal">
            <div class="fixed inset-0 z-50 overflow-y-auto" @click.self="$emit('close')">
                <div class="flex min-h-screen items-center justify-center p-4">
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

                    <!-- Modal -->
                    <div
                        class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
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
                                                d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold text-slate-900">
                                            {{ isEdit ? $t('Edit Terms & Conditions') : $t('Add Terms & Conditions') }}
                                        </h2>
                                        <p class="text-sm text-slate-600">
                                            {{ $t('Configure terms and conditions for documents') }}
                                        </p>
                                    </div>
                                </div>
                                <button @click="$emit('close')" type="button"
                                    class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Form -->
                        <form @submit.prevent="submitForm" class="p-6 space-y-6">
                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <div class="flex items-center gap-2 pb-2 border-b border-slate-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" class="text-[#ff5100]">
                                        <path fill="currentColor"
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                    </svg>
                                    <h3 class="text-sm font-semibold text-slate-900">{{ $t('Basic Information') }}</h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <FormInput v-model="form.name" :name="$t('Name')" :error="form.errors.name"
                                        :placeholder="$t('e.g., Standard Quotation Terms')" :required="true" />

                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-slate-900">
                                            {{ $t('Category') }}
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <select v-model="form.category"
                                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200">
                                            <option value="">{{ $t('Select Category') }}</option>
                                            <option value="quotation">{{ $t('Quotation') }}</option>
                                            <option value="proforma_invoice">{{ $t('Proforma Invoice') }}</option>
                                            <option value="invoice">{{ $t('Invoice') }}</option>
                                            <option value="general">{{ $t('General') }}</option>
                                            <option value="other">{{ $t('Other') }}</option>
                                        </select>
                                        <p v-if="form.errors.category" class="text-xs text-red-600 mt-1">
                                            {{ form.errors.category }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms List -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" class="text-[#ff5100]">
                                            <path fill="currentColor"
                                                d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1s-1-.45-1-1s.45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
                                        </svg>
                                        <h3 class="text-sm font-semibold text-slate-900">
                                            {{ $t('Terms & Conditions') }}
                                            <span class="text-red-500">*</span>
                                        </h3>
                                    </div>
                                    <button type="button" @click="addTerm"
                                        class="px-4 py-2 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor" d="M19 12.998h-6v6h-2v-6H5v-2h6v-6h2v6h6z" />
                                        </svg>
                                        {{ $t('Add Term') }}
                                    </button>
                                </div>

                                <div v-if="form.terms.length === 0"
                                    class="text-center py-8 bg-slate-50 rounded-xl border-2 border-dashed border-slate-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-slate-400 mb-2"
                                        viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1s-1-.45-1-1s.45-1 1-1z" />
                                    </svg>
                                    <p class="text-sm text-slate-600 font-medium">{{ $t('No terms added yet') }}</p>
                                    <p class="text-xs text-slate-500 mt-1">{{ $t('Click "Add Term" to get started') }}</p>
                                </div>

                                <TransitionGroup name="term-list" tag="div" class="space-y-4">
                                    <div v-for="(term, index) in form.terms" :key="term.id"
                                        class="bg-slate-50 rounded-xl p-4 border border-slate-200 hover:border-[#ff5100] transition-all duration-200">
                                        <div class="flex items-start gap-3">
                                            <div
                                                class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-[#ff5100] to-[#ff7340] text-white text-sm font-bold rounded-full flex items-center justify-center shadow-md">
                                                {{ index + 1 }}
                                            </div>
                                            <div class="flex-1 space-y-3">
                                                <FormInput v-model="term.title" :name="$t('Title')"
                                                    :error="form.errors[`terms.${index}.title`]"
                                                    :placeholder="$t('e.g., Payment Terms')" :required="true" />
                                                <div class="space-y-2">
                                                    <label class="text-sm font-semibold text-slate-900">
                                                        {{ $t('Description') }}
                                                        <span class="text-red-500">*</span>
                                                    </label>
                                                    <textarea v-model="term.description" rows="3"
                                                        :placeholder="$t('Enter detailed description...')"
                                                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200 resize-none"></textarea>
                                                    <p v-if="form.errors[`terms.${index}.description`]"
                                                        class="text-xs text-red-600">
                                                        {{ form.errors[`terms.${index}.description`] }}
                                                    </p>
                                                </div>
                                            </div>
                                            <button type="button" @click="removeTerm(index)"
                                                class="flex-shrink-0 p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24">
                                                    <path fill="currentColor"
                                                        d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </TransitionGroup>

                                <p v-if="form.errors.terms" class="text-xs text-red-600 mt-2">
                                    {{ form.errors.terms }}
                                </p>
                            </div>

                            <!-- Settings -->
                            <div class="space-y-4">
                                <div class="flex items-center gap-2 pb-2 border-b border-slate-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" class="text-[#ff5100]">
                                        <path fill="currentColor"
                                            d="M12 15.5A3.5 3.5 0 0 1 8.5 12A3.5 3.5 0 0 1 12 8.5a3.5 3.5 0 0 1 3.5 3.5a3.5 3.5 0 0 1-3.5 3.5m7.43-2.53c.04-.32.07-.64.07-.97c0-.33-.03-.66-.07-1l2.11-1.63c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.31-.61-.22l-2.49 1c-.52-.39-1.06-.73-1.69-.98l-.37-2.65A.506.506 0 0 0 14 2h-4c-.25 0-.46.18-.5.42l-.37 2.65c-.63.25-1.17.59-1.69.98l-2.49-1c-.22-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64L4.57 11c-.04.34-.07.67-.07 1c0 .33.03.65.07.97l-2.11 1.66c-.19.15-.25.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1.01c.52.4 1.06.74 1.69.99l.37 2.65c.04.24.25.42.5.42h4c.25 0 .46-.18.5-.42l.37-2.65c.63-.26 1.17-.59 1.69-.99l2.49 1.01c.22.08.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.66Z" />
                                    </svg>
                                    <h3 class="text-sm font-semibold text-slate-900">{{ $t('Settings') }}</h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-slate-900">
                                            {{ $t('Status') }}
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <select v-model="form.status"
                                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5100] focus:border-transparent transition-all duration-200">
                                            <option value="active">{{ $t('Active') }}</option>
                                            <option value="inactive">{{ $t('Inactive') }}</option>
                                        </select>
                                        <p v-if="form.errors.status" class="text-xs text-red-600 mt-1">
                                            {{ form.errors.status }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-slate-900">
                                            {{ $t('Primary for Category') }}
                                        </label>
                                        <div class="flex items-center h-[50px]">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" v-model="form.is_primary" class="sr-only peer">
                                                <div
                                                    class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#ff5100]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-[#ff5100] peer-checked:to-[#ff7340]">
                                                </div>
                                                <span class="ml-3 text-sm text-slate-600">
                                                    {{ form.is_primary ? $t('Yes') : $t('No') }}
                                                </span>
                                            </label>
                                        </div>
                                        <p class="text-xs text-slate-500">
                                            {{ $t('Set as default for this category') }}
                                        </p>
                                    </div>
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
                                        {{ isEdit ? $t('Update') : $t('Create') }}
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
    termsConditions: Object,
    isEdit: Boolean
});

const emit = defineEmits(['close', 'success']);

const isSubmitting = ref(false);
let termIdCounter = 0;

const form = reactive({
    name: '',
    category: '',
    terms: [],
    is_primary: false,
    status: 'active',
    errors: {}
});

// Initialize form with existing data if editing
if (props.isEdit && props.termsConditions) {
    form.name = props.termsConditions.name;
    form.category = props.termsConditions.category;
    form.terms = props.termsConditions.terms.map(term => ({
        id: termIdCounter++,
        title: term.title,
        description: term.description
    }));
    form.is_primary = props.termsConditions.is_primary;
    form.status = props.termsConditions.status;
}

const addTerm = () => {
    form.terms.push({
        id: termIdCounter++,
        title: '',
        description: ''
    });
};

const removeTerm = (index) => {
    form.terms.splice(index, 1);
};

const submitForm = async () => {
    isSubmitting.value = true;
    form.errors = {};

    try {
        const data = {
            name: form.name,
            category: form.category,
            terms: form.terms.map(({ title, description }) => ({ title, description })),
            is_primary: form.is_primary,
            status: form.status
        };

        if (props.isEdit) {
            await axios.put(`${base_url}/terms-conditions/${props.termsConditions.id}`, data);
        } else {
            await axios.post(`${base_url}/terms-conditions`, data);
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

.term-list-enter-active,
.term-list-leave-active {
    transition: all 0.3s ease;
}

.term-list-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}

.term-list-leave-to {
    opacity: 0;
    transform: translateY(10px);
}

.term-list-move {
    transition: transform 0.3s ease;
}
</style>