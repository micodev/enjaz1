<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    //
    protected $fillable = [
        'doc_number', 'doc_date', 'title', 'incoming', 'outcoming', 'note', 'images' , 'company_id', 'user_id',
        'deleted'
    ];
    protected $casts = [
        'images' => 'array',
    ];
    protected $appends = ['temp', "doc_type"];
    public function getTempAttribute()
    {
        return null;
    }
    public function getDocTypeAttribute()
    {
        return 4;
    }

    public function company()
    {
        return $this->belongsTo('App\Company' , 'company_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\User' , 'user_id', 'id');
    }
}
