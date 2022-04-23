<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";
import JetNavLink from "@/Jetstream/NavLink.vue";
import TagPill from "@/Jetstream/TagPill.vue";
import JetDropdown from "@/Jetstream/Dropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink.vue";

defineProps({
  teachers: Array,
});
</script>

<template>
  <AppLayout title="Teachers">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Teachers
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="sym-container">
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
                <tr>
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
                      <TagPill :value="teacher.state" />
                    </div>
                  </td>
                  <td>
                    <div class="flex items-center">
                      <div class="ml-3 relative">
                        <JetDropdown align="right" width="48">
                          <template #trigger>
                            <span class="ddl-trigger-wrapper">
                              <span class="material-icons"> more_horiz </span>
                            </span>
                          </template>

                          <template #content>
                            <div class="ddl-content-wrapper">
                              <JetDropdownLink :href="route('profile.show')">
                                <span class="flex items-center">
                                  <span
                                    class="material-icons text-gray-400 text-xs"
                                    >edit</span
                                  >
                                  <span class="ml-2">Update</span>
                                </span>
                              </JetDropdownLink>

                              <JetDropdownLink :href="route('profile.show')">
                                <span class="flex items-center">
                                  <span
                                    class="material-icons text-gray-400 text-xs"
                                    >delete</span
                                  >
                                  <span class="ml-2">Delete</span>
                                </span>
                              </JetDropdownLink>

                              <JetDropdownLink :href="route('profile.show')">
                                <span class="flex items-center">
                                  <span
                                    class="material-icons text-gray-400 text-xs"
                                    >inventory_2</span
                                  >
                                  <span class="ml-2">Archive</span>
                                </span>
                              </JetDropdownLink>

                              <JetDropdownLink :href="route('profile.show')">
                                <span class="flex items-center">
                                  <span
                                    class="material-icons text-gray-400 text-xs"
                                    >list</span
                                  >
                                  <span class="ml-2">Courses</span>
                                </span>
                              </JetDropdownLink>
                            </div>
                          </template>
                        </JetDropdown>
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
