<?php

use App\Http\Controllers\Api\AdController;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\FavoriteAdController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SubcategoryController;
use App\Http\Controllers\Api\UserBlockController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('auth')->group(function () {
    Route::post('login', [ApiAuthController::class, 'login']);
    Route::post('register', [ApiAuthController::class, 'register']);
    Route::post('activate', [ApiAuthController::class, 'activateAccount']);
    Route::post('forget-password', [ApiAuthController::class, 'forgetPassword']);
    Route::post('reset-password', [ApiAuthController::class, 'resetPassword']);
});



Route::prefix('auth')->middleware('auth:user-api')->group(function () {
    Route::post('change-password', [ApiAuthController::class, 'changePassword']);

    Route::put('update-profile', [ApiAuthController::class, 'updateProfile']);

    Route::get('logout', [ApiAuthController::class, 'logout']);
});


Route::middleware(['auth:user-api'])->group(function () {
    Route::get('categories', [CategoryController::class, 'index']);
    Route::post('contacts', [ContactController::class, 'store']);
    Route::get('favorite-ads', [FavoriteAdController::class, 'index']);
    Route::post('favorite-ads', [FavoriteAdController::class, 'store']);
    Route::get('notifications', [NotificationController::class, 'index']);
    
    // Route::get('reviews/{review}', [ReviewController::class, 'show']);
    Route::get('reviews/{ad}', [AdController::class, 'showReview']);

    Route::post('review', [ReviewController::class, 'store']);

    Route::apiResource('ads', AdController::class);

    Route::get('ads', [AdController::class, 'index']);
    Route::get('ads/{ad}', [AdController::class, 'show']);
    Route::get('myAdsActive', [AdController::class, 'myAdsActive']);
    Route::get('myAdsInActive', [AdController::class, 'myAdsInActive']);

    Route::get('filter', [AdController::class, 'filterAds']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);
    Route::get('users/{user}', [UserController::class, 'show']);

    Route::get('faqs', [FaqController::class, 'index']);

    Route::get('chats', [ChatController::class , 'index']);
    Route::get('chats/{id}/messages', [MessageController::class , 'index']);
    Route::post('messages', [MessageController::class , 'store']);

    Route::get('subcategories/{subcategory}', [SubcategoryController::class, 'show']);

    Route::post('/users/block', [UserController::class , 'block'])->name('users.block');

    Route::get('/user_blocks', [UserBlockController::class , 'getBlockedUsers']);


});

