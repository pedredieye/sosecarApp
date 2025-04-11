<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Scan extends Eloquent
{

    protected $table = 'sos_scans';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'user_id',
      'session_id',
      'participant_id',
      'flag_sms',
      'created_at',
      'updated_at',
    ];

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id','id');
    }

    public function session(){
        return $this->belongsTo('App\Models\Session', 'session_id','id');
    }

    public function participant(){
        return $this->belongsTo('App\Models\Participant', 'participant_id','id');
    }

}
