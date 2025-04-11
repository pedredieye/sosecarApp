<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Role extends Eloquent
{

    protected $table = 'sos_roles';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'role',
      'created_at',
      'updated_at',
    ];

    public function users(){
        return $this->hasMany('App\Models\User', 'role_id','id');
    }


}
