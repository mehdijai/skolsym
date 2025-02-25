<script setup>
import ModelViewer from "@/Layouts/ModelViewer.vue";
import { Link, useForm } from "@inertiajs/inertia-vue3";
import Breadcrumbs from "@/Jetstream/Breadcrumbs.vue";
import RemoveCard from "@/Jetstream/RemoveCard.vue";
import JetSelectInput from "@/Jetstream/SelectInput.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import GroupTable from "@/DataComponents/GroupTable.vue";
import PaymentTable from "@/DataComponents/PaymentTable.vue";
import TagPill from "@/Jetstream/TagPill.vue";
import { ref } from "@vue/reactivity";
import { computed } from "@vue/runtime-core";

const props = defineProps({
  student: Object,
});

const removeStudent = ref(null);

const form = useForm({
  id: null,
});

const deleteStudent = () => {
  removeStudent.value = props.student;
  form.id = props.student.id;
};

const cancelDeletion = () => {
  removeStudent.value = null;
  form.reset();
};

const confirmDeletion = () => {
  form.post(route("students.delete"), {
    onSuccess: () => cancelDeletion(),
  });
};

const tab = computed(() => {
  return window.location.hash != ""
    ? window.location.hash.replace("#", "")
    : "payments";
});

function diffPercentage(prev, current) {
  if (prev == 0) {
    return 100;
  }
  return Math.floor(((current - prev) / prev) * 100);
}
</script>

<template>
  <RemoveCard
    v-if="removeStudent"
    @confirm="confirmDeletion"
    @cancel="cancelDeletion"
  >
    <template #content>
      <p class="text-gray-800 dark:text-gray-200 text-xl font-bold mt-4">
        Remove {{ removeStudent.title }}
      </p>
      <p class="text-gray-600 dark:text-gray-400 text-xs py-2 px-6">
        Are you sure you want to delete this record ?
      </p>
    </template>
  </RemoveCard>

  <ModelViewer :title="`Students | ${student.name}`">
    <template #header>
      <Breadcrumbs>
        <template #items>
          <li><Link :href="route('dashboard')">Dashboard</Link></li>
          <li><Link :href="route('students.index')">Students</Link></li>
          <li>{{ student.name }}</li>
        </template>
      </Breadcrumbs>
    </template>
    <template #profileInfo>
      <h2 class="text-2xl break-all font-bold text-gray-600 capitalize">
        {{ student.name }}
      </h2>
      <div class="mt-2">
        <Link
          :href="
            route('students.index', {
              filter:
                student.state === 'removed'
                  ? student.state
                  : student.archived
                  ? 'archived'
                  : student.state,
            })
          "
        >
          <TagPill
            :value="
              student.state === 'removed'
                ? student.state
                : student.archived
                ? 'archived'
                : student.state
            "
          />
        </Link>
      </div>
      <a class="link mt-3 break-all" :href="'mailto:' + student.email">
        {{ student.email }}
      </a>
      <a class="link mt-1 break-all" :href="'tel:' + student.phone">
        {{ student.phone }}
      </a>
    </template>
    <template #profileMetric1>
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
        <p class="text-md text-black dark:text-white ml-2">Total paid</p>
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
          {{ student.month_paid }}
          <span class="text-sm"> DH </span>
        </p>
        <div
          class="flex items-center text-sm"
          :class="
            diffPercentage(student.last_month_paid, student.month_paid) >= 0
              ? 'text-green-600'
              : 'text-red-600'
          "
        >
          <span class="material-icons">{{
            diffPercentage(student.last_month_paid, student.month_paid) >= 0
              ? "arrow_drop_up"
              : "arrow_drop_down"
          }}</span>
          <span>
            {{
              Math.abs(
                diffPercentage(student.last_month_paid, student.month_paid)
              )
            }}%
          </span>
          <span class="ml-1 text-gray-400">vs last month </span>
        </div>
      </div>
    </template>
    <template #profileMetric2>
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
          >work</span
        >
        <p class="text-md text-black dark:text-white ml-2">Courses</p>
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
          {{ student.groups.map((g) => g.course.id).length }}
        </p>
      </div>
    </template>
    <template #profileCTAs>
      <Link :href="route('students.update', { id: student.id })">
        <span
          class="
            flex
            items-center
            p-2
            bg-gray-200
            hover:bg-gray-300
            rounded-md
            cursor-pointer
          "
        >
          <span class="material-icons text-xs">edit</span>
          <span class="font-semibold ml-2">Update</span>
        </span>
      </Link>
      <p @click="deleteStudent">
        <span
          class="
            flex
            items-center
            p-2
            bg-gray-200
            hover:bg-gray-300
            rounded-md
            cursor-pointer
          "
        >
          <span class="material-icons text-xs">delete</span>
          <span class="font-semibold ml-2">Delete</span>
        </span>
      </p>
      <Link :href="route('students.archive', student.id)">
        <span
          class="
            flex
            items-center
            p-2
            bg-gray-200
            hover:bg-gray-300
            rounded-md
            cursor-pointer
          "
        >
          <span class="material-icons text-xs">{{
            student.archived ? "unarchive" : "inventory_2"
          }}</span>
          <span class="font-semibold ml-2">{{
            student.archived ? "Unarchive" : "Archive"
          }}</span>
        </span>
      </Link>
    </template>
    <template #dataTables>
      <div class="tabs w-fit mx-auto">
        <Link
          href="#payments"
          class="tab tab-bordered"
          :class="tab === 'payments' ? 'tab-active' : ''"
          >Payments</Link
        >
        <Link
          href="#groups"
          class="tab tab-bordered"
          :class="tab === 'groups' ? 'tab-active' : ''"
          >Groups</Link
        >
      </div>
      <div class="w-full p-4">
        <GroupTable
          style="max-width: unset; width: 100%; margin: 0; padding: 0"
          v-if="tab === 'groups'"
          :groups="student.groups"
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
              :href="route('groups.create', { student: student.id })"
              >Add new group</Link
            >
          </template>
        </GroupTable>
        <PaymentTable
          style="max-width: unset; width: 100%; margin: 0; padding: 0"
          v-if="tab === 'payments'"
          :payments="student.payments"
        >
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
                :href="route('payments.create')"
                >Create new payment</Link
              >
            </div>
          </template>
        </PaymentTable>
      </div>
    </template>
  </ModelViewer>
</template>
