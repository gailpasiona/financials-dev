<?php

namespace Financials\Controllers;

use Financials\Repos\RegisterRepositoryInterface;
// use Financials\Repos\PurchasesRepositoryInterface;

//for checking, need to check dependency injections
class APInvoiceController extends \BaseController{
	
	public function __construct(RegisterRepositoryInterface $register) {
		$this->beforeFilter('auth');
		$this->beforeFilter('action_permission:invoice', array('except' => array('list_aging','getRegisterInfo','generate')));
		$this->beforeFilter('session');

		$this->register = $register;
		//$this->purchases = $purchases;
	}

	public function index(){
		// echo "AP Index";
		//return \Response::json($this->rfp->selectAll());
		return \View::make('financials.apinvoice')->with('user', \Confide::user()->username);
	}

	public function create(){
		
	}

	public function store_old(){
		$repo = \App::make('Financials\Purchases');
		// return \Response::json(array($this->purchases->find(\Input::get('reference'))->po_number,
		// 	$this->purchases->find(\Input::get('reference'))->po_total_amount));
		$payable = $repo->findByPO(\Input::get('po_reference'));
		
		if($payable->openforinvoice->isEmpty()){
			$invoice = $this->register->create(array('trans_type' => 'entry', 'amount'=>\Input::get('amount_request'), 
				'ref_id' =>$payable->id, 'refno' => \Input::get('register_refno'), 'module_id' => '1',
				'prefix' => 'INV'));

			if($invoice['saved']){
					// $sdd = $repo->updateById(\Input::get('reference'));
					return \Response::json(array('status' => 'success', 'message' => 'Invoice Created'));
			}
			else
				return \Response::json(array('status' => 'success_error', 'message' => $invoice['object']));
		}
		else return \Response::json(array('status' => 'success_failed', 'message' => 'Unable to created invoice (record has unposted invoice)'));
	}

	public function store(){
		$repo = \App::make('Financials\Purchases');
		$payable = $repo->findByPO(\Input::get('po_reference'));

		try{
			\DB::beginTransaction();
			if($payable->openforinvoice->isEmpty()){
				$invoice = $this->register->create(array('trans_type' => 'entry', 'amount'=>\Input::get('amount_request'), 
					'ref_id' =>$payable->id, 'refno' => \Input::get('register_refno'), 'module_id' => '1',
					'prefix' => 'AP', 'entry_type' => \Input::get('entry_type'), 'line_accounts' => \Input::get('account'),
					'line_amounts' => \Input::get('account_amount'), 'line_descriptions' => \Input::get('line_description'),
					'invoice_date' => \Input::get('invoice_date')));

				if($invoice['saved']){
					// $sdd = $repo->updateById(\Input::get('reference'));
					// return \Response::json(array('status' => 'success', 'message' => 'Invoice Created'));
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
					return \Response::json($return_info);
				}
				else
					return \Response::json(array('status' => 'success_error', 'message' => $invoice['object']));

			}

			else
				return \Response::json(array('status' => 'success_failed', 'message' => 'Unable to created invoice (record has unposted invoice)'));

		}catch(\PDOException $e){
			\DB::rollBack();
			$return_info['status'] = 'success_failed';
			$return_info['message'] = $e->getmessage();

			return \Response::json($return_info);
		}

	}

	public function show($ref){
		return \Response::json($this->register->getRecord($ref));
	}

	public function edit($record){
		//$repo = \App::make('Financials\Register');//need to verify if binding is necessary
		$data = $this->register->getOpenRecord($record);

		$coa_repo = \App::make('Financials\Coa');

		$register_info = array();

		$register_info['coa_list'] = $coa_repo->selectAll();//getAccountsBySub(array('3','4','5','6','7'));
		$register_info['cost_dept'] = $data[0]['reference']['requestor'];
		$register_info['invoice_no'] = $data[0]['register_id'];
		$register_info['invoice_date'] = $data[0]['invoice_date'];
		$register_info['amount_request'] = $data[0]['account_value'];
		$register_info['payee_name'] = $data[0]['reference']['supplier']['supplier_name'];
		$register_info['register_refno'] = $data[0]['register_refno'];
		$register_info['title'] = "Modify Invoice " . $data[0]['register_id'];
		$register_info['lines'] = $data[0]['lines'];

		return \View::make('financials.modals.form_invoice')->with('data',$register_info);
	}

	public function update($invoice){
		$return_info = array();
		
		try{
			\DB::beginTransaction();

			$input = array('amount_request'=>\Input::get('amount_request'), 
				'register_refno' => \Input::get('register_refno'), 
				'entry_type' => \Input::get('entry_type'), 'line_accounts' => \Input::get('account'),
				'line_amounts' => \Input::get('account_amount'), 'line_descriptions' => \Input::get('line_description'),
				'invoice_date' => \Input::get('invoice_date'));

			$record = $this->register->modify($invoice, $input);//\Input::all());

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

			else if($record['saved'] == 0)
				return \Response::json(array('status' => 'success_error', 'message' => $record['object']));

			else return \Response::json(array('status' => 'success_failed', 'message' => $record['object']));

		}catch(\Exception $e){
			\DB::rollBack();
			$return_info['status'] = 'success_failed';
			$return_info['message'] = $e->getmessage();
		}
		
		return \Response::json($return_info);
	}

	public function generate(){
		$repo = \App::make('Financials\Purchases');
		$payable = $repo->find(\Input::get('reference'));

		//return \Response::json($payable);

		$register_info = array();

		$register_info['cost_dept'] = $payable['requestor'];
		$register_info['amount_request'] = $payable['po_total_amount'];
		$register_info['payee_name'] = $payable['supplier']['supplier_name'];
		$register_info['po_reference'] = $payable['po_number'];
		$register_info['title'] = "Create Invoice";

		return \View::make('financials.modals.form_invoice')->with('data',$register_info);


	}

	public function old_generate(){
		$repo = \App::make('Financials\Purchases');
		// return \Response::json(array($this->purchases->find(\Input::get('reference'))->po_number,
		// 	$this->purchases->find(\Input::get('reference'))->po_total_amount));
		$payable = $repo->find(\Input::get('reference'));
		
		if($payable->openforinvoice->isEmpty()){
			$invoice = $this->register->create(array('ref'=>$payable->po_number,
			'amount'=>$payable->po_total_amount, 'ref_id' =>$payable->id));

			if($invoice->id){
					$sdd = $repo->updateById(\Input::get('reference'));
					return 'Invoice generated!';
			}
			else
				return 'Invoice Failed';
		}
		else return 'Has Open Invoice';
		// return 
		
	}

	public function posting($invoice){
		$repo = \App::make('Financials\Purchases');
		$journal = $repo->find(\Input::get('reference'));

		$data = $this->register->getOpenRecord($invoice);
		
		$coa_repo = \App::make('Financials\Coa');

		$register_info = array();

		$register_info['coa_list'] = $coa_repo->selectAll();//getAccountsBySub(array('3','4','5','6','7'));
		$register_info['invoice'] = $data[0]['register_id'];
		$register_info['amount'] = $data[0]['account_value'];
		$register_info['payee'] = $data[0]['reference']['supplier']['supplier_name'];
		$register_info['refno'] = $data[0]['register_refno'];
		$register_info['title'] = "Post Invoice " . $data[0]['register_id'];
		$register_info['lines'] = $data[0]['lines'];

		return \View::make('financials.modals.form_post')->with('data',$register_info);
	}

	public function post(){
		
		$validate_record = $this->register->pre_posting(\Input::all());

		if($validate_record['passed'] > 0){

			$post_check = $this->prePostCheck(\Input::only('account','amount_request','account_amount','entry_type'));

			if($post_check['passed']){

				$return_info = array('status' => null, 'message' => null);

				try{
					\DB::beginTransaction();
				// 	$header_account = \App::make('Financials\Coa')->findByName('Accounts Payable');
				//    // $header_account = $coa_repo->findByName('Accounts Payable');
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
							$sub_amt = $this->extractAP(\Input::only('account','account_amount'));
							$subl = $subledger_repo->create(array('entity' => $entity, 'reference' => \Input::get('invoice_no'), 'credit' => $sub_amt,
									'debit' => 0, 'balance' =>  $sub_amt, 'vendor' => $this->register->findByRegId(\Input::get('invoice_no'))->reference->supplier->supplier_name));

						}
					}
					$this->register->post(\Input::get('invoice_no'));
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

	private function extractAP($data){
		$header_account = \App::make('Financials\Coa')->findByName('Accounts Payable')->account_id;
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
			$line['debit'] = $amounts[$init];
			$line['credit'] = 0;
			array_push($lines, $line);
			$line = null;
			$init++;
		}
		return $lines;
	}

	private function parse_lines($lines){ //AP Invoice Lines
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
		$accounts = array_get($input, 'account');

		if(count(array_unique($accounts)) == count($accounts)){
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
		else return array('passed' => false,'message'=>"duplicate coa account detected");

		
	}

	public function list_aging(){
		$type = \Input::get('type');
		$module = '1';
		$data = null;
		switch ($type) {
			case 'open':
				$data = $this->register->getOpen($module);
				break;
			case 'all':
				$data = $this->register->getAll($module);
				break;
			default:
				$data = $this->register->getAging($module);
				break;
		}
		return \Response::json($data);
	}

	public function getRegisterInfo($ref){
		return \Response::json($this->register->getRecord($ref));
	}
}