<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\User;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    // public function index()
    // {
    //     if (auth('user-api')->check()) {
    //     $users = User::all();
       
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Success',
    //         'data' => [
    //         'categories' => $categories,
             
    //         ]
    //     ], Response::HTTP_OK);
    // }
    // }

    public function show(User $user)
    {
        if (auth('user-api')->check()) {
            $users = User::with('ads')->where('id', $user->id)->get();
            
            return response()->json(['status' => true, 'message' => 'Success', 'object' => $users]);
        }

    }
}