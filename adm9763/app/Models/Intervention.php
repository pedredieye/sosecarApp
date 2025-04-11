<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Intervention extends Eloquent
{

    protected $table = 'sos_interventions';
    //protected $primaryKey = 'id';

    protected $fillable = [
      'intervenant_id',
      'session_id',
      'role',
      'created_at',
      'updated_at',
    ];

    public function intervention(){
        return $this->belongsTo('App\Models\Intervenant', 'intervenant_id','id');
    }

    public function session(){
        return $this->belongsTo('App\Models\Session', 'session_id','id');
    }


}
