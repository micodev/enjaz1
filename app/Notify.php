<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notify extends Model
{
    protected $fillable = [
        'book_id', 'contract_id', 'seen', 'role_id', 'user_id', 'type'
    ];
    protected $hidden = [
        'book_id', 'contract_id', 'role_id', 'user_id'
    ];


    public function role()
    {
        return $this->belongsTo('App\Role' , 'role_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\User' , 'user_id', 'id');
    }
    public function book()
    {
        return $this->belongsTo('App\Book' , 'book_id', 'id');
    }
    public function contract()
    {
        return $this->belongsTo('App\Contract' , 'contract_id', 'id');
    }
    public function getSeenAttribute($value)
    {
      return $value ? true : false;
    }
}
