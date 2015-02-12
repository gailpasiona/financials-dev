<?php

use Financials\Repos\RfpRepositoryInterface;

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function __construct(RfpRepositoryInterface $rfp) {
		$this->rfp = $rfp;
	}

	public function show(){
		$user = Confide::user();
		$admin_role = Role::where('name','Administrator')->first();

	    if (!$user->hasRole($admin_role->name)) return Redirect::to('financials');
    	else return Redirect::to('manager');
		//return View::make('layouts.hello');
	}

	public function show_admin()
	{
		return View::make('layouts.admin_dashboard')->with('user', Confide::user()->username);
	}

	public function show_user()
	{
		return View::make('layouts.user_dashboard')->with('user', Confide::user()->username);
	}

	public function session_select(){
		if(! Session::has('company')){
			$companies = Company::all();
			return View::make('layouts.session_selection')->with('user', Confide::user()->username)
				->with('companies', $companies);
		}
		else{
			return Redirect::to('financials/' . Session::get('company') . '/dashboard');
		}
		
	}

	public function start_session($id){
		if(Session::has('company')){
			//if(Session::get('company') != $id){
				//echo 'Invalid Session';
				return Redirect::to('financials/' . Session::get('company') . '/dashboard');
			// }
			// else 
		}
		else{
			Session::put('company', $id);
			return Redirect::to('financials/' . Session::get('company') . '/dashboard');
		}
	}

	public function switch_session(){
		if(Session::has('company')){
			Session::forget('company');
		}
		return Redirect::to('financials');

	}

	public function testmodel(){
		return Response::json($this->rfp->selectAll());
	}


}
