<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        "API" => "Uts Pemograman berbasis Framework",
        "Nama" => "Fahmi Idris Anjounghan",
        "Nim" => "22416255201247",
    ], 200);
});


Route::get("/login", [AuthController::class, 'index'])->name('login');
Route::post("/login", [AuthController::class, 'login']);
Route::post("/register", [AuthController::class, 'register']);
Route::get("/logout", [AuthController::class, 'logout'])->middleware('auth:api');


// login / register with google
Route::get('/oauth/register', [AuthController::class, 'loginWithGoogle']);

// product list
Route::group(['middleware' => 'auth:api'], function () {
    Route::get("/products", [ProductController::class, 'index']);
    Route::post("/products", [ProductController::class, 'store']);
    Route::put("/products/{id}", [ProductController::class, 'update']);
    Route::delete("/products/{id}", [ProductController::class, 'destroy']);

    // category list
    Route::middleware('admin')->group(function () {
        Route::get("/categories", [CategoryController::class, 'index']);
        Route::post("/categories", [CategoryController::class, 'store']);
        Route::put("/categories/{id}", [CategoryController::class, 'update']);
        Route::delete("/categories/{id}", [CategoryController::class, 'destroy']);
    });
});
