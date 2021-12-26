<?php

namespace Tests\Unit\User;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;

use DB;
use Spatie\Permission\Models\Role;

class DestroyUserTest extends TestCase
{
    use DatabaseTransactions;

    public function testDestroyCustomerWithSuccess()
    {
        // Cria um usuário admin falso
        $body = [
            'name' => 'destroy'.Str::random(1),
            'email' => 'destroy'.Str::random(1).'@admin.com2',
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
            'name' => 'destroy-teste'.Str::random(1),
            'email' => 'destroy-teste'.Str::random(1).'@admin.com2',
            'password' => bcrypt('Adm!n12ASDASDF3'),
            'status' => '1'
        ];
        $customer = User::create($body);

        $role = Role::findByName('customer');
        $customer->assignRole($role);


        $this->json('DELETE',
                    "/api/users/{$customer->id}",
                    [
                        'id' => $customer->id
                    ],
                    ['Authorization' => 'Bearer ' . $token,        
                    'Accept'        => 'application/json',])
                        ->assertStatus(204);
    }
}