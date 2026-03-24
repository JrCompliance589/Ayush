<!-- <template>
    <div class="rounded-[5px] text-[#000]">
        <div>
            <h2 class="text-xl mb-1">{{ $t('Developer Tools') }}</h2>
        </div>
        <div class="flex gap-x-2 mt-8 border-b-2">
            <Link rel="noopener noreferrer" href="/developer-tools/access-tokens"
                class="hover:bg-white rounded-tl-md rounded-tr-md px-2 py-2 text-sm cursor-pointer hover:border-b hover:border-black"
                :class="$page.url === '/developer-tools/access-tokens' ? 'bg-white border-b border-black' : ''">
            <span>Access Token</span>
            </Link>
            <Link rel="noopener noreferrer" href="/developer-tools/webhooks"
                class="hover:bg-white rounded-tl-md rounded-tr-md px-2 py-2 text-sm cursor-pointer hover:border-b hover:border-black"
                :class="$page.url === '/developer-tools/webhooks' ? 'bg-white border-b border-black' : ''">
            <span>Webhooks</span>
            </Link>
        </div>
    </div>
</template>
<script setup>
import { Link } from "@inertiajs/vue3";
</script>

 -->


<!-- ============================================ NEW UI CODE ============================================= -->

<template>
    <div class="mb-8">
        <!-- Header Section with Icon -->
        <div class="mb-8">
            <div class="flex items-center gap-3">
                <div class="p-2 text-white bg-gradient-to-br from-[#ff5100] to-orange-600 rounded-xl shadow-lg">
                    <CodeXml class="w-6 h-6" />
                </div>
                <div>
                    <h2
                        class="text-2xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                        {{ $t('Developer Tools') }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-0.5">API integration & webhooks</p>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="relative max-w-sm">

            <!-- Tab Container -->
            <div
                class="relative bg-white/80 backdrop-blur-xl rounded-xl shadow-lg border border-white/20 p-2 flex gap-2">

                <!-- Access Token Tab -->
                <Link href="/developer-tools/access-tokens"
                    class="group relative flex-1 px-6 py-3 rounded-lg transition-all duration-300 overflow-hidden"
                    :class="$page.url === '/developer-tools/access-tokens'
                        ? 'bg-gradient-to-r from-[#ff5100] to-orange-600 text-white shadow-lg shadow-[#ff5100]/30'
                        : 'hover:bg-slate-100 text-slate-600'">

                <!-- Content -->
                <div class="relative flex items-center justify-center gap-2">

                    <Key :class="$page.url === '/developer-tools/access-tokens' ? 'text-white' : 'text-slate-400 group-hover:text-[#ff5100]'"
                        class="transition-colors text-xl duration-300" />
                    <span class="font-semibold text-sm whitespace-nowrap">
                        Access Token
                    </span>
                </div>
                </Link>

                <!-- Webhooks Tab -->
                <Link href="/developer-tools/webhooks"
                    class="group relative flex-1 px-6 py-3 rounded-lg transition-all duration-300 overflow-hidden"
                    :class="$page.url === '/developer-tools/webhooks'
                        ? 'bg-gradient-to-r from-[#ff5100] to-orange-600 text-white shadow-lg shadow-[#ff5100]/30'
                        : 'hover:bg-slate-100 text-slate-600'">

                <!-- Content -->
                <div class="relative flex items-center justify-center gap-2">
                    <!-- <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        :class="$page.url === '/developer-tools/webhooks' ? 'text-white' : 'text-slate-400 group-hover:text-[#ff5100]'"
                        class="transition-colors duration-300">
                        <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6m4-3h6v6m-11 5L21 3" />
                        </g>
                    </svg> -->
                    <Globe
                        :class="$page.url === '/developer-tools/webhooks' ? 'text-white' : 'text-slate-400 group-hover:text-[#ff5100]'"
                        class="transition-colors duration-300" />
                    <span class="font-semibold text-sm whitespace-nowrap">
                        Webhooks
                    </span>
                </div>
                </Link>

            </div>
        </div>

        <!-- Decorative Line -->
        <div class="mt-6 h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import { CodeXml, Globe, Key } from "lucide-vue-next";
</script>