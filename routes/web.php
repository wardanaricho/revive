<?php

use App\Http\Controllers\TestAntrolController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-get-antrian', [TestAntrolController::class, 'index']);
