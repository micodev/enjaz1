<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class role extends Model
{
    //
    protected $fillable = [
        'value'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function users()
    {
      return  $this->hasMany('App\User', 'user_id', 'id');
    }
}
