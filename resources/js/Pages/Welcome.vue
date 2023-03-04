<template>
  <div class="relative dark:bg-gray-900">
    <div class="container mx-auto grid grid-cols-1 md:grid-cols-2 p-2 gap-6">
      <div>
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-2 p-2 gap-6">
          <div>
            <label class="text-white px-2" for="name">Nome</label>
            <div class="mt-1">
              <input
                type="text"
                v-model="searchName"
                id="name"
                name="name"
                class="bg-white border border-l-gray-50 rounded w-full"
                placeholder="Pesquise..."
              />
            </div>
          </div>
          <div>
            <label class="text-white px-2" for="level">Selecione o tipo</label>
            <div class="mt-1">
              <Multiselect
                v-model="searchType"
                :searchable="true"
                name="level"
                id="level"
                placeholder="Selecione uma Opção"
                class="bg-white border border-l-gray-50 rounded w-full"
                :options="pokemonTypes"
              />
            </div>
          </div>
        </div>
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-2 p-2 gap-6">
          <div>
            <label class="text-white px-2" for="order">Ordenação por</label>
            <div class="mt-1">
              <select
                v-model="orderBy"
                placeholder="Selecione uma Opção"
                class="bg-white border border-l-gray-50 rounded w-full"
                id="order"
                name="order"
              >
                <option value="id">ID</option>
                <option value="name">Nome A-Z</option>
                <option value="weight_desc">Maior Peso</option>
                <option value="weight_asc">Menor Peso</option>
                <option value="height_desc">Maior Altura</option>
                <option value="height_asc">Menor Altura</option>
              </select>
            </div>
          </div>
          <div>
            <label class="text-white px-2" for="perPage">Nº de Pokemons por página</label>
            <div class="mt-1">
              <select
                v-model="pokemonsPerPage"
                placeholder="Pokemons por página"
                class="bg-white border border-l-gray-50 rounded w-full"
                id="perPage"
                name="perPage"
              >
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="15">20</option>
                <option value="30">30</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="9999">Todos</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="container mx-auto text-white">
        <div class="text-center text-xl"><h1>API de Pokemons</h1></div>
        <div>
          <span
            >Este projeto é uma simples API de Pokemons deste
            <a class="text-blue-500" href="https://www.canalti.com.br/api/pokemons.json">
              LINK</a
            >
            onde eu salvo todos os itens no banco de dados e depois retorno os dados dando
            a possibilidade do usuários fazer diversos filtros e ordenações.
            <div>
              <h1 class="text-red-600">Tecnologias e versões usadas:</h1>
              <span
                >Laravel 10, Vue 3 com Inertia, Tailwind, Vite e algumas
                bibliotecas.</span
              >
            </div>
          </span>
        </div>
      </div>
    </div>
  </div>

  <div
    class="relative sm:flex sm:justify-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white"
  >
    <div class="max-w-7xl mx-auto p-6 lg:p-8">
      <div
        class="mx-auto grid max-w-6xl grid-cols-1 gap-6 p-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5"
      >
        <div
          v-for="(pokemon, index) in pokemons.data"
          :key="pokemon.id"
          :pokemons="pokemons"
          :index="index"
          class="rounded-xl w-44 bg-white p-3 shadow-lg hover:shadow-xl hover:transform hover:scale-105 duration-300"
        >
          <a href="#">
            <div class="relative flex items-end overflow-hidden rounded-xl">
              <img :src="getImageUrl(pokemon.img)" alt="Pokemon Image" />
            </div>

            <div class="mt-1 p-2">
              <h2 class="text-slate-700">
                {{ pokemon.id }}-{{ pokemon.name.split(" ")[0] }}
              </h2>
              <p class="mt-1 text-sm text-slate-400">
                {{ pokemon.type }}
              </p>

              <div class="mt-3 flex items-end justify-between">
                <p class="text-lg font-bold text-blue-500">{{ pokemon.height }}m</p>
                <p class="text-lg font-bold text-blue-500">{{ pokemon.weight }}kg</p>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="pl-5 text-center">
        <TailwindPagination
          class=""
          :data="pokemons"
          :filters="filters"
          :limit="limit"
          :show-last-page="true"
          @pagination-change-page="(page) => getPokemons(page, filters)"
        />
      </div>
    </div>
  </div>
</template>
<script setup>
import axios from "axios";
import { ref, watchEffect, reactive, onMounted, computed, defineProps, watch } from "vue";
import { TailwindPagination } from "laravel-vue-pagination";
import { createStore } from "vuex";
import Multiselect from "@vueform/multiselect";

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

const props = defineProps({
  pokemonTypes: Array,
});

const pokemons = ref({ data: [] });
const searchName = ref(null);
const searchType = ref(null);
const orderBy = ref(null);
const filters = reactive({
  name: "",
  type: "",
  order: "id",
});
const limit = ref(5);
const pokemonsPerPage = ref(5);
const store = createStore({
  state: {
    filters: {
      name: "",
      type: "",
      order: "",
    },
  },
  mutations: {
    setFilters(state, filters) {
      state.filters = filters;
    },
  },
});

const getPokemons = (page = 1, filters = store.state.filters) => {
  let params = { ...filters };
  if (Array.isArray(params.type)) {
    params.type = params.type.join(",");
  }
  params.perPage = pokemonsPerPage.value;
  axios
    .get(`api/pokemons?page=${page}`, {
      params,
    })
    .then((response) => {
      pokemons.value = response.data.pokemons;
    });
};

onMounted(() => {
  searchName.value = "";
  searchType.value = "";
  orderBy.value = "id";
});

watchEffect(() => {
  if (window.innerWidth < 768) {
    limit.value = 1;
  } else {
    limit.value = 5;
  }
  filters.name = searchName.value;
  filters.type = searchType.value;
  filters.order = orderBy.value;
  store.commit("setFilters", filters);
  getPokemons(1, {
    orderBy: orderBy.value,
    name: searchName.value,
    type: searchType.value,
  });
});

watch(pokemonsPerPage, () => {
  getPokemons(1, {
    orderBy: orderBy.value,
    name: searchName.value,
    type: searchType.value,
  });
});
</script>

<style>
img {
  width: 10rem;
  height: 10rem;
}
</style>
<style src="@vueform/multiselect/themes/default.css"></style>
