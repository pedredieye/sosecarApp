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
      'status',
      'motif_rejet',
      'updated_by',
      'created_at',
      'updated_at',
    ];


}
