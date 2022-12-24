<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends Controller
{

    public function store(Request $request)
    {
        // {{dd(auth('user-api')->user()->name);}}
    $validator = Validator($request->all(), [
        
        // 'name' => 'required|string|min:3',
        // 'mobile' => 'required|numeric|unique:users,mobile|digits:9',
        // 'email' => 'nullable|email',
        'message' => 'required|string|min:3',
    ]);

    if (!$validator->fails()) {
        $contact = new ContactRequest();
        $contact->name = auth('user-api')->user()->name;
        $contact->message = $request->get('message');
        $contact->mobile = auth('user-api')->user()->mobile;
        $contact->email = auth('user-api')->user()->email;
        $contact->user_id = auth('user-api')->id();

        $isSaved = $contact->save();
      
        return response()->json([
            'status' => $isSaved, 'message' => $isSaved ? 'Messsge Sent Successfully' :'Messsge Sent Failed!',
          
        ], $isSaved ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);
    } else {
        
        return response()->json(['message' => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
    }
}
}
