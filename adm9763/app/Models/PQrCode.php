<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class PQrCode extends Eloquent
{

    protected $table = 'sos_qrcodes';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'qr_code_link',
      'ticket_number',
      'participant_id',
      'created_at',
      'updated_at',
    ];

    public function participant(){
        return $this->belongsTo('App\Models\Participant', 'participant_id','id');
    }


}
