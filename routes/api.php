<?php

// use App\Http\Controllers\Auth\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Feed\FeedController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//health
Route::get('/health', function () {
    return response()->json(['status' => true]);
});
Route::post("/register", [AuthenticationController::class, 'register']);
Route::post("/login", [AuthenticationController::class, 'login']);
Route::get("/feeds", [FeedController::class, 'index'])->middleware("auth:sanctum");
Route::post("/feed/store", [FeedController::class, 'store'])->middleware("auth:sanctum");
Route::post("/feed/like/{feed_id}", [FeedController::class, 'likePost'])->middleware("auth:sanctum");
Route::get("/feed/comments/{feed_id}", [FeedController::class, "getComments"])->middleware("auth:sanctum");
Route::post("/feed/comments/create", [FeedController::class, "CreateComment"])->middleware("auth:sanctum"); 

