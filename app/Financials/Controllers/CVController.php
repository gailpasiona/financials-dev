<?php

namespace Financials\Controllers;

use Financials\Repos\CVRepositoryInterface;

class CVController extends \BaseController{
	
	public function __construct(CVRepositoryInterface $cv) {
		$this->beforeFilter('auth');
		$this->beforeFilter('action_permission:cv');//, array('except' => array('index')));
		$this->beforeFilter('session');
		$this->beforeFilter('approve_permission:cv', array('only' => array('approve')));

		$this->cv = $cv;
	}

	public function index(){
		return \View::make('financials.cv_main')->with('user', \Confide::user()->username);
	}

	public function create(){
		$repo = \App::make('Financials\Rfp');
		$data = $repo->getApprovedRecord(\Input::get('rfp'));

		$data_needed = array('rfp_number' => null, 'amount_requested' => null, 'supplier' => null);

		$data_needed['rfp_number'] = $data[0]['rfp_number'];
		$data_needed['amount_requested'] = $data[0]['amount_requested'];
		$data_needed['supplier'] = $data[0]['register']['reference']['supplier']['supplier_name'];

		$data_needed['title'] = "Create Cheque Voucher for " . $data[0]['rfp_number'];

		return \View::make('financials.modals.form_cv')->with('data',$data_needed);

		//return \Response::json($data_needed);
	}

	public function store(){
		// $rfp = $this->cv->create(\Input::all());

		// if($rfp['status']) return 'Saved';
		// else return $rfp['data'];


		$repo = \App::make('Financials\Rfp');
		// return \Response::json(array($this->purchases->find(\Input::get('reference'))->po_number,
		// 	$this->purchases->find(\Input::get('reference'))->po_total_amount));
		$rfp = $repo->findByRfpNum(\Input::get('rfp_number'));

		//return \Response::json(count($invoice->openforrfp));
		if(count($rfp->cv) < 1){
			//return \Response::json(array('already has rfp'));
			$request = $this->cv->create(\Input::all());

			if($request['saved']){
					// $sdd = $repo->updateById(\Input::get('reference'));
					return \Response::json(array('status' => 'success', 'message' => 'Cheque Voucher Created'));
			}
			else{
				return \Response::json(array('status' => 'success_error', 'message' => $request['object']));;
			}
				
		}

		else{
			//return \Response::json(array('not yet rfp'));
			return \Response::json(array('status' => 'success_restrict', 'message' => 'Unable to create CV, this RFP already have CV'));
		}
	}

	public function show(){

	}

	public function edit($record){
		$data = $this->cv->getOpenRecord($record); //to check

		// return \Response::json($data);

		// $rfp_info = array();
		$cv['cv_number'] = $data[0]['cv_number'];
		$cv['amount_requested'] = $data[0]['amount'];
		$cv['description'] = $data[0]['description'];
		$cv['supplier'] = $data[0]['rfp']['register']['reference']['supplier']['supplier_name'];
		$cv['cheque_number'] = $data[0]['cheque_number'];
		$cv['title'] = "Modify CV " . $data[0]['cv_number'];

		return \View::make('financials.modals.form_cv')->with('data',$cv);

	}

	public function update($record){
		$record = $this->cv->modify($record,\Input::all());
		
		if($record['saved'] > 0)
			return \Response::json(array('status' => 'success', 'message' => 'CV update completed'));

		else if($record['saved'] == 0)
			return \Response::json(array('status' => 'success_error', 'message' => $record['object']));

		else return \Response::json(array('status' => 'success_failed', 'message' => $record['object']));
	}

	public function list_requests(){
		return \Response::json($this->cv->selectAll());
	}

	public function approve(){
		// if() return \Response::json(array('status' => 'success', 'message' => 'CV approved!'));

		// else return \Response::json(array('status' => 'success_failed', 'message' => 'CV approval Failed'));
		$validate_record = $this->cv->pre_validate(\Input::get('cv'));

		if($validate_record['passed'] > 0){
			try{
					\DB::beginTransaction();
					$approval = $this->cv->approve(\Input::get('cv'));

					$crepo = \App::make('Financials\Register');
				    $cv_record = $this->cv->findRecord(\Input::get('cv'));
					
					$cregister = $crepo->create(array('amount'=>$cv_record->amount, 
						'ref_id' =>$cv_record->id, 'refno' => $cv_record->cv_number, 'module_id' => '3',
						'prefix' => 'CV','trans_type' => 'entry')); //add trans_type

					\DB::commit();

					$return_info['status'] = 'success';
					$return_info['message'] = 'CV Approval Completed';
					
					return \Response::json($return_info);
			}catch(\PDOException $e){
					\DB::rollBack();
					$return_info['status'] = 'success_failed';
					$return_info['message'] = 'Transaction Failed, Please contact System Administrator';
			}
		}

		// $record = $this->cv->approve(\Input::get('cv'));
		
		// if($record['saved'] > 0){
		// 	// return \Response::json(array('status' => 'success', 'message' => 'CV approval completed'));
			
		// }

		else if($validate_record['passed'] == 0)
			return \Response::json(array('status' => 'success_error', 'message' => $validate_record['object']));

		else return \Response::json(array('status' => 'success_failed', 'message' => $record['object']));
	}

	public function posting($invoice){
		// $repo = \App::make('Financials\Register');
		// $journal = $repo->find(\Input::get('reference'));

		//$data = $repo->getOpenRecord($this->cv->findRecord($invoice)->);
		//$data = $this->cv->pullRecord($invoice);

		$record = $this->cv->traceRecord($invoice);

		$register_info = array();

		$register_info['invoice'] = $record[0]['cregister']['register_id'];
		$register_info['amount'] = $record[0]['cregister']['account_value'];
		$register_info['payee'] = $record[0]['rfp']['register']['reference']['supplier']['supplier_name'];
		$register_info['refno'] = $record[0]['cregister']['register_refno'];
		$register_info['title'] = "Post Cheque Register " . $record[0]['cregister']['register_id'];

		return \View::make('financials.modals.form_chequepost')->with('data',$register_info);
		// return \Response::json($record);
	}

	public function post(){
		$register = \App::make('Financials\Register');
		
		$validate_record = $register->pre_posting(\Input::all());

		if($validate_record['passed'] > 0){
			if($this->prePost(\Input::get('amount_request'), \Input::get('account_amount'))){

				$return_info = array('status' => null, 'message' => null);

				try{

					\DB::beginTransaction();

					$header_account = \App::make('Financials\Coa')->findByName('Accounts Payable');

				   $entity = \Company::where('alias', \Session::get('company'))->first()->id;

				   $journal_repo = \App::make('Financials\Journal');

					$journal = $journal_repo->create(array('entity' => $entity,'module' => '3','reference' => \Input::get('invoice_no'), 'total_amount' => \Input::get('amount_request'),
								'post_data' => $this->preparelines(\Input::get('account'), \Input::get('account_amount')), 
								'header_account' => $header_account->account_id, 'header_debit' => \Input::get('amount_request'), 'header_credit' => 0));
					
					if($journal){
						$genledger_repo = \App::make('Financials\GenLedger');
						$gl = $genledger_repo->create(array('entity' => $entity, 'module' => '3','reference' => \Input::get('invoice_no'), 'total_amount' => \Input::get('amount_request'),
									'post_data' => $this->preparelines(\Input::get('account'), \Input::get('account_amount')),
									'header_account' => $header_account->account_id, 'header_debit' => \Input::get('amount_request'), 'header_credit' => 0));
						if($gl){
							$subledger_repo = \App::make('Financials\SubLedger');
							$subl = $subledger_repo->create(array('entity' => $entity, 'reference' => \Input::get('invoice_no'), 'credit' => 0,
									'debit' => \Input::get('amount_request'), 'balance' =>  0, 'vendor' =>  $this->cv->traceRecordObj(\Input::get('register_refno'))->rfp->register->reference->supplier->supplier_name));

						}
					}

					$register->post(\Input::get('invoice_no'));
					\DB::commit();
					$return_info['status'] = 'success';
					$return_info['message'] = 'Posting Successful';
				}catch(\PDOException $e){
					\DB::rollBack();
					$return_info['status'] = 'success_failed';
					$return_info['message'] = 'Transaction Failed, Please contact System Administrator';
				}
				
				return \Response::json($return_info);
			}

			else return \Response::json(array('status' => 'success_failed', 'message' => 'Total amount does not match with the total of amount of each account'));
			
		}

		else if($validate_record['passed'] == 0)
			return \Response::json(array('status' => 'success_error', 'message' => $validate_record['object']));
		
		else return \Response::json(array('status' => 'success_failed', 'message' => $validate_record['object']));
	}

	private function preparelines($accounts, $amounts){
		$init = 0;
		$lines = array();
		foreach ($accounts as $account) {
			$line = array('account' => null, 'amount' => null);
			$line['account'] = $account;
			$line['debit'] = 0;
			$line['credit'] = $amounts[$init];
			array_push($lines, $line);
			$line = null;
			$init++;
		}
		return $lines;
	}

	private function prePost($total, $amounts){
		$post_total = 0;

		foreach ($amounts as $amount) {
			$post_total += $amount;
		}

		if($total == $post_total) return true;
		else return false;
	}
}