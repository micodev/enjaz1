<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class role extends Model
{
    //
    protected $fillable = [
        'value', 'id'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function users()
    {
      return  $this->hasMany('App\User', 'user_id', 'id');
    }
    public function notify()
    {
      return  $this->hasMany('App\Notify', 'role_id', 'id');
    }
}
