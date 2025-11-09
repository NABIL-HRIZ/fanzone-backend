<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     */

    /**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Register a new user",
 *     tags={"Authentification"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"first_name","last_name","email","password","password_confirmation"},
 *             @OA\Property(property="first_name", type="string", example="Nabil"),
 *             @OA\Property(property="last_name", type="string", example="Hariz"),
 *             @OA\Property(property="email", type="string", example="hariz@test.ma"),
 *             @OA\Property(property="phone", type="string", example="0612345678"),
 *             @OA\Property(property="password", type="string", example="password"),
 *             @OA\Property(property="password_confirmation", type="string", example="password")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully"
 *     )
 * )
 */

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'password'   => Hash::make($request->password),
        ]);

 
        $user->syncRoles(['fan']);

        $roles = $user->roles->pluck('name');


        
        $token = $user->createToken('auth_token')->plainTextToken;

        

        event(new Registered($user));

        Auth::login($user);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'access_token' => $token,
            'role'=>$roles
        ]);
    }
}
