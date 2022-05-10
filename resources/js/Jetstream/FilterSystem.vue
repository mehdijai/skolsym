<script setup>
import JetSelectInput from "@/Jetstream/SelectInput.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetButton from "@/Jetstream/Button.vue";
import { ref } from "@vue/reactivity";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
  states: Object,
});

const filter = ref(route().params.filter ?? "");
const search = ref(route().params.search ?? "");

const filterState = () => {
  let params = {};
  if (filter.value !== "") {
    params.filter = filter.value;
  }
  if (search.value !== "") {
    params.search = search.value;
  }

  Inertia.get(window.location.origin + window.location.pathname, params);
};
</script>

<template>
  <div class="flex items-center">
    <JetSelectInput
      @change="filterState"
      id="filterSate"
      v-model="filter"
      class="block w-full"
      placeholder="Filter state"
    >
      <template #options>
        <template v-for="(state, index) in states" :key="'state-' + index">
          <option :value="state">{{ state || "All" }}</option>
        </template>
      </template>
    </JetSelectInput>
  </div>
  <form class="flex items-center" @submit.prevent="filterState">
    <JetInput
      v-model="search"
      class="mr-2 block w-full"
      type="search"
      placeholder="Search..."
    />
    <JetButton> Search </JetButton>
  </form>
</template>