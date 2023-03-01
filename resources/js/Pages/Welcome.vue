<script setup>
import { Bootstrap4Pagination } from "laravel-vue-pagination";
defineProps({
  pokemons: Object,
});
</script>

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

      <div class="flex justify-center mt-16 px-6 sm:items-center sm:justify-between">
        <div
          class="ml-4 text-center text-sm text-gray-500 dark:text-gray-400 sm:text-right sm:ml-0"
        >
          <!-- Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }}) -->
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.bg-dots-darker {
  background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}
@media (prefers-color-scheme: dark) {
  .dark\:bg-dots-lighter {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
  }
}
</style>
