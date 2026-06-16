<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MassMessageController;


Route::prefix('v1')->group(function () {

    Route::get('/mass-messages', [MassMessageController::class, 'index'])
        ->name('api.mass-messages.index');

    Route::post('/mass-messages/send', [MassMessageController::class, 'send'])
        ->name('api.mass-messages.send');

    Route::get('/mass-messages/{id}/status', [MassMessageController::class, 'status'])
        ->name('api.mass-messages.status')
        ->where('id', '[0-9]+');
});
