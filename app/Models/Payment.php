<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Payment extends Eloquent
{

    protected $table = 'payments';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'payment_from',
      'invoice_url',
      'participant_ref',
      'participant_id',
      'customer_name',
      'customer_phone',
      'customer_email',
      'token',
      'status',
      'response_code',
      'response_text',
      'amount',
      'receipt_url',
      'attempts',
      'created_at',
      'updated_at',
    ];

    
    public function participant(){ 
        return $this->belongsTo('App\Models\Participant', 'participant_id','id');
    }
    public function participantbyref(){ 
        return $this->belongsTo('App\Models\Participant', 'participant_ref','ref');
    }

}