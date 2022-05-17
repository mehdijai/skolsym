<script setup>
import { onMounted } from "vue";
import { Head } from "@inertiajs/inertia-vue3";
import JetBanner from "@/Jetstream/Banner.vue";
import NavBar from "@/Jetstream/NavBar.vue";
import SideBar from "../Jetstream/SideBar.vue";
import Notification from "../Jetstream/Notification.vue";

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

    <div class="min-h-screen h-full bg-gray-100 flex flex-column">
      <SideBar />

      <div class="w-full">
        <!-- Page Heading -->
        <header v-if="$slots.header" class="bg-white shadow">
          <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="py-2 px-4 sm:px-6 lg:px-8">
              <slot name="header" />
            </div>
            <div class="my-2 py-1 px-4 sm:px-6 lg:px-8">
              <Notification />
            </div>
          </div>
        </header>

        <!-- Page Content -->
        <main>
          <slot />
        </main>
      </div>
    </div>
  </div>
</template>

