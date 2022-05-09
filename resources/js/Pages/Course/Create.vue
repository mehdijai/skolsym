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
  payment_types: Object,
  teachers: Object,
  errors: Object,
  withTeacher: [null, String],
  course: {
    type: Object,
    default: null,
  },
});

const edit = computed(() => {
  return props.course !== null;
});

const form = useForm({
  id: edit.value ? props.course.id : null,
  title: edit.value ? props.course.title : "",
  period: edit.value ? String(props.course.period) : "",
  price: edit.value ? String(props.course.price) : "",
  teacher_percentage: edit.value ? String(props.course.teacher_percentage) : "",
  payment_type: edit.value ? props.course.payment_type : "monthly",
  teacher_id: edit.value ? String(props.course.teacher_id) : props.withTeacher,
  state: edit.value
    ? props.course.state
    : props.states[Object.keys(props.states)[0]],
  archived: edit.value ? Boolean(props.course.archived) : false,
});

const submit = () => {
  if (edit.value === true) {
    form.post(route("courses.edit"));
  } else {
    form.post(route("courses.store"));
  }
};
</script>

<template>
  <AppLayout :title="edit ? 'Update course' + course.title : 'Create course'">
    <template #header>
      <Breadcrumbs>
        <template #items>
          <li><Link :href="route('dashboard')">Dashboard</Link></li>
          <li><Link :href="route('courses.index')">Courses</Link></li>
          <li>{{ edit ? "Update " + course.title : "Create" }}</li>
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
            edit ? "Update " + course.title + "'s data" : "Create a new course"
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
              <JetLabel for="teacher_id" value="Teacher" />
              <JetSelectInput
                id="teacher_id"
                v-model="form.teacher_id"
                class="mt-1 block w-full"
                required
              >
                <template #options>
                  <template
                    v-for="(teacher, index) in teachers"
                    :key="index"
                  >
                    <option class="capitalize" :value="teacher.id">
                      {{ teacher.name }}
                    </option>
                  </template>
                </template>
              </JetSelectInput>
              <JetInputError
                v-if="form.errors.teacher_id"
                :message="form.errors.teacher_id"
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
              <JetLabel for="period" value="Period in weeks" />
              <JetInput
                id="period"
                v-model="form.period"
                type="number"
                class="mt-1 block w-full"
                required
                autocomplete="period"
              />
              <JetInputError
                v-if="form.errors.period"
                :message="form.errors.period"
              />
            </div>

            <div>
              <JetLabel for="price" value="Price" />
              <JetInput
                id="price"
                v-model="form.price"
                type="number"
                step="0.01"
                class="mt-1 block w-full"
                required
                autocomplete="price"
              />
              <JetInputError
                v-if="form.errors.price"
                :message="form.errors.price"
              />
            </div>

            <div>
              <JetLabel for="percentage" value="Teacher percentage" />
              <JetInput
                id="percentage"
                v-model="form.teacher_percentage"
                type="number"
                step="0.01"
                class="mt-1 block w-full"
                required
                autocomplete="percentage"
                placeholder="0.5"
                max="1"
                min="0"
              />
              <JetInputError
                v-if="form.errors.teacher_percentage"
                :message="form.errors.teacher_percentage"
              />
            </div>

            <div>
              <JetLabel for="payment_type" value="Payment type" />
              <JetSelectInput
                id="payment_type"
                v-model="form.payment_type"
                class="mt-1 block w-full"
                required
              >
                <template #options>
                  <template
                    v-for="(payment_type, index) in payment_types"
                    :key="index"
                  >
                    <option class="capitalize" :value="payment_type">
                      {{ payment_type }}
                    </option>
                  </template>
                </template>
              </JetSelectInput>
              <JetInputError
                v-if="form.errors.payment_type"
                :message="form.errors.payment_type"
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
              @click="$inertia.get(route('courses.index'))"
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
