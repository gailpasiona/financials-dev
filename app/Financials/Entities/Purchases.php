<?php namespace Financials\Entities;


class Purchases extends FinancialModel {
	protected $table = 'PO_header';

	public function supplier(){
		$showable_fields = array('id','supplier_name','address');
		return $this->belongsTo('Financials\Entities\Supplier')->company()->select($showable_fields);
	}

	public function register(){
		return $this->hasMany('Financials\Entities\Register', 'po_id')->module('1');
	}

	public function openforinvoice(){
		return $this->hasMany('Financials\Entities\Register', 'po_id')->module('1')->open();
	}

	public function scopeInvoiced($query){
		return $query->where('invoiced', 'N');
	}

	public function scopeNoInvoice($query){
		return $query->whereNotExists(function($query){
			$this_table = DB::getTablePrefix() . $this->table; 
			$query->select(DB::raw('register_refno'))->from('_tbl_accounting_register') ->whereRaw('register_refno = '.$this_table.'.purchase_number'); 
		});

	}

}