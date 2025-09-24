<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
});

// VAT Calculation API endpoints (public - no authentication required for calculations)
Route::post('/vat/calculate', [VatController::class, 'calculate']);
Route::post('/vat/breakdown', [VatController::class, 'breakdown']);