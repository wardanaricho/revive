<?php

use App\Http\Controllers\BpjsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/bpjs/rencana-kontrol/{nosep}', [BpjsController::class, 'getBySep']);
Route::post('/bpjs/rencana-kontrol-no-surat/{no_surat}', [BpjsController::class, 'getByNoSurat']);
Route::post('/bpjs/rencana-kontrol', [BpjsController::class, 'storeSuratKontrol']);
