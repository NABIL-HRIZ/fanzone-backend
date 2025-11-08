<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Zone;

class ReservationController extends Controller
{
    
    public function index()
    {
        $reservations = Reservation::with(['user', 'fanZone'])
            ->orderBy('reservation_date', 'desc')
            ->paginate(10);

        return response()->json($reservations);
    }

   
    public function show($id)
    {
        $reservation = Reservation::with(['user', 'fanZone'])->findOrFail($id);
        return response()->json($reservation);
    }

    
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

        return response()->json($reservation, 201);
    }

    
    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'zone_id' => 'sometimes|exists:zones,id',
            'number_of_tickets' => 'sometimes|integer|min:1',
            'total_price' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|in:unpaid,paid,simulated',
            'qr_code_path' => 'nullable|string|max:255',
            'ticket_pdf_path' => 'nullable|string|max:255',
            'reservation_date' => 'nullable|date',
            'stripe_payment_intent_id' => 'nullable|string|max:255',
            'stripe_session_id' => 'nullable|string|max:255',
        ]);

        $reservation->update($validated);

        return response()->json($reservation);
    }

   
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return response()->json(['message' => 'Réservation supprimée avec succès']);
    }

    
    public function search(Request $request)
    {
        $query = Reservation::with(['user', 'fanZone']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $reservations = $query->orderBy('reservation_date', 'desc')->paginate(10);

        return response()->json($reservations);
    }


    // reservations pour chaque user

    public function myReservations(Request $request)
{
    $user = $request->user();

    $reservations = Reservation::with(['fanZone.match'])
        ->where('user_id', $user->id)
        ->orderBy('reservation_date', 'desc')
        ->paginate(10);

    return response()->json($reservations);
}

}
