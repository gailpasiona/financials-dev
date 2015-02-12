<?php namespace Financials\Entities;

class CV extends FinancialModel {
	protected $table = 'cheque_voucher';

    public static $rules = array(
        'entry' => [
                     'cv_number'  => 'required',
                     'amount' => 'required|amount',
                     'cheque_number'  =>  'alpha_num',
                     'description'    =>  'required'
        ],
        'post' => [
                     'cv_number'  => 'required',
                     'amount' => 'required|amount',
                     'cheque_number'  =>  'required|alpha_num',
                     'description'    =>  'required'
        ]
        
    );

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

    public function rfp(){
        $showable_fields = array('id','invoice_id');
        return $this->belongsTo('Financials\Entities\Rfp', 'rfp_id')->select($showable_fields);
    }

    public function cregister(){
        //$showable_fields = array('id','invoice_id');
        return $this->hasOne('Financials\Entities\Register', 'po_id')->module('3');//->select();
    }

    public function scopeOpen($query){
        return $query->where('approved', 'N'); 
    }

    public static function validate($input, $action) {
        $att = array();
       //extra validation rules for dynamic fields
        if(isset($input['debit_account'])){
            for($i=0;$i < count($input['debit_account']);$i++){
                $line = $i + 1;
                static::$rules["debit_account.{$i}"] = 'required|alpha_spaces';
                static::$rules["debit_amount.{$i}"] = 'required|amount';
                $att["debit_account.{$i}"] = "Debit account line. " . "{$line}";
                $att["debit_amount.{$i}"] = "Debit amount line. " . "{$line}";
            }
        }

        if(isset($input['credit_account'])){
            for($i=0;$i < count($input['debit_account']);$i++){
                $line = $i + 1;
                static::$rules["credit_account.{$i}"] = 'required|alpha_spaces';
                static::$rules["creditt_amount.{$i}"] = 'required|amount';
                $att["credit_account.{$i}"] = "Credit account line. " . "{$line}";
                $att["credit_amount.{$i}"] = "Credit amount line. " . "{$line}";
            }
        }
        $validator = \Validator::make($input, static::$rules[$action]);
        $validator->setAttributeNames($att);
        
        return $validator;
    }

}