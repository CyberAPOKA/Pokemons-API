<?php

namespace App\Http\Controllers;

use App\Http\PokemonService;
use Inertia\Inertia;

class PokemonController extends Controller
{
    private $pokemonService;

    public function __construct(PokemonService $pokemonService)
    {
        $this->pokemonService = $pokemonService;
    }

    public function index()
    {
        $pokemons = $this->pokemonService->getPokemons();
        return Inertia::render('Welcome', ['pokemons' => $pokemons]);
    }
}
