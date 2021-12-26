<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;

use DB;

class ResetTest extends TestCase
{
    use DatabaseTransactions;

    public function testResetSuccess()
    {
        // Cria um usuário falso
        $body = [
            'name' => 'Admin3',
            'email' => 'admi2n@admin.com3',
            'password' => bcrypt('Adm!2n1232234'),
            'status' => '1'
        ];
        $user = User::create($body);

        $token = Str::random(10);

        DB::table('password_resets')->insert([
            'email'      => $user->email,
            'token'      => $token,
        ]);  

        $this->json('PATCH',
                    '/api/auth/reset',
                    ['token' => $token, 'password' => 'Adm!2n1232234'],
                    ['Accept'        => 'application/json'])
                        ->assertStatus(200);
    }

    public function testResetErrorWithoutToken()
    {
        // Cria um usuário falso
        $body = [
            'name' => 'Admin3',
            'email' => 'ad2min@admin.com3',
            'password' => bcrypt('Adm!n123'),
            'status' => '1'
        ];
        $user = User::create($body);

        $token = Str::random(10);

        DB::table('password_resets')->insert([
            'email'      => $user->email,
            'token'      => $token,
        ]);  

        $this->json('PATCH',
                    '/api/auth/reset',
                    ['password' => 'Adm!n123'],
                    ['Accept'        => 'application/json'])
                        ->assertStatus(422);
    }

    public function testResetErrorWithoutPass()
    {
        // Cria um usuário falso
        $body = [
            'name' => 'Admin3',
            'email' => 'admi3n@admin.com3',
            'password' => bcrypt('Adm!n123'),
            'status' => '1'
        ];
        $user = User::create($body);

        $token = Str::random(10);

        DB::table('password_resets')->insert([
            'email'      => $user->email,
            'token'      => $token,
        ]);  

        $this->json('PATCH',
                    '/api/auth/reset',
                    ['token' => $token],
                    ['Accept'        => 'application/json'])
                        ->assertStatus(422);
    }

    public function testResetWithWrongToken()
    {
        // Cria um usuário falso
        $body = [
            'name' => 'Admin3',
            'email' => 'a2dmin@admin.com3',
            'password' => bcrypt('Adm!n123'),
            'status' => '1'
        ];
        $user = User::create($body);

        $token = Str::random(10);

        DB::table('password_resets')->insert([
            'email'      => $user->email,
            'token'      => $token,
        ]);  

        $this->json('PATCH',
                    '/api/auth/reset',
                    ['token' => $token.'123', 'password' => 'Adm!n121231233'],
                    ['Accept'        => 'application/json'])
                        ->assertStatus(422);
    }
}