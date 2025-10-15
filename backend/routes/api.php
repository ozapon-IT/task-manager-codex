<?php

use Illuminate\Support\Facades\Route;

// purpose: expose a simple health check endpoint for frontend connectivity tests
Route::get('/ping', fn () => response()->json(['pong' => true]));
