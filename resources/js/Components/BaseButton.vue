<template>
  <button
    :class="[
      baseClasses,
      variantClasses,
      disabled ? 'opacity-50 cursor-not-allowed' : 'hover:opacity-90',
      customClass
    ]"
    :disabled="disabled"
    @click="$emit('click', $event)"
  >
    <slot />
  </button>
</template>

<script setup lang="ts">
import { computed } from 'vue';

defineProps<{|
  variant?: 'primary' | 'secondary' | 'danger',
  disabled?: boolean,
  customClass?: string,
  |}>();

defineEmits<['click', MouseEvent]>();

const props = defineProps();

const baseClasses = 'px-4 py-2 rounded-full font-medium focus:outline-none focus:ring-2 focus:ring-offset-2';

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'secondary':
      return 'bg-gray-200 text-gray-800 focus:ring-gray-400';
    case 'danger':
      return 'bg-red-600 text-white focus:ring-red-500';
    default:
      return 'bg-blue-600 text-white focus:ring-blue-500';
  }
});

const { disabled = false, customClass = '' } = props;
</script>

