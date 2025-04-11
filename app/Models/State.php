<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class State extends Eloquent
{

    protected $table = 'states';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'name',
      'country_id',
      'country_code',
      'fips_code',
      'iso2',
      'subregion',
      'emoji',
      'created_at',
      'updated_at',
      'flag',
      'wikiDataId'
    ];

    public function country(){
        return $this->belongsTo('App\Models\Country', 'country_id','id');
    }

    public function cities(){
        return $this->hasMany('App\Models\City', 'state_id','id');
    }
    
    public function participants(){
        return $this->hasMany('App\Models\Participant', 'state_id','id');
    }

}
