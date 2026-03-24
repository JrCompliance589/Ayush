<!-- <template>
    <SettingLayout :modules="props.modules">
        <div class="md:h-[90vh]">
            <div class="flex justify-center items-center">
                <div class="md:w-[60em]">
                    <div class="bg-white border border-slate-200 rounded-lg pt-2 text-sm px-4 mb-20">
                        <div class="w-full py-2 mb-4 mt-2">
                            <div class="flex w-full">
                                <div class="text-md">
                                    <h4 class="text-[16px]">{{ $t('Enable ticketing') }}</h4>
                                    <div class="mb-1 text-slate-500">{{ $t(`Activate ticketing workflow in your
                                        conversations`) }}</div>
                                </div>
                                <div class="ml-auto">
                                    <div class="w-12 h-6 flex items-center bg-gray-300 rounded-full p-1"
                                        :class="{ 'bg-primary': form.active }" @click="toggleState1(active)">
                                        <div class="bg-white w-4 h-4 rounded-full shadow-md transform duration-300 ease-in-out"
                                            :class="{ 'translate-x-6': form.active }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="form.active"
                        class="bg-white border border-slate-200 rounded-lg py-2 text-sm pb-4 px-4 mb-20">
                        <div class="w-full py-2 mb-2 mt-2">
                            <div class="flex w-full mb-4">
                                <div class="text-md">
                                    <h4 class="text-[16px]">{{ $t('Auto assignment') }}</h4>
                                    <span class="flex items-center mt-1 text-slate-500">
                                        {{ $t(`Use auto-assignment rules to evenly distribute chats among agents
                                        automatically.`) }}
                                    </span>
                                </div>
                            </div>
                            <div class="w-5/5">
                                <div class="grid grid-cols-2 gap-x-4">
                                    <div class="border rounded-xl p-4">
                                        <div class="flex space-x-2">
                                            <div>
                                                <div class="flex mt-[1px]">
                                                    <label @click="toggleAutoAssignment(false)" for="myCheckbox"
                                                        class="cursor-pointer">
                                                        <div class="w-4 h-4 border border-gray-400 rounded-md flex items-center justify-center"
                                                            :class="form.auto_assignment === false ? 'bg-[#000]' : ''">
                                                            <svg v-if="form.auto_assignment === false" class="w-4 h-4"
                                                                :class="form.auto_assignment === false ? 'text-white' : ''"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div @click="toggleAutoAssignment(false)" class="cursor-pointer">
                                                <div>{{ $t('Off') }}</div>
                                                <div>{{ $t(`Team members pick conversations manually from Unassigned
                                                    folder.`) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="border rounded-xl p-4">
                                        <div class="flex space-x-2">
                                            <div>
                                                <div class="flex mt-[1px]">
                                                    <label @click="toggleAutoAssignment(true)" for="myCheckbox"
                                                        class="cursor-pointer">
                                                        <div class="w-4 h-4 border border-gray-400 rounded-md flex items-center justify-center"
                                                            :class="form.auto_assignment === true ? 'bg-[#000]' : ''">
                                                            <svg v-if="form.auto_assignment === true" class="w-4 h-4"
                                                                :class="form.auto_assignment === true ? 'text-white' : ''"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div @click="toggleAutoAssignment(true)" class="cursor-pointer">
                                                <div>{{ $t('Auto') }}</div>
                                                <div>{{ $t(`Distribute conversations among all your available team
                                                    members.`) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="form.active" class="bg-white border border-slate-200 rounded-lg pt-2 text-sm px-4 mb-20">
                        <div class="w-full py-2 mb-4 mt-2">
                            <div class="flex w-full">
                                <div class="w-3/4 text-md">
                                    <h4 class="text-[16px]">{{ $t('Reassign chats that have been reopened') }}</h4>
                                    <div class="mb-1 text-slate-500">{{ $t(`Enable this option to reassign chats when a
                                        contact re - opens a closed conversation.If disabled, reopened chats will either
                                        return to the previous agent or remain unassigned, based on auto - assignment
                                        settings.`) }}</div>
                                </div>
                                <div class="w-1/4">
                                    <div class="ml-auto w-12 h-6 flex items-center bg-gray-300 rounded-full p-1"
                                        :class="{ 'bg-primary': form.reassign_reopened_chats }" @click="toggleState2()">
                                        <div class="bg-white w-4 h-4 rounded-full shadow-md transform duration-300 ease-in-out"
                                            :class="{ 'translate-x-6': form.reassign_reopened_chats }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="form.active" class="bg-white border border-slate-200 rounded-lg pt-2 text-sm px-4 mb-20">
                        <div class="w-full py-2 mb-4 mt-2">
                            <div class="flex w-full">
                                <div class="w-3/4 text-md">
                                    <h4 class="text-[16px]">{{ $t(`Grant agents access to view all chats not assigned to
                                        them`) }}</h4>
                                    <div class="mb-1 text-slate-500">{{ $t(`Disable this option, if you want live chat
                                        agents to have access only to new conversations and conversations that are
                                        assigned to them.`) }}</div>
                                </div>
                                <div class="w-1/4">
                                    <div class="ml-auto w-12 h-6 flex items-center bg-gray-300 rounded-full p-1"
                                        :class="{ 'bg-primary': form.allow_agents_to_view_all_chats }"
                                        @click="toggleState3()">
                                        <div class="bg-white w-4 h-4 rounded-full shadow-md transform duration-300 ease-in-out"
                                            :class="{ 'translate-x-6': form.allow_agents_to_view_all_chats }"></div>
                                    </div>
                                </div>
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
import { ref, watch } from 'vue';
import { useForm } from "@inertiajs/vue3";
import { trans } from 'laravel-vue-i18n';

const props = defineProps(['rows', 'filters', 'settings', 'modules']);
const config = ref(props.settings.metadata);
const settings = ref(config.value ? JSON.parse(config.value) : null);

const form = useForm({
    active: settings.value?.tickets?.active ?? false,
    auto_assignment: settings.value?.tickets?.auto_assignment ?? false,
    reassign_reopened_chats: settings.value?.tickets?.reassign_reopened_chats ?? false,
    allow_agents_to_view_all_chats: settings.value?.tickets?.allow_agents_to_view_all_chats ?? false,
});

const toggleState1 = () => {
    form.active = !form.active;
    submitForm();
}

const toggleState2 = () => {
    form.reassign_reopened_chats = !form.reassign_reopened_chats;
    submitForm();
}

const toggleState3 = () => {
    form.allow_agents_to_view_all_chats = !form.allow_agents_to_view_all_chats;
    submitForm();
}

const toggleAutoAssignment = (el) => {
    form.auto_assignment = el;
    submitForm();
}

const submitForm = async () => {
    form.post('/settings/tickets', {
        preserveScroll: true,
    })
};
</script> -->


<!-- ============================================ NEW UI CODE ============================================= -->


<template>
    <SettingLayout :modules="props.modules">
        <div class="space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <div class="w-full mx-auto space-y-6">

                <!-- Enable Ticketing Card -->
                <div
                    class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between gap-6">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="p-3 bg-gradient-to-br from-[#ff5100]/10 to-[#ff7340]/10 rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="currentColor"
                                            d="M4 4a2 2 0 0 0-2 2v4a2 2 0 0 1 2 2a2 2 0 0 1-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 1-2-2a2 2 0 0 1 2-2V6a2 2 0 0 0-2-2zm0 2h16v2.54c-1.24.71-2 2.03-2 3.46s.76 2.75 2 3.46V18H4v-2.54c1.24-.71 2-2.03 2-3.46s-.76-2.75-2-3.46z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-base font-bold text-slate-900 mb-1">{{ $t('Enable ticketing') }}
                                    </h4>
                                    <p class="text-sm text-slate-600">{{ $t(`Activate ticketing workflow in your
                                        conversations`) }}</p>
                                </div>
                            </div>
                            <div @click="toggleState1()"
                                class="relative w-14 h-7 flex items-center rounded-full p-1 cursor-pointer transition-colors duration-300"
                                :class="form.active ? 'bg-gradient-to-r from-[#ff5100] to-[#ff7340] shadow-lg shadow-[#ff5100]/30' : 'bg-gray-300'">
                                <div class="bg-white w-5 h-5 rounded-full shadow-md transform transition-transform duration-300"
                                    :class="{ 'translate-x-7': form.active }"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Auto Assignment Card (shown when ticketing is enabled) -->
                <transition name="slide-fade">
                    <div v-if="form.active"
                        class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                            <div class="flex items-center gap-3">
                                <div
                                    class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-white">
                                        <path fill="currentColor"
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2m0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8s8 3.58 8 8s-3.58 8-8 8m4.59-12.42L10 14.17l-2.59-2.58L6 13l4 4l8-8z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-slate-900">{{ $t('Auto assignment') }}</h2>
                                    <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                        {{ $t(`Use auto-assignment rules to evenly distribute chats among agents
                                        automatically.`) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Off Option -->
                                <div @click="toggleAutoAssignment(false)"
                                    class="group relative p-6 rounded-2xl border-2 cursor-pointer transition-all duration-300"
                                    :class="form.auto_assignment === false
                                        ? 'border-[#ff5100] bg-gradient-to-br from-[#ff5100]/5 to-orange-50/50 shadow-lg'
                                        : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-md'">
                                    <div class="flex items-start gap-4">
                                        <!-- <div class="flex items-center justify-center w-6 h-6 rounded-full border-2 transition-all"
                                            :class="form.auto_assignment === false
                                                ? 'border-[#ff5100] bg-[#ff5100]'
                                                : 'border-slate-300 bg-white group-hover:border-slate-400'">
                                            <svg v-if="form.auto_assignment === false"
                                                xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" class="text-white">
                                                <path fill="currentColor"
                                                    d="m9.55 18l-5.7-5.7l1.425-1.425L9.55 15.15l9.175-9.175L20.15 7.4z" />
                                            </svg>
                                        </div> -->
                                        <div class="flex-1">
                                            <h3 class="font-bold text-slate-900 mb-2 flex items-center gap-2">
                                                {{ $t('Off') }}
                                                <span v-if="form.auto_assignment === false"
                                                    class="px-2 py-0.5 bg-[#ff5100] text-white text-xs font-medium rounded-full">
                                                    Active
                                                </span>
                                            </h3>
                                            <p class="text-sm text-slate-600">{{ $t(`Team members pick conversations
                                                manually from Unassigned folder.`) }}</p>
                                        </div>
                                    </div>
                                    <div v-if="form.auto_assignment === false" class="absolute top-4 right-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" class="text-[#ff5100]">
                                            <path fill="currentColor"
                                                d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8s8 3.59 8 8s-3.59 8-8 8m4.59-12.42L10 14.17l-2.59-2.58L6 13l4 4l8-8z" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Auto Option -->
                                <div @click="toggleAutoAssignment(true)"
                                    class="group relative p-6 rounded-2xl border-2 cursor-pointer transition-all duration-300"
                                    :class="form.auto_assignment === true
                                        ? 'border-[#ff5100] bg-gradient-to-br from-[#ff5100]/5 to-orange-50/50 shadow-lg'
                                        : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-md'">
                                    <div class="flex items-start gap-4">
                                        <!-- <div class="flex items-center justify-center w-6 h-6 rounded-full border-2 transition-all"
                                            :class="form.auto_assignment === true
                                                ? 'border-[#ff5100] bg-[#ff5100]'
                                                : 'border-slate-300 bg-white group-hover:border-slate-400'">
                                            <svg v-if="form.auto_assignment === true" xmlns="http://www.w3.org/2000/svg"
                                                width="16" height="16" viewBox="0 0 24 24" class="text-white">
                                                <path fill="currentColor"
                                                    d="m9.55 18l-5.7-5.7l1.425-1.425L9.55 15.15l9.175-9.175L20.15 7.4z" />
                                            </svg>
                                        </div> -->
                                        <div class="flex-1">
                                            <h3 class="font-bold text-slate-900 mb-2 flex items-center gap-2">
                                                {{ $t('Auto') }}
                                                <span v-if="form.auto_assignment === true"
                                                    class="px-2 py-0.5 bg-[#ff5100] text-white text-xs font-medium rounded-full">
                                                    Active
                                                </span>
                                            </h3>
                                            <p class="text-sm text-slate-600">{{ $t(`Distribute conversations among all
                                                your available team members.`) }}</p>
                                        </div>
                                    </div>
                                    <div v-if="form.auto_assignment === true" class="absolute top-4 right-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" class="text-[#ff5100]">
                                            <path fill="currentColor"
                                                d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8s8 3.59 8 8s-3.59 8-8 8m4.59-12.42L10 14.17l-2.59-2.58L6 13l4 4l8-8z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>

                <!-- Reassign Reopened Chats Card -->
                <transition name="slide-fade">
                    <div v-if="form.active"
                        class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-6">
                                <div class="flex items-start gap-4 flex-1">
                                    <div class="p-3 bg-gradient-to-br from-blue-500/10 to-blue-600/10 rounded-xl">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" class="text-blue-600">
                                            <path fill="currentColor"
                                                d="M12.079 2.25c-4.794 0-8.734 3.663-9.118 8.333H2a.75.75 0 0 0-.528 1.283l1.68 1.666a.75.75 0 0 0 1.056 0l1.68-1.666a.75.75 0 0 0-.528-1.283h-.893c.38-3.831 3.638-6.833 7.612-6.833a7.658 7.658 0 0 1 6.537 3.643a.75.75 0 1 0 1.277-.786A9.158 9.158 0 0 0 12.08 2.25m8.761 8.217a.75.75 0 0 0-1.054 0L18.1 12.133a.75.75 0 0 0 .527 1.284h.899c-.382 3.83-3.651 6.833-7.644 6.833a7.697 7.697 0 0 1-6.565-3.644a.75.75 0 1 0-1.277.788a9.197 9.197 0 0 0 7.842 4.356c4.808 0 8.765-3.66 9.15-8.333H22a.75.75 0 0 0 .527-1.284z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-base font-bold text-slate-900 mb-2">{{ $t(`Reassign chats that
                                            have been reopened`) }}</h4>
                                        <p class="text-sm text-slate-600">{{ $t(`Enable this option to reassign chats
                                            when a contact re - opens a closed conversation.If disabled, reopened chats
                                            will either return to the previous agent or remain unassigned, based on
                                            auto - assignment settings.`) }}</p>
                                    </div>
                                </div>
                                <div @click="toggleState2()"
                                    class="relative w-14 h-7 flex items-center rounded-full p-1 cursor-pointer transition-colors duration-300 shrink-0"
                                    :class="form.reassign_reopened_chats ? 'bg-gradient-to-r from-[#ff5100] to-[#ff7340] shadow-lg shadow-[#ff5100]/30' : 'bg-gray-300'">
                                    <div class="bg-white w-5 h-5 rounded-full shadow-md transform transition-transform duration-300"
                                        :class="{ 'translate-x-7': form.reassign_reopened_chats }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>

                <!-- Grant Agents Access Card -->
                <transition name="slide-fade">
                    <div v-if="form.active"
                        class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-6">
                                <div class="flex items-start gap-4 flex-1">
                                    <div class="p-3 bg-gradient-to-br from-purple-500/10 to-purple-600/10 rounded-xl">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" class="text-purple-600">
                                            <path fill="currentColor"
                                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4s-4 1.79-4 4s1.79 4 4 4m0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-base font-bold text-slate-900 mb-2">{{ $t(`Grant agents access
                                            to view all chats not assigned to them`) }}</h4>
                                        <p class="text-sm text-slate-600">{{ $t(`Disable this option, if you want live
                                            chat agents to have access only to new conversations and conversations that
                                            are assigned to them.`) }}</p>
                                    </div>
                                </div>
                                <div @click="toggleState3()"
                                    class="relative w-14 h-7 flex items-center rounded-full p-1 cursor-pointer transition-colors duration-300 shrink-0"
                                    :class="form.allow_agents_to_view_all_chats ? 'bg-gradient-to-r from-[#ff5100] to-[#ff7340] shadow-lg shadow-[#ff5100]/30' : 'bg-gray-300'">
                                    <div class="bg-white w-5 h-5 rounded-full shadow-md transform transition-transform duration-300"
                                        :class="{ 'translate-x-7': form.allow_agents_to_view_all_chats }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
        </div>
    </SettingLayout>
</template>

<script setup>
import SettingLayout from "./Layout.vue";
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";

const props = defineProps(['rows', 'filters', 'settings', 'modules']);
const config = ref(props.settings.metadata);
const settings = ref(config.value ? JSON.parse(config.value) : null);

const form = useForm({
    active: settings.value?.tickets?.active ?? false,
    auto_assignment: settings.value?.tickets?.auto_assignment ?? false,
    reassign_reopened_chats: settings.value?.tickets?.reassign_reopened_chats ?? false,
    allow_agents_to_view_all_chats: settings.value?.tickets?.allow_agents_to_view_all_chats ?? false,
});

const toggleState1 = () => {
    form.active = !form.active;
    submitForm();
}

const toggleState2 = () => {
    form.reassign_reopened_chats = !form.reassign_reopened_chats;
    submitForm();
}

const toggleState3 = () => {
    form.allow_agents_to_view_all_chats = !form.allow_agents_to_view_all_chats;
    submitForm();
}

const toggleAutoAssignment = (value) => {
    form.auto_assignment = value;
    submitForm();
}

const submitForm = async () => {
    form.post('/settings/tickets', {
        preserveScroll: true,
    })
};
</script>

<style scoped>
.slide-fade-enter-active {
    transition: all 0.4s ease-out;
}

.slide-fade-leave-active {
    transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    transform: translateY(-20px);
    opacity: 0;
}
</style>