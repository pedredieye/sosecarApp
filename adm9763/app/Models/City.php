<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class City extends Eloquent
{

    protected $table = 'cities';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'name',
      'state_id',
      'state_code',
      'country_id',
      'country_code',
      'latitude',
      'longitude',
      'created_at',
      'updated_at',
      'flag',
      'wikiDataId'
    ];

    public function state(){
        return $this->belongsTo('App\Models\State', 'state_id','id');
    }



}
