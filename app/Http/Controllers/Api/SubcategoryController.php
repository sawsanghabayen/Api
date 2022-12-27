<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    public function show(Request $request )
    {
        if (auth('user-api')->check()) {
            dd(json_decode($request->id, true));
            if()
           
            $ads=Ad::withCount(['subcategoryAds' => function ($query) use($subcategory){
                $query->whereIn('subcategories_id',  json_decode($subcategory->id, true) );
            }])->having('subcategory_ads_count', '=', count(json_decode($subcategory->subcategories, true) ))->get();
        
            return response()->json(['status' => true, 'message' => 'Success', 'object' => $ads]);
        }
    }
}
