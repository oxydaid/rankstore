<?php

use App\Http\Controllers\Api\AriePulsaCallbackController;
use App\Http\Controllers\Api\TokopayCallbackController;
use Illuminate\Support\Facades\Route;

Route::post('callback/tokopay', [TokopayCallbackController::class, 'handle'])->name('api.callback.tokopay');
Route::post('callback/ariepulsa', [AriePulsaCallbackController::class, 'handle'])->name('api.callback.ariepulsa');
