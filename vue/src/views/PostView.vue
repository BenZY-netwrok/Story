<template>
  <PageComponent>
    <template v-slot:header>
        
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">
                {{ route.params.id? model.title : "Create a Post" }}
            </h1>
            <!-- <pre>{{store.state.currentPost.data.current_user_id}}</pre> -->
             <button v-if="route.params.id && store.state.currentPost.data.current_user_id === model.owner_user_id" 
                type="button" 
                class="py-2 px-3 text-white bg-red-500 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" 
                @click="deletePost()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 -mt-1 inline-block" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Delete Post
              </button>
        </div>
    </template>
        <div v-if="postLoading" class="flex justify-center">Loading...</div>
        <form v-else @submit.prevent="savePost" class="animate-fade-in-down">
            <div class="shadow sm:rounded-md sm:overflow-hidden">
                <!-- Post Fields -->
                <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                <!-- Image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                    Image
                    </label>
                    <div class="mt-1 flex items-center">
                    <img
                        v-if="model.image_url"
                        :src="model.image_url"
                        :alt="model.title"
                        class="w-64 h-48 object-cover"
                    />
                    <span
                        v-else
                        class="flex items-center justify-center h-12 w-12 rounded-full overflow-hidden bg-gray-100"
                    >
                        <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-[80%] w-[80%] text-gray-300"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        >
                        <path
                            fill-rule="evenodd"
                            d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                            clip-rule="evenodd"
                        />
                        </svg>
                    </span>
                    <button
                        v-if="store.state.currentPost.data.current_user_id === model.owner_user_id" 
                        type="button"
                        class="relative overflow-hidden ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        <input
                        type="file"
                        @change="onImageChoose"
                        class="absolute left-0 top-0 right-0 bottom-0 opacity-0 cursor-pointer"
                        />
                        Change
                    </button>
                    </div>
                </div>
                <!--/ Image -->

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700"
                    >Title</label
                    >
                    <input
                    type="text"
                    :disabled="store.state.currentPost.data.current_user_id !== model.owner_user_id"
                    name="title"
                    id="title"
                    v-model="model.title"
                    autocomplete="post_title"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    />
                </div>
                <!--/ Title -->

                <!-- Content -->
                <div>
                    <label for="about" class="block text-sm font-medium text-gray-700">
                    Content
                    </label>
                    <div class="mt-1">
                    <textarea
                        id="content"
                        name="content"
                        :disabled="store.state.currentPost.data.current_user_id !== model.owner_user_id"
                        rows="3"
                        v-model="model.content"
                        autocomplete="post_content"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"
                        placeholder="Describe your post"
                    />
                    </div>
                </div>
                <!-- Content -->



                <!-- Status -->
                <div class="flex items-start" v-if="store.state.currentPost.data.current_user_id === model.owner_user_id">
                    <div class="flex items-center h-5">
                    <input
                        id="status"
                        name="status"
                        
                        type="checkbox"
                        v-model="model.status"
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                    </div>
                    <div class="ml-3 text-sm">
                    <label for="status" class="font-medium text-gray-700"
                        >Active</label
                    >
                    </div>
                </div>
                <!--/ Status -->
                </div>
                <!--/ Post Fields -->

                <!-- Floor Fields -->

                <div v-if="model.floors" class="px-4 py-5 bg-white space-y-6 sm:p-6">
                    <h3 class="text-2xl font-semibold flex items-center justify-between">
                        Reply
                    
                        <!-- Add new Reply -->

                            <button
                                type="button"
                                @click="addReply()"
                                class="flex items-center text-sm py-1 px-4 rounded-sm text-white bg-gray-600 hover:bg-gray-700"
                                >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /> 
                            </svg>
                                &nbsp; Reply
                            </button>
                    
                    </h3>

                    <div class="mt-10">
                        <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-1 md:gap-x-8 md:gap-y-10">
                            <div v-for="(floor, index) in model.floors" :key="floor.id" class="relative">
                                <dt>
                                    <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                    <component :is="floor.icon" class="h-6 w-6" aria-hidden="true" />
                                    </div>
                                    <p class="ml-16 text-lg leading-6 font-medium text-gray-900">{{ floor.id}}</p>
                                </dt>
                                <dd class="mt-2 ml-16 text-base text-gray-500">
                                    {{ floor.content }}
                                </dd>
                            <div class="flex justify-end">
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

                        </dl>
                    </div>
                </div>
                <!-- / Floor Fields -->
              
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6" v-if="store.state.currentPost.data.current_user_id === model.owner_user_id">
                <button
                    type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                    {{model.id ? 'Save' : 'Post'}}
                </button>
                </div>
            </div>
        </form>
  </PageComponent>
</template>

<script setup>
import { v4 as uuidv4 } from 'uuid';
import PageComponent from '../components/PageComponent.vue';
import { computed, ref, watch } from "vue";
import store from '../store';
import { useRoute, useRouter } from "vue-router";



const route = useRoute();

const router = useRouter();


const postLoading = computed(() => store.state.currentPost.loading)


//create empty post
let model = ref({
        title: '',
        content: '',
        num:0,
        status: false,
        owner_user_id: '',
        image_url:null,
        floors: null,
});


// Watch to current survey data change and when this happens we update local model
watch(
  () => store.state.currentPost.data,
  (newVal, oldVal) => {
    model.value = {
      ...JSON.parse(JSON.stringify(newVal)),
      status: newVal.status !== "draft",
    };
  }
);

if(route.params.id) {
    store.dispatch('getPost', route.params.id);
}

function onImageChoose (ev) {
    const file = ev.target.files[0];

    const reader = new FileReader();
    reader.onload = () => {
        // The field to send on backend and apply validations
        model.value.image = reader.result;

        //The field to display here
        model.value.image_url = reader.result;
    };
    reader.readAsDataURL(file);
}

function deleteReply(id) {
    
 model.value.floors = model.value.floors.filter(
    (r) => r.id !== id
 );
}

//create or update post
function savePost() {
    store.dispatch("savePost", model.value).then(({ data }) => {
        store.commit('notify', {
            type: 'success',
            message: 'Post was successfully updated'
        });
        
        router.push({
            name: "PostView",
            params: {id: data.data.id},
        });
    });
}

function deletePost() {
     if (
    confirm(
      `Are you sure you want to delete this post? Operation can't be undone!!`
    )
  ) {
    store.dispatch("deletePost", model.value.id).then(() => {
      router.push({
        name: "Myforum",
      });
    });
  }
}

</script>

