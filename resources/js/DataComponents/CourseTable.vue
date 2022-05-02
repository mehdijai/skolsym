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
  courses: Array,
  style: Object,
  profile: {
    type: Boolean,
    default: false,
  },
});

const removeCourse = ref(null);

const filteredCourses = (t) => {
  return props.courses.filter(
    (c) => c.id !== t.id && c.state == "active" && !t.archived
  );
};

const deleteCourse = (t) => {
  removeCourse.value = t;
  form.id = t.id;
  form.assign_to =
    t.groups_count > 0 && filteredCourses(t).length > 0
      ? String(filteredCourses(t)[0].id)
      : null;
};

const cancelDeletion = () => {
  removeCourse.value = null;
  form.reset();
};

const form = useForm({
  id: null,
  assign_to: null,
});

const confirmDeletion = () => {
  form.post(route("courses.delete"), {
    onSuccess: () => cancelDeletion(),
  });
};
</script>
<template>
  <RemoveCard
    v-if="removeCourse !== null"
    @confirm="confirmDeletion"
    @cancel="cancelDeletion"
  >
    <template #content>
      <p class="text-gray-800 dark:text-gray-200 text-xl font-bold mt-4">
        Remove {{ removeCourse.title }}
      </p>
      <p class="text-gray-600 dark:text-gray-400 text-xs py-2 px-6">
        Are you sure you want to delete this record ?
      </p>
      <div
        v-if="
          removeCourse.groups_count > 0 &&
          filteredCourses(removeCourse).length > 0
        "
        class="px-4 border-1 rounded"
      >
        <JetLabel for="assign_to" value="Assign groups to a course" />
        <JetSelectInput
          id="assign_to"
          v-model="form.assign_to"
          class="mt-1 block w-full"
        >
          <template #options>
            <template
              v-for="(course, index) in filteredCourses(removeCourse)"
              :key="'ts-' + index"
            >
              <option :value="course.id">
                {{ course.title }} ({{ course.groups_count }})
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
          <th scope="col">Title</th>
          <th v-if="profile === false" scope="col">Teacher</th>
          <th scope="col">Period</th>
          <th scope="col">Price</th>
          <th scope="col">Payment type</th>
          <th scope="col">Groups</th>
          <th scope="col">State</th>
          <th v-if="courses.length > 0 ? courses[0].revenue : false" scope="col">Revenue</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <template v-for="course in courses" :key="course.id">
          <tr
            :class="
              course.state === 'removed'
                ? 'bg-red-50'
                : course.archived
                ? 'bg-orange-50'
                : 'bg-white'
            "
          >
            <td>
              <div class="flex items-center">
                <p
                  class="
                    font-bold
                    text-gray-900
                    whitespace-no-wrap
                  "
                >
                  {{ course.title }}
                </p>
              </div>
            </td>
            <td v-if="profile === false">
              <div class="flex items-center">
                <Link
                  :href="route('teachers.view', [course.teacher.id])"
                  class="
                    text-cyan-600
                    hover:text-cyan-800
                    whitespace-no-wrap
                    flex
                    items-center
                    font-semibold
                  "
                >
                  {{ course.teacher.name }}
                  <span class="material-icons text-xs ml-1">open_in_new</span>
                </Link>
              </div>
            </td>
            <td>
              <div class="flex items-center">
                <p class="whitespace-no-wrap">
                  {{ course.period }} week{{
                    Number(course.period) > 1 ? "s" : ""
                  }}
                </p>
              </div>
            </td>
            <td>
              <div class="flex items-center">
                <p class="whitespace-no-wrap">{{ course.price }} DH</p>
              </div>
            </td>
            <td>
              <div class="flex items-center">
                <p class="capitalize whitespace-no-wrap">
                  {{ course.payment_type }}
                </p>
              </div>
            </td>
            <td>
              <div class="flex items-center">
                <Link
                  :href="
                    route('groups.index', { search: 'course:' + course.id })
                  "
                  class="font-semibold whitespace-no-wrap"
                >
                  {{ course.groups_count }} group{{
                    Number(course.groups_count) > 1 ? "s" : ""
                  }}
                </Link>
              </div>
            </td>
            <td>
              <div class="flex items-center">
                <Link
                  :href="
                    route('courses.index', {
                      filter:
                        course.state === 'removed'
                          ? course.state
                          : course.archived
                          ? 'archived'
                          : course.state,
                    })
                  "
                >
                  <TagPill
                    :value="
                      course.state === 'removed'
                        ? course.state
                        : course.archived
                        ? 'archived'
                        : course.state
                    "
                  />
                </Link>
              </div>
            </td>
            <td v-if="courses[0].revenue">
              <span class="font-semibold text-orange-700">
                {{ course.revenue }} DH
              </span>
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
                          :href="route('courses.update', { id: course.id })"
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
                        <p @click="deleteCourse(course)">
                          <span class="flex items-center">
                            <span class="material-icons text-gray-400 text-xs"
                              >delete</span
                            >
                            <span class="ml-2">Delete</span>
                          </span>
                        </p>
                      </li>

                      <li>
                        <Link :href="route('courses.archive', course.id)">
                          <span class="flex items-center">
                            <span
                              class="material-icons text-gray-400 text-xs"
                              >{{
                                course.archived ? "unarchive" : "inventory_2"
                              }}</span
                            >
                            <span class="ml-2">{{
                              course.archived ? "Unarchive" : "Archive"
                            }}</span>
                          </span>
                        </Link>
                      </li>
                      <li>
                        <Link
                          :href="
                            route('groups.index', {
                              search: 'course:' + course.id,
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
                          :href="route('groups.create', { course: course.id })"
                        >
                          <span class="flex items-center">
                            <span class="material-icons text-gray-400 text-xs"
                              >add_circle</span
                            >
                            <span class="ml-2">Add group</span>
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