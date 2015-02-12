<?php

namespace Financials\Repos;

use Financials\Entities\Supplier;

class SupplierRepository implements SupplierRepositoryInterface{

	public function selectAll(){
			return Supplier::company()->get(array('id','supplier_name'));
	}

	public function find($id){
			
	}
}