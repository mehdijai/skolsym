<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm } from "@inertiajs/inertia-vue3";
import Breadcrumbs from "@/Jetstream/Breadcrumbs.vue";
import Calendar from "@/Jetstream/Calendar.vue";
import TodoList from "@/Jetstream/TodoList.vue";
import ProfileCard from "@/Jetstream/ProfileCard.vue";
import RemoveCard from "@/Jetstream/RemoveCard.vue";
import JetSelectInput from "@/Jetstream/SelectInput.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import CourseTable from "@/DataComponents/CourseTable.vue";
import { ref } from "@vue/reactivity";

const props = defineProps({
  teacher: Object,
  teachers: Object,
});

const removeTeacher = ref(false);

const deleteTeacher = () => {
  removeTeacher.value = true;
};

const cancelDeletion = () => {
  removeTeacher.value = false;
  form.reset();
};

const form = useForm({
  id: String(props.teacher.id),
  assign_to: String(props.teachers[0].id) ?? null,
});

const confirmDeletion = () => {
  form.post(route("teachers.delete"), {
    onFinish: () => cancelDeletion(),
  });
};

const tab = ref("courses");
</script>

<template>
  <AppLayout :title="`Teachers | ${teacher.name}`">
    <template #header>
      <Breadcrumbs>
        <template #items>
          <li><Link :href="route('dashboard')">Dashboard</Link></li>
          <li><Link :href="route('teachers.index')">Teachers</Link></li>
          <li>{{ teacher.name }}</li>
        </template>
      </Breadcrumbs>
    </template>

    <RemoveCard
      v-if="removeTeacher"
      @confirm="confirmDeletion"
      @cancel="cancelDeletion"
    >
      <template #content>
        <p class="text-gray-800 dark:text-gray-200 text-xl font-bold mt-4">
          Remove {{ teacher.name }}
        </p>
        <p class="text-gray-600 dark:text-gray-400 text-xs py-2 px-6">
          Are you sure you want to delete this record ?
        </p>
        <div v-if="teacher.courses.length > 0" class="px-4 border-1 rounded">
          <JetLabel for="assign_to" value="Assign courses to a teacher" />
          <JetSelectInput
            id="assign_to"
            v-model="form.assign_to"
            class="mt-1 block w-full"
          >
            <template #options>
              <template
                v-for="(oteacher, index) in teachers"
                :key="'ts-' + index"
              >
                <option :value="oteacher.id">
                  {{ oteacher.name }} ({{ oteacher.courses_count }})
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

    <div class="grid grid-cols-12">
      <div class="max-w-sm col-span-2 flex flex-col items-center gap-y-4 p-4 min-h-screen h-full">
        <div class="pb-4 mt-4 border-b-2 border-b-gray-300">
          <h2 class="text-2xl break-all font-bold text-gray-600 capitalize">
            {{ teacher.name }}
          </h2>
          <a class="link mt-3 break-all" :href="'mailto:' + teacher.email">
            {{ teacher.email }}
          </a>
          <a class="link mt-1 break-all" :href="'tel:' + teacher.phone">
            {{ teacher.phone }}
          </a>
        </div>
        <div class="pb-4 border-b-2 border-b-gray-300">
          <div
            class="
              mx-auto
              my-4
              w-48
              shadow-lg
              rounded-2xl
              p-4
              bg-white
              dark:bg-gray-800
            "
          >
            <div class="flex items-center">
              <span class="rounded-xl relative p-4 bg-purple-200">
                <svg
                  width="40"
                  fill="currentColor"
                  height="40"
                  class="
                    text-purple-500
                    h-4
                    absolute
                    top-1/2
                    left-1/2
                    transform
                    -translate-x-1/2 -translate-y-1/2
                  "
                  viewBox="0 0 1792 1792"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M1362 1185q0 153-99.5 263.5t-258.5 136.5v175q0 14-9 23t-23 9h-135q-13 0-22.5-9.5t-9.5-22.5v-175q-66-9-127.5-31t-101.5-44.5-74-48-46.5-37.5-17.5-18q-17-21-2-41l103-135q7-10 23-12 15-2 24 9l2 2q113 99 243 125 37 8 74 8 81 0 142.5-43t61.5-122q0-28-15-53t-33.5-42-58.5-37.5-66-32-80-32.5q-39-16-61.5-25t-61.5-26.5-62.5-31-56.5-35.5-53.5-42.5-43.5-49-35.5-58-21-66.5-8.5-78q0-138 98-242t255-134v-180q0-13 9.5-22.5t22.5-9.5h135q14 0 23 9t9 23v176q57 6 110.5 23t87 33.5 63.5 37.5 39 29 15 14q17 18 5 38l-81 146q-8 15-23 16-14 3-27-7-3-3-14.5-12t-39-26.5-58.5-32-74.5-26-85.5-11.5q-95 0-155 43t-60 111q0 26 8.5 48t29.5 41.5 39.5 33 56 31 60.5 27 70 27.5q53 20 81 31.5t76 35 75.5 42.5 62 50 53 63.5 31.5 76.5 13 94z"
                  ></path>
                </svg>
              </span>
              <p class="text-md text-black dark:text-white ml-2">Total Revenue</p>
            </div>
            <div class="flex flex-col justify-start">
              <p
                class="
                  text-gray-700
                  dark:text-gray-100
                  text-4xl text-left
                  font-bold
                  my-4
                "
              >
                34,500
                <span class="text-sm"> $ </span>
              </p>
              <div class="flex items-center text-green-500 text-sm">
                <svg
                  width="20"
                  height="20"
                  fill="currentColor"
                  viewBox="0 0 1792 1792"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M1408 1216q0 26-19 45t-45 19h-896q-26 0-45-19t-19-45 19-45l448-448q19-19 45-19t45 19l448 448q19 19 19 45z"
                  ></path>
                </svg>
                <span> 5.5% </span>
                <span class="text-gray-400"> vs last month </span>
              </div>
            </div>
          </div>
          <div
            class="
              mx-auto
              my-4
              w-48
              shadow-lg
              rounded-2xl
              p-4
              bg-white
              dark:bg-gray-800
            "
          >
            <div class="flex items-center">
              <span class="rounded-xl relative p-4 bg-purple-200">
                <svg
                  width="40"
                  fill="currentColor"
                  height="40"
                  class="
                    text-purple-500
                    h-4
                    absolute
                    top-1/2
                    left-1/2
                    transform
                    -translate-x-1/2 -translate-y-1/2
                  "
                  viewBox="0 0 1792 1792"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M1362 1185q0 153-99.5 263.5t-258.5 136.5v175q0 14-9 23t-23 9h-135q-13 0-22.5-9.5t-9.5-22.5v-175q-66-9-127.5-31t-101.5-44.5-74-48-46.5-37.5-17.5-18q-17-21-2-41l103-135q7-10 23-12 15-2 24 9l2 2q113 99 243 125 37 8 74 8 81 0 142.5-43t61.5-122q0-28-15-53t-33.5-42-58.5-37.5-66-32-80-32.5q-39-16-61.5-25t-61.5-26.5-62.5-31-56.5-35.5-53.5-42.5-43.5-49-35.5-58-21-66.5-8.5-78q0-138 98-242t255-134v-180q0-13 9.5-22.5t22.5-9.5h135q14 0 23 9t9 23v176q57 6 110.5 23t87 33.5 63.5 37.5 39 29 15 14q17 18 5 38l-81 146q-8 15-23 16-14 3-27-7-3-3-14.5-12t-39-26.5-58.5-32-74.5-26-85.5-11.5q-95 0-155 43t-60 111q0 26 8.5 48t29.5 41.5 39.5 33 56 31 60.5 27 70 27.5q53 20 81 31.5t76 35 75.5 42.5 62 50 53 63.5 31.5 76.5 13 94z"
                  ></path>
                </svg>
              </span>
              <p class="text-md text-black dark:text-white ml-2">Total Students</p>
            </div>
            <div class="flex flex-col justify-start">
              <p
                class="
                  text-gray-700
                  dark:text-gray-100
                  text-4xl text-left
                  font-bold
                  my-4
                "
              >
                34,500
                <span class="text-sm"> $ </span>
              </p>
              <div class="flex items-center text-green-500 text-sm">
                <svg
                  width="20"
                  height="20"
                  fill="currentColor"
                  viewBox="0 0 1792 1792"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M1408 1216q0 26-19 45t-45 19h-896q-26 0-45-19t-19-45 19-45l448-448q19-19 45-19t45 19l448 448q19 19 19 45z"
                  ></path>
                </svg>
                <span> 5.5% </span>
                <span class="text-gray-400"> vs last month </span>
              </div>
            </div>
          </div>
        </div>
        <div
          class="
            pb-4
            px-5
            border-b-2 border-b-gray-300
            flex
            justify-between
            gap-x-2
          "
        >
          <Link :href="route('teachers.update', { id: teacher.id })">
            <span
              class="
                flex
                items-center
                p-2
                bg-gray-200
                text-red-700
                hover:bg-gray-50 hover:text-red-400
                rounded-full
                cursor-pointer
              "
            >
              <span class="material-icons text-xs">edit</span>
            </span>
          </Link>
          <p @click="deleteTeacher">
            <span
              class="
                flex
                items-center
                p-2
                bg-gray-200
                text-red-700
                hover:bg-gray-50 hover:text-red-400
                rounded-full
                cursor-pointer
              "
            >
              <span class="material-icons text-xs">delete</span>
            </span>
          </p>
          <Link :href="route('teachers.archive', teacher.id)">
            <span
              class="
                flex
                items-center
                p-2
                bg-gray-200
                text-red-700
                hover:bg-gray-50 hover:text-red-400
                rounded-full
                cursor-pointer
              "
            >
              <span class="material-icons text-xs">{{
                teacher.archived ? "unarchive" : "inventory_2"
              }}</span>
            </span>
          </Link>
        </div>
      </div>
      <div class="col-span-7 p-4">
        <div class="mx-auto max-w-5xl">
          <div class="tabs w-fit mx-auto">
            <a
              @click="tab = 'courses'"
              class="tab tab-bordered"
              :class="tab === 'courses' ? 'tab-active' : ''"
              >Courses</a
            >
            <a
              @click="tab = 'groups'"
              class="tab tab-bordered"
              :class="tab === 'groups' ? 'tab-active' : ''"
              >Groups</a
            >
            <a
              @click="tab = 'students'"
              class="tab tab-bordered"
              :class="tab === 'students' ? 'tab-active' : ''"
              >Students</a
            >
          </div>
          <div class="w-full p-4">
            <CourseTable
              style="max-width: unset; width: 100%; margin: 0; padding: 0"
              v-if="tab === 'courses'"
              :courses="teacher.courses"
              :profile="true"
            >
              <template #header>
                <Link
                  class="
                    text-sky-800
                    hover:text-sky-600
                    font-bold
                    text-xl
                    leading-tight
                    mr-auto
                    mb-4
                    block
                  "
                  :href="route('courses.create', { teacher: teacher.id })"
                  >Add new course</Link
                >
              </template>
            </CourseTable>
          </div>
        </div>
      </div>
      <div class="col-span-3 p-4 flex flex-col items-center gap-y-4">
        <Calendar />

        <TodoList />
      </div>
    </div>
  </AppLayout>
</template>
