<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm } from "@inertiajs/inertia-vue3";
import TagPill from "@/Jetstream/TagPill.vue";
import Breadcrumbs from "@/Jetstream/Breadcrumbs.vue";
import FilterSystem from "@/Jetstream/FilterSystem.vue";
import PaymentTable from "@/DataComponents/PaymentTable.vue";
import { ref } from "@vue/reactivity";
import { computed } from "@vue/runtime-core";

const props = defineProps({
  payments: Object,
  states: Object,
});
</script>

<template>
  <AppLayout title="Payments">
    <template #header>
      <Breadcrumbs>
        <template #items>
          <li><Link :href="route('dashboard')">Dashboard</Link></li>
          <li>Payments</li>
        </template>
      </Breadcrumbs>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <PaymentTable :payments="payments">
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
              <FilterSystem model="payments" :states="states" />
            </div>
          </template>
        </PaymentTable>
      </div>
    </div>
  </AppLayout>
</template>
