<?php

use App\Http\Controllers\SuratKontrolBpjsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/bpjs/rencana-kontrol/{nosep}', [SuratKontrolBpjsController::class, 'getBySep']);
Route::post('/bpjs/rencana-kontrol-no-surat/{no_surat}', [SuratKontrolBpjsController::class, 'getByNoSurat']);
Route::post('/bpjs/rencana-kontrol', [SuratKontrolBpjsController::class, 'storeSuratKontrol']);
