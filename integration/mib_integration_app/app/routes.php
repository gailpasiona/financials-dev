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
Route::get('/',	array('as'=> 'show_default', 'uses'=> 'HomeController@showWelcome'));
Route::get('po', array('as'=> 'show_purchases', 'uses'=> 'POController@show'));
Route::get('po_records',	'POController@records');
Route::get('bp',	array('as'=> 'show_suppliers', 'uses'=> 'BPController@show'));
Route::get('bp_records',	'BPController@records');
Route::get('ps',	array('as'=> 'show_payroll', 'uses'=> 'PSController@show'));
Route::get('ps_records',	'PSController@records');
Route::get('test',	'BPController@sync');
Route::post('server', 'SyncController@servertest');
Route::post('bp/sync',	array('as' => 'syncBP', 'uses' => 'BPController@server_sync'));
Route::post('po/sync',	array('as' => 'syncPO', 'uses' => 'POController@server_sync'));
Route::post('ps/sync',	array('as' => 'syncPS', 'uses' => 'PSController@server_sync'));
