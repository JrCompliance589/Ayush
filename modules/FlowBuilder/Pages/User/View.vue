<!-- <script setup lang="ts">
import axios from "axios"
import { ref } from 'vue'
import AppLayout from "./../../../../resources/js/Pages/User/Layout/App.vue";
import { Link, useForm } from "@inertiajs/vue3"
// import { useVueFlow } from '@vue-flow/core'
// import { useClipboard } from '@vueuse/core'
import Modal from '../../../../resources/js/Components/Modal.vue';
import FormInput from '../../../../resources/js/Components/FormInput.vue';
import FormTextArea from '../../../../resources/js/Components/FormTextArea.vue';
import { Tabs, TabsContent, TabsTrigger, TabsList } from './Components/ui/tabs/index'
import { ScrollArea } from './Components/ui/scroll-area'
import mainCanvas from './Components/main-canvas.vue'
import { Button } from './Components/ui/button'
import { Toaster, useToast } from './Components/ui/toast'
import { cn } from "../../../../resources/js/lib/utils";

const props = defineProps(['uuid', 'flow', 'contactGroups', 'contactFields', 'availableActions']);
const isOpenFormModal = ref(false);
const activeTab = ref('basic-nodes');

function handleOnDragStart(event: DragEvent, nodeType: any) {
  if (event.dataTransfer) {
    event.dataTransfer.setData('application/vueflow', nodeType)
    event.dataTransfer.effectAllowed = 'move'
  }
}

// const { toObject } = useVueFlow()
// const { copy } = useClipboard()
const { toast } = useToast()
const toastClass = ref('');
const status = ref(props.flow.status);
const metadata = ref(props.flow.metadata);
const loading = ref(false);

const handleClickSaveBtn = async () => {
  loading.value = true;

  try {
    const payload = {
      metadata: metadata.value,
    };

    const response = await axios.put(`/automation/flows/${props.uuid}`, payload);
    status.value = response.data.original.status;

    toastClass.value = response.data.original.success ? 'bg-green-700 text-white' : 'bg-red-700 text-white';
    status.value = response.data.original.status;

    toast({
      title: response.data.original.success ? 'Success' : 'Error',
      description: response.data.original.success ? response.data.original.message : response.data.original.errors,
    })

    loading.value = false;
  } catch (error) {
    //console.error('Failed to update flow:', error);
  }
}

function handlePayloadUpdate(flowMetadata: any) {
  metadata.value = flowMetadata;
}

const handleClickPublishBtn = async (publish: any) => {
  loading.value = true;

  try {
    const payload = {
      metadata: metadata.value,
      publish: publish
    };

    const response = await axios.put(`/automation/flows/${props.uuid}`, payload);
    status.value = response.data.original.status;

    toastClass.value = response.data.original.success ? 'bg-green-700 text-white' : 'bg-red-700 text-white';
    status.value = response.data.original.status;

    toast({
      title: response.data.original.success ? 'Success' : 'Error',
      description: response.data.original.success ? response.data.original.message : response.data.original.errors,
    })

    loading.value = false;
  } catch (error) {
    //console.error('Failed to update flow:', error);
  }
}

function handleStatusUpdate(newStatus: any) {
  status.value = newStatus;
}

const form = useForm({
  name: props.flow.name,
  description: props.flow.description
});

const submitForm = () => {
  form.put(`/automation/flows/${props.uuid}`, {
    preserveScroll: true,
    onSuccess: () => {
      window.location.href = '/automation/flows';
    }
  })
}

// Get availableActions from backend props
const availableActions = props.availableActions || [
  'add_to_group',
  'remove_from_group',
  'update_contact',
  'send_email',
  'delay',
  'webhook',
  'conditional',
];
</script>

<template>
  <AppLayout>
    <div class="">
      <div class="relative flex h-screen w-full flex-col">
        <header class="h-20 border-l border-b border-gray-200 bg-white px-4 py-3">
          <div class="flex h-full items-center justify-between">
            <div class="flex gap-x-3">
              <div class="flex items-center gap-x-1">

                <div class="flex flex-col">
                  <div class="flex items-center gap-x-6">
                    <p class="flex items-center gap-x-2 text-bold text-md">
                      <span class="p-1 rounded-md bg-slate-50">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                          <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M3 5c0-1.414 0-2.121.44-2.56C3.878 2 4.585 2 6 2s2.121 0 2.56.44C9 2.878 9 3.585 9 5s0 2.121-.44 2.56C8.122 8 7.415 8 6 8s-2.121 0-2.56-.44C3 7.122 3 6.415 3 5m0 14c0-1.414 0-2.121.44-2.56C3.878 16 4.585 16 6 16s2.121 0 2.56.44C9 16.878 9 17.585 9 19s0 2.121-.44 2.56C8.122 22 7.415 22 6 22s-2.121 0-2.56-.44C3 21.122 3 20.415 3 19M15 7c0-1.414 0-2.121.44-2.56C15.878 4 16.585 4 18 4s2.121 0 2.56.44C21 4.878 21 5.585 21 7s0 2.121-.44 2.56c-.439.44-1.146.44-2.56.44s-2.121 0-2.56-.44C15 9.122 15 8.415 15 7m-9 9V8m12 2a3 3 0 0 1-3 3H9a3 3 0 0 0-3 3"
                            color="currentColor"></path>
                        </svg>
                      </span>
                      {{ props.flow.name }}
                    </p>
                    <button @click="isOpenFormModal = true;"
                      class="bg-slate-50 px-4 py-1 rounded-md text-sm">Edit</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex gap-x-3">
              <div class="flex gap-x-3" v-if="loading == false">
                <Link href="/automation/flows"
                  class="justify-center whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 flex items-center text-blue-800">
                <span>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M20 12H4m0 0l6-6m-6 6l6 6" />
                  </svg>
                </span>
                Go Back
                </Link>
                <Button variant="outline" :class="'bg-green-700 text-white'" size="sm" @click="handleClickSaveBtn"> {{
                  $t('Save changes') }}</Button>
                <Button v-if="status == 'inactive' && loading == false" variant="outline"
                  :class="'bg-green-700 text-white'" size="sm" @click="handleClickPublishBtn(1)"> {{ $t(`Save changes &
                  publish`) }}</Button>
                <Button v-else-if="status == 'active' && loading == false" variant="outline"
                  :class="'bg-red-500 text-white'" size="sm" @click="handleClickPublishBtn(0)"> {{ $t(`Save changes &
                  unpublish`) }}</Button>
              </div>
              <div v-else>
                <Button variant="outline" :class="'bg-white'" size="sm"> {{ $t('Updating flow, please wait...')
                  }}</Button>
              </div>
            </div>
          </div>
        </header>
        <main class="relative flex h-full w-full flex-1">
          <div class="w-1/4 bg-white border-l border-r">
            <Tabs default-value="basic-nodes">
              <TabsList class="grid w-full grid-cols-2 mb-4 border-b">
 
                <TabsTrigger value="basic-nodes" @click="activeTab = 'basic-nodes'" :class="cn(
                  'flex-1 pt-3 pb-2 text-center text-sm font-medium transition-all border-b-2',
                  activeTab === 'basic-nodes'
                    ? 'bg-slate-50 border-slate-700'
                    : 'text-gray-600 border-transparent hover:bg-slate-50'
                )">
                  Messages
                </TabsTrigger>


                <TabsTrigger value="actions" @click="activeTab = 'actions'" :class="cn(
                  'flex-1 pt-3 pb-2 text-center text-sm font-medium transition-all border-b-2',
                  activeTab === 'actions'
                    ? 'bg-slate-50 border-slate-700'
                    : 'text-gray-600 border-transparent hover:bg-slate-50'
                )">
                  Actions
                </TabsTrigger>

              </TabsList>
              <TabsContent value="basic-nodes">
                <ScrollArea class="h-[calc(100vh-150px)] w-full px-4">
                  <div
                    class="mb-4 cursor-grab rounded-md bg-white px-4 py-3 border border-gray-200 shadow-sm hover:shadow-md transition-shadow"
                    :draggable="true" @dragstart="handleOnDragStart($event, 'text')">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-x-2 text-sm">
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 24 24">
                            <g fill="none">
                              <path stroke="currentColor" stroke-linecap="round" stroke-width="1.5"
                                d="M6 14.5h8M6 18h5.5" />
                              <path stroke="currentColor" stroke-width="1.5"
                                d="M13 2.5V5c0 2.357 0 3.536.732 4.268S15.643 10 18 10h4" />
                              <path fill="currentColor"
                                d="M2.75 10a.75.75 0 0 0-1.5 0zm18.5 4a.75.75 0 0 0 1.5 0zm-5.857-9.946l-.502.557zm3.959 3.563l-.502.557zm2.302 2.537l-.685.305zM3.172 20.828l.53-.53zm17.656 0l-.53-.53zM1.355 5.927a.75.75 0 0 0 1.493.146zm21.29 12.146a.75.75 0 1 0-1.493-.146zM14 21.25h-4v1.5h4zM2.75 14v-4h-1.5v4zm18.5-.437V14h1.5v-.437zM14.891 4.61l3.959 3.563l1.003-1.115l-3.958-3.563zm7.859 8.952c0-1.689.015-2.758-.41-3.714l-1.371.61c.266.598.281 1.283.281 3.104zm-3.9-5.389c1.353 1.218 1.853 1.688 2.119 2.285l1.37-.61c-.426-.957-1.23-1.66-2.486-2.79zM10.03 2.75c1.582 0 2.179.012 2.71.216l.538-1.4c-.852-.328-1.78-.316-3.248-.316zm5.865.746c-1.086-.977-1.765-1.604-2.617-1.93l-.537 1.4c.532.204.98.592 2.15 1.645zM10 21.25c-1.907 0-3.261-.002-4.29-.14c-1.005-.135-1.585-.389-2.008-.812l-1.06 1.06c.748.75 1.697 1.081 2.869 1.239c1.15.155 2.625.153 4.489.153zM1.25 14c0 1.864-.002 3.338.153 4.489c.158 1.172.49 2.121 1.238 2.87l1.06-1.06c-.422-.424-.676-1.004-.811-2.01c-.138-1.027-.14-2.382-.14-4.289zM14 22.75c1.864 0 3.338.002 4.489-.153c1.172-.158 2.121-.49 2.87-1.238l-1.06-1.06c-.424.422-1.004.676-2.01.811c-1.027.138-2.382.14-4.289.14zm-3.97-21.5c-1.875 0-3.356-.002-4.511.153c-1.177.158-2.129.49-2.878 1.238l1.06 1.06c.424-.422 1.005-.676 2.017-.811c1.033-.138 2.395-.14 4.312-.14zM2.848 6.073c.121-1.234.382-1.9.854-2.371l-1.06-1.06c-.836.834-1.153 1.919-1.287 3.285zm18.304 11.854c-.121 1.234-.383 1.9-.854 2.371l1.06 1.06c.836-.834 1.153-1.919 1.287-3.285z" />
                            </g>
                          </svg>
                        </span>
                        Simple text
                      </div>
                      <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                          <path fill="currentColor"
                            d="M9 19.23q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36">
                          </path>
                        </svg>
                      </span>
                    </div>
                  </div>
                  <div
                    class="mb-4 cursor-grab rounded-md bg-white px-4 py-3 border border-gray-200 shadow-sm hover:shadow-md transition-shadow"
                    :draggable="true" @dragstart="handleOnDragStart($event, 'media')">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-x-2 text-sm">
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <g fill="none" stroke="currentColor" stroke-width="1.5">
                              <path
                                d="M19.562 7a2.132 2.132 0 0 0-2.1-2.5H6.538a2.132 2.132 0 0 0-2.1 2.5M17.5 4.5c.028-.26.043-.389.043-.496a2 2 0 0 0-1.787-1.993C15.65 2 15.52 2 15.26 2H8.74c-.26 0-.391 0-.497.011a2 2 0 0 0-1.787 1.993c0 .107.014.237.043.496" />
                              <path stroke-linecap="round"
                                d="M21.194 16.793c-.35 2.48-.525 3.721-1.422 4.464s-2.22.743-4.867.743h-5.81c-2.646 0-3.97 0-4.867-.743s-1.072-1.983-1.422-4.464l-.422-3c-.447-3.164-.67-4.745.278-5.77C3.61 7 5.298 7 8.672 7h6.656c3.374 0 5.062 0 6.01 1.024c.749.809.767 1.966.521 3.976" />
                              <path
                                d="M14.581 13.616c.559.346.559 1.242 0 1.588l-3.371 2.09c-.543.337-1.21-.1-1.21-.794v-4.18c0-.693.667-1.13 1.21-.794z" />
                            </g>
                          </svg>
                        </span>
                        Media files
                      </div>
                      <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                          <path fill="currentColor"
                            d="M9 19.23q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36">
                          </path>
                        </svg>
                      </span>
                    </div>
                  </div>
                  <div
                    class="mb-4 cursor-grab rounded-md bg-white px-4 py-3 border border-gray-200 shadow-sm hover:shadow-md transition-shadow"
                    :draggable="true" @dragstart="handleOnDragStart($event, 'buttons')">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-x-2 text-sm">
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 15 15">
                            <path fill="none" stroke="currentColor"
                              d="M5.5 10V8.5m0 0v-5a1 1 0 0 1 2 0v4h3.353c.91 0 1.647.737 1.647 1.647V10A4.5 4.5 0 0 1 8 14.5h-.5a4 4 0 0 1-4-4a2 2 0 0 1 2-2Zm3.5-3h2a2.5 2.5 0 0 0 0-5H4a2.5 2.5 0 0 0 0 5" />
                          </svg>
                        </span>
                        Interactive buttons
                      </div>
                      <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                          <path fill="currentColor"
                            d="M9 19.23q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36">
                          </path>
                        </svg>
                      </span>
                    </div>
                  </div>
                  <div
                    class="mb-4 cursor-grab rounded-md bg-white px-4 py-3 border border-gray-200 shadow-sm hover:shadow-md transition-shadow"
                    :draggable="true" @dragstart="handleOnDragStart($event, 'list')">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-x-2 text-sm">
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 15 15">
                            <path fill="none" stroke="currentColor"
                              d="M5.5 10V8.5m0 0v-5a1 1 0 0 1 2 0v4h3.353c.91 0 1.647.737 1.647 1.647V10A4.5 4.5 0 0 1 8 14.5h-.5a4 4 0 0 1-4-4a2 2 0 0 1 2-2Zm3.5-3h2a2.5 2.5 0 0 0 0-5H4a2.5 2.5 0 0 0 0 5" />
                          </svg>
                        </span>
                        Interactive list
                      </div>
                      <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                          <path fill="currentColor"
                            d="M9 19.23q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36">
                          </path>
                        </svg>
                      </span>
                    </div>
                  </div>
                </ScrollArea>
              </TabsContent>

              <TabsContent value="actions">
                <ScrollArea class="h-[calc(100vh-150px)] w-full px-4">
                  <div v-if="availableActions.includes('add_to_group')"
                    class="mb-4 cursor-grab rounded-md bg-white px-4 py-3 border border-gray-200 shadow-sm hover:shadow-md transition-shadow"
                    :draggable="true" @dragstart="handleOnDragStart($event, 'action-add-to-group')">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-x-2 text-sm">
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="m22 21-2-2" />
                            <path d="M16 16h6" />
                          </svg>
                        </span>
                        Add to Group
                      </div>
                      <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2">
                          <path d="M5 12h14" />
                          <path d="M12 5v14" />
                        </svg>
                      </span>
                    </div>
                  </div>

                  <div v-if="availableActions.includes('remove_from_group')"
                    class="mb-4 cursor-grab rounded-md bg-white px-4 py-3 border border-gray-200 shadow-sm hover:shadow-md transition-shadow"
                    :draggable="true" @dragstart="handleOnDragStart($event, 'action-remove-from-group')">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-x-2 text-sm">
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M5 8h8" />
                          </svg>
                        </span>
                        Remove from Group
                      </div>
                      <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2">
                          <path d="M5 12h14" />
                          <path d="M12 5v14" />
                        </svg>
                      </span>
                    </div>
                  </div>

                  <div v-if="availableActions.includes('update_contact')"
                    class="mb-4 cursor-grab rounded-md bg-white px-4 py-3 border border-gray-200 shadow-sm hover:shadow-md transition-shadow"
                    :draggable="true" @dragstart="handleOnDragStart($event, 'action-update-contact')">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-x-2 text-sm">
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                          </svg>
                        </span>
                        Update Contact
                      </div>
                      <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2">
                          <path d="M5 12h14" />
                          <path d="M12 5v14" />
                        </svg>
                      </span>
                    </div>
                  </div>

                  <div v-if="availableActions.includes('send_email')"
                    class="mb-4 cursor-grab rounded-md bg-white px-4 py-3 border border-gray-200 shadow-sm hover:shadow-md transition-shadow"
                    :draggable="true" @dragstart="handleOnDragStart($event, 'action-send-email')">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-x-2 text-sm">
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                            <polyline points="22,6 12,13 2,6" />
                          </svg>
                        </span>
                        Send Email
                      </div>
                      <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2">
                          <path d="M5 12h14" />
                          <path d="M12 5v14" />
                        </svg>
                      </span>
                    </div>
                  </div>

                  <div v-if="availableActions.includes('delay')"
                    class="mb-4 cursor-grab rounded-md bg-white px-4 py-3 border border-gray-200 shadow-sm hover:shadow-md transition-shadow"
                    :draggable="true" @dragstart="handleOnDragStart($event, 'action-delay')">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-x-2 text-sm">
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12,6 12,12 16,14" />
                          </svg>
                        </span>
                        Delay
                      </div>
                      <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2">
                          <path d="M5 12h14" />
                          <path d="M12 5v14" />
                        </svg>
                      </span>
                    </div>
                  </div>

                  <div v-if="availableActions.includes('webhook')"
                    class="mb-4 cursor-grab rounded-md bg-white px-4 py-3 border border-gray-200 shadow-sm hover:shadow-md transition-shadow"
                    :draggable="true" @dragstart="handleOnDragStart($event, 'action-webhook')">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-x-2 text-sm">
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M2 12h20" />
                            <path
                              d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                          </svg>
                        </span>
                        Webhook
                      </div>
                      <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2">
                          <path d="M5 12h14" />
                          <path d="M12 5v14" />
                        </svg>
                      </span>
                    </div>
                  </div>

                  <div v-if="availableActions.includes('ai_response')"
                    class="mb-4 cursor-grab rounded-md bg-white px-4 py-3 border border-gray-200 shadow-sm hover:shadow-md transition-shadow"
                    :draggable="true" @dragstart="handleOnDragStart($event, 'action-ai-response')">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-x-2 text-sm">
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z" />
                            <path d="M2 17l10 5 10-5" />
                            <path d="M2 12l10 5 10-5" />
                          </svg>
                        </span>
                        AI Response
                      </div>
                      <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2">
                          <path d="M5 12h14" />
                          <path d="M12 5v14" />
                        </svg>
                      </span>
                    </div>
                  </div>

                  <div v-if="availableActions.includes('conditional')"
                    class="mb-4 cursor-grab rounded-md bg-white px-4 py-3 border border-gray-200 shadow-sm hover:shadow-md transition-shadow"
                    :draggable="true" @dragstart="handleOnDragStart($event, 'action-conditional')">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-x-2 text-sm">
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M9 11l3 3L22 4" />
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                          </svg>
                        </span>
                        Conditional
                      </div>
                      <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2">
                          <path d="M5 12h14" />
                          <path d="M12 5v14" />
                        </svg>
                      </span>
                    </div>
                  </div>
                </ScrollArea>
              </TabsContent>
            </Tabs>
          </div>
          <div class="relative h-full flex-1 ">
            <main-canvas :uuid="props.uuid" :flow="props.flow" :contactGroups="props.contactGroups"
              :contactFields="props.contactFields" @updateStatus="handleStatusUpdate"
              @updatePayload="handlePayloadUpdate" />
          </div>
        </main>
      </div>
    </div>
    <Toaster :class="toastClass" />

    <Modal :label="$t('Update Flow Details')" :isOpen=isOpenFormModal>
      <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-4">
        <form @submit.prevent="submitForm()" class="grid gap-x-6 gap-y-4 sm:grid-cols-6">
          <FormInput v-model="form.name" :error="form.errors.name" :name="$t('Name')" :type="'text'"
            :class="'sm:col-span-6'" />
          <FormTextArea v-model="form.description" :error="form.errors.description" :name="$t('Description')"
            :class="'sm:col-span-6'" />

          <div class="mt-4 flex">
            <button type="button" @click.self="isOpenFormModal = false"
              class="inline-flex justify-center rounded-md border border-transparent bg-slate-50 px-4 py-2 text-sm text-slate-500 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 mr-4">{{
                $t('Cancel') }}</button>
            <button
              :class="['inline-flex justify-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2', { 'opacity-50': form.processing }]"
              :disabled="form.processing">
              <svg v-if="form.processing" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                <path fill="currentColor"
                  d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5" />
                <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                  <animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite"
                    to="360 12 12" type="rotate" />
                </path>
              </svg>
              <span v-else>{{ $t('Save') }}</span>
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </AppLayout>
</template> -->


<!-- ================================================== NEW UI CODE ================================================== -->
<script setup lang="ts">
import axios from "axios"
import { ref, computed } from 'vue'
import AppLayout from "./../../../../resources/js/Pages/User/Layout/App.vue";
import { Link, useForm } from "@inertiajs/vue3"
import Modal from '../../../../resources/js/Components/Modal.vue';
import FormInput from '../../../../resources/js/Components/FormInput.vue';
import FormTextArea from '../../../../resources/js/Components/FormTextArea.vue';
import mainCanvas from './Components/main-canvas.vue'
import { Tabs, TabsContent, TabsTrigger, TabsList } from './Components/ui/tabs/index'
import { Button } from './Components/ui/button'
import { Toaster, useToast } from './Components/ui/toast'
import { cn } from "../../../../resources/js/lib/utils";
import { BookCheck, BookDashed, Mail, Save, UserPen, UserRoundPlus, UserRoundX, History, Globe, BotMessageSquare, Milestone } from "lucide-vue-next";
import { ScrollArea } from "./Components/ui/scroll-area";

const props = defineProps(['uuid', 'flow', 'contactGroups', 'contactFields', 'availableActions']);
const isOpenFormModal = ref(false);
const activeTab = ref('basic-nodes');

function handleOnDragStart(event: DragEvent, nodeType: any) {
  if (event.dataTransfer) {
    event.dataTransfer.setData('application/vueflow', nodeType)
    event.dataTransfer.effectAllowed = 'move'
  }
}

const { toast } = useToast()
const toastClass = ref('');
const status = ref(props.flow.status);
const metadata = ref(props.flow.metadata);
const loading = ref(false);

const handleClickSaveBtn = async () => {
  loading.value = true;

  try {
    const payload = {
      metadata: metadata.value,
    };

    const response = await axios.put(`/automation/flows/${props.uuid}`, payload);
    status.value = response.data.original.status;

    toastClass.value = response.data.original.success ? 'bg-green-700 text-white' : 'bg-red-700 text-white';
    status.value = response.data.original.status;

    toast({
      title: response.data.original.success ? 'Success' : 'Error',
      description: response.data.original.success ? response.data.original.message : response.data.original.errors,
    })

    loading.value = false;
  } catch (error) {
    loading.value = false;
  }
}

function handlePayloadUpdate(flowMetadata: any) {
  metadata.value = flowMetadata;
}

const handleClickPublishBtn = async (publish: any) => {
  loading.value = true;

  try {
    const payload = {
      metadata: metadata.value,
      publish: publish
    };

    const response = await axios.put(`/automation/flows/${props.uuid}`, payload);
    status.value = response.data.original.status;

    toastClass.value = response.data.original.success ? 'bg-green-700 text-white' : 'bg-red-700 text-white';
    status.value = response.data.original.status;

    toast({
      title: response.data.original.success ? 'Success' : 'Error',
      description: response.data.original.success ? response.data.original.message : response.data.original.errors,
    })

    loading.value = false;
  } catch (error) {
    loading.value = false;
  }
}

function handleStatusUpdate(newStatus: any) {
  status.value = newStatus;
}

const form = useForm({
  name: props.flow.name,
  description: props.flow.description
});

const submitForm = () => {
  form.put(`/automation/flows/${props.uuid}`, {
    preserveScroll: true,
    onSuccess: () => {
      isOpenFormModal.value = false;
      window.location.href = '/automation/flows';
    }
  })
}

// Get availableActions from backend props
const availableActions = props.availableActions || [
  'add_to_group',
  'remove_from_group',
  'update_contact',
  'send_email',
  'delay',
  'webhook',
  'conditional',
];



// Action list configuration
const getActionList = computed(() => {
  const actionConfigs = {
    'add_to_group': {
      type: 'add_to_group',
      dragType: 'action-add-to-group',
      title: 'Add to Group',
      desc: 'Add contact to group',
      bgColor: 'bg-blue-100',
      iconColor: 'text-blue-600',
      icon: UserRoundPlus
    },
    'remove_from_group': {
      type: 'remove_from_group',
      dragType: 'action-remove-from-group',
      title: 'Remove from Group',
      desc: 'Remove from group',
      bgColor: 'bg-red-100',
      iconColor: 'text-red-600 ml-1',
      icon: UserRoundX
    },
    'update_contact': {
      type: 'update_contact',
      dragType: 'action-update-contact',
      title: 'Update Contact',
      desc: 'Update contact info',
      bgColor: 'bg-purple-100',
      iconColor: 'text-purple-600',
      icon: UserPen
    },
    'send_email': {
      type: 'send_email',
      dragType: 'action-send-email',
      title: 'Send Email',
      desc: 'Send email message',
      bgColor: 'bg-green-100',
      iconColor: 'text-green-600',
      icon: Mail
    },
    'delay': {
      type: 'delay',
      dragType: 'action-delay',
      title: 'Delay',
      desc: 'Add time delay',
      bgColor: 'bg-pink-100',
      iconColor: 'text-pink-600',
      icon: History
    },
    'webhook': {
      type: 'webhook',
      dragType: 'action-webhook',
      title: 'Webhook',
      desc: 'Trigger webhook',
      bgColor: 'bg-purple-100',
      iconColor: 'text-purple-600',
      icon: Globe
    },
    'ai_response': {
      type: 'ai_response',
      dragType: 'action-ai-response',
      title: 'AI Response',
      desc: 'AI-powered reply',
      bgColor: 'bg-pink-100',
      iconColor: 'text-pink-600',
      icon: BotMessageSquare
    },
    'conditional': {
      type: 'conditional',
      dragType: 'action-conditional',
      title: 'Conditional',
      desc: 'Add condition',
      bgColor: 'bg-yellow-100',
      iconColor: 'text-yellow-600',
      icon: Milestone
    }
  };

  return availableActions
    .filter((action: any) => actionConfigs[action as keyof typeof actionConfigs])
    .map((action: any) => actionConfigs[action as keyof typeof actionConfigs]);
});
</script>

<template>
  <AppLayout>
    <div
      class="h-[calc(100vh-65px)] relative overflow-hidden flex flex-col bg-gradient-to-br from-slate-50 via-orange-50/20 to-slate-50">
      <!-- Modern Header -->
      <header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-1">
        <div class="px-6 py-4">
          <div class="flex items-center justify-between">
            <!-- Left Section -->
            <div class="flex items-center space-x-4">
              <div class="flex items-center space-x-3">
                <div
                  class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#ff5100] to-[#ff7a3d] flex items-center justify-center shadow-lg shadow-orange-500/25">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="text-white">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 5c0-1.414 0-2.121.44-2.56C3.878 2 4.585 2 6 2s2.121 0 2.56.44C9 2.878 9 3.585 9 5s0 2.121-.44 2.56C8.122 8 7.415 8 6 8s-2.121 0-2.56-.44C3 7.122 3 6.415 3 5m0 14c0-1.414 0-2.121.44-2.56C3.878 16 4.585 16 6 16s2.121 0 2.56.44C9 16.878 9 17.585 9 19s0 2.121-.44 2.56C8.122 22 7.415 22 6 22s-2.121 0-2.56-.44C3 21.122 3 20.415 3 19M15 7c0-1.414 0-2.121.44-2.56C15.878 4 16.585 4 18 4s2.121 0 2.56.44C21 4.878 21 5.585 21 7s0 2.121-.44 2.56c-.439.44-1.146.44-2.56.44s-2.121 0-2.56-.44C15 9.122 15 8.415 15 7m-9 9V8m12 2a3 3 0 0 1-3 3H9a3 3 0 0 0-3 3"
                      color="currentColor" />
                  </svg>
                </div>
                <div class="flex flex-col items-start justify-center">
                  <button @click="isOpenFormModal = true"
                    class="inline-flex items-center gap-2 text-xl font-bold text-gray-900 rounded-lg transition-colors">
                    {{ props.flow.name }}
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" class="mr-2">
                      <path fill="currentColor"
                        d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a.996.996 0 0 0 0-1.41l-2.34-2.34a.996.996 0 0 0-1.41 0l-1.83 1.83l3.75 3.75l1.83-1.83z" />
                    </svg>
                  </button>
                  <div> <span v-if="status === 'active'"
                      class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                      <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                      Active
                    </span>
                    <span v-else
                      class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                      <span class="w-1.5 h-1.5 bg-gray-500 rounded-full mr-1.5"></span>
                      Inactive
                    </span>
                  </div>
                </div>
              </div>

            </div>

            <!-- Right Section -->
            <div class="flex items-center space-x-3">
              <div v-if="!loading" class="flex items-center space-x-2">
                <Link href="/automation/flows"
                  class="inline-flex items-center px-4 py-2 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 rounded-lg font-semibold transition-all text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" class="mr-2">
                  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="2" d="M20 12H4m0 0l6-6m-6 6l6 6" />
                </svg>
                Back
                </Link>
                <Button variant="outline"
                  class="bg-blue-600 text-white flex justify-center items-center gap-2 font-semibold border-0 hover:bg-blue-700"
                  size="sm" @click="handleClickSaveBtn">
                  <Save class="h-5 w-5" />
                  Save
                </Button>
                <Button v-if="status === 'inactive'" variant="outline"
                  class="bg-green-600 text-white flex justify-center items-center gap-2 font-semibold border-0 hover:bg-green-700"
                  size="sm" @click="handleClickPublishBtn(1)">
                  <BookCheck class="h-5 w-5" />
                  Publish
                </Button>
                <Button v-else-if="status === 'active'" variant="outline"
                  class="bg-primary text-white flex justify-center items-center gap-2 font-semibold border-0 hover:bg-primary/90"
                  size="sm" @click="handleClickPublishBtn(0)">
                  <BookDashed class="h-5 w-5" />
                  Unpublish
                </Button>
              </div>
              <div v-else class="flex items-center space-x-2 px-4 py-2 bg-orange-50 text-orange-700 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="animate-spin">
                  <path fill="currentColor"
                    d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5" />
                  <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                    <animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite"
                      to="360 12 12" type="rotate" />
                  </path>
                </svg>
                <span class="text-sm font-medium">Updating...</span>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Content -->
      <main class="flex-1 flex h-full overflow-hidden">
        <!-- Sidebar -->
        <div class="w-80 h-[calc(100%-151px)] bg-white border-r border-gray-200 shadow-lg flex flex-col">
          <Tabs default-value="basic-nodes" class="flex-1 flex flex-col">
            <TabsList class="grid grid-cols-2 gap-0 bg-gradient-to-r from-gray-50 to-orange-50/50 p-2 border-b">
              <TabsTrigger value="basic-nodes" @click="activeTab = 'basic-nodes'"
                :class="cn('flex items-center justify-center space-x-2 py-3 px-4 rounded-md font-semibold text-sm transition-all',
                  activeTab === 'basic-nodes' ? 'bg-gradient-to-r from-[#ff5100] to-[#ff7a3d] text-white shadow-lg' : 'text-gray-600 hover:bg-white')">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                  <path fill="currentColor" d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z" />
                </svg>
                <span>Messages</span>
              </TabsTrigger>
              <TabsTrigger value="actions" @click="activeTab = 'actions'"
                :class="cn('flex items-center justify-center space-x-2 py-3 px-4 rounded-md font-semibold text-sm transition-all',
                  activeTab === 'actions' ? 'bg-gradient-to-r from-[#ff5100] to-[#ff7a3d] text-white shadow-lg' : 'text-gray-600 hover:bg-white')">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                  <path fill="currentColor"
                    d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89l.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7s-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42A8.954 8.954 0 0 0 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9z" />
                </svg>
                <span>Actions</span>
              </TabsTrigger>
            </TabsList>

            <TabsContent value="basic-nodes">
              <div class="p-4 space-y-3 fixed-scroll">
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-3 mb-4">
                  <p class="text-xs text-gray-700 flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                      class="text-[#ff5100] mr-2 mt-0.5 shrink-0">
                      <path fill="currentColor"
                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                    </svg>
                    <span>Drag nodes to canvas</span>
                  </p>
                </div>

                <div
                  class="group cursor-pointer rounded-xl bg-white hover:bg-orange-50 p-4 border-2 border-gray-200 hover:border-[#ff5100] shadow-sm hover:shadow-lg transition-all"
                  :draggable="true" @dragstart="handleOnDragStart($event, 'text')">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                      <div
                        class="w-10 h-10 text-blue-600 rounded-lg bg-blue-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 24 24">
                          <g fill="none">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="1.5"
                              d="M6 14.5h8M6 18h5.5" />
                            <path stroke="currentColor" stroke-width="1.5"
                              d="M13 2.5V5c0 2.357 0 3.536.732 4.268S15.643 10 18 10h4" />
                            <path fill="currentColor"
                              d="M2.75 10a.75.75 0 0 0-1.5 0zm18.5 4a.75.75 0 0 0 1.5 0zm-5.857-9.946l-.502.557zm3.959 3.563l-.502.557zm2.302 2.537l-.685.305zM3.172 20.828l.53-.53zm17.656 0l-.53-.53zM1.355 5.927a.75.75 0 0 0 1.493.146zm21.29 12.146a.75.75 0 1 0-1.493-.146zM14 21.25h-4v1.5h4zM2.75 14v-4h-1.5v4zm18.5-.437V14h1.5v-.437zM14.891 4.61l3.959 3.563l1.003-1.115l-3.958-3.563zm7.859 8.952c0-1.689.015-2.758-.41-3.714l-1.371.61c.266.598.281 1.283.281 3.104zm-3.9-5.389c1.353 1.218 1.853 1.688 2.119 2.285l1.37-.61c-.426-.957-1.23-1.66-2.486-2.79zM10.03 2.75c1.582 0 2.179.012 2.71.216l.538-1.4c-.852-.328-1.78-.316-3.248-.316zm5.865.746c-1.086-.977-1.765-1.604-2.617-1.93l-.537 1.4c.532.204.98.592 2.15 1.645zM10 21.25c-1.907 0-3.261-.002-4.29-.14c-1.005-.135-1.585-.389-2.008-.812l-1.06 1.06c.748.75 1.697 1.081 2.869 1.239c1.15.155 2.625.153 4.489.153zM1.25 14c0 1.864-.002 3.338.153 4.489c.158 1.172.49 2.121 1.238 2.87l1.06-1.06c-.422-.424-.676-1.004-.811-2.01c-.138-1.027-.14-2.382-.14-4.289zM14 22.75c1.864 0 3.338.002 4.489-.153c1.172-.158 2.121-.49 2.87-1.238l-1.06-1.06c-.424.422-1.004.676-2.01.811c-1.027.138-2.382.14-4.289.14zm-3.97-21.5c-1.875 0-3.356-.002-4.511.153c-1.177.158-2.129.49-2.878 1.238l1.06 1.06c.424-.422 1.005-.676 2.017-.811c1.033-.138 2.395-.14 4.312-.14zM2.848 6.073c.121-1.234.382-1.9.854-2.371l-1.06-1.06c-.836.834-1.153 1.919-1.287 3.285zm18.304 11.854c-.121 1.234-.383 1.9-.854 2.371l1.06 1.06c.836-.834 1.153-1.919 1.287-3.285z" />
                          </g>
                        </svg>
                      </div>
                      <div>
                        <h3 class="font-semibold text-sm text-gray-900">Simple Text</h3>
                        <p class="text-xs text-gray-500">Send text message</p>
                      </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                      <path fill="currentColor"
                        d="M9 19.23q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36">
                      </path>
                    </svg>
                  </div>
                </div>

                <div
                  class="group cursor-pointer rounded-xl bg-white hover:bg-orange-50 p-4 border-2 border-gray-200 hover:border-[#ff5100] shadow-sm hover:shadow-lg transition-all"
                  :draggable="true" @dragstart="handleOnDragStart($event, 'media')">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                      <div
                        class="w-10 h-10 text-purple-600 rounded-lg bg-purple-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                          <g fill="none" stroke="currentColor" stroke-width="1.5">
                            <path
                              d="M19.562 7a2.132 2.132 0 0 0-2.1-2.5H6.538a2.132 2.132 0 0 0-2.1 2.5M17.5 4.5c.028-.26.043-.389.043-.496a2 2 0 0 0-1.787-1.993C15.65 2 15.52 2 15.26 2H8.74c-.26 0-.391 0-.497.011a2 2 0 0 0-1.787 1.993c0 .107.014.237.043.496" />
                            <path stroke-linecap="round"
                              d="M21.194 16.793c-.35 2.48-.525 3.721-1.422 4.464s-2.22.743-4.867.743h-5.81c-2.646 0-3.97 0-4.867-.743s-1.072-1.983-1.422-4.464l-.422-3c-.447-3.164-.67-4.745.278-5.77C3.61 7 5.298 7 8.672 7h6.656c3.374 0 5.062 0 6.01 1.024c.749.809.767 1.966.521 3.976" />
                            <path
                              d="M14.581 13.616c.559.346.559 1.242 0 1.588l-3.371 2.09c-.543.337-1.21-.1-1.21-.794v-4.18c0-.693.667-1.13 1.21-.794z" />
                          </g>
                        </svg>
                      </div>
                      <div>
                        <h3 class="font-semibold text-sm text-gray-900">Media Files</h3>
                        <p class="text-xs text-gray-500">Send media</p>
                      </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                      <path fill="currentColor"
                        d="M9 19.23q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36">
                      </path>
                    </svg>
                  </div>
                </div>

                <div
                  class="group cursor-pointer rounded-xl bg-white hover:bg-orange-50 p-4 border-2 border-gray-200 hover:border-[#ff5100] shadow-sm hover:shadow-lg transition-all"
                  :draggable="true" @dragstart="handleOnDragStart($event, 'buttons')">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                      <div
                        class="w-10 h-10 text-green-600 rounded-lg bg-green-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 15 15">
                          <path fill="none" stroke="currentColor"
                            d="M5.5 10V8.5m0 0v-5a1 1 0 0 1 2 0v4h3.353c.91 0 1.647.737 1.647 1.647V10A4.5 4.5 0 0 1 8 14.5h-.5a4 4 0 0 1-4-4a2 2 0 0 1 2-2Zm3.5-3h2a2.5 2.5 0 0 0 0-5H4a2.5 2.5 0 0 0 0 5" />
                        </svg>
                      </div>
                      <div>
                        <h3 class="font-semibold text-sm text-gray-900">Buttons</h3>
                        <p class="text-xs text-gray-500">Add buttons</p>
                      </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                      <path fill="currentColor"
                        d="M9 19.23q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36">
                      </path>
                    </svg>
                  </div>
                </div>

                <div
                  class="group cursor-pointer rounded-xl bg-white hover:bg-orange-50 p-4 border-2 border-gray-200 hover:border-[#ff5100] shadow-sm hover:shadow-lg transition-all"
                  :draggable="true" @dragstart="handleOnDragStart($event, 'list')">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                      <div
                        class="w-10 h-10 text-pink-600 rounded-lg bg-pink-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                          absoluteStrokeWidth class="lucide lucide-list-icon lucide-list">
                          <path d="M3 5h.01" />
                          <path d="M3 12h.01" />
                          <path d="M3 19h.01" />
                          <path d="M8 5h13" />
                          <path d="M8 12h13" />
                          <path d="M8 19h13" />
                        </svg>
                      </div>
                      <div>
                        <h3 class="font-semibold text-sm text-gray-900">List</h3>
                        <p class="text-xs text-gray-500">Create list</p>
                      </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                      <path fill="currentColor"
                        d="M9 19.23q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36">
                      </path>
                    </svg>

                  </div>
                </div>
              </div>
            </TabsContent>

            <TabsContent value="actions">
              <div class="p-4 space-y-3 fixed-scroll">
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-3 mb-4">
                  <p class="text-xs text-gray-700 flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                      class="text-[#ff5100] mr-2 mt-0.5 shrink-0">
                      <path fill="currentColor"
                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                    </svg>
                    <span>Automate tasks</span>
                  </p>
                </div>

                <div v-for="action in getActionList" :key="action.type"
                  class="group cursor-pointer rounded-xl bg-white hover:bg-orange-50 p-4 border-2 border-gray-200 hover:border-[#ff5100] shadow-sm hover:shadow-lg transition-all"
                  :draggable="true" @dragstart="handleOnDragStart($event, action.dragType)">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                      <div
                        :class="`w-10 h-10 rounded-lg ${action.bgColor} flex items-center justify-center group-hover:scale-110 transition-transform`">
                        <component :is="action.icon" :class="`${action.iconColor}`" :size="20" :stroke-width="1.5"
                          absoluteStrokeWidth />
                      </div>
                      <div>
                        <h3 class="font-semibold text-sm text-gray-900">{{ action.title }}</h3>
                        <p class="text-xs text-gray-500">{{ action.desc }}</p>
                      </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                      <path fill="currentColor"
                        d="M9 19.23q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m-6-6q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36m6 0q-.508 0-.87-.36q-.36-.362-.36-.87t.36-.87t.87-.36t.87.36q.36.362.36.87t-.36.87t-.87.36">
                      </path>
                    </svg>
                  </div>
                </div>
              </div>
            </TabsContent>
          </Tabs>
        </div>

        <!-- Canvas Area -->
        <div class="relative h-full flex-1">
          <main-canvas :uuid="props.uuid" :flow="props.flow" :contactGroups="props.contactGroups"
            :contactFields="props.contactFields" @updateStatus="handleStatusUpdate"
            @updatePayload="handlePayloadUpdate" />
        </div>
      </main>
    </div>

    <Toaster :class="toastClass" />

    <!-- Edit Modal -->
    <Modal :label="$t('Update Flow Details')" :isOpen="isOpenFormModal">
      <div class="mt-5">
        <form @submit.prevent="submitForm()" class="space-y-5">
          <div class="flex items-center space-x-3 pb-4 border-b">
            <div
              class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#ff5100] to-[#ff7a3d] flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="text-white">
                <path fill="currentColor" d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z" />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-900">{{ $t('Edit Flow') }}</h3>
              <p class="text-sm text-gray-500">Update your automation details</p>
            </div>
          </div>

          <div class="space-y-4">
            <FormInput v-model="form.name" :error="form.errors.name" :name="$t('Name')" :type="'text'"
              :class="'w-full'" />
            <FormTextArea v-model="form.description" :error="form.errors.description" :name="$t('Description')"
              :class="'w-full'" />
          </div>

          <div class="flex items-center justify-end space-x-3 pt-4 border-t">
            <button type="button" @click.self="isOpenFormModal = false"
              class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-colors">
              {{ $t('Cancel') }}
            </button>
            <button type="submit"
              :class="['inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-[#ff5100] to-[#ff7a3d] text-white font-medium rounded-xl shadow-lg shadow-orange-500/30 transition-all', { 'opacity-50 cursor-not-allowed': form.processing }]"
              :disabled="form.processing">
              <svg v-if="form.processing" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                class="mr-2 animate-spin">
                <path fill="currentColor"
                  d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5" />
                <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                  <animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite"
                    to="360 12 12" type="rotate" />
                </path>
              </svg>
              <span>{{ form.processing ? $t('Saving...') : $t('Save') }}</span>
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </AppLayout>
</template>


<style>
.fixed-scroll {
  height: calc(100vh - 200px);
  overflow-y: auto;
  padding-bottom: 2rem;
}
</style>