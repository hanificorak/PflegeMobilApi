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
    Route::post('addPatient', [\App\Http\Controllers\Api\PatientController::class, 'addPatient']);
});

Route::middleware('auth:sanctum')->prefix('cares')->group(function () {
    Route::post('getData', [\App\Http\Controllers\Api\CaresController::class, 'getData']);
    Route::post('statusChange', [\App\Http\Controllers\Api\CaresController::class, 'statusChange']);
    Route::post('addCares', [\App\Http\Controllers\Api\CaresController::class, 'addCares']);
    Route::post('careDetails', [\App\Http\Controllers\Api\CaresController::class, 'careDetails']);
    Route::post('getParam', [\App\Http\Controllers\Api\CaresController::class, 'getParam']);
    Route::post('careProcAdd', [\App\Http\Controllers\Api\CaresController::class, 'careProcAdd']);
    Route::post('medicineList', [\App\Http\Controllers\Api\CaresController::class, 'medicineList']);
    Route::post('medicineAdd', [\App\Http\Controllers\Api\CaresController::class, 'medicineAdd']);
    Route::post('getCareDetailData', [\App\Http\Controllers\Api\CaresController::class, 'getCareDetailData']);
    Route::post('updateDesc', [\App\Http\Controllers\Api\CaresController::class, 'updateDesc']);
    Route::post('medicineDelete', [\App\Http\Controllers\Api\CaresController::class, 'medicineDelete']);
    Route::post('procDelete', [\App\Http\Controllers\Api\CaresController::class, 'procDelete']);

});
