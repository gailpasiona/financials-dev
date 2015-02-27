<?php

namespace Financials\Controllers;

use Financials\Repos\PurchasesRepositoryInterface;

class TransactionController extends \BaseController{
	
	public function __construct(PurchasesRepositoryInterface $purchases) {
		$this->beforeFilter('auth');
		// $this->beforeFilter('action_permission');//, array('except' => array('index')));
		$this->beforeFilter('session');

		$this->purchases = $purchases;
	}


	public function index(){
		return \View::make('layouts.user_dashboard')->with('user', \Confide::user()->username);
	}

	public function flush_session(){
		return Redirect::to('switch_session');
	}
	public function index_apinvoice(){
		//return \Response::json($this->rfp->selectAll());
		return \View::make('layouts.user_dashboard')->with('user', \Confide::user()->username);
	}

	public function index_rfp(){
		//return \Response::json($this->rfp->selectAll());
		return \View::make('layouts.user_dashboard')->with('user', \Confide::user()->username);
	}

	public function index_cv(){
		//return \Response::json($this->rfp->selectAll());
		return \View::make('layouts.user_dashboard')->with('user', \Confide::user()->username);
	}

	public function index_payables(){
		//return \Response::json($this->rfp->selectAll());
		return \View::make('financials.transactions_main')->with('user', \Confide::user()->username);
					//->with('data', $this->purchases->selectAll());

	}
	public function list_payables(){
		$type = \Input::get('type');
		if($type == 'all') 
			return \Response::json($this->purchases->selectAll());
		else
			return \Response::json($this->purchases->selectAllNoInvoice());
		
	}

	public function coa_list(){
		$load = null;
		$repo = \App::make('Financials\Coa');
		// $data = $repo->getAccountsByGroup(\Input::get('type'));
		if(\Input::get('type') == 1)
			$data = $repo->getAccountsBySub(array('3','4','5','6','7'));
		else
			$data = $repo->getAccountsByGroup(\Input::get('type'));

	
		return \Response::json($data);
	}

	
}