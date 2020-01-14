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

Route::middleware('api:handle')->get('/{token}/getWebhook',  'ApiController@gethook');

Route::middleware('api:handle')->post('/{token}/sendMessage', 'ApiController@message');
Route::middleware('api:handle')->post('/{token}/sendFile', 'ApiController@attachment');
Route::middleware('api:handle')->post('/{token}/setWebhook',  'ApiController@webhook');

Route::get('/getData', 'ApiController@getdata');
