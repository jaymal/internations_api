<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
Use App\Models\User_role;
use App\Models\Group;
use App\User as User;

class AddUserToGroupTest extends TestCase
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
    public function admin_can_assign_user_to_group()
    {
       $this->withExceptionHandling();
        $admin = $this->create_admin_user(); 
        $user = factory(\App\User::class)->create(); 
        $group = factory(\App\Models\Group::class)->create(); 
        $payload = [
            'group_name' => $group->group_name,
            'id' => $user->id,
        ]; 
        $response = $this->actingAs($admin, 'api')->json('PUT', 'api/user/group', $payload);
        $response->assertStatus(201);
        
        $userExists = User::find($user->id);
        $this->assertEquals($group->id, $userExists->group_id);
    }

    /** @test */
    public function admin_can_unassign_user_from_group()
    {
    
        $admin = $this->create_admin_user(); 
        $user = factory(\App\User::class)->create(); 
        
        $response = $this->actingAs($admin, 'api')->json('PUT', 'api/user/'. $user->id.'/group');
        $response->assertStatus(200);
        $response->assertJsonFragment(['groupId' => null]);

    }
}
