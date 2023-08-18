<?php


use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

//User Routes
Route::group(["prefix" => "users"],function (){
    Route::get("profile",       [UserController::class,"profile"])->middleware("customAuth");
});

Route::group(["prefix" => "products"],function (){
    Route::get("",              [ProductController::class,"index"]);
    Route::get("/{product}",    [ProductController::class,"show"]);
});
