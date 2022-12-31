<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

    //User Profile

    public function show(User $user)
    {
        
        if (auth('user-api')->check()) {

            if (auth('user-api')->user()->blockedUsers()->where('blocked_user_id', $user->id)->orWhere('user_id', auth('user-api')->user()->id)->exists()) {

                // dd(222);
                $message='The User Blocked ! You Cant Show The Profile.';
                return response()->json([
                    'status' =>false, 'message' => $message,
                  
                ]);
            }
            else{
            $users = User::with('ads')->where('id', $user->id)->get();
            
            return response()->json(['status' => true, 'message' => 'Success', 'object' => $users]);
        }}

    }



    public function block(Request $request){

    $validator = Validator($request->all(), [
        'bloked_user_id' => 'required|integer|exists:users,id',
    ]);

    if (!$validator->fails()) {
        $user = auth('user-api')->user();

        $bloked_user = User::where('id' ,'!=',$user->id)->find($request->get('bloked_user_id'));
        if (!is_null($bloked_user)) {
            $isBlocked = false;
            if (!$user->blockedUsers()->where('blocked_user_id', $bloked_user->id)->exists()) {
                $messsage = 'User has been Blocked successfully';
                $isSaved = $user->blockedUsers()->save($bloked_user);
                $isBlocked= true;
            } else {
                $messsage ='User has been UnBlocked successfully';
                $isSaved = $user->blockedUsers()->detach($bloked_user);
                $isBlocked = false;
            }
            return response()->json([
                'status' => $isSaved != null,
                'blocked' => $isBlocked,
                'message' => $isSaved ? $messsage : 'Favorite process failed, try again!',
            ], $isSaved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
        } else {
            return response()->json([
                'status' => 'false',
                'message' => 'User not found!',
            ]);
        }
    } else {
        return response()->json(array(
            'status' => false,
            'message' => $validator->getMessageBag()->first(),
        ), Response::HTTP_BAD_REQUEST);

    }
}




}