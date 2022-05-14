<script setup>
import { onMounted } from "vue";
import { Head } from "@inertiajs/inertia-vue3";
import JetBanner from "@/Jetstream/Banner.vue";
import NavBar from "@/Jetstream/NavBar.vue";

defineProps({
  title: String,
});

onMounted(() => {
  const dropdownContent = document.querySelectorAll(".dropdown-content>li");
  dropdownContent.forEach((element) => {
    element.addEventListener("click", () => {
      document.activeElement.blur();
    });
  });
});
</script>

<template>
  <div>
    <Head :title="title" />

    <JetBanner />

    <div class="min-h-screen h-full bg-gray-100">
      <NavBar />

      <!-- Page Heading -->
      <header v-if="$slots.header" class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
          <slot name="header" />
        </div>
      </header>

      <!-- Page Content -->
      <main>
        <slot />
      </main>
    </div>
  </div>
</template>

