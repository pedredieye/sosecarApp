<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Job extends Eloquent
{

    protected $table = 'sos_jobs';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'job_title',
      'created_at',
      'updated_at',
    ];

    public function users(){
        return $this->hasMany('App\Models\User', 'job_id','id');
    }


}
