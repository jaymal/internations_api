<?php

namespace App\Services;

use App\User as UserModel;
use Illuminate\Http\Request;

class User
{
	public function create($request)
    {
    	$user = UserModel::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        return $user;
    }

    public function delete($id)
    {
    	return UserModel::find($id)->delete();
    }

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
    public function removeUserFromGroup($id)
    {		
        $user = UserModel::find($id);	
        $user->group_id = null;
        $user->save();
        return $user;

    }

    public function userExists($groupId)
    {
        $userExists = UserModel::where('group_id', $groupId)->first();
        return  $userExists;
    }
}
	
