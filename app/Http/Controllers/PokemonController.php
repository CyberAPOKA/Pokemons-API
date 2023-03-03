<?php

namespace App\Http\Controllers;

use App\Http\PokemonService;
use Inertia\Inertia;

use App\Models\Pokemon;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\DB;

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

        $types = array();
        foreach ($pokemons as $pokemon) {
            $pokemonTypes = explode(',', $pokemon->type);
            $types = array_merge($types, $pokemonTypes);
        }

        $uniqueTypes = array_unique($types);
        sort($uniqueTypes);

        return Inertia::render('Welcome', [
            'pokemons' => $pokemons,
            'pokemonTypes' => $uniqueTypes
        ]);
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
            $height = preg_replace("/[^0-9.]/", "", $pokemonData['height']);
            $weight = preg_replace("/[^0-9.]/", "", $pokemonData['weight']);

            $pokemon = new Pokemon([
                'id' => $pokemonData['id'],
                'name' => $pokemonData['name'],
                // 'img' => $imgBase64,
                'img' => null,
                'type' => implode(",", $pokemonData['type']),
                'height' => $height,
                'weight' => $weight,
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
            ->when($request->type, function ($query, $type) {
                return $query->where('type', 'LIKE', '%' . $type . '%');
            });

        switch ($request->orderBy) {
            case 'weight_desc':
                $pokemons = $pokemons->orderByDesc('weight');
                break;
            case 'weight_asc':
                $pokemons = $pokemons->orderBy('weight');
                break;
            case 'height_desc':
                $pokemons = $pokemons->orderByDesc('height');
                break;
            case 'height_asc':
                $pokemons = $pokemons->orderBy('height');
                break;
            default:
                $pokemons = $pokemons->orderBy($request->orderBy ?? 'id');
                break;
        }

        $pokemons = $pokemons->paginate($request->perPage ?? 4);

        return [
            'pokemons' => $pokemons
        ];
    }
}
