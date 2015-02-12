<?php namespace Financials\Entities;

class CoaGroup extends FinancialModel {
	protected $table = 'coa_group_accounts';

	protected $primaryKey= 'group_id';

	public function coa_sub_accounts(){
		return $this->hasMany('Financials\Entities\CoaSub', 'sub_acct_id')->company();
	}
	
	public function accounts()
    {
        return $this->hasManyThrough('Financials\Entities\Coa', 'Financials\Entities\CoaSub', 'group_id', 'sub_acct_id')->company();
    }
	
}