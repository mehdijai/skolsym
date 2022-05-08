<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm } from "@inertiajs/inertia-vue3";
import Breadcrumbs from "@/Jetstream/Breadcrumbs.vue";
import FilterSystem from "@/Jetstream/FilterSystem.vue";
import TeacherTable from "@/DataComponents/TeacherTable.vue";
import { ref } from "@vue/reactivity";

defineProps({
  teachers: Array,
  states: Object,
});
</script>

<template>
  <AppLayout title="Teachers">
    <template #header>
      <Breadcrumbs>
        <template #items>
          <li><Link :href="route('dashboard')">Dashboard</Link></li>
          <li>Teachers</li>
        </template>
      </Breadcrumbs>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <TeacherTable v-if="teachers.length > 0" :teachers="teachers">
          <template #header>
            <div
              class="py-8 flex flex-row gap-x-4 mb-1 sm:mb-0 justify-end w-full"
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
                :href="route('teachers.create')"
                >Add new teacher</Link
              >
              <FilterSystem model="teachers" :states="states" />
            </div>
          </template>
        </TeacherTable>
        <div v-else class="w-full min-h-2xl">
          <div
            class="relative p-8 text-center border border-gray-200 rounded-lg bg-white shadow"
          >
            <h2 class="text-2xl font-bold">The list still empty!</h2>

            <p class="mt-4 text-sm text-gray-500">Add a new teacher.</p>

            <Link
              :href="route('teachers.create')"
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
              Create a teacher
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
