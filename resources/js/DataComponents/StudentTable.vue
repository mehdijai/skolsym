<script setup>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import TagPill from "@/Jetstream/TagPill.vue";
import RemoveCard from "@/Jetstream/RemoveCard.vue";
import JetSelectInput from "@/Jetstream/SelectInput.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import { reactive, ref } from "@vue/reactivity";
import { computed, onMounted } from "@vue/runtime-core";
import Checkbox from "@/Jetstream/Checkbox.vue";

const props = defineProps({
  students: Array,
  style: Object,
  group: {
    type: Object,
    default: null,
  },
});

const removeStudent = ref(null);

const filteredStudents = (t) => {
  return props.students.filter(
    (s) => s.id !== t.id && s.state == "active" && !t.archived
  );
};

const deleteStudent = (t) => {
  removeStudent.value = t;
  form.id = t.id;
};

const cancelDeletion = () => {
  removeStudent.value = null;
  form.reset();
};

const form = useForm({
  id: null,
});

const confirmDeletion = () => {
  form.post(route("students.delete"), {
    onSuccess: () => cancelDeletion(),
  });
};

const payForm = useForm({
  student_id: null,
  courses: [],
});

const paying = ref(null);
const attachedCourses = ref(null);

function getCourses(student) {
  let courses = [];

  student.groups.forEach((group) => {
    if (courses.find((c) => c.id == group.course.id) === undefined) {
      group.course.checked = false;
      courses.push(group.course);
    }
  });

  return courses;
}

const completePayment = useForm({
  id: null,
  price: null,
  teacher_percentage: null,
});

const payPending = (payment) => {
  completePayment.id = payment.id;
  if (props.group != null) {
    completePayment.price = props.group.course.price;
    completePayment.teacher_percentage = props.group.course.teacher_percentage;
  }
  completePayment.post(route("payments.pay"), {
    onSuccess: () => {
      completePayment.reset();
    },
  });
};

const pay = (student) => {
  if (props.group != null) {
    payForm.student_id = student.id;
    payForm.courses = [props.group.course];
    payForm.post(route("payments.store"), {
      onSuccess: () => {
        payForm.reset();
      },
    });
  } else {
    paying.value = student;
    payForm.student_id = student.id;
    attachedCourses.value = getCourses(student);
  }
};

const cancelPay = () => {
  paying.value = null;
  payForm.student_id = null;
  payForm.courses = [];
};

const confirmPay = () => {
  payForm.courses = attachedCourses.value.filter((c) => c.checked == true);
  payForm.post(route("payments.store"), {
    onSuccess: () => {
      payForm.reset();
      paying.value = null;
    },
  });
};

onMounted(() => {
  props.students.forEach((student) => {
    const targetEl = document.getElementById("dropdown-" + student.id);

    const triggerEl = document.getElementById("dropdownDefault-" + student.id);

    new Dropdown(targetEl, triggerEl, {
      placement: "bottom",
    });
  });
});
</script>
<template>
  <RemoveCard
    v-if="removeStudent !== null"
    @confirm="confirmDeletion"
    @cancel="cancelDeletion"
  >
    <template #content>
      <p class="text-gray-800 dark:text-gray-200 text-xl font-bold mt-4">
        Remove {{ removeStudent.title }}
      </p>
      <p class="text-gray-600 dark:text-gray-400 text-xs py-2 px-6">
        Are you sure you want to delete this record ?
      </p>
    </template>
  </RemoveCard>

  <div
    v-if="paying != null"
    class="
      overflow-y-auto overflow-x-hidden
      fixed
      z-50
      top-1/2
      left-1/2
      -translate-x-1/2 -translate-y-1/2
    "
  >
    <div class="relative p-4 w-full max-w-md h-full md:h-auto">
      <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
        <button
          type="button"
          class="
            mx-2
            my-2
            float-right
            text-gray-400
            bg-transparent
            hover:bg-gray-200 hover:text-gray-900
            rounded-lg
            text-sm
            p-1.5
            ml-auto
            inline-flex
            items-center
            dark:hover:bg-gray-800 dark:hover:text-white
          "
          @click="cancelPay"
        >
          <svg
            class="w-5 h-5"
            fill="currentColor"
            viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"
            ></path>
          </svg>
        </button>
        <div class="p-6 text-center">
          <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
            This student "{{ paying.name }}" has multiple courses! Please check
            payed courses.
          </h3>
          <div class="p-2 bg-gray-50 rounded mb-5">
            <template v-for="course in attachedCourses" :key="course.id">
              <div class="block my-2 flex gap-4 items-center">
                <Checkbox
                  :name="'pay_group_' + course.id"
                  v-model="course.checked"
                  :checked="course.checked"
                />
                <label
                  :for="'pay_group_' + course.id"
                  class="label-text ml-2 font-bold"
                >
                  {{ course.title }} - {{ course.price }} DH
                </label>
              </div>
            </template>
          </div>
          <button
            @click="confirmPay"
            type="button"
            class="
              text-white
              bg-red-600
              hover:bg-red-800
              focus:ring-4 focus:outline-none focus:ring-red-300
              dark:focus:ring-red-800
              font-medium
              rounded-lg
              text-sm
              inline-flex
              items-center
              px-5
              py-2.5
              text-center
              mr-2
            "
          >
            Pay
          </button>
          <button
            @click="cancelPay"
            type="button"
            class="
              text-gray-500
              bg-white
              hover:bg-gray-100
              focus:ring-4 focus:outline-none focus:ring-gray-200
              rounded-lg
              border border-gray-200
              text-sm
              font-medium
              px-5
              py-2.5
              hover:text-gray-900
              focus:z-10
              dark:bg-gray-700
              dark:text-gray-300
              dark:border-gray-500
              dark:hover:text-white
              dark:hover:bg-gray-600
              dark:focus:ring-gray-600
            "
          >
            No, cancel
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="sym-container" :style="style">
    <slot name="header" />
    <table>
      <thead>
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Email</th>
          <th scope="col">Phone</th>
          <th scope="col">Age</th>
          <th scope="col">Grade</th>
          <th scope="col">{{ group != null ? "Other groups" : "Groups" }}</th>
          <th scope="col">Payment State</th>
          <th scope="col">Total Paid</th>
          <th scope="col">State</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <template v-if="students.length > 0">
          <template v-for="student in students" :key="student.id">
            <tr
              :class="
                student.state === 'removed'
                  ? 'bg-red-50'
                  : student.archived
                  ? 'bg-orange-50'
                  : 'bg-white'
              "
            >
              <td>
                <div class="flex items-center">
                  <Link
                    :href="route('students.view', [student.id])"
                    v-if="group == null"
                    class="font-bold text-gray-900 whitespace-no-wrap"
                  >
                    {{ student.name }}
                  </Link>
                  <Link
                    v-else
                    :href="
                      route('students.index', {
                        search: 'student:' + student.id,
                      })
                    "
                    class="font-bold text-gray-900 whitespace-no-wrap"
                  >
                    {{ student.name }}
                  </Link>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <a
                    :href="'mailto:' + student.email"
                    class="text-cyan-600 hover:text-cyan-800 whitespace-no-wrap"
                  >
                    {{ student.email }}
                  </a>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <a
                    :href="'tel:' + student.phone"
                    class="text-cyan-600 hover:text-cyan-800 whitespace-no-wrap"
                  >
                    {{ student.phone }}
                  </a>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <p class="whitespace-no-wrap">
                    {{ student.age }}
                  </p>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <p class="whitespace-no-wrap">
                    {{ student.grade }}
                  </p>
                </div>
              </td>
              <td v-if="group != null">
                <div class="flex items-center">
                  <Link
                    :href="
                      route('groups.index', {
                        search: 'students:' + student.id,
                      })
                    "
                    class="font-semibold whitespace-no-wrap"
                  >
                    {{ student.groups_count - 1 }}
                    group{{ Number(student.groups_count - 1) > 1 ? "s" : "" }}
                  </Link>
                </div>
              </td>
              <td v-else>
                <div class="flex items-center">
                  <Link
                    :href="
                      route('groups.index', {
                        search: 'students:' + student.id,
                      })
                    "
                    class="font-semibold whitespace-no-wrap"
                  >
                    {{ student.groups.length }} group{{
                      Number(student.groups.length) > 1 ? "s" : ""
                    }}
                  </Link>
                </div>
              </td>
              <td v-if="group != null">
                <span class="flex items-center">
                  <Link
                    :href="
                      route('groups.view', [
                        group.id,
                        {
                          search:
                            student.payments.length > 0
                              ? student.payments[0].state
                              : 'pending',
                        },
                      ])
                    "
                  >
                    <TagPill
                      :value="
                        student.payments.length > 0
                          ? student.payments[0].state
                          : 'pending'
                      "
                    />
                  </Link>
                </span>
              </td>
              <td v-else>
                <button
                  :id="'dropdownDefault-' + student.id"
                  :data-dropdown-toggle="'dropdown-' + student.id"
                  class="
                    underline
                    decoration-dotted
                    cursor-pointer
                    font-semibold
                    text-gray-600
                    hover:text-gray-400
                  "
                  type="button"
                >
                  Payments state
                </button>

                <div
                  :id="'dropdown-' + student.id"
                  class="
                    z-50
                    hidden
                    bg-white
                    divide-y divide-gray-100
                    rounded
                    shadow
                    dark:bg-gray-700
                  "
                >
                  <ul
                    class="py-1 text-sm text-gray-700 dark:text-gray-200"
                    :aria-labelledby="'dropdownDefault-' + student.id"
                  >
                    <template v-for="group in student.groups" :key="group.id">
                      <li
                        class="
                          flex
                          justify-between
                          items-center
                          gap-4
                          px-4
                          py-2
                        "
                      >
                        <Link
                          :href="route('groups.view', [group.id])"
                          class="link font-bold"
                          >{{ group.title }}</Link
                        >
                        <span class="font-bold text-orange-600">
                          {{
                            student.month_paid * group.course.teacher_percentage
                          }}
                          DH
                        </span>
                        <Link
                          :href="
                            route('students.index', {
                              filter:
                                group.course.payments.find(
                                  (p) => p.student_id == student.id
                                ) != undefined
                                  ? group.course.payments.find(
                                      (p) => p.student_id == student.id
                                    ).state
                                  : 'pending',
                            })
                          "
                        >
                          <TagPill
                            :value="
                              group.course.payments.find(
                                (p) => p.student_id == student.id
                              ) != undefined
                                ? group.course.payments.find(
                                    (p) => p.student_id == student.id
                                  ).state
                                : 'pending'
                            "
                          />
                        </Link>
                      </li>
                    </template>
                  </ul>
                </div>
              </td>
              <td class="w-fit break-normal">
                <div class="flex items-center w-full">
                  <span class="font-semibold text-sm text-green-700">
                    {{ student.month_paid }} DH
                  </span>
                  <template v-if="group != null">
                    <span class="font-regular text-xs mx-1">/</span>
                    <span class="font-semibold text-sm text-orange-700">
                      {{ student.month_paid * group.course.teacher_percentage }}
                      DH
                    </span>
                  </template>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <Link
                    :href="
                      route('students.index', {
                        filter:
                          student.state === 'removed'
                            ? student.state
                            : student.archived
                            ? 'archived'
                            : student.state,
                      })
                    "
                  >
                    <TagPill
                      :value="
                        student.state === 'removed'
                          ? student.state
                          : student.archived
                          ? 'archived'
                          : student.state
                      "
                    />
                  </Link>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <div class="ml-3 relative">
                    <div class="dropdown dropdown-left dropdown-end">
                      <label
                        tabindex="0"
                        class="
                          material-icons
                          bg-gray-200
                          rounded-full
                          px-2
                          cursor-pointer
                          hover:bg-gray-300
                        "
                      >
                        more_horiz
                      </label>
                      <ul
                        tabindex="0"
                        class="
                          dropdown-content
                          menu
                          z-40
                          shadow
                          bg-base-100
                          rounded-md
                          w-52
                        "
                      >
                        <li
                          v-if="
                            group != null &&
                            student.payments.length > 0 &&
                            student.payments[0].state != 'paid'
                          "
                        >
                          <p @click="payPending(student.payments[0])">
                            <span class="flex items-center">
                              <span class="material-icons text-gray-400 text-xs"
                                >money</span
                              >
                              <span class="ml-2">Pay</span>
                            </span>
                          </p>
                        </li>
                        <li v-if="student.payments.length == 0">
                          <p @click="pay(student)">
                            <span class="flex items-center">
                              <span class="material-icons text-gray-400 text-xs"
                                >money</span
                              >
                              <span class="ml-2">New payment</span>
                            </span>
                          </p>
                        </li>
                        <li>
                          <Link
                            :href="
                              route('groups.create', { student: student.id })
                            "
                          >
                            <span class="flex items-center">
                              <span class="material-icons text-gray-400 text-xs"
                                >add_circle</span
                              >
                              <span class="ml-2">Add group</span>
                            </span>
                          </Link>
                        </li>
                        <li>
                          <Link
                            :href="
                              route('groups.index', {
                                search: 'students:' + student.id,
                              })
                            "
                          >
                            <span class="flex items-center">
                              <span class="material-icons text-gray-400 text-xs"
                                >list</span
                              >
                              <span class="ml-2">Groups</span>
                            </span>
                          </Link>
                        </li>
                        <li>
                          <Link
                            :href="route('students.update', { id: student.id })"
                          >
                            <span class="flex items-center">
                              <span class="material-icons text-gray-400 text-xs"
                                >edit</span
                              >
                              <span class="ml-2">Update</span>
                            </span>
                          </Link>
                        </li>

                        <li>
                          <p @click="deleteStudent(student)">
                            <span class="flex items-center">
                              <span class="material-icons text-gray-400 text-xs"
                                >delete</span
                              >
                              <span class="ml-2">Delete</span>
                            </span>
                          </p>
                        </li>
                        <li>
                          <Link :href="route('students.archive', student.id)">
                            <span class="flex items-center">
                              <span
                                class="material-icons text-gray-400 text-xs"
                                >{{
                                  student.archived ? "unarchive" : "inventory_2"
                                }}</span
                              >
                              <span class="ml-2">{{
                                student.archived ? "Unarchive" : "Archive"
                              }}</span>
                            </span>
                          </Link>
                        </li>
                      </ul>
                    </div>
                  </div>
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

                <p class="mt-4 text-sm text-gray-500">Add a new student.</p>

                <Link
                  :href="route('students.create')"
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
                  Create a student
                </Link>
              </div>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</template>