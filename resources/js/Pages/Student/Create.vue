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
import DataSelector from "@/Jetstream/DataSelector.vue";
import { computed, reactive, watch } from "@vue/runtime-core";

const props = defineProps({
  states: Object,
  courses: Object,
  withGroup: [null, String],
  errors: Object,
  student: {
    type: Object,
    default: null,
  },
});

const edit = computed(() => {
  return !!props.student;
});

const AttachedCourses = reactive(
  props.courses.map((c) => {
    c.checked =
      !!props.withGroup
        ? c.groups.find((g) => g.id === Number(props.withGroup)) !== undefined
        : edit.value
        ? props.student.groups.map((gs) => gs.course_id).includes(c.id)
        : false;

    c.groups.map((g) => {
      g.checked =
        !!props.withGroup
          ? g.id === Number(props.withGroup)
          : edit.value
          ? props.student.groups.map((gs) => gs.id).includes(g.id)
          : false;
    });

    return c;
  })
);

const form = useForm({
  id: edit.value ? props.student.id : null,
  name: edit.value ? props.student.name : "",
  email: edit.value ? props.student.email : "",
  phone: edit.value ? props.student.phone : "",
  age: edit.value ? String(props.student.age) : "",
  grade: edit.value ? props.student.grade : "",
  state: edit.value
    ? props.student.state
    : props.states[Object.keys(props.states)[0]],
  archived: edit.value ? Boolean(props.student.archived) : false,
  groups: [],
});

const submit = () => {
  form.groups = AttachedCourses.filter((c) => c.checked === true)
    .map((c) => c.groups.filter((gs) => gs.checked === true).map((gs) => gs.id))
    .reduce((p, c) => p.concat(c), []);

  if (edit.value === true) {
    form.post(route("students.edit"));
  } else {
    form.post(route("students.store"));
  }
};
</script>

<template>
  <AppLayout :title="edit ? 'Update student' + student.name : 'Create student'">
    <template #header>
      <Breadcrumbs>
        <template #items>
          <li><Link :href="route('dashboard')">Dashboard</Link></li>
          <li><Link :href="route('students.index')">Students</Link></li>
          <li>{{ edit ? "Update " + student.name : "Create" }}</li>
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
        <h2 class="font-semibold text-xl mb-4 text-gray-800 leading-tight">
          {{
            edit ? "Update " + student.name + "'s data" : "Create a new student"
          }}
        </h2>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 px-8 gap-8 items-start">
        <div
          class="
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
                  autocomplete="email"
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
                <JetLabel for="age" value="Age" />
                <JetInput
                  id="age"
                  v-model="form.age"
                  type="number"
                  class="mt-1 block w-full"
                  required
                  autocomplete="age"
                />
                <JetInputError
                  v-if="form.errors.age"
                  :message="form.errors.age"
                />
              </div>

              <div>
                <JetLabel for="grade" value="Grade" />
                <JetInput
                  id="grade"
                  v-model="form.grade"
                  type="text"
                  class="mt-1 block w-full"
                  required
                  autocomplete="grade"
                />
                <JetInputError
                  v-if="form.errors.grade"
                  :message="form.errors.grade"
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
        <div
          class="
            sm:max-w-3xl
            px-6
            py-4
            bg-white
            shadow-md
            overflow-hidden
            sm:rounded-lg
          "
        >
          <DataSelector :courses="AttachedCourses" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
