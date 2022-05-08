<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm } from "@inertiajs/inertia-vue3";
import TagPill from "@/Jetstream/TagPill.vue";
import Breadcrumbs from "@/Jetstream/Breadcrumbs.vue";
import FilterSystem from "@/Jetstream/FilterSystem.vue";
import StudentTable from "@/DataComponents/StudentTable.vue";
import { ref } from "@vue/reactivity";
import { computed } from "@vue/runtime-core";

const props = defineProps({
  students: Array,
  states: Object,
});
</script>

<template>
  <AppLayout title="Students">
    <template #header>
      <Breadcrumbs>
        <template #items>
          <li><Link :href="route('dashboard')">Dashboard</Link></li>
          <li>Students</li>
        </template>
      </Breadcrumbs>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <StudentTable v-if="students.length > 0" :students="students">
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
                :href="route('students.create')"
                >Create new student</Link
              >
              <FilterSystem model="students" :states="states" />
            </div>
          </template>
        </StudentTable>
        <div v-else class="w-full min-h-2xl">
          <div
            class="relative p-8 text-center border border-gray-200 rounded-lg bg-white shadow"
          >
            <h2 class="text-2xl font-bold">The list still empty!</h2>

            <p class="mt-4 text-sm text-gray-500">Add a new student.</p>

            <Link
              :href="route('students.create')"
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
              Create a student
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
