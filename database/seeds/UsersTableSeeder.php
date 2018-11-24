<?php

use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param Generator $faker
     * @param User      $user
     *
     * @return void
     */
    public function run(Generator $faker, User $user)
    {
        $users = [];

        for ($i = 0; $i < 5; $i++) {
            $users[] = [
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => app('hash')->make(str_random('13')),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ];
        }

        $user->insert($users);
    }
}
