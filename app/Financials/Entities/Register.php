<?php namespace Financials\Entities;


class Register extends FinancialModel {
	protected $table = 'accounting_register';

	public static $rules = array(
        'entry' => [
                     'po_id'  => 'required',
                     'account_value' => 'required|amount',
                     'invoice_date' => 'required|date',
                     'entry_type' => 'required',
                     'line_account' => 'required',
                     'line_amount' => 'required',
                     'line_description' => 'required'
                     //'register_refno'  =>  'alpha_spaces'
        ],
        'update' => [
                     'account_value' => 'required|amount',
                     'invoice_date' => 'required|date',
                     'entry_type' => 'required',
                     'line_account' => 'required',
                     'line_amount' => 'required',
                     'line_description' => 'required'
                     //'register_refno'  =>  'alpha_spaces'
        ],
        'post' => [
                     'po_id'  => 'required',
                     'account_value' => 'required|amount',
                     'register_refno'  =>  'required',
                     'account' => 'required',
                     'account_amount' => 'required',
                     'entry_type' => 'required'
        ],
        'sales_entry' => [
                     'po_id'  => 'required',
                     'invoice_date' => 'required|date',
                     'account_value' => 'required|amount',
                     'entry_type' => 'required',
                     'line_account' => 'required',
                     'line_amount' => 'required',
                     'line_description' => 'required',
                     'register_refno'  =>  'required'
        ],
        'receipt_entry' => [
                     'po_id'  => 'required',
                     'invoice_date' => 'required|date',
                     'account_value' => 'required|amount',
                     'account_id' => 'required',
                     'register_refno'  =>  'required'
        ]
    );

     public static function validate($input, $ruleset) {
        $att = array();
       //extra validation rules for dynamic fields
        if(isset($input['line_account'])){
            for($i=0;$i < count($input['line_account']);$i++){
                $line = $i + 1;

                static::$rules[$ruleset]["line_account.{$i}"] = 'required|alpha_spaces';
                static::$rules[$ruleset]["line_amount.{$i}"] = 'required|amount';
                
                if(isset($input['line_description'])){
                    static::$rules[$ruleset]["line_description.{$i}"] = 'required';
                    $att["line_description.{$i}"] = "Entry description for Line " . "{$line}";
                }
                 
                $att["line_account.{$i}"] = "Account for Line " . "{$line}";
                $att["line_amount.{$i}"] = "Amount for line " . "{$line}";
            }
        }

        $validator = \Validator::make($input, static::$rules[$ruleset]);
        $validator->setAttributeNames($att);
        
        return $validator;
        
        // $validator = \Validator::make($input, static::$rules[$ruleset]);
        // return $validator;
    }

    public static function validate_amount($input){
        $local_rules = array();

        foreach ($input as $amount) {
            $ctr = 0;
            $local_rules["amount.{$ctr}"] = 'required|amount';
        }

        return \Validator::make($input,$local_rules);

    }

    public static function boot()
    {
        parent::boot();
 
        static::creating(function($record)
        {
            $record->created_by =\Auth::user()->id;
            $record->last_updated_by = \Auth::user()->id;
        });
 
        static::updating(function($record)
        {
            $record->last_updated_by = \Auth::user()->id;
        });
    }

	public function context(){
		return $this->belongsTo('\Company', 'company_id');
	}

	public function reference(){
		$showable_fields = array('id','requestor','po_number','po_remarks','po_total_amount','supplier_id','payment_date_needed','reference_no');
		return $this->belongsTo('Financials\Entities\Purchases', 'po_id')->company()->select($showable_fields);
	}

	public function rfp(){
		$showable_fields = array('id','invoice_id');
		return $this->hasOne('Financials\Entities\Rfp', 'invoice_id')->select($showable_fields);
	}

    public function openforrfp(){
        return $this->hasOne('Financials\Entities\Rfp', 'invoice_id');
    }

    public function customer(){
        $showable_fields = array('id','supplier_name','address');
        return $this->belongsTo('Financials\Entities\Supplier', 'po_id')->company()->select($showable_fields);
    }

    public function sales_lines(){
        return $this->hasMany('Financials\Entities\Invoiceline', 'register_id'); //same with lines
    }

    public function lines(){
        return $this->hasMany('Financials\Entities\Invoiceline', 'register_id');
    }

    public function sales_receipt(){
        return $this->hasOne('Financials\Entities\Register','receipt_id');
    }

	public function scopeAging($query){
		return $query->where('register_post', 'Y'); 
	}

	public function scopeOpen($query){
		return $query->where('register_post', 'N'); 
	}

    public function scopeModule($query, $module){
        return $query->where('module_id', $module);
    }

	public function getTableName(){
		return $this->table;
	}
}