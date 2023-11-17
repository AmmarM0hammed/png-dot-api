<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FollowerController;
use App\Http\Controllers\Api\PhotoController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LikeController;
use Illuminate\Support\Facades\Route;



Route::controller(AuthController::class)->group(function (){
    Route::post('/register','register');
    Route::post('/login','login');
});


Route::middleware('auth:sanctum')->group(function(){
    Route::controller(PhotoController::class)->group(function(){
        Route::post('/create',"create");
        Route::get('/show/{id}',"show");
        Route::get('/photo',"index");
        Route::get('/photo/follower',"follower");
        Route::delete('/photo/{id}', 'delete'); 
    });

    Route::controller(UserController::class)->group(function(){
        Route::post("profile/update","update"); 
        Route::get("profile/{username}","profile");
        Route::get("profile/{username}/check_follower","check_follower");
        Route::get("profile/{username}/followers","followers");
    });

    Route::controller(FollowerController::class)->group(function(){
        Route::get('profile/{username}/follow',"followe");
    });
    Route::get('/photo/{id}/likes', [LikeController::class, 'likeDislike']);
    Route::get("/logout",[AuthController::class,"logout"]);
});