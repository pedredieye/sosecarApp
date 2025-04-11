<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Status extends Eloquent
{

    protected $table = 'sos_status';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'status',
      'created_at',
      'updated_at',
    ];


    public function participant(){ //L'utilisateur qui a ajouté son inscription
        return $this->belongsTo('App\Models\Participant', 'id','status');
    }




}
