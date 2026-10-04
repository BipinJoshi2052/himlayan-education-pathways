<?php

use App\Common\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

Route::get('/ping', function () {
    return ApiResponse::success(['pong' => true], 'Pong.');
});

Route::get('/ping/validation-error', function (Request $request) {
    Validator::make($request->all(), [
        'name' => ['required', 'string'],
    ])->validate();

    return ApiResponse::success();
});

Route::get('/ping/server-error', function () {
    throw new RuntimeException('Deliberate test exception for the response envelope.');
});

Route::middleware('auth')->get('/ping/auth', function () {
    return ApiResponse::success(['authenticated' => true]);
});
