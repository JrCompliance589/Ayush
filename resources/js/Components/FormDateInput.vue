<!-- <script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
  modelValue: [Date, String, null],
  defaultDate: { type: [Date, String], default: null },
  name: String,
  className: String,
  label: String,
  placeholder: { type: String, default: 'Select date' },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  helperText: String,
  errorMessage: String
});

const emit = defineEmits(['update:modelValue']);

const internalDate = ref(null);
const currentMonth = ref(new Date());
const isOpen = ref(false);
const dropdownRef = ref(null);

const monthNames = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
];

const quickOptions = [
  { label: '7 Days', days: 7 },
  { label: '1 Month', months: 1 },
  { label: '2 Months', months: 2 },
  { label: '3 Months', months: 3 }
];

/**
 * Convert Date → YYYY-MM-DD (LOCAL / IST safe)
 */
const toDateOnlyString = (date) => {
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, '0');
  const d = String(date.getDate()).padStart(2, '0');
  return `${y}-${m}-${d}`;
};

/**
 * Convert YYYY-MM-DD → Date (LOCAL)
 */
const fromDateOnlyString = (value) => {
  return new Date(`${value}T00:00:00`);
};

const model = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});

const displayValue = computed(() => {
  if (!model.value) return '';
  const d =
    typeof model.value === 'string'
      ? fromDateOnlyString(model.value)
      : new Date(model.value);

  return d.toLocaleDateString('en-IN', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
});

const calendarDays = computed(() => {
  const daysInMonth = new Date(
    currentMonth.value.getFullYear(),
    currentMonth.value.getMonth() + 1,
    0
  ).getDate();

  const firstDay = new Date(
    currentMonth.value.getFullYear(),
    currentMonth.value.getMonth(),
    1
  ).getDay();

  const days = [];
  for (let i = 0; i < firstDay; i++) days.push(null);
  for (let d = 1; d <= daysInMonth; d++) days.push(d);
  return days;
});

const initializeDate = () => {
  const source = model.value || props.defaultDate;
  if (!source) return;

  internalDate.value =
    typeof source === 'string'
      ? fromDateOnlyString(source)
      : new Date(source);

  currentMonth.value = new Date(internalDate.value);
};

const selectDate = (date) => {
  internalDate.value = date;
  model.value = toDateOnlyString(date); // ✅ STRING EMIT
  isOpen.value = false;
};

const handleQuickSelect = (option) => {
  const d = new Date();
  if (option.days) d.setDate(d.getDate() + option.days);
  if (option.months) d.setMonth(d.getMonth() + option.months);
  selectDate(d);
};

const handleDateClick = (day) => {
  if (!day) return;
  selectDate(
    new Date(
      currentMonth.value.getFullYear(),
      currentMonth.value.getMonth(),
      day
    )
  );
};

const prevMonth = () => {
  currentMonth.value = new Date(
    currentMonth.value.getFullYear(),
    currentMonth.value.getMonth() - 1
  );
};

const nextMonth = () => {
  currentMonth.value = new Date(
    currentMonth.value.getFullYear(),
    currentMonth.value.getMonth() + 1
  );
};

const isSelectedDate = (day) => {
  if (!day || !model.value) return false;
  const d = fromDateOnlyString(model.value);
  return (
    d.getDate() === day &&
    d.getMonth() === currentMonth.value.getMonth() &&
    d.getFullYear() === currentMonth.value.getFullYear()
  );
};

const handleClickOutside = (e) => {
  if (isOpen.value && dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    isOpen.value = false;
  }
};

watch(() => props.modelValue, initializeDate, { immediate: true });

onMounted(() => {
  initializeDate();
  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <div :class="className">
    <div ref="dropdownRef" class="relative">
      <input
        :name="name"
        readonly
        :value="displayValue"
        :placeholder="placeholder"
        :disabled="disabled"
        class="w-full cursor-pointer rounded-md border p-2"
        @click="isOpen = !isOpen"
      />

      <div v-if="isOpen" class="absolute z-50 mt-2 w-full bg-white border rounded p-4">
        <div class="grid grid-cols-2 gap-2 mb-4">
          <button v-for="o in quickOptions" :key="o.label" @click="handleQuickSelect(o)">
            {{ o.label }}
          </button>
        </div>

        <div class="flex justify-between mb-2">
          <button @click="prevMonth">‹</button>
          <span>{{ monthNames[currentMonth.getMonth()] }} {{ currentMonth.getFullYear() }}</span>
          <button @click="nextMonth">›</button>
        </div>

        <div class="grid grid-cols-7 gap-1">
          <button
            v-for="(day, i) in calendarDays"
            :key="i"
            :disabled="!day"
            @click="handleDateClick(day)"
            :class="{ 'bg-blue-500 text-white': isSelectedDate(day) }"
          >
            {{ day }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template> -->



<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
  modelValue: { type: [Date, String, null], default: null },
  defaultDate: { type: [Date, String], default: null },
  name: { type: String, default: '' },
  className: { type: String, default: '' },
  label: { type: String, default: '' },
  placeholder: { type: String, default: 'Select date' },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  helperText: { type: String, default: '' },
  errorMessage: { type: String, default: '' }
});

const emit = defineEmits(['update:modelValue']);

const internalDate = ref(null);
const currentMonth = ref(new Date());
const isOpen = ref(false);
const dropdownRef = ref(null);

const monthNames = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
];

const dayNames = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];

const quickOptions = [
  { label: 'Today', days: 0 },
  { label: '7 Days', days: 7 },
  { label: '1 Month', months: 1 },
  { label: '3 Months', months: 3 }
];

const toDateOnlyString = (date) => {
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, '0');
  const d = String(date.getDate()).padStart(2, '0');
  return `${y}-${m}-${d}`;
};

const fromDateOnlyString = (value) => {
  return new Date(`${value}T00:00:00`);
};

const model = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});

const displayValue = computed(() => {
  if (!model.value) return '';
  const d = typeof model.value === 'string'
    ? fromDateOnlyString(model.value)
    : new Date(model.value);

  return d.toLocaleDateString('en-IN', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
});

const calendarDays = computed(() => {
  const daysInMonth = new Date(
    currentMonth.value.getFullYear(),
    currentMonth.value.getMonth() + 1,
    0
  ).getDate();

  const firstDay = new Date(
    currentMonth.value.getFullYear(),
    currentMonth.value.getMonth(),
    1
  ).getDay();

  const days = [];
  for (let i = 0; i < firstDay; i++) days.push(null);
  for (let d = 1; d <= daysInMonth; d++) days.push(d);
  return days;
});

const initializeDate = () => {
  // If modelValue exists, use it
  if (model.value) {
    internalDate.value = typeof model.value === 'string'
      ? fromDateOnlyString(model.value)
      : new Date(model.value);
    currentMonth.value = new Date(internalDate.value);
    return;
  }

  // If no modelValue but defaultDate exists, set it
  if (props.defaultDate && !model.value) {
    const defaultDateObj = typeof props.defaultDate === 'string'
      ? fromDateOnlyString(props.defaultDate)
      : new Date(props.defaultDate);
    
    internalDate.value = defaultDateObj;
    currentMonth.value = new Date(defaultDateObj);
    model.value = toDateOnlyString(defaultDateObj);
  }
};

const selectDate = (date) => {
  internalDate.value = date;
  model.value = toDateOnlyString(date);
  isOpen.value = false;
};

const handleQuickSelect = (option) => {
  const d = new Date();
  if (option.days !== undefined) d.setDate(d.getDate() + option.days);
  if (option.months) d.setMonth(d.getMonth() + option.months);
  selectDate(d);
};

const handleDateClick = (day) => {
  if (!day) return;
  selectDate(
    new Date(
      currentMonth.value.getFullYear(),
      currentMonth.value.getMonth(),
      day
    )
  );
};

const prevMonth = () => {
  currentMonth.value = new Date(
    currentMonth.value.getFullYear(),
    currentMonth.value.getMonth() - 1
  );
};

const nextMonth = () => {
  currentMonth.value = new Date(
    currentMonth.value.getFullYear(),
    currentMonth.value.getMonth() + 1
  );
};

const isSelectedDate = (day) => {
  if (!day || !model.value) return false;
  const d = typeof model.value === 'string'
    ? fromDateOnlyString(model.value)
    : new Date(model.value);
  return (
    d.getDate() === day &&
    d.getMonth() === currentMonth.value.getMonth() &&
    d.getFullYear() === currentMonth.value.getFullYear()
  );
};

const isToday = (day) => {
  if (!day) return false;
  const today = new Date();
  return (
    today.getDate() === day &&
    today.getMonth() === currentMonth.value.getMonth() &&
    today.getFullYear() === currentMonth.value.getFullYear()
  );
};

const clearDate = (e) => {
  e.stopPropagation();
  model.value = null;
  internalDate.value = null;
};

const handleClickOutside = (e) => {
  if (isOpen.value && dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    isOpen.value = false;
  }
};

watch(() => props.modelValue, initializeDate, { immediate: true });

onMounted(() => {
  initializeDate();
  document.addEventListener('mousedown', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('mousedown', handleClickOutside);
});
</script>

<template>
  <div :class="className" class="w-full">
    <!-- Label -->
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1.5">
      {{ label }}
      <span v-if="required" class="text-[#ff5100] ml-1">*</span>
    </label>

    <div ref="dropdownRef" class="relative">
      <!-- Input Field -->
      <div
        @click="!disabled && (isOpen = !isOpen)"
        :class="[
          'relative w-full px-4 py-2.5 rounded-lg border-2 transition-all duration-200',
          disabled 
            ? 'bg-gray-50 border-gray-200 cursor-not-allowed' 
            : isOpen
              ? 'border-[#ff5100] bg-white shadow-sm'
              : errorMessage
                ? 'border-red-300 bg-white hover:border-red-400'
                : 'border-gray-300 bg-white hover:border-gray-400',
          !disabled && 'cursor-pointer'
        ]"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2.5 flex-1 min-w-0">
            <!-- Calendar Icon -->
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              :class="disabled ? 'text-gray-400' : 'text-[#ff5100]'"
            >
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span
              :class="[
                'text-sm truncate',
                model ? 'text-gray-900' : 'text-gray-400'
              ]"
            >
              {{ displayValue || placeholder }}
            </span>
          </div>
          <!-- Clear Button -->
          <button
            v-if="model && !disabled"
            @click="clearDate"
            type="button"
            class="ml-2 p-1 hover:bg-gray-100 rounded-full transition-colors"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="14"
              height="14"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="text-gray-500"
            >
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>
      </div>

      <!-- Dropdown Calendar -->
      <div
        v-if="isOpen"
        class="absolute z-50 mt-2 w-full min-w-[320px] bg-white rounded-xl border-2 border-gray-200 shadow-2xl overflow-hidden"
        style="animation: fadeIn 0.2s ease-out"
      >
        <!-- Quick Select Pills -->
        <div class="p-3 bg-gradient-to-br from-orange-50 to-white border-b border-gray-100">
          <div class="flex flex-wrap gap-2">
            <button
              v-for="option in quickOptions"
              :key="option.label"
              @click="handleQuickSelect(option)"
              type="button"
              class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-full hover:bg-[#ff5100] hover:text-white hover:border-[#ff5100] transition-all duration-200 shadow-sm"
            >
              {{ option.label }}
            </button>
          </div>
        </div>

        <!-- Calendar Header -->
        <div class="flex items-center justify-between px-4 py-3 bg-white border-b border-gray-100">
          <button
            @click="prevMonth"
            type="button"
            class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="text-gray-600"
            >
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
          </button>

          <span class="text-sm font-semibold text-gray-900">
            {{ monthNames[currentMonth.getMonth()] }} {{ currentMonth.getFullYear() }}
          </span>

          <button
            @click="nextMonth"
            type="button"
            class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="text-gray-600"
            >
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </button>
        </div>

        <!-- Calendar Grid -->
        <div class="p-3">
          <!-- Day Names -->
          <div class="grid grid-cols-7 gap-1 mb-2">
            <div
              v-for="day in dayNames"
              :key="day"
              class="h-8 flex items-center justify-center text-xs font-medium text-gray-500"
            >
              {{ day }}
            </div>
          </div>

          <!-- Date Cells -->
          <div class="grid grid-cols-7 gap-1">
            <button
              v-for="(day, i) in calendarDays"
              :key="i"
              @click="handleDateClick(day)"
              :disabled="!day"
              type="button"
              :class="[
                'h-9 rounded-lg text-sm font-medium transition-all duration-150',
                !day && 'invisible',
                day && !isSelectedDate(day) && !isToday(day) && 'text-gray-700 hover:bg-gray-100',
                isToday(day) && !isSelectedDate(day) && 'text-[#ff5100] font-semibold border border-[#ff5100]',
                isSelectedDate(day) && 'bg-[#ff5100] text-white shadow-md shadow-orange-200 scale-105'
              ]"
            >
              {{ day }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Helper Text -->
    <p v-if="helperText && !errorMessage" class="mt-1.5 text-xs text-gray-500">
      {{ helperText }}
    </p>

    <!-- Error Message -->
    <p v-if="errorMessage" class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
      <span class="font-medium">⚠</span> {{ errorMessage }}
    </p>

    <!-- Hidden Input for Form Submission -->
    <input type="hidden" :name="name" :value="model || ''" />
  </div>
</template>

<style scoped>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>