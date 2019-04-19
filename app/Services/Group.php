<?php

namespace App\Services;


use App\Models\Group as GroupModel;

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
        return GroupModel::find($id)->delete();
    }

    public function getGroupId($request)
    {
        return GroupModel::where('group_name', $request->group_name)->first()->id;
    }
}
	