<template>
  <!-- <div class="container">
    <div class="row">
      <div class="col-md-3">
        <label class="form-control-label" for="level">Turno</label>
        <input
          type="text"
          v-model="searchName"
          id="level"
          name="level"
          class="form-control"
          placeholder="Nome"
        />
      </div>
    </div>
  </div> -->

  <div
    class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white"
  >
    <div class="max-w-7xl mx-auto p-6 lg:p-8">
      <div
        class="mx-auto grid max-w-6xl grid-cols-1 gap-6 p-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
      >
        <div
          v-for="(pokemon, index) in pokemons.data"
          :key="pokemon.id"
          :pokemons="pokemons"
          :index="index"
          class="rounded-xl bg-white p-3 shadow-lg hover:shadow-xl hover:transform hover:scale-105 duration-300"
        >
          <a href="#">
            <div class="relative flex items-end overflow-hidden rounded-xl">
              <img :src="getImageUrl(pokemon.img)" alt="Pokemon Image" />
            </div>

            <div class="mt-1 p-2">
              <h2 class="text-slate-700">{{ pokemon.id }}-{{ pokemon.name }}</h2>
              <p class="mt-1 text-sm text-slate-400">
                {{ pokemon.type }}
              </p>

              <div class="mt-3 flex items-end justify-between">
                <p class="text-lg font-bold text-blue-500">
                  {{ pokemon.height }}
                </p>
                <p class="text-lg font-bold text-blue-500">
                  {{ pokemon.weight }}
                </p>
              </div>
            </div>
          </a>
        </div>
      </div>
      <TailwindPagination
        :data="pokemons"
        :filters="filters"
        @pagination-change-page="(page) => getPokemons(page, filters)"
      />
    </div>
  </div>
</template>
<script setup>
import axios from "axios";
import { ref, watchEffect, reactive, onMounted } from "vue";
import { TailwindPagination } from "laravel-vue-pagination";
import { createStore } from "vuex";

const getImageUrl = (base64) => {
  if (!base64) return "";

  const binary = atob(base64);
  const array = new Uint8Array(binary.length);

  for (let i = 0; i < binary.length; i++) {
    array[i] = binary.charCodeAt(i);
  }

  const blob = new Blob([array], { type: "image/jpeg" });
  const url = URL.createObjectURL(blob);

  return url;
};

const pokemons = ref({ data: [] });
const searchName = ref(null);

const filters = reactive({
  name: "",
});

const store = createStore({
  state: {
    filters: {
      name: "",
    },
  },
  mutations: {
    setFilters(state, filters) {
      state.filters = filters;
    },
  },
});

const getPokemons = (page = 1, filters = store.state.filters) => {
  axios
    .get(`api/pokemons?page=${page}`, {
      params: filters,
    })
    .then((response) => {
      pokemons.value = response.data.pokemons;
    });
};

onMounted(() => {
  searchName.value = "";
});

watchEffect(() => {
  filters.name = searchName.value;
  store.commit("setFilters", filters);
  getPokemons();
});
</script>

<style>
img {
  width: 100%;
}
</style>
