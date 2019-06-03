<?php

namespace App\Services;


use App\Models\Group as GroupModel;
use App\Models\User_group as UserGroup;

use Illuminate\Http\Request;

class Group
{
	public function create($request)
    {
    	$group = GroupModel::create([
            'group_name' => $request->group_name,
        ]);
        return $group;
    }

    public function delete($id)
    {   
        return GroupModel::findOrFail($id)->delete();
    }

    public function getGroupId($request)
    {
        return GroupModel::where('group_name', $request->group_name)->first()->id;
    }

    public function groupHasUsers($groupId)
    {
        return UserGroup::where('group_id', $groupId)->first();
    }
}
	