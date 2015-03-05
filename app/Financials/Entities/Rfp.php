<?php namespace Financials\Entities;

class Rfp extends FinancialModel {
	protected $table = 'Rfp';

    public static $rules = array(
        'entry' => [
                     'costing_department'  => 'required',
                     'date_needed' => 'required|date',
                     'amount_requested'  =>  'amount',
                     'request_description' => 'required'
        ],
        'approval' => [
                     'costing_department'  => 'required',
                     'date_needed' => 'required|date',
                     'amount_requested'  =>  'amount',
                     'request_description' => 'required'
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

    public function register(){
        $showable_fields = array('id','register_id','po_id');
        return $this->belongsTo('Financials\Entities\Register', 'invoice_id')->select($showable_fields);
    }

    public function cv(){
        $showable_fields = array('id','invoice_id');
        return $this->hasOne('Financials\Entities\CV', 'rfp_id')->select();
    } 

    public function scopeOpen($query){
        return $query->where('approved', 'N'); 
    }

    public function scopeApproved($query){
        return $query->where('approved', 'Y'); 
    }



}