<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User_role;
use App\Models\User_group;
use App\Models\Group;
use App\User as User;
use App\Services\User as UserService;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\UserBelongsToGroupException;

class UserGroupTest extends TestCase
{
    use RefreshDatabase;
    
   	/** @test */
   	public function can_add_existing_user_to_existing_group()
   	{
     		$user = factory(\App\User::class)->create();
        $group = factory(\App\Models\Group::class)->create();
        $userService = new UserService;

        $userGroup =  $userService->addUserToGroup($user->id, $group->id);

     		$this->assertEquals($user->id, $userGroup->user_id); 
        $this->assertEquals($group->id, $userGroup->group_id); 

    }

    /** @test */
    public function cannot_add_non_existing_user_to_existing_group()
    {
        
        try{
            $group = factory(\App\Models\Group::class)->create();
            $userService = new UserService;
            $userGroup =  $userService->addUserToGroup(5, $group->id);

        } catch(ModelNotFoundException $e ){  
            $this->assertContains("No query results", $e->getMessage()); 
            return;
        }

        $this->fail('can add non existing user to group');
    }


    /** @test */
    public function cannot_add_existing_user_to_non_existing_group()
    {
        $user = factory(\App\User::class)->create();
        
        try{
            $userService = new UserService;
            $userGroup =  $userService->addUserToGroup($user->id, 4);

        } catch(ModelNotFoundException $e ){  
            $this->assertContains("No query results", $e->getMessage()); 
            return;
        }

        $this->fail('can add user to non existing group');
    }

     /** @test */
    public function cannot_add_user_to_group_the_user_already_belongs()
    {
        $user = factory(\App\User::class)->create();
        $group = factory(\App\Models\Group::class)->create();
        $userService = new UserService;
        try{
        $userGroup =  $userService->addUserToGroup($user->id, $group->id);
        $this->assertEquals($user->id, $userGroup->user_id); 
        $this->assertEquals($group->id, $userGroup->group_id);

        $userGroup2 =  $userService->addUserToGroup($user->id, $group->id);

        } catch(UserBelongsToGroupException $e){

            $recordsCount = User_group::countRecords($user->id, $group->id);
            $this->assertEquals(1, $recordsCount);
            return;
            //$this->assertContains("The User already belongs to this group", $e->getMessage());
            //$this->assertEquals(403,  $e->getCode());
        }
        $this->fail('can add user that already belongs to this group');

    }

    
}
