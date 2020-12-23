<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database user seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'first_name' => 'Admin',
            'last_name' => 'Adminovich',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'validate' => 1,
            'admin' => true,
        ];

        User::create($data);

        User::factory(10)->create();
    }
}
