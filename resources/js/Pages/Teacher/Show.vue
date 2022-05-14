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

    <div class="py-4">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <TeacherTable :teachers="teachers">
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
              <FilterSystem :states="states" />
            </div>
          </template>
        </TeacherTable>
      </div>
    </div>
  </AppLayout>
</template>
