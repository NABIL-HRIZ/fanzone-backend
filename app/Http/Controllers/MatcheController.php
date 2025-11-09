<?php

namespace App\Http\Controllers;

use App\Models\Matche;
use Illuminate\Http\Request;

class MatcheController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/show-matches",
     *     summary="Afficher tous les matchs (public)",
     *     tags={"Match"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des matchs récupérée avec succès",
     *         @OA\JsonContent(type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="team_one_title", type="string", example="Team A"),
     *                 @OA\Property(property="team_two_title", type="string", example="Team B"),
     *                 @OA\Property(property="match_date", type="string", format="date", example="2025-12-01"),
     *                 @OA\Property(property="stadium", type="string", example="Stade National")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $matches = Matche::with('zones') 
                         ->orderBy('match_date', 'asc') 
                         ->paginate(10);

        return response()->json($matches);
    }

    /**
     * @OA\Get(
     *     path="/api/match-details/{id}",
     *     summary="Afficher le détail d'un match",
     *     tags={"Match"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du match",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Match récupéré avec succès"),
     *     @OA\Response(response=404, description="Match non trouvé"),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function show($id)
    {
        $match = Matche::with('zones')->findOrFail($id);
        return response()->json($match);
    }

    /**
     * @OA\Post(
     *     path="/api/add-match",
     *     summary="Créer un nouveau match",
     *     tags={"Match"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"team_one_title","team_two_title","match_date"},
     *             @OA\Property(property="team_one_title", type="string", example="Team A"),
     *             @OA\Property(property="team_one_image", type="string", example="team_a.png"),
     *             @OA\Property(property="team_two_title", type="string", example="Team B"),
     *             @OA\Property(property="team_two_image", type="string", example="team_b.png"),
     *             @OA\Property(property="match_date", type="string", format="date", example="2025-12-01"),
     *             @OA\Property(property="stadium", type="string", example="Stade National"),
     *             @OA\Property(property="description", type="string", example="Match important")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Match créé avec succès"),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_one_title' => 'required|string|max:255',
            'team_one_image' => 'nullable|string|max:255',
            'team_two_title' => 'required|string|max:255',
            'team_two_image' => 'nullable|string|max:255',
            'match_date'     => 'required|date',
            'stadium'        => 'nullable|string|max:255',
            'description'    => 'nullable|string',
        ]);

        $match = Matche::create($validated);

        return response()->json([
            'message'=>'Match créé avec succès',
            'match'=>$match
        ],201);
    }

    /**
     * @OA\Put(
     *     path="/api/match/{id}",
     *     summary="Mettre à jour un match",
     *     tags={"Match"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du match à mettre à jour",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="team_one_title", type="string", example="Team A"),
     *             @OA\Property(property="team_one_image", type="string", example="team_a.png"),
     *             @OA\Property(property="team_two_title", type="string", example="Team B"),
     *             @OA\Property(property="team_two_image", type="string", example="team_b.png"),
     *             @OA\Property(property="match_date", type="string", format="date", example="2025-12-01"),
     *             @OA\Property(property="stadium", type="string", example="Stade National"),
     *             @OA\Property(property="description", type="string", example="Match important")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Match mis à jour avec succès"),
     *     @OA\Response(response=404, description="Match non trouvé"),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function update(Request $request, $id)
    {
        $match = Matche::findOrFail($id);

        $validated = $request->validate([
            'team_one_title' => 'sometimes|required|string|max:255',
            'team_one_image' => 'nullable|string|max:255',
            'team_two_title' => 'sometimes|required|string|max:255',
            'team_two_image' => 'nullable|string|max:255',
            'match_date'     => 'sometimes|required|date',
            'stadium'        => 'nullable|string|max:255',
            'description'    => 'nullable|string',
        ]);

        $match->update($validated);

        return response()->json([
            'message'=>"Match mis à jour avec succès",
            'match'=>$match
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/match/{id}",
     *     summary="Supprimer un match",
     *     tags={"Match"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du match à supprimer",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Match supprimé avec succès"),
     *     @OA\Response(response=404, description="Match non trouvé"),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function destroy($id)
    {
        $match = Matche::findOrFail($id);
        $match->delete();

        return response()->json(['message' => 'Match supprimé avec succès']);
    }

    /**
     * @OA\Get(
     *     path="/api/match/search",
     *     summary="Rechercher des matchs",
     *     tags={"Match"},
     *     @OA\Parameter(name="team", in="query", description="Nom de l'équipe", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="date", in="query", description="Date du match", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="city", in="query", description="Ville", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Résultat de la recherche"),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function search(Request $request)
    {
        $query = Matche::with('zones');

        if ($request->filled('team')) {
            $query->where(function($q) use ($request) {
                $q->where('team_one_title', 'like', '%'.$request->team.'%')
                  ->orWhere('team_two_title', 'like', '%'.$request->team.'%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('match_date', $request->date);
        }

        if ($request->filled('city')) {
            $query->whereHas('zones', function($q) use ($request) {
                $q->where('city', 'like', '%'.$request->city.'%');
            });
        }

        $matches = $query->orderBy('match_date', 'asc')->paginate(10);

        return response()->json($matches);
    }
}
