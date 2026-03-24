<!-- <template>
    <AppLayout>
        <div class="bg-white md:bg-inherit pt-0 px-4 md:pt-8 md:p-8 rounded-[5px] text-[#000] overflow-y-scroll">
            <div class="md:flex justify-between hidden">
                <div>
                    <h1 class="text-xl mb-1">{{ $t('Create ticket') }}</h1>
                    <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                        </svg>
                        <span class="ml-1 mt-1">{{ $t('Have an issue? Describe it in detail below') }}</span>
                    </p>
                </div>
                <div>
                    <Link href="/admin/support"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-300 flex items-center space-x-2">
                    <ArrowLeft class="w-4 h-4" />
                    <span>{{ $t('Back') }}</span>
                    </Link>
                </div>
            </div>
            <form @submit.prevent="submitForm()" class="bg-white py-5 px-5 rounded-[0.5rem] md:w-2/3">
                <div class="mt-5 mb-5 grid md:grid-cols-2 gap-x-8 gap-y-8">
                    <FormInput v-model="form.subject" :name="$t('Subject')" :type="'text'" :error="form.errors.subject"
                        :class="'col-span-2'" :labelClass="'mb-0'" />
                    <FormSelect v-model="form.category" :name="$t('Category')" :options="categoryOptions"
                        :error="form.errors.category" :class="'col-span-2 md:col-span-1'"
                        :placeholder="'Select Category'" />
                    <FormSelectCombo v-model="user" :name="$t('User')" :loadOptions="loadUsers"
                        :error="form.errors.user" :class="'col-span-2 md:col-span-1'"
                        :placeholder="'Enter user email'" />
                    <FormTextArea v-model="form.message" :name="$t('Description')" :type="'text'" :showLabel="true"
                        :error="form.errors.message" :textAreaRows="5" :class="'col-span-2 mb-10'" />
                </div>
                <div class="flex justify-end py-2">
                    <button type="submit"
                        class="self-end flex items-center space-x-4 rounded-md bg-black px-3 py-2 text-sm text-white shadow-sm hover:bg-slate-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        {{ $t('Create ticket') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from './../Layout/App.vue';
import { Link, useForm } from "@inertiajs/vue3";
import { ref, onMounted, watch } from 'vue';
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import FormSelectCombo from '@/Components/FormSelectCombo.vue';
import FormTextArea from '@/Components/FormTextArea.vue';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps(['title', 'categories']);
const user = ref(null);
const form = useForm({
    'subject': null,
    'category': null,
    'message': null,
    'user': null,
});

const categoryOptions = ref([]);
const organizationOptions = ref([]);

function loadUsers(query, setOptions) {
    fetch("/admin/users?search=" + query, {
        headers: {
            'Accept': 'application/json' // Ensure that the request accepts JSON
        }
    })
        .then(response => response.json())
        .then(result => {
            setOptions(result.rows);
        })
        .catch(error => {
            console.error("Error fetching users:", error);
        });
}

const transformCategories = (categories) => {
    return categories.map((category) => ({
        value: category.id,
        label: category.name,
    }));
};

const submitForm = () => {
    form.post('/admin/support');
}

watch(user, (newValue) => {
    form.user = newValue?.value;
});

onMounted(() => {
    categoryOptions.value = transformCategories(props.categories);
});
</script> -->


<!-- ============================================ NEW UI ============================================= -->

<template>
    <AppLayout>
        <div
            class="min-h-screen bg-gradient-to-br from-slate-50 via-orange-50/30 to-slate-50 pt-6 px-4 md:pt-8 md:px-8 pb-12">
            <div class="max-w-5xl mx-auto">
                <!-- Animated Background Elements -->
                <div class="absolute top-20 right-10 w-64 h-64 bg-[#ff5100]/5 rounded-full blur-3xl -z-10"></div>
                <div class="absolute bottom-20 left-10 w-80 h-80 bg-[#ff5100]/3 rounded-full blur-3xl -z-10"></div>

                <!-- Header Section -->
                <div class="mb-8">
                    <!-- Back Button - Mobile -->
                    <div class="md:hidden mb-6">
                        <Link href="/admin/support"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/80 backdrop-blur-sm hover:bg-white text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-200 shadow-sm hover:shadow-md">
                        <ArrowLeft class="w-4 h-4" />
                        <span>{{ $t('Back') }}</span>
                        </Link>
                    </div>

                    <!-- Header Card -->
                    <div
                        class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-orange-100/50 p-6 md:p-8 relative overflow-hidden">
                        <!-- Decorative Pattern -->
                        <div class="absolute top-0 right-0 w-48 h-48 opacity-5">
                            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                <path fill="#ff5100"
                                    d="M47.1,-57.1C59.9,-45.8,68.4,-28.9,71.1,-10.7C73.8,7.5,70.7,27,60.4,42.2C50.1,57.4,32.6,68.3,13.5,72.8C-5.6,77.3,-26.3,75.4,-42.8,66.1C-59.3,56.8,-71.6,40.1,-75.1,21.8C-78.6,3.5,-73.3,-16.4,-64.3,-32.5C-55.3,-48.6,-42.6,-60.9,-27.8,-71.3C-13,-81.7,3.9,-90.2,20.3,-87.8C36.7,-85.4,52.6,-72.1,47.1,-57.1Z"
                                    transform="translate(100 100)" />
                            </svg>
                        </div>

                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 relative z-10">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <div
                                        class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7a3d] rounded-2xl shadow-lg shadow-orange-500/25">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                                            viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                            <polyline points="10 9 9 9 8 9"></polyline>
                                        </svg>
                                    </div>
                                    <div>
                                        <h1
                                            class="text-3xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                                            {{ $t('Create Ticket') }}
                                        </h1>
                                    </div>
                                </div>
                                <div class="flex items-start gap-2 text-slate-600 ml-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="mt-0.5 flex-shrink-0">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                    </svg>
                                    <p class="text-sm md:text-base leading-relaxed">
                                        {{ $t('Have an issue? Describe it in detail below') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Back Button - Desktop -->
                            <div class="hidden md:block flex-shrink-0">
                                <Link href="/admin/support"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-white hover:bg-slate-50 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-200 shadow-sm hover:shadow-md">
                                <ArrowLeft class="w-4 h-4" />
                                <span>{{ $t('Back') }}</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Section -->
                <form @submit.prevent="submitForm()"
                    class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-orange-100/50 p-6 md:p-10 relative overflow-hidden">
                    <!-- Decorative Corner Accent -->
                    <div
                        class="absolute top-0 left-0 w-32 h-32 bg-gradient-to-br from-[#ff5100]/10 to-transparent rounded-br-[100px]">
                    </div>

                    <!-- Progress Indicator -->
                    <div class="mb-8 relative z-10">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-slate-600 uppercase tracking-wider">{{ $t(`Ticket
                                Information`) }}</span>
                            <span class="text-xs text-slate-500">{{ $t('Step 1 of 1') }}</span>
                        </div>
                        <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div
                                class="h-full bg-gradient-to-r from-[#ff5100] to-[#ff7a3d] rounded-full w-full transition-all duration-500 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Form Grid -->
                    <div class="space-y-6 relative z-10">
                        <!-- Subject Field -->
                        <div class="group">
                            <label class="text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="text-[#ff5100]">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                                {{ $t('Subject') }}
                                <span class="text-[#ff5100]">*</span>
                            </label>
                            <div class="relative">
                                <input v-model="form.subject" type="text" :placeholder="$t('Enter ticket subject')"
                                    class="w-full px-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-[#ff5100] focus:bg-white focus:ring-4 focus:ring-[#ff5100]/10 transition-all duration-200 outline-none text-slate-700 placeholder:text-slate-400"
                                    :class="{ 'border-red-300 bg-red-50': form.errors.subject }" />
                                <div v-if="form.errors.subject"
                                    class="absolute -bottom-6 left-0 flex items-center gap-1 text-red-600 text-xs font-medium">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                    </svg>
                                    {{ form.errors.subject }}
                                </div>
                            </div>
                        </div>

                        <!-- Category & User Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                            <!-- Category Field -->
                            <div class="group">
                                <label class="text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="text-[#ff5100]">
                                        <path
                                            d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z">
                                        </path>
                                    </svg>
                                    {{ $t('Category') }}
                                    <span class="text-[#ff5100]">*</span>
                                </label>
                                <FormSelect v-model="form.category" :name="''" :options="categoryOptions"
                                    :error="form.errors.category" :placeholder="'Select Category'"
                                    :class="'custom-select'" />
                            </div>

                            <!-- User Field -->
                            <div class="group">
                                <label class="text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="text-[#ff5100]">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    {{ $t('User') }}
                                    <span class="text-[#ff5100]">*</span>
                                </label>
                                <FormSelectCombo v-model="user" :name="''" :loadOptions="loadUsers"
                                    :error="form.errors.user" :placeholder="'Enter user email'"
                                    :class="'custom-combo'" />
                            </div>
                        </div>

                        <!-- Description Field -->
                        <div class="group mt-8">
                            <label class="text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="text-[#ff5100]">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                                {{ $t('Description') }}
                                <span class="text-[#ff5100]">*</span>
                            </label>
                            <FormTextArea v-model="form.message" :name="''" :type="'text'" :showLabel="false"
                                :error="form.errors.message" :textAreaRows="5" :class="'custom-textarea'" />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end pt-6 mt-4 border-t border-slate-200">
                            <button type="submit" :disabled="isLoading"
                                class="group relative inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-[#ff5100] to-[#ff7a3d] hover:from-[#ff5100] hover:to-[#ff5100] text-white font-semibold rounded-2xl hover:shadow-lg hover:shadow-orange-500/30 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 overflow-hidden">

                                <svg v-if="!isLoading" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                    stroke-linecap="round" stroke-linejoin="round" class="relative z-10">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                </svg>

                                <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" class="relative z-10 animate-spin">
                                    <path fill="currentColor"
                                        d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                        opacity=".5" />
                                    <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                        <animateTransform attributeName="transform" dur="1s" from="0 12 12"
                                            repeatCount="indefinite" to="360 12 12" type="rotate" />
                                    </path>
                                </svg>

                                <span class="relative z-10">
                                    {{ isLoading ? $t('Creating...') : $t('Create Ticket') }}
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Help Text -->
                    <div
                        class="mt-8 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100 relative z-10">
                        <div class="flex gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="text-blue-600 flex-shrink-0 mt-0.5">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                            <div>
                                <h4 class="font-semibold text-blue-900 text-sm mb-1">{{ $t('Admin Tip') }}</h4>
                                <p class="text-xs text-blue-700 leading-relaxed">
                                    {{ $t(`Create tickets on behalf of users. Select the user and provide detailed
                                    information to help track and resolve issues efficiently.`) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from './../Layout/App.vue';
import { Link, useForm } from "@inertiajs/vue3";
import { ref, onMounted, watch } from 'vue';
import FormSelect from '@/Components/FormSelect.vue';
import FormSelectCombo from '@/Components/FormSelectCombo.vue';
import FormTextArea from '@/Components/FormTextArea.vue';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps(['title', 'categories']);
const user = ref(null);
const form = useForm({
    'subject': null,
    'category': null,
    'message': null,
    'user': null,
});

const categoryOptions = ref([]);
const isLoading = ref(false);

function loadUsers(query, setOptions) {
    fetch("/admin/users?search=" + query, {
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(result => {
            setOptions(result.rows);
        })
        .catch(error => {
            console.error("Error fetching users:", error);
        });
}

const transformCategories = (categories) => {
    return categories.map((category) => ({
        value: category.id,
        label: category.name,
    }));
};

const submitForm = () => {
    isLoading.value = true;
    form.post('/admin/support', {
        onFinish: () => {
            isLoading.value = false;
        }
    });
}

watch(user, (newValue) => {
    form.user = newValue?.value;
});

onMounted(() => {
    categoryOptions.value = transformCategories(props.categories);
});
</script>

<style scoped>
/* Custom animations */
@keyframes float {

    0%,
    100% {
        transform: translateY(0px);
    }

    50% {
        transform: translateY(-10px);
    }
}

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

/* Smooth transitions */
* {
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Input focus animation */
input:focus,
select:focus,
textarea:focus {
    transform: translateY(-1px);
}

/* Custom styles for FormSelect to match design */
:deep(.custom-select .c-style) {
    width: 100%;
    padding: 0.875rem 1rem;
    background-color: rgb(248 250 252);
    border: 2px solid rgb(226 232 240);
    border-radius: 0.75rem;
    outline: none;
    color: rgb(51 65 85);
    transition: all 0.2s;
    appearance: none;
    cursor: pointer;
}

:deep(.custom-select select:focus) {
    border-color: #ff5100;
    background-color: white;
    box-shadow: 0 0 0 4px rgba(255, 81, 0, 0.1);
    transform: translateY(-1px);
}

:deep(.custom-select .border-red-300) {
    border-color: rgb(252 165 165);
    background-color: rgb(254 242 242);
}

/* Custom styles for FormSelectCombo */
:deep(.custom-combo input) {
    width: 100%;
    padding: 0.875rem 1rem;
    background-color: rgb(248 250 252);
    border: 2px solid rgb(226 232 240);
    border-radius: 0.5rem;
    outline: none;
    color: rgb(51 65 85);
    transition: all 0.2s;
}

:deep(.custom-combo input:focus) {
    border-color: #ff5100;
    background-color: white;
    box-shadow: 0 0 0 4px rgba(255, 81, 0, 0.1);
    transform: translateY(-1px);
}

/* Custom styles for FormTextArea */
:deep(.custom-textarea textarea) {
    width: 100%;
    padding: 0.875rem 1rem;
    background-color: rgb(248 250 252);
    border: 2px solid rgb(226 232 240);
    border-radius: 0.75rem;
    outline: none;
    color: rgb(51 65 85);
    transition: all 0.2s;
    resize: none;
}

:deep(.custom-textarea textarea:focus) {
    border-color: #ff5100;
    background-color: white;
    box-shadow: 0 0 0 4px rgba(255, 81, 0, 0.1);
    transform: translateY(-1px);
}

:deep(.custom-textarea .border-red-300) {
    border-color: rgb(252 165 165);
    background-color: rgb(254 242 242);
}

/* Custom scrollbar for textarea */
:deep(textarea::-webkit-scrollbar) {
    width: 8px;
}

:deep(textarea::-webkit-scrollbar-track) {
    background: #f1f5f9;
    border-radius: 10px;
}

:deep(textarea::-webkit-scrollbar-thumb) {
    background: #cbd5e1;
    border-radius: 10px;
}

:deep(textarea::-webkit-scrollbar-thumb:hover) {
    background: #ff5100;
}

/* Hide labels from form components since we're showing them custom */
:deep(.custom-select label),
:deep(.custom-combo label),
:deep(.custom-textarea label) {
    display: none;
}
</style>