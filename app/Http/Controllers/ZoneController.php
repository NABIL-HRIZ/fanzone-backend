<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zone;
use App\Models\Matche;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class ZoneController extends Controller
{


public function getMarocZones(Request $request)
{
    $cacheKey = 'maroc_zones';

    return Cache::remember($cacheKey, 3600, function () {
        return Zone::with('match')
            ->whereHas('match', function ($q) {
                $q->where('team_one_title', 'like', '%Maroc%')
                  ->orWhere('team_two_title', 'like', '%Maroc%');
            })
            ->get();
    });
}

    /**
     * @OA\Get(
     *     path="/api/show-zones",
     *     summary="Afficher toutes les zones (public)",
     *     tags={"Zone"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des zones récupérée avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="match_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Zone VIP"),
     *                 @OA\Property(property="city", type="string", example="Casablanca"),
     *                 @OA\Property(property="capacity", type="integer", example=500),
     *                 @OA\Property(property="available_seats", type="integer", example=450),
     *                 @OA\Property(property="type", type="string", example="vip"),
     *                 @OA\Property(property="description", type="string", example="Zone avec meilleur confort"),
     *                 @OA\Property(property="image", type="string", example="zone_vip.png")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $zones = Zone::with('match')->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($zones);
    }

    /**
     * @OA\Get(
     *     path="/api/zone-details/{id}",
     *     summary="Afficher le détail d'une zone",
     *     tags={"Zone"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la zone",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Zone récupérée avec succès"),
     *     @OA\Response(response=404, description="Zone non trouvée")
     * )
     */
    public function show($id)
    {
        $zone = Zone::with('match', 'tickets')->findOrFail($id);
        return response()->json($zone);
    }

    /**
     * @OA\Post(
     *     path="/api/add-zone",
     *     summary="Créer une nouvelle zone (admin seulement)",
     *     tags={"Zone"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"match_id","name","city","capacity"},
     *             @OA\Property(property="matche_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Zone VIP"),
     *             @OA\Property(property="city", type="string", example="Casablanca"),
     *             @OA\Property(property="address", type="string", example="Stade National"),
     *             @OA\Property(property="latitude", type="number", format="float", example=33.5731),
     *             @OA\Property(property="longitude", type="number", format="float", example=-7.5898),
     *             @OA\Property(property="capacity", type="integer", example=500),
     *             @OA\Property(property="available_seats", type="integer", example=450),
     *             @OA\Property(property="type", type="string", example="vip"),
     *             @OA\Property(property="description", type="string", example="Zone avec meilleur confort"),
     *             @OA\Property(property="image", type="string", example="zone_vip.png")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Zone créée avec succès"),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'matche_id'   => 'required|exists:matches,id',
            'name'       => 'required|string|max:255',
            'city'       => 'required|string|max:255',
            'address'    => 'nullable|string|max:255',
            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
            'capacity'   => 'required|integer|min:0',
            'available_seats' => 'nullable|integer|min:0',
           'price' => 'required|numeric|min:0',
            'type'       => 'nullable|in:vip,standard,famille',
            'description'=> 'nullable|string',
            'image'      => 'nullable|string|max:255',

        ]);

        $zone = Zone::create($validated);

          Cache::forget('maroc_zones');
        $zone->load('match');

        return response()->json($zone, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/zone/{id}",
     *     summary="Mettre à jour une zone (admin seulement)",
     *     tags={"Zone"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la zone à mettre à jour",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="match_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Zone VIP"),
     *             @OA\Property(property="city", type="string", example="Casablanca"),
     *             @OA\Property(property="address", type="string", example="Stade National"),
     *             @OA\Property(property="latitude", type="number", format="float", example=33.5731),
     *             @OA\Property(property="longitude", type="number", format="float", example=-7.5898),
     *             @OA\Property(property="capacity", type="integer", example=500),
     *             @OA\Property(property="available_seats", type="integer", example=450),
     *             @OA\Property(property="type", type="string", example="vip"),
     *             @OA\Property(property="description", type="string", example="Zone avec meilleur confort"),
     *             @OA\Property(property="image", type="string", example="zone_vip.png")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Zone mise à jour avec succès"),
     *     @OA\Response(response=404, description="Zone non trouvée"),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function update(Request $request, $id)
    {
        $zone = Zone::findOrFail($id);

        $validated = $request->validate([
            'matche_id'   => 'sometimes|exists:matches,id',
            'name'       => 'sometimes|string|max:255',
            'city'       => 'sometimes|string|max:255',
            'address'    => 'nullable|string|max:255',
            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
            'capacity'   => 'sometimes|integer|min:0',
            'available_seats' => 'nullable|integer|min:0',
            'type'       => 'nullable|in:vip,standard,famille',
            'description'=> 'nullable|string',
            'image'      => 'nullable|string|max:255',
           'price' => 'sometimes|numeric|min:0',

        ]);

        $zone->update($validated);

           Cache::forget('maroc_zones');


        return response()->json($zone);
    }

    /**
     * @OA\Delete(
     *     path="/api/zone/{id}",
     *     summary="Supprimer une zone (admin seulement)",
     *     tags={"Zone"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la zone à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Zone supprimée avec succès"),
     *     @OA\Response(response=404, description="Zone non trouvée"),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function destroy($id)
    {
        $zone = Zone::findOrFail($id);
        $zone->delete();

        return response()->json(['message' => 'Zone supprimée avec succès']);
    }

    /**
     * @OA\Get(
     *     path="/api/zone/search",
     *     summary="Rechercher des zones (public)",
     *     tags={"Zone"},
     *     @OA\Parameter(name="name", in="query", description="Nom de la zone", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="city", in="query", description="Ville de la zone", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="type", in="query", description="Type de zone", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Résultat de la recherche")
     * )
     */
    public function search(Request $request)
    {
        $query = Zone::with('match');

        if ($request->filled('name')) {
            $query->where('name', 'like', "%{$request->name}%");
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', "%{$request->city}%");
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $zones = $query->paginate(10);

        return response()->json($zones);
    }
}
