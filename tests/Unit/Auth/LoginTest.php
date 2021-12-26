<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

use Illuminate\Support\Facades\Http;
use App\Models\User;

use Illuminate\Support\Str;
use Laravel\Passport\Passport;

use DB;
use Spatie\Permission\Models\Role;

class LoginTest extends TestCase
{
    use DatabaseTransactions;
    

    public function testLoginUserSuccess()
    {
        // Cria um usuário falso
        $body = [
            'name' => 'Admin3',
            'email' => 'admin@admin.com3',
            'password' => bcrypt('Adm!n123!@#%$'),
            'status' => '1'
        ];
        $user = User::create($body);

        $user->update(['activation_code' => null]);
        
        $role = Role::findByName('admin');
        $user->assignRole($role);

        $this->json('POST',
                    '/api/auth/login',
                    ['email' => 'admin@admin.com3', 'password' => 'Adm!n123!@#%$'],
                    ['Accept'        => 'application/json'])
                        ->assertStatus(200);
    }

    public function testLoginUserErroStatusBlock()
    {
        // Cria um usuário falso
        $body = [
            'name' => 'Admin3',
            'email' => 'admin@admin.com3',
            'password' => bcrypt('Adm!n123!@#%$'),
            'status' => '0'
        ];
        $user = User::create($body);

        $this->json('POST',
                    '/api/auth/login',
                    ['email' => 'admin@admin.com3', 'password' => 'Adm!n123!@#%$'],
                    ['Accept'        => 'application/json'])
                        ->assertStatus(403);
    }

    public function testLoginUserErrorWrongPass()
    {
        // Cria um usuário falso
        $body = [
            'name' => 'Admin3',
            'email' => 'admin@admin.com3',
            'password' => bcrypt('Adm!n123'),
            'status' => '1'
        ];
        $user = User::create($body);

        $this->json('POST',
                    '/api/auth/login',
                    ['email' => 'admin@admin.com3', 'password' => 'Adm!n123!@#%$'],
                    ['Accept'        => 'application/json'])
                        ->assertStatus(401);
    }

    public function testLoginUserErrorWrongEmail()
    {
        // Cria um usuário falso
        $body = [
            'name' => 'Admin3',
            'email' => 'admin@admin.com3',
            'password' => bcrypt('Adm!n123'),
            'status' => '1'
        ];
        $user = User::create($body);

        $this->json('POST',
                    '/api/auth/login',
                    ['email' => 'admin@admin.com6', 'password' => 'Adm!n123!@#%$'],
                    ['Accept'        => 'application/json'])
                        ->assertStatus(422);
    }

    public function testLoginUserErrorWithoutEmail()
    {
        // Cria um usuário falso
        $body = [
            'name' => 'Admin3',
            'email' => 'admin@admin.com3',
            'password' => bcrypt('Adm!n123!@#%$'),
            'status' => '1'
        ];
        $user = User::create($body);

        $this->json('POST',
                    '/api/auth/login',
                    ['password' => 'Adm!n123!@#%$'],
                    ['Accept'        => 'application/json'])
                        ->assertStatus(422);
    }

    public function testLoginUserErrorWithoutPass()
    {
        // Cria um usuário falso
        $body = [
            'name' => 'Admin3',
            'email' => 'admin@admin.com3',
            'password' => bcrypt('Adm!n123!@#%$'),
            'status' => '1'
        ];
        $user = User::create($body); 

        $this->json('POST',
                    '/api/auth/login',
                    ['email' => 'admin@admin.com3'],
                    ['Accept'        => 'application/json'])
                        ->assertStatus(422);
    }
}
