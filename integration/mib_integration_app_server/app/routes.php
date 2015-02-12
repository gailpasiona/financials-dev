<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

// Route::get('/', function()
// {
// 	return View::make('hello');
// });
Route::any('test', 'SyncController@servertest');
Route::post('sync_po', 'SyncController@po_sync');
Route::post('sync_bp', 'SyncController@bp_sync');
Route::post('sync_ps', 'SyncController@ps_sync');

