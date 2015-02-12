<?php namespace Financials\Entities;

use Eloquent;

class FinancialModel extends Eloquent {
	protected $guarded = ['id'];

	public function scopeCompany($query){
		$company_id = \Company::where('alias', \Session::get('company'))->first();
		return $query->where('company_id', $company_id->id);
	}

}