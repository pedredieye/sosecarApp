<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent
{

    protected $table = 'sos_users';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'first_name',
      'last_name',
      'email',
      'phone',
      'password',
      'role_id',
      'created_at',
      'updated_at',
    ];

    public function role(){
        return $this->belongsTo('App\Models\Role', 'role_id','id');
    }

    public function validations(){
        return $this->hasMany('App\Models\Participant', 'validated_by','id');
    }

    public function scans(){
        return $this->hasMany('App\Models\Scan', 'user_id','id');
    }





}
