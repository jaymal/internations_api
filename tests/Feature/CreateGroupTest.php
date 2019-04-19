<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

Use App\Models\User_role;
use App\Models\Group;
use App\User as User;

class CreateGroupTest extends TestCase
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
    public function admin_can_delete_groups()
    {

        $admin = $this->create_admin_user();
        $group = factory(\App\Models\Group::class)->create(); 

        $groupExistInitial = Group::find($group->id);
        $this->assertEquals($group->id, $groupExistInitial->id);
        $response = $this->actingAs($admin, 'api')->json('DELETE', '/api/group/'.$group->id);
        $response->assertStatus(200);
        $groupExistFinal = Group::find($group->id);
        $this->assertNull($groupExistFinal);


    }

    /** @test */ 
    public function admin_can_create_group()
    {
       $this->withExceptionHandling();
        $admin = $this->create_admin_user();
        $groupPayload = [
            'group_name' => "group1",
        ]; 
        $response = $this->actingAs($admin, 'api')->json('POST', '/api/group', $groupPayload);
        $response->assertStatus(201);
        $response->assertJsonFragment(['groupName' => 'group1']);

    }

}
