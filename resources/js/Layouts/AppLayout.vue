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

    <div class="app-body">
      <aside class="side-navigation shadow-md">
        <SideBar />
      </aside>
      <section class="app-content bg-gray-100">
        <header v-if="$slots.header" class="app-header bg-white shadow">
          <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="py-2 px-4 sm:px-6 lg:px-8">
              <slot name="header" />
            </div>
            <div class="my-2 py-1 px-4 sm:px-6 lg:px-8">
              <Notification />
            </div>
          </div>
        </header>
        <main class="app-slot">
          <slot />
        </main>
      </section>
    </div>
  </div>
</template>

<style lang="scss">
.app-body {
  width: 100%;
  display: flex;
  align-items: stretch;
  height: 100vh;
  .side-navigation {
    width: fit;
    height: 100%;
    z-index: 10;
  }
  .app-content {
    height: 100%;
    display: flex;
    flex-direction: column;
    width: 100%;
    .app-header {
      z-index: 2;
    }
    .app-slot {
      overflow-y: scroll;
      height: 100%;
    }
  }
}
</style>