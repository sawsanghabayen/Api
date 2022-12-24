<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Review;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
                'ad_id' => 'required|integer',
                'comment' => 'required|string|min:3',

            ]);
    
            if (!$validator->fails()) 
            {
                $ad = Ad::find($request->get('ad_id'));
                if (!is_null($ad)) {
                // $user = auth('user-api')->user();
                $review = new Review();
            
                $review->comment = $request->get('comment');
                $review->ad_id = $request->get('ad_id');
                $review->comment = $request->get('comment');
                $review->user_id = auth('user-api')->id();

                $isSaved = $review->save();
                
                return response()->json([
                    'status' => $isSaved != null,
                    'message' => $isSaved ? 'Review has been added successfully' : 'Review process failed, try again!',
                ], $isSaved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
            
            } else {
                // dd(11);

                return response()->json([
                    'status' => 'false',
                    'message' => 'Ad not fount',
                ]);
            }
            } else {
                return response()->json(
                    ['message' => $validator->getMessageBag()->first()],
                    Response::HTTP_BAD_REQUEST,
                );
            }
        }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function show(Ad $review)
    {
        if (auth('user-api')->check()) {
            $reviews = Review::where('ad_id', $review->ad_id)->get();
            dd($reviews);
            return response()->json(['status' => true, 'message' => 'Success', 'object' => $reviews]);
        }
    }

  

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function destroy(Review $review)
    {
        //
    }
}
