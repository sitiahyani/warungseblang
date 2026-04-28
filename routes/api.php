<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

Route::post('/sync-admin', function (Request $request) {
    Log::info('Data admin tersinkron:', $request->all());

    return response()->json([
        'status' => 'ok'
    ]);
});