<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param User $user
     *
     * @return void
     */
    public function run(User $user)
    {
        $users = [
            [
                'name' => 'Ankit Pokhrel',
                'email' => 'hello@ankit.pl',
                'password' => app('hash')->make('pokhrel'),
                'role' => 'admin',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'name' => 'John Doe',
                'email' => 'jonh@ankit.pl',
                'password' => app('hash')->make('doe'),
                'role' => 'customer',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
        ];

        $user->insert($users);
    }
}
