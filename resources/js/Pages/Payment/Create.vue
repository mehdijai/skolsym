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
import { computed, reactive, ref } from "@vue/runtime-core";

const props = defineProps({
  states: Object,
  students: Object,
  errors: Object,
  payment: {
    type: Object,
    default: null,
  },
});

const edit = computed(() => {
  return props.payment !== null;
});

const form = useForm({
  id: edit.value ? props.payment.id : null,
  course_id: edit.value ? String(props.payment.course_id) : null,
  student_id: edit.value ? String(props.payment.student_id) : String(0),
  courses: [],
  state: edit.value
    ? props.payment.state
    : props.states[Object.keys(props.states)[0]],
  archived: edit.value ? Boolean(props.payment.archived) : false,
});

const selectedCourse = ref(edit.value ? props.payment.course_id : 0);

const setCourse = (id) => {
  selectedCourse.value = id
}

const AttachedCourses = computed(() =>
  props.students.find((s) => s.id == form.student_id) != undefined
    ? props.students
        .find((s) => s.id == form.student_id)
        .courses.map((c) => {
          c.checked = edit.value ? c.id === props.payment.course_id : false;
          return c;
        })
    : []
);

const submit = () => {
  if (edit.value === true) {
    form.course_id = selectedCourse.value
    form.post(route("payments.edit"));
  } else {
    form.courses = AttachedCourses.value.filter((s) => s.checked === true);
    form.post(route("payments.store"));
  }
};
</script>

<template>
  <AppLayout :title="edit ? 'Update payment ' + payment.ref : 'Create payment'">
    <template #header>
      <Breadcrumbs>
        <template #items>
          <li><Link :href="route('dashboard')">Dashboard</Link></li>
          <li><Link :href="route('payments.index')">Payments</Link></li>
          <li>{{ edit ? "Update " + payment.ref : "Create" }}</li>
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
            edit ? "Update " + payment.ref + "'s data" : "Create a new payment"
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
                <JetLabel for="student_id" value="Student" />
                <JetSelectInput
                  id="student_id"
                  v-model="form.student_id"
                  class="mt-1 block w-full"
                  required
                >
                  <template #options>
                    <option :value="0">None</option>
                    <template v-for="(student, index) in students" :key="index">
                      <option class="capitalize" :value="student.id">
                        {{ student.name }} ({{ student.age }} Y.O)
                      </option>
                    </template>
                  </template>
                </JetSelectInput>
                <JetInputError
                  v-if="form.errors.student_id"
                  :message="form.errors.student_id"
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
                @click="$inertia.get(route('payments.index'))"
              >
                Cancel
              </JetButtonSecondary>
              <JetButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="
                  form.processing ||
                  form.student_id == 0
                "
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
          <DataSelector @checked="setCourse" :onlyOne="edit ? selectedCourse : null" :courses="AttachedCourses" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
