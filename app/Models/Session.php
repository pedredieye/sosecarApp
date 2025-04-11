<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Session extends Eloquent
{

    protected $table = 'sos_sessions';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'titie',
      'content',
      'start',
      'end',
      'link_zoom',
      'code_zoom',
      'salle_id',
      'created_at',
      'updated_at',
    ];

    public function salles(){
        return $this->belongsTo('App\Models\Salle', 'salle_id','id');
    }

    public function scans(){ //Les scans effectués pour accéder à cette session
        return $this->hasMany('App\Models\Scan', 'session_id','id');
    }

}
