<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scan;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{
    
    public function index()
    {
        $scans = Scan::with(['agent', 'ticket.fanZone.match'])->latest()->get();
        return response()->json($scans);
    }

   
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

   
    public function show($id)
    {
        $scan = Scan::with(['agent', 'ticket.fanZone.match'])->findOrFail($id);
        return response()->json($scan);
    }

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

  
    public function destroy($id)
    {
        $scan = Scan::findOrFail($id);
        $scan->delete();

        return response()->json(['message' => 'Scan supprimé avec succès.']);
    }

    
    public function search(Request $request)
    {
        $query = Scan::query();

        if ($request->ticket_id) {
            $query->where('ticket_id', $request->ticket_id);
        }

        if ($request->agent_id) {
            $query->where('agent_id', $request->agent_id);
        }

        if ($request->scan_status) {
            $query->where('scan_status', $request->scan_status);
        }

        $scans = $query->with(['agent', 'ticket.fanZone.match'])->get();

        return response()->json($scans);
    }
}
