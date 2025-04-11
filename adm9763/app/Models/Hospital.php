<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Hospital extends Eloquent
{

    protected $table = 'sos_hospitals';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'name',
      'created_at',
      'updated_at',
    ];

    public function users(){
        return $this->hasMany('App\Models\User', 'Hospital_id','id');
    }


}
