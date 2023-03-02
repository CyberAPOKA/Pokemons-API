<template>
  <div
    class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white"
  >
    <div class="max-w-7xl mx-auto p-6 lg:p-8">
      <div
        class="mx-auto grid max-w-6xl grid-cols-1 gap-6 p-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
      >
        <div
          v-for="pokemon in pokemons"
          :key="pokemon.id"
          class="rounded-xl bg-white p-3 shadow-lg hover:shadow-xl hover:transform hover:scale-105 duration-300"
        >
          <a href="#">
            <div class="relative flex items-end overflow-hidden rounded-xl">
              <img :src="pokemon.img" alt="Pokemon Image" />
            </div>

            <div class="mt-1 p-2">
              <h2 class="text-slate-700">{{ pokemon.id }}-{{ pokemon.name }}</h2>
              <p class="mt-1 text-sm text-slate-400">
                {{ pokemon.type.join(" and ") }}
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

    </div>
  </div>
</template>
<script setup>
import { Bootstrap4Pagination } from "laravel-vue-pagination";
defineProps({
  pokemons: Object,
});
</script>
