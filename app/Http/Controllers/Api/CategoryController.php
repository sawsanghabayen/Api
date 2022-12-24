<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
       
        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => [
                'categories' => $categories,
             
            ]
        ], Response::HTTP_OK);
    }

    public function show(Category $category)
    {
        if (auth('user-api')->check()) {
            $ads = Ad::where('category_id', $category->id)->get();
            
            return response()->json(['status' => true, 'message' => 'Success', 'object' => $ads]);
        }

}
}