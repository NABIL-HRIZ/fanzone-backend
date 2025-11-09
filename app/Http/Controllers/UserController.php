<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * @group User
 * APIs for authenticated user profile
 */
class UserController extends Controller
{
  /**
 * @OA\Get(
 *     path="/api/profile",
 *     summary="Get authenticated user's profile",
 *     tags={"User"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="User profile retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="first_name", type="string", example="Nabil"),
 *             @OA\Property(property="last_name", type="string", example="Hariz"),
 *             @OA\Property(property="email", type="string", example="nabil@test.ma"),
 *             @OA\Property(property="phone", type="string", example="0612345678")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé"
 *     )
 * )
 */
    public function showProfile()
    {
        $user = Auth::user();
        return response()->json($user, 200);
    }

   /**
 * Update authenticated user's profile
 *
 * @OA\Put(
 *     path="/api/profile",
 *     summary="Update authenticated user's profile",
 *     tags={"User"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\JsonContent(
 *             @OA\Property(property="first_name", type="string", example="John"),
 *             @OA\Property(property="last_name", type="string", example="Doe"),
 *             @OA\Property(property="email", type="string", example="john@example.com"),
 *             @OA\Property(property="phone", type="string", example="0612345678"),
 *             @OA\Property(property="password", type="string", example="newpassword123"),
 *             @OA\Property(property="password_confirmation", type="string", example="newpassword123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Profile updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Profile updated successfully"),
 *             @OA\Property(
 *                 property="user",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="first_name", type="string", example="John"),
 *                 @OA\Property(property="last_name", type="string", example="Doe"),
 *                 @OA\Property(property="email", type="string", example="john@example.com"),
 *                 @OA\Property(property="phone", type="string", example="0612345678")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 */
public function updateProfile(Request $request)
{
    $user = Auth::user();

    $validated = $request->validate([
        'first_name' => 'sometimes|string|max:255',
        'last_name'  => 'sometimes|string|max:255',
        'email'      => 'sometimes|email|unique:users,email,' . $user->id,
        'phone'      => 'nullable|string|max:10',
        'password'   => 'nullable|string|min:8|confirmed',
    ]);

    if (isset($validated['password'])) {
        $validated['password'] = Hash::make($validated['password']);
    }

    $user->update($validated);

    return response()->json([
        'message' => 'Profile updated successfully',
        'user' => $user
    ], 200);
}

}

