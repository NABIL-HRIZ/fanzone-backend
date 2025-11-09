<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/contacts",
     *     summary="Récupérer tous les messages de contact",
     *     tags={"Contact"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des messages récupérée avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                 @OA\Property(property="message", type="string", example="Bonjour, je veux plus d'infos."),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-09T15:30:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-09T15:30:00Z")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $contacts = Contact::all();
        return response()->json($contacts);
    }

    /**
     * @OA\Post(
     *     path="/api/contact",
     *     summary="Envoyer un message de contact",
     *     tags={"Contact"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","message"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="message", type="string", example="Bonjour, je veux plus d'infos.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message enregistré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Message enregistré !"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="message", type="string", example="Bonjour, je veux plus d'infos."),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-09T15:30:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-09T15:30:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Erreur de validation")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message enregistré !',
            'data' => $contact
        ]);
    }
}
