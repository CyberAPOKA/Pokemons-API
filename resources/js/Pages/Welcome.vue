<template>
  <div class="relative dark:bg-gray-900">
    <div class="container mx-auto text-center">
      <div class="text-white">
        <h1 class="text-xl">
          Este é um projeto sobre API de Pokemons com Laravel 10, Vue 3 e Tailwind!
        </h1>
        <div>
          Eu estou pegando todos os Pokemons via API do link
          <a class="text-sky-400" href="https://www.canalti.com.br/api/pokemons.json"
            >www.canalti.com.br/api/pokemons.json</a
          >
          e salvando-os dados no banco de dados.
        </div>
        <div>
          O sistema permite a paginação de pokemons por página, diversos filtros,
          ordenações, visualização, edição, criação e exclusão de Pokemons.
        </div>
      </div>
    </div>
  </div>

  <div class="relative dark:bg-gray-900">
    <div class="container mx-auto grid grid-cols-4 p-2 gap-6">
      <div>
        <label class="text-white px-2" for="level">Nome</label>
        <input
          type="text"
          v-model="searchName"
          id="level"
          name="level"
          class="bg-white border border-l-gray-50 rounded"
          placeholder="Nome"
        />
      </div>
      <div>
        <label class="text-white px-2" for="level">Turno</label>
        <Multiselect
          v-model="searchType"
          :searchable="true"
          placeholder="Selecione uma Opção"
          class="form-control"
          :options="pokemonTypes"
        />
      </div>
      <div>
        <label class="text-white px-2" for="order">Ordernar por</label>
        <select
          v-model="orderBy"
          :searchable="true"
          placeholder="Selecione uma Opção"
          class="form-control"
          id="order"
          name="order"
        >
          <option value="id" selected>ID</option>
          <option value="name">Nome</option>
          <option value="weight">Peso</option>
          <option value="height">Altura</option>
        </select>
      </div>
      <div>
        <label class="text-white px-2" for="perPage">Pokemons por página</label>
        <select
          v-model="pokemonsPerPage"
          :searchable="true"
          placeholder="Pokemons por página"
          class="form-control"
          id="perPage"
          name="perPage"
        >
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="15">15</option>
        </select>
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
          class="rounded-xl bg-white p-3 shadow-lg hover:shadow-xl hover:transform hover:scale-105 duration-300"
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
          :data="pokemons"
          :filters="filters"
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
  order: "",
});
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
  orderBy.value = "";
});

watchEffect(() => {
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
