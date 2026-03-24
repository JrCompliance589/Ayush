<!-- <template>
    <TabGroup>
        <TabList class="flex space-x-1 backdrop-blur-xl bg-white/20 p-1">
            <Tab as="template" v-slot="{ selected }">
                <button :class="[
                    'w-full py-2.5 text-sm leading-5 text-[#ffffffcc]',
                    'ring-white focus:outline-none',
                    selected
                        ? 'text-white shadow border-b-2 border-white'
                        : 'hover:bg-slate-100 hover:text-black',
                ]">
                    {{ $t('CURL') }}
                </button>
            </Tab>
            <Tab as="template" v-slot="{ selected }">
                <button :class="[
                    'w-full py-2.5 text-sm leading-5 text-[#ffffffcc]',
                    'ring-white focus:outline-none',
                    selected
                        ? 'text-white shadow border-b-2 border-white'
                        : 'hover:bg-slate-100 hover:text-black',
                ]">
                    {{ $t('PHP') }}
                </button>
            </Tab>
            <Tab as="template" v-slot="{ selected }">
                <button :class="[
                    'w-full py-2.5 text-sm leading-5 text-[#ffffffcc]',
                    'ring-white focus:outline-none',
                    selected
                        ? 'text-white shadow border-b-2 border-white'
                        : 'hover:bg-slate-100 hover:text-black',
                ]">
                    {{ $t('NODEJS') }}
                </button>
            </Tab>
            <Tab as="template" v-slot="{ selected }">
                <button :class="[
                    'w-full py-2.5 text-sm leading-5 text-[#ffffffcc]',
                    'ring-white focus:outline-none',
                    selected
                        ? 'text-white shadow border-b-2 border-white'
                        : 'hover:bg-slate-100 hover:text-black',
                ]">
                    {{ $t('PYTHON') }}
                </button>
            </Tab>
            <Tab as="template" v-slot="{ selected }">
                <button :class="[
                    'w-full py-2.5 text-sm leading-5 text-[#ffffffcc]',
                    'ring-white focus:outline-none',
                    selected
                        ? 'text-white shadow border-b-2 border-white'
                        : 'hover:bg-slate-100 hover:text-black',
                ]">
                    {{ $t('JAVA') }}
                </button>
            </Tab>
            <Tab as="template" v-slot="{ selected }">
                <button :class="[
                    'w-full py-2.5 text-sm leading-5 text-[#ffffffcc]',
                    'ring-white focus:outline-none',
                    selected
                        ? 'text-white shadow border-b-2 border-white'
                        : 'hover:bg-slate-100 hover:text-black',
                ]">
                    {{ $t('RUBY') }}
                </button>
            </Tab>
        </TabList>

        <TabPanels class="mt-2 text-white text-sm h-[90vh] overflow-y-scroll">
            <TabPanel>
                <div class="code-block">
                    <div v-for="(item1, index1) in props.apirequests">
                        <h3 class="text-white mb-2 capitalize" :class="index1 != 0 ? 'mt-4' : ''">{{ $t(item1.title) }}
                        </h3>
                        <div v-for="(item, index) in item1.value" @click="changeTab('curl' + item1.title + index + 1)"
                            class="mb-2 bg-white cursor-pointer py-1 px-2 rounded">
                            <div class="flex items-center gap-x-2">
                                <span class="rounded-md bg-primary px-2 text-[10px] uppercase">{{ item.method }}</span>
                                <span class="text-black">{{ $t(item.title) }}</span>
                            </div>
                            <div class="text-black mt-2 mb-2"
                                :class="tab === 'curl' + item1.title + index + 1 ? '' : 'hidden'">
                                <div class="flex items-center gap-x-2 mb-2">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="m7.05 11.293l-2.12 2.121a4 4 0 0 0 5.657 5.657l2.828-2.828a4 4 0 0 0 0-5.657l-1.06 1.06a2.5 2.5 0 0 1 0 3.536l-2.83 2.828a2.5 2.5 0 0 1-3.535-3.535l2.12-2.121z" />
                                            <path fill="currentColor"
                                                d="m15.889 11.646l2.121-2.12a2.5 2.5 0 0 0-3.535-3.536l-2.829 2.828a2.5 2.5 0 0 0 0 3.536l-1.06 1.06a4 4 0 0 1 0-5.657l2.828-2.828a4 4 0 0 1 5.657 5.657l-2.121 2.121z" />
                                        </svg>
                                    </span>
                                    <span>{{ item.route }}</span>
                                </div>
                                <hr />
                                <h4 class="mt-2 uppercase">{{ $t('Request') }}</h4>
                                <div class="text-[10px] mt-2">
                                    <VCodeBlock :code=item.request.curl highlightjs lang="bash" theme="github-dark" />
                                </div>
                                <h4 class="mt-4 uppercase">{{ $t('Response') }}</h4>
                                <div class="border p-2 rounded-md mt-2">
                                    <div class="flex items-center gap-x-2">
                                        <div class="bg-green-500 h-2 w-2 rounded-full"></div>
                                        <span>200</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </TabPanel>
            <TabPanel>
                <div class="code-block">
                    <div v-for="(item1, index1) in props.apirequests">
                        <h3 class="text-white mb-2 capitalize" :class="index1 != 0 ? 'mt-4' : ''">{{ $t(item1.title) }}
                        </h3>
                        <div v-for="(item, index) in item1.value" @click="changeTab('php' + item1.title + index + 1)"
                            class="mb-2 bg-white cursor-pointer py-1 px-2 rounded">
                            <div class="flex items-center gap-x-2">
                                <span class="rounded-md bg-primary px-2 text-[10px] uppercase">{{ item.method }}</span>
                                <span class="text-black">{{ $t(item.title) }}</span>
                            </div>
                            <div class="text-black mt-2 mb-2"
                                :class="tab === 'php' + item1.title + index + 1 ? '' : 'hidden'">
                                <div class="flex items-center gap-x-2 mb-2">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="m7.05 11.293l-2.12 2.121a4 4 0 0 0 5.657 5.657l2.828-2.828a4 4 0 0 0 0-5.657l-1.06 1.06a2.5 2.5 0 0 1 0 3.536l-2.83 2.828a2.5 2.5 0 0 1-3.535-3.535l2.12-2.121z" />
                                            <path fill="currentColor"
                                                d="m15.889 11.646l2.121-2.12a2.5 2.5 0 0 0-3.535-3.536l-2.829 2.828a2.5 2.5 0 0 0 0 3.536l-1.06 1.06a4 4 0 0 1 0-5.657l2.828-2.828a4 4 0 0 1 5.657 5.657l-2.121 2.121z" />
                                        </svg>
                                    </span>
                                    <span>{{ item.route }}</span>
                                </div>
                                <hr />
                                <h4 class="mt-2 uppercase">{{ $t('Request') }}</h4>
                                <div class="text-[10px] mt-2">
                                    <VCodeBlock :code=item.request.php highlightjs lang="bash" theme="github-dark" />
                                </div>
                                <h4 class="mt-4 uppercase">{{ $t('Response') }}</h4>
                                <div class="border p-2 rounded-md mt-2">
                                    <div class="flex items-center gap-x-2">
                                        <div class="bg-green-500 h-2 w-2 rounded-full"></div>
                                        <span>200</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </TabPanel>
            <TabPanel>
                <div class="code-block">
                    <div v-for="(item1, index1) in props.apirequests">
                        <h3 class="text-white mb-2 capitalize" :class="index1 != 0 ? 'mt-4' : ''">{{ $t(item1.title) }}
                        </h3>
                        <div v-for="(item, index) in item1.value" @click="changeTab('nodejs' + item1.title + index + 1)"
                            class="mb-2 bg-white cursor-pointer py-1 px-2 rounded">
                            <div class="flex items-center gap-x-2">
                                <span class="rounded-md bg-primary px-2 text-[10px] uppercase">{{ item.method }}</span>
                                <span class="text-black">{{ $t(item.title) }}</span>
                            </div>
                            <div class="text-black mt-2 mb-2"
                                :class="tab === 'nodejs' + item1.title + index + 1 ? '' : 'hidden'">
                                <div class="flex items-center gap-x-2 mb-2">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="m7.05 11.293l-2.12 2.121a4 4 0 0 0 5.657 5.657l2.828-2.828a4 4 0 0 0 0-5.657l-1.06 1.06a2.5 2.5 0 0 1 0 3.536l-2.83 2.828a2.5 2.5 0 0 1-3.535-3.535l2.12-2.121z" />
                                            <path fill="currentColor"
                                                d="m15.889 11.646l2.121-2.12a2.5 2.5 0 0 0-3.535-3.536l-2.829 2.828a2.5 2.5 0 0 0 0 3.536l-1.06 1.06a4 4 0 0 1 0-5.657l2.828-2.828a4 4 0 0 1 5.657 5.657l-2.121 2.121z" />
                                        </svg>
                                    </span>
                                    <span>{{ item.route }}</span>
                                </div>
                                <hr />
                                <h4 class="mt-2 uppercase">{{ $t('Request') }}</h4>
                                <div class="text-[10px] mt-2">
                                    <VCodeBlock :code=item.request.nodejs highlightjs lang="bash" theme="github-dark" />
                                </div>
                                <h4 class="mt-4 uppercase">{{ $t('Response') }}</h4>
                                <div class="border p-2 rounded-md mt-2">
                                    <div class="flex items-center gap-x-2">
                                        <div class="bg-green-500 h-2 w-2 rounded-full"></div>
                                        <span>200</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </TabPanel>
            <TabPanel>
                <div class="code-block">
                    <div v-for="(item1, index1) in props.apirequests">
                        <h3 class="text-white mb-2 capitalize" :class="index1 != 0 ? 'mt-4' : ''">{{ $t(item1.title) }}
                        </h3>
                        <div v-for="(item, index) in item1.value" @click="changeTab('python' + item1.title + index + 1)"
                            class="mb-2 bg-white cursor-pointer py-1 px-2 rounded">
                            <div class="flex items-center gap-x-2">
                                <span class="rounded-md bg-primary px-2 text-[10px] uppercase">{{ item.method }}</span>
                                <span class="text-black">{{ $t(item.title) }}</span>
                            </div>
                            <div class="text-black mt-2 mb-2"
                                :class="tab === 'python' + item1.title + index + 1 ? '' : 'hidden'">
                                <div class="flex items-center gap-x-2 mb-2">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="m7.05 11.293l-2.12 2.121a4 4 0 0 0 5.657 5.657l2.828-2.828a4 4 0 0 0 0-5.657l-1.06 1.06a2.5 2.5 0 0 1 0 3.536l-2.83 2.828a2.5 2.5 0 0 1-3.535-3.535l2.12-2.121z" />
                                            <path fill="currentColor"
                                                d="m15.889 11.646l2.121-2.12a2.5 2.5 0 0 0-3.535-3.536l-2.829 2.828a2.5 2.5 0 0 0 0 3.536l-1.06 1.06a4 4 0 0 1 0-5.657l2.828-2.828a4 4 0 0 1 5.657 5.657l-2.121 2.121z" />
                                        </svg>
                                    </span>
                                    <span>{{ item.route }}</span>
                                </div>
                                <hr />
                                <h4 class="mt-2 uppercase">{{ $t('Request') }}</h4>
                                <div class="text-[10px] mt-2">
                                    <VCodeBlock :code=item.request.python highlightjs lang="bash" theme="github-dark" />
                                </div>
                                <h4 class="mt-4 uppercase">{{ $t('Response') }}</h4>
                                <div class="border p-2 rounded-md mt-2">
                                    <div class="flex items-center gap-x-2">
                                        <div class="bg-green-500 h-2 w-2 rounded-full"></div>
                                        <span>200</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </TabPanel>
            <TabPanel>
                <div class="code-block">
                    <div v-for="(item1, index1) in props.apirequests">
                        <h3 class="text-white mb-2 capitalize" :class="index1 != 0 ? 'mt-4' : ''">{{ $t(item1.title) }}
                        </h3>
                        <div v-for="(item, index) in item1.value" @click="changeTab('java' + item1.title + index + 1)"
                            class="mb-2 bg-white cursor-pointer py-1 px-2 rounded">
                            <div class="flex items-center gap-x-2">
                                <span class="rounded-md bg-primary px-2 text-[10px] uppercase">{{ item.method }}</span>
                                <span class="text-black">{{ $t(item.title) }}</span>
                            </div>
                            <div class="text-black mt-2 mb-2"
                                :class="tab === 'java' + item1.title + index + 1 ? '' : 'hidden'">
                                <div class="flex items-center gap-x-2 mb-2">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="m7.05 11.293l-2.12 2.121a4 4 0 0 0 5.657 5.657l2.828-2.828a4 4 0 0 0 0-5.657l-1.06 1.06a2.5 2.5 0 0 1 0 3.536l-2.83 2.828a2.5 2.5 0 0 1-3.535-3.535l2.12-2.121z" />
                                            <path fill="currentColor"
                                                d="m15.889 11.646l2.121-2.12a2.5 2.5 0 0 0-3.535-3.536l-2.829 2.828a2.5 2.5 0 0 0 0 3.536l-1.06 1.06a4 4 0 0 1 0-5.657l2.828-2.828a4 4 0 0 1 5.657 5.657l-2.121 2.121z" />
                                        </svg>
                                    </span>
                                    <span>{{ item.route }}</span>
                                </div>
                                <hr />
                                <h4 class="mt-2 uppercase">{{ $t('Request') }}</h4>
                                <div class="text-[10px] mt-2">
                                    <VCodeBlock :code=item.request.java highlightjs lang="bash" theme="github-dark" />
                                </div>
                                <h4 class="mt-4 uppercase">{{ $t('Response') }}</h4>
                                <div class="border p-2 rounded-md mt-2">
                                    <div class="flex items-center gap-x-2">
                                        <div class="bg-green-500 h-2 w-2 rounded-full"></div>
                                        <span>200</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </TabPanel>
            <TabPanel>
                <div class="code-block">
                    <div v-for="(item1, index1) in props.apirequests">
                        <h3 class="text-white mb-2 capitalize" :class="index1 != 0 ? 'mt-4' : ''">{{ $t(item1.title) }}
                        </h3>
                        <div v-for="(item, index) in item1.value" @click="changeTab('ruby' + item1.title + index + 1)"
                            class="mb-2 bg-white cursor-pointer py-1 px-2 rounded">
                            <div class="flex items-center gap-x-2">
                                <span class="rounded-md bg-primary px-2 text-[10px] uppercase">{{ item.method }}</span>
                                <span class="text-black">{{ $t(item.title) }}</span>
                            </div>
                            <div class="text-black mt-2 mb-2"
                                :class="tab === 'ruby' + item1.title + index + 1 ? '' : 'hidden'">
                                <div class="flex items-center gap-x-2 mb-2">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="m7.05 11.293l-2.12 2.121a4 4 0 0 0 5.657 5.657l2.828-2.828a4 4 0 0 0 0-5.657l-1.06 1.06a2.5 2.5 0 0 1 0 3.536l-2.83 2.828a2.5 2.5 0 0 1-3.535-3.535l2.12-2.121z" />
                                            <path fill="currentColor"
                                                d="m15.889 11.646l2.121-2.12a2.5 2.5 0 0 0-3.535-3.536l-2.829 2.828a2.5 2.5 0 0 0 0 3.536l-1.06 1.06a4 4 0 0 1 0-5.657l2.828-2.828a4 4 0 0 1 5.657 5.657l-2.121 2.121z" />
                                        </svg>
                                    </span>
                                    <span>{{ item.route }}</span>
                                </div>
                                <hr />
                                <h4 class="mt-2 uppercase">{{ $t('Request') }}</h4>
                                <div class="text-[10px] mt-2">
                                    <VCodeBlock :code=item.request.ruby highlightjs lang="bash" theme="github-dark" />
                                </div>
                                <h4 class="mt-4 uppercase">{{ $t('Response') }}</h4>
                                <div class="border p-2 rounded-md mt-2">
                                    <div class="flex items-center gap-x-2">
                                        <div class="bg-green-500 h-2 w-2 rounded-full"></div>
                                        <span>200</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </TabPanel>
        </TabPanels>
    </TabGroup>
</template>


<script setup>
import { ref } from 'vue';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import VCodeBlock from '@wdns/vue-code-block';

const props = defineProps({ rows: Object, url: String, apirequests: Object });
const tab = ref(null);

const changeTab = (id) => {
    tab.value = id;
}

</script>


<style scoped>
.code-block {
    white-space: pre-wrap;
    /* Preserve line breaks */
    padding: 1rem;
    /* Optional: Add padding */
}
</style> -->


<!-- ============================================ NEW UI CODE ============================================= -->


<template>
    <div class="h-full flex flex-col">
        <TabGroup>
            <div class="sticky top-0 z-20 backdrop-blur-xl bg-primary/15 border-b border-primary/30">
                <TabList class="grid grid-cols-6 overflow-x-auto scrollbar-hide">
                    <Tab v-for="lang in ['CURL', 'PHP', 'NODEJS', 'PYTHON', 'JAVA', 'RUBY']" :key="lang"
                        v-slot="{ selected }" as="template">
                        <button
                            :class="['place-items-center p-3 text-sm font-medium transition-all duration-300 border-b-2 whitespace-nowrap', selected ? 'text-[#ff5100] border-[#ff5100] bg-[#ff5100]/10' : 'text-black/90 border-transparent hover:bg-primary/10']">
                            <span class="flex items-center gap-2">
                                <svg v-if="lang === 'CURL'" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="4 17 10 11 4 5"></polyline>
                                    <line x1="12" y1="19" x2="20" y2="19"></line>
                                </svg>
                                {{ $t(lang) }}
                            </span>
                        </button>
                    </Tab>
                </TabList>
            </div>

            <TabPanels class="flex-1 overflow-y-auto">
                <TabPanel v-for="lang in ['curl', 'php', 'nodejs', 'python', 'java', 'ruby']" :key="lang"
                    class="focus:outline-none p-6">
                    <div class="code-block">
                        <div v-for="(item1, index1) in props.apirequests" :key="lang + '-cat-' + index1">
                            <h3 class="text-black/70 mb-4 capitalize font-bold text-lg"
                                :class="index1 != 0 ? 'mt-8' : ''">
                                {{ $t(item1.title) }}</h3>
                            <div v-for="(item, index) in item1.value" :key="lang + '-item-' + index"
                                class="mb-3 bg-white backdrop-blur-sm cursor-pointer rounded-xl border border-primary/10 overflow-hidden hover:border-[#ff5100]/50 transition-all duration-300">
                                <div @click="changeTab(lang + item1.title + index + 1)"
                                    class="px-6 py-4 flex items-center justify-between hover:bg-slate-50/50 group">
                                    <div class="flex items-center gap-x-3">
                                        <span
                                            class="rounded-lg px-3 py-1 bg-[#ff5100] text-white text-xs font-bold uppercase">{{
                                                item.method }}</span>
                                        <span class="text-black/80 font-medium transition-colors">{{
                                            $t(item.title) }}</span>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-slate-400 group-hover:text-[#ff5100] transition-all duration-300"
                                        :class="tab === lang + item1.title + index + 1 ? 'rotate-90' : ''">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" d="m9 18l6-6l-6-6" />
                                    </svg>
                                </div>
                                <div class="text-black/80"
                                    :class="tab === lang + item1.title + index + 1 ? '' : 'hidden'">
                                    <div class="border-t border-primary/20 bg-white p-6 space-y-4">
                                        <div class="flex items-center gap-x-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" class="text-[#ff5100]">
                                                <path fill="currentColor"
                                                    d="m7.05 11.293l-2.12 2.121a4 4 0 0 0 5.657 5.657l2.828-2.828a4 4 0 0 0 0-5.657l-1.06 1.06a2.5 2.5 0 0 1 0 3.536l-2.83 2.828a2.5 2.5 0 0 1-3.535-3.535l2.12-2.121z" />
                                                <path fill="currentColor"
                                                    d="m15.889 11.646l2.121-2.12a2.5 2.5 0 0 0-3.535-3.536l-2.829 2.828a2.5 2.5 0 0 0 0 3.536l-1.06 1.06a4 4 0 0 1 0-5.657l2.828-2.828a4 4 0 0 1 5.657 5.657l-2.121 2.121z" />
                                            </svg>
                                            <span class="text-black/80 text-lg font-light">{{ item.route }}</span>
                                        </div>
                                        <hr class="border-primary/50" />
                                        <h4 class="mt-4 uppercase text-blue-500 text-xs font-bold">{{ $t('Request') }}
                                        </h4>
                                        <div class=" mt-2">
                                            <VCodeBlock :code="item.request[lang]" highlightjs lang="bash"
                                                theme="github-dark" />
                                        </div>
                                        <h4 class="mt-6 uppercase text-green-500 text-xs font-bold">{{ $t('Response') }}
                                        </h4>
                                        <div class="border border-green-500/50 p-3 rounded-md mt-2 bg-green-400/10">
                                            <div class="flex items-center gap-x-2">
                                                <div class="bg-green-700 h-2 w-2 rounded-full"></div>
                                                <span class="text-green-700">200</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </TabPanel>
            </TabPanels>
        </TabGroup>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import VCodeBlock from '@wdns/vue-code-block';

const props = defineProps({ rows: Object, url: String, apirequests: Object });
const tab = ref(null);

const changeTab = (id) => {
    if (id !== tab.value) {
        tab.value = id;
    } else {
        tab.value = null;
    }
}
</script>

<style scoped>
.code-block {
    white-space: pre-wrap;
    padding: 0;
}

.focus\:outline-none:focus {
    outline: none;
}
</style>