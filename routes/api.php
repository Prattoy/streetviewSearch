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

// Route::middleware(['cors'])->group(function () {
    
    Route::post('/streetview','StreetViewController@UploadData');

	Route::get('/streetview/get/files','StreetViewController@getFileList');
	Route::get('/streetview/get/{file}','StreetViewController@viewFile');
	Route::get('/streetview/get/street/all','StreetViewController@viewAll');
	Route::get('/streetview/get/street/{geo_id}','StreetViewController@viewStreet');

// });




