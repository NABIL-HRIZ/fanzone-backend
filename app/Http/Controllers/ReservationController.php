<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Zone;
use App\Jobs\SendTicketJob;
use App\Models\User;
use Illuminate\Support\Facades\Event;
USE App\Events\ZoneSeatsUpdated;
class ReservationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/reservations",
     *     summary="Liste de toutes les réservations (admin)",
     *     tags={"Reservation"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Liste récupérée avec succès")
     * )
     */
    public function index()
    {
        $reservations = Reservation::with(['user', 'fanZone'])
            ->orderBy('reservation_date', 'desc')
            ->get();

        return response()->json($reservations);
    }

    /**
     * @OA\Get(
     *     path="/api/reservations/{id}",
     *     summary="Afficher une réservation",
     *     tags={"Reservation"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la réservation",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Réservation récupérée avec succès"),
     *     @OA\Response(response=404, description="Réservation non trouvée")
     * )
     */
    public function show($id)
    {
        $reservation = Reservation::with(['user', 'fanZone'])->findOrFail($id);
        return response()->json($reservation);
    }

    /**
     * @OA\Post(
     *     path="/api/reservations",
     *     summary="Créer une nouvelle réservation",
     *     tags={"Reservation"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id","zone_id","number_of_tickets"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="zone_id", type="integer", example=1),
     *             @OA\Property(property="number_of_tickets", type="integer", example=2),
     *             @OA\Property(property="total_price", type="number", example=200.5),
     *             @OA\Property(property="payment_status", type="string", example="unpaid"),
     *             @OA\Property(property="reservation_date", type="string", format="date-time", example="2025-12-01T12:00:00Z")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Réservation créée avec succès"),
     *     @OA\Response(response=400, description="Erreur de validation")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'zone_id' => 'required|exists:zones,id',
            'number_of_tickets' => 'required|integer|min:1',
            'total_price' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|in:unpaid,paid,simulated',
            'qr_code_path' => 'nullable|string|max:255',
            'ticket_pdf_path' => 'nullable|string|max:255',
            'reservation_date' => 'nullable|date',
            'stripe_payment_intent_id' => 'nullable|string|max:255',
            'stripe_session_id' => 'nullable|string|max:255',
        ]);

        $reservation = Reservation::create($validated);

        $user = User::find($validated['user_id']);
        if ($user) {
           
            SendTicketJob::dispatch($user->email, $reservation);
        }


        return response()->json($reservation, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/reservations/{id}",
     *     summary="Mettre à jour une réservation",
     *     tags={"Reservation"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="number_of_tickets", type="integer", example=3),
     *             @OA\Property(property="total_price", type="number", example=300),
     *             @OA\Property(property="payment_status", type="string", example="paid")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Réservation mise à jour avec succès"),
     *     @OA\Response(response=404, description="Réservation non trouvée")
     * )
     */
    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'zone_id' => 'sometimes|exists:zones,id',
            'number_of_tickets' => 'sometimes|integer|min:1',
            'total_price' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|in:unpaid,paid,simulated',
        ]);

        $reservation->update($validated);
        return response()->json($reservation);
    }

    /**
     * @OA\Delete(
     *     path="/api/reservations/{id}",
     *     summary="Supprimer une réservation",
     *     tags={"Reservation"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Réservation supprimée avec succès"),
     *     @OA\Response(response=404, description="Réservation non trouvée")
     * )
     */
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();
        return response()->json(['message' => 'Réservation supprimée avec succès']);
    }

    /**
     * @OA\Get(
     *     path="/api/reservations/search",
     *     summary="Rechercher des réservations",
     *     tags={"Reservation"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="user_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="zone_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="payment_status", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Résultat de la recherche")
     * )
     */
    public function search(Request $request)
    {
        $query = Reservation::with(['user', 'fanZone']);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('zone_id')) $query->where('zone_id', $request->zone_id);
        if ($request->filled('payment_status')) $query->where('payment_status', $request->payment_status);

        return response()->json($query->orderBy('reservation_date','desc')->paginate(10));
    }

    /**
     * @OA\Get(
     *     path="/api/reservations/my",
     *     summary="Afficher mes réservations (user)",
     *     tags={"Reservation"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Liste des réservations de l'utilisateur")
     * )
     */
    public function myReservations(Request $request)
    {
        $user = $request->user();
        $reservations = Reservation::with(['fanZone.match'])
            ->where('user_id', $user->id)
            ->orderBy('reservation_date', 'desc')
            ->paginate(10);
        return response()->json($reservations);
    }

    /**
     * @OA\Post(
     *     path="/api/stripe/webhook",
     *     summary="Webhook Stripe pour créer des réservations après paiement",
     *     tags={"Reservation"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=200, description="Webhook reçu avec succès"),
     *     @OA\Response(response=400, description="Erreur Stripe Webhook")
     * )
     */
   
 // Stripe Webhook
   
public function handleWebHook(Request $request)
{
    $payload = $request->getContent();
    $sig_header = $request->header('stripe-signature');
    $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $endpoint_secret
        );
    } catch (\Exception $e) {
        return response('Invalid signature', 400);
    }

    if ($event->type === 'checkout.session.completed') {
        $session = $event->data->object;

        
        $user_id = $session->metadata->user_id ?? null;
        $zone_id = $session->metadata->zone_id ?? null;
        $quantity = $session->metadata->quantity ?? 1;

       

        
        $reservation = Reservation::create([
            'user_id' => $user_id,
            'zone_id' => $zone_id,
            'number_of_tickets' => $quantity,
            'total_price' => $session->amount_total / 100,
            'payment_status' => 'paid',
            'reservation_date' => now(),
            'stripe_session_id' => $session->id,
            'stripe_payment_intent_id' => $session->payment_intent,
        ]);

   
        $zone = Zone::find($zone_id);
   event(new ZoneSeatsUpdated($zone, $quantity));
        $zone->save();
    }

    $user = User::find($user_id);

if ($user) {
    SendTicketJob::dispatch($user->email, $reservation);
}


    return response('Webhook handled', 200);
}

}




