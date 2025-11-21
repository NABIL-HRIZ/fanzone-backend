<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function downloadTicket($id)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $reservation = Reservation::with('fanZone.match')->findOrFail($id);

        if ($reservation->user_id !== $user->id) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

    $qrCode = QrCode::format('svg')->size(200)->generate('reservation_' . $reservation->id);

        $pdf = Pdf::loadView('ticket', [
            'reservation' => $reservation,
            'qrCode' => $qrCode
        ]);

        return $pdf->download('ticket_' . $reservation->id . '.pdf');
    }
}
