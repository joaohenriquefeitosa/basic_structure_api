<?php

namespace Tests\Unit\User;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;

use DB;
use Spatie\Permission\Models\Role;

class CreateUserTest extends TestCase
{
    use DatabaseTransactions;

    public function testStoreCustomerWithSuccess()
    {
        // Cria um usuário falso
        $body = [
            'name' => 'Admin2',
            'email' => 'adm12in@admin.com2',
            'password' => bcrypt('Adm!n12ASDASDF3'),
            'status' => '1'
        ];
        $user = User::create($body);
        
        $role = Role::findByName('admin');
        $user->assignRole($role);

        // Obtém o token do usuário falso
        $token = auth()->attempt(['email' => $body['email'], 'password' => 'Adm!n12ASDASDF3']);   


        $this->json('POST',
                    '/api/users',
                    [
                        'name' => 'João Teste',
                        'email'=> Str::random(1).'joao@teste123.com.br',
                        'password' => bcrypt('Adm!n12ASDASDF3s'),
                        'role' => 'customer'
                    ],
                    ['Authorization' => 'Bearer ' . $token,        
                    'Accept'        => 'application/json',])
                        ->assertStatus(201);
    }

    public function testStoreAdminWithSuccess()
    {
        // Cria um usuário falso
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

        $this->json('POST',
                    '/api/users/',
                    [
                        'name' => 'João Teste',
                        'email'=> Str::random(4).'joao@teste123.com.br',
                        'password' => bcrypt('Adm!n12ASDASDF3'),
                        'role' => 'admin'
                    ],
                    ['Authorization' => 'Bearer ' . $token,        
                    'Accept'        => 'application/json',])
                        ->assertStatus(201);
    }


    public function testStoreWithoutNameWithError()
    {
        // Cria um usuário falso
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

        $this->json('POST',
                    '/api/users',
                    [
                        // 'name' => 'João Teste',
                        'email'=> 'joao@teste123.com.br',
                        'password' => bcrypt('21213213'),
                        'role' => 'admin'
                    ],
                    ['Authorization' => 'Bearer ' . $token,        
                    'Accept'        => 'application/json',])
                        ->assertStatus(422);
    }

    public function testStoreWithoutEmailWithError()
    {
        // Cria um usuário falso
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

        $this->json('POST',
                    '/api/users',
                    [
                        'name' => 'João Teste',
                        // 'email'=> 'joao@teste123.com.br',
                        'password' => bcrypt('21213213'),
                        'role' => 'adminsdf'
                    ],
                    ['Authorization' => 'Bearer ' . $token,        
                    'Accept'        => 'application/json',])
                        ->assertStatus(422);
    }

    public function testStoreWithoutPasswordWithError()
    {
        // Cria um usuário falso
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

        $this->json('POST',
                    '/api/users',
                    [
                        'name' => 'João Teste',
                        'email'=> 'joao@teste123.com.br',
                        // 'password' => bcrypt('21213213'),
                        'role' => 'adminsdf'
                    ],
                    ['Authorization' => 'Bearer ' . $token,        
                    'Accept'        => 'application/json',])
                        ->assertStatus(422);
    }
}