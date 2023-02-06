<?php

namespace Database\Seeders;

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
        \App\Models\User::create([
            'name'	=> 'Admin',
            'email'	=> 'admin' . '@gmail.com',
            'password'	=> app('hash')->make('admin'),
            'role'	=> 'admin'
        ]);

        \App\Models\User::create([
            'name'	=> 'Sultan Akmal Maulana',
            'email'	=> 'sultan' . '@gmail.com',
            'password'	=> app('hash')->make('sultan'),
            'role'	=> 'karyawan'
        ]);
    }
}
