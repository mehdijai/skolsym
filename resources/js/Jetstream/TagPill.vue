<script setup>
defineProps({
  value: String,
});
</script>

<template>
  <span class="tag-pill" :class="value">
    <span aria-hidden="true" class="tag-pill__bg"> </span>
    <span class="tag-pill__content"> {{ value }} </span>
  </span>
</template>