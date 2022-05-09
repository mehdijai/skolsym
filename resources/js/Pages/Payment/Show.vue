<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm } from "@inertiajs/inertia-vue3";
import TagPill from "@/Jetstream/TagPill.vue";
import Breadcrumbs from "@/Jetstream/Breadcrumbs.vue";
import FilterSystem from "@/Jetstream/FilterSystem.vue";
import GroupTable from "@/DataComponents/GroupTable.vue";
import { ref } from "@vue/reactivity";
import { computed } from "@vue/runtime-core";

const props = defineProps({
  groups: Array,
  states: Object,
});
</script>

<template>
  <AppLayout title="Groups">
    <template #header>
      <Breadcrumbs>
        <template #items>
          <li><Link :href="route('dashboard')">Dashboard</Link></li>
          <li>Groups</li>
        </template>
      </Breadcrumbs>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <GroupTable v-if="groups.length > 0" :groups="groups">
          <template #header>
            <div
              class="py-8 flex gap-x-4 flex-row mb-1 sm:mb-0 justify-end w-full"
            >
              <Link
                class="
                  text-sky-800
                  hover:text-sky-600
                  font-bold
                  text-2xl
                  leading-tight
                  mr-auto
                "
                :href="route('groups.create')"
                >Create new group</Link
              >
              <FilterSystem model="groups" :states="states" />
            </div>
          </template>
        </GroupTable>
        <div v-else class="w-full min-h-2xl">
          <div
            class="relative p-8 text-center border border-gray-200 rounded-lg bg-white shadow"
          >
            <h2 class="text-2xl font-bold">The list still empty!</h2>

            <p class="mt-4 text-sm text-gray-500">Add a new group.</p>

            <Link
              :href="route('groups.create')"
              class="
                inline-flex
                items-center
                px-5
                py-3
                mt-8
                font-medium
                text-white
                bg-cyan-700
                rounded-lg
                hover:bg-cyan-600
              "
            >
              Create a group
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
