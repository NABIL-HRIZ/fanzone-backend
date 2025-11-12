<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Jobs\SendWelcomeEmailJob;

class AdminController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/show-fans",
     *     summary="Afficher tous les utilisateurs avec leurs rôles",
     *     description="Retourne la liste des utilisateurs avec leurs rôles associés (accessible uniquement aux administrateurs).",
     *     tags={"CRUD _Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des utilisateurs récupérée avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="first_name", type="string", example="Nabil"),
     *                 @OA\Property(property="last_name", type="string", example="Hariz"),
     *                 @OA\Property(property="email", type="string", example="nabil@test.ma"),
     *                 @OA\Property(property="phone", type="string", example="0612345678"),
     *                 @OA\Property(
     *                     property="roles",
     *                     type="array",
     *                     @OA\Items(type="string", example="admin")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé — Token ou rôle invalide"
     *     )
     * )
     */
    public function getAllUsers()
    {
        $users = User::with('roles')->get();

        $data = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'roles' => $user->roles->pluck('name'),
            ];
        });

        return response()->json($data, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/add-fan",
     *     summary="Créer un nouveau fan",
     *     tags={"CRUD _Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","last_name","email","password","password_confirmation"},
     *             @OA\Property(property="first_name", type="string", example="Nabil"),
     *             @OA\Property(property="last_name", type="string", example="Hariz"),
     *             @OA\Property(property="email", type="string", example="nabil@test.ma"),
     *             @OA\Property(property="phone", type="string", example="0612345678"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Fan créé avec succès"),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'first_name'=>'required|string|max:255',
            'last_name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email',
            'phone'=>'nullable|string|max:10',
            'password'=>'required|string|min:8|confirmed'
        ]);

        $user=User::create([
            'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'password'=>Hash::make($request->password)
        ]);

        $user->syncRoles(['fan']);
        $roles=$user->roles->pluck('name');


              SendWelcomeEmailJob::dispatch($user);

        return response()->json([
            'message'=>'Un nouveau fan enregistré',
            'user'=>$user,
            'role'=>$roles
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/fan/{id}",
     *     summary="Mettre à jour un fan",
     *     tags={"CRUD _Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du fan à mettre à jour",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string", example="Nabil"),
     *             @OA\Property(property="last_name", type="string", example="Hariz"),
     *             @OA\Property(property="email", type="string", example="nabil@test.ma"),
     *             @OA\Property(property="phone", type="string", example="0612345678"),
     *             @OA\Property(property="password", type="string", example="newpassword123"),
     *             @OA\Property(property="password_confirmation", type="string", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Fan mis à jour avec succès"),
     *     @OA\Response(response=404, description="Fan non trouvé"),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message'=>'User not found'],404);

        $validated=$request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name'  => 'sometimes|string|max:255',
            'email'      => 'sometimes|email|unique:users,email,' . $id,
            'phone'      => 'sometimes|string|max:10',
            'password'   => 'sometimes|string|min:8|confirmed',
        ]);

        if (isset($validated['password'])) $validated['password'] = Hash::make($validated['password']);

        $user->update($validated);

        return response()->json(['message'=>'User updated successfully','user'=>$user],200);
    }

    /**
     * @OA\Delete(
     *     path="/api/fan/{id}",
     *     summary="Supprimer un fan",
     *     tags={"CRUD _Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du fan à supprimer",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Fan supprimé avec succès"),
     *     @OA\Response(response=404, description="Fan non trouvé"),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message'=>'User not found'],404);
        $user->delete();
        return response()->json(['message'=>'User deleted successfully'],200);
    }

    /**
     * @OA\Get(
     *     path="/api/fan-details/{id}",
     *     summary="Afficher le détail d'un fan",
     *     tags={"CRUD _Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du fan",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Détails récupérés avec succès"),
     *     @OA\Response(response=404, description="Fan non trouvé"),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function getUserDetail($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message'=>'User not found'],404);

        $role = $user->roles->pluck('name');

        return response()->json(['user'=>$user,'role'=>$role],200);
    }
}
