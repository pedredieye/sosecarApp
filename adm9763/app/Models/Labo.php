<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Labo extends Eloquent
{

    protected $table = 'labos';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'name',
      'status',
      'created_at',
      'updated_at',
    ];

    public function memebers(){
        return $this->hasMany('App\Models\Participant', 'from_labo','id');
    }


}