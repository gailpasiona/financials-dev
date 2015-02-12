<?php

namespace Financials\Repos;

use Financials\Entities\CV;
use Financials\Entities\Rfp;

class CVRepository implements CVRepositoryInterface {
	
	public function selectAll(){
		//return CV::company()->get();>whereHas('register', function($q){
			//$q->where('register_post','Y')
		//->has('cregister','<',1)

		return CV::company()->whereHas('cregister', function($q){
			$q->where('register_post','N');})->orHas('cregister','<',1)->with('rfp.register.reference.supplier')->get();
	}

	public function find($id){
		return Rfp::find($id);
	}

	public function findRecord($record){
		return CV::where('cv_number', $record)->first();
	}

	public function pullRecord($record){
		return CV::where('cv_number', $record)->with('cregister')->get();
	}

	public function traceRecord($record){
		return CV::where('cv_number', $record)->with('rfp.register.reference.supplier','cregister')->get();
	}

	public function traceRecordObj($record){
		return CV::where('cv_number', $record)->with('rfp.register.reference.supplier','cregister')->first();
	}
	// public function getPending(){
	// 	$fields = array('register_id','module_id','account_id','register_refno','register_post','account_value', 'po_id' ,'created_at');
	// 	return Rfp::company()->aging()->with('reference.supplier')->get($fields);
	// }

	// public function getOpen(){
	// 	$fields = array('register_id','module_id','account_id','register_refno','register_post','account_value', 'po_id' ,'created_at');
	// 	return Rfp::company()->open()->with('reference.supplier')->get($fields);
	// }

	// public function getAllNoCV(){ //return only those wihout cv request
	// 	return Rfp::company()->has('cv','<',1)->with('register.reference.supplier')->get();
	// }

	// public function getAll(){
	// 	$fields = array('id','rfp_number','invoice_id','costing_department','created_at','date_needed','amount_requested',
	// 	 'request_description','approved');
	// 	return Rfp::company()->has('cv','<',1)->with('register.reference.supplier')->get($fields);
	// }

	public function getOpenRecord($ref){
		$fields = array('id','cv_number','cheque_number','amount','description','rfp_id');
		return CV::where('cv_number',$ref)->company()->open()->with('rfp.register.reference.supplier')->get($fields);
		//return \DB::getQueryLog();
	}

	// public function getApprovedRecord($ref){
	// 	// $fields = array('id','register_id','register_refno','account_value', 'po_id');
	// 	return Rfp::where('rfp_number',$ref)->company()->approved()->with('register.reference.supplier')->get();
	// 	//return \DB::getQueryLog();
	// }

	// public function getRecord($ref){
	// 	$fields = array('id','register_id','register_refno','account_value', 'po_id');
	// 	return Register::where('register_id',$ref)->company()->aging()->with('reference.supplier','rfp')->get($fields);
	// 	//return \DB::getQueryLog();
	// }

	private function entries_count(){
		return \DB::table('cheque_voucher')->count();
	}

	public function create($data){
		$cv = new CV;

		$cv->cv_number = 'CV ' . \Helpers::recordNumGen($this->entries_count() + 1);//array_get($data,'ref') . "-" . ($this->entries_count() + 1);
		$cv->amount = array_get($data,'amount_request');
		$cv->cheque_number = array_get($data, 'cheque_number');
		$cv->description = array_get($data,'description');

		$filter = CV::validate($cv->toArray(), 'entry');

		if($filter->passes()){
			
			
			$this->save($cv, array_get($data, 'rfp_number'));

			return array('saved' => true, 'object' => $cv);

		}

		else return array('saved' => false, 'object' => $filter->messages());	

	}

	public function modify($ref,$data){
		$cv = CV::where('cv_number', $ref)->first();
		if($cv->approved == 'N'){
			$cv->cheque_number = array_get($data,'cheque_number');
			$cv->description = array_get($data,'description');
			// $register->register_date_posted = date("Y-m-d H:i:s");
			$filter = CV::validate($cv->toArray(),'entry');

			if($filter->passes()) {
        	
				$this->update($cv);

				return array('saved' => 1, 'object' => $cv);

	        }

	        else return array('saved' => 0, 'object' => $filter->messages());
			
		}
		else return array('saved' => -1 , 'object' => 'Cannot modify approved CV');
	}

	public function approve($ref){
		$request = CV::where('cv_number', $ref)->first();
		// if($request->approved == 'N'){
		$request->approved = 'Y';

			// $filter = CV::validate($request->toArray(),'post');

			// if($filter->passes()) {

		return $this->update($request);

				// return array('saved' => 1, 'object' => $request);
		// 	}

		// 	else return array('saved' => 0, 'object' => $filter->messages());
		// }
		// else return array('saved' => -1 , 'object' => 'CV approval failed!');
		

	}

	public function pre_validate($ref){
		$request = CV::where('cv_number', $ref)->first();
		if($request->approved == 'N'){
			
			$filter = CV::validate($request->toArray(),'post');

			if($filter->passes())
				return array('passed' => 1, 'object' => $request);

			else return array('passed' => 0, 'object' => $filter->messages());
		}
		else return array('passed' => -1 , 'object' => 'CV already approval');
	}

	/**
     * Simply saves the given instance
     *
     * @param  User $instance
     *
     * @return  boolean Success
     */
    public function save(CV $instance, $ref)
    {
        $entity = \Company::where('alias', \Session::get('company'))->first();
        $reference = Rfp::where('rfp_number', $ref)->first();

        $instance->context()->associate($entity);

        $instance->rfp()->associate($reference);
         
        return $instance->save();

		// $comment = $post->comments()->save($comment);
  //       return $instance->save();
        //return $entity->invoices()->save($instance);
    }

    public function update(CV $instance)
    { 
         return $instance->save();
     }

}