<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm } from "@inertiajs/inertia-vue3";
import TagPill from "@/Jetstream/TagPill.vue";
import Breadcrumbs from "@/Jetstream/Breadcrumbs.vue";
import RemoveCard from "@/Jetstream/RemoveCard.vue";
import JetSelectInput from "@/Jetstream/SelectInput.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import FilterSystem from "@/Jetstream/FilterSystem.vue";
import CourseTable from "@/DataComponents/CourseTable.vue";
import { ref } from "@vue/reactivity";
import { computed } from "@vue/runtime-core";

const props = defineProps({
  courses: Array,
  errors: Object,
  states: Object,
});

const removeCourse = ref(null);

const filteredCourses = (t) => {
  return props.courses.filter(
    (c) => c.id !== t.id && c.state == "active" && !t.archived
  );
};

const deleteCourse = (t) => {
  removeCourse.value = t;
  form.id = t.id;
  form.assign_to =
    t.groups_count > 0 && filteredCourses(t).length > 0
      ? String(filteredCourses(t)[0].id)
      : null;
};

const cancelDeletion = () => {
  removeCourse.value = null;
  form.reset();
};

const form = useForm({
  id: null,
  assign_to: null,
});

const confirmDeletion = () => {
  form.post(route("courses.delete"), {
    onSuccess: () => cancelDeletion(),
  });
};
</script>

<template>
  <AppLayout title="Courses">
    <template #header>
      <Breadcrumbs>
        <template #items>
          <li><Link :href="route('dashboard')">Dashboard</Link></li>
          <li>Courses</li>
        </template>
      </Breadcrumbs>
    </template>

    <RemoveCard
      v-if="removeCourse !== null"
      @confirm="confirmDeletion"
      @cancel="cancelDeletion"
    >
      <template #content>
        <p class="text-gray-800 dark:text-gray-200 text-xl font-bold mt-4">
          Remove {{ removeCourse.title }}
        </p>
        <p class="text-gray-600 dark:text-gray-400 text-xs py-2 px-6">
          Are you sure you want to delete this record ?
        </p>
        <div
          v-if="
            removeCourse.groups_count > 0 &&
            filteredCourses(removeCourse).length > 0
          "
          class="px-4 border-1 rounded"
        >
          <JetLabel for="assign_to" value="Assign groups to a course" />
          <JetSelectInput
            id="assign_to"
            v-model="form.assign_to"
            class="mt-1 block w-full"
          >
            <template #options>
              <template
                v-for="(course, index) in filteredCourses(removeCourse)"
                :key="'ts-' + index"
              >
                <option :value="course.id">
                  {{ course.title }} ({{ course.groups_count }})
                </option>
              </template>
            </template>
          </JetSelectInput>
          <JetInputError
            v-if="form.errors.assign_to"
            :message="form.errors.assign_to"
          />
        </div>
      </template>
    </RemoveCard>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <CourseTable :courses="courses">
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
                :href="route('courses.create')"
                >Create new course</Link
              >
              <FilterSystem model="courses" :states="states" />
            </div>
          </template>
        </CourseTable>
      </div>
    </div>
  </AppLayout>
</template>
