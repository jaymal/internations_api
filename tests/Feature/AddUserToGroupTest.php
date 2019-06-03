<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
Use App\Models\User_role;
use App\Models\Group;
use App\Models\User_group as UserGroupModel;
use App\User as User;

class AddUserToGroupTest extends TestCase
{
    

    use RefreshDatabase;
    
    /** @test */
    public function admin_can_assign_user_to_group()
    {
       $this->withExceptionHandling();
        $admin = factory(\App\User::class)->create(); 
        $assignAdminRole = factory(\App\Models\User_role::class)->states('admin')->create([
            'user_id' =>  $admin->id,
        ]);
        $user = factory(\App\User::class)->create(); 
        $group = factory(\App\Models\Group::class)->create(); 
        $payload = [
            'groupId' => $group->id,
            'userId' => $user->id,
        ]; 
        $response = $this->actingAs($admin, 'api')->json('POST', 'api/user/group', $payload);
        $response->assertStatus(201);
        
        $userGroup = UserGroupModel::where('user_id',$user->id)->first();
        $this->assertEquals($group->id, $userGroup->group_id);
    }   

    /** @test */
    public function admin_cannot_assign_non_existing_user_to_existing_group()
    {
       $this->withExceptionHandling();
        $admin = factory(\App\User::class)->create(); 
        $assignAdminRole = factory(\App\Models\User_role::class)->states('admin')->create([
            'user_id' =>  $admin->id,
        ]);
        $group = factory(\App\Models\Group::class)->create(); 
        $payload = [
            'groupId' => $group->id,
            'userId' => 4,
        ]; 
        $response = $this->actingAs($admin, 'api')->json('POST', 'api/user/group', $payload);
        $response->assertStatus(404);
        
    
    }

    /** @test */
    public function admin_cannot_assign_existing_user_to_non_existing_group()
    {
       $this->withExceptionHandling();
        $admin = factory(\App\User::class)->create(); 
        $assignAdminRole = factory(\App\Models\User_role::class)->states('admin')->create([
            'user_id' =>  $admin->id,
        ]);
        $user = factory(\App\User::class)->create();  
        $payload = [
            'groupId' => 5,
            'userId' => $user->id,
        ]; 
        $response = $this->actingAs($admin, 'api')->json('POST', 'api/user/group', $payload);
        $response->assertStatus(404);
        
    }
    /** @test */
    public function admin_cannot_assign_non_existing_user_to_non_existing_group()
    {
       $this->withExceptionHandling();
        $admin = factory(\App\User::class)->create(); 
        $assignAdminRole = factory(\App\Models\User_role::class)->states('admin')->create([
            'user_id' =>  $admin->id,
        ]);
        
        $payload = [
            'groupId' => 2,
            'userId' => 3,
        ]; 
        $response = $this->actingAs($admin, 'api')->json('POST', 'api/user/group', $payload);
        $response->assertStatus(404);
        
    }
    


    /** @test */
    public function admin_can_unassign_user_from_group()
    {
        $this->withExceptionHandling(); 
        $admin = factory(\App\User::class)->create(); 
        $assignAdminRole = factory(\App\Models\User_role::class)->states('admin')->create([
            'user_id' =>  $admin->id,
        ]);
        $user = factory(\App\User::class)->create(); 
        $group = factory(\App\Models\Group::class)->create();

        $payload = [
            'groupId' => $group->id,
            'userId' => $user->id,
        ]; 
        $respons1 = $this->actingAs($admin, 'api')->json('POST', 'api/user/group', $payload);
        $respons1->assertStatus(201);

        $response2 = $this->actingAs($admin, 'api')
            ->json('DELETE', 'api/user/'. $user->id.'/group/'.$group->id);

        $response2->assertStatus(200);
        $userGroup = UserGroupModel::where(['user_id' => $user->id, 'group_id' => $group->id])->first();
        $response2->assertSeeText('User removed');

    }

    /** @test */
    public function admin_cannot_add_user_to_same_group_twice()
    {
        $this->withExceptionHandling();
        $admin = factory(\App\User::class)->create(); 
        $assignAdminRole = factory(\App\Models\User_role::class)->states('admin')->create([
            'user_id' =>  $admin->id,
        ]);
        $user = factory(\App\User::class)->create(); 
        $group = factory(\App\Models\Group::class)->create(); 
        $payload = [
            'groupId' => $group->id,
            'userId' => $user->id,
        ]; 
        $response = $this->actingAs($admin, 'api')->json('POST', 'api/user/group', $payload);
        $response->assertStatus(201);

        $response2 = $this->actingAs($admin, 'api')->json('POST', 'api/user/group', $payload);
        $response2->assertStatus(403);
        

    }
}
