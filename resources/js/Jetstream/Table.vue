<script setup>
import JetNavLink from "@/Jetstream/NavLink.vue";
import TagPill from "@/Jetstream/TagPill.vue";
const props = defineProps({
  title: String,
  data: Array,
});
</script>

<template>
  <div class="container mx-auto px-4 sm:px-8 max-w-5xl">
    <div class="py-8">
      <div class="flex flex-row mb-1 sm:mb-0 justify-between w-full">
        <h2 class="text-2xl leading-tight">{{ title }}</h2>
      </div>
      <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
        <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
          <table class="min-w-full leading-normal">
            <thead>
              <tr>
                <template
                  v-for="(column, index) in Object.keys(data[0])"
                  :key="'h-' + index"
                >
                  <th
                    scope="col"
                    class="
                      px-5
                      py-3
                      bg-white
                      border-b border-gray-200
                      text-gray-800 text-left text-sm
                      uppercase
                      font-normal
                    "
                  >
                    {{ column }}
                  </th>
                </template>
              </tr>
            </thead>
            <tbody>
              <template v-for="(row, index) in data" :key="'r-' + index">
                <tr>
                  <template v-for="(column, index) in row" :key="'c-' + index">
                    <td
                      class="
                        px-5
                        py-5
                        border-b border-gray-200
                        bg-white
                        text-sm
                      "
                    >
                      <div v-if="Array.isArray(column)">
                        <template
                          v-for="(item, index) in column"
                          :key="'i-' + index"
                        >
                          <span
                            v-if="item.icon"
                            :title="item.title"
                            class="material-icons mx-2 text-xs cursor-pointer"
                            :class="item.class"
                            >{{ item.content }}</span
                          >
                          <a
                            v-else
                            :title="item.title"
                            :class="
                              item.class ??
                              'text-indigo-600 mx-2 hover:text-indigo-900'
                            "
                            href="#"
                          >
                            {{ item.content }}
                          </a>
                        </template>
                      </div>
                      <div v-else class="flex items-center">
                        <TagPill
                          v-if="column.tagPill"
                          :content="column.content"
                          :color="column.color"
                        />
                        <p
                          v-else
                          class="text-gray-900 whitespace-no-wrap"
                          :class="column.class ?? ''"
                        >
                          {{
                            typeof column === "string" ? column : column.content
                          }}
                        </p>
                      </div>
                    </td>
                  </template>
                </tr>
              </template>
            </tbody>
          </table>
          <div
            class="
              px-5
              bg-white
              py-5
              flex flex-col
              xs:flex-row
              items-center
              xs:justify-between
            "
          >
            <div class="flex items-center">
              <button
                type="button"
                class="
                  w-full
                  p-4
                  border
                  text-base
                  rounded-l-xl
                  text-gray-600
                  bg-white
                  hover:bg-gray-100
                "
              >
                <svg
                  width="9"
                  fill="currentColor"
                  height="8"
                  class=""
                  viewBox="0 0 1792 1792"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M1427 301l-531 531 531 531q19 19 19 45t-19 45l-166 166q-19 19-45 19t-45-19l-742-742q-19-19-19-45t19-45l742-742q19-19 45-19t45 19l166 166q19 19 19 45t-19 45z"
                  ></path>
                </svg>
              </button>
              <button
                type="button"
                class="
                  w-full
                  px-4
                  py-2
                  border-t border-b
                  text-base text-indigo-500
                  bg-white
                  hover:bg-gray-100
                "
              >
                1
              </button>
              <button
                type="button"
                class="
                  w-full
                  px-4
                  py-2
                  border
                  text-base text-gray-600
                  bg-white
                  hover:bg-gray-100
                "
              >
                2
              </button>
              <button
                type="button"
                class="
                  w-full
                  px-4
                  py-2
                  border-t border-b
                  text-base text-gray-600
                  bg-white
                  hover:bg-gray-100
                "
              >
                3
              </button>
              <button
                type="button"
                class="
                  w-full
                  px-4
                  py-2
                  border
                  text-base text-gray-600
                  bg-white
                  hover:bg-gray-100
                "
              >
                4
              </button>
              <button
                type="button"
                class="
                  w-full
                  p-4
                  border-t border-b border-r
                  text-base
                  rounded-r-xl
                  text-gray-600
                  bg-white
                  hover:bg-gray-100
                "
              >
                <svg
                  width="9"
                  fill="currentColor"
                  height="8"
                  class=""
                  viewBox="0 0 1792 1792"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M1363 877l-742 742q-19 19-45 19t-45-19l-166-166q-19-19-19-45t19-45l531-531-531-531q-19-19-19-45t19-45l166-166q19-19 45-19t45 19l742 742q19 19 19 45t-19 45z"
                  ></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>