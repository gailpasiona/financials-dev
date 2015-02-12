<?php namespace Financials\Entities;


class Invoiceline extends FinancialModel {

	protected $table = 'invoice_lines';

	// public static function boot()
 //    {
 //        parent::boot();
 
 //        static::creating(function($record)
 //        {
 //            $record->company_id = \Company::where('alias', \Session::get('company'))->first()->id;
 //        });
 
 //    }

	public function salesinvoice(){
		//$showable_fields = array('account_id', 'sub_acct_id', 'account_title');
		return $this->belongsTo('Financials\Entities\Register', 'register_id')->company()->select();
	}
}