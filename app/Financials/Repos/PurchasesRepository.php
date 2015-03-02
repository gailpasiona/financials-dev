<?php

namespace Financials\Repos;

use Financials\Entities\Purchases;

class PurchasesRepository implements PurchasesRepositoryInterface {
	
	public function selectAll(){//PO with previous invoice and invoice is already posted, support for multiple payments
		// return Purchases::company()->has('register','<',1)->with(array('supplier' => function($query){
		// 	$query->select('id','supplier_name');
		// }))->get();

		return Purchases::company()->approved('1')->with(array('supplier' => function($query){
			$query->select('id','supplier_name');
		}),'register')->whereHas('register', function($q){
			$q->where('register_post','Y');
		})->get();

		// return Purchases::company()->with(array('supplier' => function($query){
		// 	$query->select('id','supplier_name');
		// }),'register')->get();
	}

	public function selectForApproval(){
		return Purchases::company()->approved('1')->with(array('supplier' => function($query){
			$query->select('id','supplier_name');
		}),'register')->get();
	}

	public function selectAllNoInvoice(){//has('register','<',1)
		return Purchases::company()->approved('2')->has('register','<',1)->with(array('supplier' => function($query){
			$query->select('id','supplier_name');
		}))->get();
		// return Purchases::company()->whereHas('register', function ($q) {
	 //    		$q->where('register_post', '=', 'Y');  
		// });
	}

	public function selectAllForRequest(){//has('register','<',1)
		return Purchases::company()->approved('0')->has('register','<',1)->with(array('supplier' => function($query){
			$query->select('id','supplier_name');
		}))->get();
		// return Purchases::company()->whereHas('register', function ($q) {
	 //    		$q->where('register_post', '=', 'Y');  
		// });
	}

	public function find($id){
		return Purchases::company()->with('supplier')->find($id);
	}

	public function findByPO($number){
		$fields = array('id','po_number');
		return Purchases::company()->where('po_number',$number)->first();
	}

	public function updateById($id){
		$record = Purchases::find($id);

		$record->invoiced = 'Y';

		$record->save();

		return;
	}

	public function find_selected_columns($id,$fields){
		return Purchases::find($id)->select();
	}

	private function entries_count(){
		return \DB::table('PO_header')->count();
	}

	public function create($data){
		$record = new Purchases;
		$record->id = $this->entries_count() + 1;
		$record->po_number = array_get($data, 'po_number');
        $record->po_total_amount = array_get($data, 'amount');
        $record->po_date = array_get($data, 'po_date');
        $record->requestor = array_get($data, 'requestor');
        $record->supplier_id = array_get($data, 'payee');
        $record->po_paymentterms = 'N/A';
		$record->po_downpayment = 'N/A';
		$record->requestor_dept_det ='N/A';
		$record->po_fullyreceived = '0';
		$record->po_status = 'N/A';
		$record->approved_by = 'N/A';
		$record->po_remarks = 'N/A';
		$record->cancelled = '0';
		$record->invoiced = 'N';

		$filter = Purchases::validate($record->toArray(), 'entry');
		
		if($filter->passes()) {
				
			if($this->save($record))
				return array('saved' => true, 'object' => $record);

			else return array('saved' => false, 'object' => 'Unable to create Payable');

	    }

	    else return array('saved' => false , 'object' => $filter->messages());

	}

	public function approve($record){
		$request = Purchases::company()->find($record);
		if($request->approved == '1'){
			$data = array('data'=>array('approved' => '2', 'approver' => \Auth::user()->username, 'approved_at' => date("Y-m-d H:i:s")),
							'company' => \Company::where('alias', \Session::get('company'))->first()->id, 'id' => $record);
			return $this->update($data);
		}
		else return false;
		
	}

	public function request($record){

		$request = Purchases::company()->find($record);//$this->find($rec);

		if($request->approved == '0'){
			$data = array('data'=>array('approved' => '1', 'approver' => \Auth::user()->username, 'approved_at' => date("Y-m-d H:i:s")),
							'company' => \Company::where('alias', \Session::get('company'))->first()->id, 'id' => $record);
			
			return $this->update($data);//last(\DB::getQueryLog());
		}

		else return false;
		
		// return $request;
	}



	public function save(Purchases $instance)
    {
        $entity = \Company::where('alias', \Session::get('company'))->first();

        $instance->context()->associate($entity);
         
         return $instance->save();

		// $comment = $post->comments()->save($comment);
  //       return $instance->save();
        //return $entity->invoices()->save($instance);
    }

    public function update($instance)
    { 
         // return $instance->save();
         return Purchases::where('id', $instance['id'])->where('company_id', $instance['company'])->update($instance['data']);


     }
}