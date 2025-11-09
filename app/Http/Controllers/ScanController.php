<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scan;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/scans",
     *     summary="Liste de tous les scans (admin)",
     *     tags={"Scan"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Liste des scans récupérée avec succès")
     * )
     */
    public function index()
    {
        $scans = Scan::with(['agent', 'ticket.fanZone.match'])->latest()->get();
        return response()->json($scans);
    }

    /**
     * @OA\Post(
     *     path="/api/scans",
     *     summary="Créer un nouveau scan",
     *     tags={"Scan"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ticket_id","scan_status"},
     *             @OA\Property(property="ticket_id", type="integer", example=1),
     *             @OA\Property(property="scan_status", type="string", enum={"valid","invalid"}, example="valid")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Scan enregistré avec succès"),
     *     @OA\Response(response=400, description="Erreur de validation")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:reservation_tickets,id',
            'scan_status' => 'required|in:valid,invalid',
        ]);

        $scan = Scan::create([
            'agent_id' => Auth::id(),
            'ticket_id' => $request->ticket_id,
            'scan_time' => now(),
            'scan_status' => $request->scan_status,
        ]);

        return response()->json([
            'message' => 'Scan enregistré avec succès.',
            'data' => $scan
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/scans/{id}",
     *     summary="Afficher un scan spécifique",
     *     tags={"Scan"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du scan",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Scan récupéré avec succès"),
     *     @OA\Response(response=404, description="Scan non trouvé")
     * )
     */
    public function show($id)
    {
        $scan = Scan::with(['agent', 'ticket.fanZone.match'])->findOrFail($id);
        return response()->json($scan);
    }

    /**
     * @OA\Put(
     *     path="/api/scans/{id}",
     *     summary="Mettre à jour le statut d'un scan",
     *     tags={"Scan"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du scan",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="scan_status", type="string", enum={"valid","invalid"}, example="invalid")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Scan mis à jour avec succès"),
     *     @OA\Response(response=404, description="Scan non trouvé")
     * )
     */
    public function update(Request $request, $id)
    {
        $scan = Scan::findOrFail($id);

        $request->validate([
            'scan_status' => 'in:valid,invalid',
        ]);

        $scan->update($request->only('scan_status'));

        return response()->json([
            'message' => 'Scan mis à jour avec succès.',
            'data' => $scan
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/scans/{id}",
     *     summary="Supprimer un scan",
     *     tags={"Scan"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du scan",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Scan supprimé avec succès"),
     *     @OA\Response(response=404, description="Scan non trouvé")
     * )
     */
    public function destroy($id)
    {
        $scan = Scan::findOrFail($id);
        $scan->delete();

        return response()->json(['message' => 'Scan supprimé avec succès.']);
    }

    /**
     * @OA\Get(
     *     path="/api/scans/search",
     *     summary="Rechercher des scans par ticket, agent ou statut",
     *     tags={"Scan"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="ticket_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="agent_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="scan_status", in="query", @OA\Schema(type="string", enum={"valid","invalid"})),
     *     @OA\Response(response=200, description="Résultat de la recherche")
     * )
     */
    public function search(Request $request)
    {
        $query = Scan::query();

        if ($request->ticket_id) $query->where('ticket_id', $request->ticket_id);
        if ($request->agent_id) $query->where('agent_id', $request->agent_id);
        if ($request->scan_status) $query->where('scan_status', $request->scan_status);

        $scans = $query->with(['agent', 'ticket.fanZone.match'])->get();

        return response()->json($scans);
    }
}
