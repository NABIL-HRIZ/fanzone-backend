<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscribe;

class SubscribeController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/add-email",
     *     summary="Ajouter un email à la liste de souscription",
     *     tags={"Subscribe"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email enregistré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email enregistré !")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Erreur de validation")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:subscribers,email',
        ]);

        Subscribe::create([
            'email' => $request->email
        ]);

        return response()->json(['message' => 'Email enregistré !']);
    }

    /**
     * @OA\Get(
     *     path="/api/show-emails",
     *     summary="Récupérer tous les emails souscrits",
     *     tags={"Subscribe"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des emails récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="emails",
     *                 type="array",
     *                 @OA\Items(type="string", example="user@example.com")
     *             )
     *         )
     *     )
     * )
     */
    public function getEmails()
    {
        $emails = Subscribe::pluck('email');
        return response()->json(['emails' => $emails]);
    }
}
