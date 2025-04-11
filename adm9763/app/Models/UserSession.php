<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class UserSession extends Eloquent
{

    protected $table = 'sos_user_sessions';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'user_id',
      'user_device',
      'user_browser',
      'user_ip',
      'created_at',
      'updated_at'
    ];

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id','id');
    }


}
