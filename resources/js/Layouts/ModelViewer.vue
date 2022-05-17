<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";

defineProps({
  title: String,
});
</script>

<template>
  <AppLayout :title="title">
    <template #header>
      <slot name="header" />
    </template>
    <div>
      <div
        class="grid grid-cols-4 items-stretch justify-around py-6 px-4 gap-x-4"
      >
        <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col gap-y-2">
          <slot name="profileInfo" />
        </div>
        <div class="shadow-lg rounded-2xl p-4 bg-white dark:bg-gray-800">
          <slot name="profileMetric1" />
        </div>
        <div class="shadow-lg rounded-2xl p-4 bg-white dark:bg-gray-800">
          <slot name="profileMetric2" />
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col gap-y-2">
          <slot name="profileCTAs" />
        </div>
      </div>
      <div class="p-4">
        <div class="mx-auto">
          <slot name="dataTables" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
