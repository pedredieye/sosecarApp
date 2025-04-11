<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Country extends Eloquent
{

    protected $table = 'countries';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'name',
      'iso3',
      'iso2',
      'phonecode',
      'capital',
      'currency',
      'native',
      'region',
      'subregion',
      'emoji',
      'emojiU',
      'created_at',
      'updated_at',
      'flag',
      'wikiDataId',
    ];

    public function states(){
        return $this->hasMany('App\Models\State', 'country_id','id');
    }


    public function participants(){
        return $this->hasMany('App\Models\Participant', 'country_id','id');
    }


}
