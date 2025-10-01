<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/login_page', function () {
    return response()->json([
        'status' => false,
        'message' => "Login required"
    ], 401);
})->name('login');


Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, "login"]);
});


Route::middleware('auth:sanctum')->prefix('patient')->group(function () {
    Route::post('getData', [\App\Http\Controllers\Api\PatientController::class, 'getData']);
});
