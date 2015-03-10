<?php

namespace Financials\Controllers;

use Financials\Repos\RegisterRepositoryInterface;
// use Financials\Repos\PurchasesRepositoryInterface;

//for checking, need to check dependency injections
class ARReceiptController extends \BaseController{

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
		return \View::make('financials.arreceipt')->with('user', \Confide::user()->username);
	}

	public function edit($record){
		// $data = $this->register->getOpenReceiptRecord($record);

		// $repo = \App::make('Financials\Supplier');
		// $coa_repo = \App::make('Financials\Coa');

		// $register_info = array();

		// $register_info['record']['customer'] = $data;
		// $register_info['coa_list'] = $coa_repo->getAccountsByGroup('4');

		// $register_info['invoice_no'] = $data[0]['register_id'];
		// $register_info['payee'] = $data[0]['customer']['id'];
		// $register_info['invoice_date'] = $data[0]['invoice_date'];
		// $register_info['title'] = "Modify Invoice " . $data[0]['register_id'];

		// $s_invoice = \Input::get('billing_no');
		
		$coa_repo = \App::make('Financials\Coa');

		$data['record'] = $this->register->getOpenReceiptRecord($record)->toArray()[0];
		$data['coa'] = $coa_repo->selectAll();//getAccountsByGroup('1');
		$data['title'] = 'Modify Sales Receipt';

		return \View::make('financials.modals.form_sales_receipt')->with('data',$data);
	}

	public function update($invoice){
		$return_info = array();

		try{
			\DB::beginTransaction();
			$data = array('invoice_date' => \Input::get('receipt_date'), 'account' => \Input::get('account'), 'reference' => \Input::get('receipt_ref'));

			$record = $this->register->modifyReceipt($invoice,$data);
			
			if($record['saved'] > 0){
				$lines_repo = \App::make('Financials\InvoiceLine');

				// $parsed_lines = $this->parse_lines(\Input::only('account_amount','account','account_description'));

				$lines = $lines_repo->updateLines_receipt(\Input::get('account'),$this->register->findByRegId($invoice)->id);

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
		
		// $repo = \App::make('Financials\Purchases');
		// $journal = $repo->find(\Input::get('reference'));

		$coa_repo = \App::make('Financials\Coa');

		//$data = $this->register->getOpenSIRecord($invoice);
		$data['record'] = $this->register->getOpenReceiptRecord($invoice)->toArray()[0];
		$data['coa'] = $coa_repo->selectAll();//getAccountsByGroup('1');
		$data['title'] = 'Verify Sales Receipt';

		// $register_info = array();
		// $register_info['coa_list'] = $coa_repo->selectAll();//getAccountsByGroup('4');

		// $register_info['invoice'] = $data[0]['register_id'];
		// $register_info['amount'] = $data[0]['account_value'];
		// $register_info['payee'] = $data[0]['customer']['supplier_name'];
		// $register_info['lines'] = $data[0]['sales_lines'];
		// $register_info['title'] = "Complete Invoice " . $data[0]['register_id'];

		return \View::make('financials.modals.form_receipt_post')->with('data',$data);
	}

	public function post_old(){
		$validate_record = $this->register->pre_posting_receipt(\Input::all());

		if($validate_record['passed'] > 0){
			// if($this->prePost(\Input::get('amount_request'), \Input::get('account_amount'))){
				$return_info = array('status' => null, 'message' => null);
				try{
					\DB::beginTransaction();
					$header_account = \App::make('Financials\Coa')->findByName('Accounts Receivable');
				    $entity = \Company::where('alias', \Session::get('company'))->first()->id;

				    $journal_repo = \App::make('Financials\Journal');
					$journal = $journal_repo->create(array('entity' => $entity,'module' => '4','reference' => \Input::get('receipt_no'), 'total_amount' => \Input::get('amount'),
								'post_data' => array(array('account' => \Input::get('account'), 'debit' => \Input::get('amount'), 'credit' => 0)), 
								'header_account' => $header_account->account_id, 'header_debit' => 0, 'header_credit' => \Input::get('amount')));
					if($journal){
						$genledger_repo = \App::make('Financials\GenLedger');
						$gl = $genledger_repo->create(array('entity' => $entity, 'module' => '4','reference' => \Input::get('receipt_no'), 'total_amount' => \Input::get('amount'),
									'post_data' => array(array('account' => \Input::get('account'), 'debit' => \Input::get('amount'), 'credit' => 0)),
									'header_account' => $header_account->account_id, 'header_debit' => 0, 'header_credit' => \Input::get('amount')));
						// if($gl){
						// 	$subledger_repo = \App::make('Financials\SubLedger');
						// 	$subl = $subledger_repo->create(array('entity' => $entity, 'reference' => \Input::get('invoice_no'), 'credit' => \Input::get('amount_request'),
						// 			'debit' => 0, 'balance' =>  \Input::get('amount_request'), 'vendor' => $this->register->findByRegId(\Input::get('invoice_no'))->reference->supplier->supplier_name));

						// }
					}
					$this->register->post(\Input::only('receipt_no'));
					\DB::commit();
					$return_info['status'] = 'success';
					$return_info['message'] = 'Posting Successful';
				}catch(\PDOException $e){
					\DB::rollBack();
					$return_info['status'] = 'success_failed';
					$return_info['message'] = 'Transaction Failed, Please contact System Administrator';
				}


				return \Response::json($return_info);
		//	}

			// else return \Response::json(array('status' => 'success_failed', 'message' => 'Total amount does not match with the total of amount of each account'));

		}

		else if($validate_record['passed'] == 0)
			return \Response::json(array('status' => 'success_error', 'message' => $validate_record['object']));
		
		else return \Response::json(array('status' => 'success_failed', 'message' => $validate_record['object']));
		
				
	}

	public function post(){
		$validate_record = $this->register->pre_posting_receipt(\Input::get('receipt_no'));

		if($validate_record['passed'] > 0){
			$post_check = $this->prePostCheck(\Input::only('account'));
			if($post_check['passed']){
				$return_info = array('status' => null, 'message' => null);
				try{
					\DB::beginTransaction();
					// $header_account = \App::make('Financials\Coa')->findByName('Accounts Receivable');
				    $entity = \Company::where('alias', \Session::get('company'))->first()->id;

				    $journal_repo = \App::make('Financials\Journal');

				    $entries =  $this->makeAccountingEntries(\Input::only('account','account_amount','entry_type'));
					
					
				    $journal = $journal_repo->create(array('entity' => $entity,'module' => '4','reference' => \Input::get('receipt_no'), 'total_amount' => \Input::get('amount'),
								'post_data' => $entries));

					if($journal){
						$genledger_repo = \App::make('Financials\GenLedger');
						
						
						$gl = $genledger_repo->create(array('entity' => $entity, 'module' => '4','reference' => \Input::get('receipt_no'), 'total_amount' => \Input::get('amount'),
									'post_data' => $entries));
						if($gl){
						
							$subledger_repo = \App::make('Financials\SubLedger');
							
							$subl = $subledger_repo->create(array('entity' => $entity, 'reference' => \Input::get('receipt_no'), 'credit' => 0,
									'debit' => \Input::get('amount'), 'balance' =>  0, 'vendor' => \Input::get('payee')));
						}
					}

					$this->register->post(array('invoice_no' => \Input::get('receipt_no')));
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
	
		// if(isset($input['subject_payment']))
		// 	return array('passed' => true,'message'=> null);
		// else return array('passed' => false,'message'=>"Please select the amount subject for payment");
		return array('passed' => true,'message'=> null);

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

	public function store(){
		// $lines =  $this->compute_line_total(\Input::only('account_amount'));
		// return \Response::json($lines);
		$return_info = array();

		try{

			\DB::beginTransaction();

			$invoice = $this->register->create(array('trans_type' => 'receipt_entry', 'amount'=>\Input::get('amount'), 
				'ref_id' =>\Input::get('payee'), 'module_id' => '4', 'invoice_date' => \Input::get('receipt_date'),
				'prefix' => 'RCPT', 'account' => \Input::get('account'), 'receipt' => \Input::get('invoice_no'),
				'refno' => \Input::get('receipt_ref')));

			if($invoice['saved']){
					// $sdd = $repo->updateById(\Input::get('reference'));
					//return \Response::json(array('status' => 'success', 'message' => 'Invoice Created'));
				$lines_repo = \App::make('Financials\InvoiceLine');

				$parsed_lines = $this->parse_lines(\Input::only('debit_account','amount','account'));

				$lines = $lines_repo->create($parsed_lines,$invoice['object']->id);

				if($lines){
					\DB::commit();
					return \Response::json(array('status' => 'success', 'message' => 'Receipt Created'));
				}
				else return \Response::json(array('status' => 'success_failed', 'message' => 'Unable to record receipt'));
					
			}
			else
				return \Response::json(array('status' => 'success_error', 'message' => $invoice['object']));

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
		//$repo = \App::make('Financials\Supplier');
		// return "hello";
		$s_invoice = \Input::get('billing_no');
		
		$coa_repo = \App::make('Financials\Coa');

		$data['record'] = $this->register->getSIRecord($s_invoice)->toArray()[0];
		$data['coa'] = $coa_repo->getAccountsByGroup('1');
		$data['title'] = 'Create Sales Receipt';
		$data['type'] = 0;

		return \View::make('financials.modals.form_sales_receipt')->with('data', $data);
	}
	public function receipt(){
		// echo "AP Index";
		//return \Response::json($this->rfp->selectAll());
		return \View::make('financials.arinvoice')->with('user', \Confide::user()->username);
	}

	public function list_receipts(){
		$type = \Input::get('type');
		$module = '4';
		$data = null;
		switch ($type) {
			case 'open':
				$data = $this->register->getOpenSI($module);
				break;
			case 'all':
				$data = $this->register->getAll($module);
				break;
			default:
				$data = $this->register->getVerifiedReceipts($module);
				break;
		}
		return \Response::json($data);
	}

	private function compute_line_total($lines){
		return array_sum($lines['account_amount']);
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

	private function parse_lines($lines){
		$ctr = 0;
		$bulk = array();


		//debit to account receivables
		array_push($bulk, array('account' => array_get($lines, 'debit_account'),'line' => '0','description' => 'Debit to Receivables',
				'amount' => array_get($lines, 'amount'), 'type' => 'D'));

		//credit to bank
		array_push($bulk, array('account' => array_get($lines, 'account'),'line' => '1','description' => 'Credit to Bank',
				'amount' => array_get($lines, 'amount'), 'type' => 'C'));


		return $bulk;
	}
}