<!-- <template>
    <AppLayout>
        <div class="fixed top-0 z-10 w-full bg-white border-b px-4 py-4 flex md:hidden">
            <div class="w-1/3">
                <Link href="/admin/settings">
                <svg xmlns="http://www.w3.org/2000/svg" width="2.5em" height="2.5em" viewBox="0 0 256 256">
                    <path fill="currentColor"
                        d="M168.49 199.51a12 12 0 0 1-17 17l-80-80a12 12 0 0 1 0-17l80-80a12 12 0 0 1 17 17L97 128Z" />
                </svg>
                </Link>
            </div>
            <h3 class="w-1/3 text-3xl">{{ $t('Settings') }}</h3>
            <div class="w-1/3"></div>
        </div>
        <div class="flex flex-grow h-screen md:h-[calc(100vh-65px)]">
            <Sidebar></Sidebar>
            <div class="bg-white md:bg-inherit pt-16 md:pt-8 md:px-16 px-4 w-full py-8 md:overflow-y-scroll h-full">
                <slot></slot>
            </div>
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from "./../../Layout/App.vue";
import Sidebar from "./Sidebar.vue";
import { Link } from "@inertiajs/vue3";
</script>
 -->


<!-- ========================================= NEW UI CODE ==================================== -->

<template>
    <AppLayout>
        <div class="lg:h-[calc(100vh-65px)] overflow-hidden bg-white px-4 md:px-8 py-8">
            <!-- Header Section with Gradient -->
            <div class="mb-8">
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-2">
                        <div
                            class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-xl shadow-lg shadow-[#ff5100]/20">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                class="text-white">
                                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2">
                                    <path d="M12 15a3 3 0 1 0 0-6a3 3 0 0 0 0 6" />
                                    <path
                                        d="m19.622 10.395l-1.097-2.65L20 6l-2-2l-1.735 1.483l-2.707-1.113L12.935 2h-1.954l-.632 2.401l-2.645 1.115L6 4L4 6l1.453 1.789l-1.08 2.657L2 11v2l2.401.656L5.516 16.3L4 18l2 2l1.791-1.46l2.606 1.072L11 22h2l.604-2.387l2.651-1.098C16.697 18.832 18 20 18 20l2-2l-1.484-1.75l1.098-2.652l2.386-.62V11z" />
                                </g>
                            </svg>
                        </div>
                        <h1
                            class="text-3xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent">
                            {{ $t('Settings') }}
                        </h1>
                    </div>
                    <div class="flex items-start gap-2 text-gray-600">
                        <p class="text-sm md:text-base font-medium leading-relaxed">
                            {{ $t('Manage system configuration and preferences') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-12 gap-6">
                <!-- Sidebar Navigation -->
                <div class="col-span-12 lg:col-span-3">
                    <div class="bg-white rounded-2xl shadow-sm border border-primary/10 overflow-hidden sticky top-6">
                        <div class="p-4 bg-gradient-to-r from-slate-50 to-orange-50/30 border-b border-slate-200/60">
                            <h3 class="font-semibold text-slate-900 text-sm uppercase tracking-wide">{{ $t('Navigation')
                                }}</h3>
                        </div>
                        <nav class="p-3 space-y-1 max-h-[calc(100vh-330px)] overflow-y-auto">
                            <Link v-for="item in menuItems" :key="item.href" :href="item.href"
                                class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200"
                                :class="isActive(item.href)
                                    ? 'bg-primary font-semibold text-white scale-[1.02]'
                                    : 'text-slate-700 hover:bg-white hover:text-[#ff5100]'">
                                <div class="p-2 rounded-lg transition-colors" :class="isActive(item.href)
                                    ? 'bg-white/20'
                                    : 'bg-slate-100 group-hover:bg-[#ff5100]/10'">
                                    <component :is="item.icon" class="w-5 h-5" />
                                </div>
                                <span class="text-sm truncate flex-1">{{ $t(item.label) }}</span>
                                <svg v-if="isActive(item.href)" xmlns="http://www.w3.org/2000/svg" width="20"
                                    height="20" viewBox="0 0 24 24" class="shrink-0">
                                    <path fill="currentColor"
                                        d="m9.55 18l-5.7-5.7l1.425-1.425L9.55 15.15l9.175-9.175L20.15 7.4z" />
                                </svg>
                            </Link>
                        </nav>

                        <!-- Version Footer -->
                        <div class="p-4 border-t border-slate-200/60 bg-gradient-to-r from-slate-50 to-orange-50/20">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-gray-500">Version: v{{ version }}</span>
                                <Link href="/admin/updates"
                                    class="px-3 py-1.5 text-xs font-medium bg-white hover:bg-[#ff5100] hover:text-white border border-slate-200 hover:border-[#ff5100] rounded-lg transition-all duration-200">
                                    {{ $t('Updates') }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="col-span-12 lg:col-span-9">
                    <slot />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../../Layout/App.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { ChartNoAxesGantt, FileText, Landmark } from "lucide-vue-next";
import { computed, h } from 'vue';

const isActive = (href) => {
    return window.location.pathname.startsWith(href);
};

const version = computed(() => usePage().props.applicationVersion);

// Icon components
const GeneralIcon = () => h('svg', {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    'stroke-width': "2",
    'stroke-linecap': "round",
    'stroke-linejoin': "round"
}, [
    h('path', { d: "m13.6 21.076l5.46-3.152c.584-.337.875-.505 1.087-.74a2 2 0 0 0 .416-.72c.097-.301.097-.637.097-1.307V8.843c0-.67 0-1.006-.098-1.307a2 2 0 0 0-.416-.72c-.21-.234-.5-.402-1.079-.736L13.6 2.924c-.583-.337-.874-.505-1.184-.57a2 2 0 0 0-.832 0c-.31.065-.601.233-1.184.57L4.938 6.077c-.582.336-.873.504-1.084.739a2 2 0 0 0-.416.72c-.098.302-.098.638-.098 1.311v6.305c0 .673 0 1.01.098 1.311a2 2 0 0 0 .416.72c.211.236.503.404 1.085.74l5.46 3.153c.584.337.875.505 1.185.57c.274.059.558.059.832 0c.31-.065.602-.233 1.185-.57Z" }),
    h('path', { d: "M9 12a3 3 0 1 0 6 0a3 3 0 0 0-6 0Z" })
]);

const TimezoneIcon = () => h('svg', {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    'stroke-width': "2",
    'stroke-linecap': "round",
    'stroke-linejoin': "round"
}, [
    h('path', { d: "M12 13V9m9-3l-2-2m-9-2h4m-2 19a8 8 0 1 1 0-16a8 8 0 0 1 0 16Z" })
]);

const BroadcastIcon = () => h('svg', {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    'stroke-width': "2",
    'stroke-linecap': "round",
    'stroke-linejoin': "round"
}, [
    h('path', { d: "M18.364 19.364a9 9 0 1 0-12.728 0" }),
    h('path', { d: "M15.536 16.536a5 5 0 1 0-7.072 0" }),
    h('path', { d: "M11 13a1 1 0 1 0 2 0a1 1 0 1 0-2 0" })
]);

const SocialIcon = () => h('svg', {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    'stroke-width': "1.5"
}, [
    h('path', { 'stroke-linecap': "round", d: "M8 16c0 2.828 0 4.243.879 5.121C9.757 22 11.172 22 14 22h1c2.828 0 4.243 0 5.121-.879C21 20.243 21 18.828 21 16V8c0-2.828 0-4.243-.879-5.121C19.243 2 17.828 2 15 2h-1c-2.828 0-4.243 0-5.121.879C8 3.757 8 5.172 8 8" }),
    h('path', { d: "M8 19.5c-2.357 0-3.536 0-4.268-.732C3 18.035 3 16.857 3 14.5v-5c0-2.357 0-3.536.732-4.268C4.464 4.5 5.643 4.5 8 4.5" }),
    h('path', { 'stroke-linecap': "round", 'stroke-linejoin': "round", d: "M6 12h9m0 0l-2.5 2.5M15 12l-2.5-2.5" })
]);

const StorageIcon = () => h('svg', {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    fill: "currentColor"
}, [
    h('path', { d: "M6.7 21q-.725 0-1.287-.475t-.688-1.2L3.2 10.175q-.075-.45.213-.812T4.175 9h15.65q.475 0 .763.363t.212.812l-1.525 9.15q-.125.725-.687 1.2T17.3 21H6.7ZM5.4 11l1.275 8h10.65l1.275-8H5.4Zm4.6 4h4q.425 0 .713-.288T15 14q0-.425-.288-.712T14 13h-4q-.425 0-.712.288T9 14q0 .425.288.713T10 15ZM6 8q-.425 0-.712-.288T5 7q0-.425.288-.712T6 6h12q.425 0 .713.288T19 7q0 .425-.288.713T18 8H6Zm2-3q-.425 0-.712-.288T7 4q0-.425.288-.712T8 3h8q.425 0 .713.288T17 4q0 .425-.288.713T16 5H8ZM6.675 19h10.65h-10.65Z" })
]);

const PaymentIcon = () => h('svg', {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    'stroke-width': "2",
    'stroke-linecap': "round",
    'stroke-linejoin': "round"
}, [
    h('path', { d: "M3 11v4.8c0 1.12 0 1.68.218 2.108a2 2 0 0 0 .874.874c.427.218.987.218 2.105.218h11.606c1.118 0 1.677 0 2.104-.218c.377-.192.683-.498.875-.874c.218-.428.218-.986.218-2.104V11M3 11V9m0 2h18M3 9v-.8c0-1.12 0-1.68.218-2.108c.192-.377.497-.682.874-.874C4.52 5 5.08 5 6.2 5h11.6c1.12 0 1.68 0 2.107.218c.377.192.683.497.875.874c.218.427.218.987.218 2.105V9M3 9h18M7 15h4m10-4V9" })
]);

const SMTPIcon = () => h('svg', {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    'stroke-width': "2",
    'stroke-linecap': "round",
    'stroke-linejoin': "round"
}, [
    h('path', { d: "M10 18h11M3 18h3m0 0v2m0-2v-2m14-4h1M3 12h13m0 0v2m0-2v-2m-2-4h7M3 6h7m0 0v2m0-2V4" })
]);

const EmailIcon = () => h('svg', {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    'stroke-width': "2",
    'stroke-linecap': "round",
    'stroke-linejoin': "round"
}, [
    h('path', { d: "m4 6l6.108 4.612l.002.002c.678.497 1.017.746 1.389.842a2 2 0 0 0 1.002 0c.372-.096.712-.345 1.392-.844c0 0 3.917-3.006 6.107-4.612M3 15.8V8.2c0-1.12 0-1.68.218-2.108c.192-.377.497-.682.874-.874C4.52 5 5.08 5 6.2 5h11.6c1.12 0 1.68 0 2.107.218c.377.192.683.497.875.874c.218.427.218.987.218 2.105v7.607c0 1.118 0 1.676-.218 2.104a2.002 2.002 0 0 1-.875.874c-.427.218-.986.218-2.104.218H6.197c-1.118 0-1.678 0-2.105-.218a2 2 0 0 1-.874-.874C3 17.48 3 16.92 3 15.8Z" })
]);

const PageIcon = () => h('svg', {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 48 48",
    fill: "none",
    stroke: "currentColor",
    'stroke-linejoin': "round",
    'stroke-width': "4"
}, [
    h('rect', { width: "36", height: "36", x: "6", y: "6", rx: "3" }),
    h('path', { 'stroke-linecap': "round", d: "M6 17h36M17 42V17" })
]);

const SubscriptionIcon = () => h('svg', {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    'stroke-width': "2",
    'stroke-linecap': "round",
    'stroke-linejoin': "round"
}, [
    h('path', { d: "M12 13V9m9-3l-2-2m-9-2h4m-2 19a8 8 0 1 1 0-16a8 8 0 0 1 0 16Z" })
]);

const TaxIcon = () => h('svg', {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    'stroke-width': "2",
    'stroke-linecap': "round",
    'stroke-linejoin': "round"
}, [
    h('path', { d: "m9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16l3.5-2l3.5 2l3.5-2l3.5 2ZM10 8.5a.5.5 0 1 1-1 0a.5.5 0 0 1 1 0Zm5 5a.5.5 0 1 1-1 0a.5.5 0 0 1 1 0Z" })
]);

const CouponIcon = () => h('svg', {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    'stroke-width': "2",
    'stroke-linecap': "round",
    'stroke-linejoin': "round"
}, [
    h('path', { d: "M14 6H6c-.932 0-1.398 0-1.766.152a2 2 0 0 0-1.082 1.083C3 7.602 3 8.068 3 9a3 3 0 1 1 0 6c0 .932 0 1.398.152 1.765a2 2 0 0 0 1.082 1.083C4.602 18 5.068 18 6 18h8m0-12h4c.932 0 1.398 0 1.765.152a2 2 0 0 1 1.083 1.083C21 7.602 21 8.068 21 9a3 3 0 1 0 0 6c0 .932 0 1.398-.152 1.765a2 2 0 0 1-1.083 1.083C19.398 18 18.932 18 18 18h-4m0-12v12" })
]);

const LanguageIcon = () => h('svg', {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    'stroke-width': "1.5",
    'stroke-linecap': "round",
    'stroke-linejoin': "round"
}, [
    h('path', { d: "M2 12c0 5.523 4.477 10 10 10s10-4.477 10-10S17.523 2 12 2S2 6.477 2 12" }),
    h('path', { d: "M13 2.05S16 6 16 12s-3 9.95-3 9.95m-2 0S8 18 8 12s3-9.95 3-9.95M2.63 15.5h18.74m-18.74-7h18.74" })
]);

const menuItems = [
    { href: '/admin/settings/general', label: 'General settings', icon: GeneralIcon },
    { href: '/admin/settings/timezone', label: 'Timezone and currency', icon: TimezoneIcon },
    // { href: '/admin/settings/alerts', label: 'Alerts and Remainders', icon: Siren },
    { href: '/admin/settings/broadcast-drivers', label: 'Broadcast driver', icon: BroadcastIcon },
    { href: '/admin/settings/socials', label: 'Social login', icon: SocialIcon },
    { href: '/admin/settings/storage', label: 'Storage settings', icon: StorageIcon },
    { href: '/admin/payment-gateways', label: 'Payment gateways', icon: PaymentIcon },
    { href: '/admin/tax-rates', label: 'Tax rates', icon: TaxIcon },
    { href: '/admin/settings/smtp', label: 'SMTP settings', icon: SMTPIcon },
    { href: '/admin/settings/email-templates', label: 'Email templates', icon: EmailIcon },
    { href: '/admin/settings/pages', label: 'Frontend Pages', icon: PageIcon },
    { href: '/admin/settings/subscription', label: 'Subscription settings', icon: SubscriptionIcon },
    { href: '/admin/settings/billing', label: 'Billing info', icon: PaymentIcon },
    { href: '/admin/settings/bank', label: 'Bank info', icon: Landmark },
    { href: '/admin/settings/products', label: 'Products', icon: ChartNoAxesGantt },
    { href: '/admin/settings/terms-conditions', label: 'Invoices T&C', icon: FileText },
    { href: '/admin/settings/tds-tax-slabs', label: 'TDS Tax Slabs', icon: TaxIcon },
    { href: '/admin/coupons', label: 'Coupons', icon: CouponIcon },
    { href: '/admin/languages', label: 'Languages & translations', icon: LanguageIcon },
];

</script>