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
  groups: Array,
  style: Object,
});

const removeGroup = ref(null);

const filteredGroups = (t) => {
  return props.groups.filter(
    (g) => g.id !== t.id && g.state == "active" && !t.archived
  );
};

const deleteGroup = (t) => {
  removeGroup.value = t;
  form.id = t.id;
  form.assign_to =
    t.groups_count > 0 && filteredGroups(t).length > 0
      ? String(filteredGroups(t)[0].id)
      : null;
};

const cancelDeletion = () => {
  removeGroup.value = null;
  form.reset();
};

const form = useForm({
  id: null,
  assign_to: null,
});

const confirmDeletion = () => {
  form.post(route("groups.delete"), {
    onSuccess: () => cancelDeletion(),
  });
};
</script>
<template>
  <RemoveCard
    v-if="removeGroup !== null"
    @confirm="confirmDeletion"
    @cancel="cancelDeletion"
  >
    <template #content>
      <p class="text-gray-800 dark:text-gray-200 text-xl font-bold mt-4">
        Remove {{ removeGroup.title }}
      </p>
      <p class="text-gray-600 dark:text-gray-400 text-xs py-2 px-6">
        Are you sure you want to delete this record ?
      </p>
      <div
        v-if="
          removeGroup.students_count > 0 &&
          filteredGroups(removeGroup).length > 0
        "
        class="px-4 border-1 rounded"
      >
        <JetLabel for="assign_to" value="Assign groups to a group" />
        <JetSelectInput
          id="assign_to"
          v-model="form.assign_to"
          class="mt-1 block w-full"
        >
          <template #options>
            <template
              v-for="(group, index) in filteredGroups(removeGroup)"
              :key="'ts-' + index"
            >
              <option :value="group.id">
                {{ group.title }} ({{ group.students_count }})
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
          <th scope="col">Course</th>
          <th scope="col">Students</th>
          <th scope="col">State</th>
          <th v-if="groups.length > 0 ? groups[0].revenue : false" scope="col">
            Revenue
          </th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <template v-for="group in groups" :key="group.id">
          <tr
            :class="
              group.state === 'removed'
                ? 'bg-red-50'
                : group.archived
                ? 'bg-orange-50'
                : 'bg-white'
            "
          >
            <td>
              <div class="flex items-center">
                <p class="font-bold text-gray-900 whitespace-no-wrap">
                  {{ group.title }}
                </p>
              </div>
            </td>
            <td>
              <div class="flex items-center">
                <Link
                  :href="
                    route('groups.index', {
                      search: 'course:' + group.course.id,
                    })
                  "
                  class="
                    text-cyan-600
                    hover:text-cyan-800
                    whitespace-no-wrap
                    flex
                    items-center
                    font-semibold
                  "
                >
                  {{ group.course.title }}
                </Link>
              </div>
            </td>
            <td>
              <div class="flex items-center">
                <Link
                  :href="
                    route('students.index', { search: 'groups:' + group.id })
                  "
                  class="font-semibold whitespace-no-wrap"
                >
                  {{ group.students_count }} student{{
                    Number(group.students_count) > 1 ? "s" : ""
                  }}
                </Link>
              </div>
            </td>
            <td>
              <div class="flex items-center">
                <Link
                  :href="
                    route('groups.index', {
                      filter:
                        group.state === 'removed'
                          ? group.state
                          : group.archived
                          ? 'archived'
                          : group.state,
                    })
                  "
                >
                  <TagPill
                    :value="
                      group.state === 'removed'
                        ? group.state
                        : group.archived
                        ? 'archived'
                        : group.state
                    "
                  />
                </Link>
              </div>
            </td>
            <td v-if="groups.length > 0 ? groups[0].revenue : false">
              <span class="font-semibold text-orange-700">
                {{ group.revenue }} DH
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
                        <Link :href="route('profile.show')">
                          <span class="flex items-center">
                            <span class="material-icons text-gray-400 text-xs"
                              >add_circle</span
                            >
                            <span class="ml-2">Add student</span>
                          </span>
                        </Link>
                      </li>
                      <li>
                        <Link
                          :href="
                            route('students.index', {
                              search: 'groups:' + group.id,
                            })
                          "
                        >
                          <span class="flex items-center">
                            <span class="material-icons text-gray-400 text-xs"
                              >list</span
                            >
                            <span class="ml-2">Students</span>
                          </span>
                        </Link>
                      </li>

                      <li>
                        <Link :href="route('groups.update', { id: group.id })">
                          <span class="flex items-center">
                            <span class="material-icons text-gray-400 text-xs"
                              >edit</span
                            >
                            <span class="ml-2">Update</span>
                          </span>
                        </Link>
                      </li>

                      <li>
                        <p @click="deleteGroup(group)">
                          <span class="flex items-center">
                            <span class="material-icons text-gray-400 text-xs"
                              >delete</span
                            >
                            <span class="ml-2">Delete</span>
                          </span>
                        </p>
                      </li>

                      <li>
                        <Link :href="route('groups.archive', group.id)">
                          <span class="flex items-center">
                            <span
                              class="material-icons text-gray-400 text-xs"
                              >{{
                                group.archived ? "unarchive" : "inventory_2"
                              }}</span
                            >
                            <span class="ml-2">{{
                              group.archived ? "Unarchive" : "Archive"
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