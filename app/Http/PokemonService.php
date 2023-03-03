<?php

namespace App\Http;

class PokemonService
{
    private $apiUrl = 'https://www.canalti.com.br/api/pokemons.json';

    public function getPokemons()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        return array_slice($data['pokemon'], 0, 30);
    }
}
