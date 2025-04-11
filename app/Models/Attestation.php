<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Attestation extends Eloquent
{

    protected $table = 'sos_attestations';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'name',
      'email',
      'created_at',
      'updated_at',
    ];


}
