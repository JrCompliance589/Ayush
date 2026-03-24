<!-- <template>
    <SettingLayout :modules="props.modules">
        <div class="md:h-[90vh]">
            <div class="flex justify-center items-center mb-8">
                <form @submit.prevent="submitForm2()" class="md:w-[60em]">
                    <div class="bg-white border border-slate-200 rounded-lg py-2 text-sm mb-4 pb-2">
                        <div class="flex px-4 pt-2 pb-4">
                            <div>
                                <h2 class="text-[17px]">{{ $t('Organization details') }}</h2>
                                <span class="flex items-center mt-1">
                                    <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/></svg>
                                    {{ $t('Update your organization settings') }}
                                </span>
                            </div>
                        </div>
                        <div class="flex space-x-10 border-b w-full px-4 py-6">
                            <div class="w-[40%]">
                                <span class="text-slate-600">{{ $t('Organization name') }}</span>
                                <div class="text-xs text-slate-700 flex items-center">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/></svg>
                                    <span>{{ $t('Specify the name of your business/organization') }}</span>
                                </div>
                            </div>
                            <div class="w-[60%]">
                                <FormInput v-model="form2.organization_name" :error="form2.errors.organization_name" :name="''" :type="'text'" :class="'col-span-4'"/>
                            </div>
                        </div>
                        <div class="flex space-x-10 w-full px-4 py-6">
                            <div class="w-[40%]">
                                <span class="text-slate-600">{{ $t('Address details') }}</span>
                                <div class="text-xs text-slate-700 flex items-center">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/></svg>
                                    <span>{{ $t('Specify your physical business address') }}</span>
                                </div>
                            </div>
                            <div class="w-[60%] grid grid-cols-4 gap-x-4 gap-y-4">
                                <FormInput v-model="form2.address" :error="form2.errors.address" :name="$t('Physical address')" :type="'text'" :class="'col-span-4'"/>
                                <FormInput v-model="form2.city" :error="form2.errors.city" :name="$t('City')" :type="'text'" :class="'col-span-2'"/>
                                <FormInput v-model="form2.state" :error="form2.errors.state" :name="$t('State')" :type="'text'" :class="'col-span-2'"/>
                                <FormInput v-model="form2.zip" :error="form2.errors.zip" :name="$t('Zip code')" :type="'text'" :class="'col-span-2'"/>
                                <FormSelect v-model="form2.country" :name="$t('Country')" :type="'text'" :optionClassName="'h-40'" :options="props.countries" :error="form2.errors.country" :class="'col-span-2'"/>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-lg py-2 text-sm mb-4">
                        <div class="flex items-center px-4 pt-2 pb-4">
                            <div class="w-[60%]">
                                <h2 class="text-[17px]">{{ $t('Timezone') }}</h2>
                                <span class="flex items-center mt-1">
                                    {{ $t('All your data will be processed according to this timezone') }}
                                </span>
                            </div>
                            <div class="w-[40%] ml-auto">
                                <FormSelect v-model="form2.timezone" :name="''" :type="'text'"  :options="props.timezones" :error="form2.errors.timezone" :class="'col-span-2'"/>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-lg py-2 text-sm pb-4 mb-4">
                        <div class="px-4 pt-2 pb-4">
                            <h2 class="text-[17px]">{{ $t('Notifications') }}</h2>
                            <span class="flex items-center mt-1">
                                <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/></svg>
                                {{ $t('Configure notification settings for new message alerts') }}
                            </span>
                        </div>
                        <div class="flex space-x-10 w-full px-4 py-6" :class="form2.enable_sound_notification === false ? '' : 'border-b'">
                            <div class="w-[40%]">
                                <span class="text-slate-600">{{ $t('Enable notification sound') }}</span>
                                <div class="text-xs text-slate-700 flex items-center">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/></svg>
                                    <span>{{ $t('Specify the name of your business/organization') }}</span>
                                </div>
                            </div>
                            <div class="w-[60%] flex justify-end">
                                <FormToggleSwitch v-model="form2.enable_sound_notification" />
                            </div>
                        </div>
                        <div v-if="form2.enable_sound_notification === true" class="flex space-x-10 w-full px-4 py-6">
                            <div class="w-[60%]">
                                <span class="text-slate-600">{{ $t('New chat sound') }}</span>
                                <div class="text-xs text-slate-700 flex items-center">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/></svg>
                                    <span>{{ $t('Notification sound for every new message') }}</span>
                                </div>
                            </div>
                            <div class="w-[40%] grid grid-cols-4 items-center gap-x-4 gap-y-4">
                                <FormSelect v-model="form2.tone" :name="''" :type="'text'" :optionClassName="'h-32'"  :options="props.sounds" @update:modelValue="handleSoundChange" :error="form2.errors.tone" :class="'col-span-4'"/>
                                <div class="col-span-4 flex gap-x-2">
                                    <svg v-if="form2.volume > 0" @click="reduceVolume()" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3.5 13.857v-3.714a2 2 0 0 1 2-2h2.9a1 1 0 0 0 .55-.165l6-3.956a1 1 0 0 1 1.55.835v14.286a1 1 0 0 1-1.55.835l-6-3.956a1 1 0 0 0-.55-.165H5.5a2 2 0 0 1-2-2Z"/><path stroke-linecap="round" d="M20.5 15V9"/></g></svg>
                                    <svg v-else @click="reduceVolume()" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m18 14l2-2m2-2l-2 2m0 0l-2-2m2 2l2 2"/><path d="M2 13.857v-3.714a2 2 0 0 1 2-2h2.9a1 1 0 0 0 .55-.165l6-3.956a1 1 0 0 1 1.55.835v14.286a1 1 0 0 1-1.55.835l-6-3.956a1 1 0 0 0-.55-.165H4a2 2 0 0 1-2-2Z"/></g></svg>
                                    <input type="range" class="w-full" v-model="form2.volume" @change="playSound(form2.tone)" id="volume-slider" min="0" max="1" step="0.01" />
                                    <svg @click="increaseVolume()" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1.5"><path d="M1 13.857v-3.714a2 2 0 0 1 2-2h2.9a1 1 0 0 0 .55-.165l6-3.956a1 1 0 0 1 1.55.835v14.286a1 1 0 0 1-1.55.835l-6-3.956a1 1 0 0 0-.55-.165H3a2 2 0 0 1-2-2Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M17.5 7.5S19 9 19 11.5s-1.5 4-1.5 4m3-11S23 7 23 11.5s-2.5 7-2.5 7"/></g></svg>
                                </div>
                                <audio ref="audioPlayer"></audio>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-lg py-2 text-sm mb-4">
                        <div class="px-4 pt-2 pb-4">
                            <h2 class="text-[17px]">{{ $t('Failed Message Retry Settings (Campaigns)') }}</h2>
                            <span class="flex items-center mt-1">
                                <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/></svg>
                                {{ $t('Configure the intervals at which failed campaign messages will be retried.') }}
                            </span>
                        </div>
                        <div class="flex space-x-10 w-full px-4 py-6" :class="form2.enable_campaign_resend === false ? '' : 'border-b'">
                            <div class="w-[80%]">
                                <span class="text-slate-600">{{ $t('Enable campaign resend feature') }}</span>
                                <div class="text-xs text-slate-700 flex items-center">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/></svg>
                                    <span>{{ $t('If active, failed messages in campaigns will be retried according to your set intervals') }}</span>
                                </div>
                            </div>
                            <div class="w-[20%] flex justify-end">
                                <FormToggleSwitch v-model="form2.enable_campaign_resend" />
                            </div>
                        </div>
                        <div v-if="form2.enable_campaign_resend === true" class="flex space-x-10 w-full px-4 py-6 border-b">
                            <div class="w-[60%]">
                                <span class="text-slate-600">{{ $t('Interval Unit') }}</span>
                                <div class="text-xs text-slate-700 flex items-center">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/></svg>
                                    <span>{{ $t('Choose the time unit for retry intervals') }}</span>
                                </div>
                            </div>
                            <div class="w-[40%] justify-end">
                                <FormSelect v-model="form2.resend_interval_unit" :name="''" :error="''" :options="intervalUnitOptions" :class="'w-40'"/>
                            </div>
                        </div>
                        <div v-if="form2.enable_campaign_resend === true" class="flex space-x-10 w-full px-4 py-6 border-b">
                            <div class="w-[60%]">
                                <span class="text-slate-600">{{ $t('Retry Intervals') }} ({{ form2.resend_interval_unit }})</span>
                                <div class="text-xs text-slate-700 flex items-center">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/></svg>
                                    <span>{{ $t('Set retry intervals in') }} {{ form2.resend_interval_unit }}</span>
                                </div>
                            </div>
                            <div class="w-[40%] justify-end">
                                <div class="flex space-x-2">
                                    <FormInput v-for="(interval, index) in form2.resend_intervals" v-model.number="form2.resend_intervals[index]" :error="''" :name="''" :type="'number'" :min="'1'" :class="'w-24'"/>
                                </div>
                            </div>
                        </div>
                        <div v-if="form2.enable_campaign_resend === true" class="flex space-x-10 w-full px-4 py-6" :class="form2.move_failed_contacts_to_group === false ? '' : 'border-b'">>
                            <div class="w-[80%]">
                                <span class="text-slate-600">{{ $t('Move failed contacts to a group') }}</span>
                                <div class="text-xs text-slate-700 flex items-center">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/></svg>
                                    <span>{{ $t('If enabled, failed contacts will be moved to a selected group after all retry attempts.') }}</span>
                                </div>
                            </div>
                            <div class="w-[20%] flex justify-end">
                                <FormToggleSwitch v-model="form2.move_failed_contacts_to_group" />
                            </div>
                        </div>
                        <div v-if="form2.enable_campaign_resend === true && form2.move_failed_contacts_to_group === true" class="flex space-x-10 w-full px-4 py-6">
                            <div class="w-[60%]">
                                <span class="text-slate-600">{{ $t('Failed contacts groups') }}</span>
                                <div class="text-xs text-slate-700 flex items-center">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/></svg>
                                    <span>{{ $t('After the three retry attempts, contacts will be moved to this group.') }}</span>
                                </div>
                            </div>
                            <div class="w-[40%] ml-auto">
                                <FormSelect v-model="form2.failed_campaign_group" :name="''" :error="form2.errors.failed_campaign_group" :options="contactGroupOptions()" :type="'text'" :class="'sm:col-span-6'"/>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-lg py-2 text-sm pb-4 mb-20">
                        <div class="flex px-4 pt-1 pb-2">
                            <div class="ml-auto">
                                <button type="submit" class="float-right rounded-md bg-primary px-3 py-2 text-sm text-white shadow-sm hover:shadow-md hover:bg-primary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600" :disabled="form2.processing">
                                    <svg v-if="form2.processing" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5"/><path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z"><animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite" to="360 12 12" type="rotate"/></path></svg>
                                    <span v-else>{{ $t('Save') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </SettingLayout>
</template>
<script setup>
    import SettingLayout from "./Layout.vue";
    import { ref } from 'vue';
    import EmbeddedSignupBtn from '@/Components/EmbeddedSignupBtn.vue';
    import FormModal from '@/Components/FormModal.vue';
    import FormInput from '@/Components/FormInput.vue';
    import FormSelect from '@/Components/FormSelect.vue';
    import FormTextArea from '@/Components/FormTextArea.vue';
    import FormToggleSwitch from '@/Components/FormToggleSwitch.vue';
    import Modal from '@/Components/Modal.vue';
    import { trans } from 'laravel-vue-i18n';
    import { router, useForm } from "@inertiajs/vue3";

    const props = defineProps(['contactGroups', 'settings', 'timezones', 'modules', 'organization', 'countries', 'sounds']);
    const statusView = ref(false);
    const config = ref(props.settings.metadata);
    const settings = ref(config.value ? JSON.parse(config.value) : null);
    const audioPlayer = ref(null);

    const reduceVolume = () => {
        if (form2.volume > 0) {
            form2.volume = Math.max(0, parseFloat(form2.volume) - 0.1);
            playSound(form2.tone);
        }
    };

    const increaseVolume = () => {
        if (form2.volume < 1) {
            form2.volume = Math.min(1, parseFloat(form2.volume) + 0.1);
            playSound(form2.tone);
        }
    };

    const handleSoundChange = (newValue) => {
        playSound(newValue);
    };

    const playSound = (fileUrl) => {
        audioPlayer.value.src = fileUrl;
        audioPlayer.value.volume = form2.volume;
        audioPlayer.value.play();
    };

    const contactGroupOptions = () => {
        return props.contactGroups.map((option) => ({
            value: option.uuid,
            label: option.name,
        }));
    };

    const intervalUnitOptions = [
        { value: 'seconds', label: 'Seconds' },
        { value: 'minutes', label: 'Minutes' },
        { value: 'hours', label: 'Hours' },
    ];

    const getAddressDetail = (value, key) => {
        if(value){
            const address = JSON.parse(value);
            return address?.[key] ?? null;
        } else {
            return null;
        }
    };

    const form2 = useForm({
        organization_name: props.settings?.name,
        address: getAddressDetail(props.settings?.address, 'street'),
        city: getAddressDetail(props.settings?.address, 'city'),
        state: getAddressDetail(props.settings?.address, 'state'),
        zip: getAddressDetail(props.settings?.address, 'zip'),
        country: getAddressDetail(props.settings?.address, 'country'),
        timezone:  settings.value && settings.value.timezone ? settings.value.timezone : 'UTC',
        enable_sound_notification: settings.value && settings.value.notifications ? settings.value.notifications.enable_sound : false,
        volume: settings.value && settings.value.notifications ? settings.value.notifications.volume : 1,
        tone: settings.value && settings.value.notifications ? settings.value.notifications.tone : null,
        enable_campaign_resend: settings.value && settings.value.campaigns?.enable_resend ? settings.value.campaigns?.enable_resend : false,
        move_failed_contacts_to_group: settings.value && settings.value?.campaigns?.move_failed_contacts_to_group ? settings.value?.campaigns?.move_failed_contacts_to_group : false,
        resend_intervals: settings?.value?.campaigns?.resend_intervals ?? [60, 180, 360],
        resend_interval_unit: settings?.value?.campaigns?.resend_interval_unit ?? 'seconds',
        failed_campaign_group: settings.value && settings.value?.campaigns?.failed_campaign_group ? settings.value.campaigns?.failed_campaign_group : null,
    });

    const capitalizeString = (string) => {
        return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
    };

    const submitForm = () => {
        form.post('/settings/whatsapp', {
            preserveScroll: true,
            preserveState: false,
        })
    }

    const submitForm2 = () => {
        form2.put('./profile/organization', {
            preserveScroll: true
        });
    }
</script> -->


<!-- ============================================ NEW UI CODE ============================================= -->


<template>
    <SettingLayout :modules="props.modules">
        <div class="space-y-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <form @submit.prevent="submitForm2()" class="w-full mx-auto space-y-6">

                <!-- Organization Details Card -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                    <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor" d="M10 20v-6h4v6h5v-8h3L12 3L2 12h3v8z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Organization details') }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Update your organization settings') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Organization Name -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('Organization name')
                                    }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Specify the name of your business/organization') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3">
                                <FormInput v-model="form2.organization_name" :error="form2.errors.organization_name"
                                    :name="''" :type="'text'" :class="'w-full'" />
                            </div>
                        </div>

                        <div class="border-t border-slate-100"></div>

                        <!-- Address Details -->
                        <div class="grid md:grid-cols-5 gap-6 items-start">
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-sm font-semibold text-slate-900">{{ $t('Address details') }}</label>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Specify your physical business address') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-3 grid grid-cols-2 gap-4">
                                <FormInput v-model="form2.address" :error="form2.errors.address"
                                    :name="$t('Physical address')" :type="'text'" :class="'col-span-2'" />
                                <FormInput v-model="form2.city" :error="form2.errors.city" :name="$t('City')"
                                    :type="'text'" :class="''" />
                                <FormInput v-model="form2.state" :error="form2.errors.state" :name="$t('State')"
                                    :type="'text'" :class="''" />
                                <FormInput v-model="form2.zip" :error="form2.errors.zip" :name="$t('Zip code')"
                                    :type="'text'" :class="''" />
                                <FormSelect v-model="form2.country" :name="$t('Country')" :type="'text'"
                                    :optionClassName="'h-40'" :options="props.countries" :error="form2.errors.country"
                                    :class="'mt-2'" />

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timezone Card -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                    <div class="p-6">
                        <div class="grid md:grid-cols-5 gap-6 items-center">
                            <div class="md:col-span-3 space-y-1">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-gradient-to-br from-[#ff5100]/10 to-[#ff7340]/10 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" class="text-[#ff5100]">
                                            <path fill="currentColor"
                                                d="M12 2A10 10 0 0 0 2 12a10 10 0 0 0 10 10a10 10 0 0 0 10-10A10 10 0 0 0 12 2m4.2 14.2L11 13V7h1.5v5.2l4.5 2.7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-slate-900">{{ $t('Timezone') }}</h3>
                                        <p class="text-sm text-slate-600">{{ $t(`All your data will be processed
                                            according to this timezone`) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <FormSelect v-model="form2.timezone" :name="''" :type="'text'"
                                    :options="props.timezones" :error="form2.errors.timezone" :class="''" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications Card -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                    <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M21 19v1H3v-1l2-2v-6c0-3.1 2.03-5.83 5-6.71V4a2 2 0 0 1 2-2a2 2 0 0 1 2 2v.29c2.97.88 5 3.61 5 6.71v6zm-7 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t('Notifications') }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Configure notification settings for new message alerts') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Enable Sound -->
                        <div
                            class="flex items-center justify-between p-4 rounded-xl bg-slate-50/50 hover:bg-slate-50 transition-colors">
                            <div class="flex-1">
                                <div class="font-semibold text-slate-900 mb-1">{{ $t('Enable notification sound') }}
                                </div>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t('Play sound when new messages arrive') }}</span>
                                </div>
                            </div>
                            <FormToggleSwitch v-model="form2.enable_sound_notification" />
                        </div>

                        <!-- Sound Settings (shown when enabled) -->
                        <transition name="slide-fade">
                            <div v-if="form2.enable_sound_notification"
                                class="space-y-4 p-4 rounded-xl bg-gradient-to-br from-[#ff5100]/5 to-orange-50/50 border border-[#ff5100]/10">
                                <div class="grid md:grid-cols-5 gap-6 items-start">
                                    <div class="md:col-span-2 space-y-1">
                                        <label class="text-sm font-semibold text-slate-900">{{ $t('New chat sound')
                                            }}</label>
                                        <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                                <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                            </svg>
                                            <span>{{ $t('Notification sound for every new message') }}</span>
                                        </div>
                                    </div>
                                    <div class="md:col-span-3 space-y-4">
                                        <FormSelect v-model="form2.tone" :name="''" :type="'text'"
                                            :optionClassName="'h-32'" :options="props.sounds"
                                            @update:modelValue="handleSoundChange" :error="form2.errors.tone"
                                            :class="''" />

                                        <!-- Volume Control -->
                                        <div
                                            class="flex items-center gap-3 p-3 bg-white rounded-xl border border-slate-200">
                                            <button type="button" @click="reduceVolume()"
                                                class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                                                <svg v-if="form2.volume > 0" xmlns="http://www.w3.org/2000/svg"
                                                    width="20" height="20" viewBox="0 0 24 24" class="text-[#ff5100]">
                                                    <g fill="none" stroke="currentColor" stroke-width="1.5">
                                                        <path
                                                            d="M3.5 13.857v-3.714a2 2 0 0 1 2-2h2.9a1 1 0 0 0 .55-.165l6-3.956a1 1 0 0 1 1.55.835v14.286a1 1 0 0 1-1.55.835l-6-3.956a1 1 0 0 0-.55-.165H5.5a2 2 0 0 1-2-2Z" />
                                                        <path stroke-linecap="round" d="M20.5 15V9" />
                                                    </g>
                                                </svg>
                                                <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24" class="text-slate-400">
                                                    <g fill="none" stroke="currentColor" stroke-width="1.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m18 14l2-2m2-2l-2 2m0 0l-2-2m2 2l2 2" />
                                                        <path
                                                            d="M2 13.857v-3.714a2 2 0 0 1 2-2h2.9a1 1 0 0 0 .55-.165l6-3.956a1 1 0 0 1 1.55.835v14.286a1 1 0 0 1-1.55.835l-6-3.956a1 1 0 0 0-.55-.165H4a2 2 0 0 1-2-2Z" />
                                                    </g>
                                                </svg>
                                            </button>
                                            <input type="range"
                                                class="flex-1 h-2 bg-slate-200 rounded-lg cursor-pointer accent-[#ff5100]"
                                                v-model="form2.volume" @change="playSound(form2.tone)"
                                                id="volume-slider" min="0" max="1" step="0.01" />
                                            <button type="button" @click="increaseVolume()"
                                                class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24" class="text-[#ff5100]">
                                                    <g fill="none" stroke="currentColor" stroke-width="1.5">
                                                        <path
                                                            d="M1 13.857v-3.714a2 2 0 0 1 2-2h2.9a1 1 0 0 0 .55-.165l6-3.956a1 1 0 0 1 1.55.835v14.286a1 1 0 0 1-1.55.835l-6-3.956a1 1 0 0 0-.55-.165H3a2 2 0 0 1-2-2Z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M17.5 7.5S19 9 19 11.5s-1.5 4-1.5 4m3-11S23 7 23 11.5s-2.5 7-2.5 7" />
                                                    </g>
                                                </svg>
                                            </button>
                                        </div>
                                        <audio ref="audioPlayer"></audio>
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>

                <!-- Campaign Retry Settings Card -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                    <div class="bg-gradient-to-r from-slate-50 to-orange-50/30 px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-gradient-to-br from-[#ff5100] to-[#ff7340] rounded-lg shadow-lg shadow-[#ff5100]/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M12.079 2.25c-4.794 0-8.734 3.663-9.118 8.333H2a.75.75 0 0 0-.528 1.283l1.68 1.666a.75.75 0 0 0 1.056 0l1.68-1.666a.75.75 0 0 0-.528-1.283h-.893c.38-3.831 3.638-6.833 7.612-6.833a7.658 7.658 0 0 1 6.537 3.643a.75.75 0 1 0 1.277-.786A9.158 9.158 0 0 0 12.08 2.25m8.761 8.217a.75.75 0 0 0-1.054 0L18.1 12.133a.75.75 0 0 0 .527 1.284h.899c-.382 3.83-3.651 6.833-7.644 6.833a7.697 7.697 0 0 1-6.565-3.644a.75.75 0 1 0-1.277.788a9.197 9.197 0 0 0 7.842 4.356c4.808 0 8.765-3.66 9.15-8.333H22a.75.75 0 0 0 .527-1.284z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $t(`Failed Message Retry Settings
                                    (Campaigns)`) }}</h2>
                                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t(`Configure the intervals at which failed campaign messages will be
                                        retried.`) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Enable Campaign Resend -->
                        <div
                            class="flex items-center justify-between p-4 rounded-xl bg-slate-50/50 hover:bg-slate-50 transition-colors">
                            <div class="flex-1">
                                <div class="font-semibold text-slate-900 mb-1">{{ $t('Enable campaign resend feature')
                                    }}</div>
                                <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        class="text-[#ff5100] shrink-0 mt-0.5">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                    </svg>
                                    <span>{{ $t(`If active, failed messages in campaigns will be retried according to
                                        your set intervals`) }}</span>
                                </div>
                            </div>
                            <FormToggleSwitch v-model="form2.enable_campaign_resend" />
                        </div>

                        <!-- Retry Intervals (shown when enabled) -->
                        <transition name="slide-fade">
                            <div v-if="form2.enable_campaign_resend"
                                class="space-y-6 p-4 rounded-xl bg-gradient-to-br from-[#ff5100]/5 to-orange-50/50 border border-[#ff5100]/10">
                                
                                <!-- Interval Unit Selector -->
                                <div class="grid md:grid-cols-5 gap-6 items-start">
                                    <div class="md:col-span-2 space-y-1">
                                        <label class="text-sm font-semibold text-slate-900">{{ $t('Interval Unit') }}</label>
                                        <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                                <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                            </svg>
                                            <span>{{ $t('Choose the time unit for retry intervals') }}</span>
                                        </div>
                                    </div>
                                    <div class="md:col-span-3">
                                        <FormSelect v-model="form2.resend_interval_unit" :name="''"
                                            :error="''" :options="intervalUnitOptions" class="w-40" />
                                    </div>
                                </div>

                                <div class="border-t border-[#ff5100]/10"></div>

                                <!-- Retry Intervals -->
                                <div class="grid md:grid-cols-5 gap-6 items-start">
                                    <div class="md:col-span-2 space-y-1">
                                        <label class="text-sm font-semibold text-slate-900">{{ $t('Retry Intervals') }} ({{ form2.resend_interval_unit }})</label>
                                        <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                                <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                            </svg>
                                            <span>{{ $t('Set retry intervals in') }} {{ form2.resend_interval_unit }}</span>
                                        </div>
                                    </div>
                                    <div class="md:col-span-3 flex gap-3">
                                        <FormInput v-for="(interval, index) in form2.resend_intervals" :key="index"
                                            v-model.number="form2.resend_intervals[index]" :error="''" :name="''"
                                            :type="'number'" :min="'1'" :class="'w-24'" />
                                    </div>
                                </div>

                                <div class="border-t border-[#ff5100]/10"></div>

                                <!-- Move Failed Contacts -->
                                <div
                                    class="flex items-center justify-between p-4 rounded-xl bg-white border border-slate-200">
                                    <div class="flex-1">
                                        <div class="font-semibold text-slate-900 mb-1">{{ $t(`Move failed contacts to a
                                            group`) }}</div>
                                        <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                                <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                            </svg>
                                            <span>{{ $t(`If enabled, failed contacts will be moved to a selected group
                                                after all retry attempts.`) }}</span>
                                        </div>
                                    </div>
                                    <FormToggleSwitch v-model="form2.move_failed_contacts_to_group" />
                                </div>

                                <!-- Failed Contacts Group -->
                                <transition name="slide-fade">
                                    <div v-if="form2.move_failed_contacts_to_group"
                                        class="grid md:grid-cols-5 gap-6 items-start p-4 rounded-xl bg-white border border-slate-200">
                                        <div class="md:col-span-2 space-y-1">
                                            <label class="text-sm font-semibold text-slate-900">{{ $t(`Failed contacts
                                                groups`) }}</label>
                                            <div class="flex items-start gap-1.5 text-xs text-slate-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 24 24" class="text-[#ff5100] shrink-0 mt-0.5">
                                                    <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                                                </svg>
                                                <span>{{ $t(`After the three retry attempts, contacts will be moved to
                                                    this group.`) }}</span>
                                            </div>
                                        </div>
                                        <div class="md:col-span-3">
                                            <FormSelect v-model="form2.failed_campaign_group" :name="''"
                                                :error="form2.errors.failed_campaign_group"
                                                :options="contactGroupOptions()" :type="'text'" :class="''" />
                                        </div>
                                    </div>
                                </transition>
                            </div>
                        </transition>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end pt-4">
                    <button type="submit"
                        class="group px-8 py-3 bg-primary text-white font-semibold rounded-xl shadow-lg shadow-[#ff5100]/10 hover:shadow-xl hover:shadow-[#ff5100]/20 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                        :disabled="form2.processing">
                        <span v-if="form2.processing" class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
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
            </form>
        </div>
    </SettingLayout>
</template>

<script setup>
import SettingLayout from "./Layout.vue";
import { ref } from 'vue';
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import FormToggleSwitch from '@/Components/FormToggleSwitch.vue';
import { useForm } from "@inertiajs/vue3";

const props = defineProps(['contactGroups', 'settings', 'timezones', 'modules', 'organization', 'countries', 'sounds']);
const config = ref(props.settings.metadata);
const settings = ref(config.value ? JSON.parse(config.value) : null);
const audioPlayer = ref(null);

const reduceVolume = () => {
    if (form2.volume > 0) {
        form2.volume = Math.max(0, parseFloat(form2.volume) - 0.1);
        playSound(form2.tone);
    }
};

const increaseVolume = () => {
    if (form2.volume < 1) {
        form2.volume = Math.min(1, parseFloat(form2.volume) + 0.1);
        playSound(form2.tone);
    }
};

const handleSoundChange = (newValue) => {
    playSound(newValue);
};

const playSound = (fileUrl) => {
    audioPlayer.value.src = fileUrl;
    audioPlayer.value.volume = form2.volume;
    audioPlayer.value.play();
};

const contactGroupOptions = () => {
    return props.contactGroups.map((option) => ({
        value: option.uuid,
        label: option.name,
    }));
};

const getAddressDetail = (value, key) => {
    if (value) {
        const address = JSON.parse(value);
        return address?.[key] ?? null;
    } else {
        return null;
    }
};

const intervalUnitOptions = [
    { value: 'seconds', label: 'Seconds' },
    { value: 'minutes', label: 'Minutes' },
    { value: 'hours', label: 'Hours' },
];

const form2 = useForm({
    organization_name: props.settings?.name,
    address: getAddressDetail(props.settings?.address, 'street'),
    city: getAddressDetail(props.settings?.address, 'city'),
    state: getAddressDetail(props.settings?.address, 'state'),
    zip: getAddressDetail(props.settings?.address, 'zip'),
    country: getAddressDetail(props.settings?.address, 'country'),
    timezone: settings.value && settings.value.timezone ? settings.value.timezone : 'UTC',
    enable_sound_notification: settings.value && settings.value.notifications ? settings.value.notifications.enable_sound : false,
    volume: settings.value && settings.value.notifications ? settings.value.notifications.volume : 1,
    tone: settings.value && settings.value.notifications ? settings.value.notifications.tone : null,
    enable_campaign_resend: settings.value && settings.value.campaigns?.enable_resend ? settings.value.campaigns?.enable_resend : false,
    move_failed_contacts_to_group: settings.value && settings.value?.campaigns?.move_failed_contacts_to_group ? settings.value?.campaigns?.move_failed_contacts_to_group : false,
    resend_intervals: settings?.value?.campaigns?.resend_intervals ?? [60, 180, 360],
    resend_interval_unit: settings?.value?.campaigns?.resend_interval_unit ?? 'seconds',
    failed_campaign_group: settings.value && settings.value?.campaigns?.failed_campaign_group ? settings.value.campaigns?.failed_campaign_group : null,
});

const submitForm2 = () => {
    form2.put('./profile/organization', {
        preserveScroll: true
    });
}
</script>