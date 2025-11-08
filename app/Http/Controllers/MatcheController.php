<?php

namespace App\Http\Controllers;

use App\Models\Matche;
use Illuminate\Http\Request;

class MatcheController extends Controller
{
    
    public function index()
    {
        $matches = Matche::with('zones') 
                         ->orderBy('match_date', 'asc') 
                         ->paginate(10);

        return response()->json($matches);
    }

    
    public function show($id)
    {
        $match = Matche::with('zones')->findOrFail($id);
        return response()->json($match);
    }


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
            'message'=>'match succes created',
            '$match'=>$match
        ],201);
    }

    
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
            'message'=>"match success updated",
            'matche'=>$match
        ]);
    }

   
    public function destroy($id)
    {
        $match = Matche::findOrFail($id);
        $match->delete();

        return response()->json(['message' => 'Match supprimé avec succès']);
    }

    
   
   
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
 