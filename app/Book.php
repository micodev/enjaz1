<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    //
    protected $fillable =[
        'destination', 'title', 'state_id', 'doc_number', 'doc_date', 'images', 'type_id', 'note', 'company_id',
        'action_id', 'user_id'
       
    ];
    protected $casts = [
        'images' => 'array',
    ];
    public function state()
    {
        return $this->belongsTo('App\State' , 'state_id', 'id');
    }
    public function action()
    {
        return $this->belongsTo('App\Action' , 'action_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo('App\Type' , 'type_id', 'id');
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
