import { createStore, Store } from 'vuex';
import profiler from './api/profiler';
import api from './api/api';
import {State} from "@vue/runtime-core";

export const store = createStore({
  // adding modules in
  modules: {
    profiler,
    api
  },
});

export default store;

export function useStore(): Store<unknown> {
  return store;
}
