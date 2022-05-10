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
  teachers: Array,
  errors: Object,
  style: Object,
});

const removeTeacher = ref(null);

const deleteTeacher = (t) => {
  removeTeacher.value = t;
  form.id = t.id;
  form.assign_to = null;
};

const cancelDeletion = () => {
  removeTeacher.value = null;
  form.reset();
};

const form = useForm({
  id: null,
  assign_to: null,
});

const confirmDeletion = () => {
  form.post(route("teachers.delete"), {
    onFinish: () => cancelDeletion(),
  });
};
</script>
<template>
  <RemoveCard
    v-if="removeTeacher !== null"
    @confirm="confirmDeletion"
    @cancel="cancelDeletion"
  >
    <template #content>
      <p class="text-gray-800 dark:text-gray-200 text-xl font-bold mt-4">
        Remove {{ removeTeacher.name }}
      </p>
      <p class="text-gray-600 dark:text-gray-400 text-xs py-2 px-6">
        Are you sure you want to delete this record ?
      </p>
      <div v-if="removeTeacher.courses_count > 0" class="px-4 border-1 rounded">
        <JetLabel for="assign_to" value="Assign courses to a teacher" />
        <JetSelectInput
          id="assign_to"
          v-model="form.assign_to"
          class="mt-1 block w-full"
        >
          <template #options>
            <template
              v-for="(teacher, index) in teacher.filter(
                (t) => t.id !== form.id
              )"
              :key="'ts-' + index"
            >
              <option :value="teacher.id">
                {{ teacher.name }} ({{ teacher.courses_count }})
              </option>
            </template>
          </template>
        </JetSelectInput>
        <JetInputError
          v-if="form.errors.assign_to"
          :message="form.errors.assign_to"
        />
      </div>
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
          <th scope="col">Courses</th>
          <th scope="col">Status</th>
          <th scope="col">Month $</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <template v-if="teachers.length > 0">
          <template v-for="teacher in teachers" :key="teacher.id">
            <tr
              :class="
                teacher.state === 'removed'
                  ? 'bg-red-50'
                  : teacher.archived
                  ? 'bg-orange-50'
                  : 'bg-white'
              "
            >
              <td>
                <div class="flex items-center">
                  <Link
                    class="font-bold"
                    :href="route('teachers.view', [teacher.id])"
                  >
                    <p
                      class="
                        text-gray-900
                        hover:text-gray-500
                        whitespace-no-wrap
                      "
                    >
                      {{ teacher.name }}
                    </p>
                  </Link>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <a
                    :href="'mailto:' + teacher.email"
                    class="text-cyan-600 hover:text-cyan-800 whitespace-no-wrap"
                  >
                    {{ teacher.email }}
                  </a>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <a
                    :href="'tel:' + teacher.phone"
                    class="text-cyan-600 hover:text-cyan-800 whitespace-no-wrap"
                  >
                    {{ teacher.phone }}
                  </a>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <Link
                    :href="
                      route('courses.index', {
                        search: 'teacher:' + teacher.id,
                      })
                    "
                    class="font-semibold whitespace-no-wrap"
                  >
                    {{ teacher.courses_count }} course{{
                      Number(teacher.courses_count) > 1 ? "s" : ""
                    }}
                  </Link>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <Link
                    :href="
                      route('teachers.index', {
                        filter:
                          teacher.state === 'removed'
                            ? teacher.state
                            : teacher.archived
                            ? 'archived'
                            : teacher.state,
                      })
                    "
                  >
                    <TagPill
                      :value="
                        teacher.state === 'removed'
                          ? teacher.state
                          : teacher.archived
                          ? 'archived'
                          : teacher.state
                      "
                    />
                  </Link>
                </div>
              </td>
              <td>
                <div class="flex items-center">
                  <p class="text-green-500 font-bold">
                    {{ teacher.month_revenue }} DH
                  </p>
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
                          shadow
                          bg-base-100
                          rounded-md
                          w-52
                        "
                      >
                        <li>
                          <Link
                            :href="route('teachers.update', { id: teacher.id })"
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
                          <p @click="deleteTeacher(teacher)">
                            <span class="flex items-center">
                              <span class="material-icons text-gray-400 text-xs"
                                >delete</span
                              >
                              <span class="ml-2">Delete</span>
                            </span>
                          </p>
                        </li>
                        <li>
                          <Link :href="route('teachers.archive', teacher.id)">
                            <span class="flex items-center">
                              <span
                                class="material-icons text-gray-400 text-xs"
                                >{{
                                  teacher.archived ? "unarchive" : "inventory_2"
                                }}</span
                              >
                              <span class="ml-2">{{
                                teacher.archived ? "Unarchive" : "Archive"
                              }}</span>
                            </span>
                          </Link>
                        </li>
                        <li>
                          <Link
                            :href="
                              route('courses.index', {
                                search: 'teacher:' + teacher.id,
                              })
                            "
                          >
                            <span class="flex items-center">
                              <span class="material-icons text-gray-400 text-xs"
                                >list</span
                              >
                              <span class="ml-2">Courses</span>
                            </span>
                          </Link>
                        </li>
                        <li>
                          <Link
                            :href="
                              route('courses.create', {
                                teacher: teacher.id,
                              })
                            "
                          >
                            <span class="flex items-center">
                              <span class="material-icons text-gray-400 text-xs"
                                >add_circle</span
                              >
                              <span class="ml-2">Add course</span>
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

                <p class="mt-4 text-sm text-gray-500">Add a new teacher.</p>

                <Link
                  :href="route('teachers.create')"
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
                  Create a teacher
                </Link>
              </div>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</template>