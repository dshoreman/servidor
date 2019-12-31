<?php

use Illuminate\Database\Seeder;
use Servidor\User;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User;
        $user->name = 'Servidor Admin';
        $user->email = 'admin@servidor.local';
        $user->password = bcrypt('servidor');
        $user->save();
    }
}
