<template>
  <PageComponent>
    <div v-if="allposts.loading" class="flex justify-center">Loading...</div>
    <div v-else>
      <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3">
        
        <PostListItem 
        v-for="(allpost, ind)  in allposts.data" 
        :key="allpost.id" 
        :post="allpost"
        class="opacity-0 animate-fade-in-down"
        :style="{ animationDelay: `${ind * 0.1}s` }"
        @delete="deletePost(allpost)"
        :isDashboard="true"
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
          v-for="(link, i) of allposts.links"
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
            i === allposts.links.length - 1 ? 'rounded-r-md' : '',
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
import store from '../store';
import { computed } from 'vue';
import PostListItem from '../components/PostListItem.vue';

  const allposts = computed(() => store.state.posts);
  store.dispatch('getPosts', { url: '/allposts' });

  function getForPage(ev, link) {
    ev.preventDefault();
    if (!link.url || link.active) {
      return;
    }
      store.dispatch("getPosts", { url: link.url });
  }

</script>