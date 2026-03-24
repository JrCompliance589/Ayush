<!-- <template>
  <div v-if="isPaymentLoading" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg text-center text-sm">
      <div class="flex justify-center mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24">
          <path fill="black" d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,20a9,9,0,1,1,9-9A9,9,0,0,1,12,21Z"
            transform="matrix(0 0 0 0 12 12)">
            <animateTransform id="svgSpinnersPulseRingsMultiple0" attributeName="transform"
              begin="0;svgSpinnersPulseRingsMultiple2.end" calcMode="spline" dur="1.2s" keySplines=".52,.6,.25,.99"
              type="translate" values="12 12;0 0" />
            <animateTransform additive="sum" attributeName="transform" begin="0;svgSpinnersPulseRingsMultiple2.end"
              calcMode="spline" dur="1.2s" keySplines=".52,.6,.25,.99" type="scale" values="0;1" />
            <animate attributeName="opacity" begin="0;svgSpinnersPulseRingsMultiple2.end" calcMode="spline" dur="1.2s"
              keySplines=".52,.6,.25,.99" values="1;0" />
          </path>
          <path fill="black" d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,20a9,9,0,1,1,9-9A9,9,0,0,1,12,21Z"
            transform="matrix(0 0 0 0 12 12)">
            <animateTransform id="svgSpinnersPulseRingsMultiple1" attributeName="transform"
              begin="svgSpinnersPulseRingsMultiple0.begin+0.2s" calcMode="spline" dur="1.2s" keySplines=".52,.6,.25,.99"
              type="translate" values="12 12;0 0" />
            <animateTransform additive="sum" attributeName="transform" begin="svgSpinnersPulseRingsMultiple0.begin+0.2s"
              calcMode="spline" dur="1.2s" keySplines=".52,.6,.25,.99" type="scale" values="0;1" />
            <animate attributeName="opacity" begin="svgSpinnersPulseRingsMultiple0.begin+0.2s" calcMode="spline"
              dur="1.2s" keySplines=".52,.6,.25,.99" values="1;0" />
          </path>
          <path fill="black" d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,20a9,9,0,1,1,9-9A9,9,0,0,1,12,21Z"
            transform="matrix(0 0 0 0 12 12)">
            <animateTransform id="svgSpinnersPulseRingsMultiple2" attributeName="transform"
              begin="svgSpinnersPulseRingsMultiple0.begin+0.4s" calcMode="spline" dur="1.2s" keySplines=".52,.6,.25,.99"
              type="translate" values="12 12;0 0" />
            <animateTransform additive="sum" attributeName="transform" begin="svgSpinnersPulseRingsMultiple0.begin+0.4s"
              calcMode="spline" dur="1.2s" keySplines=".52,.6,.25,.99" type="scale" values="0;1" />
            <animate attributeName="opacity" begin="svgSpinnersPulseRingsMultiple0.begin+0.4s" calcMode="spline"
              dur="1.2s" keySplines=".52,.6,.25,.99" values="1;0" />
          </path>
        </svg>
      </div>
      <p>{{ $t("Please wait as the payment is being processed!") }}</p>
      <Link href="billing" class="underline">{{ $t("Go to billing") }}</Link>
    </div>
  </div>
  <AppLayout>
    <div class="bg-white md:bg-inherit pt-0 px-4 md:pt-8 md:p-8 rounded-[5px] text-[#000] overflow-y-scroll">
      <div class="flex justify-between mt-8 md:mt-0">
        <div>
          <h2 class="text-xl mb-1">{{ $t("Billing and subscription") }}</h2>
          <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
              <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
            </svg>
            <span class="ml-1 mt-1">{{
              $t("View and manage your billing history")
              }}</span>
          </p>
        </div>
      </div>
      <div v-if="!subscriptionIsActive"
        class="bg-red-500 p-5 w-100 mb-4 rounded-[0.5rem] flex justify-between items-center space-x-4 text-white">
        <div class="flex items-center space-x-4">
          <span>
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 56 56">
              <path fill="currentColor"
                d="M9.965 50.207h36.07c3.985 0 6.469-2.86 6.469-6.469c0-1.078-.281-2.18-.867-3.187L33.567 9.074c-1.22-2.133-3.352-3.281-5.555-3.281c-2.18 0-4.336 1.148-5.579 3.281l-18.07 31.5a6.253 6.253 0 0 0-.867 3.164c0 3.61 2.508 6.469 6.469 6.469m.047-3.68c-1.641 0-2.72-1.336-2.72-2.789c0-.422.071-.914.306-1.406l18.046-31.477c.516-.89 1.454-1.312 2.368-1.312c.914 0 1.804.398 2.32 1.312l18.047 31.5c.234.47.351.961.351 1.383c0 1.453-1.125 2.79-2.742 2.79Zm18-12.117c1.125 0 1.78-.656 1.804-1.875l.328-12.351c.024-1.196-.914-2.086-2.156-2.086c-1.265 0-2.156.867-2.133 2.062l.305 12.375c.023 1.196.68 1.875 1.852 1.875m0 7.617c1.359 0 2.53-1.078 2.53-2.437c0-1.383-1.148-2.438-2.53-2.438c-1.383 0-2.532 1.078-2.532 2.438c0 1.336 1.172 2.437 2.532 2.437" />
            </svg>
          </span>
          <h2 v-if="props.subscription?.status === 'trial'" class="text-sm">
            {{ $t("Your trial period is over") }} <br />
            {{ $t("Please subscribe to a plan to continue using the app") }}.
          </h2>
          <h2 v-if="props.subscription?.status === 'active'" class="text-sm">
            {{ $t("We were unable to autorenew your subscription") }} <br />
            {{ $t("To continue using the app, please make a payment of") }}
            {{ props.subscriptionDetails?.accountBalance }}.
          </h2>
        </div>
        <div>
          <div class="float-right">
            <Link href="/subscription" v-if="props.subscription?.status === 'trial'" type="button"
              class="rounded-md bg-white text-gray-600 px-3 py-2 text-sm shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            {{ $t("Subscribe") }}</Link>
            <button @click="openModal()" v-if="
              props.subscription?.status === 'active' &&
              props.setting['enable_custom_payment'] == 1
            " type="button"
              class="rounded-md bg-white text-gray-600 px-3 py-2 text-sm shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
              {{ $t("Add payment") }}
            </button>
          </div>
        </div>
      </div>
      <div class="md:flex bg-slate-100 md:bg-white shadow-sm p-5 rounded-[0.5rem] space-y-6">
        <div class="md:w-[50%]">
          <div class="mb-5">
            <h2 class="text-sm text-gray-500 tracking-[0px]">
              {{ $t("Plan details") }}
            </h2>
            <h2 v-if="props.subscription?.status === 'trial'" class="text-[18px]">
              {{ $t("Trial period") }}
            </h2>
            <h2 v-if="props.subscription?.status === 'active'" class="text-[18px]">
              {{ props.subscription?.plan?.name }}
            </h2>
          </div>
          <div v-if="props.subscription?.status != 'trial'" class="flex gap-x-3">
            <div>
              <h2 class="text-sm text-gray-500 tracking-[0px]">
                {{ $t("Start date") }}
              </h2>
              <span class="text-sm">{{ props.subscription?.start_date }}</span>
            </div>
            <div>
              <h2 class="text-sm text-gray-500 tracking-[0px]">
                {{ $t("Next billing date") }}
              </h2>
              <span class="text-sm">{{ props.subscription?.valid_until }}</span>
            </div>
          </div>
          <div v-if="props.subscription?.status === 'trial'" class="flex space-x-6">
            <div>
              <h2 class="text-sm text-gray-500 tracking-[0px]">
                {{ $t("Trial expires on") }}
              </h2>
              <span class="text-sm">{{ props.subscription?.valid_until }}</span>
            </div>
          </div>
        </div>
        <div class="md:w-[50%]">
          <div class="md:float-right gap-y-2 gap-x-3" :class="props.setting['enable_custom_payment'] == 0
            ? 'flex'
            : 'grid grid-cols-2'
            ">
            <button @click="openModal()" type="button"
              class="w-full capitalize rounded-md bg-primary px-3 py-2 text-sm text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 mr-2 col-span-2 md:col-span-1 text-center">
              {{ $t("Add payment") }}
            </button>
            <Link href="/subscription" v-if="props.subscription?.status === 'trial'"
              class="w-full rounded-md bg-primary px-3 py-2 text-sm text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 col-span-2 md:col-span-1 text-center">
            {{ $t("Subscribe to plan") }}</Link>
            <button v-if="props.subscription?.status === 'active'" @click="showUpgradeModal = true" type="button"
              class="w-full rounded-md bg-primary px-3 py-2 text-sm text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 col-span-2 md:col-span-1 text-center">
              {{ $t("Upgrade subscription") }}
            </button>
          </div>
        </div>
      </div>

      <div v-if="showUpgradeModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
          <h2 class="text-lg font-semibold mb-4">Upgrade Subscription</h2>
          <p class="text-gray-600 mb-2">
            Your current subscription will be lost.
          </p>
          <p class="text-sm text-red-600 mb-6">
            You will have to pay for the new subscription you select.
          </p>
          <div class="flex justify-center space-x-4">
            <button @click="proceedToUpgrade" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
              Continue
            </button>
            <button @click="showUpgradeModal = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
              Cancel
            </button>
          </div>
        </div>
      </div>

      <div v-if="showPaymentSuccess" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center w-80">
          <h2 class="text-green-600 text-xl font-bold mb-4">
            Payment Successful
          </h2>
          <p class="text-gray-700">{{ paymentMessage }}</p>
          <button @click="showPaymentSuccess = false"
            class="mt-6 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
            Close
          </button>
        </div>
      </div>

      <div v-if="showPaymentFailed" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center w-80">
          <h2 class="text-red-600 text-xl font-bold mb-4">Payment Failed</h2>
          <p class="text-gray-700">{{ paymentMessage }}</p>
          <button @click="showPaymentFailed = false"
            class="mt-6 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
            Close
          </button>
        </div>
      </div>

      <div class="bg-slate-100 md:bg-gray-50 p-5 md:px-0 rounded-[0.5rem] mt-10 w-full">
        <div class="w-full">
          <div class="mb-5">
            <h2 class="text-xl tracking-[0px]">{{ $t("Invoices") }}</h2>
            <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
              <span class="ml-1 mt-1">{{
                $t("You can view your invoices and past billing details here")
                }}</span>
            </p>
          </div>
          <div class="w-100">
            <BillingTable :rows="props.rows" :filters="props.filters" />
          </div>
        </div>
      </div>
    </div>
    <Modal :label="label" :isOpen="isOpenModal">
      <form @submit.prevent="submitForm()" class="gap-y-4">
        <h2 class="text-2xl mb-4">{{ $t("Add payment") }}</h2>
        <p class="text-sm">{{ $t("Add your own custom amount") }}</p>
        <FormInput v-model="form.amount" :error="form.errors.amount" :name="''" :type="'number'" :class="'w-100'" />
        <h2 class="text-sm mt-4 mb-2">{{ $t("Pay via") }}</h2>
        <div class="grid grid-cols-2 gap-2">
          <div v-for="(item, index) in props.methods" :key="index" class="">
            <div class="flex items-center">
              <label @click="selectPayment(item.name)" for="myCheckbox" class="cursor-pointer">
                <div class="w-5 h-5 border border-gray-400 rounded-md flex items-center justify-center"
                  :class="form.method === item.name ? 'bg-[#000]' : ''">
                  <svg v-if="form.method === item.name" class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
              </label>
              <span @click="selectPayment(item.name)" class="ml-2 text-sm cursor-pointer">{{ item.name }}</span>
            </div>
          </div>
        </div>
        <div class="form-error text-[#b91c1c] text-xs mt-2">
          {{ form.errors.method }}
        </div>
        <div class="mt-6 flex">
          <button type="button" @click.self="onClose"
            class="inline-flex justify-center rounded-md border border-transparent bg-slate-50 px-4 py-2 text-sm text-slate-500 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 mr-4">
            {{ $t("Cancel") }}
          </button>
          <button :class="[
            'inline-flex justify-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2',
            { 'opacity-50': isLoading },
          ]" :disabled="isLoading">
            <span v-if="isLoading" class="flex space-x-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                <path fill="currentColor"
                  d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5" />
                <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                  <animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite"
                    to="360 12 12" type="rotate" />
                </path>
              </svg>
              <span>{{ $t("Processing") }}</span>
            </span>
            <span v-else class="flex space-x-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                <path fill="currentColor"
                  d="M12 13a1.49 1.49 0 0 0-1 2.61V17a1 1 0 0 0 2 0v-1.39A1.49 1.49 0 0 0 12 13m5-4V7A5 5 0 0 0 7 7v2a3 3 0 0 0-3 3v7a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-7a3 3 0 0 0-3-3M9 7a3 3 0 0 1 6 0v2H9Zm9 12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-7a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1Z" />
              </svg>
              <span>{{ $t("Continue") }}</span>
            </span>
          </button>
        </div>
      </form>
    </Modal>
  </AppLayout>
</template>
<script setup>
import AppLayout from "./../Layout/App.vue";
import BillingTable from "@/Components/Tables/BillingTable.vue";
import { Link, useForm, router } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import FormInput from "@/Components/FormInput.vue";
import { ref, onMounted } from "vue";
import Echo from "laravel-echo";
import Pusher from "pusher-js";

const props = defineProps([
  "subscription",
  "rows",
  "filters",
  "subscriptionIsActive",
  "subscriptionDetails",
  "methods",
  "isPaymentLoading",
  "pusherSettings",
  "organizationId",
  "setting",
]);

const showUpgradeModal = ref(false);

function proceedToUpgrade() {
  showUpgradeModal.value = false;
  router.visit("/subscription"); // redirect to subscription page
}

const isOpenModal = ref(false);
const isLoading = ref(false);

const form = useForm({
  amount: null,
  method: null,
});

const selectPayment = (method) => {
  form.method = method;
};

function openModal() {
  isOpenModal.value = true;
}

function onClose() {
  isOpenModal.value = false;
}

const submitForm = async () => {
  isLoading.value = true;
  form.post("/pay", {
    preserveScroll: true,
    onFinish: () => {
      isLoading.value = false;
    },
  });
};

onMounted(() => {
  //Pusher.logToConsole = true;
  if (
    props.pusherSettings["pusher_app_key"] != null &&
    props.pusherSettings["pusher_app_cluster"] != null
  ) {
    window.Pusher = Pusher;

    window.Echo = new Echo({
      broadcaster: "pusher",
      key: props.pusherSettings["pusher_app_key"],
      cluster: props.pusherSettings["pusher_app_cluster"],
      encrypted: true,
    });

    window.Echo.channel("payments.ch" + props.organizationId).listen(
      "NewPaymentEvent",
      (event) => {
        router.visit("/billing", {});
      }
    );
  }
});

const showPaymentSuccess = ref(false);
const showPaymentFailed = ref(false);
const paymentMessage = ref("");

onMounted(async () => {
  const params = new URLSearchParams(window.location.search);
  const paymentId = params.get("razorpay_payment_id");

  if (paymentId) {
    try {
      const response = await fetch("/verify-payment", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
        },
        body: JSON.stringify({ paymentId }),
      });

      const result = await response.json();
      console.log("Payment verify result:", result);

      if (result.status === "success") {
        paymentMessage.value = `₹${result.amount} credited successfully!`;
        showPaymentSuccess.value = true;
      } else {
        paymentMessage.value = result.reason || "Payment failed!";
        showPaymentFailed.value = true;
      }
    } catch (e) {
      console.error("Verification failed:", e);
      paymentMessage.value = "Server error. Try again later.";
      showPaymentFailed.value = true;
    }
  }
});
</script> -->

<!-- ========================================= NEW UI CODE ==================================== -->

<template>
  <!-- Payment Processing Overlay -->
  <div v-if="isPaymentLoading"
    class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/40 backdrop-blur-md flex items-center justify-center z-50">
    <div
      class="bg-white p-8 rounded-2xl shadow-2xl text-center max-w-md mx-4 transform scale-100 animate-[scaleIn_0.3s_ease-out]">
      <div class="flex justify-center mb-6">
        <div class="relative">
          <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" class="text-[#ff5100]">
            <path fill="currentColor" d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,20a9,9,0,1,1,9-9A9,9,0,0,1,12,21Z"
              transform="matrix(0 0 0 0 12 12)">
              <animateTransform id="ring0" attributeName="transform" begin="0;ring2.end" calcMode="spline" dur="1.2s"
                keySplines=".52,.6,.25,.99" type="translate" values="12 12;0 0" />
              <animateTransform additive="sum" attributeName="transform" begin="0;ring2.end" calcMode="spline"
                dur="1.2s" keySplines=".52,.6,.25,.99" type="scale" values="0;1" />
              <animate attributeName="opacity" begin="0;ring2.end" calcMode="spline" dur="1.2s"
                keySplines=".52,.6,.25,.99" values="1;0" />
            </path>
            <path fill="currentColor" d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,20a9,9,0,1,1,9-9A9,9,0,0,1,12,21Z"
              transform="matrix(0 0 0 0 12 12)">
              <animateTransform id="ring1" attributeName="transform" begin="ring0.begin+0.2s" calcMode="spline"
                dur="1.2s" keySplines=".52,.6,.25,.99" type="translate" values="12 12;0 0" />
              <animateTransform additive="sum" attributeName="transform" begin="ring0.begin+0.2s" calcMode="spline"
                dur="1.2s" keySplines=".52,.6,.25,.99" type="scale" values="0;1" />
              <animate attributeName="opacity" begin="ring0.begin+0.2s" calcMode="spline" dur="1.2s"
                keySplines=".52,.6,.25,.99" values="1;0" />
            </path>
            <path fill="currentColor" d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,20a9,9,0,1,1,9-9A9,9,0,0,1,12,21Z"
              transform="matrix(0 0 0 0 12 12)">
              <animateTransform id="ring2" attributeName="transform" begin="ring0.begin+0.4s" calcMode="spline"
                dur="1.2s" keySplines=".52,.6,.25,.99" type="translate" values="12 12;0 0" />
              <animateTransform additive="sum" attributeName="transform" begin="ring0.begin+0.4s" calcMode="spline"
                dur="1.2s" keySplines=".52,.6,.25,.99" type="scale" values="0;1" />
              <animate attributeName="opacity" begin="ring0.begin+0.4s" calcMode="spline" dur="1.2s"
                keySplines=".52,.6,.25,.99" values="1;0" />
            </path>
          </svg>
        </div>
      </div>
      <h3 class="text-xl font-semibold text-gray-800 mb-2">Processing Payment</h3>
      <p class="text-gray-600 mb-4">{{ $t("Please wait as the payment is being processed!") }}</p>
      <Link href="billing" class="text-[#ff5100] hover:text-[#cc4100] font-medium underline transition-colors">
      {{ $t("Go to billing") }}
      </Link>
    </div>
  </div>

  <AppLayout>
    <div class="min-h-screen bg-white px-4 md:px-8 py-6 md:py-10">
      <!-- Header Section -->
      <div class="flex-1">
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
            {{ $t('View and manage your billing history') }}
          </p>
        </div>
      </div>

      <!-- Alert Banner -->
      <div v-if="!subscriptionIsActive" class=" mx-auto mb-8">
        <div class="bg-gradient-to-r from-red-500 via-red-600 to-orange-600 rounded-2xl p-6 shadow-xl">
          <div class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0">
            <div class="flex items-start space-x-4">
              <div class="flex-shrink-0 bg-white/20 rounded-full p-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 56 56" class="text-white">
                  <path fill="currentColor"
                    d="M9.965 50.207h36.07c3.985 0 6.469-2.86 6.469-6.469c0-1.078-.281-2.18-.867-3.187L33.567 9.074c-1.22-2.133-3.352-3.281-5.555-3.281c-2.18 0-4.336 1.148-5.579 3.281l-18.07 31.5a6.253 6.253 0 0 0-.867 3.164c0 3.61 2.508 6.469 6.469 6.469m.047-3.68c-1.641 0-2.72-1.336-2.72-2.789c0-.422.071-.914.306-1.406l18.046-31.477c.516-.89 1.454-1.312 2.368-1.312c.914 0 1.804.398 2.32 1.312l18.047 31.5c.234.47.351.961.351 1.383c0 1.453-1.125 2.79-2.742 2.79Zm18-12.117c1.125 0 1.78-.656 1.804-1.875l.328-12.351c.024-1.196-.914-2.086-2.156-2.086c-1.265 0-2.156.867-2.133 2.062l.305 12.375c.023 1.196.68 1.875 1.852 1.875m0 7.617c1.359 0 2.53-1.078 2.53-2.437c0-1.383-1.148-2.438-2.53-2.438c-1.383 0-2.532 1.078-2.532 2.438c0 1.336 1.172 2.437 2.532 2.437" />
                </svg>
              </div>
              <div class="text-white space-y-1">
                <h3 v-if="props.subscription?.status === 'trial'" class="text-lg font-semibold">
                  {{ $t("Your trial period is over") }}
                </h3>
                <h3 v-if="props.subscription?.status === 'active'" class="text-lg font-semibold">
                  {{ $t("We were unable to autorenew your subscription") }}
                </h3>
                <p v-if="props.subscription?.status === 'trial'" class="text-sm text-white/90">
                  {{ $t("Please subscribe to a plan to continue using the app") }}
                </p>
                <p v-if="props.subscription?.status === 'active'" class="text-sm text-white/90">
                  <!-- {{ $t("To continue using the app, please make a payment of") }} {{
                    props.subscriptionDetails?.accountBalance }} -->

                  {{ $t("To continue using the app, please take a subscription") }}

                </p>
              </div>
            </div>
            <div class="flex-shrink-0">
              <Link v-if="props.subscription?.status === 'trial'" href="/subscription"
                class="inline-flex items-center px-6 py-3 bg-white text-[#ff5100] rounded-xl font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200">
              {{ $t("Subscribe") }}
              </Link>
              <button v-if="props.subscription?.status === 'active' && props.setting['enable_custom_payment'] == 1"
                @click="openModal()"
                class="inline-flex items-center px-6 py-3 bg-white text-[#ff5100] rounded-xl font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200">
                {{ $t("Add payment") }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Plan Details Card -->
      <div class="mx-auto my-8">
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
          <div class="bg-gradient-to-r from-[#ff5100] to-[#ff7733] p-4">
            <h2 class="text-lg font-bold text-white">{{ $t("Current Plan") }}</h2>
          </div>
          <div class="p-4">
            <div class="grid md:grid-cols-2 gap-8">
              <!-- Plan Info -->
              <div class="space-y-6">
                <div>
                  <p class="text-sm text-gray-500 uppercase tracking-wider mb-2">{{ $t("Plan details") }}</p>
                  <h3 v-if="props.subscription?.status === 'trial'" class="font-bold text-gray-900">
                    {{ $t("Trial period") }}
                  </h3>
                  <h3 v-if="props.subscription?.status === 'active'" class="font-bold text-gray-900">
                    {{ props.subscription?.plan?.name }}
                  </h3>
                </div>

                <div v-if="props.subscription?.status != 'trial'" class="grid grid-cols-2 gap-6">
                  <div class="bg-orange-50 rounded-xl p-4">
                    <p class="text-xs text-gray-600 uppercase tracking-wider mb-1">{{ $t("Start date") }}</p>
                    <p class="font-semibold text-gray-900">{{ props.subscription?.start_date }}</p>
                  </div>
                  <div class="bg-orange-50 rounded-xl p-4">
                    <p class="text-xs text-gray-600 uppercase tracking-wider mb-1">{{ $t("Next billing date") }}</p>
                    <p class="font-semibold text-gray-900">{{ props.subscription?.valid_until }}</p>
                  </div>
                </div>

                <div v-if="props.subscription?.status === 'trial'" class="bg-orange-50 rounded-xl p-4">
                  <p class="text-xs text-gray-600 uppercase tracking-wider mb-1">{{ $t("Trial expires on") }}</p>
                  <p class="font-semibold text-gray-900">{{ props.subscription?.valid_until }}</p>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex items-center justify-center md:justify-end">
                <div class="grid grid-cols-1 gap-4 w-full md:w-auto">
                  <button @click="openModal()"
                    class="px-4 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff7733] text-white rounded-xl font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200 flex items-center justify-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2">
                      <rect x="2" y="5" width="20" height="14" rx="2" />
                      <line x1="2" y1="10" x2="22" y2="10" />
                    </svg>
                    <span class="text-sm">{{ $t("Add payment") }}</span>
                  </button>

                  <Link v-if="props.subscription?.status === 'trial'" href="/subscription"
                    class="px-8 py-4 bg-gray-900 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200 flex items-center justify-center space-x-2">
                  <span>{{ $t("Subscribe to plan") }}</span>
                  </Link>

                  <button v-if="props.subscription?.status === 'active'" @click="showUpgradeModal = true"
                    class="px-4 py-3 border-2 border-primary text-primary hover:bg-primary hover:text-white rounded-xl font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200 flex items-center justify-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2">
                      <line x1="12" y1="19" x2="12" y2="5" />
                      <polyline points="5 12 12 5 19 12" />
                    </svg>
                    <span class="text-sm">{{ $t("Upgrade subscription") }}</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Upgrade Modal -->
      <div v-if="showUpgradeModal"
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
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Upgrade Subscription</h2>
            <p class="text-gray-600 mb-1">Your current subscription will be lost.</p>
            <p class="text-sm text-red-600 font-medium">You will have to pay for the new subscription you select.</p>
          </div>
          <div class="flex space-x-4">
            <button @click="proceedToUpgrade"
              class="flex-1 px-6 py-3 bg-[#ff5100] text-white rounded-xl font-semibold hover:bg-[#cc4100] transition-colors">
              Continue
            </button>
            <button @click="showUpgradeModal = false"
              class="flex-1 px-6 py-3 bg-gray-200 text-gray-800 rounded-xl font-semibold hover:bg-gray-300 transition-colors">
              Cancel
            </button>
          </div>
        </div>
      </div>

      <!-- Payment Success Modal -->
      <div v-if="showPaymentSuccess"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div
          class="bg-white rounded-2xl shadow-2xl text-center max-w-sm w-full p-8 transform scale-100 animate-[scaleIn_0.3s_ease-out]">
          <div class="mx-auto w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" class="text-green-600">
              <path fill="currentColor" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-gray-900 mb-3">Payment Successful</h2>
          <p class="text-gray-600 mb-6">{{ paymentMessage }}</p>
          <button @click="showPaymentSuccess = false"
            class="w-full px-6 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition-colors">
            Close
          </button>
        </div>
      </div>

      <!-- Payment Failed Modal -->
      <div v-if="showPaymentFailed"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div
          class="bg-white rounded-2xl shadow-2xl text-center max-w-sm w-full p-8 transform scale-100 animate-[scaleIn_0.3s_ease-out]">
          <div class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" class="text-red-600">
              <path fill="currentColor"
                d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-gray-900 mb-3">Payment Failed</h2>
          <p class="text-gray-600 mb-6">{{ paymentMessage }}</p>
          <button @click="showPaymentFailed = false"
            class="w-full px-6 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition-colors">
            Close
          </button>
        </div>
      </div>

      <!-- Invoices Section -->
      <div class="mx-auto">
        <!-- <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100"> -->
        <div class="p-6 pb-0">
          <h2 class="text-2xl font-bold text-black mb-2">{{ $t("Invoices") }}</h2>
          <p class="text-gray-500 text-sm">{{ $t("You can view your invoices and past billing details here") }}</p>
        </div>
        <div class="p-6">
          <BillingTable :rows="props.rows" :filters="props.filters" />
        </div>
        <!-- </div> -->
      </div>

      <!-- Add Payment Modal -->
      <Modal :label="label" :isOpen="isOpenModal">
        <div class="space-y-6">


          <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $t("Add payment") }}</h2>
            <p class="text-sm text-gray-600">{{ $t("Add your own custom amount") }}</p>
          </div>

          <div>
            <FormInput v-model="form.amount" :error="form.errors.amount" :name="''" :type="'number'"
              :class="'w-full'" />
            <span class="text-sm text-yellow-600" v-if="form.amount >= 1 && form.amount < 1000">{{ $t(`Amount should be
              greater
              than or equal to 1000`) }}</span>
          </div>

          <div>
            <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ $t("Pay via") }}</h3>
            <div class="grid grid-cols-2 gap-3">
              <div v-for="(item, index) in props.methods" :key="index" @click="selectPayment(item.name)"
                class="flex items-center space-x-3 p-4 border-2 rounded-xl cursor-pointer transition-all"
                :class="form.method === item.name ? 'border-[#ff5100] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                <div class="w-5 h-5 rounded-md border-2 flex items-center justify-center transition-all"
                  :class="form.method === item.name ? 'bg-[#ff5100] border-[#ff5100]' : 'border-gray-400'">
                  <svg v-if="form.method === item.name" class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                  </svg>
                </div>

                <svg v-if="item.name === 'Razorpay'" width="100px" height="24px" viewBox="0 0 1896 401" version="1.1"
                  xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                  <defs></defs>
                  <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <g id="Group">
                      <path
                        d="M451.9209,151.4937 C448.9309,162.6497 443.1359,170.8377 434.5349,176.0657 C425.9239,181.2927 413.8469,183.9117 398.2689,183.9117 L348.7769,183.9117 L366.1509,119.0807 L415.6429,119.0807 C431.2089,119.0807 441.8959,121.6947 447.7019,126.9217 C453.4969,132.1547 454.9109,140.3437 451.9209,151.4937 M503.1739,150.0967 C509.4679,126.6377 506.8589,108.6267 495.3509,96.0797 C483.8409,83.5327 463.6739,77.2627 434.8709,77.2627 L324.3909,77.2627 L257.8969,325.4027 L311.5719,325.4027 L338.3809,225.3777 L373.5809,225.3777 C381.4739,225.3777 387.6869,226.6577 392.2309,229.2137 C396.7849,231.7747 399.4509,236.3067 400.2739,242.8037 L409.8479,325.4027 L467.3589,325.4027 L458.0289,248.3847 C456.1279,231.1897 448.2589,221.0827 434.4309,218.0637 C452.0599,212.9577 466.8259,204.4677 478.7179,192.6167 C490.5989,180.7717 498.7579,166.6017 503.1739,150.0967"
                        id="Fill-1" fill="#072654"></path>
                      <path
                        d="M633.625,236.533 C629.14,253.258 622.231,266.042 612.901,274.868 C603.56,283.7 592.386,288.111 579.382,288.111 C566.122,288.111 557.128,283.758 552.387,275.042 C547.623,266.332 547.461,253.733 551.889,237.228 C556.305,220.735 563.352,207.841 573.053,198.539 C582.742,189.255 594.09,184.602 607.105,184.602 C620.11,184.602 628.919,189.082 633.485,198.024 C638.053,206.966 638.11,219.802 633.625,236.533 L633.625,236.533 Z M657.153,148.706 L650.431,173.8 C647.521,164.736 641.9,157.538 633.578,152.195 C625.245,146.852 614.918,144.174 602.608,144.174 C587.506,144.174 572.983,148.069 559.052,155.852 C545.12,163.64 532.938,174.617 522.519,188.786 C512.099,202.961 504.461,219.107 499.604,237.228 C494.748,255.356 493.774,271.328 496.695,285.149 C499.616,298.977 505.944,309.605 515.691,317.04 C525.428,324.481 537.969,328.19 553.303,328.19 C565.612,328.19 577.342,325.635 588.469,320.523 C599.595,315.418 609.041,308.325 616.818,299.266 L609.807,325.403 L661.731,325.403 L709.079,148.706 L657.153,148.706 Z"
                        id="Fill-3" fill="#072654"></path>
                      <polygon id="Fill-5" fill="#072654"
                        points="895.79 148.7061 744.882 148.7061 734.334 188.0911 822.155 188.0911 706.042 288.4581 696.132 325.4031 851.92 325.4031 862.478 286.0241 768.388 286.0241 886.263 184.2541">
                      </polygon>
                      <path
                        d="M1028.6514,236.1853 C1023.9804,253.6053 1017.0604,266.6273 1007.9044,275.2223 C998.7484,283.8163 987.6674,288.1103 974.6634,288.1103 C947.4714,288.1103 938.5234,270.8113 947.7964,236.1853 C952.4094,218.9903 959.3634,206.0383 968.6594,197.3283 C977.9654,188.6123 989.2324,184.2543 1002.4804,184.2543 C1015.4844,184.2543 1024.2584,188.6123 1028.7794,197.3283 C1033.2984,206.0383 1033.2644,218.9903 1028.6514,236.1853 M1059.0304,155.3243 C1047.0804,147.8943 1031.8154,144.1743 1013.2244,144.1743 C994.4014,144.1743 976.9694,147.8943 960.9174,155.3243 C944.8644,162.7653 931.1984,173.4523 919.9214,187.3893 C908.6314,201.3323 900.4954,217.5943 895.5114,236.1853 C890.5274,254.7763 889.9484,271.0323 893.7734,284.9753 C897.5864,298.9183 905.5144,309.6053 917.5914,317.0403 C929.6574,324.4813 945.0954,328.1903 963.9194,328.1903 C982.5094,328.1903 999.7674,324.4813 1015.7054,317.0403 C1031.6184,309.6053 1045.2374,298.9183 1056.5264,284.9753 C1067.8034,271.0323 1075.9404,254.7763 1080.9244,236.1853 C1085.9084,217.5943 1086.4884,201.3323 1082.6744,187.3893 C1078.8494,173.4523 1070.9674,162.7653 1059.0304,155.3243"
                        id="Fill-7" fill="#072654"></path>
                      <path
                        d="M1602.1367,236.533 C1597.6517,253.258 1590.7427,266.042 1581.4127,274.868 C1572.0817,283.7 1560.8857,288.111 1547.8817,288.111 C1534.6457,288.111 1525.6397,283.758 1520.8987,275.042 C1516.1347,266.332 1515.9727,253.733 1520.4007,237.228 C1524.8167,220.735 1531.8637,207.841 1541.5647,198.539 C1551.2537,189.255 1562.6017,184.602 1575.6167,184.602 C1588.6217,184.602 1597.4307,189.082 1601.9967,198.024 C1606.5647,206.966 1606.6217,219.802 1602.1367,236.533 L1602.1367,236.533 Z M1625.6647,148.706 L1618.9427,173.8 C1616.0327,164.736 1610.4117,157.538 1602.0897,152.195 C1593.7567,146.852 1583.4297,144.174 1571.1197,144.174 C1556.0177,144.174 1541.4947,148.069 1527.5637,155.852 C1513.6317,163.64 1501.4497,174.617 1491.0307,188.786 C1480.6107,202.961 1472.9717,219.107 1468.1157,237.228 C1463.2597,255.356 1462.2967,271.328 1465.2067,285.149 C1468.1267,298.977 1474.4447,309.605 1484.2027,317.04 C1493.9397,324.481 1506.4807,328.19 1521.8147,328.19 C1534.1227,328.19 1545.8537,325.635 1556.9797,320.523 C1568.1067,315.418 1577.5527,308.325 1585.3297,299.266 L1578.3187,325.403 L1630.2427,325.403 L1677.5907,148.706 L1625.6647,148.706 Z"
                        id="Fill-9" fill="#072654"></path>
                      <path
                        d="M1244.165,196.1055 L1257.401,148.0105 C1252.904,145.6865 1246.946,144.5225 1239.517,144.5225 C1227.66,144.5225 1216.243,147.4835 1205.244,153.4115 C1195.798,158.4975 1187.754,165.6365 1180.962,174.5815 L1187.847,148.6815 L1172.813,148.7065 L1135.938,148.7065 L1088.227,325.4025 L1140.87,325.4025 L1165.616,233.0505 C1169.221,219.5765 1175.688,209.0635 1185.042,201.5125 C1194.372,193.9615 1206.02,190.1825 1219.964,190.1825 C1228.563,190.1825 1236.619,192.1585 1244.165,196.1055"
                        id="Fill-11" fill="#072654"></path>
                      <path
                        d="M1390.6973,237.2256 C1386.2693,253.7306 1379.4083,266.3296 1370.1123,275.0396 C1360.7943,283.7556 1349.6433,288.1076 1336.6393,288.1076 C1323.6233,288.1076 1314.7573,283.6976 1310.0393,274.8656 C1305.3103,266.0396 1305.1943,253.2606 1309.6793,236.5296 C1314.1653,219.7996 1321.1423,206.9626 1330.6243,198.0206 C1340.1043,189.0786 1351.3593,184.5986 1364.3753,184.5986 C1377.1473,184.5986 1385.8293,189.2526 1390.4303,198.5426 C1395.0203,207.8376 1395.1133,220.7376 1390.6973,237.2256 M1427.4853,155.8486 C1417.7153,148.0656 1405.2783,144.1776 1390.1873,144.1776 C1376.9393,144.1776 1364.3293,147.1966 1352.3903,153.2356 C1340.4183,159.2856 1330.7173,167.5206 1323.2753,177.9806 L1323.4433,176.8216 L1332.2873,148.6786 L1322.1103,148.6786 L1322.1103,148.7036 L1280.9003,148.7036 L1267.8153,197.5656 C1267.6643,198.1336 1267.5373,198.6636 1267.3853,199.2376 L1213.4093,400.6806 L1266.0423,400.6806 L1293.2213,299.2696 C1295.8863,308.3216 1301.4273,315.4146 1309.8193,320.5206 C1318.2103,325.6316 1328.5603,328.1876 1340.8813,328.1876 C1356.2163,328.1876 1370.7963,324.4786 1384.6463,317.0376 C1398.4863,309.6076 1410.5163,298.9736 1420.7173,285.1466 C1430.9273,271.3306 1438.4623,255.3526 1443.3183,237.2256 C1448.1753,219.1036 1449.1823,202.9586 1446.3663,188.7836 C1443.5503,174.6136 1437.2443,163.6376 1427.4853,155.8486"
                        id="Fill-13" fill="#072654"></path>
                      <path
                        d="M1895.5381,148.7554 L1895.5721,148.7064 L1863.6921,148.7064 C1862.6731,148.7064 1861.7741,148.7354 1860.8421,148.7554 L1844.2961,148.7554 L1835.8351,160.5434 C1835.1571,161.4314 1834.4791,162.3254 1833.7491,163.3624 L1832.8271,164.7274 L1765.5851,258.3754 L1751.6421,148.7064 L1696.5641,148.7064 L1724.4561,315.3544 L1662.8591,400.6834 L1664.6151,400.6834 L1696.0651,400.6834 L1717.7341,400.6834 L1732.6621,379.5374 C1733.1021,378.9074 1733.4791,378.3914 1733.9491,377.7254 L1751.3691,353.0284 L1751.8681,352.3214 L1829.8131,241.8274 L1895.4851,148.8284 L1895.5721,148.7554 L1895.5381,148.7554 Z"
                        id="Fill-15" fill="#072654"></path>
                      <polygon id="Fill-17" fill="#3395FF"
                        points="122.6338 105.6902 106.8778 163.6732 197.0338 105.3642 138.0748 325.3482 197.9478 325.4032 285.0458 0.4822">
                      </polygon>
                      <path
                        d="M25.5947,232.9246 L0.8077,325.4026 L123.5337,325.4026 C123.5337,325.4026 173.7317,137.3196 173.7457,137.2656 C173.6987,137.2956 25.5947,232.9246 25.5947,232.9246"
                        id="Fill-19" fill="#072654"></path>
                    </g>
                  </g>
                </svg>

                <span v-else class="text-sm font-medium text-gray-900">{{ item.name }}</span>
              </div>
            </div>
            <div v-if="form.errors.method" class="text-red-600 text-sm mt-2">{{ form.errors.method }}</div>
          </div>

          <div class="flex space-x-4 pt-4">
            <button type="button" @click.self="onClose"
              class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
              {{ $t("Cancel") }}
            </button>
            <button @click="submitForm" :disabled="isLoading || form.method === null || form.amount < 1000"
              class="flex-1 px-6 py-3 bg-[#ff5100] text-white rounded-xl font-semibold hover:bg-[#cc4100] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2">
              <svg v-if="isLoading" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                <path fill="currentColor"
                  d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5" />
                <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                  <animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite"
                    to="360 12 12" type="rotate" />
                </path>
              </svg>
              <span>{{ isLoading ? $t("Processing") : $t("Continue") }}</span>
            </button>
          </div>

        </div>
      </Modal>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import BillingTable from "@/Components/Tables/BillingTable.vue";
import { Link, useForm, router } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import FormInput from "@/Components/FormInput.vue";
import { ref, onMounted } from "vue";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import { CreditCard } from "lucide-vue-next";
import { toast } from "vue3-toastify";

const props = defineProps([
  "subscription",
  "rows",
  "filters",
  "subscriptionIsActive",
  "subscriptionDetails",
  "methods",
  "isPaymentLoading",
  "pusherSettings",
  "organizationId",
  "setting",
]);

const showUpgradeModal = ref(false);
const isOpenModal = ref(false);
const isLoading = ref(false);
const showPaymentSuccess = ref(false);
const showPaymentFailed = ref(false);
const paymentMessage = ref("");

const form = useForm({
  amount: null,
  method: null,
});

function proceedToUpgrade() {
  showUpgradeModal.value = false;
  router.visit("/subscription");
}

const selectPayment = (method) => {
  if (form.method === method) {
    form.method = null;
  } else {
    form.method = method;
  }
};

function openModal() {
  isOpenModal.value = true;
}

function onClose() {
  isOpenModal.value = false;
}

const submitForm = async () => {
  if (form.method === null) {
    toast.error("Please select a payment method");
    return;
  }

  if (form.amount < 1000) {
    toast.error("Please enter an amount greater than 1000");
    return;
  }

  isLoading.value = true;
  form.post("/pay", {
    preserveScroll: true,
    onFinish: () => {
      isLoading.value = false;
    },
  });
};

onMounted(() => {
  if (
    props.pusherSettings["pusher_app_key"] != null &&
    props.pusherSettings["pusher_app_cluster"] != null
  ) {
    window.Pusher = Pusher;
    window.Echo = new Echo({
      broadcaster: "pusher",
      key: props.pusherSettings["pusher_app_key"],
      cluster: props.pusherSettings["pusher_app_cluster"],
      encrypted: true,
    });
    window.Echo.channel("payments.ch" + props.organizationId).listen(
      "NewPaymentEvent",
      (event) => {
        router.visit("/billing", {});
      }
    );
  }

  const params = new URLSearchParams(window.location.search);
  const paymentId = params.get("razorpay_payment_id");

  if (paymentId) {
    fetch("/verify-payment", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
      },
      body: JSON.stringify({ paymentId }),
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.status === "success") {
          paymentMessage.value = `₹${result.amount} credited successfully!`;
          showPaymentSuccess.value = true;
        } else {
          paymentMessage.value = result.reason || "Payment failed!";
          showPaymentFailed.value = true;
        }
      })
      .catch((e) => {
        paymentMessage.value = "Server error. Try again later.";
        showPaymentFailed.value = true;
      });
  }
});
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
</style>