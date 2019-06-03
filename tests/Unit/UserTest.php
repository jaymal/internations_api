<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User_role;
use App\Models\Group;
use App\User as User;

class UserTest extends TestCase
{
    use RefreshDatabase;
    
   	/** @test */
   	public function can_create_new_user_with_name()
   	{
     		$user = factory(\App\User::class)->create([
          'name' => "John Doe"
        ]);
     		$this->assertEquals("John Doe", $user->name); 

    }

    /** @test */
    public function can_not_add_new_user_with_no_name()
    {
      
        try {
            $user = factory(\App\User::class)->create([
              'name' => Null
            ]);
            
        } catch (\PDOException $e) {
           $this->assertContains("NOT NULL constraint failed", $e->getMessage());   
           return;        
        }
        $this->fail('Can add user with no name');
      
        
    }

    
}
