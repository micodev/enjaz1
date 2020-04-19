<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    //
    protected $fillable = [
        'value'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function books()
    {
       return $this->hasMany('App\Book', 'state_id', 'id');
    }
    public function contracts()
    {
       return $this->hasMany('App\Contract', 'state_id', 'id');
    }
}
