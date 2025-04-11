<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class AteliersParticipant extends Eloquent
{

    protected $table = 'sos_ateliers_participants';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'first_name',
      'last_name',
      'email',
      'specialite',
      'activity_1_status',
      'activity_2_status',
      'activity_3_status',
      'activity_4_status',
      'activity_5_status',
      'created_at',
      'updated_at',
    ];

   


}