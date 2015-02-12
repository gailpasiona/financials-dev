<?php

namespace Financials\Controllers;

use Financials\Repos\RfpRepositoryInterface;

class RFPController extends \BaseController{
	
	public function __construct(RfpRepositoryInterface $rfp) {
		$this->beforeFilter('auth');
		$this->beforeFilter('action_permission:rfp');//, array('except' => array('index')));$this->beforeFilter('test:value');
		$this->beforeFilter('session');
		$this->beforeFilter('approve_permission:rfp', array('only' => array('approve')));

		$this->rfp = $rfp;
	}

	public function index(){
		// return \Response::json($this->rfp->getAll());
		return \View::make('financials.rfp_main')->with('user', \Confide::user()->username);
	}

	public function create(){
		$repo = \App::make('Financials\Register');
		$data = $repo->getRecord(\Input::get('invoice'));

		$register_info = array();

		$register_info['cost_dept'] = $data[0]['reference']['requestor'];
		$register_info['invoice_ref'] = $data[0]['register_id'];
		$register_info['amount_request'] = $data[0]['account_value'];
		$register_info['payee_name'] = $data[0]['reference']['supplier']['supplier_name'];
		$register_info['payee_address'] = $data[0]['reference']['supplier']['address'];
		$register_info['title'] = "Create RFP for Invoice " . $data[0]['register_id'];

		return \View::make('financials.modals.form_rfp')->with('data',$register_info);
	}

	public function store(){
		// $rfp = $this->rfp->create(\Input::all());
		// if($rfp->id) return 'Saved';
		$repo = \App::make('Financials\Register');
		// return \Response::json(array($this->purchases->find(\Input::get('reference'))->po_number,
		// 	$this->purchases->find(\Input::get('reference'))->po_total_amount));
		$invoice = $repo->findByRegId(\Input::get('invoice_ref'));

		//return \Response::json(count($invoice->openforrfp));
		if(count($invoice->openforrfp) < 1){
			//return \Response::json(array('already has rfp'));
			$request = $this->rfp->create(\Input::all());

			if($request['saved']){
					// $sdd = $repo->updateById(\Input::get('reference'));
					return \Response::json(array('status' => 'success', 'message' => 'RFP Created'));
			}
			else{
				return \Response::json(array('status' => 'success_error', 'message' => $request['object']));;
			}
				
		}

		else{
			//return \Response::json(array('not yet rfp'));
			return \Response::json(array('status' => 'success_restrict', 'message' => 'Unable to create RFP, this invoice already have RFP'));
		}
	}

	public function show(){

	}

	public function edit($record){
		$data = $this->rfp->getOpenRecord($record); //to check

		// return \Response::json($data);

		// $rfp_info = array();
		$rfp['rfp_number'] = $data[0]['rfp_number'];
		$rfp['cost_dept'] = $data[0]['costing_department'];
		$rfp['date_needed'] = $data[0]['date_needed'];
		// $rfp['invoice_ref'] = $data[0]['register']['register_id'];
		$rfp['amount_request'] = $data[0]['amount_requested'];
		$rfp['description'] = $data[0]['request_description'];
		$rfp['payee_name'] = $data[0]['register']['reference']['supplier']['supplier_name'];
		$rfp['payee_address'] = $data[0]['register']['reference']['supplier']['address'];
		$rfp['title'] = "Modify RFP " . $data[0]['rfp_number'];

		return \View::make('financials.modals.form_rfp')->with('data',$rfp);

		//return \Response::json($data);
	}

	public function update($record){
		// if($this->rfp->modify($record,\Input::all())) return 'RFP Updated!';
		// else return 'Update Failed!';
		$record = $this->rfp->modify($record,\Input::all());
		
		if($record['saved'] > 0)
			return \Response::json(array('status' => 'success', 'message' => 'RFP update completed'));

		else if($record['saved'] == 0)
			return \Response::json(array('status' => 'success_error', 'message' => $record['object']));

		else return \Response::json(array('status' => 'success_failed', 'message' => $record['object']));
	}

	public function open_rfp(){
		$type = \Input::get('type');
		$data = null;
		switch ($type) {
			case 'open':
				$data = $this->rfp->getOpen();
				break;
			case 'all':
				$data = $this->rfp->getAll();
				break;
			default:
				$data = $this->rfp->getPending();
				break;
		}
		return \Response::json($data);
	}

	public function list_requests(){
		return \Response::json($this->rfp->getAll());
	}

	public function approve(){
		if($this->rfp->approve(\Input::get('rfp'))) return \Response::json(array('status' => 'success', 'message' => 'RFP approved!'));

		else return \Response::json(array('status' => 'success_failed', 'message' => 'RFP approval Failed'));
	}
}