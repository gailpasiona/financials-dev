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


// Route::any('scope', 'HomeController@scope_testing');
//

// Confide routes
# Admin Routes
Route::group(['before' => 'auth'], function()
{
  // Route::get('/admin', ['as' => 'admin_dashboard', 'uses' => 'AdminController@getHome']);
  // Route::resource('admin/profiles', 'AdminUsersController', ['only' => ['index', 'show', 'edit', 'update', 'destroy']]);
	
	Route::get('manager/users', array('as'=> 'user_create', 'uses' => 'UsersController@create'));
	Route::post('manager/users/create', 'UsersController@store');
	Route::post('manager/users/update', 'UsersController@update_user');
	Route::get('manager/users/list', 'UsersController@load_users');
	Route::get('manager/users/modify/{user}', array('as'=> 'user_modify', 'uses' => 'UsersController@modify'));
	Route::post('manager/roles/create', 'RoleController@create');
	Route::get('manager', array('as' => 'admin_dashboard', 'uses'=>'HomeController@show_admin'));
	Route::get('manager/roles/list', 'RoleController@load_roles');
	Route::get('manager/roles/users/{role}', 'RoleController@role_users');
	Route::get('manager/roles/members/{role}', 'RoleController@role_members');
	Route::post('manager/roles/assign', 'RoleController@assign_users_to_role');
	Route::post('manager/roles/detach', 'RoleController@detach_users_from_role');
	Route::get('manager/roles/permissions/{role}', 'RoleController@role_permissions');
	Route::post('manager/roles/assign_permisions', 'RoleController@assign_permisions');

});


Route::group(['before' => 'auth'], function()
{

// Route::get('manager/users', array('as'=> 'user_create', 'uses' => 'UsersController@create'));
// Route::post('manager/users/create', 'UsersController@store');

Route::get('/', 'HomeController@show');
Route::get('users/confirm/{code}', 'UsersController@confirm');
Route::get('users/forgot_password', 'UsersController@forgotPassword');
Route::post('users/forgot_password', 'UsersController@doForgotPassword');
Route::get('users/reset_password/{token}', 'UsersController@resetPassword');
Route::post('users/reset_password', 'UsersController@doResetPassword');

// Route::post('manager/users/update', 'UsersController@update_user');

// Route::get('manager/users/list', 'UsersController@load_users');
// Route::get('manager/users/modify/{user}', array('as'=> 'user_modify', 'uses' => 'UsersController@modify'));
// Route::post('manager/roles/create', 'RoleController@create');

// Route::get('test', 'UsersController@test');
// Route::get('manager/dashboard', array('as' => 'admin_dashboard', 'uses'=>'HomeController@show'));
// Route::get('manager/roles/list', 'RoleController@load_roles');
// Route::get('manager/roles/users/{role}', 'RoleController@role_users');
// Route::get('manager/roles/members/{role}', 'RoleController@role_members');
// Route::post('manager/roles/assign', 'RoleController@assign_users_to_role');
// Route::post('manager/roles/detach', 'RoleController@detach_users_from_role');

Route::get('forms/user', array('as' => 'user_form', 'uses' => 'UsersController@loadform'));
Route::get('forms/role', array('as' => 'role_form', 'uses' => 'RoleController@loadform'));

});

Route::group(['before' => 'auth'], function(){
Route::get('session/start/{company}', array('as' => 'start_session', 'uses'=>'HomeController@start_session'));
Route::get('session/switch', array('as' => 'switch_session', 'uses'=>'HomeController@switch_session'));
	
});

Route::group(['before' => 'auth'], function(){
Route::get('financials', array('as' => 'select_session', 'uses'=>'HomeController@session_select'));
});

Route::group(['before' => 'auth|session|perms'], function(){
//Route::get('company/{company}/*', array('as' => 'user_dashboard', 'uses'=>'HomeController@show_user'));
//Route::get('financials/{company}', array('as' => 'user_dashboard', 'uses'=>'HomeController@show_user'));
// Route::get('financials/' . Session::get('company'), 'Financials\Controllers\TransactionController@index');
});



Route::group(['prefix' => 'financials/' . Request::segment(2)], function(){
	Route::get('dashboard', 'Financials\Controllers\TransactionController@index');
	Route::get('AP/create', 'Financials\Controllers\TransactionController@create');
	Route::post('AP/save', 'Financials\Controllers\TransactionController@store');
	//Route::get('/', array('as' => 'user_dashboard', 'uses'=>'HomeController@show_user')); //to do check perms
	Route::get('AP', 'Financials\Controllers\TransactionController@index_payables');

	Route::resource('AP/invoice', 'Financials\Controllers\APInvoiceController');
	Route::resource('Cashier/sales', 'Financials\Controllers\ARInvoiceController');
	Route::resource('Cashier/receipts', 'Financials\Controllers\ARReceiptController');
	Route::post('Cashier/receipts/posting', 'Financials\Controllers\ARReceiptController@post');

	Route::get('Cashier/receivables', array('as' => 'list_aging_invoice', 'uses' => 'Financials\Controllers\ARInvoiceController@list_receivables'));
	Route::get('Cashier/list_receipts', array('as' => 'list_receipts', 'uses' => 'Financials\Controllers\ARReceiptController@list_receipts'));

	Route::get('AP/generate_invoice', 'Financials\Controllers\APInvoiceController@generate');
	Route::get('AP/aging', array('as' => 'list_aging_invoice', 'uses' => 'Financials\Controllers\APInvoiceController@list_aging'));
    Route::get('AP/register/{ref}', 'Financials\Controllers\APInvoiceController@getRegisterInfo');
    Route::post('AP/invoice/posting', 'Financials\Controllers\APInvoiceController@post');
    Route::get('AP/invoice/post/{invoice}', 'Financials\Controllers\APInvoiceController@posting');
    Route::get('AP/cv/post/{invoice}', 'Financials\Controllers\CVController@posting');
    Route::post('AP/cv/posting', 'Financials\Controllers\CVController@post');
    Route::post('Cashier/sales/posting', 'Financials\Controllers\ARInvoiceController@post');
    Route::get('Cashier/sales/post/{invoice}', 'Financials\Controllers\ARInvoiceController@posting');


	Route::resource('AP/rfp', 'Financials\Controllers\RFPController');
	Route::resource('AP/cv', 'Financials\Controllers\CVController');


	//Route::resource('AP/invoice', 'Financials\Controllers\APController');
	Route::get('AP/list', array('as' => 'list_payables', 'uses' => 'Financials\Controllers\TransactionController@list_payables'));
	
    Route::get('AP/pay_requests', array('as' => 'list_payment_requests', 'uses' => 'Financials\Controllers\RFPController@list_requests'));
    Route::get('AP/cv_requests', array('as' => 'list_payment_requests', 'uses' => 'Financials\Controllers\CVController@list_requests'));
    Route::post('AP/rfp/approval', 'Financials\Controllers\RFPController@approve');
    Route::post('AP/cv/approval', 'Financials\Controllers\CVController@approve');
    Route::get('coa_list', 'Financials\Controllers\TransactionController@coa_list'); //AP/coa_list
    Route::get('AP/coa_list', 'Financials\Controllers\TransactionController@coa_list');
    Route::get('Cashier/coa_list', 'Financials\Controllers\TransactionController@coa_list');
	
});

Route::get('login', 'UsersController@login');
Route::post('users/login', 'UsersController@doLogin');
Route::get('users/logout', 'UsersController@logout');




