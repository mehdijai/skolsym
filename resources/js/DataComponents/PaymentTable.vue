<script setup>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import TagPill from "@/Jetstream/TagPill.vue";
import RemoveCard from "@/Jetstream/RemoveCard.vue";
import JetSelectInput from "@/Jetstream/SelectInput.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import { ref } from "@vue/reactivity";
import { computed } from "@vue/runtime-core";

const props = defineProps({
  payments: Array,
  style: Object,
});

function padTo2Digits(num) {
  return num.toString().padStart(2, "0");
}

function formatDate(date) {
  return [
    date.getFullYear(),
    padTo2Digits(date.getMonth() + 1),
    padTo2Digits(date.getDate()),
  ].join("-");
}

const destroyPayment = ref(null);

const deletePayment = (p) => {
  destroyPayment.value = p;
  destroyForm.id = p.id;
};

const cancelDeletion = () => {
  destroyPayment.value = null;
  destroyForm.reset();
};

const destroyForm = useForm({
  id: null,
});

const confirmDeletion = () => {
  destroyForm.post(route("payments.destroy"), {
    onSuccess: () => cancelDeletion(),
  });
};
</script>
<template>
  <RemoveCard
    v-if="destroyPayment !== null"
    @confirm="confirmDeletion"
    @cancel="cancelDeletion"
  >
    <template #content>
      <p class="text-gray-800 dark:text-gray-200 text-xl font-bold mt-4">
        Remove {{ destroyPayment.ref }} - {{ destroyPayment.student.name }}
      </p>
      <p class="text-gray-600 dark:text-gray-400 text-xs py-2 px-6">
        Are you sure you want to delete this record ?
      </p>
    </template>
  </RemoveCard>
  <div class="sym-container" :style="style">
    <slot name="header" />
    <table>
      <thead>
        <tr>
          <th scope="col">Ref</th>
          <th scope="col">Student</th>
          <th scope="col">Group</th>
          <th scope="col">Course</th>
          <th scope="col">Teacher</th>
          <th scope="col">Paid $</th>
          <th scope="col">Teacher $</th>
          <th scope="col">Paid at</th>
          <th scope="col">Type</th>
          <th scope="col">State</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <template v-if="payments.length > 0">
          <template v-for="payment in payments" :key="payment.id">
            <tr
              :class="
                payment.state === 'removed'
                  ? 'bg-red-50'
                  : payment.archived
                  ? 'bg-orange-50'
                  : 'bg-white'
              "
            >
              <td>
                <div class="flex items-center">
                  <p class="font-bold text-sm text-gray-900">
                    {{ payment.ref }}
                  </p>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <Link
                    :href="route('students.view', [payment.student.id])"
                    class="link"
                  >
                    {{ payment.student.name }}
                  </Link>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <Link
                    :href="route('groups.view', [payment.group.id])"
                    class="link"
                  >
                    {{ payment.group.title }}
                  </Link>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <Link
                    :href="
                      route('courses.index', {
                        search: 'course:' + payment.group.course.id,
                      })
                    "
                    class="link"
                  >
                    {{ payment.group.course.title }}
                  </Link>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <Link
                    :href="
                      route('teachers.view', [payment.group.course.teacher_id])
                    "
                    class="link"
                  >
                    {{ payment.group.course.teacher.name }}
                  </Link>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <p class="font-bold text-green-500">
                    {{ payment.amount_payed }}
                  </p>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <p class="font-bold text-orange-500">
                    {{
                      payment.group.course.teacher_percentage *
                      payment.amount_payed
                    }}
                  </p>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <p>
                    {{ formatDate(new Date(payment.paid_at)) }}
                  </p>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <p class="capitalize whitespace-no-wrap">
                    {{ payment.group.course.payment_type }}
                  </p>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <Link
                    :href="
                      route('payments.index', {
                        filter:
                          payment.state === 'removed'
                            ? payment.state
                            : payment.archived
                            ? 'archived'
                            : payment.state,
                      })
                    "
                  >
                    <TagPill
                      :value="
                        payment.state === 'removed'
                          ? payment.state
                          : payment.archived
                          ? 'archived'
                          : payment.state
                      "
                    />
                  </Link>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <Link
                    :href="route('payments.update', [payment.id])"
                    class="
                      material-icons
                      rounded-full
                      p-2
                      cursor-pointer
                      hover:bg-gray-100
                      text-xs
                      mr-1
                    "
                  >
                    edit
                  </Link>
                  <span
                    @click="deletePayment(payment)"
                    class="
                      material-icons
                      rounded-full
                      p-2
                      cursor-pointer
                      text-red-600
                      bg-red-100
                      hover:bg-red-200
                      text-xs
                    "
                  >
                    delete_forever
                  </span>
                </div>
              </td>
            </tr>
          </template>
        </template>
        <template v-else>
          <tr>
            <td colspan="100">
              <div
                class="
                  p-8
                  text-center
                  border border-gray-200
                  rounded-lg
                  bg-white
                  shadow
                "
              >
                <h2 class="text-2xl font-bold">The list still empty!</h2>

                <p class="mt-4 text-sm text-gray-500">Add a new payment.</p>

                <Link
                  :href="route('payments.create')"
                  class="
                    inline-flex
                    items-center
                    px-5
                    py-3
                    mt-8
                    font-medium
                    text-white
                    bg-cyan-700
                    rounded-lg
                    hover:bg-cyan-600
                  "
                >
                  Create a payment
                </Link>
              </div>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</template>