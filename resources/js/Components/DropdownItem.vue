<script setup>
import { MenuItem } from '@headlessui/vue';
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    as: {
        type: String,
        default: "a"
    }
})
</script>
<template>
    <MenuItem v-slot="{ active }">
    <component v-if="props.as != 'a'" :is="props.as" :class="[
        active ? 'bg-primary text-white' : 'text-gray-900',
        'group flex w-full rounded-md px-2 py-2 text-sm text-left',
    ]">
        <slot />
    </component>
    <Link v-else :is="props.as" :class="[
        active ? 'bg-primary text-white' : 'text-gray-900',
        'group flex w-full rounded-md px-2 py-2 text-sm',
    ]">
    <slot />
    </Link>
    </MenuItem>
</template>