<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scan;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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
        $scans = Scan::with(['agent', 'reservation.fanZone.match'])->latest()->get();
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
     *             required={"reservation_id","scan_status"},
     *             @OA\Property(property="reservation_id", type="integer", example=1),
     *             @OA\Property(property="scan_status", type="string", enum={"valid","invalid"}, example="valid")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Scan enregistré avec succès"),
     *     @OA\Response(response=400, description="Erreur de validation")
     * )
     */
public function store(Request $request)
{
    try {
       
       $raw = $request->input('ticket_id');

        $ticketId = filter_var($raw, FILTER_SANITIZE_NUMBER_INT);

if (!$ticketId) {
    return response()->json(['message' => 'Identifiant de ticket invalide'], 422);
}

        $validator = Validator::make(['ticket_id' => $ticketId], [
            'ticket_id' => 'required|integer|exists:reservation_tickets,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation échouée',
                'errors' => $validator->errors()
            ], 422);
        }

        $reservation = Reservation::with('fanZone.match')->find($ticketId);

        if (!$reservation) {
            return response()->json(['message' => 'Ticket introuvable'], 404);
        }

        $alreadyScanned = Scan::where('ticket_id', $ticketId)->exists();
        if ($alreadyScanned) {
            return response()->json(['message' => 'Ticket déjà scanné'], 400);
        }

        $scan = Scan::create([
            'agent_id' => Auth::id(),
            'ticket_id' => $ticketId,
            'scan_time' => now(),
            'scan_status' => 'valid'
        ]);

        return response()->json([
            'message' => 'Ticket validé avec succès',
            'data' => $scan
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erreur serveur',
            'error' => $e->getMessage()
        ], 500);
    }
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
        $scan = Scan::with(['agent', 'reservation.fanZone.match'])->findOrFail($id);
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
     *     summary="Rechercher des scans par reservation, agent ou statut",
     *     tags={"Scan"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="reservation_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="agent_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="scan_status", in="query", @OA\Schema(type="string", enum={"valid","invalid"})),
     *     @OA\Response(response=200, description="Résultat de la recherche")
     * )
     */
    public function search(Request $request)
    {
        $query = Scan::query();

        if ($request->reservation_id) $query->where('reservation_id', $request->reservation_id);
        if ($request->agent_id) $query->where('agent_id', $request->agent_id);
        if ($request->scan_status) $query->where('scan_status', $request->scan_status);

        $scans = $query->with(['agent', 'reservation.fanZone.match'])->get();

        return response()->json($scans);
    }
}
