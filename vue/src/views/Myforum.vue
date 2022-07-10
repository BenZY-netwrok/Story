
<template>
  <PageComponent>
    <template v-slot:header>
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">My forum list</h1>
        <router-link
          :to="{name: 'PostCreate'}"
          class="py-2 px-3 text-white bg-emerald-500 rounded-md hover:bg-emerald-600"
        >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
          Add new Post
        </router-link>
      </div>
    </template>
    <div v-if="posts.loading" class="flex justify-center">Loading...</div>
    <div v-else>
      <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3">
        <PostListItem 
        v-for="(post, ind)  in posts.data" 
        :key="post.id" 
        :post="post"
        class="opacity-0 animate-fade-in-down"
        :style="{ animationDelay: `${ind * 0.1}s` }"
        @delete="deletePost(post)"
        />
      </div>
    </div>
     <div class="flex justify-center mt-5">
                <nav
                  class="relative z-0 inline-flex justify-center rounded-md shadow-sm -space-x-px"
                  aria-label="Pagination"
                >
                  <!-- Current: "z-10 bg-indigo-50 border-indigo-500 text-indigo-600", Default: "bg-white border-gray-300 text-gray-500 hover:bg-gray-50" -->
                  <a
                    v-for="(link, i) of posts.links"
                    :key="i"
                    :disabled="!link.url"
                    href="#"
                    @click="getForPage($event, link)"
                    aria-current="page"
                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap"
                    :class="[
                      link.active
                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                      i === 0 ? 'rounded-l-md bg-gray-100 text-gray-700' : '',
                      i === posts.links.length - 1 ? 'rounded-r-md' : '',
                    ]"
                    v-html="link.label"
                  >
                  </a>
                </nav>
              </div>
  </PageComponent>
</template>

<script setup>
import PageComponent from '../components/PageComponent.vue';
import { computed } from "vue";
import store from '../store';
import PostListItem from '../components/PostListItem.vue';

const posts = computed(() => store.state.posts);

store.dispatch('getPosts')

function deletePost(post) {
  if(confirm(`Are you want to delete this survey? Operation can't be undone!`)) {
    store.dispatch('deletePost', post.id)
    .then(() => {
      store.dispatch('getPosts')
    })
  }
}

function getForPage(ev, link) {
  ev.preventDefault();
  if (!link.url || link.active) {
    return;
  }
    store.dispatch("getPosts", { url: link.url });
}

</script>