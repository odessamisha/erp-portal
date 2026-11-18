<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/webhook/incoming_messages', [\App\Http\Controllers\WebhookController::class, 'incomingMessages']);
Route::post('/webhook/status_message', [\App\Http\Controllers\WebhookController::class, 'statusMessage']);
