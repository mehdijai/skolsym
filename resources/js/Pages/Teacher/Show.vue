<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";
import JetNavLink from "@/Jetstream/NavLink.vue";
import TagPill from "@/Jetstream/TagPill.vue";
import JetDropdown from "@/Jetstream/Dropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink.vue";
import Breadcrumbs from "@/Jetstream/Breadcrumbs.vue";

defineProps({
  teachers: Array,
});
</script>

<template>
  <AppLayout title="Teachers">
    <template #header>
      <Breadcrumbs>
        <template #items>
          <li><Link :href="route('dashboard')">Dashboard</Link></li>
          <li>Teachers</li>
        </template>
      </Breadcrumbs>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="sym-container">
          <div class="py-8 flex flex-row mb-1 sm:mb-0 justify-between w-full">
            <Link
              class="
                text-sky-800
                hover:text-sky-600
                font-bold
                text-2xl
                leading-tight
              "
              :href="route('teachers.create')"
              >Create new teacher</Link
            >
          </div>
          <table>
            <thead>
              <tr>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody>
              <template v-for="teacher in teachers" :key="teacher.id">
                <tr :class="teacher.archived ? 'bg-orange-50' : 'bg-white'">
                  <td>
                    <div class="flex items-center">
                      <Link class="font-bold" :href="route('profile.show')">
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
                        class="
                          text-cyan-600
                          hover:text-cyan-800
                          whitespace-no-wrap
                        "
                      >
                        {{ teacher.email }}
                      </a>
                    </div>
                  </td>
                  <td>
                    <div class="flex items-center">
                      <a
                        :href="'tel:' + teacher.phone"
                        class="
                          text-cyan-600
                          hover:text-cyan-800
                          whitespace-no-wrap
                        "
                      >
                        {{ teacher.phone }}
                      </a>
                    </div>
                  </td>
                  <td>
                    <div class="flex items-center">
                      <Link
                        :href="
                          route('teachers.index', {
                            filter: teacher.archived
                              ? 'archived'
                              : teacher.state,
                          })
                        "
                      >
                        <TagPill
                          :value="teacher.archived ? 'archived' : teacher.state"
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
                              shadow
                              bg-base-100
                              rounded-md
                              w-52
                            "
                          >
                            <li>
                              <Link
                                :href="
                                  route('teachers.update', { id: teacher.id })
                                "
                              >
                                <span class="flex items-center">
                                  <span
                                    class="material-icons text-gray-400 text-xs"
                                    >edit</span
                                  >
                                  <span class="ml-2">Update</span>
                                </span>
                              </Link>
                            </li>

                            <li>
                              <Link :href="route('profile.show')">
                                <span class="flex items-center">
                                  <span
                                    class="material-icons text-gray-400 text-xs"
                                    >delete</span
                                  >
                                  <span class="ml-2">Delete</span>
                                </span>
                              </Link>
                            </li>

                            <li>
                              <Link
                                :href="route('teachers.archive', teacher.id)"
                              >
                                <span class="flex items-center">
                                  <span
                                    class="material-icons text-gray-400 text-xs"
                                    >{{
                                      teacher.archived
                                        ? "unarchive"
                                        : "inventory_2"
                                    }}</span
                                  >
                                  <span class="ml-2">{{
                                    teacher.archived ? "Unarchive" : "Archive"
                                  }}</span>
                                </span>
                              </Link>
                            </li>

                            <li>
                              <Link :href="route('profile.show')">
                                <span class="flex items-center">
                                  <span
                                    class="material-icons text-gray-400 text-xs"
                                    >list</span
                                  >
                                  <span class="ml-2">Courses</span>
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
      </div>
    </div>
  </AppLayout>
</template>
