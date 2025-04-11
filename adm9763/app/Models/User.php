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
      'hospital_id',
      'flag_email_after_creation',
      'created_at',
      'updated_at',
    ];

    public function role(){
        return $this->belongsTo('App\Models\Role', 'role_id','id');
    }

    public function validations(){
        return $this->hasMany('App\Models\Participant', 'validated_by','id');
    }

    public function added(){
        return $this->hasMany('App\Models\Participant', 'added_by','id');
    }

    public function scans(){
        return $this->hasMany('App\Models\Scan', 'user_id','id');
    }
    public function hospital(){
        return $this->belongsTo('App\Models\Hospital', 'hospital_id','id');
    }





}
