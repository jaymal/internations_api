<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User_role;
use App\Models\Group;
use App\User as User;

use App\Services\Admin as AdminService;

class UserTest extends TestCase
{
    use RefreshDatabase;
    
    public function create_admin_user()
    {
        $user = factory(\App\User::class)->create(); 
        $addAdminRole = factory(\App\Models\Role::class)->create(); 
        $assignAdminRole = factory(\App\Models\User_role::class)->states('admin')->create([
            'user_id' =>  $user->id,
        ]);
        $roleId = (User::findOrFail($user->id)->user_role()->first()->role_id);
        return $user;
    }

   	/** @test */
   	public function can_create_new_user_with_no_group()
   	{
   		$user = factory(\App\User::class)->create([
   			'group_id' => null
   		]);
   		$this->assertEquals(null, $user->group_id); 

    }

    /** @test */
  	public function can_add_user_to_a_group()
   	{
   		$admin = $this->create_admin_user();
      $user = factory(\App\User::class)->create();
   		$group = factory(\App\Models\Group::class)->create();
   		$userService = new AdminService($admin->id);
   		
      $requestArray = [
   			'group_name' => $group->group_name,
   			'id' => $user->id
   		];

   		$request = (object) $requestArray;
   		$user = $userService->addUserToGroup($request, $group->id);
   		$this->assertEquals($group->id, $user->group_id); 

   	}
}
