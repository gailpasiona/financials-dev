<?php namespace Financials\Entities;


class Purchases extends FinancialModel {
	protected $table = 'PO_header';

	public static $rules = array(
        'entry' => [
                     'po_number'  => 'required',
                     'po_total_amount' => 'required|amount',
                     'po_date' => 'required|date',
                     'requestor' => 'required',
                     'supplier_id' => 'required',
                     'po_paymentterms' => 'required',
					 'po_downpayment' => 'required',
					 'requestor_dept_det' => 'required',
					 'po_fullyreceived' => 'required',
					 'po_status' =>  'required',
					 'approved_by' => 'required',
					 'po_remarks' => 'required',
					 'cancelled' => 'required',
					 'invoiced' => 'required'

        ],
        'request' => [
        				'date_needed' => 'required|date',
        				'reference_no' => 'required'
        ]
    );

    public static function validate($input, $ruleset) {
        
        $validator = \Validator::make($input, static::$rules[$ruleset]);
        //$validator->setAttributeNames($att);
        
        return $validator;
    }

	public static function boot()
    {
        parent::boot();
 
        static::creating(function($record)
        {
            $record->created_by =\Auth::user()->full_name;
            // $record->last_updated_by = \Auth::user()->id;
        });
 
        // static::updating(function($record)
        // {
        //     //$record->last_updated_by = \Auth::user()->id;
        // });
    }

    public function context(){
		return $this->belongsTo('\Company', 'company_id');
	}

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

	public function scopeApproved($query,$status){
		return $query->where('approved', $status);
	}

	public function scopeNoInvoice($query){
		return $query->whereNotExists(function($query){
			$this_table = DB::getTablePrefix() . $this->table; 
			$query->select(DB::raw('register_refno'))->from('_tbl_accounting_register') ->whereRaw('register_refno = '.$this_table.'.purchase_number'); 
		});

	}

}