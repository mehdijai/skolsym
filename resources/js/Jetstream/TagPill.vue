<script setup>
defineProps({
  content: String,
  color: String,
});
</script>

<template>
  <span
    class="relative inline-block px-3 py-1 font-semibold leading-tight"
    :class="`text-${color}-900`"
  >
    <span
      aria-hidden="true"
      class="absolute inset-0 opacity-50 rounded-full"
      :class="`bg-${color}-200`"
    >
    </span>
    <span class="relative"> {{ content }} </span>
  </span>
</template>