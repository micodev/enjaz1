<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paper extends Model
{
    //
    protected $fillable = [
        'title', 'doc_date', 'note', 'company_id', 'images' ,'user_id'
    ];
    protected $casts = [
        'images' => 'array',
    ];
    protected $appends = ['temp'];
    public function getTempAttribute()
    {
        return null;
    }
    // protected $appends = ['company_id'];
    // protected $hidden = ['company_id'];

    public function company()
    {
        return $this->belongsTo('App\Company' , 'company_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\User' , 'user_id', 'id');
    }

    // public function getimagesAttribute($value)
    // {
    //     return json_decode($value);
    // }
       
    // public function getcompanyidAttribute($value){
    //     return $this->company();
    // }


}
