<?php

namespace App\Http\Controllers;

use App\Models\UserProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProductController extends Controller
{
    public function index()
    {
        $userProducts = UserProduct::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('user-product.index', compact('userProducts'));
    }
}
