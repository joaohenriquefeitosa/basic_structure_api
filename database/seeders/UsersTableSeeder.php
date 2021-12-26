<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Suporte',
            'email' => 'suporte@fluxostech.com.br',
            'password' => bcrypt('#Fluxos123456'),
            'status' => '1'
        ]);

        $role = Role::findByName('admin');
        $user->assignRole($role);
    }
}
