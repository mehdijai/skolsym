<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import { ref, watch, watchEffect } from "@vue/runtime-core";

const props = defineProps({
  students: {
    type: Object,
    default: null,
  },
  courses: {
    type: Object,
    default: null,
  },
});

const isAllChecked = () => {
  if (props.courses != null) {
    return props.courses.every(
      (c) => c.checked == true && c.groups.every((g) => g.checked == true)
    );
  }

  if (props.students != null) {
    return props.students.every((s) => s.checked == true);
  }

  return false;
};

const allChecked = ref(isAllChecked());

watchEffect(() => {
  if (props.courses != null) {
    allChecked.value = props.courses.every(
      (c) => c.checked == true && c.groups.every((g) => g.checked == true)
    );
  }
  if (props.students != null) {
    allChecked.value = props.students.every((s) => s.checked == true);
  }
});

watch(allChecked, () => {
  if (allChecked.value == true) {
    if (props.courses != null) {
      props.courses.map((c) => {
        if (c.groups.length > 0) {
          c.checked = true;
          c.groups.map((g) => {
            g.checked = true;
            return g;
          });
        }

        return c;
      });
    }
    if (props.students != null) {
      props.students.map((s) => {
        s.checked = true;
        return s;
      });
    }
  } else {
    if (props.courses != null) {
      props.courses.map((c) => {
        if (c.groups.length > 0) {
          c.checked = false;
          c.groups.map((g) => {
            g.checked = false;
            return g;
          });
        }

        return c;
      });
    }
    if (props.students != null) {
      props.students.map((s) => {
        s.checked = false;
        return s;
      });
    }
  }
});

// if (props.courses != null) {
//   watch(props.courses, () => {
//     console.log(props.courses);
//     props.courses.map((c) => {
//       if (c.groups.find((g) => g.checked === true) === undefined) {
//         c.checked = false;
//       }
//       return c;
//     });
//   });
// }
</script>
<template>
  <div class="overflow-x-auto w-full">
    <table class="table w-full">
      <!-- head -->
      <thead>
        <tr>
          <th>
            <label>
              <input
                v-model="allChecked"
                value="allChecked"
                type="checkbox"
                class="checkbox"
              />
            </label>
          </th>
          <template v-if="students != null">
            <th>Name</th>
            <th>Email</th>
          </template>
          <template v-if="courses != null">
            <th>Title</th>
            <th>Teacher</th>
          </template>
        </tr>
      </thead>
      <tbody>
        <template v-if="students != null">
          <template v-for="(student, index) in students" :key="index">
            <tr>
              <th>
                <label>
                  <input
                    v-model="student.checked"
                    :value="student.checked"
                    type="checkbox"
                    class="checkbox"
                  />
                </label>
              </th>
              <td>
                <Link
                  :href="
                    route('students.index', { search: 'student:' + student.id })
                  "
                  class="font-bold"
                  >{{ student.name }}</Link
                >
              </td>
              <td>
                <a class="link" :href="'mailto:' + student.email">
                  {{ student.email }}
                </a>
              </td>
            </tr>
          </template>
        </template>
        <template v-if="courses != null">
          <template v-for="(course, index) in courses" :key="index">
            <tr>
              <th :class="course.groups.length == 0 ? 'bg-red-100' : ''">
                <label>
                  <input
                    v-model="course.checked"
                    :value="course.checked"
                    type="checkbox"
                    class="checkbox"
                    :disabled="course.groups.length == 0"
                  />
                </label>
              </th>
              <td :class="course.groups.length == 0 ? 'bg-red-100' : ''">
                <Link
                  :href="
                    route('courses.index', { search: 'course:' + course.id })
                  "
                  class="font-bold"
                  >{{ course.title }}</Link
                >
              </td>
              <td :class="course.groups.length == 0 ? 'bg-red-100' : ''">
                <Link :href="route('teachers.view', course.teacher.id)">
                  {{ course.teacher.name }}
                </Link>
              </td>
            </tr>
            <template v-if="course.checked === true">
              <template
                v-for="(group, index) in course.groups"
                :key="'g' + index"
              >
                <tr>
                  <th class="bg-gray-100">
                    <label>
                      <input
                        v-model="group.checked"
                        :value="group.checked"
                        type="checkbox"
                        class="checkbox"
                      />
                    </label>
                  </th>
                  <td class="bg-gray-50" colspan="2">
                    <Link
                      :href="
                        route('groups.index', { search: 'group:' + group.id })
                      "
                      class="font-bold"
                      >{{ group.title }}</Link
                    >
                  </td>
                </tr>
              </template>
            </template>
          </template>
        </template>
      </tbody>
    </table>
  </div>
</template>
