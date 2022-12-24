<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\FavoriteAd;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FavoriteAdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $favorites = auth('user-api')->user()->favoriteAds;
        return response()->json(['status' => true, 'message' => 'Success', 'object' => $favorites]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
            'ad_id' => 'required|integer|exists:ads,id',
        ]);

        if (!$validator->fails()) {

            $ad = Ad::find($request->get('ad_id'));
            if (!is_null($ad)) {
                $user = auth('user-api')->user();
                $isFavorite = false;
                if (!$user->favoriteAds()->where('ad_id', $ad->id)->exists()) {
                    $messsage = 'Product has been added to your favorites successfully';
                    $isSaved = $user->favoriteAds()->save($ad);
                    $isFavorite = true;
                } else {
                    $messsage ='Product has been removed from your favorites successfully';
                    $isSaved = $user->favoriteAds()->detach($ad);
                    $isFavorite = false;
                }
                return response()->json([
                    'status' => $isSaved != null,
                    'favorite' => $isFavorite,
                    'message' => $isSaved ? $messsage : 'Favorite process failed, try again!',
                ], $isSaved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
            } else {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Object not fount',
                ]);
            }
        } else {
            return response()->json(array(
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
            ), Response::HTTP_BAD_REQUEST);

            // return ControllersService::generateValidationErrorMessage($validator->getMessageBag()->first());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FavoriteAd  $favoriteAd
     * @return \Illuminate\Http\Response
     */
    public function show(FavoriteAd $favoriteAd)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FavoriteAd  $favoriteAd
     * @return \Illuminate\Http\Response
     */
    public function edit(FavoriteAd $favoriteAd)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FavoriteAd  $favoriteAd
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FavoriteAd $favoriteAd)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FavoriteAd  $favoriteAd
     * @return \Illuminate\Http\Response
     */
    public function destroy(FavoriteAd $favoriteAd)
    {
        //
    }
}
