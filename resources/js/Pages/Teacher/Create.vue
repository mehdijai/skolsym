<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetSelectInput from "@/Jetstream/SelectInput.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetInputError from "@/Jetstream/InputError.vue";

const props = defineProps({
  states: Object,
});

const form = useForm({
  name: "",
  email: "",
  phone: "",
  state: props.states[Object.keys(props.states)[0]],
});

const submit = () => {
  form.post(route("teachers.store"));
};
</script>

<template>
  <AppLayout title="Teachers">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Add a new teacher
      </h2>
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
      <div
        class="
          w-full
          sm:max-w-3xl
          mt-6
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

          <div class="flex items-center justify-end mt-4">
            <JetButton
              class="mx-auto"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              Create
            </JetButton>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
