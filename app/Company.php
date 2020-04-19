<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //
    protected $fillable = [
        'value'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function books()
    {
       return $this->hasMany('App\Book', 'company_id' , 'id');
    }
    public function contracts()
    {
       return $this->hasMany('App\Contract', 'company_id' , 'id');
    }
    public function notes()
    {
      return  $this->hasMany('App\Note', 'company_id' , 'id');
    }
    public function papers()
    {
       return $this->hasMany('App\Paper', 'company_id' , 'id');
    }
    public function users()
    {
       return $this->hasMany('App\User', 'user_id' , 'id');
    }
    
}
