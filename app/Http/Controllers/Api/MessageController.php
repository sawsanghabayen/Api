<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

use function PHPUnit\Framework\isEmpty;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $user =auth('user-api')->user();
   
        if($user->whereHas('chats', function ($query) use($id) {
            $query->where('id', '=', $id);
        })->exists()   )
        $messages= $user->chats()->findOrFail($id)->messages()->with('user')->get();
        else
        
        $messages='Not Found Chat';

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => [
            'messages' => $messages,
             
            ]
        ], Response::HTTP_OK);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator($request->all(), [
            'chat_id' => 'required_if:user_id,==,null|numeric|exists:chats,id',
            'user_id' => 'required|numeric|exists:users,id',
            // 'user_id' => 'required_if:chat_id,==,null|numeric|exists:users,id',
            // 'chat_id' => Rule::requiredIf(function() use ($request){
            //     return !$request->input('user_id');
            //     })|'numeric|exists:chats,id',
         
            'message' => 'required|string|min:3',
          
        ]);

        if (!$validator->fails()) {


            // $messgae = new Ad();
            $user =auth('user-api')->user();
            // $chat_id = $request->post('chat_id')??null;
            $user_id = $request->post('user_id');
            // if($chat_id){
            //     $chat=$user->chats()->where('id',$chat_id)->get();
            // }
            if($user_id){
      
            // else

                $chat=Chat::where('sender_id' ,$user->id )->where('receiver_id' ,$user_id )->orWhere('receiver_id' ,$user->id)->orWhere('sender_id' ,$user_id)->get();
            }
            // dd($chat->isEmpty());

            if($chat->isEmpty()){
                // dd($user_id);
                $chat = new Chat();
                $chat->sender_id = $user->id;
                $chat->receiver_id = $user_id;
                $chat->save();
            }

            // dd( $chat->first()->id);
            // dd(111);

            if ($user->blockedUsers()->where('blocked_user_id', $user_id)->orWhere('user_id', $user->id)->exists()) {

                // dd(222);
                $message='The User Blocked ! You Cant Sent The Message.';
                return response()->json([
                    'status' =>false, 'message' => $message,
                  
                ]);
            }
            else{
                $message = new Message();
                $message->user_id = $user->id;
                $message->chat_id = $chat->first()->id;
                $message->message =$request->get('message');
                $isSaved=$message->save();
                return response()->json([
                    'status' => $isSaved, 'message' => $isSaved ? 'Messsge Sent Successfully' :'Messsge Sent Failed!',
                  
                ], $isSaved ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);
          
            }
        }
        else {
        
            return response()->json(['message' => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
        }

            // $chat->sender_id = auth('user-api')->user()->id;
            // $chat->receiver_id = $user_id;
          
            // $isSaved = $message->save();
         

         
            
      
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Message  $Message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $Message)
    {
        //
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Message  $Message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $Message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $Message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $Message)
    {
        //
    }
}
