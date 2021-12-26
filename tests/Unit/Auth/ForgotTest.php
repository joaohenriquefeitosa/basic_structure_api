<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;

class ForgotTest extends TestCase
{
    use DatabaseTransactions;

    public function testForgotSuccess()
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
                    '/api/auth/forgot',
                    ['email' => 'admin@admin.com3'],
                    ['Accept'        => 'application/json'])
                        ->assertStatus(200);
    }

    public function testForgotErrorWithoutEmail()
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
                    '/api/auth/forgot',
                    [],
                    ['Accept'        => 'application/json'])
                        ->assertStatus(422);
    }

    public function testForgotErrorWrongEmail()
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
                    '/api/auth/forgot',
                    ['email' => 'admin@admin.com122'],
                    ['Accept'        => 'application/json'])
                        ->assertStatus(422);
    }
}