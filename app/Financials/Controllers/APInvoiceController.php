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
		// return "hello";
	}

	public function store(){
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

	public function show($ref){
		return \Response::json($this->register->getRecord($ref));
	}

	public function edit($record){
		//$repo = \App::make('Financials\Register');//need to verify if binding is necessary
		$data = $this->register->getOpenRecord($record);

		$register_info = array();

		$register_info['cost_dept'] = $data[0]['reference']['requestor'];
		$register_info['invoice_no'] = $data[0]['register_id'];
		$register_info['amount_request'] = $data[0]['account_value'];
		$register_info['payee_name'] = $data[0]['reference']['supplier']['supplier_name'];
		$register_info['register_refno'] = $data[0]['register_refno'];
		$register_info['title'] = "Modify Invoice " . $data[0]['register_id'];

		return \View::make('financials.modals.form_invoice')->with('data',$register_info);
	}

	public function update($record){

		$record = $this->register->modify($record,\Input::all());
		
		if($record['saved'] > 0)
			return \Response::json(array('status' => 'success', 'message' => 'Invoice Updated'));

		else if($record['saved'] == 0)
			return \Response::json(array('status' => 'success_error', 'message' => $record['object']));

		else return \Response::json(array('status' => 'success_failed', 'message' => $record['object']));
		
		// return \Response::json($record);
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

		$register_info = array();

		$register_info['invoice'] = $data[0]['register_id'];
		$register_info['amount'] = $data[0]['account_value'];
		$register_info['payee'] = $data[0]['reference']['supplier']['supplier_name'];
		$register_info['refno'] = $data[0]['register_refno'];
		$register_info['title'] = "Post Invoice " . $data[0]['register_id'];

		return \View::make('financials.modals.form_post')->with('data',$register_info);
	}

	public function post(){
		
		$validate_record = $this->register->pre_posting(\Input::all());

		if($validate_record['passed'] > 0){
			if($this->prePost(\Input::get('amount_request'), \Input::get('account_amount'))){

				$return_info = array('status' => null, 'message' => null);

				try{
					\DB::beginTransaction();
					$header_account = \App::make('Financials\Coa')->findByName('Accounts Payable');
				   // $header_account = $coa_repo->findByName('Accounts Payable');
				   $entity = \Company::where('alias', \Session::get('company'))->first()->id;
				   $journal_repo = \App::make('Financials\Journal');
					$journal = $journal_repo->create(array('entity' => $entity,'module' => '1','reference' => \Input::get('invoice_no'), 'total_amount' => \Input::get('amount_request'),
								'post_data' => $this->preparelines(\Input::get('account'), \Input::get('account_amount')), 
								'header_account' => $header_account->account_id, 'header_debit' => 0, 'header_credit' => \Input::get('amount_request')));
					if($journal){
						$genledger_repo = \App::make('Financials\GenLedger');
						$gl = $genledger_repo->create(array('entity' => $entity, 'module' => '1','reference' => \Input::get('invoice_no'), 'total_amount' => \Input::get('amount_request'),
									'post_data' => $this->preparelines(\Input::get('account'), \Input::get('account_amount')),
									'header_account' => $header_account->account_id, 'header_debit' => 0, 'header_credit' => \Input::get('amount_request')));
						if($gl){
							$subledger_repo = \App::make('Financials\SubLedger');
							$subl = $subledger_repo->create(array('entity' => $entity, 'reference' => \Input::get('invoice_no'), 'credit' => \Input::get('amount_request'),
									'debit' => 0, 'balance' =>  \Input::get('amount_request'), 'vendor' => $this->register->findByRegId(\Input::get('invoice_no'))->reference->supplier->supplier_name));

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
			}

			else return \Response::json(array('status' => 'success_failed', 'message' => 'Total amount does not match with the total of amount of each account'));
			
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
			$line['debit'] = $amounts[$init];
			$line['credit'] = 0;
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