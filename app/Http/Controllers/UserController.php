<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Import the User model

class UserController extends Controller
{
    public function index()
    {
        $users = User::all(); // Fetch all users
        return view('pages.user', compact('users')); // Pass users to the view
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();
    
        return response()->json(['success' => 'User role updated successfully!']);
    }    
    
}
