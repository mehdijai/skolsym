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
import GroupTable from "@/DataComponents/GroupTable.vue";
import StudentTable from "@/DataComponents/StudentTable.vue";
import TagPill from "@/Jetstream/TagPill.vue";
import { ref } from "@vue/reactivity";
import { computed } from "@vue/runtime-core";

const props = defineProps({
  teacher: Object,
  teachers: Object,
  groups: Object,
  students: Object,
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
  assign_to: props.teachers.length > 0 ? String(props.teachers[0].id) : null,
});

const confirmDeletion = () => {
  form.post(route("teachers.delete"), {
    onFinish: () => cancelDeletion(),
  });
};

const tab = computed(() => {
  return window.location.hash != ""
    ? window.location.hash.replace("#", "")
    : "courses";
});

function diffPercentage(prev, current) {
  if (prev == 0) {
    return 100;
  }
  return Math.floor(((current - prev) / prev) * 100);
}
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
      <div
        class="
          max-w-sm
          col-span-2
          flex flex-col
          items-center
          gap-y-4
          p-4
          min-h-screen
          h-full
        "
      >
        <div class="pb-4 mt-4 border-b-2 border-b-gray-300">
          <h2 class="text-2xl break-all font-bold text-gray-600 capitalize">
            {{ teacher.name }}
          </h2>
          <div class="mt-2">
            <Link
              :href="
                route('teachers.index', {
                  filter:
                    teacher.state === 'removed'
                      ? teacher.state
                      : teacher.archived
                      ? 'archived'
                      : teacher.state,
                })
              "
            >
              <TagPill
                :value="
                  teacher.state === 'removed'
                    ? teacher.state
                    : teacher.archived
                    ? 'archived'
                    : teacher.state
                "
              />
            </Link>
          </div>
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
              <span
                class="
                  material-icons
                  bg-orange-200
                  text-orange-600
                  p-2
                  rounded-lg
                  text-xs
                "
                >attach_money</span
              >
              <p class="text-md text-black dark:text-white ml-2">
                Total Revenue
              </p>
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
                {{ teacher.month_revenue }}
                <span class="text-sm"> DH </span>
              </p>
              <div
                class="flex items-center text-sm"
                :class="
                  diffPercentage(teacher.last_month_revenue, teacher.month_revenue) >=
                  0
                    ? 'text-green-600'
                    : 'text-red-600'
                "
              >
                <span class="material-icons">{{
                  diffPercentage(teacher.last_month_revenue, teacher.month_revenue) >=
                  0
                    ? "arrow_drop_up"
                    : "arrow_drop_down"
                }}</span>
                <span>
                  {{
                    Math.abs(
                      diffPercentage(
                        teacher.last_month_revenue,
                        teacher.month_revenue
                      )
                    )
                  }}%
                </span>
                <span class="ml-1 text-gray-400">vs last month </span>
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
              <span
                class="
                  material-icons
                  bg-purple-200
                  text-purple-600
                  p-2
                  rounded-lg
                  text-xs
                "
                >group</span
              >
              <p class="text-md text-black dark:text-white ml-2">
                Total Students
              </p>
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
                {{ students.length }}
              </p>
              <div class="flex items-center text-green-500 text-sm">
                <span class="material-icons text-green-600">arrow_drop_up</span>
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
      <div class="col-span-10 p-4">
        <div class="mx-auto">
          <div class="tabs w-fit mx-auto">
            <Link
              href="#courses"
              class="tab tab-bordered"
              :class="tab === 'courses' ? 'tab-active' : ''"
              >Courses</Link
            >
            <Link
              href="#groups"
              class="tab tab-bordered"
              :class="tab === 'groups' ? 'tab-active' : ''"
              >Groups</Link
            >
            <Link
              href="#students"
              class="tab tab-bordered"
              :class="tab === 'students' ? 'tab-active' : ''"
              >Students</Link
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
            <GroupTable
              style="max-width: unset; width: 100%; margin: 0; padding: 0"
              v-if="tab === 'groups'"
              :groups="groups"
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
                  :href="route('groups.create', { teacher: teacher.id })"
                  >Add new group</Link
                >
              </template>
            </GroupTable>
            <StudentTable
              style="max-width: unset; width: 100%; margin: 0; padding: 0"
              v-if="tab === 'students'"
              :students="students"
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
                  :href="route('students.create', { teacher: teacher.id })"
                  >Add new student</Link
                >
              </template>
            </StudentTable>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
