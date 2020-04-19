<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    //
    protected $fillable = [
        'value', 'table'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function books()
    {
      return  $this->hasMany('App\Book', 'type_id' , 'id');
    }
    public function contracts()
    {
      return  $this->hasMany('App\Contract', 'type_id' , 'id');
    }
    
}
