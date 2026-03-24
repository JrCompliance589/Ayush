<!-- <script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import 'vue3-toastify/dist/index.css';
import Table from '@/Components/Table.vue';
import TableHeader from '@/Components/TableHeader.vue';
import TableHeaderRow from '@/Components/TableHeaderRow.vue';
import TableHeaderRowItem from '@/Components/TableHeaderRowItem.vue';
import TableBody from '@/Components/TableBody.vue';
import TableBodyRow from '@/Components/TableBodyRow.vue';
import TableBodyRowItem from '@/Components/TableBodyRowItem.vue';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
});

const user = computed(() => usePage().props.auth.user);

const ticketUrl = (uuid) => {
    return usePage().props.auth.user.role != 'user' ? '/admin/support/' + uuid : '/support/' + uuid;
}
</script>
<template>
    <Table :rows="rows">
        <TableHeader>
            <TableHeaderRow>
                <TableHeaderRowItem :position="'first'">{{ $t('Ref') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Subject') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Priority') }}</TableHeaderRowItem>
                <TableHeaderRowItem v-if="user.role != 'user'">{{ $t('User') }}</TableHeaderRowItem>
                <TableHeaderRowItem v-if="user.role != 'user'">{{ $t('Assigned to') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Status') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Last updated') }}</TableHeaderRowItem>
                <TableHeaderRowItem :position="'last'"></TableHeaderRowItem>
            </TableHeaderRow>
        </TableHeader>
        <TableBody>
            <TableBodyRow v-for="(item, index) in rows.data" :key="index">
                <TableBodyRowItem :position="'first'" class="capitalize">
                    <Link :href="ticketUrl(item.uuid)">
                    {{ item.reference }}
                    </Link>
                </TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">
                    <Link :href="ticketUrl(item.uuid)">
                    <div
                        class="py-1 px-2 bg-gray-50 rounded-[5px] border border-dashed w-[20em] truncate text-xs capitalize">
                        {{ item.subject }}
                    </div>
                    </Link>
                </TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">
                    <Link :href="ticketUrl(item.uuid)">
                    <span class="text-left bg-slate-100 py-2 px-3 rounded-xl text-[11px]">{{ item.priority ?? $t(`Not
                        set`) }}</span>
                    </Link>
                </TableBodyRowItem>
                <TableBodyRowItem v-if="user.role != 'user'" class="hidden sm:table-cell">
                    <Link :href="ticketUrl(item.uuid)">
                    <span class="text-left">{{ item.user.first_name + ' ' + item.user.last_name }}</span>
                    </Link>
                </TableBodyRowItem>
                <TableBodyRowItem v-if="user.role != 'user'" class="hidden sm:table-cell">
                    <Link :href="ticketUrl(item.uuid)">
                    <span class="text-left">{{ item.agent ? item.agent?.first_name + ' ' + item.agent?.last_name :
                        $t('Not set') }}</span>
                    </Link>
                </TableBodyRowItem>
                <TableBodyRowItem class="capitalize">
                    <Link :href="ticketUrl(item.uuid)">
                    <span class="py-1 rounded-[5px] text-xs px-3 bg-[#ddebf7] text-slate-700">{{ $t(item.status)
                        }}</span>
                    </Link>
                </TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">
                    <Link :href="ticketUrl(item.uuid)">
                    {{ item.updated_at }}
                    </Link>
                </TableBodyRowItem>
                <TableBodyRowItem :position="'last'">
                    <div class="flex items-center mt-3">
                        <Link :href="ticketUrl(item.uuid)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor" fill-rule="evenodd"
                                d="M9.97 7.47a.75.75 0 0 1 1.06 0l4 4a.75.75 0 0 1 0 1.06l-4 4a.75.75 0 1 1-1.06-1.06L13.44 12L9.97 8.53a.75.75 0 0 1 0-1.06Z"
                                clip-rule="evenodd" />
                        </svg>
                        </Link>
                    </div>
                </TableBodyRowItem>
            </TableBodyRow>
        </TableBody>
    </Table>
</template> -->




<!-- ============================================ NEW UI ============================================= -->

<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import 'vue3-toastify/dist/index.css';
import Table from '@/Components/Table.vue';
import TableHeader from '@/Components/TableHeader.vue';
import TableHeaderRow from '@/Components/TableHeaderRow.vue';
import TableHeaderRowItem from '@/Components/TableHeaderRowItem.vue';
import TableBody from '@/Components/TableBody.vue';
import TableBodyRow from '@/Components/TableBodyRow.vue';
import TableBodyRowItem from '@/Components/TableBodyRowItem.vue';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
});

const user = computed(() => usePage().props.auth.user);

const ticketUrl = (uuid) => {
    return usePage().props.auth.user.role != 'user' ? '/admin/support/' + uuid : '/support/' + uuid;
}

const getPriorityColor = (priority) => {
    const colors = {
        'high': 'bg-red-100 text-red-700 border-red-200',
        'medium': 'bg-orange-100 text-orange-700 border-orange-200',
        'low': 'bg-green-100 text-green-700 border-green-200',
    };
    return colors[priority?.toLowerCase()] || 'bg-slate-100 text-slate-700 border-slate-200';
}

const getStatusColor = (status) => {
    const colors = {
        'open': 'bg-blue-100 text-blue-700 border-blue-200',
        'pending': 'bg-yellow-100 text-yellow-700 border-yellow-200',
        'resolved': 'bg-green-100 text-green-700 border-green-200',
        'closed': 'bg-slate-100 text-slate-700 border-slate-200',
    };
    return colors[status?.toLowerCase()] || 'bg-slate-100 text-slate-700 border-slate-200';
}
</script>

<template>
    <div class="overflow-hidden">
        <!-- Mobile Card View -->
        <div class="md:hidden space-y-4 p-4">
            <div v-for="(item, index) in rows.data" :key="index"
                class="group relative bg-gradient-to-br from-white to-slate-50 rounded-2xl border border-slate-200 hover:border-[#ff5100]/30 transition-all duration-300 hover:shadow-lg hover:shadow-orange-500/10 overflow-hidden">

                <!-- Decorative corner accent -->
                <div
                    class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-[#ff5100]/10 to-transparent rounded-bl-3xl">
                </div>

                <Link :href="ticketUrl(item.uuid)" class="block p-5 relative">
                <!-- Reference Badge -->
                <div class="flex items-center justify-between mb-4">
                    <span
                        class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-[#ff5100] to-[#ff7a3d] text-white text-xs font-bold rounded-lg shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                        {{ item.reference }}
                    </span>
                    <span :class="['px-3 py-1 text-xs font-semibold rounded-full border', getStatusColor(item.status)]">
                        {{ $t(item.status) }}
                    </span>
                </div>

                <!-- Subject -->
                <h3
                    class="text-sm font-semibold text-slate-800 mb-3 line-clamp-2 group-hover:text-[#ff5100] transition-colors">
                    {{ item.subject }}
                </h3>

                <!-- Meta Information -->
                <div class="space-y-2 text-xs text-slate-600">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-[#ff5100]">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                        </svg>
                        <span class="font-medium">Priority:</span>
                        <span
                            :class="['px-2 py-0.5 rounded-md text-[10px] font-semibold border', getPriorityColor(item.priority)]">
                            {{ item.priority ?? $t('Not set') }}
                        </span>
                    </div>

                    <div v-if="user.role != 'user'" class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-[#ff5100]">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span class="font-medium">User:</span>
                        <span>{{ item.user.first_name + ' ' + item.user.last_name }}</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-[#ff5100]">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span class="font-medium">Updated:</span>
                        <span>{{ item.updated_at }}</span>
                    </div>
                </div>

                <!-- Arrow Icon -->
                <div
                    class="absolute bottom-5 right-5 w-8 h-8 rounded-full bg-[#ff5100]/10 flex items-center justify-center group-hover:bg-[#ff5100] transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                        class="text-[#ff5100] group-hover:text-white group-hover:translate-x-0.5 transition-all">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </div>
                </Link>
            </div>

            <!-- Empty State -->
            <div v-if="rows.data.length === 0" class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                        class="text-slate-400">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
                <p class="text-slate-600 font-medium">{{ $t('No tickets found') }}</p>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block">
            <Table :rows="rows">
                <TableHeader>
                    <TableHeaderRow>
                        <TableHeaderRowItem :position="'first'"
                            class="sm:table-cell text-black font-bold uppercase text-xs tracking-wider py-5">
                            {{ $t('Ref') }}
                        </TableHeaderRowItem>
                        <TableHeaderRowItem
                            class="sm:table-cell text-black font-bold uppercase text-xs tracking-wider py-5">
                            {{ $t('Subject') }}
                        </TableHeaderRowItem>
                        <TableHeaderRowItem
                            class="sm:table-cell text-black font-bold uppercase text-xs tracking-wider py-5">
                            {{ $t('Priority') }}
                        </TableHeaderRowItem>
                        <TableHeaderRowItem v-if="user.role != 'user'"
                            class="sm:table-cell text-black font-bold uppercase text-xs tracking-wider py-5">
                            {{ $t('User') }}
                        </TableHeaderRowItem>
                        <TableHeaderRowItem v-if="user.role != 'user'"
                            class="sm:table-cell text-black font-bold uppercase text-xs tracking-wider py-5">
                            {{ $t('Assigned to') }}
                        </TableHeaderRowItem>
                        <TableHeaderRowItem
                            class="sm:table-cell text-black font-bold uppercase text-xs tracking-wider py-5">
                            {{ $t('Status') }}
                        </TableHeaderRowItem>
                        <TableHeaderRowItem
                            class="sm:table-cell text-black font-bold uppercase text-xs tracking-wider py-5">
                            {{ $t('Last updated') }}
                        </TableHeaderRowItem>
                        <TableHeaderRowItem :position="'last'" class="bg-transparent">
                        </TableHeaderRowItem>
                    </TableHeaderRow>
                </TableHeader>
                <TableBody>
                    <TableBodyRow v-for="(item, index) in rows.data" :key="index"
                        class="group hover:bg-gradient-to-r hover:from-orange-50/30 hover:to-transparent transition-all duration-200 border-b border-slate-100">
                        <TableBodyRowItem :position="'first'" class="capitalize">
                            <Link :href="ticketUrl(item.uuid)" class="flex items-center gap-2">
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-[#ff5100] to-[#ff7a3d] text-white text-xs font-bold rounded-lg shadow-sm group-hover:shadow-md transition-shadow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                {{ item.reference }}
                            </span>
                            </Link>
                        </TableBodyRowItem>
                        <TableBodyRowItem class="hidden sm:table-cell">
                            <Link :href="ticketUrl(item.uuid)">
                            <div
                                class="py-2 px-3 bg-slate-50 hover:bg-slate-100 rounded-xl border border-slate-200 w-[20em] truncate text-xs font-medium text-slate-700 transition-colors group-hover:border-[#ff5100]/30">
                                {{ item.subject }}
                            </div>
                            </Link>
                        </TableBodyRowItem>
                        <TableBodyRowItem class="hidden sm:table-cell">
                            <Link :href="ticketUrl(item.uuid)">
                            <span
                                :class="['inline-flex px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all', getPriorityColor(item.priority)]">
                                {{ item.priority ?? $t('Not set') }}
                            </span>
                            </Link>
                        </TableBodyRowItem>
                        <TableBodyRowItem v-if="user.role != 'user'" class="hidden sm:table-cell">
                            <Link :href="ticketUrl(item.uuid)">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-[#ff5100] to-[#ff7a3d] flex items-center justify-center text-white text-xs font-bold">
                                    {{ item.user.first_name.charAt(0) + item.user.last_name.charAt(0) }}
                                </div>
                                <span class="text-sm font-medium text-slate-700">{{ item.user.first_name + ' ' +
                                    item.user.last_name }}</span>
                            </div>
                            </Link>
                        </TableBodyRowItem>
                        <TableBodyRowItem v-if="user.role != 'user'" class="hidden sm:table-cell">
                            <Link :href="ticketUrl(item.uuid)">
                            <span class="text-sm text-slate-600">{{ item.agent ? item.agent?.first_name + ' ' +
                                item.agent?.last_name : $t('Not set') }}</span>
                            </Link>
                        </TableBodyRowItem>
                        <TableBodyRowItem class="capitalize">
                            <Link :href="ticketUrl(item.uuid)">
                            <span
                                :class="['inline-flex px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all', getStatusColor(item.status)]">
                                {{ $t(item.status) }}
                            </span>
                            </Link>
                        </TableBodyRowItem>
                        <TableBodyRowItem class="hidden sm:table-cell">
                            <Link :href="ticketUrl(item.uuid)">
                            <div class="flex items-center gap-2 text-xs text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="text-slate-400">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                {{ item.updated_at }}
                            </div>
                            </Link>
                        </TableBodyRowItem>
                        <TableBodyRowItem :position="'last'">
                            <Link :href="ticketUrl(item.uuid)" class="flex items-center justify-center">
                            <div
                                class="w-9 h-9 rounded-full bg-slate-100 group-hover:bg-[#ff5100] flex items-center justify-center transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="text-slate-600 group-hover:text-white group-hover:translate-x-0.5 transition-all">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </div>
                            </Link>
                        </TableBodyRowItem>
                    </TableBodyRow>
                </TableBody>
            </Table>

            <!-- Empty State for Desktop -->
            <div v-if="rows.data.length === 0" class="text-center py-20">
                <div
                    class="w-32 h-32 mx-auto mb-6 rounded-full bg-gradient-to-br from-slate-100 to-orange-50 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                        class="text-slate-400">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
                <p class="text-slate-600 font-semibold text-lg">{{ $t('No tickets found') }}</p>
                <p class="text-slate-500 text-sm mt-2">{{ $t('Create your first ticket to get started') }}</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Smooth hover transitions */
.group:hover .group-hover\:translate-x-0\.5 {
    transform: translateX(2px);
}

/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>