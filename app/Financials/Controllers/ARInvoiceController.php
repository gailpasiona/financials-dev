<?php

namespace Financials\Controllers;

use Financials\Repos\RegisterRepositoryInterface;
// use Financials\Repos\PurchasesRepositoryInterface;

//for checking, need to check dependency injections
class ARInvoiceController extends \BaseController{

	public function __construct(RegisterRepositoryInterface $register) {
		$this->beforeFilter('auth');
		//$this->beforeFilter('action_permission:invoice', array('except' => array('list_aging','getRegisterInfo','generate')));
		$this->beforeFilter('session');

		$this->register = $register;
		//$this->purchases = $purchases;
	}

	public function index(){
		// echo "AP Index";
		//return \Response::json($this->rfp->selectAll());
		return \View::make('financials.arinvoice')->with('user', \Confide::user()->username);
	}

	public function edit($record){
		$data = $this->register->getOpenSIRecord($record);

		$repo = \App::make('Financials\Supplier');
		$coa_repo = \App::make('Financials\Coa');

		$register_info = array();

		$register_info['customer'] = $repo->selectAll();
		$register_info['coa_list'] = $coa_repo->selectAll();//getAccountsByGroup('4');

		$register_info['invoice_no'] = $data[0]['register_id'];
		$register_info['payee'] = $data[0]['customer']['id'];
		$register_info['invoice_date'] = $data[0]['invoice_date'];
		$register_info['register_refno'] = $data[0]['register_refno'];
		$register_info['lines'] = $data[0]['sales_lines'];
		$register_info['title'] = "Modify Invoice " . $data[0]['register_id'];
		
		return \View::make('financials.modals.form_billing')->with('data',$register_info);
	}

	public function update($invoice){
		$return_info = array();

		try{
			\DB::beginTransaction();
			$data = array('ref_id' =>\Input::get('payee'), 'invoice_date' => \Input::get('invoice_date'), 
				'entry_type' => \Input::get('entry_type'),'line_amounts' => \Input::get('account_amount'), 
				'line_accounts' => \Input::get('account'),'line_descriptions' => \Input::get('line_description'),
				'refno' => \Input::get('register_refno'));

			$record = $this->register->modify_SI($invoice,$data);
			
			if($record['saved'] > 0){
				$lines_repo = \App::make('Financials\InvoiceLine');

				$parsed_lines = $this->parse_lines(\Input::only('entry_type','account_amount','account','line_description'));

				$lines = $lines_repo->updateLines($parsed_lines,$this->register->findByRegId($invoice)->id);

				if($lines){
					\DB::commit();
					$return_info['status'] = 'success';
					$return_info['message'] = 'Invoice updated!';
				}
				else{
					$return_info['status'] = 'success_error';
					$return_info['message'] = 'Unable to complete transaction';
				}

			}
				

			else if($record['saved'] == 0){
				$return_info['status'] = 'success_error';
				$return_info['message'] =$record['object'];
			}
				// return \Response::json(array('status' => 'success_error', 'message' => $record['object']));

			else{
				$return_info['status'] = 'success_failed';
				$return_info['message'] =$record['object'];
				// return \Response::json(array('status' => 'success_failed', 'message' => $record['object']));
			}

		}catch(\PDOException $e){
			\DB::rollBack();
			$return_info['status'] = 'success_failed';
			$return_info['message'] = $e->getmessage();
		}

		
		 return \Response::json($return_info);
	}

	public function posting($invoice){
		$repo = \App::make('Financials\Purchases');
		$journal = $repo->find(\Input::get('reference'));

		$coa_repo = \App::make('Financials\Coa');

		$data = $this->register->getOpenSIRecord($invoice);

		$register_info = array();
		$register_info['coa_list'] = $coa_repo->selectAll();//getAccountsByGroup('4');

		$register_info['invoice'] = $data[0]['register_id'];
		$register_info['amount'] = $data[0]['account_value'];
		$register_info['register_refno'] = $data[0]['register_refno'];
		$register_info['payee'] = $data[0]['customer']['supplier_name'];
		$register_info['lines'] = $data[0]['sales_lines'];
		$register_info['title'] = "Complete Invoice " . $data[0]['register_id'];
		$register_info['extra'] = $data[0]['sales_lines'];


		return \View::make('financials.modals.form_post_ar')->with('data',$register_info);
	}

	public function post(){
		$validate_record = $this->register->pre_posting_ar(\Input::get('invoice_no'));

		if($validate_record['passed'] > 0){
			$post_check = $this->prePostCheck(\Input::only('account','subject_payment'));
			if($post_check['passed']){
				$return_info = array('status' => null, 'message' => null);
				try{
					\DB::beginTransaction();
					// $header_account = \App::make('Financials\Coa')->findByName('Accounts Receivable');
				    $entity = \Company::where('alias', \Session::get('company'))->first()->id;

				    $journal_repo = \App::make('Financials\Journal');

				    $entries =  $this->makeAccountingEntries(\Input::only('account','account_amount','entry_type'));
					
					// $journal = $journal_repo->create(array('entity' => $entity,'module' => '2','reference' => \Input::get('invoice_no'), 'total_amount' => \Input::get('amount_request'),
					// 			'post_data' => $this->preparelines(\Input::get('account'), \Input::get('account_amount')), 
					// 			'header_account' => $header_account->account_id, 'header_debit' => \Input::get('amount_request'), 'header_credit' => 0));
					
				    $journal = $journal_repo->create(array('entity' => $entity,'module' => '2','reference' => \Input::get('invoice_no'), 'total_amount' => \Input::get('amount_request'),
								'post_data' => $entries));

					if($journal){
						$genledger_repo = \App::make('Financials\GenLedger');
						// $gl = $genledger_repo->create(array('entity' => $entity, 'module' => '2','reference' => \Input::get('invoice_no'), 'total_amount' => \Input::get('amount_request'),
						// 			'post_data' => $this->preparelines(\Input::get('account'), \Input::get('account_amount')),
						// 			'header_account' => $header_account->account_id, 'header_debit' => \Input::get('amount_request'), 'header_credit' => 0));
						
						$gl = $genledger_repo->create(array('entity' => $entity, 'module' => '2','reference' => \Input::get('invoice_no'), 'total_amount' => \Input::get('amount_request'),
									'post_data' => $entries));
						if($gl){
							$lines_repo = \App::make('Financials\InvoiceLine');

							$tagged_line_amount = $lines_repo->tagLine(\Input::get('subject_payment'),$this->register->findByRegId(\Input::get('invoice_no'))->id);
						
							$subledger_repo = \App::make('Financials\SubLedger');
							// $subl = $subledger_repo->create(array('entity' => $entity, 'reference' => \Input::get('invoice_no'), 'credit' => \Input::get('amount_request'),
							// 		'debit' => 0, 'balance' =>  \Input::get('amount_request'), 'vendor' => $this->register->findByRegId(\Input::get('invoice_no'))->reference->supplier->supplier_name));

							// $subledger_repo = \App::make('Financials\SubLedger');
							// $sub_amt = $this->extractAR(\Input::only('account','account_amount'));
							$subl = $subledger_repo->create(array('entity' => $entity, 'reference' => \Input::get('invoice_no'), 'credit' => $tagged_line_amount,
									'debit' => 0, 'balance' =>  $tagged_line_amount, 'vendor' => \Input::get('payee_name')));
						}
					}

					$this->register->post(\Input::only('invoice_no'));
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

			else return \Response::json(array('status' => 'success_failed', 'message' => $post_check['message']));

		}

		else if($validate_record['passed'] == 0)
			return \Response::json(array('status' => 'success_error', 'message' => $validate_record['object']));
		
		else return \Response::json(array('status' => 'success_failed', 'message' => $validate_record['object']));
		
				
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
	
		// $accounts = array_get($input, 'account');
		
		// if(count(array_unique($accounts)) == count($accounts))
		if(isset($input['subject_payment']))
			return array('passed' => true,'message'=> null);
		else return array('passed' => false,'message'=>"Please select the amount subject for payment");

		// else return array('passed' => false,'message'=>"duplicate coa entry detected");

		
	}

	private function extractAR($data){
		$header_account = \App::make('Financials\Coa')->findByName('Accounts Receivable')->account_id;
		$return_value = null;
		$ctr = 0;
		foreach (array_get($data, 'account') as $account) {
			if($account == $header_account){
				// $return_value = array('account_id' => $line['account_id'], 'amount' => $line['line_amount'],
				// 	['description'] => 'N/A');
				$return_value = $data['account_amount'][$ctr];

				break;
			}

			$ctr ++;
		}

		return $return_value;

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

	public function store(){
		// $lines =  $this->compute_line_total(\Input::only('account_amount'));
		// return \Response::json($lines);
		$return_info = array();

		try{

			\DB::beginTransaction();

			// $invoice = $this->register->create(array('trans_type' => 'sales_entry', 'amount'=>\Input::get('amount_request'), 
			// 	'ref_id' =>\Input::get('payee'), 'module_id' => '2', 'invoice_date' => \Input::get('invoice_date'),
			// 	'register_refno' => \Input::get('register_refno'),
			// 	'prefix' => 'SI', 'line_amounts' => \Input::get('account_amount'), 'line_accounts' => \Input::get('account'),
			// 	'line_descriptions' => \Input::get('account_description')));

			// $invoice = $this->register->create(array('trans_type' => 'entry', 'amount'=> \Input::get('amount_request'), 
			// 		'ref_id' =>$payable->id, 'refno' => \Input::get('register_refno'), 'module_id' => '1',
			// 		'prefix' => 'AP', 'entry_type' => \Input::get('entry_type'), 'line_accounts' => \Input::get('account'),
			// 		'line_amounts' => \Input::get('account_amount'), 'line_descriptions' => \Input::get('line_description'),
			// 		'invoice_date' => \Input::get('invoice_date')));

			$invoice = $this->register->create(array('trans_type' => 'sales_entry', 
					'ref_id' =>\Input::get('payee'), 'refno' => \Input::get('register_refno'), 'module_id' => '2',
					'prefix' => 'AR', 'entry_type' => \Input::get('entry_type'), 'line_accounts' => \Input::get('account'),
					'line_amounts' => \Input::get('account_amount'), 'line_descriptions' => \Input::get('line_description'),
					'invoice_date' => \Input::get('invoice_date')));

			//\DB::commit();

			if($invoice['saved']){
					// $sdd = $repo->updateById(\Input::get('reference'));
					//return \Response::json(array('status' => 'success', 'message' => 'Invoice Created'));
				$lines_repo = \App::make('Financials\InvoiceLine');

				$parsed_lines = $this->parse_lines(\Input::only('entry_type','account_amount','account','line_description'));

				$lines = $lines_repo->create($parsed_lines,$invoice['object']->id);
				
				if($lines){
					\DB::commit();
					$return_info['status'] = 'success';
					$return_info['message'] = 'Invoice created!';
				}
				else{
					$return_info['status'] = 'success_error';
					$return_info['message'] = 'Unable to process transaction';
				}

			}
			else{
				$return_info['status'] = 'success_error';
				$return_info['message'] = $invoice['object'];
			}

		}catch(\PDOException $e){
			\DB::rollBack();
			$return_info['status'] = 'success_failed';
			$return_info['message'] = $e->getmessage();//'Transaction Failed, Please contact System Administrator';
		}

		return \Response::json($return_info);

			// if($invoice['saved']){
			// 		// $sdd = $repo->updateById(\Input::get('reference'));
			// 		return \Response::json(array('status' => 'success', 'message' => 'Invoice Created'));
			// }
			// else
			 	// return \Response::json(array('status' => 'success_error', 'message' => $return_info));
	}

	public function show($ref){
		return \Response::json($this->register->getRecord($ref));
	}

	public function create(){
		$repo = \App::make('Financials\Supplier');
		// return "hello";
		$data['customer'] = $repo->selectAll();
		$data['title'] = 'Create Sales Invoice';
		return \View::make('financials.modals.form_billing')->with('data', $data);
	}
	public function receipt(){
		// echo "AP Index";
		//return \Response::json($this->rfp->selectAll());
		return \View::make('financials.arinvoice')->with('user', \Confide::user()->username);
	}

	public function list_receivables(){
		$type = \Input::get('type');
		$module = '2';
		$data = null;
		switch ($type) {
			case 'open':
				$data = $this->register->getOpenSI($module);
				break;
			case 'all':
				$data = $this->register->getAll($module);
				break;
			default:
				$temp_data = $this->register->getAgingAR($module);
				$store = array();
				foreach($temp_data as $filter){
					if(is_null(array_get($filter,'sales_receipt')))
						array_push($store, $filter);
				}

				$data = $store;
				break;
		}
		return \Response::json($data);
	}

	private function compute_line_total($lines){
		return array_sum($lines['account_amount']);
	}

	private function parse_lines($lines){ //AR Invoice Lines
		$ctr = 0;
		$bulk = array();

		foreach ($lines['account'] as $line ) {
			$bulk_line = array('account' => $line,'line' => $ctr,'description' => $lines['line_description'][$ctr],
				'amount' => $lines['account_amount'][$ctr], 'type' => ($lines['entry_type'][$ctr] == 0 ? 'D' : 'C'));

			array_push($bulk, $bulk_line);

			$ctr++;

		}

		return $bulk;
	}
}