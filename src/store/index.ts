import { createStore } from 'vuex';
import profiler from './api/profiler';
import api from './api/api';

export const store = createStore({
  // adding modules in
  modules: {
    profiler,
    api
  },
});

export default store;

export function useStore(){
  return store;
}
