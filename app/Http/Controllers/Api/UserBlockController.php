<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserBlock;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserBlockController extends Controller
{
    public function getBlockedUsers()
    {
        if (auth('user-api')->check()) {

            $id=auth('user-api')->user()->id;
        $users_blocked = UserBlock::where('user_id' , $id)->get();
       
        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => [
            'users_blocked' => $users_blocked,
             
            ]
        ], Response::HTTP_OK);
    }
    }
}
