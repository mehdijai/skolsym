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
const perm = ref(false);

const form = useForm({
  id: null,
  assign_to: null,
});

const filteredGroups = (t) => {
  return props.groups.filter(
    (g) => g.id !== t.id && g.state == "active" && !t.archived
  );
};

const deleteGroupPerm = (t) => {
  perm.value = true;
  removeGroup.value = t;
  form.id = t.id;
};

const deleteGroup = (t) => {
  removeGroup.value = t;
  form.id = t.id;
  form.assign_to = 'null'
};

const cancelDeletion = () => {
  removeGroup.value = null;
  form.reset();
};

const confirmDeletion = () => {
  if (perm.value == true) {
    delete form.assign_to;
    form.post(route("groups.destroy"), {
      onFinish: () => cancelDeletion(),
    });
  } else {
    form.post(route("groups.delete"), {
      onFinish: () => cancelDeletion(),
    });
  }
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
        v-if="!perm &&
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
            <option :selected="form.assign_to == 'null'" :value="'null'">
              None
            </option>
            <template
              v-for="group in filteredGroups(removeGroup)"
              :key="'ts-' + group.id"
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
          <th scope="col">Revenue</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <template v-if="groups.length > 0">
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
                  <Link
                    :href="route('groups.view', [group.id])"
                    class="font-bold text-gray-900 whitespace-no-wrap"
                  >
                    {{ group.title }}
                  </Link>
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
              <td>
                <span class="font-semibold text-green-700">
                  {{ group.month_revenue }} DH
                </span>
                <span class="font-regular mx-1">
                  /
                </span>
                <span class="font-semibold text-orange-700">
                  {{ group.month_revenue * group.course.teacher_percentage }} DH
                </span>
              </td>
              <td>
                <div class="flex items-center">
                  <div class="ml-3 relative">
                    <div class="dropdown dropdown-end dropdown-left">
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
                          translate-y-1/2
                        "
                      >
                        <li v-if="!group.archived">
                          <Link
                            :href="
                              route('students.create', { group: group.id })
                            "
                          >
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
                            :href="route('groups.update', { id: group.id })"
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
                          <p @click="deleteGroup(group)">
                            <span class="flex items-center">
                              <span class="material-icons text-gray-400 text-xs"
                                >delete</span
                              >
                              <span class="ml-2">Remove</span>
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
                        <li class="bg-red-100 hover:bg-red-200">
                          <p @click="deleteGroupPerm(group)">
                            <span class="flex items-center">
                              <span class="material-icons text-red-600 text-xs"
                                >delete_forever</span
                              >
                              <span class="ml-2">Delete permanently</span>
                            </span>
                          </p>
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

                <p class="mt-4 text-sm text-gray-500">Add a new group.</p>

                <Link
                  :href="route('groups.create')"
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
                  Create a group
                </Link>
              </div>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</template>