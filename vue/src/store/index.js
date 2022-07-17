import {createStore} from "vuex";
import axiosClient from "../axios";
import { AnnotationIcon, GlobeAltIcon, LightningBoltIcon, ScaleIcon } from '@heroicons/vue/outline'

// const tmpPosts = [
//     {
//         id:1,
//         title: 'The temp title',
//         status: 'draft',
//         content: 'This is a test for adding question to post',
//         floors:[
//             {
//                 id: 1,
//                 content: 'this is a good place',
//                 user_name: 'Ben',
//                 reply_user_id: '10'
//             },

//             {
//                 id: 2,
//                 content: "need to work on it and make it better",
//                 user_name: 'Joe',
//                 reply_user_id: '20'
//             },
//         ],

//     },
// ]

const store = createStore({
    state: {
        user: {
            data: {  
                // name: 'Tom Cook',
                // email: 'tom@example.com',
                // imageUrl:
                // 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
            },
            token: sessionStorage.getItem('TOKEN'),
        },
        // flors: [...tmpPosts],
        currentPost: {
            loading: false,
            replyAdd:false,
            data: {}
        },
        posts: {
            loading: false,
            links: [],
            data: []
        },
        notification: {
            show: false,
            type: null,
            message: null
        },
    },
    getters: {},
    actions: {
        getReplyAdd({commit}){
            commit('setReplyAdd', true)
        },
        getPost({commit}, id) {
            commit('setCurrentPostLoading', true);
            return axiosClient
              .get(`/post/${id}`)
              .then((res) => {
                commit("setCurrentPost", res.data);
                commit("setCurrentPostLoading", false);
                return res;
              })
              .catch((err) => {
                commit("setCurrentPostLoading", false);
                throw err;
              });
        },
        savePost({commit}, post) {
            delete post.image_url;
            let response;
            
            if(post.id) {
                console.log('post id exist');
                response = axiosClient
                .put(`/post/${post.id}`, post)
                .then((res) => {
                    commit("setCurrentPost", res.data);
                    return res;
                });
            }else {
                response = axiosClient.post("/post", post).then((res) => {
                    commit("setCurrentPost", res.data);
                    return res;
                });
            }


            return response;
        },
        replyPost({commit}, {postId, replies}) {
            return axiosClient.post(`/post/${postId}/reply`, {replies});
        },
        deletePost({}, id) {
            return axiosClient.delete(`/post/${id}`);
        },

        getPosts({commit}, {url = null} = {}) {
            commit('setPostsLoading', true)
            url = url || "/post";
            console.log(url);
            return axiosClient.get(url).then((res) => {
            commit('setPostsLoading', false)
            commit("setPosts", res.data);
            return res;
            });
        },

        register({commit}, user) {
            return axiosClient.post('/register', user)
            .then(({data}) => {
                commit('setUser', data)
                return data;
            })
          },
        login({commit}, user) {
            return axiosClient.post('/login', user)
            .then(({data}) => {
                commit('setUser', data)
                return data;
            })
          },
        logout({commit}) {
            return axiosClient.post('/logout')
            .then(response => {
                commit('logout')
                return response;
            })
        }
    },
    mutations: {
        setReplyAdd: (state, replyAdd) => {
            state.currentPost.replyAdd = replyAdd;
        },
        setCurrentPostLoading: (state, loading) => {
            state.currentPost.loading = loading;
          },

        setPostsLoading: (state, loading) => {
            state.posts.loading = loading;
          },

        setCurrentPost: (state, post) => {
            state.currentPost.data = post.data;
          },

        setPosts: (state, posts) => {
        state.posts.links = posts.meta.links;
        state.posts.data = posts.data;
        },


        logout: (state) => {
            state.user.token = null;
            state.user.data = {};
            sessionStorage.removeItem('TOKEN');
           
        },
        setUser: (state, userData) => {
            state.user.token = userData.token;
            state.user.data = userData.user;
            sessionStorage.setItem('TOKEN', userData.token);
        },
        notify: (state, {message, type}) => {
            state.notification.show = true;
            state.notification.type = type;
            state.notification.message = message;
            setTimeout(() => {
                state.notification.show = false;
            }, 3000)
        }
    },
    modules: {}
})

export default store;