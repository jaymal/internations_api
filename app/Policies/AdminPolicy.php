<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * If a user is an admin
     *
     * @param \App\User  $user
     * 
     */
    public function create( User $user)
    {
        return $user->IsAdmin();

    }
    public function update( User $user)
    {
        
        return $user->IsAdmin();

    }

    public function delete( User $user)
    {
        return $user->IsAdmin();

    }


    /*public function before($user, $ability)
    {
        if ($user->IsAdmin()) {
            return true;
        }
    }*/
}
