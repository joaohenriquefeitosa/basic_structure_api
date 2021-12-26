<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RegisterTest extends TestCase
{
    use DatabaseTransactions;
 
    public function testRegisterWithSuccess()
    {
        $body = [
            'name' => 'Admin',
            'email' => 'admin2@admin.com',
            'password' => 'Adm!n123!@#%$'
        ];

        $this->json('POST',
                    '/api/auth/register',
                    $body,
                    ['Accept' => 'application/json'])
                        ->assertStatus(201)
                        ->assertJsonStructure(['token']);
    }

    public function testRegisterWithErrorWithoutName()
    {
        $body = [
            'email' => 'admi2n@admin.com',
            'password' => 'Adm!n123!@#%$'
        ];

        $this->json('POST',
                    '/api/auth/register',
                    $body,
                    ['Accept' => 'application/json'])
                        ->assertStatus(422);
    }

    public function testRegisterWithErrorWithoutEmail()
    {
        $body = [
            'name' => 'Admin',
            'password' => 'Adm!n123!@#%$'
        ];

        $this->json('POST',
                    '/api/auth/register',
                    $body,
                    ['Accept' => 'application/json'])
                        ->assertStatus(422);
    }

    public function testRegisterWithErrorWithoutPassword()
    {
        $body = [
            'name' => 'Admin',
            'email' => 'admi2n@admin.com',
        ];

        $this->json('POST',
                    '/api/auth/register',
                    $body,
                    ['Accept' => 'application/json'])
                        ->assertStatus(422);
    }
}
