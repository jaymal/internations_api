<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;


use App\Http\Resources\UserResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\GroupResource;

use App\Services\User as UserService;
use App\Services\Group as GroupService;
use App\Services\Role as RoleService;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\UserBelongsToGroupException;

use App\Events\UserCreated;
use App\Events\UserAssignedToGroup;



class AdminController extends Controller
{
   	public $userService;
   	public $groupService;
   	public $roleService;

 
    function __construct( UserService $userService, RoleService $roleService,GroupService $groupService )
    {
    
        $this->middleware('auth:api')->except('index');
        $this->groupService = $groupService;
        $this->roleService = $roleService;
        $this->userService = $userService;

    }
    
    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', auth()->user());
        $this->validate($request, [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        $user = $this->userService->create($request);

        event(new UserCreated($user));

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
        $this->authorize('update', auth()->user());

        $this->validate($request, [
            'groupId' => 'required|numeric',
            'userId' =>'required|numeric',
        ]);
        try{
             $userGroup = $this->userService->addUserToGroup($request->userId, $request->groupId);
             event(new UserAssignedToGroup($userGroup));
            
        } catch(Exception $exception){

            return response()->json(['error' => $exception->getMessage()], 404);

        }catch(UserBelongsToGroupException $ex){

            return response()->json(['error' => $ex->getMessage()], 403);
        }

        return response()->json(['message'=>'User assigned successfully'],201);


    }

    //remove user from groups
    public function unAssign($userId, $groupId)
    {

        $this->authorize('update', auth()->user());

        if( $this->userService->removeUserFromGroup($userId, $groupId)){
            return response()->json(['message'=>'User removed from group successfully'],200);
        }
        return response()->json(['error'=>'Unable to remove user from the group'],422);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user->id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete', auth()->user());
        try {
            $this->userService->delete($id);         
       
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
        return response()->json(['message'=>'User deleted successfully'], 200);
       
    }

    //Create a new group
    public function groupStore(Request $request)
    {
    	$this->authorize('update', auth()->user());

        $this->validate($request, [
            'group_name' => 'required|min:3',
        ]);
        $group = $this->groupService->create($request);
        return new GroupResource($group);
    }

    /**
     * Delete group if it has no users.
     *
     * @param   group Id $groupId
     * @return \Illuminate\Http\Response
     */
    public function groupDestroy($groupId)
    {
        $this->authorize('update', auth()->user());

         try {
            $userExists = $this->groupService->groupHasUsers($groupId);

            if($userExists){
                return response()->json(['error'=>'Unable to delete group.Users are still assigned to the group'],403);//403 Forbidden
            }

             $this->groupService->delete($groupId);           
       
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
        return response()->json(['message'=>'Group deleted successfully'],200);

    }

    //Add a new role
    public function roleStore(Request $request)
    {
        $this->authorize('update', auth()->user());
    	 $this->validate($request, [
            'role_name' => 'required|min:3',
        ]);
        $role = $this->roleService->create($request);
        return new RoleResource($role);
    }





    
}
