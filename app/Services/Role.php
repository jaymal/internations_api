<?php

namespace App\Services;

use App\User as UserModel;
use App\Models\Group as GroupModel;
use App\Models\Role as RoleModel;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class Role
{
	public function create($request)
    {
    	$role = RoleModel::create([
            'role_name' => $request->name,
        ]);
        return $role;
    }

    public function delete($id)
    {
    	return RoleModel::find($id)->delete();
    }
}
	