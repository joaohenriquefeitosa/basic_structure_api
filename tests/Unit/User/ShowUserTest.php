<?php

namespace Tests\Unit\User;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;

use DB;
use Spatie\Permission\Models\Role;

class ShowUserTest extends TestCase
{
    use DatabaseTransactions;

    public function testShowUserWithSuccess()
    {
        $body = [
            'name' => 'pubshoder1'.Str::random(9),
            'email' => 'pubsdof3her1'.Str::random(9).'@pb.com2',
            'password' => bcrypt('Adm!n12ASDASDF3'),
            'status' => '1'
        ];
        $user = User::create($body);
        $role = Role::findByName('admin');
        $user->assignRole($role);

        // Obtém o token do usuário falso
        $token = auth()->attempt(['email' => $body['email'], 'password' => 'Adm!n12ASDASDF3']);    
        
                
        $this->json('GET',
                    "/api/users/{$user->id}/",
                    [
                        'id' => $user->id,
                    ],
                    ['Authorization' => 'Bearer ' . $token,        
                    'Accept'        => 'application/json',]
                    )
                        ->assertStatus(200);
    }
}