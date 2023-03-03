<?php

namespace App\Http\Controllers;

use App\Http\PokemonService;
use Inertia\Inertia;

use App\Models\Pokemon;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;

class PokemonController extends Controller
{
    private $pokemonService;

    public function __construct(PokemonService $pokemonService)
    {
        $this->pokemonService = $pokemonService;
    }

    public function index()
    {
        $pokemons = Pokemon::all();
        return Inertia::render('Welcome', ['pokemons' => $pokemons]);
    }

    public function savePokemons()
    {
        ini_set('max_execution_time', 1200);
        $pokemons = $this->pokemonService->getPokemons();

        foreach ($pokemons as $pokemonData) {
            $img = Image::make($pokemonData['img'])->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode('jpg', 80);

            $imgBase64 = base64_encode($img);

            $pokemon = new Pokemon([
                'id' => $pokemonData['id'],
                'name' => $pokemonData['name'],
                'img' => $imgBase64,
                'type' => implode(",", $pokemonData['type']),
                'height' => $pokemonData['height'],
                'weight' => $pokemonData['weight'],
            ]);
            $pokemon->save();
        }
        return $pokemons;
    }

    public function getPokemons(Request $request)
    {
        $pokemons = Pokemon::when($request->name, function ($query, $name) {
            return $query->where('name', 'LIKE', '%' . $name . '%');
        })
            ->orderBy('id')
            ->paginate(4);

        return [
            'pokemons' => $pokemons
        ];
    }
}
