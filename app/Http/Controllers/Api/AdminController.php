<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\User;
use App\Models\User_role;

use App\Http\Resources\UserResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\GroupResource;

use App\Services\Admin as AdminService;
use App\Services\Group as GroupService;
use App\Services\Role as RoleService;

class AdminController extends Controller
{
   	public $adminService;
   	public $groupService;
   	public $roleService;
    
 
    function __construct(GroupService $groupService, RoleService $roleService )
    {
    
        $this->groupService = $groupService;
        $this->roleService = $roleService;
        $this->middleware(function ($request, $next) {//laravels

            $this->adminService = new AdminService(request()->user()->id);
            return $next($request);

        });
        return $this->middleware('auth:api');
    }
    
    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        $user = $this->adminService->create($request);
        return new UserResource($user);
    }

     /**
     * update the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assign(Request $request)
    {

        $this->validate($request, [
            'group_name' => 'required|min:3',
            'id' =>'required|numeric',
        ]);
        $groupId = $this->groupService->getGroupId($request);
        if( $this->adminService->addUserToGroup($request, $groupId)){
        	return response()->json(['message'=>'Users assigned successfully'],201);

        }
        return response()->json(['error'=>'Unable to assign user to the group'],500);

    }

    //remove user from groups
    public function unAssign($id)
    {

        $user = $this->adminService->removeUserFromGroup($id);
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user->id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    	$user = $this->adminService->delete($id);

        return response()->json(null,200);
    }

    //Create a new group
    public function groupStore(Request $request)
    {
    	 $this->validate($request, [
            'group_name' => 'required|min:3',
        ]);
        $group = $this->groupService->create($request);
        return new GroupResource($group);
    }

    //Delete group
    public function groupDestroy($id)
    {
        
    	$userExists = $this->adminService->userExists($id);
        if($userExists){
        	 return response()->json(['error'=>'Unable to delete.Users are still assigned to the group'],400);
        }

    	$this->groupService->delete($id);           
        return response()->json(null,200);
    }

    //Add a new role
    public function roleStore(Request $request)
    {

    	 $this->validate($request, [
            'role_name' => 'required|min:3',
        ]);
        $role = $this->roleService->create($request);
        return new RoleResource($role);
    }



    
}
