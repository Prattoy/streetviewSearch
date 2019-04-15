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
    //old apis
    Route::post('/streetview','StreetViewController@UploadData');
    Route::post('/geo/search','StreetViewController@geoSearch');
    Route::post('/streetview/update','StreetViewController@update');
    Route::post('/streetview/delete','StreetViewController@delete');

	Route::get('/streetview/get/files','StreetViewController@getFileList');
	Route::get('/streetview/get/{file}','StreetViewController@viewFile');
	// Route::get('/streetview/get/street/all','StreetViewController@viewAll');
	// 

	//New APIs
	Route::post('/streetviewNew','StreetViewController@saveData');
	// Route::post('/streetviewNew/folder','StreetViewController@saveImg');
    Route::post('/geo/newsearch','StreetViewController@geoSearchNew');
    // Route::post('/geo/pointsearch','StreetViewController@geoSearchPoint');
    Route::post('/streetview/deleteAll','StreetViewController@deleteAll');

    Route::post('/zip/save','StreetViewController@zipSaver');

    Route::get('/streetview/get/street/all','StreetViewController@viewAllNew');
    
    Route::get('/streetview/folders','StreetViewController@folders');
    Route::get('/streetview/folders/{slug}','StreetViewController@slug')->where('slug', '([A-Za-z0-9\-\.\_\/]+)');

    Route::get('/streetview/get/street/{geo_id}','StreetViewController@viewStreet');


    Route::get('/test','StreetViewController@test');

// });




