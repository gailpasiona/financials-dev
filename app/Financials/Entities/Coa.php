<?php namespace Financials\Entities;

class Coa extends FinancialModel {
	protected $table = 'coa_accounts';

	protected $primaryKey= 'account_id';

	public function coa_sub_account(){
		$showable_fields = array('account_id', 'sub_acct_id', 'account_title');
		return $this->belongsTo('Financials\Entities\CoaSub', 'sub_acct_id')->company()->select();
	}
	
}