<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        DB::table('users')->insert([
            'name' => 'User 1',
            'email' => 1,
            // 'phone' => $faker->unique()->phoneNumber,
            'password' => '$2y$10$oZxmjr5yFhq.oRe6qSlqnOCVdx6k.CVxVI94lF6QPu55.Gj0baMye', // 1
            'remember_token' => Str::random(10),
            // 'created_at' => Carbon::now()->timestamp,
            // 'updated_at' => Carbon::now()->timestamp,
        ]);
        DB::table('users')->insert([
            'name' => 'User 2',
            'email' => 2,
            'password' => '$2y$10$c0.jS/TtU/sfzyPCsJkmN.dzdMjWviXFKXgpfoCGnx2KHiu7vvybS', // 2
            'remember_token' => Str::random(10),
        ]);
        DB::table('users')->insert([
            'name' => 'User 3',
            'email' => 3,
            'password' => '$2y$10$/pKvjRavZGF.PYk1C//aD.IPH2XrV2Qa/ajMIHTAjKGzg2WO44idy', // 3
            'remember_token' => Str::random(10),
        ]);
    }
}
