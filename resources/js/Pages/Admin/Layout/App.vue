<!-- <template>
    <div :class="rtlClass">
        <MobileSidebar :user="user" :config="config" :title="currentPageTitle" :displayCreateBtn="displayCreateBtn">
        </MobileSidebar>

        <div class="pt-10 mt-4 md:mt-0 md:pt-0 flex md:h-screen w-full tracking-[0.3px] bg-gray-300/10">
            <Sidebar :user="user" :config="config"></Sidebar>
            <div class="md:min-h-screen flex flex-col w-full min-w-0">
                <slot :user="user"></slot>
            </div>
        </div>
    </div>
</template>
<script setup>
import { usePage } from "@inertiajs/vue3";
import Sidebar from "./Sidebar.vue";
import MobileSidebar from "./MobileSidebar.vue";
import { computed, watch } from 'vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import { useRtl } from '../../../Composables/useRtl';

const { rtlClass } = useRtl();

const user = computed(() => usePage().props.auth.user);
const config = computed(() => usePage().props.config);
const currentPageTitle = computed(() => usePage().props.title);
const displayCreateBtn = computed(() => usePage().props.allowCreate);

watch(() => [usePage().props.flash, { deep: true }], () => {
    if (usePage().props.flash.status != null) {
        toast(usePage().props.flash.status.message, {
            autoClose: 3000,
        });
    }
});
</script> -->

<!-- ============================================ NEW UI CODE ============================================= -->
<script>
export const description
    = "A sidebar that collapses to icons."
export const iframeHeight = "800px"
export const containerClass = "w-full h-full"
</script>

<script setup>
import { usePage } from "@inertiajs/vue3";
import { computed, watch } from 'vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import { useRtl } from '../../../Composables/useRtl';
import { SidebarInset, SidebarProvider, SidebarTrigger } from "../../../Components/ui/sidebar";
import AdminAppSidebar from "../../../Components/AdminAppSidebar.vue";
import LangToggle from "../../../Components/LangToggle.vue";
import { Separator } from "../../../Components/ui/separator";

const { rtlClass } = useRtl();

const user = computed(() => usePage().props.auth.user);
const config = computed(() => usePage().props.config);
const languages = computed(() => usePage().props.languages);
const currentLanguage = computed(() => usePage().props.currentLanguage);

watch(() => [usePage().props.flash, { deep: true }], () => {
    if (usePage().props.flash.status != null) {
        toast(usePage().props.flash.status.message, {
            autoClose: 3000,
        });
    }
});
</script>

<template>
    <SidebarProvider :class="rtlClass">
        <AdminAppSidebar :user="user" :config="config" />
        <SidebarInset>
            <header
                class="w-full flex h-16 shrink-0 justify-between items-center gap-2 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12">
                <div class="flex items-center gap-2 px-4">
                    <SidebarTrigger class="-ml-1" />
                </div>
                <div class="mr-8 flex items-center gap-4 bg-slate-100 rounded-3xl py-1 px-2"
                    :class="menuIconsOnly ? 'hidden' : ''">
                    <LangToggle class="text-black" :languages="languages" :currentLanguage="currentLanguage" />
                </div>
            </header>
            <Separator orientation="horizontal" />
            <div class="flex flex-1 flex-col gap-4 pt-0 w-full">
                <slot :user="user"></slot>
            </div>
        </SidebarInset>
    </SidebarProvider>
</template>
