<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'password', 'role_id', 'company_id', 'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
   
    public function papers()
    {
      return  $this->hasMany('App\Paper', 'user_id' , 'id');
    }
    public function books()
    {
      return  $this->hasMany('App\Book', 'user_id' , 'id');
    }
    public function contracts()
    {
       return $this->hasMany('App\Contract', 'user_id' , 'id');
    }
    public function notes()
    {
      return  $this->hasMany('App\Note', 'user_id' , 'id');
    }
    public function role()
    {
        return $this->belongsTo('App\Role' , 'role_id', 'id');
    }
    public function tokens()
    {
       return $this->hasMany('App\Token', 'user_id' , 'id');
    }

    public function company()
    {
        return $this->belongsTo('App\Company' , 'company_id', 'id');
    }
    public function notify()
    {
      return  $this->hasMany('App\Notify', 'user_id' , 'id');
    }
    public function getActiveAttribute($value)
    {
      return $value ? true : false;
    }
}
