<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FaqController extends Controller
{
    public function index()
    {
        if (auth('user-api')->check()) {
            $faqs = Faq::all();
            return response()->json([
                'status' => true,
                'message' => 'Success',
                'data' => [
                'ads' => $faqs,
                 
                ]
            ], Response::HTTP_OK);
        }
    }
}
