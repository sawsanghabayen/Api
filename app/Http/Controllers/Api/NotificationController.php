<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       
        $notifications = $request->user()->notifications;
        $notifications->markAsRead();

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => [
            'notifications' => $notifications,
             
            ]
        ], Response::HTTP_OK);
    }
}
