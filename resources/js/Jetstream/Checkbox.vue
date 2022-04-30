<script setup>
import { computed } from "vue";

const emit = defineEmits(["update:checked"]);

const props = defineProps({
  checked: {
    type: [Array, Boolean],
    default: false,
  },
  value: {
    type: String,
    default: null,
  },
  label: String,
});

const proxyChecked = computed({
  get() {
    return props.checked;
  },

  set(val) {
    emit("update:checked", val);
  },
});
</script>


<template>
  <!-- <input
    v-model="proxyChecked"
    type="checkbox"
    :value="value"
    class="
      rounded
      border-gray-300
      text-indigo-600
      shadow-sm
      focus:border-indigo-300
      focus:ring
      focus:ring-indigo-200
      focus:ring-opacity-50
    "
  /> -->
  <div>
    <div>
      <div class="my-3">
        <div class="relative inline-block w-10 mr-2 align-middle select-none">
          <input
            type="checkbox"
            name="toggle"
            id="Checkbox"
            :value="value"
            v-model="proxyChecked"
            class="
              checked:bg-blue-500
              outline-none
              focus:outline-none
              right-4
              checked:right-0
              duration-200
              ease-in
              absolute
              block
              w-6
              h-6
              rounded-full
              bg-white
              border-4
              appearance-none
              cursor-pointer
            "
          />
          <label
            for="Checkbox"
            class="
              block
              overflow-hidden
              h-6
              rounded-full
              bg-gray-300
              cursor-pointer
            "
          >
          </label>
        </div>
        <span v-if="label" class="label-text ml-2 font-bold">
          {{ label }}
        </span>
      </div>
    </div>
  </div>
</template>
