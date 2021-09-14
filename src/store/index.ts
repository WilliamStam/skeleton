import { createStore, Store } from 'vuex';
import api from './api';
import {State} from "@vue/runtime-core";

export const store = createStore({
  // adding modules in
  modules: {
    api
  },
});

export default store;

export function useStore(): Store<unknown> {
  return store;
}
