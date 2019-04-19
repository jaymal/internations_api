<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    //

    protected $guarded = [];

     /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
