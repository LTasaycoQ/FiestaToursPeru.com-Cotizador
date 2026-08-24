<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GeoController extends Controller
{
    public function paises()
    {
        $paises = Cache::remember('geo:paises:todos', now()->addDay(), function () {
            return Country::where('is_active', true)
                ->orderBy('name')
                ->get(['id_countries', 'name'])
                ->map(function ($country) {
                    return [
                        'id' => $country->id_countries,
                        'nombre' => $country->name,
                    ];
                })->toArray();
        });

        return response()->json($paises);
    }

    // public function estados(Request $request)
    // {
    //     $request->validate(['country_id' => 'required|integer|exists:countries,id']);

    //     $estados = State::where('country_id', $request->country_id)
    //         ->where('is_active', true)
    //         ->orderBy('name')
    //         ->get(['id', 'name', 'code']);

    //     return response()->json($estados);
    // }

    public function ciudades(Request $request)
    {
        $request->validate([
            'country_id' => 'required|integer|exists:countries,id_countries',
        ]);

        $query = City::where('country_id', $request->country_id)
            ->where('is_active', true);

        // if ($request->filled('state_id')) {
        //     $query->where('state_id', $request->state_id);
        // }

        $ciudades = $query->orderBy('name')
            ->get(['id_cities', 'name'])
            ->map(function ($city) {
                return [
                    'id' => $city->id_cities,
                    'name' => $city->name
                ];
            });

        return response()->json($ciudades);
    }
}
