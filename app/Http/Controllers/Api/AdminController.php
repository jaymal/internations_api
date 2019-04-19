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

use App\Services\User as UserService;
use App\Services\Group as GroupService;
use App\Services\Role as RoleService;

class AdminController extends Controller
{
   	public $userService;
   	public $groupService;
   	public $roleService;

    function __construct(UserService $userService, GroupService $groupService, RoleService $roleService )
    {
        $this->userService = $userService;
        $this->groupService = $groupService;
        $this->roleService = $roleService;
        return $this->middleware('auth:api');
    }
    
    //Check if a user is admin i.e if role_id  is 1.
    public function checkUserIsAdmin()
    {     	
    	$recordExist = User_role::where(["role_id" => 1, "user_id" => request()->user()->id])->firstOrFail();
    	return $recordExist;
    }

    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->checkUserIsAdmin();

        $this->validate($request, [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        $user = $this->userService->create($request);
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
        $this->checkUserIsAdmin();

        $this->validate($request, [
            'group_name' => 'required|min:3',
            'id' =>'required|numeric',
        ]);
        $groupId = $this->groupService->getGroupId($request);
        if( $this->userService->addUserToGroup($request, $groupId)){
        	return response()->json(['message'=>'Users assigned successfully'],201);

        }
        return response()->json(['error'=>'Unable to assign user to the group'],500);

    }

    public function unAssign($id)
    {
        $this->checkUserIsAdmin();

        $user = $this->userService->removeUserFromGroup($id);
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
        
    	$this->checkUserIsAdmin();
    	$user = $this->userService->delete($id);

        return response()->json(null,200);
    }

    //Create a new group
    public function groupStore(Request $request)
    {
    	$this->checkUserIsAdmin();
    	 $this->validate($request, [
            'group_name' => 'required|min:3',
        ]);
        $group = $this->groupService->create($request);
        return new GroupResource($group);
    }

    //Delete group
    public function groupDestroy($id)
    {
        
    	$this->checkUserIsAdmin();
    	$userExists = $this->userService->userExists($id);
        if($userExists){
        	 return response()->json(['error'=>'Unable to delete.Users are still assigned to the group'],400);
        }

    	$this->groupService->delete($id);           
        return response()->json(null,200);
    }

    //Add a new role
    public function roleStore(Request $request)
    {
    	$this->checkUserIsAdmin();
    	 $this->validate($request, [
            'role_name' => 'required|min:3',
        ]);
        $role = $this->roleService->create($request);
        return new RoleResource($role);
    }



    
}
