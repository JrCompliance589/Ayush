<!-- ============================================ NEW UI CODE ============================================= -->

<template>
  <AppLayout>
    <div class="min-h-screen bg-white px-4 md:px-8 py-6 md:py-10">
      <!-- Header Section -->
      <div class="flex-1 mb-8">
        <div class="flex items-center gap-3 mb-3">
          <div
            class="w-10 h-10 text-white rounded-xl bg-gradient-to-br from-[#ff5100] to-[#ff7a3d] flex items-center justify-center shadow-lg shadow-[#ff5100]/25">
            <CreditCard />
          </div>
          <h2
            class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-700 bg-clip-text text-transparent">
            {{ $t('Billing & Subscription') }}
          </h2>
        </div>
        <div class="flex items-start gap-2 text-gray-600">
          <p class="text-sm md:text-base font-medium leading-relaxed">
            {{ $t('Select the plan that you want to subscribe to') }}
          </p>
        </div>
      </div>

      <div class="mx-auto">
        <div class="grid lg:grid-cols-3 gap-8">
          <!-- Plans Section -->
          <div class="lg:col-span-2 space-y-6">
            <div class="grid md:grid-cols-2 gap-6">
              <div v-for="(item, index) in props.plans?.data" :key="index"
                @click="selectPlan(item.id, item.name, item.period, item.price)"
                class="relative group cursor-pointer transform transition-all duration-300 hover:scale-105">

                <div class="relative bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-300"
                  :class="form.plan === item.id ? 'ring-2 ring-[#ff5100]' : 'hover:shadow-xl'">
                  <!-- Card Header -->
                  <div class="p-6 border-b border-gray-100"
                    :class="form.plan === item.id ? 'bg-gradient-to-r from-[#ff5100] to-[#ff7733]' : 'bg-gray-50'">
                    <div class="flex items-center justify-between mb-4">
                      <h3 class="text-xl font-bold" :class="form.plan === item.id ? 'text-white' : 'text-gray-900'">
                        {{ item.name }}
                      </h3>
                      <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all" :class="form.plan === item.id
                        ? 'bg-white border-white'
                        : 'border-gray-300 bg-white group-hover:border-[#ff5100]'">
                        <svg v-if="form.plan === item.id" class="w-4 h-4 text-[#ff5100]" fill="none"
                          stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                      </div>
                    </div>

                    <div class="flex items-baseline space-x-2">
                      <span class="text-4xl font-bold" :class="form.plan === item.id ? 'text-white' : 'text-gray-900'">
                        {{ item.price }}
                      </span>
                      <span class="text-sm" :class="form.plan === item.id ? 'text-white/80' : 'text-gray-500'">
                        <span v-if="item.period === 'monthly'">/ {{ $t("month") }}</span>
                        <span v-if="item.period === 'yearly'">/ {{ $t("year") }}</span>
                      </span>
                    </div>
                  </div>

                  <!-- Features List -->
                  <div class="p-6 space-y-3">
                    <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
                      {{ $t("Features") }}
                    </h4>

                    <!-- Campaign Limit -->
                    <div class="flex items-center space-x-3">
                      <div class="flex-shrink-0 w-5 h-5 rounded-full bg-green-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 16 16"
                          class="text-green-600">
                          <path fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M14.25 8.75c-.5 2.5-2.385 4.854-5.03 5.38A6.25 6.25 0 0 1 3.373 3.798C5.187 1.8 8.25 1.25 10.75 2.25m-5 5.5l2.5 2.5l6-6.5" />
                        </svg>
                      </div>
                      <span class="text-sm text-gray-700">
                        <span class="font-semibold text-gray-900">
                          {{ getDetail(item?.metadata, "campaign_limit") == "-1" ? $t("Unlimited") :
                            getDetail(item?.metadata, "campaign_limit") }}
                        </span>
                        {{ $t("Campaigns") }}
                      </span>
                    </div>

                    <!-- Message Limit -->
                    <div class="flex items-center space-x-3">
                      <div class="flex-shrink-0 w-5 h-5 rounded-full bg-green-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 16 16"
                          class="text-green-600">
                          <path fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M14.25 8.75c-.5 2.5-2.385 4.854-5.03 5.38A6.25 6.25 0 0 1 3.373 3.798C5.187 1.8 8.25 1.25 10.75 2.25m-5 5.5l2.5 2.5l6-6.5" />
                        </svg>
                      </div>
                      <span class="text-sm text-gray-700">
                        <span class="font-semibold text-gray-900">
                          {{ getDetail(item?.metadata, "message_limit") == "-1" ? $t("Unlimited") :
                            getDetail(item?.metadata, "message_limit") }}
                        </span>
                        {{ $t("Messages") }}
                      </span>
                    </div>

                    <!-- Contacts Limit -->
                    <div class="flex items-center space-x-3">
                      <div class="flex-shrink-0 w-5 h-5 rounded-full bg-green-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 16 16"
                          class="text-green-600">
                          <path fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M14.25 8.75c-.5 2.5-2.385 4.854-5.03 5.38A6.25 6.25 0 0 1 3.373 3.798C5.187 1.8 8.25 1.25 10.75 2.25m-5 5.5l2.5 2.5l6-6.5" />
                        </svg>
                      </div>
                      <span class="text-sm text-gray-700">
                        <span class="font-semibold text-gray-900">
                          {{ getDetail(item?.metadata, "contacts_limit") == "-1" ? $t("Unlimited") :
                            getDetail(item?.metadata, "contacts_limit") }}
                        </span>
                        {{ $t("Contacts") }}
                      </span>
                    </div>

                    <!-- Canned Replies -->
                    <div class="flex items-center space-x-3">
                      <div class="flex-shrink-0 w-5 h-5 rounded-full bg-green-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 16 16"
                          class="text-green-600">
                          <path fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M14.25 8.75c-.5 2.5-2.385 4.854-5.03 5.38A6.25 6.25 0 0 1 3.373 3.798C5.187 1.8 8.25 1.25 10.75 2.25m-5 5.5l2.5 2.5l6-6.5" />
                        </svg>
                      </div>
                      <span class="text-sm text-gray-700">
                        <span class="font-semibold text-gray-900">
                          {{ getDetail(item?.metadata, "canned_replies_limit") == "-1" ? $t("Unlimited") :
                            getDetail(item?.metadata, "canned_replies_limit") }}
                        </span>
                        {{ $t("Canned replies") }}
                      </span>
                    </div>

                    <!-- Team Limit -->
                    <div class="flex items-center space-x-3">
                      <div class="flex-shrink-0 w-5 h-5 rounded-full bg-green-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 16 16"
                          class="text-green-600">
                          <path fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M14.25 8.75c-.5 2.5-2.385 4.854-5.03 5.38A6.25 6.25 0 0 1 3.373 3.798C5.187 1.8 8.25 1.25 10.75 2.25m-5 5.5l2.5 2.5l6-6.5" />
                        </svg>
                      </div>
                      <span class="text-sm text-gray-700">
                        <span class="font-semibold text-gray-900">
                          {{ getDetail(item?.metadata, "team_limit") == "-1" ? $t("Unlimited") :
                            getDetail(item?.metadata, "team_limit") }}
                        </span>
                        {{ $t("Users") }}
                      </span>
                    </div>

                    <!-- Addons -->
                    <!-- <div v-for="(value, key) in filteredAddons(item)" :key="key" class="flex items-center space-x-3">
                      <div class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center"
                        :class="value ? 'bg-green-100' : 'bg-red-100'">
                        <svg v-if="value" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 16 16"
                          class="text-green-600">
                          <path fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M14.25 8.75c-.5 2.5-2.385 4.854-5.03 5.38A6.25 6.25 0 0 1 3.373 3.798C5.187 1.8 8.25 1.25 10.75 2.25m-5 5.5l2.5 2.5l6-6.5" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 26 26"
                          class="text-red-600">
                          <path fill="currentColor"
                            d="M10.172 17.243a1 1 0 1 1-1.415-1.415l7.071-7.07a1 1 0 1 1 1.415 1.414z" />
                          <path fill="currentColor"
                            d="M8.757 10.172a1 1 0 0 1 1.415-1.415l7.07 7.071a1 1 0 1 1-1.414 1.415z" />
                        </svg>
                      </div>
                      <span class="text-sm text-gray-700">{{ key }}</span>
                    </div> -->

                    <!-- AI Features -->
                    <div v-if="props.enable_ai_billing == 1 && getDetail(item?.metadata, 'ai_text_response_limit')"
                      class="flex items-center space-x-3">
                      <div class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center"
                        :class="filteredAddons(item)['AI Assistant'] ? 'bg-green-100' : 'bg-red-100'">
                        <svg v-if="filteredAddons(item)['AI Assistant']" xmlns="http://www.w3.org/2000/svg" width="12"
                          height="12" viewBox="0 0 16 16" class="text-green-600">
                          <path fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M14.25 8.75c-.5 2.5-2.385 4.854-5.03 5.38A6.25 6.25 0 0 1 3.373 3.798C5.187 1.8 8.25 1.25 10.75 2.25m-5 5.5l2.5 2.5l6-6.5" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 26 26"
                          class="text-red-600">
                          <path fill="currentColor"
                            d="M10.172 17.243a1 1 0 1 1-1.415-1.415l7.071-7.07a1 1 0 1 1 1.415 1.414z" />
                          <path fill="currentColor"
                            d="M8.757 10.172a1 1 0 0 1 1.415-1.415l7.07 7.071a1 1 0 1 1-1.414 1.415z" />
                        </svg>
                      </div>
                      <span class="text-sm text-gray-700">
                        <span v-if="filteredAddons(item)['AI Assistant']" class="font-semibold text-gray-900">
                          {{ getDetail(item?.metadata, "ai_text_response_limit") == "-1" ? $t("Unlimited") :
                            getDetail(item?.metadata, "ai_text_response_limit") }}
                        </span>
                        {{ $t("AI Text Response Limit") }}
                      </span>
                    </div>

                    <div v-if="props.enable_ai_billing == 1 && getDetail(item?.metadata, 'ai_audio_response_limit')"
                      class="flex items-center space-x-3">
                      <div class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center"
                        :class="filteredAddons(item)['AI Assistant'] ? 'bg-green-100' : 'bg-red-100'">
                        <svg v-if="filteredAddons(item)['AI Assistant']" xmlns="http://www.w3.org/2000/svg" width="12"
                          height="12" viewBox="0 0 16 16" class="text-green-600">
                          <path fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M14.25 8.75c-.5 2.5-2.385 4.854-5.03 5.38A6.25 6.25 0 0 1 3.373 3.798C5.187 1.8 8.25 1.25 10.75 2.25m-5 5.5l2.5 2.5l6-6.5" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 26 26"
                          class="text-red-600">
                          <path fill="currentColor"
                            d="M10.172 17.243a1 1 0 1 1-1.415-1.415l7.071-7.07a1 1 0 1 1 1.415 1.414z" />
                          <path fill="currentColor"
                            d="M8.757 10.172a1 1 0 0 1 1.415-1.415l7.07 7.071a1 1 0 1 1-1.414 1.415z" />
                        </svg>
                      </div>
                      <span class="text-sm text-gray-700">
                        <span v-if="filteredAddons(item)['AI Assistant']" class="font-semibold text-gray-900">
                          {{ getDetail(item?.metadata, "ai_audio_response_limit") == "-1" ? $t("Unlimited") :
                            getDetail(item?.metadata, "ai_audio_response_limit") }}
                        </span>
                        {{ $t("AI Audio Response Limit") }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Summary Sidebar -->
          <div class="lg:col-span-1">
            <div class="sticky top-8">
              <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-[#ff5100] to-[#ff7733] p-6">
                  <h3 class="text-2xl font-bold text-white">{{ $t("Summary") }}</h3>
                </div>

                <div class="p-6">
                  <!-- Empty State -->
                  <div v-if="!form.plan" class="text-center py-12">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                        class="text-gray-400">
                        <path fill="currentColor"
                          d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z" />
                      </svg>
                    </div>
                    <p class="text-gray-600 text-sm">{{ $t("Select plan to continue") }}</p>
                  </div>

                  <!-- Selected Plan -->
                  <div v-if="form.plan" class="space-y-6">
                    <!-- Plan Details -->
                    <div class="flex items-center justify-between p-4 bg-orange-50 rounded-xl">
                      <div class="space-y-1">
                        <h4 class="font-semibold text-gray-900">{{ selectedPlan.name }}</h4>
                        <span
                          class="inline-block px-3 py-1 bg-white rounded-lg text-xs font-medium text-gray-700 capitalize">
                          {{ selectedPlan.period }}
                        </span>
                      </div>
                      <span class="text-xl font-bold text-gray-900">{{ basePrice }}</span>
                    </div>

                    <div class="space-y-3">
                      <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">{{ $t("Gross total") }}</span>
                        <span class="font-semibold text-gray-900">{{ grossAmount }}</span>
                      </div>

                      <!-- Tax -->
                      <div v-if="taxRates.length > 0" class="bg-blue-50 rounded-xl p-4 space-y-2">
                        <div
                          class="flex items-center space-x-2 text-sm font-semibold text-blue-900 border-b border-blue-200 pb-2">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            class="text-blue-600">
                            <path fill="currentColor"
                              d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" />
                          </svg>
                          <span>{{ $t("Tax") }}</span>
                        </div>
                        <div v-for="(item, index) in taxRates" :key="index"
                          class="flex items-center justify-between text-sm">
                          <span class="text-gray-700">{{ item.name }} ({{ item.percentage }}%)</span>
                          <span class="font-semibold text-gray-900">{{ item.amount }}</span>
                        </div>
                      </div>

                      <!-- Debits -->
                      <div v-if="debit.total > 0" class="bg-green-50 rounded-xl p-4">
                        <div class="flex items-center justify-between text-sm">
                          <div class="space-y-1">
                            <div class="font-semibold text-green-900">{{ $t("Available debits") }}</div>
                            <div class="text-xs text-green-700">({{ $t("Applicable debits due") }})</div>
                          </div>
                          <span class="font-semibold text-green-900">{{ debit.total }}</span>
                        </div>
                      </div>

                      <!-- Coupon -->
                      <div v-if="parseFloat(amountDue) > 0" class="bg-purple-50 rounded-xl p-4 space-y-3">
                        <div class="flex items-center space-x-2 text-sm font-semibold text-purple-900"
                          :class="coupon.length === 0 && 'border-b border-purple-200 pb-2'">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            class="text-purple-600">
                            <path fill="currentColor"
                              d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58c.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41c0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4S7 4.67 7 5.5S6.33 7 5.5 7z" />
                          </svg>
                          <span>{{ $t("Coupon code") }}</span>
                        </div>

                        <div v-if="coupon?.length === 0" class="flex items-center gap-2 flex-wrap">
                          <input v-model="form1.coupon"
                            class="flex-1 px-4 py-2 border-2 border-purple-200 rounded-lg focus:border-purple-500 focus:outline-none text-sm"
                            :placeholder="$t('Enter coupon code')" />
                          <button @click="applyCoupon" :disabled="form1.processing"
                            class="ml-auto px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-semibold hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-1">
                            <svg v-if="form1.processing" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                              viewBox="0 0 24 24">
                              <path fill="currentColor"
                                d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                opacity=".5" />
                              <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                <animateTransform attributeName="transform" dur="1s" from="0 12 12"
                                  repeatCount="indefinite" to="360 12 12" type="rotate" />
                              </path>
                            </svg>
                            <span>{{ $t("Apply") }}</span>
                          </button>
                        </div>

                        <div v-if="form1.errors.coupon" class="text-red-600 text-xs">{{ form1.errors.coupon }}</div>

                        <div v-if="coupon?.length !== 0"
                          class="flex items-center justify-between bg-white rounded-lg p-3">
                          <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900">{{ coupon?.code }}</span>
                            <span v-if="coupon?.type == 'percentage'" class="text-xs text-green-600 font-medium">({{
                              coupon?.amount }}% OFF)</span>
                          </div>
                          <div class="flex items-center space-x-2">
                            <!-- Old way -->
                            <!-- <span class="text-sm font-semibold text-red-600">-{{ coupon?.discount }}</span> -->
                            <!-- New way -->
                            <span class="text-sm font-semibold text-red-600">-{{ (netAmount?.toString().replace(/,/g,
                              "")
                              || 0) - (renderAmount?.toString().replace(/,/g, "") || 0) }}</span>
                            <button @click="removeCoupon" class="text-red-500 hover:text-red-700 transition-colors">
                              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                  d="M12 20a8 8 0 1 1 0-16a8 8 0 0 1 0 16M9.707 8.293a1 1 0 0 0-1.414 1.414L10.586 12l-2.293 2.293a1 1 0 1 0 1.414 1.414L12 13.414l2.293 2.293a1 1 0 0 0 1.414-1.414L13.414 12l2.293-2.293a1 1 0 0 0-1.414-1.414L12 10.586z" />
                              </svg>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Total Due -->
                    <div class="pt-4 border-t-2 border-gray-200">
                      <div class="flex items-center justify-between mb-6">
                        <span class="text-lg font-semibold text-gray-900">{{ $t("Total due") }}</span>
                        <!-- Old way - (amountDue) -->
                        <!-- <span class="text-3xl font-bold text-[#ff5100]">{{ amountDue }}</span> -->
                        <!-- New way - (renderAmount) -->
                        <span class="text-3xl font-bold text-[#ff5100]">{{ renderAmount }}</span>
                      </div>

                      <!-- Continue Button -->
                      <button v-if="!buttonLoading || (!buttonLoading && amountDue <= 0)"
                        @click="showConfirmModal = true"
                        class="w-full py-4 bg-gradient-to-r from-[#ff5100] to-[#ff7733] text-white rounded-xl font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200 flex items-center justify-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                          fill="currentColor">
                          <path
                            d="M12 13a1.49 1.49 0 0 0-1 2.61V17a1 1 0 0 0 2 0v-1.39A1.49 1.49 0 0 0 12 13m5-4V7A5 5 0 0 0 7 7v2a3 3 0 0 0-3 3v7a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-7a3 3 0 0 0-3-3M9 7a3 3 0 0 1 6 0v2H9Zm9 12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-7a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1Z" />
                        </svg>
                        <span>{{ $t("Continue") }}</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Confirmation Modal -->
      <div v-if="showConfirmModal"
        class="fixed inset-0 flex items-center justify-center bg-black/60 backdrop-blur-sm z-50 p-4">
        <div
          class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 transform scale-100 animate-[scaleIn_0.3s_ease-out]">
          <div class="text-center mb-6">
            <div class="mx-auto w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" class="text-[#ff5100]">
                <path fill="currentColor"
                  d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Confirm Action</h2>
            <p class="text-gray-600 mb-1">Are you sure you want to continue with this action?</p>
            <p class="text-sm text-red-600 font-medium">Note: Subscription amount will be deducted from your wallet.</p>
          </div>
          <div class="flex space-x-4">
            <button @click="confirmAction"
              class="flex-1 px-6 py-3 bg-[#ff5100] text-white rounded-xl font-semibold hover:bg-[#cc4100] transition-colors">
              Yes, Continue
            </button>
            <button @click="showConfirmModal = false"
              class="flex-1 px-6 py-3 bg-gray-200 text-gray-800 rounded-xl font-semibold hover:bg-gray-300 transition-colors">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import { router, useForm } from "@inertiajs/vue3";
import { CreditCard, X } from "lucide-vue-next";
import { computed, ref } from "vue";

const props = defineProps([
  "addons",
  "enable_ai_billing",
  "plans",
  "methods",
  "subscription",
  "subscriptionDetails",
]);

const subscriptionDetails = ref(props.subscriptionDetails);

const form = useForm({
  plan: props.subscription?.plan_id,
  method: null,
});

const form1 = useForm({
  coupon: subscriptionDetails.value?.coupon?.code,
});

const buttonLoading = ref(false);
const showConfirmModal = ref(false);

const selectedPlan = ref({
  name: props.subscription?.plan?.name,
  period: props.subscription?.plan?.period,
  amount: props.subscription?.plan?.price,
});

const grossAmount = ref(subscriptionDetails.value?.grossAmount);
const amountDue = ref(subscriptionDetails.value?.amountDue);
const taxRates = ref(subscriptionDetails.value?.taxRates);
const credit = ref(subscriptionDetails.value?.credit);
const debit = ref(subscriptionDetails.value?.debit);
const basePrice = ref(subscriptionDetails.value?.basePrice);
const coupon = ref(subscriptionDetails.value?.coupon);
const netAmount = ref(subscriptionDetails.value?.netAmount);


// added new variable for showing amount ---------------------------------------------------------------------------------------------
function calculateNewAmount(amount, discountPercent, taxRates) {
  const gross = Number(amount.toString().replace(/,/g, "")) || 0;
  const discount = Number(discountPercent.toString().replace(/,/g, "")) || 0;

  // Apply % discount
  const discountedAmount = gross - (gross * discount / 100);

  // Add all tax amounts
  const taxTotal = (taxRates || []).reduce(
    (acc, curr) => acc + Number(curr.amount.toString().replace(/,/g, "") || 0),
    0
  );

  // Final net amount
  const net = discountedAmount + taxTotal;

  return net.toFixed(2);
}

const renderAmount = computed(() => {
  return calculateNewAmount(
    subscriptionDetails.value?.grossAmount || '0',
    subscriptionDetails.value?.coupon?.amount || '0',
    subscriptionDetails.value?.taxRates || []
  );
});




const selectPlan = (plan, name, period, amount) => {
  form.plan = plan;
  selectedPlan.value = { name, period, amount };
  getPlanDetails(plan);
};

const getDetail = (value, key) => {
  if (value) {
    const item = JSON.parse(value);
    return item?.[key] ?? null;
  } else {
    return null;
  }
};

const filteredAddons = (item) => {
  const addons = JSON.parse(item.metadata)["addons"];

  if (!addons || typeof addons !== "object") {
    return {};
  }

  return Object.entries(props.addons).reduce((acc, [key, value]) => {
    if (value === 1 && addons.hasOwnProperty(key)) {
      acc[key] = addons[key];
    }
    return acc;
  }, {});
};

const getPlanDetails = (planId) => {
  router.visit("/subscription/" + planId, {
    method: "get",
    preserveState: true,
    onSuccess: (response) => {
      form.plan = planId;
      const res = response.props.response_data.data;
      subscriptionDetails.value = response.props.response_data.data;
      grossAmount.value = res.grossAmount;
      taxRates.value = res.taxRates;
      netAmount.value = res.netAmount;
      credit.value = res.credit;
      debit.value = res.debit;
      basePrice.value = res.basePrice;
      amountDue.value = res.amountDue;
      coupon.value = res.coupon;
    },
  });
};

const removeCoupon = () => {
  const planId = form.plan;

  router.visit("/subscription/coupon/remove/" + planId, {
    method: "get",
    preserveState: true,
    onSuccess: (response) => {
      form.plan = planId;
      const res = response.props.response_data.data;
      subscriptionDetails.value = response.props.response_data.data;
      grossAmount.value = res.grossAmount;
      taxRates.value = res.taxRates;
      netAmount.value = res.netAmount;
      credit.value = res.credit;
      debit.value = res.debit;
      basePrice.value = res.basePrice;
      amountDue.value = res.amountDue;
      coupon.value = res.coupon;
    },
  });
};

const applyCoupon = () => {
  const planId = form.plan;

  form1.post("/subscription/coupon/apply/" + planId, {
    preserveScroll: true,
    onSuccess: (response) => {
      form.plan = planId;
      const res = response.props.response_data.data;
      subscriptionDetails.value = response.props.response_data.data;
      grossAmount.value = res.grossAmount;
      taxRates.value = res.taxRates;
      netAmount.value = res.netAmount;
      credit.value = res.credit;
      debit.value = res.debit;
      basePrice.value = res.basePrice;
      amountDue.value = res.amountDue;
      coupon.value = res.coupon;
    },
  });
};

const submitForm = async () => {
  buttonLoading.value = true;
  try {
    await form.post("/subscription", {
      preserveScroll: true,
      onSuccess: () => {
        console.log("Form submitted successfully");
      },
      onError: (errors) => {
        console.error("Form submission failed with errors:", errors);
      },
      onFinish: () => {
        buttonLoading.value = false;
      },
    });
  } catch (error) {
    console.error("Unexpected error during submission:", error);
    buttonLoading.value = false;
  }
};

function confirmAction() {
  showConfirmModal.value = false;
  submitForm();
}
</script>

<style scoped>
@keyframes scaleIn {
  from {
    transform: scale(0.9);
    opacity: 0;
  }

  to {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes pulse {

  0%,
  100% {
    opacity: 0.75;
  }

  50% {
    opacity: 1;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>