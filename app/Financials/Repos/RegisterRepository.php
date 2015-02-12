<?php

namespace Financials\Repos;

use Financials\Entities\Register;

class RegisterRepository implements RegisterRepositoryInterface {
	
	public function selectAll(){
		
	}

	public function find($id){
		
	}

	public function findByRef($reference){
		return Register::where('register_refno', $reference)->where('register_post', 'N')->get();
	}

	public function findByRegId($reg_id){
		return Register::company()->where('register_id', $reg_id)->first();
	}

	public function getRecord($ref){
		$fields = array('id','register_id','register_refno','account_value', 'po_id');
		return Register::where('register_id',$ref)->company()->aging()->with('reference.supplier','rfp')->get($fields);
		//return \DB::getQueryLog();
	}

	public function getOpenRecord($ref){
		$fields = array('id','register_id','register_refno','account_value', 'po_id');
		return Register::where('register_id',$ref)->company()->open()->with('reference.supplier','rfp')->get($fields);
		//return \DB::getQueryLog();
	}

	public function getSIRecord($ref){ //Supplier Invoice
		$fields = array('id','register_id','register_refno','account_value', 'po_id');
		return Register::where('register_id',$ref)->company()->aging()->with('customer')->get($fields);
	}

	public function getOpenSIRecord($ref){ //Customer Invoice
		$fields = array('id','register_id','register_refno','account_value','invoice_date','po_id');
		return Register::where('register_id',$ref)->company()->open()->with('customer','sales_lines')->get($fields);
		//return \DB::getQueryLog();
	}

	public function getOpenReceiptRecord($ref){ //Customer Invoice
		$fields = array('id','account_id','register_id','register_refno','account_value','invoice_date','po_id');
		return Register::where('register_id',$ref)->company()->module(4)->open()->with('customer')->get($fields);
		//return \DB::getQueryLog();
	}

	public function getAging($module){
		$fields = array('id','register_id','module_id','account_id','register_refno','register_post','account_value', 'po_id' ,'created_at');
		$object = NULL; 
		if($module == 1)
			$object = Register::company()->module($module)->aging()->with('reference.supplier','rfp')->get($fields);
		// else if($module == 4)
		// 	$object = Register::company()->module($module)->aging()->with('customer')->get(array('id','register_id','module_id','account_id','register_refno','register_post','account_value', 'po_id' ,'created_at', 'invoice_date'));
		else $object = Register::company()->module($module)->aging()->with('customer')->get(array('id','register_id','module_id','account_id','register_refno','register_post','account_value', 'po_id' ,'created_at', 'invoice_date'));

		return $object;
	}

	public function getAgingAR(){
		return Register::company()->module(2)->aging()->with('customer','sales_receipt')->get(array('id','register_id','module_id','account_id','register_refno','register_post','account_value', 'po_id' ,'created_at', 'invoice_date'));
	}

	public function getVerifiedReceipts(){
		return Register::company()->module(4)->aging()->with('customer')->get(array('id','register_id','module_id','account_id','register_refno','register_post','account_value', 'po_id' ,'created_at', 'invoice_date'));
	}

	public function getOpen($module){
		$fields = array('id','register_id','module_id','account_id','register_refno','register_post','account_value', 'po_id' ,'created_at');
		return Register::company()->module($module)->open()->with('reference.supplier','rfp')->get($fields);
	}

	public function getOpenSI($module){
		$fields = array('id','register_id','module_id','account_id','register_post','account_value', 'po_id' ,'created_at', 'invoice_date');
		return Register::company()->module($module)->open()->with('customer')->get($fields);
	}

	public function getAll($module){
		$fields = array('id','register_id','module_id','account_id','register_refno','register_post','account_value', 'po_id' ,'created_at');
		return Register::company()->module($module)->with('reference.supplier','rfp')->get($fields);
	}

	public function getAllNoRfp(){//has('register','<',1)
		return Register::company()->has('rfp','<',1)->get();
	}

	private function entries_count(){
		return \DB::table('accounting_register')->count();
	}

	public function create($data){
		$continue = true;
		

		$register = new Register;

		$register->register_id = array_get($data,'prefix') . \Helpers::recordNumGen($this->entries_count() + 1);//array_get($data,'ref') . "-" . ($this->entries_count() + 1);
		$register->module_id = array_get($data,'module_id');

		if(isset($data['account']))
			$register->account_id = array_get($data,'account');
		else
			$register->account_id = 3;

		if(isset($data['receipt']))
			$register->receipt_id = Register::company()->where('register_id',array_get($data,'receipt'))->first()->id;

		$register->po_id = array_get($data,'ref_id');


		if(isset($data['amount']))
			$register->account_value = array_get($data,'amount');

		else{
			if(isset($data['line_amounts'])){
				$sum_amt = 0;

				foreach (array_get($data,'line_amounts') as $amt) {
					if(preg_match('/^([1-9][0-9]*|0)(\.[0-9]{2})?$/', $amt)) //check if amounts are valid
						$sum_amt += $amt;

					else{
						$continue = false;
						break;
					}
						
				}
				$register->account_value = array_sum($data['line_amounts']);
			}
				
			
		}

		if(isset($data['refno']))
			$register->register_refno = array_get($data, 'refno');

		if(isset($data['invoice_date']))
			$register->invoice_date = array_get($data, 'invoice_date');

		if($continue){

			if(strcmp(array_get($data, 'trans_type'), "sales_entry") == 0){
				$register->account = array_get($data, 'line_accounts');
				$register->account_amount = array_get($data, 'line_amounts');
				$register->account_description = array_get($data, 'line_descriptions');
			}	

			$filter = Register::validate($register->toArray(), array_get($data, 'trans_type'));

			if($filter->passes()) {
				
				if(isset($register->account)) unset($register->account);
				if(isset($register->account_amount)) unset($register->account_amount);
				if(isset($register->account_description)) unset($register->account_description);
	        	
				$this->save($register);

				return array('saved' => true, 'object' => $register);

	        }
	        else return array('saved' => false , 'object' => $filter->messages());
		}
		else
			return array('saved' => false, 'object' => array('amount' => "Please check line amounts"));
            
		
	}


	public function modify($ref,$data){
		$register = Register::where('register_id', $ref)->first();

		if($register->register_post == 'N'){
			$register->account_value = array_get($data,'amount_request');
			$register->register_refno = array_get($data,'register_refno');

			$filter = Register::validate($register->toArray(),'entry');

			if($filter->passes()) {
	        	
				$this->update($register);

				return array('saved' => 1, 'object' => $register);

	        }
	        else return array('saved' => 0 , 'object' => $filter->messages());
		}
		else return array('saved' => -1, 'object' => array('message' => 'Failed to apply changes'));
	}

	public function modify_SI($ref,$data){
		$register = Register::where('register_id', $ref)->first();
		$continue = true;
		if($register->register_post == 'N'){

			$register->po_id = array_get($data,'ref_id');

			$sum_amt = 0;

			foreach (array_get($data,'line_amounts') as $amt) {
				if(preg_match('/^([1-9][0-9]*|0)(\.[0-9]{2})?$/', $amt)) //check if amounts are valid
					$sum_amt += $amt;
				else{
					$continue = false;
					break;
				}
			}

			if($continue){
				$register->account_value = array_sum($data['line_amounts']);
			
				$register->register_refno = array_get($data, 'refno');
				$register->invoice_date = array_get($data, 'invoice_date');

				$register->account = array_get($data, 'line_accounts');
				$register->account_amount = array_get($data, 'line_amounts');
				$register->account_description = array_get($data, 'line_descriptions');

				$filter = Register::validate($register->toArray(),'sales_entry');

				if($filter->passes()) {
		        	
					unset($register->account);
					unset($register->account_amount);
					unset($register->account_description);

					$this->update($register);

					return array('saved' => 1, 'object' => $register);

		        }
		        else return array('saved' => 0 , 'object' => $filter->messages());
			}
			else return array('saved' => false, 'object' => array('amount' => "Please check line amounts"));
			
		}
		else return array('saved' => -1, 'object' => array('message' => 'Failed to apply changes'));
	}

	public function modifyReceipt($ref,$data){
		$register = Register::where('register_id', $ref)->first();

		if($register->register_post == 'N'){
			$register->invoice_date = array_get($data, 'invoice_date');
			$register->account_id = array_get($data, 'account');

			$filter = Register::validate($register->toArray(),'receipt_entry');

			if($filter->passes()) {
	        	
				$this->update($register);

				return array('saved' => 1, 'object' => $register);

	        }
	        else return array('saved' => 0 , 'object' => $filter->messages());
		}
		else return array('saved' => -1, 'object' => array('message' => 'Failed to apply changes'));
	}

	public function post($ref){
		$register = Register::where('register_id', $ref)->first();

		// if($register->register_post == 'N'){
			$register->register_post = 'Y';
			$register->register_date_posted = date("Y-m-d H:i:s");

			// $filter = Register::validate($register->toArray(),'post');

			// if($filter->passes()) {
				$this->update($register);
				return 1;
			// 	return array('saved' => 1, 'object' => $register);
			// }
			// else return array('saved' => 0 , 'object' => $filter->messages());
		//}
		// else return array('saved' => -1, 'object' => array('message' => 'Posting not permitted'));
	}

	public function pre_posting($ref){
		$register = Register::where('register_id', array_get($ref, 'invoice_no'))->first();
		$register->account = array_get($ref, 'account');
		$register->account_amount = array_get($ref, 'account_amount');

		if($register->register_post == 'N'){
			$filter = Register::validate($register->toArray(),'post');

			if($filter->passes()) 
				return array('passed' => 1, 'object' => $register);
			
			else return array('passed' => 0 , 'object' => $filter->messages());
		}
		else return array('passed' => -1, 'object' => array('message' => 'Record already posted'));
	}

	public function pre_posting_ar($ref){
		$register = Register::where('register_id', array_get($ref, 'invoice_no'))->first();

		if($register->register_post == 'N'){
			 return array('passed' => 1, 'object' => $register);
		}
		else return array('passed' => -1, 'object' => array('message' => 'Record already posted'));
	}

	public function pre_posting_receipt($ref){
		$register = Register::where('register_id', array_get($ref, 'receipt_no'))->first();

		if($register->register_post == 'N'){
			 return array('passed' => 1, 'object' => $register);
		}
		else return array('passed' => -1, 'object' => array('message' => 'Record already posted'));
	}

	public function amount_total($amounts){
		//$check = Register::validate($amounts, '')
	}

	 /**
     * Simply saves the given instance
     *
     * @param  User $instance
     *
     * @return  boolean Success
     */
    public function save(Register $instance)
    {
        $entity = \Company::where('alias', \Session::get('company'))->first();

        $instance->context()->associate($entity);
         
         return $instance->save();

		// $comment = $post->comments()->save($comment);
  //       return $instance->save();
        //return $entity->invoices()->save($instance);
    }

     /**
     * Simply update the given instance
     *
     * @param  User $instance
     *
     * @return  boolean Success
     */
    public function update(Register $instance)
    { 
         return $instance->save();
     }
}