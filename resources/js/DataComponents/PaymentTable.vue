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
</script>
<template>
  <div class="sym-container" :style="style">
    <slot name="header" />
    <table>
      <thead>
        <tr>
          <th scope="col">Ref</th>
          <th scope="col">Student</th>
          <th scope="col">Course</th>
          <th scope="col">Teacher</th>
          <th scope="col">Paid $</th>
          <th scope="col">Teacher $</th>
          <th scope="col">Created at</th>
          <th scope="col">Paid at</th>
          <th scope="col">Payment Type</th>
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
                    :href="
                      route('students.index', {
                        search: 'student:' + payment.student.id,
                      })
                    "
                    class="link"
                  >
                    {{ payment.student.name }}
                  </Link>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <Link
                    :href="
                      route('courses.index', {
                        search: 'course:' + payment.course.id,
                      })
                    "
                    class="link"
                  >
                    {{ payment.course.title }}
                  </Link>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <Link
                    :href="route('teachers.view', [payment.course.teacher_id])"
                    class="link"
                  >
                    {{ payment.course.teacher.name }}
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
                      payment.course.teacher_percentage * payment.amount_payed
                    }}
                  </p>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <p>
                    {{ formatDate(new Date(payment.created_at)) }}
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
                    {{ payment.course.payment_type }}
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
                  <span
                    class="
                      material-icons
                      rounded-full
                      p-2
                      cursor-pointer
                      hover:bg-gray-100
                      text-xs
                    "
                  >
                    edit
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