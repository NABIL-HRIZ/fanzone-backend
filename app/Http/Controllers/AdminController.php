<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
    use App\Models\Evenement;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    //  Get all users
  public function getAllUsers()
{
    $users = User::with('roles')->get();

    $userss = $users->map(function ($user) {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->roles->pluck('name')->join(', ') 
        ];
    });

    return response()->json($userss, 200);
}

    //  Create a new user
    
    public function storeUser(Request $request){

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

        return response()->json([
            'message'=>'un nouveau fan enregister',
            'user'=>$user,
            'role'=>$roles
        ]);

    }


    //  Update user
   
 
    public function updateUser(Request $request, $id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

  
    $validated=$request->validate([
        'first_name' => 'sometimes|string|max:255',
        'last_name'  => 'sometimes|string|max:255',
        'email'      => 'sometimes|email|unique:users,email,' . $id,
        'phone'      => 'sometimes|string|max:10',
        'password'   => 'sometimes|string|min:8|confirmed',
    ]);

    
    if (isset($validated['password'])) {
        $validated['password'] = Hash::make($validated['password']);
    }

   
    $user->update($validated);

    return response()->json([
        'message' => 'User updated successfully',
        'user' => $user
    ]);
}

    

    //  Delete user
    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }


    //  Get user detail
    public function getUserDetail($id)
    {
        $user = User::find($id);

        $role=$user->roles->pluck('name');

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            
            'user'=>$user,
            'role'=>$role
            
        ],200);
    }







}