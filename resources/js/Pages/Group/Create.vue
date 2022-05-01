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
  courses: Object,
  withCourse: [null, String],
  errors: Object,
  group: {
    type: Object,
    default: null,
  },
});

const edit = computed(() => {
  return props.group !== null;
});

const form = useForm({
  id: edit.value ? props.group.id : null,
  title: edit.value ? props.group.title : "",
  course_id: edit.value ? String(props.group.course_id) : props.withCourse,
  state: edit.value
    ? props.group.state
    : props.states[Object.keys(props.states)[0]],
  archived: edit.value ? Boolean(props.group.archived) : false,
});

const submit = () => {
  if (edit.value === true) {
    form.post(route("groups.edit"));
  } else {
    form.post(route("groups.store"));
  }
};
</script>

<template>
  <AppLayout :title="edit ? 'Update group' + group.title : 'Create group'">
    <template #header>
      <Breadcrumbs>
        <template #items>
          <li><Link :href="route('dashboard')">Dashboard</Link></li>
          <li><Link :href="route('groups.index')">Groups</Link></li>
          <li>{{ edit ? "Update " + group.title : "Create" }}</li>
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
            edit ? "Update " + group.title + "'s data" : "Create a new group"
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
              <JetLabel for="course_id" value="Course" />
              <JetSelectInput
                id="course_id"
                v-model="form.course_id"
                class="mt-1 block w-full"
                required
              >
                <template #options>
                  <template v-for="(course, index) in courses" :key="index">
                    <option class="capitalize" :value="course.id">
                      {{ course.title }}
                    </option>
                  </template>
                </template>
              </JetSelectInput>
              <JetInputError
                v-if="form.errors.course_id"
                :message="form.errors.course_id"
              />
            </div>

            <div>
              <JetLabel for="title" value="Title" />
              <JetInput
                id="title"
                v-model="form.title"
                type="text"
                class="mt-1 block w-full"
                required
                autofocus
                autocomplete="title"
              />
              <JetInputError
                v-if="form.errors.title"
                :message="form.errors.title"
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
            />
            <label for="archive" class="label-text ml-2 font-bold"
              >Archived</label
            >
          </div>

          <div class="flex items-center justify-end mt-4">
            <JetButtonSecondary
              class="mr-3"
              :disabled="form.processing"
              @click="$inertia.get(route('groups.index'))"
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
