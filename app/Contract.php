<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    //
    protected $fillable = [
        'destination', 'title', 'state_id', 'doc_number', 'doc_date', 'images', 'type_id', 'note', 'company_id',
        'user_id', 'action_id', 'deleted', 'body'

    ];
    protected $appends = ['temp', 'qr'];
    // protected $hidden = ['deleted'];

    public function getTempAttribute()
    {
        return null;
    }
    public function getqrAttribute()
    {
        return "/images/qr/valid.png";
    }

    protected $casts = [
        'images' => 'array',
    ];

    public function state()
    {
        return $this->belongsTo('App\State', 'state_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo('App\Type', 'type_id', 'id');
    }
    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    public function action()
    {
        return $this->belongsTo('App\Action', 'action_id', 'id');
    }
    public function notify()
    {
        return  $this->hasMany('App\Notify', 'contract_id', 'id');
    }
}
