<!-- <template>
    <SettingLayout :modules="props.modules">
        <div class="md:h-[90vh]">
            <div class="flex justify-center items-center">
                <div class="md:w-[60em]">
                    <div class="bg-white border border-slate-200 rounded-lg py-2 text-sm pb-4 px-4 mb-20">
                        <div class="w-full py-2 mb-2 mt-2">
                            <div class="flex w-full mb-4">
                                <div class="text-md">
                                    <h4 class="text-[16px]">{{ $t('Response Sequence Priority') }}</h4>
                                    <span class="flex items-center mt-1 text-slate-500">
                                        {{ $t(`Choose the order in which the system searches for a matching response
                                        when replying to messages.Set the sequence for Basic Replies, Automated Flows,
                                        and AI Assistant to control how replies are prioritized.`) }}
                                    </span>
                                    <div v-if="moduleActive('Flow builder')"
                                        class="text-sm mt-2 bg-orange-100 px-2 py-1 rounded-md w-[fit-content]">
                                        {{ $t(`If a flow is already active for a contact, the response sequence will not
                                        be followed.`) }}
                                    </div>
                                </div>
                            </div>
                            <div>
                                <span class="mb-2 flex items-center text-slate-700">
                                    <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                        viewBox="0 0 24 24">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"></path>
                                    </svg>
                                    Drag to reorder
                                </span>
                                <draggable :list="form.response_sequence" class="mt-2" @end="submitForm()">
                                    <template #item="{ index, element }">
                                        <div
                                            class="flex gap-x-2 bg-slate-50 rounded-md mb-2 p-2 border w-[20em] cursor-pointer">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24">
                                                    <path fill="currentColor"
                                                        d="M9 19.23q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36">
                                                    </path>
                                                </svg>
                                            </span>
                                            <span>{{ element }}</span>
                                        </div>
                                    </template>
                                </draggable>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SettingLayout>
</template>
<script setup>
import SettingLayout from "./Layout.vue";
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import draggable from "vuedraggable";

const props = defineProps(['rows', 'filters', 'settings', 'modules']);
const config = ref(props.settings.metadata);
const settings = ref(config.value ? JSON.parse(config.value) : null);

const moduleActive = (moduleName) => {
    const module = props.modules.find((mod) => mod.name === moduleName);
    return module && module.status === 1;
};

const draggableList = ref([
    "Basic Replies",
    moduleActive('Flow builder') ? "Automated Flows" : null,
    moduleActive('AI Assistant') ? "AI Reply Assistant" : null,
].filter(item => item !== null));

const form = useForm({
    response_sequence: settings.value?.automation?.response_sequence ?? draggableList.value,
});

// Map module names for checking
const moduleNames = {
    'Automated Flows': 'Flow builder',
    'AI Reply Assistant': 'AI Assistant'
};

const removeInactiveModulesFromSequence = () => {
    if (settings.value?.automation?.response_sequence) {
        const updatedSequence = settings.value.automation.response_sequence.filter(module => {
            if (module === 'Basic Replies') {
                return true;
            }
            // Remove the module if it's inactive (for mapped names)
            return moduleActive(moduleNames[module] || module); // Keep only inactive modules
        });

        // Add any module that was previously removed but is now active, at the bottom of the sequence
        Object.keys(moduleNames).forEach(moduleKey => {
            const module = moduleNames[moduleKey];
            if (moduleActive(module) && !updatedSequence.includes(moduleKey)) {

                updatedSequence.push(moduleKey); // Add back the module if it's active and not already in the sequence
            }
        });

        form.response_sequence = updatedSequence;
    }
};

removeInactiveModulesFromSequence();

const submitForm = async () => {
    form.post('/settings/automation', {
        preserveScroll: true,
    })
};
</script> -->


<!-- ============================================ NEW UI CODE ============================================= -->

<template>
    <SettingLayout :modules="props.modules">
        <div class="space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <div class="w-full mx-auto">

                <!-- Response Sequence Priority Card -->
                <div
                    class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300">
                    <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M3 13h2v-2H3v2m0 4h2v-2H3v2m0-8h2V7H3v2m8 16h2V1h-2v24m8-6h2v-2h-2v2m0 4h2v-2h-2v2m0-8h2V7h-2v10z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Response Sequence Priority') }}</h2>
                                <p class="text-sm text-slate-600 mt-0.5">{{ $t(`Choose the order in which the system
                                    searches for a matching response when replying to messages.Set the sequence for
                                    Basic Replies, Automated Flows, and AI Assistant to control how replies are
                                    prioritized.`) }}</p>
                            </div>
                        </div>

                        <!-- Info Alert -->
                        <div v-if="moduleActive('Flow builder')"
                            class="flex items-start gap-2 p-3 bg-amber-100/50 border border-amber-200 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                class="text-amber-700 shrink-0 mt-0.5">
                                <path fill="currentColor" d="M1 21h22L12 2L1 21m12-3h-2v-2h2v2m0-4h-2v-4h2v4z" />
                            </svg>
                            <span class="text-sm text-amber-800 font-medium">{{ $t(`If a flow is already active for a
                                contact, the response sequence will not be followed.`) }}</span>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Drag Instruction -->
                        <div class="flex items-center gap-2 mb-6 p-3 rounded-lg bg-blue-50 border border-blue-100">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                class="text-blue-600 shrink-0">
                                <path fill="currentColor"
                                    d="M9 3c-1.1 0-2 .9-2 2s.9 2 2 2s2-.9 2-2s-.9-2-2-2m0 4c-1.1 0-2 .9-2 2s.9 2 2 2s2-.9 2-2s-.9-2-2-2m0 4c-1.1 0-2 .9-2 2s.9 2 2 2s2-.9 2-2s-.9-2-2-2m6-8c1.1 0 2-.9 2-2s-.9-2-2-2s-2 .9-2 2s.9 2 2 2m0 4c1.1 0 2-.9 2-2s-.9-2-2-2s-2 .9-2 2s.9 2 2 2m0 4c1.1 0 2-.9 2-2s-.9-2-2-2s-2 .9-2 2s.9 2 2 2" />
                            </svg>
                            <span class="text-sm text-blue-800 font-medium">{{ $t('Drag to reorder') }}</span>
                        </div>

                        <!-- Draggable List -->
                        <div class="space-y-3">
                            <draggable v-model="form.response_sequence" @end="submitForm()" class="space-y-3"
                                animation="200" handle=".drag-handle">
                                <template #item="{ element, index }">
                                    <div
                                        class="group flex items-center gap-3 p-4 bg-gradient-to-r from-slate-50 to-slate-100/50 rounded-xl border border-slate-200 hover:border-[#ff5100]/30 hover:shadow-md transition-all duration-300 cursor-pointer">
                                        <!-- Drag Handle -->
                                        <div
                                            class="drag-handle p-2 rounded-lg hover:bg-slate-200 transition-colors cursor-grab active:cursor-grabbing">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" class="text-slate-400 group-hover:text-[#ff5100]">
                                                <path fill="currentColor"
                                                    d="M9 3c-1.1 0-2 .9-2 2s.9 2 2 2s2-.9 2-2s-.9-2-2-2m0 4c-1.1 0-2 .9-2 2s.9 2 2 2s2-.9 2-2s-.9-2-2-2m0 4c-1.1 0-2 .9-2 2s.9 2 2 2s2-.9 2-2s-.9-2-2-2m6-8c1.1 0 2-.9 2-2s-.9-2-2-2s-2 .9-2 2s.9 2 2 2m0 4c1.1 0 2-.9 2-2s-.9-2-2-2s-2 .9-2 2s.9 2 2 2m0 4c1.1 0 2-.9 2-2s-.9-2-2-2s-2 .9-2 2s.9 2 2 2" />
                                            </svg>
                                        </div>

                                        <!-- Sequence Number Badge -->
                                        <div
                                            class="flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white font-bold text-sm ">
                                            {{ index + 1 }}
                                        </div>

                                        <!-- Item Label -->
                                        <div class="flex-1">
                                            <span class="font-semibold text-slate-900">{{ element }}</span>
                                        </div>

                                        <!-- Icon -->
                                        <div class="p-2 rounded-lg bg-white border border-slate-200">

                                            <MessagesSquare size="22" v-if="element === 'Basic Replies'" />

                                            <BotMessageSquare size="22" v-else-if="element === 'Automated Flows'" />
                                            <svg v-else-if="element === 'AI Reply Assistant'"
                                                xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" class="text-slate-600">
                                                <path fill="currentColor"
                                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2m-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                            </svg>
                                        </div>
                                    </div>
                                </template>
                            </draggable>
                        </div>

                        <!-- Info Section -->
                        <div class="mt-8 p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                            <h4 class="font-semibold text-slate-900 text-sm">{{ $t('How it works') }}</h4>
                            <ul class="space-y-2 text-sm text-slate-600">
                                <li class="flex items-start gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="currentColor"
                                            d="m9.55 18l-5.7-5.7l1.425-1.425L9.55 15.15l9.175-9.175L20.15 7.4z" />
                                    </svg>
                                    <span>{{ $t('The system will check responses in the order you arrange them.')
                                        }}</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="currentColor"
                                            d="m9.55 18l-5.7-5.7l1.425-1.425L9.55 15.15l9.175-9.175L20.15 7.4z" />
                                    </svg>
                                    <span>{{ $t(`If a match is found, it will be used; otherwise, the next option is
                                        checked.`) }}</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="currentColor"
                                            d="m9.55 18l-5.7-5.7l1.425-1.425L9.55 15.15l9.175-9.175L20.15 7.4z" />
                                    </svg>
                                    <span>{{ $t(`Place the most frequently used option first for better performance.`)
                                        }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Info Banner -->
                <div class="mt-6 p-6 rounded-2xl bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-100">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-blue-100 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                class="text-blue-600">
                                <path fill="currentColor"
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2m1 15h-2v-2h2v2m0-4h-2V7h2v6z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-blue-900 mb-2">{{ $t('Response Sequence') }}</h4>
                            <p class="text-sm text-blue-800">{{ $t(`The response sequence determines which system checks
                                for matching
                                responses first.Reorder these items to prioritize specific types of responses for your
                                use case.`) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SettingLayout>
</template>

<script setup>
import SettingLayout from "./Layout.vue";
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import draggable from "vuedraggable";
import { BotMessageSquare, MessagesSquare } from "lucide-vue-next";

const props = defineProps(['rows', 'filters', 'settings', 'modules']);
const config = ref(props.settings.metadata);
const settings = ref(config.value ? JSON.parse(config.value) : null);

const moduleActive = (moduleName) => {
    const module = props.modules.find((mod) => mod.name === moduleName);
    return module && module.status === 1;
};

const draggableList = ref([
    "Basic Replies",
    moduleActive('Flow builder') ? "Automated Flows" : null,
    moduleActive('AI Assistant') ? "AI Reply Assistant" : null,
].filter(item => item !== null));

const form = useForm({
    response_sequence: settings.value?.automation?.response_sequence ?? draggableList.value,
});

// Map module names for checking
const moduleNames = {
    'Automated Flows': 'Flow builder',
    'AI Reply Assistant': 'AI Assistant'
};

const removeInactiveModulesFromSequence = () => {
    if (settings.value?.automation?.response_sequence) {
        const updatedSequence = settings.value.automation.response_sequence.filter(module => {
            if (module === 'Basic Replies') {
                return true;
            }
            // Remove the module if it's inactive (for mapped names)
            return moduleActive(moduleNames[module] || module); // Keep only inactive modules
        });

        // Add any module that was previously removed but is now active, at the bottom of the sequence
        Object.keys(moduleNames).forEach(moduleKey => {
            const module = moduleNames[moduleKey];
            if (moduleActive(module) && !updatedSequence.includes(moduleKey)) {

                updatedSequence.push(moduleKey); // Add back the module if it's active and not already in the sequence
            }
        });

        form.response_sequence = updatedSequence;
    }
};

removeInactiveModulesFromSequence();

const submitForm = async () => {
    form.post('/settings/automation', {
        preserveScroll: true,
    })
};
</script>

<style scoped>
.sortable-ghost {
    opacity: 0.5;
    background-color: #f3f4f6;
}

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
