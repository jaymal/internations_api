<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User_role;
use App\Models\Group;
use App\User as User;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */ 
    public function admin_can_create_user()
    {
      // $this->withExceptionHandling();
        $admin = factory(\App\User::class)->create(); 
        $assignAdminRole = factory(\App\Models\User_role::class)->states('admin')->create([
            'user_id' =>  $admin->id,
        ]);
        $userPayload = [
            'email' => "jamal@email.com",
            'password' => "secret",
            'name' => "jay",
        ]; 
        $response = $this->actingAs($admin, 'api')->json('POST', '/api/users', $userPayload);
        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => 'jay', 'email' => 'jamal@email.com']);

    }

    /** @test */
    public function non_admin_cannot_create_user()
    {
        $this->withExceptionHandling();
        $user = factory(\App\User::class)->create(); 
        $userPayload = [
            'email' => "jamalnon@email.com",
            'password' => "secret",
            'name' => "jay non",
        ]; 
        $response = $this->actingAs($user, 'api')->json('POST', '/api/users', $userPayload);
        //$response->dump();
        $response->assertStatus(403);
        $response->assertJsonMissing(['name' => 'jay non', 'email' => 'jamalnon@email.com']);
    }

    /** @test */
    public function admin_can_delete_user()
    {
       //$this->withExceptionHandling();
        $admin = factory(\App\User::class)->create(); 
        $assignAdminRole = factory(\App\Models\User_role::class)->states('admin')->create([
            'user_id' =>  $admin->id,
        ]);
        $user = factory(\App\User::class)->create(); 
        $userPayload = [
            'id' => $user->id,
        ]; 
        $response = $this->actingAs($admin, 'api')->json('DELETE', '/api/user/'.$user->id);
        $response->assertStatus(200);
    }
   
    /** @test */
    public function non_admin_cannot_delete_user()
    {
        $this->withExceptionHandling();
        $user1 = factory(\App\User::class)->create(); 
        $user = factory(\App\User::class)->create(); 
        $userPayload = [
            'id' => $user->id,
        ]; 
        $response = $this->actingAs($user1, 'api')->json('DELETE', '/api/user/'.$user->id);
        $userExists = User::find($user->id);
        
        $response->assertStatus(403);       
        $this->assertEquals($user->id, $userExists->id);
    }



 

    


}
