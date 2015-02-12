<?php

namespace Financials\Repos;

use Financials\Entities\Purchases;

class PurchasesRepository implements PurchasesRepositoryInterface {
	
	public function selectAll(){//PO with previous invoice and invoice is already posted, support for multiple payments
		// return Purchases::company()->has('register','<',1)->with(array('supplier' => function($query){
		// 	$query->select('id','supplier_name');
		// }))->get();

		return Purchases::company()->with(array('supplier' => function($query){
			$query->select('id','supplier_name');
		}),'register')->whereHas('register', function($q){
			$q->where('register_post','Y');
		})->get();

		// return Purchases::company()->with(array('supplier' => function($query){
		// 	$query->select('id','supplier_name');
		// }),'register')->get();
	}

	public function selectAllNoInvoice(){//has('register','<',1)
		return Purchases::company()->has('register','<',1)->with(array('supplier' => function($query){
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
}