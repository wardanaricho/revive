<?php

use App\Http\Controllers\IcareController;
use App\Http\Controllers\SuratKontrolBpjsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/bpjs/rencana-kontrol/{nosep}', [SuratKontrolBpjsController::class, 'getBySep']);
Route::post('/bpjs/rencana-kontrol-no-surat/{no_surat}', [SuratKontrolBpjsController::class, 'getByNoSurat']);
Route::post('/bpjs/rencana-kontrol', [SuratKontrolBpjsController::class, 'storeSuratKontrol']);
Route::put('/bpjs/rencana-kontrol', [SuratKontrolBpjsController::class, 'updateSuratKontrol']);
Route::delete('/bpjs/rencana-kontrol', [SuratKontrolBpjsController::class, 'deleteSuratKontrol']);

Route::get('/bpjs/icare/{no_kartu}/{kode_dokter}', [IcareController::class, 'getIcare']);
