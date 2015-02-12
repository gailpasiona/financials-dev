<?php namespace Financials\Entities;

class CoaSub extends FinancialModel {
	protected $table = 'coa_sub_accounts';

	protected $primaryKey = 'sub_acct_id';

	public function coa_group(){
		$showable_fields = array('sub_acct_id','sub_acct_name', 'group_id');
		return $this->belongsTo('Financials\Entities\CoaGroup', 'group_id')->company()->select();
	}

	public function coa_accounts(){
		return $this->hasMany('Financials\Entities\Coa', 'sub_acct_id')->company();
	}

	public function scopeSubname($query, $filter){
		return $query->where('sub_acct_name', $filter);
	}

	
}