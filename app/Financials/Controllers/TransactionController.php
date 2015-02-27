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

	public function create(){
		$repo = \App::make('Financials\Supplier');
		
		$data['payee'] = $repo->selectAll();
		$data['title'] = 'Create Supplier Invoice';

		return \View::make('financials.modals.form_po')->with('data', $data);
	}

	public function store(){
		//company_id
		//$new_record->po_number = \Input::get('');
		// $new_record->supplier_id = \Input::get('');
		// $new_record->po_date = \Input::get('');
		// $new_record->po_total_amount = \Input::get('');
		// $new_record->po_paymentterms = \Input::get('');
		// $new_record->po_downpayment = \Input::get('');
		// $new_record->requestor = \Input::get('');
		// $new_record->requestor_dept_det = \Input::get('');
		// $new_record->po_fullyreceived = \Input::get('');
		// $new_record->po_status = \Input::get('');
		// $new_record->created_by = \Input::get('');
		// $new_record->approved_by = \Input::get('');
		// $new_record->po_remarks = \Input::get('');
		// $new_record->cancelled = \Input::get('');
		// $new_record->sync = \Input::get('');
		// $new_record->invoiced = \Input::get('');

		$return_info = array();

		try{

			\DB::beginTransaction();

			$record = $this->purchases->create(array('po_number' => \Input::get('po_number'), 'amount'=>\Input::get('amount_request'), 
				'payee' =>\Input::get('payee'), 'requestor' => \Input::get('requestor'), 'po_date' => \Input::get('po_date')));

			//\DB::commit();

			if($record['saved']){
					// $sdd = $repo->updateById(\Input::get('reference'));
					//return \Response::json(array('status' => 'success', 'message' => 'Invoice Created'));
				
					\DB::commit();
					$return_info['status'] = 'success';
					$return_info['message'] = 'Payable created!';

			}

			else{
				$return_info['status'] = 'success_error';
				$return_info['message'] = $record['object'];
			}

		}catch(\PDOException $e){
			\DB::rollBack();
			$return_info['status'] = 'success_failed';
			$return_info['message'] = $e->getmessage();//'Transaction Failed, Please contact System Administrator';
		}

		return \Response::json($return_info);
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
		//if(\Input::get('type') == 1)
		//	$data = $repo->selectAll();//$repo->getAccountsBySub(array('3','4','5','6','7'));
		//else
			$data = $repo->selectAll();//$repo->getAccountsByGroup(\Input::get('type'));

	
		return \Response::json($data);
	}

	
}