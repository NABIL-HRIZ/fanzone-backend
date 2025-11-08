<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zone;
use App\Models\Matche;
use Illuminate\Support\Facades\Validator;

class ZoneController extends Controller
{
    
    public function index()
    {
       
        $zones = Zone::with('match')->paginate(10);
        return response()->json($zones);
    }

  
    public function show($id)
    {
        $zone = Zone::with('match', 'tickets')->findOrFail($id);
        return response()->json($zone);
    }

   
    public function store(Request $request)
    {
        $validated = $request->validate([
            'match_id'   => 'required|exists:matches,id',
            'name'       => 'required|string|max:255',
            'city'       => 'required|string|max:255',
            'address'    => 'nullable|string|max:255',
            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
            'capacity'   => 'required|integer|min:0',
            'available_seats' => 'nullable|integer|min:0',
            'type'       => 'nullable|in:vip,standard,famille',
            'description'=> 'nullable|string',
            'image'      => 'nullable|string|max:255',
        ]);

        $zone = Zone::create($validated);

        return response()->json($zone, 201);
    }

   
    public function update(Request $request, $id)
    {
        $zone = Zone::findOrFail($id);

        $validated = $request->validate([
            'match_id'   => 'sometimes|exists:matches,id',
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
        ]);

        $zone->update($validated);

        return response()->json($zone);
    }

    
    public function destroy($id)
    {
        $zone = Zone::findOrFail($id);
        $zone->delete();

        return response()->json(['message' => 'Zone supprimée avec succès']);
    }

    
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
