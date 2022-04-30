<script setup>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import TagPill from "@/Jetstream/TagPill.vue";
import { ref } from "@vue/reactivity";
import { computed } from "@vue/runtime-core";

const props = defineProps({
  courses: Array,
  profile: {
    type: Boolean,
    default: false
  }
});

</script>
<template>
  <div class="sym-container">
    <slot name="header" />
    <table>
      <thead>
        <tr>
          <th scope="col">Title</th>
          <th v-if="profile === false" scope="col">Teacher</th>
          <th scope="col">Period</th>
          <th scope="col">Price</th>
          <th scope="col">Payment type</th>
          <th scope="col">State</th>
          <th v-if="courses[0].revenue" scope="col">Revenue</th>
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
                <Link class="font-bold" :href="route('profile.show')">
                  <p
                    class="text-gray-900 hover:text-gray-500 whitespace-no-wrap"
                  >
                    {{ course.title }}
                  </p>
                </Link>
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
                {{course.revenue}} DH
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
                        <Link :href="route('profile.show')">
                          <span class="flex items-center">
                            <span class="material-icons text-gray-400 text-xs"
                              >list</span
                            >
                            <span class="ml-2">Groups</span>
                          </span>
                        </Link>
                      </li>
                      <li>
                        <Link :href="route('profile.show')">
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