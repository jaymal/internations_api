<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function userRole()
    {
        return $this->hasMany('App\Models\User_role');
    }

    public function userGroup()
    {
        return $this->hasMany('App\Models\User_group');
    }
    
    public function group()
    {     
        return $this->hasOne('App\Models\Group');
    }

    public function IsAdmin()
    {
        if($this->userRole->first()){
            return $this->userRole->first()->role_id == 1 ? 1: 0;
        }
        return false;
    }
}
