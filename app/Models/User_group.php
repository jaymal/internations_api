<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class User_group extends Model
{
    //
    //use SoftDeletes;
    use Notifiable;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\Group');
    }

    public function userExists($userId)
    {
    	return $this->user->find($userId)->first();
    }

    public function scopeCountRecords($query, $userId, $groupId)
    {

    	return $query->where(['user_id' => $userId, 'group_id' => $groupId])->count();

    }

    
    

}
