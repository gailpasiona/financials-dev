<?php

namespace Financials\Repos;

use Financials\Entities\Rfp;
use Financials\Entities\Register as Invoice;

class RfpRepository implements RfpRepositoryInterface {
	
	public function selectAll(){
		return Rfp::company()->get();
	}

	public function find($id){
		return Rfp::find($id);
	}

	public function findByRfpNum($rfp){
		return Rfp::company()->where('rfp_number', $rfp)->first();
	}

	// public function getPending(){
	// 	$fields = array('register_id','module_id','account_id','register_refno','register_post','account_value', 'po_id' ,'created_at');
	// 	return Rfp::company()->aging()->with('reference.supplier')->get($fields);
	// }

	// public function getOpen(){
	// 	$fields = array('register_id','module_id','account_id','register_refno','register_post','account_value', 'po_id' ,'created_at');
	// 	return Rfp::company()->open()->with('reference.supplier')->get($fields);
	// }

	public function getAllNoCV(){ //return only those wihout cv request
		return Rfp::company()->has('cv','<',1)->with('register.reference.supplier')->get();
	}

	public function getAll(){ //return only those wihout cv request
		$fields = array('id','rfp_number','invoice_id','costing_department','created_at','date_needed','amount_requested',
		 'request_description','approved');
		return Rfp::company()->has('cv','<',1)->with('register.reference.supplier')->get($fields);
	}

	public function getOpenRecord($ref){
		$fields = array('id','rfp_number','invoice_id','costing_department','created_at','date_needed','amount_requested',
		 'request_description','approved');
		return Rfp::where('rfp_number',$ref)->company()->open()->with('register.reference.supplier')->get($fields);
		//return \DB::getQueryLog();
	}

	public function getApprovedRecord($ref){
		// $fields = array('id','register_id','register_refno','account_value', 'po_id');
		return Rfp::where('rfp_number',$ref)->company()->approved()->with('register.reference.supplier')->get();
		//return \DB::getQueryLog();
	}

	// public function getRecord($ref){
	// 	$fields = array('id','register_id','register_refno','account_value', 'po_id');
	// 	return Register::where('register_id',$ref)->company()->aging()->with('reference.supplier','rfp')->get($fields);
	// 	//return \DB::getQueryLog();
	// }

	private function entries_count(){
		return \DB::table('Rfp')->count();
	}

	public function create($data){
		$rfp = new Rfp;

		$rfp->rfp_number = 'RFP ' . \Helpers::recordNumGen($this->entries_count() + 1);//array_get($data,'ref') . "-" . ($this->entries_count() + 1);
		$rfp->costing_department = array_get($data,'cost_dept');;
		$rfp->date_needed = array_get($data,'date_needed');;
		$rfp->amount_requested = array_get($data,'amount_request');
		$rfp->request_description = array_get($data,'description');

		$filter = Rfp::validate($rfp->toArray(),'entry');

		if($filter->passes()) {
        	
			$this->save($rfp, array_get($data, 'invoice_ref'));

			return array('saved' => true, 'object' => $rfp);

        }
        else return array('saved' => false , 'object' => $filter->messages());
		
		

	}

	public function modify($ref,$data){
		$rfp = Rfp::where('rfp_number', $ref)->first();
		if($rfp->approved == 'N'){
			$rfp->date_needed = array_get($data,'date_needed');
			$rfp->request_description = array_get($data,'description');
			// $register->register_date_posted = date("Y-m-d H:i:s");
			$filter = Rfp::validate($rfp->toArray(),'entry');

			if($filter->passes()) {
        	
				$this->update($rfp);

				return array('saved' => 1, 'object' => $rfp);

	        }

	        else return array('saved' => 0, 'object' => $filter->messages());
			
		}
		else return array('saved' => -1 , 'object' => 'Cannot modify approved RFP');;
	}

	public function approve($ref){
		$request = Rfp::where('rfp_number', $ref)->first();
		if($request->approved == 'N'){
			$request->approved = 'Y';
			// $request->register_date_posted = date("Y-m-d H:i:s");
			return $this->update($request);
		}
		else return false;
		

	}

	/**
     * Simply saves the given instance
     *
     * @param  User $instance
     *
     * @return  boolean Success
     */
    public function save(Rfp $instance, $ref)
    {
        $entity = \Company::where('alias', \Session::get('company'))->first();
        $reference = Invoice::where('register_id', $ref)->first();

        $instance->context()->associate($entity);

        $instance->register()->associate($reference);
         
         return $instance->save();

		// $comment = $post->comments()->save($comment);
  //       return $instance->save();
        //return $entity->invoices()->save($instance);
    }

    public function update(Rfp $instance)
    { 
         return $instance->save();
     }

}