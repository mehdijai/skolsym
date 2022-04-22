<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Table from "@/Jetstream/Table.vue";

const teachers = [
  {
    name: "Mehdi Jai",
    email: "mehdi.jai.mj@gmail.com",
    phone: "0612113830",
    state: {
      content: "active",
      tagPill: true,
      color: "green",
    },
    actions: [
      {
        icon: true,
        content: "edit",
        title: "Edit teacher",
        route: "teachers.update",
        class: "text-indigo-600 hover:text-indigo-900",
      },
      {
        icon: true,
        content: "delete",
        title: "Edit Delete",
        route: "teachers.delete",
        class: "text-red-600 mx-2 hover:text-red-900",
      },
      {
        icon: true,
        content: "inventory_2",
        title: "Archive teacher",
        route: "teachers.archive",
        class: "text-orange-600 mx-2 hover:text-orange-900",
      },
      {
        icon: true,
        content: "add_circle",
        title: "Create a new course",
        route: "teachers.courses.new",
        class: "text-lime-600 mx-2 hover:text-lime-900",
      },
      {
        icon: true,
        content: "list",
        title: "Display teacher's courses",
        route: "teachers.courses.index",
        class: "text-teal-600 mx-2 hover:text-teal-900",
      },
      {
        icon: true,
        content: "info",
        title: "Display teacher's details",
        class: "text-blue-600 mx-2 hover:text-blue-900",
      },
    ],
  },
];
</script>

<template>
  <AppLayout title="Teachers">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Teachers
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <Table :data="teachers" title="Teachers" />
      </div>
    </div>
  </AppLayout>
</template>
