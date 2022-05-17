<script setup>
const Notifications = [
  {
    content: "Student exceeded payment delay",
    read: false,
    state: "info",
  },
];

const getStateColor = (state) => {
  switch (state) {
    case "error":
      return "red-700";
    case "warning":
      return "orange-700";
    case "info":
      return "blue-600";
    default:
      return "gray-100";
  }
};

const getNotClass = (not) => {
  return (
    (!not.read ? "bg-teal-100" : "bg-gray-100") +
    ` border-${getStateColor(not.state)}`
  );
};
</script>

<template>
  <div class="dropdown dropdown-end">
    <label tabindex="0">
      <div class="indicator">
        <span
          v-if="Notifications.length > 0"
          class="indicator-item badge border-white bg-red-600"
        ></span>
        <span
          :class="
            Notifications.length > 0
              ? 'bg-red-100 hover:bg-red-200 text-red-800'
              : 'bg-gray-100 hover:bg-gray-200 text-gray-800'
          "
          class="material-icons p-2 rounded-full cursor-pointer text-xs"
          >notifications</span
        >
      </div>
    </label>
    <ul
      tabindex="0"
      class="
        dropdown-content
        menu
        p-2
        shadow-lg
        bg-base-100
        border-t-2 border-gray-200
        rounded-md
        w-96
      "
    >
      <template v-for="(not, index) in Notifications" :key="index">
        <li>
          <div
            :class="getNotClass(not)"
            class="border-l-4 flex gap-x-2 items-center"
          >
            <span class="mr-auto text-sm text-gray-800">{{ not.content }}</span>
            <span
              class="
                material-icons
                text-xs
                cursor-pointer
                bg-white
                text-red-700
                p-1
                rounded-full
                shadow
                hover:bg-gray-100
              "
              >close</span
            >
            <span
              v-if="!not.read"
              class="
                material-icons
                text-xs
                bg-white
                text-green-700
                p-1
                rounded-full
                shadow
                hover:bg-gray-100
              "
              >check_circle</span
            >
            <span
              v-else
              class="
                material-icons
                text-xs
                bg-white
                text-teal-700
                p-1
                rounded-full
                shadow
                hover:bg-gray-100
              "
              >check_circle_outline</span
            >
          </div>
        </li>
      </template>
    </ul>
  </div>
</template>