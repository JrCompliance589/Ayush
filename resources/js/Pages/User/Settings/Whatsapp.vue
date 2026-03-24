<!-- <template>
    <SettingLayout :modules="props.modules">
        <div class="md:h-[90vh]">
            <div class="flex justify-center items-center mb-8">
                <div class="md:w-[60em]">
                    <div v-if="!settings?.whatsapp" class="bg-white border border-slate-200 rounded-lg py-2 text-sm mb-4">
                        <div class="flex items-center px-4 pt-2 pb-4">
                            <div class="w-[70%]">
                                <h2 class="text-[17px]">{{ $t('Setup Whatsapp Account') }}</h2>
                                <span class="flex items-center mt-1">
                                    {{ $t('Setup your integration to be able to receive and send messages via Whatsapp.') }}
                                </span>
                            </div>
                            <div class="ml-auto">
                                <EmbeddedSignupBtn v-if="embeddedSignupActive == 1" :appId="props.appId" :configId="props.configId" :graphAPIVersion="props.graphAPIVersion"/>
                                <button v-else  @click="openModal()" class="bg-primary text-white p-2 rounded-lg text-sm mt-5 flex px-3 w-fit">
                                    {{ $t('Setup whatsapp') }}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"><g opacity=".2"><path d="M12.206 5.848a1.5 1.5 0 0 1 2.113.192l3.333 4a1.5 1.5 0 1 1-2.304 1.92l-3.334-4a1.5 1.5 0 0 1 .192-2.112Z"/><path d="M12.206 16.152a1.5 1.5 0 0 1-.192-2.112l3.334-4a1.5 1.5 0 0 1 2.304 1.92l-3.333 4a1.5 1.5 0 0 1-2.113.192Z"/><path d="M16 11a1.5 1.5 0 0 1-1.5 1.5h-8a1.5 1.5 0 0 1 0-3h8A1.5 1.5 0 0 1 16 11Z"/></g><path d="M11.347 5.616a.5.5 0 0 1 .704.064l3.333 4a.5.5 0 0 1-.768.64l-3.333-4a.5.5 0 0 1 .064-.704Z"/><path d="M11.347 14.384a.5.5 0 0 1-.064-.704l3.333-4a.5.5 0 0 1 .768.64l-3.333 4a.5.5 0 0 1-.704.064Z"/><path d="M15.5 10a.5.5 0 0 1-.5.5H5a.5.5 0 0 1 0-1h20a.5.5 0 0 1 .5.5Z"/></g></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="settings?.whatsapp" class="bg-white border border-slate-200 rounded-lg py-2 text-sm mb-4">
                        <div class="grid grid-cols-4 items-center px-4 gap-x-4 py-2 border-b relative">
                            <div class="border-r">
                                <div>{{ $t('Display name') }}</div>
                                <div>{{ settings.whatsapp?.verified_name }}</div>
                            </div>
                            <div class="border-r">
                                <div>{{ $t('Connected number') }}</div>
                                <div>{{ settings.whatsapp?.display_phone_number }}</div>
                            </div>
                            <div class="border-r">
                                <div>{{ $t('Message limits') }}</div>
                                <div>{{ settings.whatsapp?.messaging_limit_tier ? settings.whatsapp?.messaging_limit_tier : 'N/A' }}</div>
                            </div>
                            <div>
                                <div>{{ $t('Number status') }}</div>
                                <div class="bg-slate-50 py-1 px-2 rounded-md w-[fit-content] text-xs">{{ settings.whatsapp?.number_status }}</div>
                            </div>
                            <button v-if="refreshLoading === false" @click="refreshData()" class="flex items-center absolute right-0 top-0 text-xs mr-1 space-x-2 p-1 px-2 bg-slate-50 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M12.079 2.25c-4.794 0-8.734 3.663-9.118 8.333H2a.75.75 0 0 0-.528 1.283l1.68 1.666a.75.75 0 0 0 1.056 0l1.68-1.666a.75.75 0 0 0-.528-1.283h-.893c.38-3.831 3.638-6.833 7.612-6.833a7.658 7.658 0 0 1 6.537 3.643a.75.75 0 1 0 1.277-.786A9.158 9.158 0 0 0 12.08 2.25m8.761 8.217a.75.75 0 0 0-1.054 0L18.1 12.133a.75.75 0 0 0 .527 1.284h.899c-.382 3.83-3.651 6.833-7.644 6.833a7.697 7.697 0 0 1-6.565-3.644a.75.75 0 1 0-1.277.788a9.197 9.197 0 0 0 7.842 4.356c4.808 0 8.765-3.66 9.15-8.333H22a.75.75 0 0 0 .527-1.284z"/></svg>
                                <span>{{ $t('Refresh') }}</span>
                            </button>
                            <button v-else class="flex items-center absolute right-0 top-0 text-xs mr-1 space-x-2 p-1 px-2 bg-slate-50 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5"/><path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z"><animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite" to="360 12 12" type="rotate"/></path></svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-4 items-center px-4 gap-x-4 py-2">
                            <div class="border-r">
                                <div>{{ $t('Whatsapp business ac ID') }}</div>
                                <div>{{ settings.whatsapp?.waba_id }}</div>
                            </div>
                            <div v-if="settings.whatsapp?.is_embedded_signup == 1" class="border-r">
                                <div>{{ $t('Phone verification status') }}</div>
                                <div class="bg-slate-50 py-1 px-2 rounded-md w-[fit-content] text-xs">{{ settings.whatsapp?.code_verification_status }}</div>
                            </div>
                            <div class="border-r">
                                <div>{{ $t('Quality rating') }}</div>
                                <div class="bg-slate-50 py-1 px-2 rounded-md w-[fit-content] text-xs">{{ settings.whatsapp?.quality_rating }}</div>
                            </div>
                            <div>
                                <div>{{ $t('Account status') }}</div>
                                <div class="bg-slate-50 py-1 px-2 rounded-md w-[fit-content] text-xs">{{ settings.whatsapp?.account_review_status }}</div>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submitForm2()" v-if="settings?.whatsapp" class="bg-white border border-slate-200 rounded-lg py-2 text-sm mb-4 pb-4">
                        <div class="flex items-center justify-between px-4 pt-2 pb-4">
                            <div>
                                <h2 class="text-[17px]">{{ $t('Business profile settings') }}</h2>
                                <span class="flex items-center mt-1">
                                    <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/></svg>
                                    {{ $t('Setup the Whatsapp business profile for your number') }}
                                </span>
                            </div>
                            <div>
                                <button v-if="settings?.whatsapp && settings?.whatsapp?.is_embedded_signup === 0" type="button" @click="openModal2()" class="bg-primary text-white p-2 rounded-lg text-sm mt-5 flex px-3 w-fit">
                                    {{ $t('Update token') }}
                                </button>
                            </div>
                        </div>
                        <div class="flex space-x-10 border-b w-full px-4 py-6">
                            <div class="w-[40%]">
                                <span class="text-slate-600">{{ $t('Whatsapp profile picture') }}</span>
                                <div class="text-xs text-slate-700 flex items-center">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/></svg>
                                    <span>{{ $t('Add/update your profile picture') }}</span>
                                </div>
                            </div>
                            <div class="w-[60%]">
                                <FormImageLogo v-model="form2.profile_picture_url" :name="''" :error="form2.errors.profile_picture_url" :label="$t('Upload logo')" :imageUrl="form2.profile_picture_url" :class="'col-span-4 ml-6'"/>
                                <div class="ml-6">{{ $t('Accepted formats: JPG/PNG') }}</div>
                                <div class="ml-6">{{ $t('Minimum dimensions: 192x192 pixels.') }}</div>
                            </div>
                        </div>
                        <div class="flex space-x-10 border-b w-full px-4 py-6">
                            <div class="w-[40%]">
                                <span class="text-slate-600">{{ $t('Business address') }}</span>
                                <div class="text-xs text-slate-700 flex items-center">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/></svg>
                                    <span>{{ $t('Specify your physical business address') }}</span>
                                </div>
                            </div>
                            <div class="w-[60%]">
                                <FormInput v-model="form2.address" :error="form2.errors.address" :name="''" :type="'text'" :class="'col-span-4'"/>
                            </div>
                        </div>
                        <div class="flex space-x-10 border-b w-full px-4 py-6">
                            <div class="w-[40%]">
                                <span class="text-slate-600">{{ $t('Business email') }}</span>
                                <div class="text-xs text-slate-700 flex items-center">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/></svg>
                                    <span>{{ $t('Add your business email address') }}</span>
                                </div>
                            </div>
                            <div class="w-[60%]">
                                <FormInput v-model="form2.email" :error="form2.errors.email" :name="''" :type="'email'" :class="'col-span-4'"/>
                            </div>
                        </div>
                        <div class="flex space-x-10 border-b w-full px-4 py-6">
                            <div class="w-[40%]">
                                <span class="text-slate-600">{{ $t('Business description') }}</span>
                                <div class="text-xs text-slate-700 flex items-center">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/></svg>
                                    <span>{{ $t('Edit your whatsapp business account description') }}</span>
                                </div>
                            </div>
                            <div class="w-[60%]">
                                <FormTextArea v-model="form2.description" :error="form2.errors.description" :name="''" :type="'text'" :class="'col-span-4'"/>
                            </div>
                        </div>
                        <div class="flex space-x-10 w-full px-4 py-6">
                            <div class="w-[40%]">
                                <span class="text-slate-600">{{ $t('Business industry') }}</span>
                                <div class="text-xs text-slate-700 flex items-center">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/></svg>
                                    <span>{{ $t('Specify your business vertical') }}</span>
                                </div>
                            </div>
                            <div class="w-[60%]">
                                <FormSelect v-model="form2.industry" :name="''" :type="'text'"  :options="options" :error="form2.errors.industry" :class="'col-span-4'"/>
                            </div>
                        </div>
                        <div class="flex px-4 pt-1 pb-2">
                            <div class="ml-auto">
                                <button type="submit" class="float-right rounded-md bg-primary px-3 py-2 text-sm text-white shadow-sm hover:shadow-md hover:bg-primary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600" :disabled="form2.processing">
                                    <svg v-if="form2.processing" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5"/><path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z"><animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite" to="360 12 12" type="rotate"/></path></svg>
                                    <span v-else>{{ $t('Save') }}</span>
                                </button>
                            </div>
                        </div>
                    </form>

                    <div v-if="settings?.whatsapp && settings?.whatsapp?.is_embedded_signup === 0" class="bg-white border border-slate-200 rounded-lg py-2 text-sm mb-4">
                        <div class="px-4 pt-2 pb-4">
                            <h2 class="text-[17px]">{{ $t('Meta webhook settings') }}</h2>
                            <span class="flex items-center mt-1">
                                <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/></svg>
                                {{ $t('Add these webhook settings to your facebook developer account') }}
                            </span>
                        </div>
                        <div class="flex space-x-10 border-b w-full px-4 py-4">
                            <div class="w-[40%]">
                                <span class="text-slate-600">{{ $t('Webhook url') }}</span>
                            </div>
                            <div class="text-left w-[60%]">
                                <span class="w-48 break-all">{{ currentURL + '/webhook/whatsapp/' +props.settings.identifier }}</span>
                            </div>
                        </div>
                        <div class="flex space-x-10 w-full px-4 py-4">
                            <div class="w-[40%]">
                                <span class="text-slate-600">{{ $t('Verify token') }}</span>
                            </div>
                            <div class="text-left w-[60%]">
                                <div class="text-left">{{ props.settings.identifier }}</div>
                            </div>
                        </div>
                    </div>

                    <div v-if="settings?.whatsapp" class="bg-white border border-slate-200 rounded-lg py-2 text-sm mb-20">
                        <div class="flex items-center px-4 pt-2 pb-4">
                            <div class="w-[60%]">
                                <h2 class="text-[17px]">{{ $t('Remove Whatsapp account') }}</h2>
                                <span class="flex items-center mt-1">
                                    {{ $t('This will completely delete your whatsapp integration. Your contacts & messages will be unaffected.') }}
                                </span>
                            </div>
                            <div class="w-[40%] ml-auto">
                                <button @click="deleteIntegration()" class="float-right rounded-md bg-red-700 px-3 py-2 text-sm text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{ $t('Delete integration')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :label="$t('Whatsapp API config')" :isOpen=isOpenFormModal>
            <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-4">
                <form @submit.prevent="submitForm()" class="grid gap-x-6 gap-y-4 sm:grid-cols-6">

                    <FormInput v-model="form.app_id" :error="form.errors.app_id" :name="$t('App ID')" :type="'text'" :class="'sm:col-span-6'"/>
                    <FormInput v-model="form.access_token" :error="form.errors.access_token" :name="$t('Access token')" :type="'text'" :class="'sm:col-span-6'"/>
                    <FormInput v-model="form.phone_number_id" :error="form.errors.phone_number_id" :name="$t('Phone number ID')" :type="'text'" :class="'sm:col-span-6'"/>
                    <FormInput v-model="form.waba_id" :error="form.errors.waba_id" :name="$t('Whatsapp business account ID')" :type="'text'" :class="'sm:col-span-6'"/>

                    <div class="mt-4 flex">
                        <button type="button" @click.self="isOpenFormModal = false" class="inline-flex justify-center rounded-md border border-transparent bg-slate-50 px-4 py-2 text-sm text-slate-500 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 mr-4">{{ $t('Cancel') }}</button>
                        <button 
                            :class="['inline-flex justify-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2', { 'opacity-50': form.processing }]"
                            :disabled="form.processing"
                        >
                            <svg v-if="form.processing" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5"/><path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z"><animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite" to="360 12 12" type="rotate"/></path></svg>
                            <span v-else>{{ $t('Save') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal v-if="settings?.whatsapp && settings?.whatsapp?.is_embedded_signup === 0" :label="$t('Whatsapp API config')" :isOpen=isOpenForm2Modal>
            <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-4">
                <form @submit.prevent="submitForm3()" class="grid gap-x-6 gap-y-4 sm:grid-cols-6">
                    <FormInput v-model="form3.access_token" :error="form3.errors.access_token" :name="$t('Access token')" :type="'text'" :class="'sm:col-span-6'"/>

                    <div class="mt-4 flex">
                        <button type="button" @click.self="isOpenForm2Modal = false" class="inline-flex justify-center rounded-md border border-transparent bg-slate-50 px-4 py-2 text-sm text-slate-500 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 mr-4">{{ $t('Cancel') }}</button>
                        <button 
                            :class="['inline-flex justify-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2', { 'opacity-50': form.processing }]"
                            :disabled="form3.processing"
                        >
                            <svg v-if="form3.processing" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5"/><path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z"><animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite" to="360 12 12" type="rotate"/></path></svg>
                            <span v-else>{{ $t('Save') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </SettingLayout>
</template>
<script setup>
    import SettingLayout from "./Layout.vue";
    import { ref } from 'vue';
    import EmbeddedSignupBtn from '@/Components/EmbeddedSignupBtn.vue';
    import FormModal from '@/Components/FormModal.vue';
    import FormImageLogo from '@/Components/FormImageLogo.vue';
    import FormInput from '@/Components/FormInput.vue';
    import FormSelect from '@/Components/FormSelect.vue';
    import FormTextArea from '@/Components/FormTextArea.vue';
    import Modal from '@/Components/Modal.vue';
    import { trans } from 'laravel-vue-i18n';
    import { router, useForm } from "@inertiajs/vue3";

    const props = defineProps(['settings', 'embeddedSignupActive', 'appId', 'configId', 'graphAPIVersion', 'modules']);
    const statusView = ref(false);
    const config = ref(props.settings.metadata);
    const currentURL = ref(window.location.origin);
    const isOpenFormModal = ref(false);
    const isOpenForm2Modal = ref(false);
    const settings = ref(config.value ? JSON.parse(config.value) : null);
    const refreshLoading = ref(false);
    const form = useForm({
        app_id: settings.value && settings.value.whatsapp ? settings.value.whatsapp.app_id : null,
        access_token: settings.value && settings.value.whatsapp ? settings.value.whatsapp.access_token : null,
        phone_number_id: settings.value && settings.value.whatsapp ? settings.value.whatsapp.phone_number_id : null,
        waba_id: settings.value && settings.value.whatsapp ? settings.value.whatsapp.waba_id : null,
    });

    const form2 = useForm({
        profile_picture_url: settings.value && settings.value.whatsapp ? settings.value?.whatsapp?.business_profile?.profile_picture_url : null,
        description: settings.value && settings.value.whatsapp ? settings.value?.whatsapp?.business_profile?.description : null,
        address: settings.value && settings.value.whatsapp ? settings.value?.whatsapp?.business_profile?.address : null,
        email: settings.value && settings.value.whatsapp ? settings.value?.whatsapp?.business_profile?.email : null,
        industry: settings.value && settings.value.whatsapp ? settings.value?.whatsapp?.business_profile?.industry : null,
        websites: []
    });

    const form3 = useForm({
        access_token: settings.value && settings.value.whatsapp ? settings.value.whatsapp.access_token : null,
    });

    const options = [
        { label: 'Automotive', value: 'AUTO' },
        { label: 'Beauty, spa and salon', value: 'BEAUTY' },
        { label: 'Clothing', value: 'APPAREL' },
        { label: 'Education', value: 'EDU' },
        { label: 'Entertainment', value: 'ENTERTAIN' },
        { label: 'Event planning and service', value: 'EVENT_PLAN' },
        { label: 'Finance and banking', value: 'FINANCE' },
        { label: 'Food and groceries', value: 'GROCERY' },
        { label: 'Public service', value: 'GOVT' },
        { label: 'Hotel and lodging', value: 'HOTEL' },
        { label: 'Medical and health', value: 'HEALTH' },
        { label: 'Charity', value: 'NONPROFIT' },
        { label: 'Professional services', value: 'PROF_SERVICES' },
        { label: 'Shopping and retail', value: 'RETAIL' },
        { label: 'Travel and transportation', value: 'TRAVEL' },
        { label: 'Restaurant', value: 'RESTAURANT' },
        { label: 'Not a business', value: 'NOT_A_BIZ' },
        { label: 'Undefined', value: 'UNDEFINED' },
        { label: 'Other', value: 'OTHER' },
    ]

    function openModal() {
        isOpenFormModal.value = true;
    }

    function openModal2() {
        isOpenForm2Modal.value = true;
    }

    const capitalizeString = (string) => {
        return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
    };

    const toggleStatusView = () => {
        statusView.value = !statusView.value;
    }

    const submitForm = () => {
        form.post('/settings/whatsapp', {
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => {
                isOpenFormModal.value = false
            }
        })
    }

    const submitForm2 = () => {
        form2.post('/settings/whatsapp/business-profile', {
            preserveScroll: true,
        })
    }

    const submitForm3 = () => {
        form3.post('/settings/whatsapp/token', {
            preserveScroll: true,
            onSuccess: () => {
                isOpenForm2Modal.value = false
            }
        })
    }

    const refreshData = () => {
        refreshLoading.value = true;

        router.visit(`/settings/whatsapp/refresh`, {
            method: 'get',
            preserveState: true,
            onFinish: () => {
                refreshLoading.value = false;
            },
            onSuccess: () => {
                router.visit('/settings/whatsapp', {
                    preserveState: false,
                });
            },
        })
    }

    const deleteIntegration = () => {
        router.delete(`/settings/whatsapp/business-profile`, {
            onBefore: () => confirm('Are you sure you want to delete your integration?'),
            preserveState: true,
            onSuccess: () => {
                router.visit('/settings/whatsapp', {
                    preserveState: false,
                });
            },
        })
    }
</script> -->

<!-- ========================================= NEW UI CODE ==================================== -->

<template>
    <SettingLayout :modules="props.modules">
        <div class="space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <div class="w-full mx-auto space-y-6">

                <!-- Setup WhatsApp Card (when not connected) -->
                <div v-if="!settings?.whatsapp"
                    class="group bg-gradient-to-br from-white to-green-50/30 rounded-2xl border border-slate-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="relative p-8">
                        <!-- Decorative Elements -->
                        <div
                            class="absolute top-0 right-0 w-48 h-48 bg-gradient-to-br from-green-500/10 to-transparent rounded-full blur-3xl">
                        </div>
                        <div
                            class="absolute -bottom-8 -left-8 w-32 h-32 bg-gradient-to-tr from-[#ff5100]/10 to-transparent rounded-full blur-2xl">
                        </div>

                        <div class="relative z-10 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="p-4 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg shadow-green-500/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                        class="text-white">
                                        <path fill="currentColor"
                                            d="M19.05 4.91A9.816 9.816 0 0 0 12.04 2c-5.46 0-9.91 4.45-9.91 9.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21c5.46 0 9.91-4.45 9.91-9.91c0-2.65-1.03-5.14-2.9-7.01m-7.01 15.24c-1.48 0-2.93-.4-4.2-1.15l-.3-.18l-3.12.82l.83-3.04l-.2-.31a8.264 8.264 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24c2.2 0 4.27.86 5.82 2.42a8.183 8.183 0 0 1 2.41 5.83c.02 4.54-3.68 8.23-8.22 8.23m4.52-6.16c-.25-.12-1.47-.72-1.69-.81c-.23-.08-.39-.12-.56.12c-.17.25-.64.81-.78.97c-.14.17-.29.19-.54.06c-.25-.12-1.05-.39-1.99-1.23c-.74-.66-1.23-1.47-1.38-1.72c-.14-.25-.02-.38.11-.51c.11-.11.25-.29.37-.43s.17-.25.25-.41c.08-.17.04-.31-.02-.43s-.56-1.34-.76-1.84c-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31c-.22.25-.86.85-.86 2.07c0 1.22.89 2.4 1.01 2.56c.12.17 1.75 2.67 4.23 3.74c.59.26 1.05.41 1.41.52c.59.19 1.13.16 1.56.1c.48-.07 1.47-.6 1.67-1.18c.21-.58.21-1.07.14-1.18s-.22-.16-.47-.28" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-slate-900 mb-1">{{ $t('Setup Whatsapp Account')
                                    }}</h2>
                                    <p class="text-slate-600">{{ $t(`Setup your integration to be able to receive and
                                        send messages via Whatsapp.`) }}</p>
                                </div>
                            </div>
                            <div>
                                <EmbeddedSignupBtn v-if="embeddedSignupActive == 1" :appId="props.appId"
                                    :configId="props.configId" :graphAPIVersion="props.graphAPIVersion" />
                                <button v-else @click="openModal()"
                                    class="group flex items-center gap-2 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white px-6 py-3 rounded-xl font-semibold shadow-lg shadow-[#ff5100]/30 hover:shadow-xl hover:shadow-[#ff5100]/40 hover:scale-105 transition-all duration-200">
                                    {{ $t('Setup whatsapp') }}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                                        class="group-hover:translate-x-1 transition-transform">
                                        <path fill="currentColor" fill-rule="evenodd"
                                            d="M12.206 5.848a1.5 1.5 0 0 1 2.113.192l3.333 4a1.5 1.5 0 1 1-2.304 1.92l-3.334-4a1.5 1.5 0 0 1 .192-2.112Z"
                                            clip-rule="evenodd" opacity=".2" />
                                        <path fill="currentColor" fill-rule="evenodd"
                                            d="M12.206 16.152a1.5 1.5 0 0 1-.192-2.112l3.334-4a1.5 1.5 0 0 1 2.304 1.92l-3.333 4a1.5 1.5 0 0 1-2.113.192Z"
                                            clip-rule="evenodd" opacity=".2" />
                                        <path fill="currentColor" fill-rule="evenodd"
                                            d="M16 11a1.5 1.5 0 0 1-1.5 1.5h-8a1.5 1.5 0 0 1 0-3h8A1.5 1.5 0 0 1 16 11Z"
                                            clip-rule="evenodd" opacity=".2" />
                                        <path fill="currentColor" fill-rule="evenodd"
                                            d="M11.347 5.616a.5.5 0 0 1 .704.064l3.333 4a.5.5 0 0 1-.768.64l-3.333-4a.5.5 0 0 1 .064-.704Z"
                                            clip-rule="evenodd" />
                                        <path fill="currentColor" fill-rule="evenodd"
                                            d="M11.347 14.384a.5.5 0 0 1-.064-.704l3.333-4a.5.5 0 0 1 .768.64l-3.333 4a.5.5 0 0 1-.704.064Z"
                                            clip-rule="evenodd" />
                                        <path fill="currentColor" fill-rule="evenodd"
                                            d="M15.5 10a.5.5 0 0 1-.5.5H5a.5.5 0 0 1 0-1h10a.5.5 0 0 1 .5.5Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WhatsApp Connection Status Card (when connected) -->
                <div v-if="settings?.whatsapp"
                    class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300">
                    <div
                        class="bg-gradient-to-r from-green-50 to-emerald-50/30 px-6 py-4 border-b border-slate-200 relative">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="p-2 bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg shadow-green-500/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-white">
                                        <path fill="currentColor"
                                            d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8s8 3.59 8 8s-3.59 8-8 8m4.59-12.42L10 14.17l-2.59-2.58L6 13l4 4l8-8z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-slate-900">{{ $t('WhatsApp Connected') }}</h2>
                                    <p class="text-sm text-slate-600">{{ $t('Your account is successfully configured')
                                    }}</p>
                                </div>
                            </div>
                            <button v-if="refreshLoading === false" @click="refreshData()"
                                class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-[#ff5100] hover:text-[#ff5100] transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M12.079 2.25c-4.794 0-8.734 3.663-9.118 8.333H2a.75.75 0 0 0-.528 1.283l1.68 1.666a.75.75 0 0 0 1.056 0l1.68-1.666a.75.75 0 0 0-.528-1.283h-.893c.38-3.831 3.638-6.833 7.612-6.833a7.658 7.658 0 0 1 6.537 3.643a.75.75 0 1 0 1.277-.786A9.158 9.158 0 0 0 12.08 2.25m8.761 8.217a.75.75 0 0 0-1.054 0L18.1 12.133a.75.75 0 0 0 .527 1.284h.899c-.382 3.83-3.651 6.833-7.644 6.833a7.697 7.697 0 0 1-6.565-3.644a.75.75 0 1 0-1.277.788a9.197 9.197 0 0 0 7.842 4.356c4.808 0 8.765-3.66 9.15-8.333H22a.75.75 0 0 0 .527-1.284z" />
                                </svg>
                                {{ $t('Refresh') }}
                            </button>
                            <button v-else
                                class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-400">
                                <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                        opacity=".5" />
                                    <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                        <animateTransform attributeName="transform" dur="1s" from="0 12 12"
                                            repeatCount="indefinite" to="360 12 12" type="rotate" />
                                    </path>
                                </svg>
                                {{ $t('Refreshing...') }}
                            </button>
                        </div>
                    </div>

                    <!-- Connection Details Grid -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                            <div
                                class="p-4 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100/50 border border-slate-200">
                                <div class="text-xs font-medium text-slate-500 mb-1">{{ $t('Display name') }}</div>
                                <div class="text-sm font-semibold text-slate-900 truncate">{{
                                    settings.whatsapp?.verified_name }}</div>
                            </div>
                            <div
                                class="p-4 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100/50 border border-blue-200">
                                <div class="text-xs font-medium text-blue-600 mb-1">{{ $t('Connected number') }}</div>
                                <div class="text-sm font-semibold text-blue-900">{{
                                    settings.whatsapp?.display_phone_number }}</div>
                            </div>
                            <div
                                class="p-4 rounded-xl bg-gradient-to-br from-purple-50 to-purple-100/50 border border-purple-200">
                                <div class="text-xs font-medium text-purple-600 mb-1">{{ $t('Message limits') }}</div>
                                <div class="text-sm font-semibold text-purple-900">{{
                                    settings.whatsapp?.messaging_limit_tier ? settings.whatsapp?.messaging_limit_tier :
                                        'N/A' }}</div>
                            </div>
                            <div
                                class="p-4 rounded-xl bg-gradient-to-br from-green-50 to-green-100/50 border border-green-200">
                                <div class="text-xs font-medium text-green-600 mb-1">{{ $t('Number status') }}</div>
                                <div
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-semibold">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                    {{ settings.whatsapp?.number_status }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div
                                class="p-4 rounded-xl bg-gradient-to-br from-orange-50 to-orange-100/50 border border-orange-200">
                                <div class="text-xs font-medium text-orange-600 mb-1">{{ $t('Whatsapp business ac ID')
                                }}</div>
                                <div class="text-sm font-semibold text-orange-900 truncate">{{
                                    settings.whatsapp?.waba_id }}</div>
                            </div>
                            <div v-if="settings.whatsapp?.is_embedded_signup == 1"
                                class="p-4 rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100/50 border border-indigo-200">
                                <div class="text-xs font-medium text-indigo-600 mb-1">{{ $t('Phone verification status')
                                }}</div>
                                <div
                                    class="inline-flex px-2.5 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold">
                                    {{ settings.whatsapp?.code_verification_status }}
                                </div>
                            </div>
                            <div
                                class="p-4 rounded-xl bg-gradient-to-br from-pink-50 to-pink-100/50 border border-pink-200">
                                <div class="text-xs font-medium text-pink-600 mb-1">{{ $t('Quality rating') }}</div>
                                <div
                                    class="inline-flex px-2.5 py-1 bg-pink-100 text-pink-700 rounded-lg text-xs font-semibold">
                                    {{ settings.whatsapp?.quality_rating }}
                                </div>
                            </div>
                            <div
                                class="p-4 rounded-xl bg-gradient-to-br from-cyan-50 to-cyan-100/50 border border-cyan-200">
                                <div class="text-xs font-medium text-cyan-600 mb-1">{{ $t('Account status') }}</div>
                                <div
                                    class="inline-flex px-2.5 py-1 bg-cyan-100 text-cyan-700 rounded-lg text-xs font-semibold">
                                    {{ settings.whatsapp?.account_review_status }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Profile Settings Form -->
                <form v-if="settings?.whatsapp" @submit.prevent="submitForm2()" class="space-y-6">
                    <div
                        class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" class="text-white">
                                            <path fill="currentColor"
                                                d="M12 2A10 10 0 0 0 2 12a10 10 0 0 0 10 10a10 10 0 0 0 10-10A10 10 0 0 0 12 2M7.07 18.28c.43-.9 3.05-1.78 4.93-1.78s4.5.88 4.93 1.78A7.893 7.893 0 0 1 12 20c-1.86 0-3.57-.64-4.93-1.72m11.29-1.45c-1.43-1.74-4.9-2.33-6.36-2.33s-4.93.59-6.36 2.33A7.95 7.95 0 0 1 4 12c0-4.41 3.59-8 8-8s8 3.59 8 8c0 1.82-.62 3.49-1.64 4.83M12 6c-1.94 0-3.5 1.56-3.5 3.5S10.06 13 12 13s3.5-1.56 3.5-3.5S13.94 6 12 6m0 5a1.5 1.5 0 0 1-1.5-1.5A1.5 1.5 0 0 1 12 8a1.5 1.5 0 0 1 1.5 1.5A1.5 1.5 0 0 1 12 11" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold text-slate-900">{{ $t('Business profile settings')
                                        }}</h2>
                                        <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" class="text-[#ff5100]">
                                                <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                            </svg>
                                            <span>{{ $t('Setup the Whatsapp business profile for your number') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <button v-if="settings?.whatsapp && settings?.whatsapp?.is_embedded_signup === 0"
                                    type="button" @click="openModal2()"
                                    class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white rounded-xl text-sm font-semibold shadow-lg shadow-[#ff5100]/20 hover:shadow-xl hover:shadow-[#ff5100]/30 hover:scale-105 transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8s8 3.59 8 8s-3.59 8-8 8m4.59-12.42L10 14.17l-2.59-2.58L6 13l4 4l8-8z" />
                                    </svg>
                                    {{ $t('Update token') }}
                                </button>
                            </div>
                        </div>

                        <div class="p-6 space-y-6">
                            <!-- Profile Picture -->
                            <div class="grid md:grid-cols-5 gap-6 items-start">
                                <div class="md:col-span-2 space-y-1">
                                    <label class="text-sm font-semibold text-slate-900">{{ $t(`Whatsapp profile
                                        picture`) }}</label>
                                    <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                            <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                        </svg>
                                        <span>{{ $t('Add/update your profile picture') }}</span>
                                    </div>
                                </div>
                                <div class="md:col-span-3 space-y-2">
                                    <FormImageLogo v-model="form2.profile_picture_url" :name="''"
                                        :error="form2.errors.profile_picture_url" :label="$t('Upload logo')"
                                        :imageUrl="form2.profile_picture_url" :class="''" />
                                    <div class="text-xs text-slate-600 space-y-1">
                                        <div class="flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                viewBox="0 0 24 24" class="text-[#ff5100]">
                                                <path fill="currentColor"
                                                    d="m9.55 18l-5.7-5.7l1.425-1.425L9.55 15.15l9.175-9.175L20.15 7.4z" />
                                            </svg>
                                            {{ $t('Accepted formats: JPG/PNG') }}
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                viewBox="0 0 24 24" class="text-[#ff5100]">
                                                <path fill="currentColor"
                                                    d="m9.55 18l-5.7-5.7l1.425-1.425L9.55 15.15l9.175-9.175L20.15 7.4z" />
                                            </svg>
                                            {{ $t('Minimum dimensions: 192x192 pixels.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-slate-100"></div>

                            <!-- Business Address -->
                            <div class="grid md:grid-cols-5 gap-6 items-start">
                                <div class="md:col-span-2 space-y-1">
                                    <label class="text-sm font-semibold text-slate-900">{{ $t('Business address')
                                    }}</label>
                                    <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                            <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                        </svg>
                                        <span>{{ $t('Specify your physical business address') }}</span>
                                    </div>
                                </div>
                                <div class="md:col-span-3">
                                    <FormInput v-model="form2.address" :error="form2.errors.address" :name="''"
                                        :type="'text'" :class="''" />
                                </div>
                            </div>

                            <div class="border-t border-slate-100"></div>

                            <!-- Business Email -->
                            <div class="grid md:grid-cols-5 gap-6 items-start">
                                <div class="md:col-span-2 space-y-1">
                                    <label class="text-sm font-semibold text-slate-900">{{ $t('Business email')
                                    }}</label>
                                    <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                            <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                        </svg>
                                        <span>{{ $t('Add your business email address') }}</span>
                                    </div>
                                </div>
                                <div class="md:col-span-3">
                                    <FormInput v-model="form2.email" :error="form2.errors.email" :name="''"
                                        :type="'email'" :class="''" />
                                </div>
                            </div>

                            <div class="border-t border-slate-100"></div>

                            <!-- Business Description -->
                            <div class="grid md:grid-cols-5 gap-6 items-start">
                                <div class="md:col-span-2 space-y-1">
                                    <label class="text-sm font-semibold text-slate-900">{{ $t('Business description')
                                    }}</label>
                                    <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                            <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                        </svg>
                                        <span>{{ $t('Edit your whatsapp business account description') }}</span>
                                    </div>
                                </div>
                                <div class="md:col-span-3">
                                    <FormTextArea v-model="form2.description" :error="form2.errors.description"
                                        :name="''" :type="'text'" :class="''" />
                                </div>
                            </div>

                            <div class="border-t border-slate-100"></div>

                            <!-- Business Industry -->
                            <div class="grid md:grid-cols-5 gap-6 items-start">
                                <div class="md:col-span-2 space-y-1">
                                    <label class="text-sm font-semibold text-slate-900">{{ $t('Business industry')
                                    }}</label>
                                    <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                            <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                        </svg>
                                        <span>{{ $t('Specify your business vertical') }}</span>
                                    </div>
                                </div>
                                <div class="md:col-span-3">
                                    <FormSelect v-model="form2.industry" :name="''" :type="'text'" :options="options"
                                        :error="form2.errors.industry" :class="''" />
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end">
                            <button type="submit"
                                class="group px-8 py-3 bg-primary text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/10 hover:shadow-xl hover:shadow-[#ff5100]/20 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                                :disabled="form2.processing">
                                <span v-if="form2.processing" class="flex items-center gap-2">
                                    <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
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
                                    {{ $t('Save') }}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="m9.55 18l-5.7-5.7l1.425-1.425L9.55 15.15l9.175-9.175L20.15 7.4z" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Meta Webhook Settings (non-embedded signup) -->
                <div v-if="settings?.whatsapp && settings?.whatsapp?.is_embedded_signup === 0"
                    class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300">
                    <div class="bg-gradient-to-r from-slate-50 to-blue-50/30 px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg shadow-blue-500/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Meta webhook settings') }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Add these webhook settings to your facebook developer account')
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <!-- Webhook URL -->
                        <div
                            class="p-4 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100/50 border border-slate-200">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-slate-900 mb-2">{{ $t('Webhook url') }}</div>
                                    <div
                                        class="font-mono text-sm text-slate-700 bg-white px-3 py-2 rounded-lg border border-slate-200 break-all">
                                        {{ currentURL + '/webhook/whatsapp/' + props.settings.identifier }}
                                    </div>
                                </div>
                                <button type="button"
                                    @click="copyToClipboard(currentURL + '/webhook/whatsapp/' + props.settings.identifier)"
                                    class="shrink-0 p-2 hover:bg-white rounded-lg transition-colors group"
                                    title="Copy to clipboard">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-slate-600 group-hover:text-[#ff5100]">
                                        <path fill="currentColor"
                                            d="M9 18q-.825 0-1.412-.587T7 16V4q0-.825.588-1.412T9 2h9q.825 0 1.413.588T20 4v12q0 .825-.587 1.413T18 18zm0-2h9V4H9zm-4 6q-.825 0-1.412-.587T3 20V7q0-.425.288-.712T4 6t.713.288T5 7v13h10q.425 0 .713.288T16 21t-.288.713T15 22zm4-6V4z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Verify Token -->
                        <div
                            class="p-4 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100/50 border border-slate-200">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-slate-900 mb-2">{{ $t('Verify token') }}
                                    </div>
                                    <div
                                        class="font-mono text-sm text-slate-700 bg-white px-3 py-2 rounded-lg border border-slate-200 break-all">
                                        {{ props.settings.identifier }}
                                    </div>
                                </div>
                                <button type="button" @click="copyToClipboard(props.settings.identifier)"
                                    class="shrink-0 p-2 hover:bg-white rounded-lg transition-colors group"
                                    title="Copy to clipboard">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        class="text-slate-600 group-hover:text-[#ff5100]">
                                        <path fill="currentColor"
                                            d="M9 18q-.825 0-1.412-.587T7 16V4q0-.825.588-1.412T9 2h9q.825 0 1.413.588T20 4v12q0 .825-.587 1.413T18 18zm0-2h9V4H9zm-4 6q-.825 0-1.412-.587T3 20V7q0-.425.288-.712T4 6t.713.288T5 7v13h10q.425 0 .713.288T16 21t-.288.713T15 22zm4-6V4z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Remove WhatsApp Integration -->
                <div v-if="settings?.whatsapp"
                    class="bg-gradient-to-br from-red-50 to-red-100/50 rounded-2xl border border-red-200 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-red-100 rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        class="text-red-600">
                                        <path fill="currentColor"
                                            d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6zM8 9h8v10H8zm7.5-5l-1-1h-5l-1 1H5v2h14V4z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-red-900 mb-1">{{ $t('Remove Whatsapp account') }}
                                    </h2>
                                    <p class="text-sm text-red-700">{{ $t(`This will completely delete your whatsapp
                                        integration.Your contacts & messages will be unaffected.`) }}</p>
                                </div>
                            </div>
                            <button @click="deleteIntegration()"
                                class="group px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-200">
                                <span class="flex items-center gap-2 text-sm text-nowrap">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6zM8 9h8v10H8zm7.5-5l-1-1h-5l-1 1H5v2h14V4z" />
                                    </svg>
                                    {{ $t('Delete integration') }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for API Config -->
        <Modal :label="$t('Whatsapp API config')" :isOpen="isOpenFormModal">
            <div class="mt-5 space-y-4">
                <form @submit.prevent="submitForm()" class="space-y-4">
                    <FormInput v-model="form.app_id" :error="form.errors.app_id" :name="$t('App ID')" :type="'text'"
                        :class="''" />
                    <FormInput v-model="form.access_token" :error="form.errors.access_token" :name="$t('Access token')"
                        :type="'text'" :class="''" />
                    <FormInput v-model="form.phone_number_id" :error="form.errors.phone_number_id"
                        :name="$t('Phone number ID')" :type="'text'" :class="''" />
                    <FormInput v-model="form.waba_id" :error="form.errors.waba_id"
                        :name="$t('Whatsapp business account ID')" :type="'text'" :class="''" />

                    <div class="flex gap-3 pt-4">
                        <button type="button" @click.self="isOpenFormModal = false"
                            class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition-colors">
                            {{ $t('Cancel') }}
                        </button>
                        <button type="submit"
                            :class="['flex-1 px-4 py-2.5 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl hover:shadow-lg transition-all', { 'opacity-50': form.processing }]"
                            :disabled="form.processing">
                            <span v-if="form.processing" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
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
                            <span v-else>{{ $t('Save') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal for Token Update -->
        <Modal v-if="settings?.whatsapp && settings?.whatsapp?.is_embedded_signup === 0"
            :label="$t('Update Access Token')" :isOpen="isOpenForm2Modal">
            <div class="mt-5 space-y-4">
                <form @submit.prevent="submitForm3()" class="space-y-4">
                    <FormInput v-model="form3.access_token" :error="form3.errors.access_token"
                        :name="$t('Access token')" :type="'text'" :class="''" />

                    <div class="flex gap-3 pt-4">
                        <button type="button" @click.self="isOpenForm2Modal = false"
                            class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition-colors">
                            {{ $t('Cancel') }}
                        </button>
                        <button type="submit"
                            :class="['flex-1 px-4 py-2.5 bg-gradient-to-r from-[#ff5100] to-[#ff7340] text-white font-semibold rounded-xl hover:shadow-lg transition-all', { 'opacity-50': form3.processing }]"
                            :disabled="form3.processing">
                            <span v-if="form3.processing" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
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
                            <span v-else>{{ $t('Save') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </SettingLayout>
</template>

<script setup>
import SettingLayout from "./Layout.vue";
import { ref } from 'vue';
import EmbeddedSignupBtn from '@/Components/EmbeddedSignupBtn.vue';
import FormImageLogo from '@/Components/FormImageLogo.vue';
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import FormTextArea from '@/Components/FormTextArea.vue';
import Modal from '@/Components/Modal.vue';
import { router, useForm } from "@inertiajs/vue3";

const props = defineProps(['settings', 'embeddedSignupActive', 'appId', 'configId', 'graphAPIVersion', 'modules']);
const config = ref(props.settings.metadata);
const currentURL = ref(window.location.origin);
const isOpenFormModal = ref(false);
const isOpenForm2Modal = ref(false);
const settings = ref(config.value ? JSON.parse(config.value) : null);
const refreshLoading = ref(false);

const form = useForm({
    app_id: settings.value && settings.value.whatsapp ? settings.value.whatsapp.app_id : null,
    access_token: settings.value && settings.value.whatsapp ? settings.value.whatsapp.access_token : null,
    phone_number_id: settings.value && settings.value.whatsapp ? settings.value.whatsapp.phone_number_id : null,
    waba_id: settings.value && settings.value.whatsapp ? settings.value.whatsapp.waba_id : null,
});

const form2 = useForm({
    profile_picture_url: settings.value && settings.value.whatsapp ? settings.value?.whatsapp?.business_profile?.profile_picture_url : null,
    description: settings.value && settings.value.whatsapp ? settings.value?.whatsapp?.business_profile?.description : null,
    address: settings.value && settings.value.whatsapp ? settings.value?.whatsapp?.business_profile?.address : null,
    email: settings.value && settings.value.whatsapp ? settings.value?.whatsapp?.business_profile?.email : null,
    industry: settings.value && settings.value.whatsapp ? settings.value?.whatsapp?.business_profile?.industry : null,
    websites: []
});

const form3 = useForm({
    access_token: settings.value && settings.value.whatsapp ? settings.value.whatsapp.access_token : null,
});

const options = [
    { label: 'Automotive', value: 'AUTO' },
    { label: 'Beauty, spa and salon', value: 'BEAUTY' },
    { label: 'Clothing', value: 'APPAREL' },
    { label: 'Education', value: 'EDU' },
    { label: 'Entertainment', value: 'ENTERTAIN' },
    { label: 'Event planning and service', value: 'EVENT_PLAN' },
    { label: 'Finance and banking', value: 'FINANCE' },
    { label: 'Food and groceries', value: 'GROCERY' },
    { label: 'Public service', value: 'GOVT' },
    { label: 'Hotel and lodging', value: 'HOTEL' },
    { label: 'Medical and health', value: 'HEALTH' },
    { label: 'Charity', value: 'NONPROFIT' },
    { label: 'Professional services', value: 'PROF_SERVICES' },
    { label: 'Shopping and retail', value: 'RETAIL' },
    { label: 'Travel and transportation', value: 'TRAVEL' },
    { label: 'Restaurant', value: 'RESTAURANT' },
    { label: 'Not a business', value: 'NOT_A_BIZ' },
    { label: 'Undefined', value: 'UNDEFINED' },
    { label: 'Other', value: 'OTHER' },
];

function openModal() {
    isOpenFormModal.value = true;
}

function openModal2() {
    isOpenForm2Modal.value = true;
}

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text).then(() => {
        // Optional: Show a toast notification
        console.log('Copied to clipboard');
    });
};

const submitForm = () => {
    form.post('/settings/whatsapp', {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            isOpenFormModal.value = false
        }
    })
}

const submitForm2 = () => {
    form2.post('/settings/whatsapp/business-profile', {
        preserveScroll: true,
    })
}

const submitForm3 = () => {
    form3.post('/settings/whatsapp/token', {
        preserveScroll: true,
        onSuccess: () => {
            isOpenForm2Modal.value = false
        }
    })
}

const refreshData = () => {
    refreshLoading.value = true;

    router.visit(`/settings/whatsapp/refresh`, {
        method: 'get',
        preserveState: true,
        onFinish: () => {
            refreshLoading.value = false;
        },
        onSuccess: () => {
            router.visit('/settings/whatsapp', {
                preserveState: false,
            });
        },
    })
}

const deleteIntegration = () => {
    router.delete(`/settings/whatsapp/business-profile`, {
        onBefore: () => confirm('Are you sure you want to delete your integration?'),
        preserveState: true,
        onSuccess: () => {
            router.visit('/settings/whatsapp', {
                preserveState: false,
            });
        },
    })
}
</script>

<style scoped>
@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: .5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>