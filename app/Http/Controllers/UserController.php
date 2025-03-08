<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 

class UserController extends Controller
{
    public function index()
    {
        $users = User::all(); 
        return view('user.index', compact('users')); 
    }

    public function update(Request $request, $id)
    {   
        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();
    
        return response()->json(['success' => 'User role updated successfully!']);
    }    
    
}
