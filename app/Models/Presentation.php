<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Presentation extends Eloquent
{

    protected $table = 'sos_presentations';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'session_id',
      'title',
      'name',
      'subject',
      'file',
      'status',
      'nb_views',
      'created_at',
      'updated_at',
    ];


}
