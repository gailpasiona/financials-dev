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

		$coa_repo = \App::make('Financials\Coa');

		$data_needed = array('rfp_number' => null, 'amount_requested' => null, 'supplier' => null);

		$data_needed['coa_list'] = $coa_repo->selectAll();//getAccountsBySub(array('1'));
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

		$coa_repo = \App::make('Financials\Coa');
		// return \Response::json($data);

		// $rfp_info = array();
		$cv['cv_number'] = $data[0]['cv_number'];
		$cv['payment_bank'] = $data[0]['payment_bank'];
		$cv['coa_list'] = $coa_repo->selectAll();//getAccountsBySub(array('1'));
		$cv['amount_requested'] = $data[0]['amount'];
		$cv['description'] = $data[0]['description'];
		$cv['supplier'] = $data[0]['rfp']['register']['reference']['supplier']['supplier_name'];
		//$cv['cheque_number'] = $data[0]['cheque_number'];
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

	// private function getAccountData($data){
	// 	$header_account = \App::make('Financials\Coa')->findByName('Accounts Payable')->account_id;
	// 	$return_value = null;
	// 	foreach ($data as $line) {
	// 		if($line['account_id'] == $header_account){
	// 			$return_value = array('account_id' => $line['account_id'], 'amount' => $line['line_amount'],
	// 				['description'] => 'N/A');

	// 			break;
	// 		}
	// 	}

	// 	return $return_value;

	// }

	private function parse_lines($header, $lines){ //Check register lines
		$ctr = 0;
		$bulk = array();

		$bank_acct = array();
		$ap_acct = array();
		$types = array();
		$accts = array();
		$amts = array();

		$header_account = \App::make('Financials\Coa')->findByName('Accounts Payable')->account_id;
		//$return_value = null;
		
		foreach ($lines as $line) {

			if($line['account_id'] == $header_account){
				// $return_value = array('account_id' => $line['account_id'], 'amount' => $line['line_amount'],
				// 	['description'] => 'N/A');
				$ap_account = array('account' => $line['account_id'],'line' => $ctr,'description' => 'N/A',
					'amount' => $line['line_amount'], 'type' => 'D');
				
				$ctr++;

				array_push($types, 'D');
				array_push($accts, $line['account_id']);
				array_push($amts, $line['line_amount']);
				
				break;
			}

		}

		array_push($types, 'C');
		array_push($accts, $header['bank']);
		array_push($amts, $header['amount']);

		$bank_acct = array('account' => $header['bank'],'line' => $ctr,'description' => 'N/A',
			'amount' => $header['amount'], 'type' => 'C');

		array_push($bulk, $ap_account);
		array_push($bulk, $bank_acct);

			

		return array('parsed_lines' => $bulk, 'types' => $types, 'accounts' => $accts, 'amounts' => $amts);
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
				    $cv_record = $this->cv->findRecordwithRef(\Input::get('cv'));
					
					// $cregister = $crepo->create(array('amount'=>$cv_record->amount, 
					// 	'ref_id' =>$cv_record->id, 'refno' => $cv_record->cv_number, 'module_id' => '3',
					// 	'prefix' => 'CV','trans_type' => 'entry')); //add trans_type

					// $parsed_lines = $this->parse_lines();
					$parsed_lines = $this->parse_lines(array('bank' => $cv_record->toArray()['payment_bank'],
							'amount' => $cv_record->toArray()['amount']), $cv_record->toArray()['rfp']['register']['lines']);

					$cregister = $crepo->create(array('trans_type' => 'check_entry', 'amount'=>$cv_record->amount, 
						'ref_id' =>$cv_record->id, 'refno' => 'To Follow', 'module_id' => '3','prefix' => 'CHK',
						'entry_type' => $parsed_lines['types'], 'line_accounts' => $parsed_lines['accounts'],
						'line_amounts' => $parsed_lines['amounts'], 'line_descriptions' => array('N/A','N/A'),
						'invoice_date' => date("Y-m-d")));

					if($cregister['saved']){
						$lines_repo = \App::make('Financials\InvoiceLine');

						$lines = $lines_repo->create($parsed_lines['parsed_lines'],$cregister['object']->id);

						if($lines){
							\DB::commit();
							$return_info['status'] = 'success';
							$return_info['message'] = 'Approval Succeeded!';
						}
						else{
							$return_info['status'] = 'success_error';
							$return_info['message'] = 'Unable to process transaction';
						}
						return \Response::json($return_info);
					}
					else{
						$arr = $cv_record->toArray();//['rfp']['register']['lines'];
						$return_info['status'] = 'success_failed';
						$return_info['object'] = $arr;
					
						$return_info['message'] = $cregister['object'];
					}

					return \Response::json($return_info);

			}catch(\PDOException $e){
					\DB::rollBack();
					$return_info['status'] = 'success_failed';
					$return_info['message'] = $e->getMessage();//'Transaction Failed, Please contact System Administrator';

					return \Response::json($return_info);
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

		$coa_repo = \App::make('Financials\Coa');

		$register_info = array();

		$register_info['coa_list'] = $coa_repo->selectAll();//getAccountsBySub(array('1','6'));
		$register_info['invoice'] = $record[0]['cregister']['register_id'];
		$register_info['amount'] = $record[0]['cregister']['account_value'];
		$register_info['payee'] = $record[0]['rfp']['register']['reference']['supplier']['supplier_name'];
		$register_info['refno'] = $record[0]['cregister']['register_refno'];
		$register_info['title'] = "Post Cheque Register " . $record[0]['cregister']['register_id'];
		$register_info['lines'] = $record[0]['cregister']['lines'];

		return \View::make('financials.modals.form_chequepost')->with('data',$register_info);
		// return \Response::json($record);
	}

	public function post_old(){
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
									'debit' => \Input::get('amount_request'), 'balance' =>  0, 'vendor' => \Input::get('payee_name')));//$this->cv->traceRecordObj(\Input::get('register_refno'))->rfp->register->reference->supplier->supplier_name));

						}
					}

					$register->post(\Input::get('invoice_no'));
					\DB::commit();
					$return_info['status'] = 'success';
					$return_info['message'] = $subl;//'Posting Successful';
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

	public function post(){
		$reg_repo =  \App::make('Financials\Register');
		
		$validate_record = $reg_repo->pre_posting(\Input::all());

		if($validate_record['passed'] > 0){

			$post_check = $this->prePostCheck(\Input::only('amount_request','account_amount','entry_type'));

			if($post_check['passed']){

				$return_info = array('status' => null, 'message' => null);

				try{
					\DB::beginTransaction();
				   $entity = \Company::where('alias', \Session::get('company'))->first()->id;
				   $journal_repo = \App::make('Financials\Journal');

				   $entries =  $this->makeAccountingEntries(\Input::only('account','account_amount','entry_type'));

				   $journal = $journal_repo->create(array('entity' => $entity,'module' => '1','reference' => \Input::get('invoice_no'), 'total_amount' => \Input::get('amount_request'),
								'post_data' => $entries));//$this->preparelines(\Input::get('account'), \Input::get('account_amount'))));
					
					if($journal){
						$genledger_repo = \App::make('Financials\GenLedger');
						$gl = $genledger_repo->create(array('entity' => $entity, 'module' => '1','reference' => \Input::get('invoice_no'), 'total_amount' => \Input::get('amount_request'),
									'post_data' => $entries));

						if($gl){
							$subledger_repo = \App::make('Financials\SubLedger');
							$subl = $subledger_repo->create(array('entity' => $entity, 'reference' => \Input::get('invoice_no'), 'credit' => 0,
									'debit' => \Input::get('amount_request'), 'balance' =>  0, 'vendor' => \Input::get('payee_name')));//$this->register->findByRegId(\Input::get('invoice_no'))->reference->supplier->supplier_name));

						}
					}
					$reg_repo->post(\Input::get('invoice_no'));
					\DB::commit();
					$return_info['status'] = 'success';
					$return_info['message'] = 'Posting Successful';
				}catch(\PDOException $e){
					\DB::rollBack();
					$return_info['status'] = 'success_failed';
					$return_info['message'] = 'Transaction Failed, Please contact System Administrator';
				}
				
				return \Response::json($return_info);
				// return \Response::json($this->prePostCheck(\Input::only('amount_request','account_amount','account_action')));
			}

			else return \Response::json(array('status' => 'success_failed', 'message' => $post_check['message']));
			
		}

		else if($validate_record['passed'] == 0)
			return \Response::json(array('status' => 'success_error', 'message' => $validate_record['object']));
		
		else return \Response::json(array('status' => 'success_failed', 'message' => $validate_record['object']));

		// $posting = $this->register->post(\Input::get('invoice_no'));
		
		// if($posting['saved'] > 0)
		// 	return \Response::json(array('status' => 'success', 'message' => 'Invoice Posted'));
	
		// return \Response::json($journal);
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

	private function makeAccountingEntries($data){
		$accounts = array_get($data, 'account');
		$amounts = array_get($data, 'account_amount');
		$action = array_get($data, 'entry_type');
		$lines = array();

		$init = 0;

		foreach ($accounts as $account) {
			$line = array();
			$line['account'] = $account;

			if($action[$init] == 0){
				$line['debit'] = $amounts[$init];
				$line['credit'] = 0;
			}
				
			else{
				$line['debit'] = 0;
				$line['credit'] = $amounts[$init];
			}

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

	private function prePostCheck($input){
		$total = array_get($input,'amount_request');
		$total_debit = array();
		$total_credit = array();
		$post_action = array_get($input,'entry_type');

		if(!in_array('0',$post_action))
			return array('passed' => false,'message'=>"Debit account is required");
		else if(!in_array('1', $post_action))
			return array('passed' => false,'message'=>"Credit account is required");
		else{
			
			$ctr = 0;
			$actions = array_get($input, 'entry_type'); 

			foreach (array_get($input, 'account_amount') as $amount) {
				if($actions[$ctr] == 0)
					array_push($total_debit, $amount);
				else
					array_push($total_credit, $amount);
	
				$ctr++;
			}

			if(array_sum($total_credit) == array_sum($total_debit)){
				if(array_sum($total_credit) == $total)
					return array('passed' => true,'message'=>"passed");
				else
					return array('passed' => false,'message'=>"Invoice amount did not match the total of accounts' amount");
			}
			else return array('passed' => false,'message'=>"total credit and debit amount must be equal");
				
		} 
	}
}