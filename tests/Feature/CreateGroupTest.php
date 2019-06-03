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
    
   /** @test */
    public function admin_can_delete_groups()
    {

        $admin = factory(\App\User::class)->create(); 
        $assignAdminRole = factory(\App\Models\User_role::class)->states('admin')->create([
            'user_id' =>  $admin->id,
        ]);
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
        $admin = factory(\App\User::class)->create(); 
        $assignAdminRole = factory(\App\Models\User_role::class)->states('admin')->create([
            'user_id' =>  $admin->id,
        ]);
        $groupPayload = [
            'group_name' => "group1",
        ]; 
        $response = $this->actingAs($admin, 'api')->json('POST', '/api/group', $groupPayload);
        $response->assertStatus(201);
        $response->assertJsonFragment(['groupName' => 'group1']);

    }

    /** @test */
    public function non_admin_cannot_create_group()
    {
        $this->withExceptionHandling();
        $user = factory(\App\User::class)->create(); 
        $groupPayload = [
            'group_name' => "group1",
        ];  
        $response = $this->actingAs($user, 'api')->json('POST', '/api/group', $groupPayload);
        //$response->dump();
        $response->assertStatus(403);
        $response->assertJsonMissing(['group_name' => "group1"]);
    }

}
