<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class AbstractFile extends Eloquent
{

    protected $table = 'sos_abstract';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'title',
      'authors',
      'addresse',
      'resume',
      'file',
      'flag_upload_file',
      'sender_title',
      'sender_name',
      'sender_address',
      'sender_email',
      'created_at',
      'updated_at',
    ];

    public function participant(){
        return $this->belongsTo('App\Models\Participant', 'participant_id','id');
    }


}
