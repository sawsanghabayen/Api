<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Image;
use App\Models\Review;
use App\Models\User;
use App\Notifications\NewAdNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class AdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

       
       if (auth('user-api')->check()) {

        if($request->has('minprice') || $request->has('maxprice') || $request->has('type') ){
            if($request->has('minprice') && $request->has('maxprice')  && $request->has('type')  ){
              
                $ads_filter =Ad::whereBetween('price', [$request->minprice , $request->maxprice ])->where('type' , $request->type)->get();
    
    
            }
        elseif($request->has('minprice') && $request->has('maxprice')  ){
           
            $ads_filter =Ad::whereBetween('price', [$request->minprice,$request->maxprice ])->get();
           
          

        }
       
        elseif($request->has('minprice')){
            
            $ads_filter =Ad::where('price', $request->minprice)->get();


        }

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => [
            'ads' => $ads_filter,
             
            ]
        ], Response::HTTP_OK);
    }
        else{
            $ads = Ad::all();
            // dd($ads);
            return response()->json([
                'status' => true,
                'message' => 'Success',
                'data' => [
                'ads' => $ads,
                 
                ]
            ], Response::HTTP_OK);

        }
         
        }

    }
    public function filterAds(Request $request)
    {
       
          dd($request);
        $validator = Validator($request->all(), [
           
            // 'price' => 'required|numeric|min:1',
           
        ]);

        if (!$validator->fails()) {
        //  $request->price =$request->get('price');
       if (auth('user-api')->check()) {

        $ads_filter=Ad::when($request->price ,function($query ,$value){
            $query->where('price',$value);
        })->get();
       
            return response()->json([
                'status' => true,
                'message' => 'Success',
                'data' => [
                'ads' => $ads_filter,
                 
                ]
            ], Response::HTTP_OK);
        }
    }

    else {
        // dd(111);
        return response()->json(['message' => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
    }

    }

        /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ad  $ad
     * @return \Illuminate\Http\Response
     */
    public function show(Ad $ad)
    {
        
        if (auth('user-api')->check()) {
            $ad = $ad->load(['images']);
            $similarAds = Ad::first()->where('user_id', $ad->user_id)->where('category_id', $ad->category_id)->limit(4)->get();
            $user=User::where('id', $ad->user_id)->get(['name' ,'image' ,'created_at' ,'facebook_url']);
            $ad->setAttribute('similarAds', $similarAds);
            $ad->setAttribute('user', $user);
            // dd($ad);
            return response()->json(['status' => true, 'message' => 'Success', 'object' => $ad]);
        }

    }
    public function showReview(Ad $ad)
    {
        
        if (auth('user-api')->check()) {
            $reviews = Review::where('ad_id', $ad->id)->get();
            return response()->json(['status' => true, 'message' => 'Success', 'object' => $reviews]);
        }

    }


    public function myAdsActive()
    {
        
        if (auth('user-api')->check()) {
            // dd(auth('user-api')->user()->id);
            $myAdsActive = Ad::where('user_id',auth('user-api')->user()->id)->where('active', true)->get();
           
            return response()->json(['status' => true, 'message' => 'Success', 'object' => $myAdsActive]);
        }

    }
    public function myAdsInActive()
    {
        
        if (auth('user-api')->check()) {
            // dd(auth('user-api')->user()->id);
            $myAdsActive = Ad::where('user_id',auth('user-api')->user()->id)->where('active', false)->get();
           
            return response()->json(['status' => true, 'message' => 'Success', 'object' => $myAdsActive]);
        }

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
            'category_id' => 'required|numeric|exists:categories,id',
            // 'user_id' => 'required|numeric|exists:users,id',
            'name' => 'required|string|min:3',
            'description' => 'required|string|min:3',
            'price' => 'required|numeric|min:1',
            'type' => 'required|String|in:N,U',
            'image_1' => 'nullable|image|mimes:jpg,png|max:2048',
            'image_2' => 'nullable|image|mimes:jpg,png|max:2048',
            'image_3' => 'nullable|image|mimes:jpg,png|max:2048',
            'active' => 'in:true,false',
        ]);

        if (!$validator->fails()) {
            $ad = new Ad();
            $ad->name = $request->get('name');
            $ad->description = $request->get('description');
            $ad->price = $request->get('price');
            $ad->type = $request->get('type');
            $ad->category_id = $request->get('category_id');
            $ad->active = $request->get('active') == 'true';
            $ad->user_id = auth('user-api')->id();

            $isSaved = $ad->save();
            $users=User::all();
            foreach ($users as $user) {
                $user->notify(new NewAdNotification($ad));
            }
            if ($isSaved) {
                $this->saveImage($request, $ad, 'image_1');
                $this->saveImage($request, $ad, 'image_2');
                $this->saveImage($request, $ad, 'image_3');
            }

            return response()->json([
                'status' => $isSaved, 'message' => $isSaved ? 'Created successfully' :'Created Failed!',
              
            ], $isSaved ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);
        } else {
            return response()->json(['message' => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function saveImage(Request $request, ad $ad, String $key, bool $update = false)
    {
        if ($request->hasFile($key)) {
            if ($update) {
                foreach ($ad->images as $image) {
                    if (str_contains($image->name, $key)) {
                        Storage::disk('public')->delete('ads/' . $image->name);
                        $image->delete();
                    }
                }
            }
            $imageName = time() . '_' . str_replace(' ', '', $ad->name) . "_ad_$key." . $request->file($key)->extension();
            $request->file($key)->storePubliclyAs('ads', $imageName, ['disk' => 'public']);

            $image = new Image();
            $image->name = $imageName;
            $image->url = 'ads/' . $imageName;
            $ad->images()->save($image);
        }
    }

   

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ad  $ad
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ad $ad)
    {

        if ($ad->user_id != auth('user-api')->id()) {
            return response()->json(['status' => false, 'message' => 'You cannot update an advertisement that does not follow you !'], Response::HTTP_BAD_REQUEST);
        }
        
        $validator = Validator($request->all(), [
            'category_id' => 'required|numeric|exists:categories,id',
            'name' => 'required|string|min:3',
            'description' => 'required|string|min:3',
            'price' => 'required|numeric|min:1',
            'type' => 'required|String|in:N,U',
            'image_1' => 'nullable|image|mimes:jpg,png|max:2048',
            'image_2' => 'nullable|image|mimes:jpg,png|max:2048',
            'image_3' => 'nullable|image|mimes:jpg,png|max:2048',
            'active' => 'in:true,false',
        ]);

        

        if (!$validator->fails()) {
            $ad->category_id = $request->get('category_id');
            $ad->name = $request->get('name');
            $ad->description = $request->get('description');
            $ad->price = $request->get('price');
            $ad->type = $request->get('type');
            $ad->active = $request->get('active') == 'true';

            // $isSaved = $request->user()->ads()->save();
            $isSaved = $ad->save();

            if ($isSaved) {
                $this->saveImage($request, $ad, 'image_1' ,true);
                $this->saveImage($request, $ad, 'image_2',true);
                $this->saveImage($request, $ad, 'image_3',true);
            }

            return response()->json([
                'status' => $isSaved, 'message' => $isSaved ? 'Updated successfully' :'Updating Failed!',
                'object' => $ad,
            ], $isSaved ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);
        } else {
            return response()->json(['message' => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ad  $ad
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ad $ad)
    {
        if ($ad->user_id != auth('user-api')->id()) {
            return response()->json(['status' => false, 'message' => 'You cannot delete an advertisement that does not follow you !'], Response::HTTP_BAD_REQUEST);
        }
        $isDeleted = $ad->delete();
        if ($isDeleted) $this->deleteImages($ad);
        return response()->json([
            'message' =>  $isDeleted ? 'Ad DELETE SUCCESS' : 'Ad DELETE Faild'
        ], $isDeleted ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    private function deleteImages(Ad $ad)
    {
        foreach ($ad->images as $image) {
            Storage::disk('public')->delete('ads/' . $image->name);
            $image->delete();
        }
    }
}