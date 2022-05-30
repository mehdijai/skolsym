<script setup>
import JetApplicationMark from "@/Jetstream/ApplicationMark.vue";
import { Inertia } from "@inertiajs/inertia";
import { Link, usePage } from "@inertiajs/inertia-vue3";
import { ref } from "@vue/reactivity";
import DropdownLink from "../../../vendor/laravel/jetstream/stubs/inertia/resources/js/Jetstream/DropdownLink.vue";
import Dropdown from "../../../vendor/laravel/jetstream/stubs/inertia/resources/js/Jetstream/Dropdown.vue";

let navRoutes = [
  {
    name: "Dashboard",
    route: "dashboard",
    icon: "space_dashboard",
  },
  {
    name: "Teachers",
    route: "teachers.index",
    icon: "account_box",
  },
  {
    name: "Courses",
    route: "courses.index",
    icon: "assignment",
  },
  {
    name: "Groups",
    route: "groups.index",
    icon: "workspaces",
  },
  {
    name: "Students",
    route: "students.index",
    icon: "groups",
  },
  {
    name: "Payments",
    route: "payments.index",
    icon: "monetization_on",
  },
  {
    name: "Accounting",
    route: "accounting.index",
    icon: "account_balance_wallet",
  },
];

if (usePage().props.value.isAdmin == true) {
  navRoutes.push({
    name: "Users",
    route: "users.index",
    icon: "admin_panel_settings",
  });
}

const collapsed = ref(false);

const toggleCollapse = () => {
  collapsed.value = !collapsed.value;
};

const logout = () => {
  Inertia.post(route("logout"));
};
</script>
<template>
  <div class="bg-white h-full w-full flex flex-col">
    <div class="flex items-center justify-between mx-4 my-4">
      <!-- Logo -->
      <div v-if="!collapsed" class="shrink-0 flex items-center">
        <Link :href="route('dashboard')">
          <JetApplicationMark
            class="
              hover:text-teal-500
              transition-colors
              duration-300
              ease-in-out
            "
          />
        </Link>
      </div>
      <span
        @click="toggleCollapse"
        :class="{ 'mx-auto': collapsed }"
        class="
          material-icons
          text-gray-800
          bg-gray-200
          rounded-full
          p-3
          w-3
          h-3
          text-xs
          cursor-pointer
          hover:bg-gray-100
          transition
          duration-300
          ease-in-out
        "
        >{{ collapsed ? "navigate_next" : "navigate_before" }}</span
      >
    </div>
    <nav class="h-full flex flex-col gap-3 my-5 px-4">
      <template v-for="(link, index) in navRoutes" :key="index">
        <Link
          :href="route(link.route)"
          :class="{
            'border-r-2 border-gray-600 dark:border-gray-300 text-gray-800 dark:text-gray-100  bg-gray-100 dark:bg-gray-600':
              route().current(link.route.split('.')[0] + '.*'),
          }"
          class="
            hover:text-gray-800 hover:bg-gray-100
            flex
            items-center
            p-2
            transition-colors
            dark:hover:text-white dark:hover:bg-gray-600
            duration-200
            text-gray-500
            dark:text-gray-400
          "
        >
          <span class="material-icons text-sm">{{ link.icon }}</span>
          <span v-if="!collapsed" class="mx-4 font-medium">
            {{ link.name }}
          </span>
          <span class="flex-grow text-right"> </span>
        </Link>
      </template>
      <hr class="mt-auto" />
      <Link
        :href="route('profile.show')"
        :class="{
          'border-r-2 border-gray-600 dark:border-gray-300 text-gray-800 dark:text-gray-100  bg-gray-100 dark:bg-gray-600':
            route().current('profile.show'),
        }"
        class="
          hover:text-gray-800 hover:bg-gray-100
          flex
          items-center
          p-2
          transition-colors
          dark:hover:text-white dark:hover:bg-gray-600
          duration-200
          text-gray-500
          dark:text-gray-400
        "
      >
        <span class="material-icons text-sm">manage_accounts</span>
        <span v-if="!collapsed" class="mx-4 font-medium">Profile</span>
        <span class="flex-grow text-right"> </span>
      </Link>

      <button
        @click="logout"
        class="
          hover:text-gray-800 hover:bg-gray-100
          flex
          items-center
          p-2
          transition-colors
          dark:hover:text-white dark:hover:bg-gray-600
          duration-200
          text-gray-500
          dark:text-gray-400
        "
      >
        <span class="material-icons text-sm">exit_to_app</span>
        <span v-if="!collapsed" class="mx-4 font-medium">Log out</span>
        <span class="flex-grow text-right"> </span>
      </button>
    </nav>
  </div>
</template>