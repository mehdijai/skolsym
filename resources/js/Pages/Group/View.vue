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
  group: Object,
  states: Object,
});
</script>

<template>
  <AppLayout :title="'Group ' + group.title">
    <template #header>
      <Breadcrumbs>
        <template #items>
          <li><Link :href="route('dashboard')">Dashboard</Link></li>
          <li><Link :href="route('groups.index')">Groups</Link></li>
          <li class="flex items-center">
            <span>
              {{ group.title }}
            </span>
            <Link
              class="ml-2"
              :href="
                route('groups.index', {
                  filter:
                    group.state === 'removed'
                      ? group.state
                      : group.archived
                      ? 'archived'
                      : group.state,
                })
              "
            >
              <TagPill
                :value="
                  group.state === 'removed'
                    ? group.state
                    : group.archived
                    ? 'archived'
                    : group.state
                "
              />
            </Link>
          </li>
        </template>
      </Breadcrumbs>
    </template>

    <div class="py-4">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <StudentTable :group="group" :students="group.students">
          <template #header>
            <div
              class="
                py-8
                flex
                gap-x-4
                flex-row
                mb-1
                sm:mb-0
                items-center
                justify-end
                w-full
              "
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
                :href="route('students.create', { group: group.id })"
                >Create new student</Link
              >
              <FilterSystem :states="states" />
            </div>
          </template>
        </StudentTable>
      </div>
    </div>
  </AppLayout>
</template>
