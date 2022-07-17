<template>
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold">
            @{{ floor.owner_user_id}} : 
        </h3>

        <div v-if="floor.owner_user_id === store.state.currentPost.data.current_user_id" class="flex items-center">
            
            <!-- Delete floor -->
            <button
                type="button"
                @click="deleteReply(floor.id)"
                class="
                flex
                items-center
                text-xs
                py-1
                px-3
                rounded-sm
                border border-transparent
                text-red-500
                hover:border-red-600
                "
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                <path
                    fill-rule="evenodd"
                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                    clip-rule="evenodd"
                />
                </svg>
                Delete
            </button>
        </div>
    </div>

    <div class="grid gap-3 grid-cols.12">
        <input 
            type="text"
            :name="'floor_text_' + floor.content"
            v-model="floor.content"
            @change="dataChange"
            :id="'flloor_text_' + floor.content"
            class="
                mt-1
                focus:ring-indigo-500
                focus:border-indigo-500
                block
                w-full
                shadow-sm
                sm:text-sm
                border-gray-300
                rounded-md
            "
        >
    </div>
       

</template>

<script setup>

import { computed, ref, watch } from "vue";
import store from "../../store";

const props = defineProps({
    floor: Object,
    index: Number,
});
const emit = defineEmits(["change", "deleteReply"]);
const floor = ref(JSON.parse(JSON.stringify(props.floor)));

// Emit the data change
function dataChange() {
  const data = JSON.parse(JSON.stringify(floor.value));
  emit("change", data);
}
function addQuestion() {
  emit("addQuestion", props.index + 1);
}
function deleteReply() {
  emit("deleteReply", props.floor);
}

</script>
