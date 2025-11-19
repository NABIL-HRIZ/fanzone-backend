<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function downloadTicket($id)
    {
        $reservation = Reservation::with('fanZone.match')->findOrFail($id);

        $qrCode = QrCode::format('png')->size(200)->generate('reservation_'.$reservation->id);

        $pdf = Pdf::loadView('ticket', compact('reservation', 'qrCode'));

        return $pdf->download('ticket_'.$reservation->id.'.pdf');
    }
}
