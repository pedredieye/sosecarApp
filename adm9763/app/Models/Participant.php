<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Participant extends Eloquent
{

    protected $table = 'sos_participants';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'first_name',
      'last_name',
      'year_of_birth',
      'email',
      'phone',
      'gender',
      'job_id',
      'title_id',
      'country_id',
      'state_id',
      'ticket_number',
      'num_recu',
      'added_by',
      'validated_by',
      'status',
      'formule',
      'payment_status',
      'flag_mail_validation',
      'flag_mail_day',
      'flag_gen_certificate',
      'link_certificate',
      'flag_download_certificate',
      'password',
      'created_at',
      'updated_at',
    ];

    public function etat(){ //Les scans effectués pour accéder à une session
        return $this->belongsTo('App\Models\Status', 'status','id');
    }


    public function scans(){ //Les scans effectués pour accéder à une session
        return $this->hasMany('App\Models\Scan', 'participant_id','id');
    }

    public function qrcode(){ //Le qrcode généré pour cet utilisateur
        return $this->hasMany('App\Models\PQrCode', 'participant_id','id');
    }

    public function validatedby(){ //L'utilisateur qui a validé son inscription
        return $this->belongsTo('App\Models\User', 'validated_by','id');
    }

    public function country(){
        return $this->belongsTo('App\Models\Country', 'country_id','id');
    }

    public function state(){
        return $this->belongsTo('App\Models\State', 'state_id','id');
    }

    public function job(){
        return $this->belongsTo('App\Models\Job', 'job_id','id');
    }

    public function title(){
        return $this->belongsTo('App\Models\Title', 'title_id','id');
    }

    public function addedby(){ //L'utilisateur qui a ajouté son inscription
        return $this->belongsTo('App\Models\User', 'added_by','id');
    }


}