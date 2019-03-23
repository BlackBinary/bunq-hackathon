<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/receipt', 'ApiController@createReceipt');
Route::get('/getjson/{hash}', 'ApiController@getJSON');
//
//function (Request $request) {
//    \Log::debug($request);
//    return 'test';
//});
