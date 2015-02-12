<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('dashboard');
});

// Route::filter('admin', function()
// {
//   $user = Confide::user();
//   $admin_role = Role::where('name','Administrator')->first();

//   if (!$user->hasRole($admin_role->name)) return Response::make('Unauthorized, you are not an admin', 401);//Redirect::to('login');
// });

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});


//Admin console filtering
Route::filter('ajax_filter', function(){
	if (!Request::ajax())
        {
            return Response::make('Invalid Request', 401);
        }
});

Route::filter('session', function(){
	if (! Session::has('company'))
	{
        return Redirect::to('financials');
    }
    else{
    	if(Session::get('company') != Request::segment(2) ){

    	return Response::make('Session-Request Mismatch');
    }
    	//return Redirect::to('company/' . Session::get('company'));

    } 
    
    //return Response::make('Invalid Request', 401);
});

Route::filter('perms', function(){
	if (! Entrust::can(Request::segment(2) . '_view_records'))
	{
		if(Session::has('company')){
			Session::forget('company');
		}
        return Redirect::to('financials');
    }
    else{
    	//Session::put('company', Request::segment(2));
    	//return Response::make('Invalid Requestjj', 300);
    }
    //return Response::make('Invalid Request', 401);
});

Route::filter('action_permission', function($route, $request, $value =''){
	if (! Entrust::can(Request::segment(2) . '_manage_' . $value))//(! Entrust::can(Request::segment(2) . '_manage_' . Request::segment(4)))
	{
         // if (!Request::ajax())
        	return  Response::view('errors.401',array('user' => Confide::user()));//json(array('ss' => Request::segment(2) . '_manage_' . $value));
        // else
        // 	return  Response::json(array('status'  => 'success_failed', 'message' => 'Insufficient permissions to access this data'));

    }
    
});

Route::filter('approve_permission', function($route, $reques, $value=''){
	if (! Entrust::can(Request::segment(2) . '_approve_' . $value))//(! Entrust::can(Request::segment(2) . '_manage_' . Request::segment(4)))
	{
		return  Response::json(array('status'  => 'success_failed', 'message' => 'Insufficient permissions for this action'));
	}
});
	
Route::when(':any', 'auth');
Route::when('forms/*', 'ajax_filter');
Route::when('manager/users/*', 'ajax_filter');
Route::when('manager/roles/*', 'ajax_filter');

//route authorization
//Entrust::routeNeedsPermission('manager',Request::segment(1).'manage_system', Response::make(Request::segment(1), 399));//Response::view('errors.401'));
Entrust::routeNeedsPermission('manager','manage_system', Response::view('errors.401'));
Entrust::routeNeedsPermission('manager/*','manage_system', Response::view('errors.401'));
//Entrust::routeNeedsPermission('company/' . Request::segment(2) . '/*', Request::segment(2) . '_view_records', Response::view('errors.401'));
//Entrust::routeNeedsPermission('company/' . Request::segment(2), Request::segment(2) . '_view_records', Response::view('errors.401'));

//Entrust::routeNeedsPermission('system/*', 'view_records', Response::view('errors.401'));
//Entrust::routeNeedsPermission('system/', 'view_records',  Response::view('errors.401'));