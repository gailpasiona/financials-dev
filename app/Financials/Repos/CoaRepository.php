<?php

namespace Financials\Repos;

use Financials\Entities\Coa;
use Financials\Entities\CoaGroup;
use Financials\Entities\CoaSub;

class CoaRepository implements CoaRepositoryInterface {
	
	public function getAccountsByGroup($group_id){
		$group = CoaGroup::where('group_id',$group_id)->first();

		return $group->accounts;
	}

	public function selectAll(){

	}

	public function find($id){

	}

	public function findByName($name){
		return Coa::company()->where('account_title', $name)->first();
	}
}