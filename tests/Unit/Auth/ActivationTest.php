<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;

class ActivationTest extends TestCase
{
    use DatabaseTransactions;
    
    public function testActivateUserSuccess()
    {
        // Cria um usuário falso
        $body = [
            'name' => 'Admin2',
            'email' => 'admin@admin.com2',
            'password' => bcrypt('Adm!n123!@#%$'),
            'status' => '1'
        ];
        $user = User::create($body);
        
        // Gera o código de ativação do usuário falso
        $activation_code = Str::random(6);
        $user->activation_code = $activation_code;
        $user->update();

        // Obtém o token do usuário falso
        $token = auth()->attempt(['email' => 'admin@admin.com2', 'password' => 'Adm!n123!@#%$']); 

        $this->json('PATCH',
                    '/api/auth/activate',
                    ['activation_code' => $activation_code],
                    ['Authorization' => 'Bearer ' . $token,        
                    'Accept'        => 'application/json',])
                        ->assertStatus(200);
    }

    public function testActivateUserErrorWithoutActivationCode()
    {
        // Cria um usuário falso
        $body = [
            'name' => 'Admin2',
            'email' => 'admin@admin.com2',
            'password' => bcrypt('Adm!n123!@#%$'),
            'status' => '1'
        ];
        $user = User::create($body);
        
        // Gera o código de ativação do usuário falso
        $activation_code = Str::random(6);
        $user->activation_code = $activation_code;
        $user->update();

        // Obtém o token do usuário falso
        $token = auth()->attempt(['email' => 'admin@admin.com2', 'password' => 'Adm!n123!@#%$']);     

        $this->json('PATCH',
                    '/api/auth/activate',
                    ['Authorization' => 'Bearer ' . $token,        
                    'Accept'        => 'application/json',])
                        ->assertStatus(422);
    }
}