<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    //
    protected $fillable = [
        'value'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function books()
    {
      return  $this->hasMany('App\Book', 'action_id', 'id');
    }
    public function contracts()
    {
       return $this->hasMany('App\Contract', 'action_id', 'id');
    }
}
