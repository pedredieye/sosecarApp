<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Title extends Eloquent
{

    protected $table = 'sos_titles';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'title',
      'created_at',
      'updated_at',
    ];

    public function users(){
        return $this->hasMany('App\Models\User', 'title_id','id');
    }


}
