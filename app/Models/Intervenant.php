<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Intervenant extends Eloquent
{

    protected $table = 'sos_intervenants';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'first_name',
      'last_name',
      'title',
      'created_at',
      'updated_at',
    ];

    public function intervention(){
        return $this->hasMany('App\Models\Intervention', 'intervenant_id','id');
    }


}
