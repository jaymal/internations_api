<?php

namespace App\Services;


use App\User as UserModel;
use App\Models\User_role as UserRole;
use App\Models\Group as GroupModel;
use App\Models\User_group as UserGroup;


use Illuminate\Http\Request;
use App\Exceptions\UserBelongsToGroupException;

class User
{
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
    	return UserModel::findOrFail($id)->delete();
    }

    //Add users to a group they are not already part of
    public function addUserToGroup($userId, $groupId)
    {		
		if($this->userBelongsToGroup($userId, $groupId)){
			//return false;
			throw new UserBelongsToGroupException("The User already belongs to this group yeah", 403);			
		}
        //check if user and group exists
        if ($this->userExists($userId) && $this->groupExists($groupId)){

            $userGroup = UserGroup::create([
                'user_id' => $userId,
                'group_id' => $groupId,
            ]);
            return $userGroup;
        }
		

    } 

    public function userExists($userId)
    {
        //return  (new UserGroup)->userExists($userId);
        return UserModel::findOrFail($userId)->first();
    }

    public function groupExists($groupId)
    {
        
        return GroupModel::findOrFail($groupId)->first();
    }

    //Remove user from a group
    public function removeUserFromGroup($userId, $groupId)
    {		
        $recordExists = UserGroup::where(['user_id' => $userId,'group_id' => $groupId])->firstOrFail();
         if($recordExists){
            return $recordExists->delete();
         }
         return $recordExists;
                
    }

    //Check if user belongs to a group
    public function userBelongsToGroup($userId, $groupId)
    {
        return UserGroup::where(['user_id' => $userId,'group_id' => $groupId])->first();
    }

	//create admin user
    public function makeUserAdmin($id)
    {

        if ($this->userExists($id) && UserRole::where('user_id', $id)->first()){
    		return UserRole::where('user_id', $id)->first()->update(['role_id' => 1]);   
    	}
    	return  UserRole::create([
            'user_id' => $id,
            'role_id' => 1,
        ]);
    }
}
	
