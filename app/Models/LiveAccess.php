<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class LiveAccess extends Eloquent
{

    protected $table = 'live_access';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'participant_id',
      'salle_id',
      'created_at',
      'updated_at',
    ];

  


}
