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
  students: Array,
  style: Object,
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
          <th scope="col">Groups</th>
          <th scope="col">State</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
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
                <p class="font-bold text-gray-900 whitespace-no-wrap">
                  {{ student.name }}
                </p>
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
            <td>
              <div class="flex items-center">
                <Link
                  :href="
                    route('groups.index', { search: 'students:' + student.id })
                  "
                  class="font-semibold whitespace-no-wrap"
                >
                  {{ student.groups.length }} group{{
                    Number(student.groups.length) > 1 ? "s" : ""
                  }}
                </Link>
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
                  <div class="dropdown dropdown-end">
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
      </tbody>
    </table>
  </div>
</template>