<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";
import Breadcrumbs from "@/Jetstream/Breadcrumbs.vue";
import Calendar from "@/Jetstream/Calendar.vue";
import TodoList from "@/Jetstream/TodoList.vue";
import ProfileCard from "@/Jetstream/ProfileCard.vue";

defineProps({
  teacher: Object,
});
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

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- States and recap -->
        <div class="stats stats-vertical lg:stats-horizontal shadow">
          <div class="stat">
            <div class="stat-title">Courses</div>
            <div class="stat-value">31K</div>
            <div class="stat-desc">Jan 1st - Feb 1st</div>
          </div>

          <div class="stat">
            <div class="stat-title">Students</div>
            <div class="stat-value">4,200</div>
            <div class="stat-desc">↗︎ 400 (22%)</div>
          </div>

          <div class="stat">
            <div class="stat-title">Total revenue</div>
            <div class="stat-value">1,200</div>
            <div class="stat-desc">↘︎ 90 (14%)</div>
          </div>
        </div>

        <Calendar />

        <TodoList />

        <ProfileCard />
      </div>
    </div>
  </AppLayout>
</template>
