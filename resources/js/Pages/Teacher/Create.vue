<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm, Link } from "@inertiajs/inertia-vue3";
import JetButton from "@/Jetstream/Button.vue";
import JetButtonSecondary from "@/Jetstream/SecondaryButton.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetSelectInput from "@/Jetstream/SelectInput.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import Breadcrumbs from "@/Jetstream/Breadcrumbs.vue";
import Checkbox from "@/Jetstream/Checkbox.vue";
import { computed } from "@vue/runtime-core";

const props = defineProps({
  states: Object,
  teacher: {
    type: Object,
    default: null,
  },
});

const edit = computed(() => {
  return props.teacher !== null;
});

const form = useForm({
  id: edit.value ? props.teacher.id : null,
  name: edit.value ? props.teacher.name : "",
  email: edit.value ? props.teacher.email : "",
  phone: edit.value ? props.teacher.phone : "",
  state: edit.value
    ? props.teacher.state
    : props.states[Object.keys(props.states)[0]],
  archived: edit.value ? Boolean(props.teacher.archived) : false,
});

const submit = () => {
  if (edit.value === true) {
    form.post(route("teachers.edit"));
  } else {
    form.post(route("teachers.store"));
  }
};
</script>

<template>
  <AppLayout :title="edit ? 'Update teacher' + teacher.name : 'Create teacher'">
    <template #header>
      <Breadcrumbs>
        <template #items>
          <li><Link :href="route('dashboard')">Dashboard</Link></li>
          <li><Link :href="route('teachers.index')">Teachers</Link></li>
          <li>{{ edit ? "Update" : "Create" }}</li>
        </template>
      </Breadcrumbs>
    </template>
    <div
      class="
        flex flex-col
        sm:justify-center
        items-center
        pt-6
        sm:pt-0
        bg-gray-100
      "
    >
      <div class="py-4 mt-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{
            edit ? "Update " + teacher.name + "'s data" : "Create a new teacher"
          }}
        </h2>
      </div>
      <div
        class="
          w-full
          sm:max-w-3xl
          px-6
          py-4
          bg-white
          shadow-md
          overflow-hidden
          sm:rounded-lg
        "
      >
        <form @submit.prevent="submit">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <JetLabel for="name" value="Name" />
              <JetInput
                id="name"
                v-model="form.name"
                type="text"
                class="mt-1 block w-full"
                required
                autofocus
                autocomplete="name"
              />
              <JetInputError
                v-if="form.errors.name"
                :message="form.errors.name"
              />
            </div>

            <div>
              <JetLabel for="email" value="Email" />
              <JetInput
                id="email"
                v-model="form.email"
                type="email"
                class="mt-1 block w-full"
                required
              />
              <JetInputError
                v-if="form.errors.email"
                :message="form.errors.email"
              />
            </div>

            <div>
              <JetLabel for="phone" value="Phone" />
              <JetInput
                id="phone"
                v-model="form.phone"
                type="tel"
                class="mt-1 block w-full"
                required
                autocomplete="phone"
              />
              <JetInputError
                v-if="form.errors.phone"
                :message="form.errors.phone"
              />
            </div>

            <div>
              <JetLabel for="state" value="State" />
              <JetSelectInput
                id="state"
                v-model="form.state"
                class="mt-1 block w-full"
                required
              >
                <template #options>
                  <template v-for="(state, index) in states" :key="index">
                    <option class="capitalize" :value="state">
                      {{ state }}
                    </option>
                  </template>
                </template>
              </JetSelectInput>
              <JetInputError
                v-if="form.errors.state"
                :message="form.errors.state"
              />
            </div>
          </div>

          <div v-if="edit" class="flex items-center mt-4">
            <Checkbox
              id="archive"
              v-model="form.archived"
              :checked="form.archived"
              label="Archived"
            />
          </div>

          <div class="flex items-center justify-end mt-4">
            <JetButtonSecondary
              class="mr-3"
              :disabled="form.processing"
              @click="$inertia.get(route('teachers.index'))"
            >
              Cancel
            </JetButtonSecondary>
            <JetButton
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              {{ edit ? "Update" : "Create" }}
            </JetButton>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
