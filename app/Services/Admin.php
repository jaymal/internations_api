<?php

namespace App\Services;

use App\Services\User ;
use App\User as UserModel;
use App\Models\User_role;

use Illuminate\Http\Request;

class Admin extends User
{
	 
    public function __construct($userId)
    {
        $recordExist = User_role::where(["role_id" => 1, "user_id" => $userId])->firstOrFail();
        return $recordExist;//Check if a user is admin i.e if role_id  is 1.
    }

    //create a user
    public function create($request)
    {
    	$user = UserModel::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        return $user;
    }

    //delete a user
    public function delete($id)
    {
    	return UserModel::find($id)->delete();
    }

    //Add users to a group they are not already part of
    public function addUserToGroup($request, $groupId)
    {		
		
        if(! UserModel::where(['id' => $request->id, 'group_id' => $groupId])->first()){
			$user = UserModel::find($request->id);
            $user->group_id = $groupId;
            $user->save();
            return $user;
		}
		return false;
    }

    //Remove user from a group
    public function removeUserFromGroup($id)
    {		
        $user = UserModel::find($id);	
        $user->group_id = null;
        $user->save();
        return $user;

    }

    //Check if user exists
    public function userExists($groupId)
    {
        $userExists = UserModel::where('group_id', $groupId)->first();
        return  $userExists;
    }
}
	
