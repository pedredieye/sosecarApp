<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Salle extends Eloquent
{

    protected $table = 'sos_salles';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'salle',
      'created_at',
      'updated_at',
    ];

    public function sessions(){
        return $this->hasMany('App\Models\Session', 'salle_id','id');
    }


}
