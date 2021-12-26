<?php

namespace Tests\Unit\User;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;

use DB;
use Spatie\Permission\Models\Role;

class UpdateUserTest extends TestCase
{
    use DatabaseTransactions;

    public function testUpdateCustomerWithSuccess()
    {
         // Cria um usuário admin falso
         $body = [
            'name' => 'Admin2',
            'email' => 'ad1min@admin.com2',
            'password' => bcrypt('Adm!n12ASDASDF3'),
            'status' => '1'
        ];
        $user = User::create($body);        
        
        $role = Role::findByName('admin');
        $user->assignRole($role);

        // Obtém o token do usuário falso
        $token = auth()->attempt(['email' => $body['email'], 'password' => 'Adm!n12ASDASDF3']);    

        // Cria um usuário customer falso
        $body = [
            'name' => 'teste123123',
            'email' => 'teste123123@tester.com2',
            'password' => bcrypt('Adm!n12ASDASDF3'),
            'status' => '1'
        ];
        $customer = User::create($body);

        $role = Role::findByName('customer');
        $customer->assignRole($role);

        $this->json('PUT',
                    "/api/users/{$customer->id}",
                    [
                        'id' => $customer->id,
                        'name' => 'TESTE',
                        'email' => 'teste'.Str::random(4).'@tester.com2',
                        'password' => bcrypt('Adm!n12ASDASDF3'),
                        'role' => 'customer',
                        'status' => '1'
                    ],
                    ['Authorization' => 'Bearer ' . $token,        
                    'Accept'        => 'application/json',])
                        ->assertStatus(200);
    }

    public function testUpdateCustomerWithoutIdWithError()
    {
        // Cria um usuário admin falso
        $body = [
            'name' => 'Admin2',
            'email' => 'admin@admin.com2',
            'password' => bcrypt('Adm!n12ASDASDF3'),
            'status' => '1'
        ];
        $user = User::create($body);        
        
        $role = Role::findByName('admin');
        $user->assignRole($role);

        // Obtém o token do usuário falso
        $token = auth()->attempt(['email' => $body['email'], 'password' => 'Adm!n12ASDASDF3']);      

        // Cria um usuário customer falso
        $body = [
            'name' => 'teste123123',
            'email' => 'teste123123@tester.com2',
            'password' => bcrypt('Adm!n12ASDASDF3'),
            'status' => '1'
        ];
        $customer = User::create($body);

        $role = Role::findByName('customer');
        $customer->assignRole($role);


        $this->json('PUT',
                    "/api/users/",
                    [
                        // 'id' => $customer->id,
                        'name' => 'TESTE',
                        'email' => 'teste'.Str::random(4).'@tester.com2',
                        'password' => bcrypt('Adm!n12ASDASDF3'),
                        'role' => 'customer',
                        'status' => '1'
                    ],
                    ['Authorization' => 'Bearer ' . $token,        
                    'Accept'        => 'application/json',])
                        ->assertStatus(405);
    }
}